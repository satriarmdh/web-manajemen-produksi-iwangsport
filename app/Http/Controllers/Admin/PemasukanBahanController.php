<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\Supplier;
use App\Models\StokMasukBahanBaku;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PemasukanBahanController extends Controller
{
    /**
     * Simpan transaksi stok masuk
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bahan_baku_id' => 'required|exists:bahan_baku,id',
            'quantity' => 'required|integer|min:1',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'keterangan' => 'nullable|string|max:500',
            'bukti_pembelian' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $bahanBaku = BahanBaku::findOrFail($validated['bahan_baku_id']);
            $stokSebelum = (int) $bahanBaku->stok;
            $stokSesudah = $stokSebelum + $validated['quantity'];

            // Handle upload bukti pembelian
            $buktiPath = null;
            if ($request->hasFile('bukti_pembelian')) {
                $buktiPath = $request->file('bukti_pembelian')->store('img/bukti-pembelian', 'public');
            }

            // Simpan transaksi stok masuk
            $transaksi = StokMasukBahanBaku::create([
                'bahan_baku_id' => $validated['bahan_baku_id'],
                'jumlah' => $validated['quantity'],
                'supplier_id' => $validated['supplier_id'] ?? null,
                'bukti_pembelian' => $buktiPath,
                'user_id' => auth()->id(),
                'catatan' => $validated['keterangan'] ?? null,
            ]);

            // Catat riwayat stok secara langsung (tidak bergantung observer)
            $riwayat = RiwayatStok::create([
                'jenis_item' => 'bahan_baku',
                'id_item' => $bahanBaku->id,
                'jenis_pergerakan' => 'masuk',
                'jumlah' => $validated['quantity'],
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'user_id' => auth()->id(),
                'keterangan' => $validated['keterangan'] ?? 'Stok masuk dari pembelian',
                'referensi_type' => StokMasukBahanBaku::class,
                'referensi_id' => $transaksi->id,
            ]);

            // Update stok tanpa trigger observer
            BahanBaku::withoutEvents(function () use ($bahanBaku, $stokSesudah) {
                $bahanBaku->stok = $stokSesudah;
                $bahanBaku->save();
            });
        });

        return redirect()->route('admin.pergerakan-stok.index', ['tab' => 'masuk'])
            ->with('success', 'Stok masuk berhasil ditambahkan!');
    }

    /**
     * Hapus transaksi stok masuk (soft delete)
     */
    public function destroy(StokMasukBahanBaku $pemasukanBahan)
    {
        DB::transaction(function () use ($pemasukanBahan) {
            $bahanBaku = $pemasukanBahan->bahanBaku;
            $stokSebelum = (int) $bahanBaku->stok;
            $stokSesudah = max(0, $stokSebelum - $pemasukanBahan->jumlah);

            // Catat pembatalan
            RiwayatStok::create([
                'jenis_item' => 'bahan_baku',
                'id_item' => $bahanBaku->id,
                'jenis_pergerakan' => 'penyesuaian',
                'jumlah' => $pemasukanBahan->jumlah,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'keterangan' => 'Pembatalan stok masuk',
                'user_id' => auth()->id()
            ]);

            // Kembalikan stok tanpa trigger observer
            BahanBaku::withoutEvents(function () use ($bahanBaku, $stokSesudah) {
                $bahanBaku->stok = $stokSesudah;
                $bahanBaku->save();
            });

            // Hapus file bukti jika ada
            if ($pemasukanBahan->bukti_pembelian) {
                Storage::disk('public')->delete($pemasukanBahan->bukti_pembelian);
            }

            $pemasukanBahan->delete();
        });

        return redirect()->route('admin.pergerakan-stok.index', ['tab' => 'masuk'])
            ->with('success', 'Transaksi stok masuk berhasil dihapus!');
    }
}

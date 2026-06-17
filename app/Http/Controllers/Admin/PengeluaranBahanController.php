<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\StokKeluarBahanBaku;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengeluaranBahanController extends Controller
{
    /**
     * Simpan transaksi stok keluar
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bahan_baku_id' => [
                'required',
                'exists:bahan_baku,id',
                function ($attribute, $value, $fail) {
                    $bahan = BahanBaku::find($value);
                    if ($bahan && $bahan->kategori === 'kain') {
                        $fail('Bahan baku kategori kain tidak dapat dikeluarkan melalui menu ini.');
                    }
                },
            ],
            'quantity' => 'required|integer|min:1',
            'penerima' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:500',
            'bukti_pengeluaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $bahanBaku = BahanBaku::findOrFail($validated['bahan_baku_id']);

            // Validasi: tidak boleh keluar lebih dari stok
            if ($validated['quantity'] > $bahanBaku->stok) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'quantity' => 'Jumlah yang diminta melebihi stok tersedia.',
                ]);
            }

            $stokSebelum = (int) $bahanBaku->stok;
            $stokSesudah = $stokSebelum - $validated['quantity'];

            // Handle upload bukti pengeluaran
            $buktiPath = null;
            if ($request->hasFile('bukti_pengeluaran')) {
                $buktiPath = $request->file('bukti_pengeluaran')->store('img/bukti-pengeluaran', 'public');
            }

            // Simpan transaksi stok keluar
            $transaksi = StokKeluarBahanBaku::create([
                'bahan_baku_id' => $validated['bahan_baku_id'],
                'jumlah' => $validated['quantity'],
                'penerima' => $validated['penerima'],
                'bukti_pengeluaran' => $buktiPath,
                'user_id' => auth()->id(),
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // Catat riwayat stok secara langsung
            $keterangan = 'Diberikan ke: ' . $validated['penerima'] . 
                (($validated['keterangan'] ?? null) ? ' | ' . $validated['keterangan'] : '');

            RiwayatStok::create([
                'jenis_item' => 'bahan_baku',
                'id_item' => $bahanBaku->id,
                'jenis_pergerakan' => 'keluar',
                'jumlah' => $validated['quantity'],
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'user_id' => auth()->id(),
                'keterangan' => $keterangan,
                'referensi_type' => StokKeluarBahanBaku::class,
                'referensi_id' => $transaksi->id,
            ]);

            // Update stok tanpa trigger observer
            BahanBaku::withoutEvents(function () use ($bahanBaku, $stokSesudah) {
                $bahanBaku->stok = $stokSesudah;
                $bahanBaku->save();
            });
        });

        return redirect()->route('admin.pergerakan-stok.index', ['tab' => 'keluar'])
            ->with('success', 'Stok keluar berhasil dicatat!');
    }

    /**
     * Hapus transaksi stok keluar (soft delete)
     */
    public function destroy(StokKeluarBahanBaku $pengeluaranBahan)
    {
        DB::transaction(function () use ($pengeluaranBahan) {
            $bahanBaku = $pengeluaranBahan->bahanBaku;
            $stokSebelum = (int) $bahanBaku->stok;
            $stokSesudah = $stokSebelum + $pengeluaranBahan->jumlah;

            // Catat pembatalan
            RiwayatStok::create([
                'jenis_item' => 'bahan_baku',
                'id_item' => $bahanBaku->id,
                'jenis_pergerakan' => 'penyesuaian',
                'jumlah' => $pengeluaranBahan->jumlah,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'keterangan' => 'Pembatalan stok keluar',
                'user_id' => auth()->id()
            ]);

            // Kembalikan stok tanpa trigger observer
            BahanBaku::withoutEvents(function () use ($bahanBaku, $stokSesudah) {
                $bahanBaku->stok = $stokSesudah;
                $bahanBaku->save();
            });

            // Hapus file bukti jika ada
            if ($pengeluaranBahan->bukti_pengeluaran) {
                Storage::disk('public')->delete($pengeluaranBahan->bukti_pengeluaran);
            }

            $pengeluaranBahan->delete();
        });

        return redirect()->route('admin.pergerakan-stok.index', ['tab' => 'keluar'])
            ->with('success', 'Transaksi stok keluar berhasil dihapus!');
    }
}

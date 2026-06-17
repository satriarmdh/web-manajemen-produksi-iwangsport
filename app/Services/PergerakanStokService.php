<?php

namespace App\Services;

use App\Models\BahanBaku;
use App\Models\RiwayatStok;
use App\Models\StokMasukBahanBaku;
use App\Models\StokKeluarBahanBaku;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class PergerakanStokService
{
    /**
     * Simpan transaksi stok MASUK + catat riwayat + update stok bahan baku.
     */
    public function storeStokMasuk(array $data, ?UploadedFile $buktiFile = null): StokMasukBahanBaku
    {
        return DB::transaction(function () use ($data, $buktiFile) {
            $bahanBaku = BahanBaku::findOrFail($data['bahan_baku_id']);
            $stokSebelum = (int) $bahanBaku->stok;
            $stokSesudah = $stokSebelum + $data['quantity'];

            // Upload bukti pembelian
            $buktiPath = $buktiFile
                ? $buktiFile->store('img/bukti-pembelian', 'public')
                : null;

            // Simpan transaksi
            $transaksi = StokMasukBahanBaku::create([
                'bahan_baku_id'  => $data['bahan_baku_id'],
                'jumlah'         => $data['quantity'],
                'supplier_id'    => $data['supplier_id'] ?? null,
                'bukti_pembelian' => $buktiPath,
                'user_id'        => auth()->id(),
                'catatan'        => $data['keterangan'] ?? null,
            ]);

            // Catat riwayat stok
            RiwayatStok::create([
                'jenis_item'         => 'bahan_baku',
                'id_item'            => $bahanBaku->id,
                'jenis_pergerakan'   => 'masuk',
                'jumlah'             => $data['quantity'],
                'stok_sebelum'       => $stokSebelum,
                'stok_sesudah'       => $stokSesudah,
                'user_id'            => auth()->id(),
                'keterangan'         => $data['keterangan'] ?? 'Stok masuk dari pembelian',
                'referensi_type'     => StokMasukBahanBaku::class,
                'referensi_id'       => $transaksi->id,
            ]);

            // Update stok tanpa trigger observer
            BahanBaku::withoutEvents(function () use ($bahanBaku, $stokSesudah) {
                $bahanBaku->stok = $stokSesudah;
                $bahanBaku->save();
            });

            return $transaksi;
        });
    }

    /**
     * Hapus (batal) transaksi stok MASUK + catat penyesuaian + kembalikan stok.
     */
    public function destroyStokMasuk(StokMasukBahanBaku $transaksi): void
    {
        DB::transaction(function () use ($transaksi) {
            $bahanBaku   = $transaksi->bahanBaku;
            $stokSebelum = (int) $bahanBaku->stok;
            $stokSesudah = max(0, $stokSebelum - $transaksi->jumlah);

            // Catat penyesuaian pembatalan
            RiwayatStok::create([
                'jenis_item'       => 'bahan_baku',
                'id_item'          => $bahanBaku->id,
                'jenis_pergerakan' => 'penyesuaian',
                'jumlah'           => $transaksi->jumlah,
                'stok_sebelum'     => $stokSebelum,
                'stok_sesudah'     => $stokSesudah,
                'user_id'          => auth()->id(),
                'keterangan'       => 'Pembatalan stok masuk',
            ]);

            // Kembalikan stok tanpa trigger observer
            BahanBaku::withoutEvents(function () use ($bahanBaku, $stokSesudah) {
                $bahanBaku->stok = $stokSesudah;
                $bahanBaku->save();
            });

            // Hapus file bukti jika ada
            if ($transaksi->bukti_pembelian) {
                Storage::disk('public')->delete($transaksi->bukti_pembelian);
            }

            $transaksi->delete();
        });
    }

    /**
     * Simpan transaksi stok KELUAR + catat riwayat + update stok bahan baku.
     */
    public function storeStokKeluar(array $data, ?UploadedFile $buktiFile = null): StokKeluarBahanBaku
    {
        return DB::transaction(function () use ($data, $buktiFile) {
            $bahanBaku   = BahanBaku::findOrFail($data['bahan_baku_id']);
            $stokSebelum = (int) $bahanBaku->stok;
            $stokSesudah = $stokSebelum - $data['quantity'];

            // Upload bukti pengeluaran
            $buktiPath = $buktiFile
                ? $buktiFile->store('img/bukti-pengeluaran', 'public')
                : null;

            // Simpan transaksi
            $transaksi = StokKeluarBahanBaku::create([
                'bahan_baku_id'    => $data['bahan_baku_id'],
                'jumlah'           => $data['quantity'],
                'penerima'         => $data['penerima'],
                'bukti_pengeluaran' => $buktiPath,
                'user_id'          => auth()->id(),
                'keterangan'       => $data['keterangan'] ?? null,
            ]);

            // Catat riwayat stok
            $keterangan = 'Diberikan ke: ' . $data['penerima']
                . (($data['keterangan'] ?? null) ? ' | ' . $data['keterangan'] : '');

            RiwayatStok::create([
                'jenis_item'       => 'bahan_baku',
                'id_item'          => $bahanBaku->id,
                'jenis_pergerakan' => 'keluar',
                'jumlah'           => $data['quantity'],
                'stok_sebelum'     => $stokSebelum,
                'stok_sesudah'     => $stokSesudah,
                'user_id'          => auth()->id(),
                'keterangan'       => $keterangan,
                'referensi_type'   => StokKeluarBahanBaku::class,
                'referensi_id'     => $transaksi->id,
            ]);

            // Update stok tanpa trigger observer
            BahanBaku::withoutEvents(function () use ($bahanBaku, $stokSesudah) {
                $bahanBaku->stok = $stokSesudah;
                $bahanBaku->save();
            });

            return $transaksi;
        });
    }

    /**
     * Hapus (batal) transaksi stok KELUAR + catat penyesuaian + kembalikan stok.
     */
    public function destroyStokKeluar(StokKeluarBahanBaku $transaksi): void
    {
        DB::transaction(function () use ($transaksi) {
            $bahanBaku   = $transaksi->bahanBaku;
            $stokSebelum = (int) $bahanBaku->stok;
            $stokSesudah = $stokSebelum + $transaksi->jumlah;

            // Catat penyesuaian pembatalan
            RiwayatStok::create([
                'jenis_item'       => 'bahan_baku',
                'id_item'          => $bahanBaku->id,
                'jenis_pergerakan' => 'penyesuaian',
                'jumlah'           => $transaksi->jumlah,
                'stok_sebelum'     => $stokSebelum,
                'stok_sesudah'     => $stokSesudah,
                'user_id'          => auth()->id(),
                'keterangan'       => 'Pembatalan stok keluar',
            ]);

            // Kembalikan stok tanpa trigger observer
            BahanBaku::withoutEvents(function () use ($bahanBaku, $stokSesudah) {
                $bahanBaku->stok = $stokSesudah;
                $bahanBaku->save();
            });

            // Hapus file bukti jika ada
            if ($transaksi->bukti_pengeluaran) {
                Storage::disk('public')->delete($transaksi->bukti_pengeluaran);
            }

            $transaksi->delete();
        });
    }
}

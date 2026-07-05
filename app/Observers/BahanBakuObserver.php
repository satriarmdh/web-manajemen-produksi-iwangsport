<?php

namespace App\Observers;

use App\Models\BahanBaku;
use App\Models\RiwayatStok;

class BahanBakuObserver
{
    /**
     * Handle the BahanBaku "created" event.
     * Catat stok awal saat bahan baku pertama kali dibuat
     */
    public function created(BahanBaku $bahanBaku): void
    {
        if ($bahanBaku->stok > 0) {
            RiwayatStok::create([
                'jenis_item'       => 'bahan_baku',
                'id_item'          => $bahanBaku->id,
                'jenis_pergerakan' => 'inisiasi data',
                'jumlah'           => $bahanBaku->stok,
                'stok_sebelum'     => 0,
                'stok_sesudah'     => $bahanBaku->stok,
                'user_id'          => auth()->id(),
                'keterangan'       => 'Stok awal bahan baku ' . $bahanBaku->nama_bahan,
                'referensi_type'   => BahanBaku::class,
                'referensi_id'     => $bahanBaku->id,
            ]);
        }
    }

    /**
     * Handle the BahanBaku "updated" event.
     * Catat perubahan stok sebagai 'penyesuaian' jika stok berubah saat edit data master.
     */
    public function updated(BahanBaku $bahanBaku): void
    {
        if ($bahanBaku->isDirty('stok')) {
            $stokSebelum = (int) $bahanBaku->getOriginal('stok');
            $stokSesudah = (int) $bahanBaku->stok;

            RiwayatStok::create([
                'jenis_item'       => 'bahan_baku',
                'id_item'          => $bahanBaku->id,
                'jenis_pergerakan' => 'penyesuaian',
                'jumlah'           => $stokSesudah - $stokSebelum,
                'stok_sebelum'     => $stokSebelum,
                'stok_sesudah'     => $stokSesudah,
                'user_id'          => auth()->id(),
                'keterangan'       => 'Koreksi stok saat edit data bahan baku',
                'referensi_type'   => BahanBaku::class,
                'referensi_id'     => $bahanBaku->id,
            ]);
        }
    }

    /**
     * Handle the BahanBaku "deleted" event.
     */
    public function deleted(BahanBaku $bahanBaku): void
    {
        //
    }

    /**
     * Handle the BahanBaku "restored" event.
     */
    public function restored(BahanBaku $bahanBaku): void
    {
        //
    }

    /**
     * Handle the BahanBaku "force deleted" event.
     */
    public function forceDeleted(BahanBaku $bahanBaku): void
    {
        //
    }
}

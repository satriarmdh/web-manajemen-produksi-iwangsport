<?php

namespace App\Observers;

use App\Models\Produk;
use App\Models\RiwayatStok;

class ProdukObserver
{
    /**
     * Handle the Produk "created" event.
     * Catat stok awal saat produk pertama kali dibuat
     */
    public function created(Produk $produk): void
    {
        if ($produk->stok > 0) {
            RiwayatStok::create([
                'jenis_item'       => 'produk',
                'id_item'          => $produk->id,
                'jenis_pergerakan' => 'inisiasi data',
                'jumlah'           => $produk->stok,
                'stok_sebelum'     => 0,
                'stok_sesudah'     => $produk->stok,
                'user_id'          => auth()->id(),
                'keterangan'       => 'Stok awal produk ' . $produk->nama_produk,
                'referensi_type'   => Produk::class,
                'referensi_id'     => $produk->id,
            ]);
        }
    }

    /**
     * Handle the Produk "updated" event.
     * Catat perubahan stok sebagai 'penyesuaian' jika stok berubah saat edit data master.
     */
    public function updated(Produk $produk): void
    {
        if ($produk->isDirty('stok')) {
            $stokSebelum = (int) $produk->getOriginal('stok');
            $stokSesudah = (int) $produk->stok;

            RiwayatStok::create([
                'jenis_item'       => 'produk',
                'id_item'          => $produk->id,
                'jenis_pergerakan' => 'penyesuaian',
                'jumlah'           => $stokSesudah - $stokSebelum,
                'stok_sebelum'     => $stokSebelum,
                'stok_sesudah'     => $stokSesudah,
                'user_id'          => auth()->id(),
                'keterangan'       => 'Koreksi stok saat edit data produk',
                'referensi_type'   => Produk::class,
                'referensi_id'     => $produk->id,
            ]);
        }
    }

    /**
     * Handle the Produk "deleted" event.
     */
    public function deleted(Produk $produk): void
    {
        //
    }

    /**
     * Handle the Produk "restored" event.
     */
    public function restored(Produk $produk): void
    {
        //
    }

    /**
     * Handle the Produk "force deleted" event.
     */
    public function forceDeleted(Produk $produk): void
    {
        //
    }
}

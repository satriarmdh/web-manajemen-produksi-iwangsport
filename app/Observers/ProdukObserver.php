<?php

namespace App\Observers;

use App\Models\Produk;
use App\Models\StockMovement;

class ProdukObserver
{
    /**
     * Handle the Produk "created" event.
     * Catat stok awal saat produk pertama kali dibuat
     */
    public function created(Produk $produk): void
    {
        if ($produk->stok > 0) {
            StockMovement::record(
                'produk',
                $produk->id,
                'in',
                $produk->stok,
                0,
                $produk->stok,
                'Stok awal produk ' . $produk->nama_produk
            );
        }
    }

    /**
     * Handle the Produk "updated" event.
     * Perubahan stok saat edit data master ditangani di ProdukService (movement_type: adjustment).
     * Observer tidak mencatat ulang untuk menghindari duplikasi.
     */
    public function updated(Produk $produk): void
    {
        // Stock movement saat edit data master ditangani oleh ProdukService
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

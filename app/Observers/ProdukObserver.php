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
     * Catat perubahan stok jika ada
     */
    public function updated(Produk $produk): void
    {
        if ($produk->isDirty('stok')) {
            $previousStock = $produk->getOriginal('stok');
            $newStock = $produk->stok;
            $quantity = $newStock - $previousStock;

            if ($quantity > 0) {
                // Stok bertambah
                StockMovement::record(
                    'produk',
                    $produk->id,
                    'in',
                    $quantity,
                    $previousStock,
                    $newStock,
                    'Penambahan stok produk ' . $produk->nama_produk
                );
            } elseif ($quantity < 0) {
                // Stok berkurang
                StockMovement::record(
                    'produk',
                    $produk->id,
                    'out',
                    abs($quantity),
                    $previousStock,
                    $newStock,
                    'Pengurangan stok produk ' . $produk->nama_produk
                );
            }
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

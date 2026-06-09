<?php

namespace App\Observers;

use App\Models\BahanBaku;
use App\Models\StockMovement;

class BahanBakuObserver
{
    /**
     * Handle the BahanBaku "created" event.
     * Catat stok awal saat bahan baku pertama kali dibuat
     */
    public function created(BahanBaku $bahanBaku): void
    {
        if ($bahanBaku->stok > 0) {
            StockMovement::record(
                'bahan_baku',
                $bahanBaku->id,
                'in',
                $bahanBaku->stok,
                0,
                $bahanBaku->stok,
                'Stok awal bahan baku ' . $bahanBaku->nama_bahan
            );
        }
    }

    /**
     * Handle the BahanBaku "updated" event.
     * Catat perubahan stok jika ada
     */
    public function updated(BahanBaku $bahanBaku): void
    {
        if ($bahanBaku->isDirty('stok')) {
            $previousStock = $bahanBaku->getOriginal('stok');
            $newStock = $bahanBaku->stok;
            $quantity = $newStock - $previousStock;

            if ($quantity > 0) {
                // Stok bertambah
                StockMovement::record(
                    'bahan_baku',
                    $bahanBaku->id,
                    'in',
                    $quantity,
                    $previousStock,
                    $newStock,
                    'Penambahan stok bahan baku ' . $bahanBaku->nama_bahan
                );
            } elseif ($quantity < 0) {
                // Stok berkurang
                StockMovement::record(
                    'bahan_baku',
                    $bahanBaku->id,
                    'out',
                    abs($quantity),
                    $previousStock,
                    $newStock,
                    'Pengurangan stok bahan baku ' . $bahanBaku->nama_bahan
                );
            }
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

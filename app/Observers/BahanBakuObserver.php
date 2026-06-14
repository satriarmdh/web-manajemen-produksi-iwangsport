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
     * Perubahan stok saat edit data master ditangani di BahanBakuService (movement_type: adjustment).
     * Observer tidak mencatat ulang untuk menghindari duplikasi.
     */
    public function updated(BahanBaku $bahanBaku): void
    {
        // Stock movement saat edit data master ditangani oleh BahanBakuService
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

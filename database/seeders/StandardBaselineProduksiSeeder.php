<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\StandardBaselineProduksi;
use Illuminate\Database\Seeder;

class StandardBaselineProduksiSeeder extends Seeder
{
    public function run(): void
    {
        $baselines = [
            ['produk' => 'CLN-001', 'bahan' => 'KAIN-001', 'pcs_per_roll' => 120, 'toleransi_minus' => 5, 'keterangan' => 'Celana training pria normal bahan baby terry hitam.'],
            ['produk' => 'CLN-002', 'bahan' => 'KAIN-002', 'pcs_per_roll' => 118, 'toleransi_minus' => 5, 'keterangan' => 'Celana training wanita normal bahan baby terry navy.'],
            ['produk' => 'CLN-003', 'bahan' => 'KAIN-003', 'pcs_per_roll' => 120, 'toleransi_minus' => 6, 'keterangan' => 'Celana jogger normal bahan fleece abu-abu.'],
            ['produk' => 'CLN-004', 'bahan' => 'KAIN-001', 'pcs_per_roll' => 95, 'toleransi_minus' => 4, 'keterangan' => 'Celana training pria jumbo bahan baby terry hitam.'],
            ['produk' => 'CLN-005', 'bahan' => 'KAIN-002', 'pcs_per_roll' => 92, 'toleransi_minus' => 4, 'keterangan' => 'Celana training wanita jumbo bahan baby terry navy.'],
            ['produk' => 'CLN-006', 'bahan' => 'KAIN-001', 'pcs_per_roll' => 90, 'toleransi_minus' => 4, 'keterangan' => 'Celana jogger sport jumbo bahan baby terry hitam.'],
        ];

        foreach ($baselines as $baseline) {
            $produk = Produk::where('kode_produk', $baseline['produk'])->first();
            $bahan = BahanBaku::where('kode_bahan', $baseline['bahan'])->first();

            if (! $produk || ! $bahan) {
                continue;
            }

            StandardBaselineProduksi::updateOrCreate(
                [
                    'produk_id' => $produk->id,
                    'bahan_baku_id' => $bahan->id,
                ],
                [
                    'pcs_per_roll' => $baseline['pcs_per_roll'],
                    'toleransi_minus' => $baseline['toleransi_minus'],
                    'keterangan' => $baseline['keterangan'],
                    'is_aktif' => true,
                ]
            );
        }
    }
}

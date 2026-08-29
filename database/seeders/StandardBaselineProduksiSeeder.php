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
            ['produk' => 'CLN-001', 'bahan' => 'KAIN-001', 'pcs_per_roll' => 120, 'toleransi_minus' => 5, 'keterangan' => 'Baseline Celana Training Terry Hitam per roll Baby Terry.'],
            ['produk' => 'CLN-002', 'bahan' => 'KAIN-001', 'pcs_per_roll' => 120, 'toleransi_minus' => 5, 'keterangan' => 'Baseline Celana Training Terry Abu-abu per roll Baby Terry.'],
            ['produk' => 'CLN-003', 'bahan' => 'KAIN-002', 'pcs_per_roll' => 118, 'toleransi_minus' => 5, 'keterangan' => 'Baseline Celana Training Terry Navy per roll Baby Terry Navy.'],
            ['produk' => 'CLN-004', 'bahan' => 'KAIN-003', 'pcs_per_roll' => 110, 'toleransi_minus' => 5, 'keterangan' => 'Baseline Celana Jogger Diadora Hitam per roll Diadora.'],
            ['produk' => 'CLN-005', 'bahan' => 'KAIN-003', 'pcs_per_roll' => 110, 'toleransi_minus' => 5, 'keterangan' => 'Baseline Celana Jogger Diadora Abu-abu per roll Diadora.'],
            ['produk' => 'CLN-006', 'bahan' => 'KAIN-003', 'pcs_per_roll' => 108, 'toleransi_minus' => 4, 'keterangan' => 'Baseline Celana Jogger Diadora Navy per roll Diadora.'],
            ['produk' => 'CLN-007', 'bahan' => 'KAIN-001', 'pcs_per_roll' => 140, 'toleransi_minus' => 5, 'keterangan' => 'Baseline Celana BroadShort Hitam per roll.'],
            ['produk' => 'CLN-008', 'bahan' => 'KAIN-001', 'pcs_per_roll' => 140, 'toleransi_minus' => 5, 'keterangan' => 'Baseline Celana BroadShort Abu-abu per roll.'],
            ['produk' => 'CLN-010', 'bahan' => 'KAIN-003', 'pcs_per_roll' => 130, 'toleransi_minus' => 5, 'keterangan' => 'Baseline Celana Basket Hitam per roll.'],
            ['produk' => 'CLN-013', 'bahan' => 'KAIN-001', 'pcs_per_roll' => 160, 'toleransi_minus' => 8, 'keterangan' => 'Baseline Celana Boxer Hitam per roll.'],
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

        if ($this->command) {
            $this->command->info('StandardBaselineProduksiSeeder berhasil diperbarui!');
        }
    }
}

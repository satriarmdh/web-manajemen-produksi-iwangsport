<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use Illuminate\Database\Seeder;

class BahanBakuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bahanBaku = [
            // Kain
            [
                'kode_bahan' => 'KAIN-001',
                'nama_bahan' => 'Baby Terry',
                'warna' => 'hitam',
                'kategori' => 'kain',
                'satuan' => 'roll',
                'stok' => 10,
                'stok_minimal' => 2,
            ],
            [
                'kode_bahan' => 'KAIN-002',
                'nama_bahan' => 'Baby Terry',
                'warna' => 'navy',
                'kategori' => 'kain',
                'satuan' => 'roll',
                'stok' => 5,
                'stok_minimal' => 2,
            ],
            [
                'kode_bahan' => 'KAIN-003',
                'nama_bahan' => 'Diadora',
                'warna' => 'abu-abu',
                'kategori' => 'kain',
                'satuan' => 'roll',
                'stok' => 5,
                'stok_minimal' => 2,
            ],
            // Bahan Pendukung
            [
                'kode_bahan' => 'BPD-001',
                'nama_bahan' => 'Benang Jahit 1 Lusin (12pcs)',
                'warna' => 'hitam',
                'kategori' => 'bahan_pendukung',
                'satuan' => 'pcs',
                'stok' => 100,
                'stok_minimal' => 10,
            ],
            [
                'kode_bahan' => 'BPD-002',
                'nama_bahan' => 'Peniti 1 Box (500pcs)',
                'warna' => 'silver',
                'kategori' => 'bahan_pendukung',
                'satuan' => 'pcs',
                'stok' => 80,
                'stok_minimal' => 10,
            ],
            [
                'kode_bahan' => 'BPD-003',
                'nama_bahan' => 'Tali Peniti 1 Box (300pcs)',
                'warna' => 'hitam',
                'kategori' => 'bahan_pendukung',
                'satuan' => 'pcs',
                'stok' => 50,
                'stok_minimal' => 5,
            ],
            [
                'kode_bahan' => 'BPD-004',
                'nama_bahan' => 'Karet 30 Meter (40 Yard)',
                'warna' => 'putih',
                'kategori' => 'bahan_pendukung',
                'satuan' => 'pcs',
                'stok' => 20,
                'stok_minimal' => 5,
            ],
            [
                'kode_bahan' => 'BPD-005',
                'nama_bahan' => 'Label Tag 1 Pack (100 pcs)',
                'warna' => '-',
                'kategori' => 'bahan_pendukung',
                'satuan' => 'pcs',
                'stok' => 50,
                'stok_minimal' => 10,
            ],
            [
                'kode_bahan' => 'BPD-006',
                'nama_bahan' => 'List Training 25 Meter',
                'warna' => 'biru',
                'kategori' => 'bahan_pendukung',
                'satuan' => 'pcs',
                'stok' => 20,
                'stok_minimal' => 5,
            ],
        ];

        foreach ($bahanBaku as $bahan) {
            BahanBaku::updateOrCreate(
                ['kode_bahan' => $bahan['kode_bahan']],
                $bahan
            );
        }
    }
}

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
                'stok' => 200,
            ],
            [
                'kode_bahan' => 'KAIN-002',
                'nama_bahan' => 'Baby Terry',
                'warna' => 'navy',
                'kategori' => 'kain',
                'satuan' => 'roll',
                'stok' => 150,
            ],
            [
                'kode_bahan' => 'KAIN-003',
                'nama_bahan' => 'Fleece',
                'warna' => 'abu-abu',
                'kategori' => 'kain',
                'satuan' => 'roll',
                'stok' => 120,
            ],
            // Benang
            [
                'kode_bahan' => 'BNG-001',
                'nama_bahan' => 'Benang Jahit',
                'warna' => 'hitam',
                'kategori' => 'benang',
                'satuan' => 'pcs',
                'stok' => 100,
            ],
            [
                'kode_bahan' => 'BNG-002',
                'nama_bahan' => 'Benang Jahit',
                'warna' => 'putih',
                'kategori' => 'benang',
                'satuan' => 'pcs',
                'stok' => 80,
            ],
            // Kancing
            [
                'kode_bahan' => 'KNC-001',
                'nama_bahan' => 'Kancing Plastik',
                'warna' => 'hitam',
                'kategori' => 'kancing',
                'satuan' => 'pcs',
                'stok' => 20,
            ],
            // Resleting
            [
                'kode_bahan' => 'RSL-001',
                'nama_bahan' => 'Resleting Nylon 20cm',
                'warna' => 'hitam',
                'kategori' => 'resleting',
                'satuan' => 'pcs',
                'stok' => 2,
            ],
            // Aksesoris
            [
                'kode_bahan' => 'AKS-001',
                'nama_bahan' => 'Karet Pinggang 5cm',
                'warna' => 'hitam',
                'kategori' => 'aksesoris',
                'satuan' => 'pcs',
                'stok' => 10,
            ],
        ];

        foreach ($bahanBaku as $bahan) {
            BahanBaku::create($bahan);
        }
    }
}

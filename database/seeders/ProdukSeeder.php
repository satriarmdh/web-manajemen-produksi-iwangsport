<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $produks = [
            // Celana Training Terry
            [
                'kode_produk' => 'CLN-001',
                'nama_produk' => 'Celana Training Terry',
                'ukuran' => 'normal',
                'warna' => 'hitam',
                'harga_satuan' => 85000,
                'satuan' => 'pcs',
                'stok' => 1000,
                'stok_minimal' => 200,
                'is_aktif' => 1,
            ],
            [
                'kode_produk' => 'CLN-002',
                'nama_produk' => 'Celana Training Terry',
                'ukuran' => 'normal',
                'warna' => 'abu-abu',
                'harga_satuan' => 85000,
                'satuan' => 'pcs',
                'stok' => 400,
                'stok_minimal' => 100,
                'is_aktif' => 1,
            ],
            [
                'kode_produk' => 'CLN-003',
                'nama_produk' => 'Celana Training Terry',
                'ukuran' => 'normal',
                'warna' => 'navy',
                'harga_satuan' => 85000,
                'satuan' => 'pcs',
                'stok' => 500,
                'stok_minimal' => 100,
                'is_aktif' => 1,
            ],
            // Celana Jogger Diadora
            [
                'kode_produk' => 'CLN-004',
                'nama_produk' => 'Celana Jogger Diadora',
                'ukuran' => 'normal',
                'warna' => 'hitam',
                'harga_satuan' => 95000,
                'satuan' => 'pcs',
                'stok' => 300,
                'stok_minimal' => 100,
                'is_aktif' => 1,
            ],
            [
                'kode_produk' => 'CLN-005',
                'nama_produk' => 'Celana Jogger Diadora',
                'ukuran' => 'normal',
                'warna' => 'abu-abu',
                'harga_satuan' => 95000,
                'satuan' => 'pcs',
                'stok' => 200,
                'stok_minimal' => 100,
                'is_aktif' => 1,
            ],
            [
                'kode_produk' => 'CLN-006',
                'nama_produk' => 'Celana Jogger Diadora',
                'ukuran' => 'normal',
                'warna' => 'navy',
                'harga_satuan' => 95000,
                'satuan' => 'pcs',
                'stok' => 400,
                'stok_minimal' => 200,
                'is_aktif' => 1,
            ],
            // Celana BroadShort
            [
                'kode_produk' => 'CLN-007',
                'nama_produk' => 'Celana BroadShort',
                'ukuran' => 'normal',
                'warna' => 'hitam',
                'harga_satuan' => 65000,
                'satuan' => 'pcs',
                'stok' => 500,
                'stok_minimal' => 200,
                'is_aktif' => 1,
            ],
            [
                'kode_produk' => 'CLN-008',
                'nama_produk' => 'Celana BroadShort',
                'ukuran' => 'normal',
                'warna' => 'abu-abu',
                'harga_satuan' => 65000,
                'satuan' => 'pcs',
                'stok' => 200,
                'stok_minimal' => 75,
                'is_aktif' => 1,
            ],
            [
                'kode_produk' => 'CLN-009',
                'nama_produk' => 'Celana BroadShort',
                'ukuran' => 'normal',
                'warna' => 'silver',
                'harga_satuan' => 65000,
                'satuan' => 'pcs',
                'stok' => 200,
                'stok_minimal' => 75,
                'is_aktif' => 1,
            ],
            // Celana Basket
            [
                'kode_produk' => 'CLN-010',
                'nama_produk' => 'Celana Basket',
                'ukuran' => 'normal',
                'warna' => 'hitam',
                'harga_satuan' => 75000,
                'satuan' => 'pcs',
                'stok' => 800,
                'stok_minimal' => 300,
                'is_aktif' => 1,
            ],
            [
                'kode_produk' => 'CLN-011',
                'nama_produk' => 'Celana Basket',
                'ukuran' => 'normal',
                'warna' => 'silver',
                'harga_satuan' => 75000,
                'satuan' => 'pcs',
                'stok' => 300,
                'stok_minimal' => 100,
                'is_aktif' => 1,
            ],
            [
                'kode_produk' => 'CLN-012',
                'nama_produk' => 'Celana Basket',
                'ukuran' => 'normal',
                'warna' => 'biru',
                'harga_satuan' => 75000,
                'satuan' => 'pcs',
                'stok' => 100,
                'stok_minimal' => 100,
                'is_aktif' => 1,
            ],
            // Celana Boxer
            [
                'kode_produk' => 'CLN-013',
                'nama_produk' => 'Celana Boxer',
                'ukuran' => 'normal',
                'warna' => 'hitam',
                'harga_satuan' => 50000,
                'satuan' => 'pcs',
                'stok' => 600,
                'stok_minimal' => 150,
                'is_aktif' => 1,
            ],
            [
                'kode_produk' => 'CLN-014',
                'nama_produk' => 'Celana Boxer',
                'ukuran' => 'normal',
                'warna' => 'silver',
                'harga_satuan' => 50000,
                'satuan' => 'pcs',
                'stok' => 400,
                'stok_minimal' => 100,
                'is_aktif' => 1,
            ],
            [
                'kode_produk' => 'CLN-015',
                'nama_produk' => 'Celana Boxer',
                'ukuran' => 'normal',
                'warna' => 'biru',
                'harga_satuan' => 50000,
                'satuan' => 'pcs',
                'stok' => 300,
                'stok_minimal' => 75,
                'is_aktif' => 1,
            ],
        ];

        foreach ($produks as $produk) {
            Produk::updateOrCreate(
                ['kode_produk' => $produk['kode_produk']],
                $produk
            );
        }
    }
}

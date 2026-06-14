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
            // Celana Olahraga - Ukuran Normal
            [
                'kode_produk' => 'CLN-001',
                'nama_produk' => 'Celana Training Pria',
                'ukuran' => 'normal',
                'warna' => 'hitam',
                'harga_satuan' => 85000,
                'satuan' => 'pcs',
                'stok' => 0,
            ],
            [
                'kode_produk' => 'CLN-002',
                'nama_produk' => 'Celana Training Wanita',
                'ukuran' => 'normal',
                'warna' => 'navy',
                'harga_satuan' => 85000,
                'satuan' => 'pcs',
                'stok' => 0,
            ],
            [
                'kode_produk' => 'CLN-003',
                'nama_produk' => 'Celana Jogger',
                'ukuran' => 'normal',
                'warna' => 'abu-abu',
                'harga_satuan' => 95000,
                'satuan' => 'pcs',
                'stok' => 0,
            ],
            // Celana Olahraga - Ukuran Jumbo
            [
                'kode_produk' => 'CLN-004',
                'nama_produk' => 'Celana Training Pria Jumbo',
                'ukuran' => 'jumbo',
                'warna' => 'hitam',
                'harga_satuan' => 100000,
                'satuan' => 'pcs',
                'stok' => 0,
            ],
            [
                'kode_produk' => 'CLN-005',
                'nama_produk' => 'Celana Training Wanita Jumbo',
                'ukuran' => 'jumbo',
                'warna' => 'navy',
                'harga_satuan' => 100000,
                'satuan' => 'pcs',
                'stok' => 0,
            ],
            [
                'kode_produk' => 'CLN-006',
                'nama_produk' => 'Celana Jogger Sport Jumbo',
                'ukuran' => 'jumbo',
                'warna' => 'hitam',
                'harga_satuan' => 110000,
                'satuan' => 'pcs',
                'stok' => 0,
            ],
        ];

        foreach ($produks as $produk) {
            Produk::create($produk);
        }
    }
}

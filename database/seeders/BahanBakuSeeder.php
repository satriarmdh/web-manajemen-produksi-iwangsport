<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BahanBakuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BahanBaku::create([
            'kode_bahan' => 'KAIN-001',
            'nama_bahan' => 'Kain Combed 30s',
            'warna' => 'hitam',
            'kategori' => 'kain',
            'satuan' => 'roll',
            'stok' => '1',
        ]);
    }
}

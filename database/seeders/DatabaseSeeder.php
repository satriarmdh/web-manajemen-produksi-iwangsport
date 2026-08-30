<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SupplierSeeder::class,
            PelangganSeeder::class,
            BahanBakuSeeder::class,
            ProdukSeeder::class,
            StandardBaselineProduksiSeeder::class,
            // PergerakanStokSeeder::class,
            PenjualanSeeder::class,
            // PerintahProduksiTestingSeeder::class,
        ]);
    }
}

<?php

namespace Database\Seeders;

// use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        ]);
    }
}

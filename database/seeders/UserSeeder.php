<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Membuat 1 data Owner utama (untuk login testing Anda)
        User::create([
            'name' => 'owner',
            'email' => 'owner@gmail.com',
            'alamat' => 'Jl. Jendral Sudirman No. 123, Jakarta',
            'no_hp' => '081234567890',
            'role' => 'owner',
            'jenis_kelamin' => 'laki-laki',
            'password' => Hash::make('password123'),
        ]);

        // 2. admin
        User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'alamat' => 'Jl. Rungkut',
            'no_hp' => '081234567890',
            'role' => 'admin',
            'jenis_kelamin' => 'laki-laki',
            'password' => Hash::make('password123'),
        ]);
    }
}

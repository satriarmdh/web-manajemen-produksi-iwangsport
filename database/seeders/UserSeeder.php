<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'owner',
                'email' => 'owner@gmail.com',
                'alamat' => 'Jl. Jendral Sudirman No. 123, Jakarta',
                'no_hp' => '081234567890',
                'role' => 'owner',
                'jenis_kelamin' => 'laki-laki',
            ],
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'alamat' => 'Jl. Rungkut, Surabaya',
                'no_hp' => '081234567891',
                'role' => 'admin',
                'jenis_kelamin' => 'laki-laki',
            ],
            [
                'name' => 'potong',
                'email' => 'potong@gmail.com',
                'alamat' => 'Jl. Rungkut, Surabaya',
                'no_hp' => '081234567892',
                'role' => 'potong',
                'jenis_kelamin' => 'laki-laki',
            ],
            [
                'name' => 'jahit',
                'email' => 'jahit@gmail.com',
                'alamat' => 'Jl. Rungkut, Surabaya',
                'no_hp' => '081234567893',
                'role' => 'jahit',
                'jenis_kelamin' => 'perempuan',
            ],
            [
                'name' => 'agus',
                'email' => 'agus@gmail.com',
                'alamat' => 'Jl. Rungkut, Surabaya',
                'no_hp' => '081234567894',
                'role' => 'jahit',
                'jenis_kelamin' => 'perempuan',
            ],
            [
                'name' => 'reza',
                'email' => 'reza@gmail.com',
                'alamat' => 'Jl. Rungkut, Surabaya',
                'no_hp' => '081234567894',
                'role' => 'finishing',
                'jenis_kelamin' => 'perempuan',
            ],
            [
                'name' => 'finishing',
                'email' => 'finishing@gmail.com',
                'alamat' => 'Jl. Rungkut, Surabaya',
                'no_hp' => '081234567894',
                'role' => 'finishing',
                'jenis_kelamin' => 'perempuan',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                array_merge($user, [
                    'password' => Hash::make('password123'),
                ])
            );
        }
    }
}

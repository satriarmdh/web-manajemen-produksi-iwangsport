<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use Illuminate\Database\Seeder;

class PelangganSeeder extends Seeder
{
    public function run(): void
    {
        $pelanggans = [
            [
                'kode_pelanggan' => 'PLG-001',
                'nama_pelanggan' => 'Toko Sport Jaya',
                'no_telp' => '081222334455',
                'email' => 'sportjaya@example.com',
                'alamat' => 'Jl. Raya Darmo No. 21, Surabaya',
                'keterangan' => 'Pelanggan grosir celana olahraga area Surabaya.',
                'is_aktif' => true,
            ],
            [
                'kode_pelanggan' => 'PLG-002',
                'nama_pelanggan' => 'Agen Iwangsport Malang',
                'no_telp' => '082233445566',
                'email' => 'agen.malang@example.com',
                'alamat' => 'Jl. Soekarno Hatta No. 18, Malang',
                'keterangan' => 'Agen reseller wilayah Malang dan sekitarnya.',
                'is_aktif' => true,
            ],
            [
                'kode_pelanggan' => 'PLG-003',
                'nama_pelanggan' => 'Komunitas Lari Sidoarjo',
                'no_telp' => '083344556677',
                'email' => 'lari.sidoarjo@example.com',
                'alamat' => 'Jl. Pahlawan No. 8, Sidoarjo',
                'keterangan' => 'Pelanggan komunitas untuk pesanan seragam olahraga.',
                'is_aktif' => true,
            ],
        ];

        foreach ($pelanggans as $pelanggan) {
            Pelanggan::updateOrCreate(
                ['kode_pelanggan' => $pelanggan['kode_pelanggan']],
                $pelanggan
            );
        }
    }
}

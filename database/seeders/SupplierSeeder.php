<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'kode_supplier' => 'SUP-001',
                'nama_supplier' => 'CV Maju Jaya Textile',
                'kategori' => ['kain'],
                'kontak' => '081234567890',
                'email' => 'info@majujayatextile.com',
                'alamat' => 'Jl. Industri Tekstil No. 45, Bandung, Jawa Barat',
                'catatan' => 'Supplier utama kain baby terry, fleece, dan diadora. Pengiriman setiap hari Senin dan Kamis.',
                'is_aktif' => '1',
            ],
            [
                'kode_supplier' => 'SUP-002',
                'nama_supplier' => 'UD Sumber Benang Indah',
                'kategori' => ['bahan_pendukung'],
                'kontak' => '082345678901',
                'email' => 'sales@sumberbenang.com',
                'alamat' => 'Jl. Raya Tekstil No. 12, Solo, Jawa Tengah',
                'catatan' => 'Supplier benang jahit dan benang obras berbagai warna.',
                'is_aktif' => '1',
            ],
            [
                'kode_supplier' => 'SUP-003',
                'nama_supplier' => 'PT Aksesoris Garmen Nusantara',
                'kategori' => ['bahan_pendukung'],
                'kontak' => '083456789012',
                'email' => 'order@aksesorisgarmen.co.id',
                'alamat' => 'Jl. Garmen Raya No. 78, Semarang, Jawa Tengah',
                'catatan' => 'Supplier peniti, tali peniti, kolor, karet, dan aksesoris garmen lainnya.',
                'is_aktif' => '1',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(
                ['kode_supplier' => $supplier['kode_supplier']],
                $supplier
            );
        }
    }
}

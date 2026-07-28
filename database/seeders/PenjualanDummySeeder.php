<?php

namespace Database\Seeders;

use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key checks untuk melakukan truncate data penjualan lama
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DetailPenjualan::truncate();
        Penjualan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $pelanggans = Pelanggan::all();
        $produks = Produk::all();
        $user = User::where('role', 'admin')->first() ?? User::first();

        if ($pelanggans->isEmpty() || $produks->isEmpty() || !$user) {
            $this->command->error('Pastikan PelangganSeeder, ProdukSeeder, dan UserSeeder sudah dijalankan terlebih dahulu!');
            return;
        }

        $now = Carbon::now();
        $this->command->info('Memulai seeding data penjualan dummy untuk 730 hari terakhir...');

        $invoiceCounter = 1;

        // Loop mundur dari 730 hari lalu sampai hari ini
        for ($i = 730; $i >= 0; $i--) {
            $date = (clone $now)->subDays($i);

            // Naikkan kuantitas / probabilitas transaksi seiring mendekati hari ini
            // agar memicu kenaikan tren (persentase delta positif)
            if ($i > 365) {
                // Hari 730 s.d 366 (Tahun lalu): volume kecil
                $minQty = 5;
                $maxQty = 15;
                $chanceOfSale = 0.4; // 40% kemungkinan transaksi per hari
            } elseif ($i > 90) {
                // Hari 365 s.d 91: sedang
                $minQty = 10;
                $maxQty = 25;
                $chanceOfSale = 0.55;
            } elseif ($i > 30) {
                // Hari 90 s.d 31: cukup ramai
                $minQty = 15;
                $maxQty = 35;
                $chanceOfSale = 0.65;
            } elseif ($i > 7) {
                // Hari 30 s.d 8: ramai
                $minQty = 20;
                $maxQty = 45;
                $chanceOfSale = 0.75;
            } else {
                // 7 hari terakhir: sangat ramai
                $minQty = 30;
                $maxQty = 65;
                $chanceOfSale = 0.85;
            }

            // Tentukan apakah hari ini ada transaksi berdasarkan chanceOfSale
            if (rand(1, 100) > ($chanceOfSale * 100)) {
                continue;
            }

            // Jumlah transaksi per hari (antara 1 s.d 2 transaksi)
            $salesCount = rand(1, 2);

            for ($s = 0; $s < $salesCount; $s++) {
                $pelanggan = $pelanggans->random();
                $invoiceNum = 'INV/' . $date->format('Ymd') . '/' . str_pad($invoiceCounter++, 5, '0', STR_PAD_LEFT);

                // Buat data Penjualan utama
                $penjualan = Penjualan::create([
                    'nomor_invoice' => $invoiceNum,
                    'pelanggan_id' => $pelanggan->id,
                    'tanggal' => $date->format('Y-m-d'),
                    'total_item' => 0,
                    'total_harga' => 0,
                    'catatan' => 'Data dummy penjualan otomatis untuk visualisasi chart.',
                    'user_id' => $user->id,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                // Pilih 1 hingga 3 produk secara acak
                $selectedProduks = $produks->random(rand(1, 3));
                $totalItem = 0;
                $totalHarga = 0;

                foreach ($selectedProduks as $produk) {
                    $qty = rand($minQty, $maxQty);
                    $subtotal = $qty * $produk->harga_satuan;

                    DetailPenjualan::create([
                        'penjualan_id' => $penjualan->id,
                        'produk_id' => $produk->id,
                        'qty' => $qty,
                        'harga_satuan' => $produk->harga_satuan,
                        'subtotal' => $subtotal,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);

                    $totalItem += $qty;
                    $totalHarga += $subtotal;
                }

                // Perbarui total_item dan total_harga pada tabel Penjualan
                $penjualan->update([
                    'total_item' => $totalItem,
                    'total_harga' => $totalHarga,
                ]);
            }
        }

        $this->command->info('Seeding data penjualan dummy selesai!');
    }
}

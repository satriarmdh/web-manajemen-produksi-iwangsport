<?php

namespace Database\Seeders;

use App\Models\DetailPenjualan;
use App\Models\Pelanggan;
use App\Models\PembayaranPenjualan;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\RiwayatStok;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate data penjualan & pembayaran lama secara aman
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PembayaranPenjualan::truncate();
        DetailPenjualan::truncate();
        Penjualan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $pelanggans = Pelanggan::all();
        $produks = Produk::all();
        $user = User::where('role', 'admin')->first() ?? User::first();

        if ($pelanggans->isEmpty() || $produks->isEmpty() || ! $user) {
            $this->command->error('Pastikan PelangganSeeder, ProdukSeeder, dan UserSeeder sudah dijalankan!');

            return;
        }

        // Daftar file bukti pembayaran riil yang ada di storage/app/public/bukti-penjualan
        $buktiImages = [
            'bukti-penjualan/4XEDY7SnOwbuabn6whTiPmb44tEIPDYP5qEKcrPr.jpg',
            'bukti-penjualan/E7zNTk1N1NWmM9QVbZJ3pdElTtPDBjFuTyPKuyWM.jpg',
            'bukti-penjualan/hBTgCZxA5RpCmG3HJPs0HWXEd4sYgRh4BkrECAxq.jpg',
            'bukti-penjualan/iantrPRSLv2R203kbkwGH4aAQ8yONYblsUwxUcAJ.jpg',
            'bukti-penjualan/JuNkTj8eFsC9zn2gqjEB2k1ZrxZStyUz9RqFGh8i.jpg',
            'bukti-penjualan/LQM4DgyWcFusX2mTNp423ckyt28E0uu3sXrzHXyZ.jpg',
            'bukti-penjualan/S0m0GMGk4QG36NUbkfws83PQGbOBKuMfhFB89RmS.jpg',
            'bukti-penjualan/uMJCuh5wOaPA86s6JG9enm9UZOkzZtryYyfXIPZQ.jpg',
            'bukti-penjualan/uSVU8WTO8eFhprWsL3h5TbnI9msozmKqV1XH33SD.jpg',
            'bukti-penjualan/Zj5ZfhGwCs4QMwRvap2xBZtRNMTYYyLuPBR5LNrZ.jpg',
        ];

        // Sebar transaksi secara alami dalam 2 bulan terakhir (Juli - Agustus 2026)
        $dates = [
            '2026-07-02 10:15:00',
            '2026-07-05 14:30:00',
            '2026-07-08 09:45:00',
            '2026-07-12 11:20:00',
            '2026-07-15 16:00:00',
            '2026-07-18 13:10:00',
            '2026-07-22 10:00:00',
            '2026-07-25 15:40:00',
            '2026-07-28 11:30:00',
            '2026-07-31 09:15:00',
            '2026-08-02 14:00:00',
            '2026-08-05 10:45:00',
            '2026-08-08 16:20:00',
            '2026-08-11 11:10:00',
            '2026-08-14 13:30:00',
            '2026-08-17 09:50:00',
            '2026-08-20 15:00:00',
            '2026-08-23 10:30:00',
            '2026-08-26 14:15:00',
            '2026-08-28 11:00:00',
        ];

        $invoiceCounter = [];

        foreach ($dates as $index => $datetimeStr) {
            $dt = Carbon::parse($datetimeStr);
            $dateOnly = $dt->toDateString();
            $dateYmd = $dt->format('Ymd');

            if (! isset($invoiceCounter[$dateYmd])) {
                $invoiceCounter[$dateYmd] = 1;
            } else {
                $invoiceCounter[$dateYmd]++;
            }

            $nomorInvoice = 'INV-'.$dateYmd.'-'.str_pad($invoiceCounter[$dateYmd], 4, '0', STR_PAD_LEFT);
            $pelanggan = $pelanggans->random();

            // Pilih 1 hingga 3 jenis produk acak untuk transaksi ini
            $selectedProduks = $produks->random(rand(1, 3));
            $totalItem = 0;
            $totalHarga = 0;

            // 1. Buat Header Penjualan
            $penjualan = Penjualan::create([
                'nomor_invoice' => $nomorInvoice,
                'pelanggan_id' => $pelanggan->id,
                'tanggal' => $dateOnly,
                'total_item' => 0,
                'total_harga' => 0,
                'catatan' => 'Pesanan grosir produk olahraga '.$pelanggan->nama_pelanggan,
                'user_id' => $user->id,
                'created_at' => $dt,
                'updated_at' => $dt,
            ]);

            // 2. Buat Detail Penjualan & Perbarui Stok
            foreach ($selectedProduks as $produk) {
                $qty = rand(15, 60);
                $hargaSatuan = $produk->harga_satuan;
                $subtotal = $qty * $hargaSatuan;

                DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'produk_id' => $produk->id,
                    'qty' => $qty,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                    'created_at' => $dt,
                    'updated_at' => $dt,
                ]);

                $totalItem += $qty;
                $totalHarga += $subtotal;

                // Riwayat Stok Keluar untuk Audit Trail
                $stokSebelum = $produk->stok;
                $stokSesudah = max(0, $stokSebelum - $qty);
                $produk->update(['stok' => $stokSesudah]);

                RiwayatStok::create([
                    'jenis_item' => 'produk',
                    'id_item' => $produk->id,
                    'jenis_pergerakan' => 'keluar',
                    'jumlah' => $qty,
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSesudah,
                    'user_id' => $user->id,
                    'keterangan' => "Penjualan {$nomorInvoice} ke {$pelanggan->nama_pelanggan}",
                    'referensi_type' => 'penjualan',
                    'referensi_id' => $penjualan->id,
                    'created_at' => $dt,
                    'updated_at' => $dt,
                ]);
            }

            // Update Total Header
            $penjualan->update([
                'total_item' => $totalItem,
                'total_harga' => $totalHarga,
            ]);

            // 3. Buat Pembayaran Penjualan (Lunas)
            $metode = rand(0, 1) === 0 ? 'transfer' : 'tunai';
            $buktiPath = $metode === 'transfer' ? $buktiImages[$index % count($buktiImages)] : null;

            PembayaranPenjualan::create([
                'penjualan_id' => $penjualan->id,
                'user_id' => $user->id,
                'tanggal_bayar' => $dt,
                'jumlah_bayar' => $totalHarga,
                'metode_pembayaran' => $metode,
                'catatan' => $metode === 'transfer' ? 'Pembayaran lunas via Transfer Bank' : 'Pembayaran lunas secara Tunai',
                'bukti_pembayaran' => $buktiPath,
                'created_at' => $dt,
                'updated_at' => $dt,
            ]);
        }

        $this->command->info('PenjualanSeeder berhasil membuat 20 transaksi valid 2 bulan terakhir!');
    }
}

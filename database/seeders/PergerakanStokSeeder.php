<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use App\Models\DetailPergerakanStokBahanBaku;
use App\Models\PergerakanStokBahanBaku;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PergerakanStokSeeder extends Seeder
{
    /**
     * Run the database seeds for stock movement pagination testing.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DetailPergerakanStokBahanBaku::truncate();
        PergerakanStokBahanBaku::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $suppliers = Supplier::all();
        $bahanBakus = BahanBaku::all();
        $user = User::where('role', 'admin')->first() ?? User::first();

        if ($suppliers->isEmpty() || $bahanBakus->isEmpty() || ! $user) {
            if ($this->command) {
                $this->command->error('Pastikan SupplierSeeder, BahanBakuSeeder, dan UserSeeder sudah dijalankan!');
            }

            return;
        }

        $penerimaList = [
            'Tim Pemotongan (Bagian Potong)',
            'Tim Penjahitan (Karyawan Jahit)',
            'Gudang Produksi Utama',
            'Vendor Makloon Jahit',
            'Bagian Finishing & QC',
        ];

        // 1. Buat 15 Transaksi Stok Masuk (untuk pengujian pagination tab stok masuk > 10 data)
        for ($i = 1; $i <= 15; $i++) {
            $dt = Carbon::now()->subDays(60 - ($i * 3))->setHour(9)->setMinute(15 * ($i % 4));
            $dateYmd = $dt->format('Ymd');
            $nomorTrx = 'TRX-IN-'.$dateYmd.'-'.str_pad($i, 4, '0', STR_PAD_LEFT);
            $supplier = $suppliers->random();

            $pergerakan = PergerakanStokBahanBaku::create([
                'nomor_transaksi' => $nomorTrx,
                'jenis_pergerakan' => 'masuk',
                'tanggal' => $dt->toDateString(),
                'supplier_id' => $supplier->id,
                'penerima' => null,
                'catatan' => 'Penerimaan pasokan bahan baku dari supplier '.$supplier->nama_supplier,
                'user_id' => $user->id,
                'created_at' => $dt,
                'updated_at' => $dt,
            ]);

            // Tambahkan 1 - 3 detail bahan baku
            $selectedBahans = $bahanBakus->random(rand(1, 3));
            foreach ($selectedBahans as $bahan) {
                $jumlah = $bahan->kategori === 'kain' ? rand(5, 20) : rand(50, 200);

                DetailPergerakanStokBahanBaku::create([
                    'pergerakan_stok_bahan_baku_id' => $pergerakan->id,
                    'bahan_baku_id' => $bahan->id,
                    'jumlah' => $jumlah,
                    'created_at' => $dt,
                    'updated_at' => $dt,
                ]);
            }
        }

        // 2. Buat 15 Transaksi Stok Keluar (untuk pengujian pagination tab stok keluar > 10 data)
        for ($i = 1; $i <= 15; $i++) {
            $dt = Carbon::now()->subDays(58 - ($i * 3))->setHour(14)->setMinute(10 * ($i % 5));
            $dateYmd = $dt->format('Ymd');
            $nomorTrx = 'TRX-OUT-'.$dateYmd.'-'.str_pad($i, 4, '0', STR_PAD_LEFT);
            $penerima = $penerimaList[($i - 1) % count($penerimaList)];

            $pergerakan = PergerakanStokBahanBaku::create([
                'nomor_transaksi' => $nomorTrx,
                'jenis_pergerakan' => 'keluar',
                'tanggal' => $dt->toDateString(),
                'supplier_id' => null,
                'penerima' => $penerima,
                'catatan' => 'Pengeluaran bahan baku produksi untuk '.$penerima,
                'user_id' => $user->id,
                'created_at' => $dt,
                'updated_at' => $dt,
            ]);

            // Tambahkan 1 - 3 detail bahan baku
            $selectedBahans = $bahanBakus->random(rand(1, 3));
            foreach ($selectedBahans as $bahan) {
                $jumlah = $bahan->kategori === 'kain' ? rand(2, 10) : rand(20, 100);

                DetailPergerakanStokBahanBaku::create([
                    'pergerakan_stok_bahan_baku_id' => $pergerakan->id,
                    'bahan_baku_id' => $bahan->id,
                    'jumlah' => $jumlah,
                    'created_at' => $dt,
                    'updated_at' => $dt,
                ]);
            }
        }

        if ($this->command) {
            $this->command->info('PergerakanStokSeeder berhasil membuat 15 data Stok Masuk dan 15 data Stok Keluar untuk pengujian pagination!');
        }
    }
}

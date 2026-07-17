<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use App\Models\DetailPerintahProduksi;
use App\Models\PerintahProduksi;
use App\Models\Produk;
use App\Models\StokVirtual;
use App\Models\User;
use Illuminate\Database\Seeder;

class PerintahProduksiTestingSeeder extends Seeder
{
    public function run(): void
    {
        // $admin = User::where('role', 'admin')->first() ?? User::first();
        // $approver = User::where('role', 'owner')->first() ?? $admin;
        // $potong = User::where('role', 'potong')->first();
        // $bahanBaku = BahanBaku::where('kategori', 'kain')->first() ?? BahanBaku::first();
        // $produkList = Produk::orderBy('id')->take(6)->get();

        // if (! $admin || ! $approver || ! $potong || ! $bahanBaku || $produkList->isEmpty()) {
        //     $this->command?->warn('Seeder PerintahProduksiTestingSeeder dilewati: data user, bahan baku, atau produk belum tersedia. Jalankan seeder utama terlebih dahulu.');
        //     return;
        // }

        // $perintahProduksi = [
        //     [
        //         'nomor_wo' => 'TEST-PP-001',
        //         'tgl_mulai' => now()->toDateString(),
        //         'detail_count' => 2,
        //     ],
        //     [
        //         'nomor_wo' => 'TEST-PP-002',
        //         'tgl_mulai' => now()->addDay()->toDateString(),
        //         'detail_count' => 3,
        //     ],
        //     [
        //         'nomor_wo' => 'TEST-PP-003',
        //         'tgl_mulai' => now()->addDays(2)->toDateString(),
        //         'detail_count' => 4,
        //     ],
        // ];

        // foreach ($perintahProduksi as $index => $data) {
        //     $perintah = PerintahProduksi::updateOrCreate(
        //         ['nomor_wo' => $data['nomor_wo']],
        //         [
        //             'tgl_mulai' => $data['tgl_mulai'],
        //             'tgl_selesai' => null,
        //             'status_produksi' => 'disetujui',
        //             'user_id' => $admin->id,
        //             'approved_by' => $approver->id,
        //             'approved_at' => now(),
        //             'alasan_penolakan' => null,
        //         ]
        //     );

        //     StokVirtual::where('id_perintah', $perintah->id)->delete();
        //     DetailPerintahProduksi::where('perintah_produksi_id', $perintah->id)->delete();

        //     $produkUntukPerintah = collect(range(0, $data['detail_count'] - 1))
        //         ->map(fn (int $detailIndex) => $produkList[($index + $detailIndex) % $produkList->count()]);

        //     foreach ($produkUntukPerintah as $detailIndex => $produk) {
        //         $estimasiPcs = 150 + (($index + $detailIndex) * 50);

        //         $detail = DetailPerintahProduksi::create([
        //             'perintah_produksi_id' => $perintah->id,
        //             'produk_id' => $produk->id,
        //             'bahan_baku_id' => $bahanBaku->id,
        //             'qty_roll_pakai' => 2 + $detailIndex,
        //             'estimasi_pcs' => $estimasiPcs,
        //             'toleransi_minus' => 5,
        //             'qty_pcs_potong' => $estimasiPcs,
        //             'status_validasi_potong' => 'normal',
        //             'alasan' => null,
        //         ]);

        //         StokVirtual::create([
        //             'id_perintah' => $perintah->id,
        //             'id_detail_perintah' => $detail->id,
        //             'id_karyawan' => $potong->id,
        //             'id_produk' => $produk->id,
        //             'peran' => 'potong',
        //             'qty_hold' => $estimasiPcs,
        //             'total_selesai' => $estimasiPcs,
        //             'total_reject' => 0,
        //             'status_barang' => 'Ready',
        //             'is_selesai' => true,
        //         ]);
        //     }
        // }
    }
}

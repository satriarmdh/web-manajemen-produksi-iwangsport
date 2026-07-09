<?php

namespace App\Services;

use App\Models\AjuanPengambilanProduksi;
use App\Models\MutasiProduksi;
use App\Models\StokVirtual;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AjuanPengambilanProduksiService
{
    public function store(array $data, User $user): AjuanPengambilanProduksi
    {
        return DB::transaction(function () use ($data, $user) {
            $stokSumber = StokVirtual::lockForUpdate()->findOrFail($data['stok_virtual_id']);
            $qtyAjuan = (int) $data['qty_ajuan'];

            return AjuanPengambilanProduksi::create([
                'id_perintah' => $stokSumber->id_perintah,
                'id_detail_perintah' => $stokSumber->id_detail_perintah,
                'id_produk' => $stokSumber->id_produk,
                'dari_karyawan_id' => $stokSumber->id_karyawan,
                'ke_karyawan_id' => $user->id,
                'dari_tahapan' => $stokSumber->peran,
                'ke_tahapan' => $user->role,
                'qty_ajuan' => $qtyAjuan,
                'status' => 'pending',
                'catatan_pengaju' => $data['catatan_pengaju'] ?? null,
                'tgl_ajuan' => now(),
            ]);
        });
    }

    public function storeMany(array $items, User $user, ?string $catatanPengaju = null): void
    {
        DB::transaction(function () use ($items, $user, $catatanPengaju) {
            foreach ($items as $item) {
                $stokSumber = StokVirtual::lockForUpdate()->findOrFail($item['stok_virtual_id']);
                $qtyAjuan = (int) $item['qty_ajuan'];

                AjuanPengambilanProduksi::create([
                    'id_perintah' => $stokSumber->id_perintah,
                    'id_detail_perintah' => $stokSumber->id_detail_perintah,
                    'id_produk' => $stokSumber->id_produk,
                    'dari_karyawan_id' => $stokSumber->id_karyawan,
                    'ke_karyawan_id' => $user->id,
                    'dari_tahapan' => $stokSumber->peran,
                    'ke_tahapan' => $user->role,
                    'qty_ajuan' => $qtyAjuan,
                    'status' => 'pending',
                    'catatan_pengaju' => $catatanPengaju,
                    'tgl_ajuan' => now(),
                ]);
            }
        });
    }

    public function approve(AjuanPengambilanProduksi $ajuan, User $user): void
    {
        DB::transaction(function () use ($ajuan, $user) {
            $ajuan = AjuanPengambilanProduksi::lockForUpdate()->findOrFail($ajuan->id);
            $this->ensureCanRespond($ajuan, $user);

            if ($ajuan->status !== 'pending') {
                abort(403);
            }

            $stokSumber = StokVirtual::where('id_detail_perintah', $ajuan->id_detail_perintah)
                ->where('id_karyawan', $ajuan->dari_karyawan_id)
                ->where('peran', $ajuan->dari_tahapan)
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $stokSumber->qty_hold < (int) $ajuan->qty_ajuan) {
                abort(422, 'Stok ready sumber tidak mencukupi.');
            }

            $stokTujuan = StokVirtual::firstOrNew([
                'id_detail_perintah' => $ajuan->id_detail_perintah,
                'id_karyawan' => $ajuan->ke_karyawan_id,
                'peran' => $ajuan->ke_tahapan,
            ]);

            if (! $stokTujuan->exists) {
                $stokTujuan->fill([
                    'id_perintah' => $ajuan->id_perintah,
                    'id_produk' => $ajuan->id_produk,
                    'qty_hold' => 0,
                    'total_selesai' => 0,
                    'total_reject' => 0,
                    'status_barang' => 'Proses',
                    'is_selesai' => false,
                ]);
            }

            $stokSumber->qty_hold = (int) $stokSumber->qty_hold - (int) $ajuan->qty_ajuan;
            $stokSumber->save();

            $stokTujuan->qty_hold = (int) $stokTujuan->qty_hold + (int) $ajuan->qty_ajuan;
            $stokTujuan->status_barang = 'Ready';
            $stokTujuan->save();

            $ajuan->update([
                'status' => 'disetujui',
                'tgl_respon' => now(),
            ]);

            MutasiProduksi::create([
                'id_ajuan' => $ajuan->id,
                'id_perintah' => $ajuan->id_perintah,
                'id_detail_perintah' => $ajuan->id_detail_perintah,
                'id_produk' => $ajuan->id_produk,
                'dari_karyawan_id' => $ajuan->dari_karyawan_id,
                'ke_karyawan_id' => $ajuan->ke_karyawan_id,
                'dari_tahapan' => $ajuan->dari_tahapan,
                'ke_tahapan' => $ajuan->ke_tahapan,
                'qty_pindah' => $ajuan->qty_ajuan,
                'tgl_transaksi' => now(),
            ]);
        });
    }

    public function reject(AjuanPengambilanProduksi $ajuan, User $user, ?string $catatan = null): void
    {
        DB::transaction(function () use ($ajuan, $user, $catatan) {
            $ajuan = AjuanPengambilanProduksi::lockForUpdate()->findOrFail($ajuan->id);
            $this->ensureCanRespond($ajuan, $user);

            if ($ajuan->status !== 'pending') {
                abort(403);
            }

            $ajuan->update([
                'status' => 'ditolak',
                'catatan_respon' => $catatan,
                'tgl_respon' => now(),
            ]);
        });
    }

    private function ensureCanRespond(AjuanPengambilanProduksi $ajuan, User $user): void
    {
        if ((int) $ajuan->dari_karyawan_id !== (int) $user->id || $ajuan->dari_tahapan !== $user->role) {
            abort(403);
        }
    }
}

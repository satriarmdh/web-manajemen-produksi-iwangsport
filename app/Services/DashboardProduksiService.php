<?php

namespace App\Services;

use App\Models\AjuanPengambilanProduksi;
use App\Models\PerintahProduksi;
use App\Models\StokVirtual;
use App\Models\User;

class DashboardProduksiService
{
    public function getStats(User $user): array
    {
        $role = $user->role;
        $data = [
            'role' => $role,
            'jumlahPerintahKerja' => 0,
            'jumlahAjuanMasuk' => 0,
            'jumlahBarangReady' => 0,
            'jumlahPekerjaanAktif' => 0,
            'selesaiHariIni' => 0,
        ];

        // 1. Ajuan Masuk pending yang harus direspon oleh karyawan ini (dari_karyawan_id = user->id)
        $data['jumlahAjuanMasuk'] = AjuanPengambilanProduksi::where('dari_karyawan_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // 2. Selesai Hari Ini (jumlah input stok virtual yang diselesaikan hari ini)
        $data['selesaiHariIni'] = StokVirtual::where('id_karyawan', $user->id)
            ->where('is_selesai', true)
            ->whereDate('updated_at', today())
            ->count();

        if ($role === 'potong') {
            // Perintah kerja baru/aktif yang menunggu dipotong oleh potong (status disetujui / dalam_produksi dan belum input potong sama sekali)
            $data['jumlahPerintahKerja'] = PerintahProduksi::whereIn('status_produksi', ['disetujui', 'dalam_produksi'])
                ->whereHas('details', fn($q) => $q->whereNull('qty_pcs_potong'))
                ->count();
        } else {
            // Pekerjaan aktif (di-hold oleh jahit / finishing) yang belum diselesaikan
            $data['jumlahPekerjaanAktif'] = StokVirtual::where('id_karyawan', $user->id)
                ->where('is_selesai', false)
                ->where('qty_hold', '>', 0)
                ->count();

            // Barang yang selesai di role sebelumnya dan siap diambil (source role)
            $sourceRole = $role === 'jahit' ? 'potong' : 'jahit';
            $data['jumlahBarangReady'] = StokVirtual::where('peran', $sourceRole)
                ->whereRaw('total_selesai - total_dikeluarkan > 0')
                ->count();
        }

        // ponytail: fallback keys for backward compatibility, even though we removed the cards
        $data['pekerjaanAktif'] = $data['jumlahPekerjaanAktif'] ?: $data['jumlahPerintahKerja'];
        $data['menungguInput'] = $data['pekerjaanAktif'];
        $data['ajuanMasuk'] = $data['jumlahAjuanMasuk'];

        return $data;
    }
}

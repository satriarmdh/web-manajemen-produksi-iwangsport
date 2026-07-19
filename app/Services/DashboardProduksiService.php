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
        $ajuanMasuk = $this->getAjuanMasuk($user);

        return $user->role === 'potong'
            ? array_merge($this->getPotongStats($user), ['ajuanMasuk' => $ajuanMasuk])
            : array_merge($this->getNonPotongStats($user), ['ajuanMasuk' => $ajuanMasuk]);
    }

    private function getAjuanMasuk(User $user): int
    {
        return AjuanPengambilanProduksi::where('dari_karyawan_id', $user->id)
            ->where('status', 'pending')
            ->distinct('id_perintah')
            ->count('id_perintah');
    }

    private function getPotongStats(User $user): array
    {
        $pekerjaanAktif = PerintahProduksi::whereIn('status_produksi', ['disetujui', 'dalam_produksi'])
            ->whereHas('details', fn($q) => $q->whereNull('qty_pcs_potong'))
            ->count();

        $selesaiHariIni = StokVirtual::where('id_karyawan', $user->id)
            ->where('peran', 'potong')
            ->where('is_selesai', true)
            ->whereDate('updated_at', today())
            ->count();

        return [
            'pekerjaanAktif' => $pekerjaanAktif,
            'menungguInput' => $pekerjaanAktif,
            'selesaiHariIni' => $selesaiHariIni,
        ];
    }

    private function getNonPotongStats(User $user): array
    {
        $pekerjaanAktif = StokVirtual::where('id_karyawan', $user->id)
            ->where('is_selesai', false)
            ->distinct('id_perintah')
            ->count('id_perintah');

        $menungguInput = StokVirtual::where('id_karyawan', $user->id)
            ->where('is_selesai', false)
            ->where('qty_hold', '>', 0)
            ->where('total_selesai', 0)
            ->distinct('id_perintah')
            ->count('id_perintah');

        $selesaiHariIni = StokVirtual::where('id_karyawan', $user->id)
            ->where('is_selesai', true)
            ->whereDate('updated_at', today())
            ->count();

        return [
            'pekerjaanAktif' => $pekerjaanAktif,
            'menungguInput' => $menungguInput,
            'selesaiHariIni' => $selesaiHariIni,
        ];
    }
}

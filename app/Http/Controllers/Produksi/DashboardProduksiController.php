<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Models\AjuanPengambilanProduksi;
use App\Models\StokVirtual;
use Illuminate\Http\Request;

class DashboardProduksiController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $role = $request->user()->role;

        if ($role === 'potong') {
            // Potong: no qty_hold flow. Active = WO with details missing qty_pcs_potong.
            $pekerjaanAktif = \App\Models\PerintahProduksi::whereIn('status_produksi', ['disetujui', 'dalam_produksi'])
                ->whereHas('details', fn($q) => $q->whereNull('qty_pcs_potong'))
                ->count();

            $menungguInput = $pekerjaanAktif;

            $selesaiHariIni = StokVirtual::where('id_karyawan', $userId)
                ->where('peran', 'potong')
                ->where('is_selesai', true)
                ->whereDate('updated_at', today())
                ->count();
        } else {
            // Jahit/Finishing: qty_hold means items received but not yet processed.
            $pekerjaanAktif = StokVirtual::where('id_karyawan', $userId)
                ->where('is_selesai', false)
                ->distinct('id_perintah')
                ->count('id_perintah');

            $menungguInput = StokVirtual::where('id_karyawan', $userId)
                ->where('is_selesai', false)
                ->where('qty_hold', '>', 0)
                ->where('total_selesai', 0)
                ->distinct('id_perintah')
                ->count('id_perintah');

            $selesaiHariIni = StokVirtual::where('id_karyawan', $userId)
                ->where('is_selesai', true)
                ->whereDate('updated_at', today())
                ->count();
        }

        $ajuanMasuk = AjuanPengambilanProduksi::where('dari_karyawan_id', $userId)
            ->where('status', 'pending')
            ->distinct('id_perintah')
            ->count('id_perintah');

        return view('produksi.dashboard', compact(
            'pekerjaanAktif',
            'menungguInput',
            'ajuanMasuk',
            'selesaiHariIni',
        ));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\Supplier;
use App\Models\User;
use App\Models\StokMasukBahanBaku;
use App\Models\StokKeluarBahanBaku;
use Illuminate\Http\Request;

class PergerakanStokController extends Controller
{
    /**
     * Display pergerakan stok bahan baku dengan tab
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'masuk');

        // ========== Query Stok Masuk ==========
        $queryMasuk = StokMasukBahanBaku::with(['bahanBaku', 'supplier', 'user'])->latest();

        // Search
        if ($request->filled('search_masuk')) {
            $search = $request->search_masuk;
            $queryMasuk->where(function ($q) use ($search) {
                $q->whereHas('bahanBaku', function ($q2) use ($search) {
                    $q2->where('nama_bahan', 'like', "%{$search}%")
                       ->orWhere('kode_bahan', 'like', "%{$search}%");
                })->orWhereHas('supplier', function ($q2) use ($search) {
                    $q2->where('nama_supplier', 'like', "%{$search}%");
                });
            });
        }

        // Filter kategori bahan baku
        if ($request->filled('kategori_masuk')) {
            $queryMasuk->whereHas('bahanBaku', function ($q) use ($request) {
                $q->where('kategori', $request->kategori_masuk);
            });
        }

        // Filter supplier
        if ($request->filled('supplier_masuk')) {
            $queryMasuk->where('supplier_id', $request->supplier_masuk);
        }

        // Filter tanggal
        if ($request->filled('tanggal_masuk')) {
            $queryMasuk->whereDate('created_at', $request->tanggal_masuk);
        }

        $stokMasuk = $queryMasuk->paginate(10, ['*'], 'page_masuk')->withQueryString();

        // ========== Query Stok Keluar ==========
        $queryKeluar = StokKeluarBahanBaku::with(['bahanBaku', 'user'])->latest();

        // Search
        if ($request->filled('search_keluar')) {
            $search = $request->search_keluar;
            $queryKeluar->where(function ($q) use ($search) {
                $q->whereHas('bahanBaku', function ($q2) use ($search) {
                    $q2->where('nama_bahan', 'like', "%{$search}%")
                       ->orWhere('kode_bahan', 'like', "%{$search}%");
                })->orWhere('penerima', 'like', "%{$search}%");
            });
        }

        // Filter kategori bahan baku
        if ($request->filled('kategori_keluar')) {
            $queryKeluar->whereHas('bahanBaku', function ($q) use ($request) {
                $q->where('kategori', $request->kategori_keluar);
            });
        }

        // Filter tanggal
        if ($request->filled('tanggal_keluar')) {
            $queryKeluar->whereDate('created_at', $request->tanggal_keluar);
        }

        $stokKeluar = $queryKeluar->paginate(10, ['*'], 'page_keluar')->withQueryString();

        // Data untuk form & filter dropdown
        $bahanBakuAll = BahanBaku::where('is_aktif', true)->orderBy('nama_bahan')->get();
        $bahanBakuNonKain = BahanBaku::where('is_aktif', true)
            ->where('kategori', '!=', 'kain')
            ->orderBy('nama_bahan')
            ->get();
        $suppliers = Supplier::where('is_aktif', true)->orderBy('nama_supplier')->get();
        $karyawan = User::whereNotIn('role', ['admin', 'owner'])->orderBy('name')->get();

        return view('admin.pergerakan-stok.index', compact(
            'tab',
            'stokMasuk',
            'stokKeluar',
            'bahanBakuAll',
            'bahanBakuNonKain',
            'suppliers',
            'karyawan'
        ));
    }
}

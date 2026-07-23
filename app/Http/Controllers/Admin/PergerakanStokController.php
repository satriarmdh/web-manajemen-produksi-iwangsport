<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PergerakanStokService;
use Illuminate\Http\Request;

class PergerakanStokController extends Controller
{
    protected PergerakanStokService $pergerakanStokService;

    public function __construct(PergerakanStokService $pergerakanStokService)
    {
        $this->pergerakanStokService = $pergerakanStokService;
    }

    /**
     * Display pergerakan stok bahan baku dengan tab
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'masuk');

        // Validasi dan redirect jika tanggal akhir kurang dari tanggal awal
        if ($tab === 'masuk') {
            if ($request->filled('tanggal_mulai_masuk') && $request->filled('tanggal_akhir_masuk')) {
                if (strtotime($request->tanggal_akhir_masuk) < strtotime($request->tanggal_mulai_masuk)) {
                    return redirect()->route('admin.pergerakan-stok.index', [
                        'tab' => 'masuk',
                        'search_masuk' => $request->search_masuk,
                        'kategori_masuk' => $request->kategori_masuk,
                        'supplier_masuk' => $request->supplier_masuk,
                        'tanggal_mulai_masuk' => $request->tanggal_mulai_masuk,
                    ])->with('error', 'Tanggal akhir tidak boleh kurang dari tanggal awal.');
                }
            }
        } else {
            if ($request->filled('tanggal_mulai_keluar') && $request->filled('tanggal_akhir_keluar')) {
                if (strtotime($request->tanggal_akhir_keluar) < strtotime($request->tanggal_mulai_keluar)) {
                    return redirect()->route('admin.pergerakan-stok.index', [
                        'tab' => 'keluar',
                        'search_keluar' => $request->search_keluar,
                        'kategori_keluar' => $request->kategori_keluar,
                        'tanggal_mulai_keluar' => $request->tanggal_mulai_keluar,
                    ])->with('error', 'Tanggal akhir tidak boleh kurang dari tanggal awal.');
                }
            }
        }

        // Get paginated data with filters from service
        $stokMasuk = $this->pergerakanStokService->getStokMasukPaginated([
            'search' => $request->search_masuk,
            'kategori' => $request->kategori_masuk,
            'supplier' => $request->supplier_masuk,
            'tanggal_mulai' => $request->tanggal_mulai_masuk,
            'tanggal_akhir' => $request->tanggal_akhir_masuk,
        ]);

        $stokKeluar = $this->pergerakanStokService->getStokKeluarPaginated([
            'search' => $request->search_keluar,
            'kategori' => $request->kategori_keluar,
            'tanggal_mulai' => $request->tanggal_mulai_keluar,
            'tanggal_akhir' => $request->tanggal_akhir_keluar,
        ]);

        // Get form data from service
        $formData = $this->pergerakanStokService->getFormData();

        return view('admin.pergerakan-stok.index', array_merge([
            'tab' => $tab,
            'stokMasuk' => $stokMasuk,
            'stokKeluar' => $stokKeluar,
        ], $formData));
    }
}

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

        // Get paginated data with filters from service
        $stokMasuk = $this->pergerakanStokService->getStokMasukPaginated([
            'search' => $request->search_masuk,
            'kategori' => $request->kategori_masuk,
            'supplier' => $request->supplier_masuk,
            'tanggal' => $request->tanggal_masuk,
        ]);

        $stokKeluar = $this->pergerakanStokService->getStokKeluarPaginated([
            'search' => $request->search_keluar,
            'kategori' => $request->kategori_keluar,
            'tanggal' => $request->tanggal_keluar,
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEstimasiProduksiRequest;
use App\Http\Requests\Admin\UpdateEstimasiProduksiRequest;
use App\Models\BahanBaku;
use App\Models\EstimasiProduksi;
use App\Models\Produk;
use App\Services\EstimasiProduksiService;
use Illuminate\Http\Request;

class EstimasiProduksiController extends Controller
{
    public function __construct(
        protected EstimasiProduksiService $estimasiService
    ) {}

    /**
     * Display a listing of estimasi produksi
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'sort']);
        $estimasi = $this->estimasiService->getEstimasi($filters);
        $produks = Produk::orderBy('nama_produk')->get();
        $bahanBaku = BahanBaku::where('kategori', 'kain')->orderBy('nama_bahan')->get();

        return view('admin.estimasi-produksi.index', compact('estimasi', 'produks', 'bahanBaku'));
    }

    /**
     * Store a newly created estimasi produksi
     */
    public function store(StoreEstimasiProduksiRequest $request)
    {
        $estimasi = $this->estimasiService->create($request->validated());

        return redirect()
            ->route('admin.estimasi-produksi.index')
            ->with('success', 'Estimasi produksi berhasil ditambahkan');
    }

    /**
     * Update the specified estimasi produksi
     */
    public function update(UpdateEstimasiProduksiRequest $request, EstimasiProduksi $estimasi_produksi)
    {
        $this->estimasiService->update($estimasi_produksi, $request->validated());

        return redirect()
            ->route('admin.estimasi-produksi.index')
            ->with('success', 'Estimasi produksi berhasil diperbarui.');
    }

    /**
     * Remove the specified estimasi produksi
     */
    public function destroy(EstimasiProduksi $estimasi_produksi)
    {
        $this->estimasiService->delete($estimasi_produksi);

        return redirect()
            ->route('admin.estimasi-produksi.index')
            ->with('success', 'Estimasi produksi berhasil dihapus.');
    }
}

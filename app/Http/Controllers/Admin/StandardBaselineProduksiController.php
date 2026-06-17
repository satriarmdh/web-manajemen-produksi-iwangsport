<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStandardBaselineProduksiRequest;
use App\Http\Requests\Admin\UpdateStandardBaselineProduksiRequest;
use App\Models\BahanBaku;
use App\Models\StandardBaselineProduksi;
use App\Models\Produk;
use App\Services\StandardBaselineProduksiService;
use Illuminate\Http\Request;

class StandardBaselineProduksiController extends Controller
{
    public function __construct(
        protected StandardBaselineProduksiService $estimasiService
    ) {}

    /**
     * Display a listing of standard baseline produksi
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'sort']);
        $estimasi = $this->estimasiService->getEstimasi($filters);
        $produks = Produk::orderBy('nama_produk')->get();
        $bahanBaku = BahanBaku::where('kategori', 'kain')->orderBy('nama_bahan')->get();

        return view('admin.standard-baseline-produksi.index', compact('estimasi', 'produks', 'bahanBaku'));
    }

    /**
     * Store a newly created standard baseline produksi
     */
    public function store(StoreStandardBaselineProduksiRequest $request)
    {
        $estimasi = $this->estimasiService->create($request->validated());

        return redirect()
            ->route('admin.standard-baseline-produksi.index')
            ->with('success', 'Standard baseline produksi berhasil ditambahkan');
    }

    /**
     * Update the specified standard baseline produksi
     */
    public function update(UpdateStandardBaselineProduksiRequest $request, StandardBaselineProduksi $standard_baseline_produksi)
    {
        $this->estimasiService->update($standard_baseline_produksi, $request->validated());

        return redirect()
            ->route('admin.standard-baseline-produksi.index')
            ->with('success', 'Standard baseline produksi berhasil diperbarui.');
    }

    /**
     * Remove the specified standard baseline produksi
     */
    public function destroy(StandardBaselineProduksi $standard_baseline_produksi)
    {
        $this->estimasiService->delete($standard_baseline_produksi);

        return redirect()
            ->route('admin.standard-baseline-produksi.index')
            ->with('success', 'Standard baseline produksi berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePerintahProduksiRequest;
use App\Http\Requests\Admin\UpdatePerintahProduksiRequest;
use App\Models\PerintahProduksi;
use App\Models\Produk;
use App\Models\BahanBaku;
use App\Models\StandardBaselineProduksi;
use App\Services\PerintahProduksiService;
use Illuminate\Http\Request;

class PerintahProduksiController extends Controller
{
    protected PerintahProduksiService $service;

    public function __construct(PerintahProduksiService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'sort']);
        $perintahProduksi = $this->service->getAllPaginated($filters, 10);

        return view('admin.perintah-produksi.index', compact('perintahProduksi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produks = Produk::where('is_aktif', true)->get();
        $bahanBakus = BahanBaku::where('is_aktif', true)
            ->where('kategori', 'kain')
            ->get();

        $previewNomorWO = $this->service->generateNomorWO();

        // Ambil standard baseline aktif dan format untuk lookup JS
        $baselines = StandardBaselineProduksi::where('is_aktif', true)
            ->with(['produk', 'bahanBaku'])
            ->get();

        return view('admin.perintah-produksi.create', compact('produks', 'bahanBakus', 'previewNomorWO', 'baselines'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePerintahProduksiRequest $request)
    {
        $perintahProduksi = $this->service->create($request->validated());

        return redirect()
            ->route('admin.perintah-produksi.index')
            ->with('success', 'Perintah produksi berhasil dibuat dengan nomor ' . $perintahProduksi->nomor_wo);
    }

    /**
     * Display the specified resource.
     */
    public function show(PerintahProduksi $perintahProduksi)
    {
        $perintahProduksi->load(['details.produk', 'details.bahanBaku', 'user', 'approver']);

        return view('admin.perintah-produksi.show', compact('perintahProduksi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PerintahProduksi $perintahProduksi)
    {
        if ($perintahProduksi->status_produksi !== 'pending') {
            abort(403, 'Perintah produksi hanya bisa diedit saat status masih pending');
        }

        $produks = Produk::where('is_aktif', true)->get();
        $bahanBakus = BahanBaku::where('is_aktif', true)
            ->where('kategori', 'kain')
            ->get();

        // Ambil standard baseline aktif dan format untuk lookup JS
        $baselines = StandardBaselineProduksi::where('is_aktif', true)
            ->with(['produk', 'bahanBaku'])
            ->get();

        return view('admin.perintah-produksi.edit', compact('perintahProduksi', 'produks', 'bahanBakus', 'baselines'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePerintahProduksiRequest $request, PerintahProduksi $perintahProduksi)
    {
        $this->service->update($perintahProduksi, $request->validated());

        return redirect()
            ->route('admin.perintah-produksi.index')
            ->with('success', 'Perintah produksi berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PerintahProduksi $perintahProduksi)
    {
        if ($perintahProduksi->status_produksi !== 'pending') {
            abort(403, 'Perintah produksi hanya bisa dihapus saat status masih pending');
        }

        $this->service->delete($perintahProduksi);

        return redirect()
            ->route('admin.perintah-produksi.index')
            ->with('success', 'Perintah produksi berhasil dihapus');
    }

    /**
     * Tandai perintah produksi selesai
     */
    public function selesai(Request $request, PerintahProduksi $perintahProduksi)
    {
        if ($perintahProduksi->status_produksi !== 'dalam_produksi') {
            abort(403, 'Perintah produksi hanya bisa diselesaikan jika status dalam produksi');
        }

        $request->validate([
            'tgl_selesai' => 'required|date',
        ]);

        $this->service->selesai($perintahProduksi, $request->tgl_selesai);

        return redirect()
            ->route('admin.perintah-produksi.index')
            ->with('success', 'Perintah produksi telah ditandai selesai');
    }

    /**
     * Cetak PDF perintah produksi
     */
    public function cetakPdf(PerintahProduksi $perintahProduksi)
    {
        $perintahProduksi->load(['details.produk', 'details.bahanBaku', 'user', 'approver']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.perintah-produksi.pdf', compact('perintahProduksi'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('perintah-produksi-' . $perintahProduksi->nomor_wo . '.pdf');
    }
}

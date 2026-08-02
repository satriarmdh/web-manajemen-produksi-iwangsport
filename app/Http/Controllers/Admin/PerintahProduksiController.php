<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SelesaikanPerintahProduksiRequest;
use App\Http\Requests\Admin\StorePerintahProduksiRequest;
use App\Http\Requests\Admin\UpdatePerintahProduksiRequest;
use App\Models\PerintahProduksi;
use App\Services\NotificationService;
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
        $filters = $request->only(['search', 'status', 'sort', 'tanggal_mulai']);
        $perintahProduksi = $this->service->getAllPaginated($filters, 10);

        return view('admin.perintah-produksi.index', compact('perintahProduksi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.perintah-produksi.create', $this->service->getFormData());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePerintahProduksiRequest $request)
    {
        try {
            $perintahProduksi = $this->service->create($request->validated());
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.perintah-produksi.create')
                ->withInput()
                ->with('error', $e->getMessage());
        }

        // Notifikasi -> owner
        app(NotificationService::class)->woBaru(
            $perintahProduksi->nomor_wo,
            auth()->user()->name
        );

        return redirect()
            ->route('admin.perintah-produksi.index')
            ->with('success', 'Perintah produksi berhasil dibuat dengan nomor ' . $perintahProduksi->nomor_wo);
    }

    /**
     * Display the specified resource.
     */
    public function show(PerintahProduksi $perintahProduksi)
    {
        $perintahProduksi = $this->service->loadForDetail($perintahProduksi);

        return view('admin.perintah-produksi.show', compact('perintahProduksi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PerintahProduksi $perintahProduksi)
    {
        try {
            $this->service->ensurePending($perintahProduksi);
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.perintah-produksi.index')
                ->with('error', 'Perintah produksi tidak bisa diedit karena status sudah berubah.');
        }

        return view('admin.perintah-produksi.edit', array_merge(
            ['perintahProduksi' => $perintahProduksi],
            $this->service->getFormData(false)
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePerintahProduksiRequest $request, PerintahProduksi $perintahProduksi)
    {
        try {
            $this->service->update($perintahProduksi, $request->validated());
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.perintah-produksi.edit', $perintahProduksi)
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.perintah-produksi.index')
            ->with('success', 'Perintah produksi berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PerintahProduksi $perintahProduksi)
    {
        try {
            if ($perintahProduksi->status_produksi !== 'pending') {
                throw new \Exception('Perintah produksi hanya bisa dihapus saat status masih pending');
            }
            $this->service->delete($perintahProduksi);
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.perintah-produksi.index')
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.perintah-produksi.index')
            ->with('success', 'Perintah produksi berhasil dihapus');
    }

    /**
     * Tandai perintah produksi selesai
     */
    public function selesai(SelesaikanPerintahProduksiRequest $request, PerintahProduksi $perintahProduksi)
    {
        try {
            $this->service->selesai($perintahProduksi, $request->validated('tgl_selesai'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.perintah-produksi.show', $perintahProduksi)
                ->with('error', $e->getMessage());
        }

        // Notifikasi -> admin + owner
        app(NotificationService::class)->woSelesai($perintahProduksi->nomor_wo);

        return redirect()
            ->route('admin.perintah-produksi.index')
            ->with('success', 'Perintah produksi telah ditandai selesai');
    }

    /**
     * Cetak PDF perintah produksi
     */
    public function cetakPdf(PerintahProduksi $perintahProduksi)
    {
        $perintahProduksi = $this->service->loadForDetail($perintahProduksi);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.perintah-produksi.pdf', compact('perintahProduksi'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('perintah-produksi-' . $perintahProduksi->nomor_wo . '.pdf');
    }
}

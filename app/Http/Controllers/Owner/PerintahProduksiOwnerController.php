<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\RejectPerintahProduksiRequest;
use App\Models\PerintahProduksi;
use App\Services\PerintahProduksiService;
use Illuminate\Http\Request;

class PerintahProduksiOwnerController extends Controller
{
    protected PerintahProduksiService $service;

    public function __construct(PerintahProduksiService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of pending perintah produksi for approval
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'sort']);
        $filters['status'] = 'pending'; // Owner hanya melihat yang pending

        $perintahProduksi = $this->service->getAllPaginated($filters, 10);

        return view('owner.perintah-produksi.index', compact('perintahProduksi'));
    }

    /**
     * Approve perintah produksi
     */
    public function approve(PerintahProduksi $perintahProduksi)
    {
        $this->service->approve($perintahProduksi);

        return redirect()
            ->route('owner.perintah-produksi.index')
            ->with('success', 'Perintah produksi ' . $perintahProduksi->nomor_wo . ' telah disetujui');
    }

    /**
     * Reject perintah produksi
     */
    public function reject(RejectPerintahProduksiRequest $request, PerintahProduksi $perintahProduksi)
    {
        $this->service->reject($perintahProduksi, $request->validated('alasan_penolakan'));

        return redirect()
            ->route('owner.perintah-produksi.index')
            ->with('success', 'Perintah produksi ' . $perintahProduksi->nomor_wo . ' telah ditolak');
    }
}

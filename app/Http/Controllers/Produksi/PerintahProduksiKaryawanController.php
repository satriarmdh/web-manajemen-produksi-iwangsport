<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Models\PerintahProduksi;
use App\Services\PerintahProduksiService;
use Illuminate\Http\Request;

class PerintahProduksiKaryawanController extends Controller
{
    public function __construct(
        private readonly PerintahProduksiService $service
    ) {}

    public function index(Request $request)
    {
        $role = $request->user()->role;
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();
        $filterTanggal = $request->string('tanggal')->toString();
        $sort = $request->string('sort', 'mulai_terlama')->toString();

        $perintahProduksi = $this->service->getForKaryawan([
            'search' => $search,
            'status' => $status,
            'tanggal' => $filterTanggal,
            'sort' => $sort,
        ]);

        return view('produksi.perintah-produksi.index', compact('perintahProduksi', 'role', 'search', 'status', 'filterTanggal', 'sort'));
    }

    public function show(Request $request, PerintahProduksi $perintahProduksi)
    {
        $this->service->ensureVisibleForKaryawan($perintahProduksi);

        $role = $request->user()->role;
        $perintahProduksi = $this->service->loadForDetail($perintahProduksi);

        return view('produksi.perintah-produksi.show', compact('perintahProduksi', 'role'));
    }
}

<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Produksi\ApproveAjuanPengambilanProduksiRequest;
use App\Http\Requests\Produksi\IndexAjuanPengambilanProduksiRequest;
use App\Http\Requests\Produksi\RespondAjuanPengambilanProduksiRequest;
use App\Http\Requests\Produksi\StoreAjuanPengambilanProduksiRequest;
use App\Models\AjuanPengambilanProduksi;
use App\Services\AjuanPengambilanProduksiService;
use Illuminate\Http\Request;

class AjuanPengambilanProduksiController extends Controller
{
    public function __construct(
        private readonly AjuanPengambilanProduksiService $service
    ) {}

    public function index(IndexAjuanPengambilanProduksiRequest $request)
    {
        return view(
            'produksi.ajuan-pengambilan.index',
            $this->service->getIndexData($request->user(), $request->filters())
        );
    }

    public function redirectLegacy()
    {
        return redirect()->route('produksi.ajuan-pengambilan.index');
    }

    public function masuk(Request $request)
    {
        return view('produksi.ajuan-pengambilan.masuk', [
            'ajuanMasuk' => $this->service->getIncoming($request->user()),
        ]);
    }

    public function store(StoreAjuanPengambilanProduksiRequest $request)
    {
        $this->service->storeMany(
            $request->ajuanItems(),
            $request->user(),
            $request->validated('catatan_pengaju')
        );

        return redirect()
            ->route('produksi.ajuan-pengambilan.index')
            ->with('success', 'Ajuan pengambilan barang berhasil dibuat.');
    }

    public function approve(
        ApproveAjuanPengambilanProduksiRequest $request,
        AjuanPengambilanProduksi $ajuan
    ) {
        $this->service->approve($ajuan, $request->user());

        return redirect()
            ->route('produksi.ajuan-pengambilan.masuk')
            ->with('success', 'Ajuan pengambilan barang disetujui.');
    }

    public function reject(
        RespondAjuanPengambilanProduksiRequest $request,
        AjuanPengambilanProduksi $ajuan
    ) {
        $this->service->reject(
            $ajuan,
            $request->user(),
            $request->validated('catatan_respon')
        );

        return redirect()
            ->route('produksi.ajuan-pengambilan.masuk')
            ->with('success', 'Ajuan pengambilan barang ditolak.');
    }
}
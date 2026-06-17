<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStokMasukRequest;
use App\Models\StokMasukBahanBaku;
use App\Services\PergerakanStokService;

class PemasukanBahanController extends Controller
{
    protected PergerakanStokService $pergerakanStokService;

    public function __construct(PergerakanStokService $pergerakanStokService)
    {
        $this->pergerakanStokService = $pergerakanStokService;
    }

    /**
     * Simpan transaksi stok masuk
     */
    public function store(StoreStokMasukRequest $request)
    {
        $this->pergerakanStokService->storeStokMasuk(
            $request->validated(),
            $request->file('bukti_pembelian')
        );

        return redirect()
            ->route('admin.pergerakan-stok.index', ['tab' => 'masuk'])
            ->with('success', 'Stok masuk berhasil ditambahkan!');
    }

    /**
     * Hapus transaksi stok masuk (soft delete)
     */
    public function destroy(StokMasukBahanBaku $pemasukanBahan)
    {
        $this->pergerakanStokService->destroyStokMasuk($pemasukanBahan);

        return redirect()
            ->route('admin.pergerakan-stok.index', ['tab' => 'masuk'])
            ->with('success', 'Transaksi stok masuk berhasil dihapus!');
    }
}

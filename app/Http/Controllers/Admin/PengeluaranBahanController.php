<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStokKeluarRequest;
use App\Models\StokKeluarBahanBaku;
use App\Services\PergerakanStokService;

class PengeluaranBahanController extends Controller
{
    protected PergerakanStokService $pergerakanStokService;

    public function __construct(PergerakanStokService $pergerakanStokService)
    {
        $this->pergerakanStokService = $pergerakanStokService;
    }

    /**
     * Simpan transaksi stok keluar
     */
    public function store(StoreStokKeluarRequest $request)
    {
        $this->pergerakanStokService->storeStokKeluar(
            $request->validated(),
            $request->file('bukti_pengeluaran')
        );

        return redirect()
            ->route('admin.pergerakan-stok.index', ['tab' => 'keluar'])
            ->with('success', 'Stok keluar berhasil dicatat!');
    }

    /**
     * Hapus transaksi stok keluar (soft delete)
     */
    public function destroy(StokKeluarBahanBaku $pengeluaranBahan)
    {
        $this->pergerakanStokService->destroyStokKeluar($pengeluaranBahan);

        return redirect()
            ->route('admin.pergerakan-stok.index', ['tab' => 'keluar'])
            ->with('success', 'Transaksi stok keluar berhasil dihapus!');
    }
}

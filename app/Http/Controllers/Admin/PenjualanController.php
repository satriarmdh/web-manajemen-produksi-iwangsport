<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePenjualanRequest;
use App\Http\Requests\Admin\UpdatePenjualanRequest;
use App\Models\Penjualan;
use App\Services\PenjualanService;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    public function __construct(
        private readonly PenjualanService $service
    ) {}

    /**
     * Display list of penjualan.
     */
    public function index(Request $request)
    {
        $penjualan = $this->service->getPenjualanPaginated(
            $request->only(['search', 'tanggal_mulai', 'tanggal_akhir']),
            10
        );

        return view('admin.penjualan.index', compact('penjualan'));
    }

    /**
     * Show form to create new penjualan.
     */
    public function create()
    {
        $data = $this->service->getCreateData();

        return view('admin.penjualan.create', $data);
    }

    /**
     * Store new penjualan.
     */
    public function store(StorePenjualanRequest $request)
    {
        try {
            $penjualan = $this->service->create(
                $request->validated(),
                $request->user()
            );

            return redirect()
                ->route('admin.penjualan.index')
                ->with('success', "Transaksi penjualan {$penjualan->nomor_invoice} berhasil dicatat. Total: Rp " . number_format($penjualan->total_harga, 0, ',', '.'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mencatat penjualan: ' . $e->getMessage());
        }
    }

    /**
     * Show penjualan detail.
     */
    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['pelanggan', 'user', 'detailPenjualan.produk']);

        return view('admin.penjualan.show', compact('penjualan'));
    }

    /**
     * Show form to edit penjualan.
     */
    public function edit(Penjualan $penjualan)
    {
        $data = $this->service->getEditData($penjualan);

        return view('admin.penjualan.edit', $data);
    }

    /**
     * Update penjualan.
     */
    public function update(UpdatePenjualanRequest $request, Penjualan $penjualan)
    {
        try {
            $penjualan = $this->service->update(
                $penjualan,
                $request->validated(),
                $request->user()
            );

            return redirect()
                ->route('admin.penjualan.index')
                ->with('success', "Transaksi penjualan {$penjualan->nomor_invoice} berhasil diperbarui. Total: Rp " . number_format($penjualan->total_harga, 0, ',', '.'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui penjualan: ' . $e->getMessage());
        }
    }

    /**
     * Delete penjualan (soft delete, return stock).
     */
    public function destroy(Penjualan $penjualan)
    {
        try {
            $this->service->delete($penjualan, auth()->user());

            return redirect()
                ->route('admin.penjualan.index')
                ->with('success', "Transaksi penjualan {$penjualan->nomor_invoice} berhasil dihapus. Stok produk telah dikembalikan.");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus penjualan: ' . $e->getMessage());
        }
    }
}

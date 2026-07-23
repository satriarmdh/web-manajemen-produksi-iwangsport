<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePenjualanRequest;
use App\Http\Requests\Admin\UpdatePenjualanRequest;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Produk;
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
        $query = Penjualan::with(['pelanggan', 'user'])
            ->latest('tanggal');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nomor_invoice', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function ($q) use ($search) {
                      $q->where('nama_pelanggan', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->input('tanggal_mulai'));
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal', '<=', $request->input('tanggal_akhir'));
        }

        $penjualan = $query->paginate(10)->withQueryString();

        return view('admin.penjualan.index', compact('penjualan'));
    }

    /**
     * Show form to create new penjualan.
     */
    public function create()
    {
        $pelanggan = Pelanggan::where('is_aktif', true)->orderBy('nama_pelanggan')->get();
        $produk = Produk::where('is_aktif', true)->where('stok', '>', 0)->orderBy('nama_produk')->get();

        return view('admin.penjualan.create', compact('pelanggan', 'produk'));
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
        $penjualan->load(['pelanggan', 'detailPenjualan.produk']);
        $pelanggan = Pelanggan::where('is_aktif', true)->orderBy('nama_pelanggan')->get();
        $produk = Produk::where('is_aktif', true)->orderBy('nama_produk')->get();

        return view('admin.penjualan.edit', compact('penjualan', 'pelanggan', 'produk'));
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

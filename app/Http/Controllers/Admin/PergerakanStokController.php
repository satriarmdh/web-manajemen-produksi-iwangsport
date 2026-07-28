<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePergerakanStokRequest;
use App\Models\PergerakanStokBahanBaku;
use App\Services\PergerakanStokService;
use Illuminate\Http\Request;

class PergerakanStokController extends Controller
{
    protected PergerakanStokService $pergerakanStokService;

    public function __construct(PergerakanStokService $pergerakanStokService)
    {
        $this->pergerakanStokService = $pergerakanStokService;
    }

    /**
     * Display pergerakan stok bahan baku dengan tab
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'masuk');

        // Validasi dan redirect jika tanggal akhir kurang dari tanggal awal
        if ($tab === 'masuk') {
            if ($request->filled('tanggal_mulai_masuk') && $request->filled('tanggal_akhir_masuk')) {
                if (strtotime($request->tanggal_akhir_masuk) < strtotime($request->tanggal_mulai_masuk)) {
                    return redirect()->route('admin.pergerakan-stok.index', [
                        'tab' => 'masuk',
                        'search_masuk' => $request->search_masuk,
                        'kategori_masuk' => $request->kategori_masuk,
                        'tanggal_mulai_masuk' => $request->tanggal_mulai_masuk,
                    ])->with('error', 'Tanggal akhir tidak boleh kurang dari tanggal awal.');
                }
            }
        } else {
            if ($request->filled('tanggal_mulai_keluar') && $request->filled('tanggal_akhir_keluar')) {
                if (strtotime($request->tanggal_akhir_keluar) < strtotime($request->tanggal_mulai_keluar)) {
                    return redirect()->route('admin.pergerakan-stok.index', [
                        'tab' => 'keluar',
                        'search_keluar' => $request->search_keluar,
                        'kategori_keluar' => $request->kategori_keluar,
                        'tanggal_mulai_keluar' => $request->tanggal_mulai_keluar,
                    ])->with('error', 'Tanggal akhir tidak boleh kurang dari tanggal awal.');
                }
            }
        }

        // Get paginated data with filters from service
        $stokMasuk = $this->pergerakanStokService->getStokMasukPaginated([
            'search' => $request->search_masuk,
            'kategori' => $request->kategori_masuk,
            'tanggal_mulai' => $request->tanggal_mulai_masuk,
            'tanggal_akhir' => $request->tanggal_akhir_masuk,
        ]);

        $stokKeluar = $this->pergerakanStokService->getStokKeluarPaginated([
            'search' => $request->search_keluar,
            'kategori' => $request->kategori_keluar,
            'tanggal_mulai' => $request->tanggal_mulai_keluar,
            'tanggal_akhir' => $request->tanggal_akhir_keluar,
        ]);

        return view('admin.pergerakan-stok.index', compact('tab', 'stokMasuk', 'stokKeluar'));
    }

    /**
     * Show form to create new pergerakan stok
     */
    public function create(Request $request)
    {
        $tab = $request->get('tab', 'masuk');
        $formData = $this->pergerakanStokService->getFormData();

        return view('admin.pergerakan-stok.create', array_merge([
            'tab' => $tab,
        ], $formData));
    }

    /**
     * Store new pergerakan stok bulk
     */
    public function store(StorePergerakanStokRequest $request)
    {
        try {
            $transaksi = $this->pergerakanStokService->store(
                $request->validated(),
                $request->file('bukti')
            );

            $tabName = $transaksi->jenis_pergerakan;

            return redirect()
                ->route('admin.pergerakan-stok.index', ['tab' => $tabName])
                ->with('success', "Transaksi pergerakan stok {$transaksi->nomor_transaksi} berhasil dicatat.");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mencatat pergerakan stok: ' . $e->getMessage());
        }
    }

    /**
     * Show detail pergerakan stok
     */
    public function show(PergerakanStokBahanBaku $pergerakanStok)
    {
        $pergerakanStok->load(['detailPergerakanStok.bahanBaku', 'supplier', 'user']);

        return view('admin.pergerakan-stok.show', compact('pergerakanStok'));
    }

    /**
     * Delete/Cancel pergerakan stok
     */
    public function destroy(PergerakanStokBahanBaku $pergerakanStok)
    {
        try {
            $jenis = $pergerakanStok->jenis_pergerakan;
            $nomor = $pergerakanStok->nomor_transaksi;

            $this->pergerakanStokService->destroy($pergerakanStok);

            return redirect()
                ->route('admin.pergerakan-stok.index', ['tab' => $jenis])
                ->with('success', "Transaksi pergerakan stok {$nomor} berhasil dibatalkan. Stok bahan baku telah dikembalikan.");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal membatalkan pergerakan stok: ' . $e->getMessage());
        }
    }
}

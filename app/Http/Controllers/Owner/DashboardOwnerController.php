<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\SalesTrendRequest;
use App\Services\DashboardOwnerService;
use Illuminate\Http\Request;

class DashboardOwnerController extends Controller
{
    public function __construct(
        private readonly DashboardOwnerService $dashboardService
    ) {}

    /**
     * Menampilkan dashboard utama owner (Executive Summary).
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $summary = $this->dashboardService->getDashboardSummary();
        $stats = $summary['stats'];
        $topProduk = $summary['topProduk'];

        // Data tren penjualan awal: 30 hari terakhir.
        $end = now();
        $start = now()->subDays(29);
        $salesTrend = $this->dashboardService->getSalesTrend($start, $end);

        $recentActivity = $this->dashboardService->getRecentActivity(5);

        return view('owner.dashboard', compact('stats', 'salesTrend', 'topProduk', 'recentActivity'));
    }

    /**
     * Endpoint AJAX: mengembalikan data tren penjualan sesuai filter periode.
     * Query params:
     *  - range: '7d' | '30d' | '1y' (preset), atau
     *  - start & end (format Y-m-d) untuk custom range.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function salesTrend(SalesTrendRequest $request)
    {
        $validated = $request->validated();

        if (!empty($validated['start']) && !empty($validated['end'])) {
            $start = \Carbon\Carbon::parse($validated['start']);
            $end = \Carbon\Carbon::parse($validated['end']);
        } else {
            $end = now();
            $start = match ($validated['range'] ?? '30d') {
                '7d' => now()->subDays(6),
                '1y' => now()->subYear()->addDay(),
                default => now()->subDays(29),
            };
        }

        return response()->json($this->dashboardService->getSalesTrend($start, $end));
    }

    /**
     * Menampilkan laporan inventori khusus (stok detail & mutasi).
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function inventori(Request $request)
    {
        $filters = $request->only(['stok']);
        $stats = $this->dashboardService->getInventoryStats();
        
        $mutasiFilters = $request->only(['jenis_item', 'jenis_pergerakan']);
        $mutasiStok = $this->dashboardService->getMutasiStokPaginated($mutasiFilters, 10);
        
        $bahanBaku = $this->dashboardService->getBahanBakuStockList($filters);
        $produk = $this->dashboardService->getProdukStockList($filters);

        return view('owner.inventori', compact('stats', 'mutasiStok', 'bahanBaku', 'produk'));
    }
}


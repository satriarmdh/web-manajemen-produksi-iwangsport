<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\Pelanggan;
use App\Models\PerintahProduksi;
use App\Models\PergerakanStokBahanBaku;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class DashboardAdminController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard admin
     */
    public function index()
    {
        // 1. Hitung statistik
        $activeWoCount = PerintahProduksi::whereIn('status_produksi', ['disetujui', 'dalam_produksi'])->count();
        
        $lowBahanCount = BahanBaku::where('stok', '<', 10)->count();
        $lowProdukCount = Produk::where('stok', '<', 100)->count();
        $lowStockCount = $lowBahanCount + $lowProdukCount;

        $partnersCount = Supplier::count() + Pelanggan::count();

        $todayStok = PergerakanStokBahanBaku::whereDate('tanggal', today())->count();
        $todaySales = Penjualan::whereDate('tanggal', today())->count();
        $todayTransactionsCount = $todayStok + $todaySales;

        $stats = [
            'active_wo' => $activeWoCount,
            'low_stock' => $lowStockCount,
            'partners' => $partnersCount,
            'today_transactions' => $todayTransactionsCount
        ];

        // 2. Transaksi/Aktivitas Terbaru
        $recentWo = PerintahProduksi::with('user')
            ->latest('id')
            ->take(5)
            ->get();

        $recentSales = Penjualan::with('pelanggan')
            ->latest('id')
            ->take(5)
            ->get();

        $recentStockMovements = PergerakanStokBahanBaku::with(['supplier', 'detailPergerakanStok'])
            ->latest('id')
            ->take(5)
            ->get();

        // 3. Alerts untuk barang/bahan hampir habis
        $lowBahanList = BahanBaku::where('stok', '<', 10)
            ->select('nama_bahan as nama', 'stok', 'satuan')
            ->selectRaw("'Bahan Baku' as tipe")
            ->orderBy('stok', 'asc')
            ->take(3)
            ->get();

        $lowProdukList = Produk::where('stok', '<', 100)
            ->select('nama_produk as nama', 'stok')
            ->selectRaw("'Pcs' as satuan, 'Produk' as tipe")
            ->orderBy('stok', 'asc')
            ->take(3)
            ->get();

        $lowStockAlerts = $lowBahanList->concat($lowProdukList)->sortBy('stok')->take(5);

        return view('admin.dashboard', compact('stats', 'recentWo', 'recentSales', 'recentStockMovements', 'lowStockAlerts'));
    }
}

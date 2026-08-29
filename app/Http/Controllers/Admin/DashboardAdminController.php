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
        
        // Item bahan baku & produk yang menipis (0 < stok < min_stok) atau habis (stok = 0)
        $lowBahanCount = BahanBaku::where(function ($q) {
            $q->where('stok', 0)
              ->orWhere(function ($sq) {
                  $sq->where('stok', '>', 0)->where('stok_minimal', '>', 0)->whereColumn('stok', '<', 'stok_minimal');
              });
        })->count();

        $lowProdukCount = Produk::where(function ($q) {
            $q->where('stok', 0)
              ->orWhere(function ($sq) {
                  $sq->where('stok', '>', 0)->where('stok_minimal', '>', 0)->whereColumn('stok', '<', 'stok_minimal');
              });
        })->count();

        $stockoutBahanCount = BahanBaku::where('stok', 0)->count();
        $stockoutProdukCount = Produk::where('stok', 0)->count();
        $stockoutTotal = $stockoutBahanCount + $stockoutProdukCount;

        $partnersCount = Supplier::count() + Pelanggan::count();

        $todayStok = PergerakanStokBahanBaku::whereDate('tanggal', today())->count();
        $todaySales = Penjualan::whereDate('tanggal', today())->count();
        $todayTransactionsCount = $todayStok + $todaySales;

        $stats = [
            'active_wo' => $activeWoCount,
            'low_stock' => $lowBahanCount + $lowProdukCount,
            'low_bahan' => $lowBahanCount,
            'low_produk' => $lowProdukCount,
            'stockout_bahan_count' => $stockoutBahanCount,
            'stockout_produk_count' => $stockoutProdukCount,
            'stockout_total' => $stockoutTotal,
            'partners' => $partnersCount,
            'today_transactions' => $todayTransactionsCount
        ];

        // 2. Transaksi/Aktivitas Terbaru
        $recentWo = PerintahProduksi::with('user')
            ->latest('id')
            ->take(5)
            ->get();

        $recentSales = Penjualan::with(['pelanggan', 'pembayaranPenjualan'])
            ->latest('id')
            ->take(5)
            ->get();

        $recentStockMovements = PergerakanStokBahanBaku::with(['supplier', 'detailPergerakanStok'])
            ->latest('id')
            ->take(5)
            ->get();

        // 3. Alerts untuk barang/bahan hampir habis (0 < stok < min_stok)
        $lowBahanList = BahanBaku::where('stok', '>', 0)
            ->where('stok_minimal', '>', 0)
            ->whereColumn('stok', '<', 'stok_minimal')
            ->select('nama_bahan as nama', 'warna', 'stok', 'stok_minimal', 'satuan')
            ->selectRaw("'Bahan Baku' as tipe")
            ->orderBy('stok', 'asc')
            ->take(5)
            ->get();

        $lowProdukList = Produk::where('stok', '>', 0)
            ->where('stok_minimal', '>', 0)
            ->whereColumn('stok', '<', 'stok_minimal')
            ->select('nama_produk as nama', 'warna', 'stok', 'stok_minimal')
            ->selectRaw("'Pcs' as satuan, 'Produk' as tipe")
            ->orderBy('stok', 'asc')
            ->take(5)
            ->get();

        $lowStockAlerts = $lowBahanList->concat($lowProdukList)->sortBy('stok')->take(5);

        // 4. Alerts untuk barang/bahan yang stoknya sudah habis (= 0)
        $stockoutBahanList = BahanBaku::where('stok', 0)
            ->select('nama_bahan as nama', 'warna', 'stok', 'stok_minimal', 'satuan')
            ->selectRaw("'Bahan Baku' as tipe")
            ->orderBy('nama_bahan', 'asc')
            ->take(5)
            ->get();

        $stockoutProdukList = Produk::where('stok', 0)
            ->select('nama_produk as nama', 'warna', 'stok', 'stok_minimal')
            ->selectRaw("'Pcs' as satuan, 'Produk' as tipe")
            ->orderBy('nama_produk', 'asc')
            ->take(5)
            ->get();

        $stockoutAlerts = $stockoutBahanList->concat($stockoutProdukList)->take(5);

        return view('admin.dashboard', compact('stats', 'recentWo', 'recentSales', 'recentStockMovements', 'lowStockAlerts', 'stockoutAlerts'));
    }
}

<?php

namespace App\Services;

use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\RiwayatStok;
use App\Models\PerintahProduksi;
use App\Models\User;
use App\Models\DetailPenjualan;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class DashboardOwnerService
{
    /**
     * Mengambil summary bisnis untuk Dashboard Utama.
     *
     * @return array
     */
    public function getDashboardSummary(): array
    {
        // Produk terlaris 30 hari terakhir (bukan akumulasi sepanjang masa,
        // agar mencerminkan tren penjualan yang sedang berjalan).
        $penjualanTable = (new \App\Models\Penjualan)->getTable();
        $detailTable = (new DetailPenjualan)->getTable();

        $topProduk = DetailPenjualan::query()
            ->join($penjualanTable, "{$detailTable}.penjualan_id", '=', "{$penjualanTable}.id")
            ->where("{$penjualanTable}.tanggal", '>=', now()->subDays(29)->toDateString())
            ->select("{$detailTable}.produk_id", DB::raw("SUM({$detailTable}.qty) as total_qty"))
            ->groupBy("{$detailTable}.produk_id")
            ->orderByDesc('total_qty')
            ->limit(5)
            ->with('produk')
            ->get();

        // "Menipis" = masih ada stok namun di bawah ambang batas.
        // Barang dengan stok 0 masuk kategori "habis", bukan "menipis",
        // agar konsisten dengan getInventoryStats() di halaman inventori.
        $bahanMenipis = (int) BahanBaku::where('stok', '>', 0)->where('stok_minimal', '>', 0)->whereColumn('stok', '<', 'stok_minimal')->count();
        $produkMenipis = (int) Produk::where('stok', '>', 0)->where('stok_minimal', '>', 0)->whereColumn('stok', '<', 'stok_minimal')->count();
        $bahanHabis = (int) BahanBaku::where('stok', 0)->count();
        $produkHabis = (int) Produk::where('stok', 0)->count();

        return [
            'stats' => [
                'wo_pending_count' => PerintahProduksi::where('status_produksi', 'pending')->count(),
                'total_staff_count' => User::whereIn('role', ['admin', 'potong', 'jahit', 'finishing'])->count(),
                'total_bahan_count' => (int) BahanBaku::count(),
                'total_produk_count' => (int) Produk::count(),
                'bahan_menipis_count' => $bahanMenipis,
                'produk_menipis_count' => $produkMenipis,
                'bahan_habis_count' => $bahanHabis,
                'produk_habis_count' => $produkHabis,
            ],
            'topProduk' => $topProduk,
        ];
    }

    /**
     * Mengambil aktivitas terbaru lintas modul untuk ditampilkan di dashboard.
     * Menggabungkan perintah produksi terakhir dan mutasi stok terakhir.
     *
     * @param int $limit
     * @return array{perintahProduksi: Collection, mutasiStok: Collection}
     */
    public function getRecentActivity(int $limit = 5): array
    {
        return [
            'perintahProduksi' => PerintahProduksi::with(['user', 'details.produk'])
                ->latest('created_at')
                ->limit($limit)
                ->get(),
            'mutasiStok' => RiwayatStok::with(['item', 'user'])
                ->latest('created_at')
                ->limit($limit)
                ->get(),
        ];
    }

    /**
     * Mengambil statistik inventori real-time.
     *
     * @return array
     */
    public function getInventoryStats(): array
    {
        return [
            'bahan_menipis_count' => (int) BahanBaku::where('stok', '>', 0)->where('stok_minimal', '>', 0)->whereColumn('stok', '<', 'stok_minimal')->count(),
            'produk_menipis_count' => (int) Produk::where('stok', '>', 0)->where('stok_minimal', '>', 0)->whereColumn('stok', '<', 'stok_minimal')->count(),
            'bahan_habis_count' => (int) BahanBaku::where('stok', 0)->count(),
            'produk_habis_count' => (int) Produk::where('stok', 0)->count(),
        ];
    }

    /**
     * Mengambil list bahan baku beserta filter stok.
     *
     * @param array $filters
     * @return Collection
     */
    public function getBahanBakuStockList(array $filters): Collection
    {
        $query = BahanBaku::query();

        if (!empty($filters['stok']) && $filters['stok'] === 'menipis') {
            $query->where('stok', '>', 0)->where('stok_minimal', '>', 0)->whereColumn('stok', '<', 'stok_minimal');
        }

        return $query->orderBy('stok', 'asc')->get();
    }

    /**
     * Mengambil list produk beserta filter stok.
     *
     * @param array $filters
     * @return Collection
     */
    public function getProdukStockList(array $filters): Collection
    {
        $query = Produk::query();

        if (!empty($filters['stok']) && $filters['stok'] === 'menipis') {
            $query->where('stok', '>', 0)->where('stok_minimal', '>', 0)->whereColumn('stok', '<', 'stok_minimal');
        }

        return $query->orderBy('stok', 'asc')->get();
    }

    /**
     * Mengambil riwayat mutasi stok paginated.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getMutasiStokPaginated(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = RiwayatStok::with(['item', 'user']);

        if (!empty($filters['jenis_item']) && $filters['jenis_item'] !== 'semua') {
            $query->where('jenis_item', $filters['jenis_item']);
        }

        if (!empty($filters['jenis_pergerakan']) && $filters['jenis_pergerakan'] !== 'semua') {
            $query->where('jenis_pergerakan', $filters['jenis_pergerakan']);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Mengambil tren penjualan (total qty terjual) yang diagregasi per periode,
     * lengkap dengan pembanding terhadap periode sebelumnya.
     * Rentang <= 92 hari dikelompokkan harian, selebihnya dikelompokkan bulanan.
     *
     * @param \Carbon\Carbon $start
     * @param \Carbon\Carbon $end
     * @return array{labels: array, values: array, total: int, granularity: string, previous_total: int, change_percent: float|null, period_label: string}
     */
    public function getSalesTrend(\Carbon\Carbon $start, \Carbon\Carbon $end): array
    {
        $start = $start->copy()->startOfDay();
        $end = $end->copy()->endOfDay();
        $daysDiff = $start->diffInDays($end);
        $granularity = $daysDiff > 92 ? 'month' : 'day';

        $driver = DB::connection()->getDriverName();

        $penjualanTable = (new \App\Models\Penjualan)->getTable();
        $detailTable = (new DetailPenjualan)->getTable();
        $tanggalCol = "{$penjualanTable}.tanggal";

        if ($granularity === 'month') {
            $groupExpr = $driver === 'sqlite'
                ? "strftime('%Y-%m', {$tanggalCol})"
                : "DATE_FORMAT({$tanggalCol}, '%Y-%m')";
        } else {
            $groupExpr = $driver === 'sqlite'
                ? "strftime('%Y-%m-%d', {$tanggalCol})"
                : "DATE_FORMAT({$tanggalCol}, '%Y-%m-%d')";
        }

        $rows = DetailPenjualan::query()
            ->join($penjualanTable, "{$detailTable}.penjualan_id", '=', "{$penjualanTable}.id")
            ->whereBetween($tanggalCol, [$start->toDateString(), $end->toDateString()])
            ->selectRaw("{$groupExpr} as bucket, SUM({$detailTable}.qty) as total_qty")
            ->groupBy('bucket')
            ->orderBy('bucket', 'asc')
            ->pluck('total_qty', 'bucket');

        // Susun label kontinu agar bar tetap muncul walau 0.
        $labels = [];
        $values = [];
        $cursor = $start->copy();

        if ($granularity === 'month') {
            $cursor = $cursor->startOfMonth();
            $endMonth = $end->copy()->startOfMonth();
            while ($cursor->lte($endMonth)) {
                $key = $cursor->format('Y-m');
                $labels[] = $cursor->translatedFormat('M Y');
                $values[] = (int) ($rows[$key] ?? 0);
                $cursor->addMonth();
            }
        } else {
            while ($cursor->lte($end)) {
                $key = $cursor->format('Y-m-d');
                $labels[] = $cursor->translatedFormat('d M');
                $values[] = (int) ($rows[$key] ?? 0);
                $cursor->addDay();
            }
        }

        $total = (int) array_sum($values);

        // Periode pembanding: rentang dengan panjang sama, tepat sebelum periode berjalan.
        $prevEnd = $start->copy()->subDay()->endOfDay();
        $prevStart = $prevEnd->copy()->subDays($daysDiff)->startOfDay();
        $previousTotal = $this->sumSalesQty($prevStart, $prevEnd);

        // Bila periode lalu nol, persentase tidak bermakna (pembagian nol).
        $changePercent = $previousTotal > 0
            ? round((($total - $previousTotal) / $previousTotal) * 100, 1)
            : null;

        return [
            'labels' => $labels,
            'values' => $values,
            'total' => $total,
            'granularity' => $granularity,
            'previous_total' => $previousTotal,
            'change_percent' => $changePercent,
            'period_label' => $start->translatedFormat('d M Y') . ' — ' . $end->translatedFormat('d M Y'),
        ];
    }

    /**
     * Menjumlahkan total qty terjual pada rentang tanggal tertentu.
     * Dipakai untuk menghitung total periode pembanding tanpa menyusun bucket.
     *
     * @param \Carbon\Carbon $start
     * @param \Carbon\Carbon $end
     * @return int
     */
    private function sumSalesQty(\Carbon\Carbon $start, \Carbon\Carbon $end): int
    {
        $penjualanTable = (new \App\Models\Penjualan)->getTable();
        $detailTable = (new DetailPenjualan)->getTable();

        return (int) DetailPenjualan::query()
            ->join($penjualanTable, "{$detailTable}.penjualan_id", '=', "{$penjualanTable}.id")
            ->whereBetween("{$penjualanTable}.tanggal", [$start->toDateString(), $end->toDateString()])
            ->sum("{$detailTable}.qty");
    }
}

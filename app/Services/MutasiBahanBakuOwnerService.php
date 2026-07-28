<?php

namespace App\Services;

use App\Models\PergerakanStokBahanBaku;
use App\Models\PerintahProduksi;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class MutasiBahanBakuOwnerService
{
    /**
     * Get paginated stok keluar (both kain and non-kain) for Owner.
     */
    public function getStokKeluarPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        // 1. Query untuk pergerakan non-kain (keluar)
        $queryNonKain = DB::table('pergerakan_stok_bahan_baku as p')
            ->select([
                DB::raw("'non-kain' as tipe_mutasi"),
                'p.id',
                'p.nomor_transaksi as nomor',
                'p.tanggal',
                'p.penerima as detail_tujuan',
                'u.name as creator',
                'p.created_at'
            ])
            ->join('users as u', 'u.id', '=', 'p.user_id')
            ->where('p.jenis_pergerakan', 'keluar')
            ->whereNull('p.deleted_at');

        // 2. Query untuk pergerakan kain (perintah produksi disetujui)
        $queryKain = DB::table('perintah_produksi as pp')
            ->select([
                DB::raw("'kain' as tipe_mutasi"),
                'pp.id',
                'pp.nomor_wo as nomor',
                'pp.approved_at as tanggal',
                DB::raw("'Keperluan Produksi (WO)' as detail_tujuan"),
                'u.name as creator',
                'pp.created_at'
            ])
            ->join('users as u', 'u.id', '=', 'pp.user_id')
            ->where('pp.status_produksi', 'disetujui')
            ->whereNull('pp.deleted_at');

        // Apply search filter if any
        if (!empty($filters['search_keluar'])) {
            $search = $filters['search_keluar'];
            $queryNonKain->where(function ($q) use ($search) {
                $q->where('p.nomor_transaksi', 'like', "%{$search}%")
                  ->orWhere('p.penerima', 'like', "%{$search}%");
            });
            $queryKain->where(function ($q) use ($search) {
                $q->where('pp.nomor_wo', 'like', "%{$search}%");
            });
        }

        // Apply date filters if any
        if (!empty($filters['tanggal_mulai_keluar'])) {
            $queryNonKain->whereDate('p.tanggal', '>=', $filters['tanggal_mulai_keluar']);
            $queryKain->whereDate('pp.approved_at', '>=', $filters['tanggal_mulai_keluar']);
        }
        if (!empty($filters['tanggal_akhir_keluar'])) {
            $queryNonKain->whereDate('p.tanggal', '<=', $filters['tanggal_akhir_keluar']);
            $queryKain->whereDate('pp.approved_at', '<=', $filters['tanggal_akhir_keluar']);
        }

        // Apply category filter
        if (!empty($filters['kategori_keluar'])) {
            $kategori = $filters['kategori_keluar'];
            
            $queryNonKain->whereExists(function ($q) use ($kategori) {
                $q->select(DB::raw(1))
                  ->from('detail_pergerakan_stok_bahan_baku as d')
                  ->join('bahan_baku as b', 'b.id', '=', 'd.bahan_baku_id')
                  ->whereColumn('d.pergerakan_stok_bahan_baku_id', 'p.id')
                  ->where('b.kategori', $kategori);
            });

            if ($kategori !== 'kain') {
                $queryKain->whereRaw('1 = 0');
            } else {
                $queryKain->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                      ->from('riwayat_penggunaan_kain as r')
                      ->join('bahan_baku as b', 'b.id', '=', 'r.bahan_baku_id')
                      ->whereColumn('r.perintah_produksi_id', 'pp.id')
                      ->where('b.kategori', 'kain');
                });
            }
        }

        // Combine using union
        $combined = $queryNonKain->unionAll($queryKain);

        // Fetch sorted results
        $bindings = $combined->getBindings();
        $sql = $combined->toSql();

        $orderedQuery = DB::table(DB::raw("({$sql}) as combined"))
            ->mergeBindings($combined)
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc');

        // Paginate
        $stokKeluar = $orderedQuery->paginate($perPage, ['*'], 'page_keluar')->withQueryString();

        // Load details dynamically to avoid N+1 queries
        $stokKeluarCollection = $stokKeluar->getCollection();
        $nonKainIds = $stokKeluarCollection->where('tipe_mutasi', 'non-kain')->pluck('id')->toArray();
        $kainIds = $stokKeluarCollection->where('tipe_mutasi', 'kain')->pluck('id')->toArray();

        $nonKainDetails = [];
        if (!empty($nonKainIds)) {
            $nonKainDetails = PergerakanStokBahanBaku::with('detailPergerakanStok.bahanBaku')
                ->whereIn('id', $nonKainIds)
                ->get()
                ->keyBy('id');
        }

        $kainDetails = [];
        if (!empty($kainIds)) {
            $kainDetails = PerintahProduksi::with('riwayatPenggunaanKain.bahanBaku')
                ->whereIn('id', $kainIds)
                ->get()
                ->keyBy('id');
        }

        // Attach details mapping
        foreach ($stokKeluarCollection as $row) {
            if ($row->tipe_mutasi === 'non-kain') {
                $model = $nonKainDetails->get($row->id);
                $row->total_qty = $model ? $model->detailPergerakanStok->sum('jumlah') : 0;
                $row->items_summary = $model 
                    ? $model->detailPergerakanStok->map(fn($d) => ($d->bahanBaku->nama_bahan ?? 'Bahan') . ' (' . $d->jumlah . ' ' . ($d->bahanBaku->satuan ?? 'pcs') . ')')->implode(', ')
                    : '';
            } else {
                $model = $kainDetails->get($row->id);
                $row->total_qty = $model ? $model->riwayatPenggunaanKain->sum('jumlah_pakai') : 0;
                $row->items_summary = $model 
                    ? $model->riwayatPenggunaanKain->map(fn($d) => ($d->bahanBaku->nama_bahan ?? 'Kain') . ' (' . (int)$d->jumlah_pakai . ' Roll)')->implode(', ')
                    : '';
            }
        }

        return $stokKeluar;
    }
}

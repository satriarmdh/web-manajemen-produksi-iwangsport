<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\PergerakanStokService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MutasiBahanBakuOwnerController extends Controller
{
    /**
     * Menampilkan riwayat mutasi bahan baku (masuk & keluar) khusus owner.
     *
     * @param Request $request
     * @param PergerakanStokService $pergerakanStokService
     * @return \Illuminate\View\View
     */
    public function mutasiBahanBaku(Request $request, PergerakanStokService $pergerakanStokService)
    {
        $tab = $request->get('tab', 'masuk');

        $stokMasuk = $pergerakanStokService->getStokMasukPaginated([
            'search' => $request->search_masuk,
            'kategori' => $request->kategori_masuk,
            'tanggal_mulai' => $request->tanggal_mulai_masuk,
            'tanggal_akhir' => $request->tanggal_akhir_masuk,
        ]);

        if ($tab === 'keluar') {
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
            if ($request->filled('search_keluar')) {
                $search = $request->input('search_keluar');
                $queryNonKain->where(function ($q) use ($search) {
                    $q->where('p.nomor_transaksi', 'like', "%{$search}%")
                      ->orWhere('p.penerima', 'like', "%{$search}%");
                });
                $queryKain->where(function ($q) use ($search) {
                    $q->where('pp.nomor_wo', 'like', "%{$search}%");
                });
            }

            // Apply date filters if any
            if ($request->filled('tanggal_mulai_keluar')) {
                $queryNonKain->whereDate('p.tanggal', '>=', $request->input('tanggal_mulai_keluar'));
                $queryKain->whereDate('pp.approved_at', '>=', $request->input('tanggal_mulai_keluar'));
            }
            if ($request->filled('tanggal_akhir_keluar')) {
                $queryNonKain->whereDate('p.tanggal', '<=', $request->input('tanggal_akhir_keluar'));
                $queryKain->whereDate('pp.approved_at', '<=', $request->input('tanggal_akhir_keluar'));
            }

            // Apply category filter
            if ($request->filled('kategori_keluar')) {
                $kategori = $request->input('kategori_keluar');
                
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
            $stokKeluar = $orderedQuery->paginate(10, ['*'], 'page_keluar')->withQueryString();

            // Load details dynamically to avoid N+1 queries
            $stokKeluarCollection = $stokKeluar->getCollection();
            $nonKainIds = $stokKeluarCollection->where('tipe_mutasi', 'non-kain')->pluck('id')->toArray();
            $kainIds = $stokKeluarCollection->where('tipe_mutasi', 'kain')->pluck('id')->toArray();

            $nonKainDetails = [];
            if (!empty($nonKainIds)) {
                $nonKainDetails = \App\Models\PergerakanStokBahanBaku::with('detailPergerakanStok.bahanBaku')
                    ->whereIn('id', $nonKainIds)
                    ->get()
                    ->keyBy('id');
            }

            $kainDetails = [];
            if (!empty($kainIds)) {
                $kainDetails = \App\Models\PerintahProduksi::with('riwayatPenggunaanKain.bahanBaku')
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
        } else {
            $stokKeluar = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }

        return view('owner.mutasi-bahan-baku.index', compact('tab', 'stokMasuk', 'stokKeluar'));
    }

    /**
     * Menampilkan detail mutasi bahan baku (bukti lampiran, dll) khusus owner.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function showMutasiBahanBaku(Request $request, $id)
    {
        $type = $request->get('type', 'non-kain');

        if ($type === 'kain') {
            $perintahProduksi = \App\Models\PerintahProduksi::with(['riwayatPenggunaanKain.bahanBaku', 'user', 'approver'])
                ->findOrFail($id);

            return view('owner.mutasi-bahan-baku.show-kain', compact('perintahProduksi'));
        } else {
            $pergerakanStok = \App\Models\PergerakanStokBahanBaku::with(['detailPergerakanStok.bahanBaku', 'supplier', 'user'])
                ->findOrFail($id);

            return view('owner.mutasi-bahan-baku.show', compact('pergerakanStok'));
        }
    }
}

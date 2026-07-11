<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Models\PerintahProduksi;
use Illuminate\Http\Request;

class PerintahProduksiKaryawanController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->user()->role;
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();
        $filterTanggal = $request->string('tanggal')->toString();
        $sort = $request->string('sort', 'mulai_terlama')->toString();

        $perintahProduksi = PerintahProduksi::with(['user', 'details.produk', 'details.bahanBaku'])
            ->whereIn('status_produksi', ['disetujui', 'dalam_produksi'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('nomor_wo', 'like', "%{$search}%")
                        ->orWhereHas('details.produk', function ($produkQuery) use ($search) {
                            $produkQuery->where('nama_produk', 'like', "%{$search}%")
                                ->orWhere('warna', 'like', "%{$search}%");
                        })
                        ->orWhereHas('details.bahanBaku', function ($bahanQuery) use ($search) {
                            $bahanQuery->where('nama_bahan', 'like', "%{$search}%");
                        });
                });
            })
            ->when(in_array($status, ['disetujui', 'dalam_produksi'], true), function ($query) use ($status) {
                $query->where('status_produksi', $status);
            })
            ->when($filterTanggal !== '', function ($query) use ($filterTanggal) {
                $query->whereDate('tgl_mulai', $filterTanggal);
            })
            ->when($sort === 'mulai_terlama', fn ($query) => $query->orderBy('tgl_mulai')->orderBy('created_at')->orderBy('id'))
            ->when($sort === 'mulai_terbaru', fn ($query) => $query->orderByDesc('tgl_mulai'))
            ->when($sort === 'wo_asc', fn ($query) => $query->orderBy('nomor_wo'))
            ->when(! in_array($sort, ['mulai_terlama', 'mulai_terbaru', 'wo_asc'], true), fn ($query) => $query->orderBy('tgl_mulai')->orderBy('created_at')->orderBy('id'))
            ->paginate(10)
            ->withQueryString();

        return view('produksi.perintah-produksi.index', compact('perintahProduksi', 'role', 'search', 'status', 'filterTanggal', 'sort'));
    }

    public function show(Request $request, PerintahProduksi $perintahProduksi)
    {
        abort_unless(in_array($perintahProduksi->status_produksi, ['disetujui', 'dalam_produksi'], true), 403);

        $role = $request->user()->role;
        $perintahProduksi->load(['user', 'details.produk', 'details.bahanBaku']);

        return view('produksi.perintah-produksi.show', compact('perintahProduksi', 'role'));
    }
}

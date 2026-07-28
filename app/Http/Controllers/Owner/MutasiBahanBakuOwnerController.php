<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\PergerakanStokService;
use App\Services\MutasiBahanBakuOwnerService;
use Illuminate\Http\Request;

class MutasiBahanBakuOwnerController extends Controller
{
    public function __construct(
        private readonly PergerakanStokService $pergerakanStokService,
        private readonly MutasiBahanBakuOwnerService $mutasiOwnerService
    ) {}

    /**
     * Menampilkan riwayat mutasi bahan baku (masuk & keluar) khusus owner.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function mutasiBahanBaku(Request $request)
    {
        $tab = $request->get('tab', 'masuk');

        $stokMasuk = $this->pergerakanStokService->getStokMasukPaginated([
            'search' => $request->search_masuk,
            'kategori' => $request->kategori_masuk,
            'tanggal_mulai' => $request->tanggal_mulai_masuk,
            'tanggal_akhir' => $request->tanggal_akhir_masuk,
        ]);

        if ($tab === 'keluar') {
            $stokKeluar = $this->mutasiOwnerService->getStokKeluarPaginated(
                $request->only([
                    'search_keluar',
                    'tanggal_mulai_keluar',
                    'tanggal_akhir_keluar',
                    'kategori_keluar'
                ]),
                10
            );
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

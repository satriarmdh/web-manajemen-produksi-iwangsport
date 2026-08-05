<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Produksi\ApproveAjuanPengambilanProduksiRequest;
use App\Http\Requests\Produksi\IndexAjuanPengambilanProduksiRequest;
use App\Http\Requests\Produksi\RespondAjuanPengambilanProduksiRequest;
use App\Http\Requests\Produksi\StoreAjuanPengambilanProduksiRequest;
use App\Models\AjuanPengambilanProduksi;
use App\Services\AjuanPengambilanProduksiService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AjuanPengambilanProduksiController extends Controller
{
    public function __construct(
        private readonly AjuanPengambilanProduksiService $service
    ) {}

    public function index(IndexAjuanPengambilanProduksiRequest $request)
    {
        return view(
            'produksi.ajuan-pengambilan.index',
            $this->service->getIndexData($request->user(), $request->filters())
        );
    }

    public function redirectLegacy()
    {
        return redirect()->route('produksi.ajuan-pengambilan.index');
    }

    public function masuk(Request $request)
    {
        return view('produksi.ajuan-pengambilan.masuk', [
            'ajuanMasuk' => $this->service->getIncoming($request->user()),
        ]);
    }

    public function store(StoreAjuanPengambilanProduksiRequest $request)
    {
        $items = $request->ajuanItems();

        // Kumpulkan unique dari_karyawan_id dari stok_virtual sebelum create
        $dariKaryawanIds = [];
        foreach ($items as $item) {
            $stokVirtual = \App\Models\StokVirtual::find($item['stok_virtual_id']);
            if ($stokVirtual && !in_array($stokVirtual->id_karyawan, $dariKaryawanIds)) {
                $dariKaryawanIds[] = $stokVirtual->id_karyawan;
            }
        }

        $this->service->storeMany(
            $items,
            $request->user(),
            $request->validated('catatan_pengaju')
        );

        // Notifikasi -> karyawan pemilik stok (dariKaryawan) yang perlu approve
        foreach ($dariKaryawanIds as $karyawanId) {
            $karyawan = \App\Models\User::find($karyawanId);
            if ($karyawan) {
                app(NotificationService::class)->notifyUser(
                    $karyawan,
                    'Ajuan Pengambilan Baru',
                    "{$request->user()->name} mengajukan pengambilan barang dari stok Anda.",
                    NotificationService::TYPE_AJUAN_BARU,
                    '/produksi/ajuan-masuk'
                );
            }
        }

        return redirect()
            ->route('produksi.ajuan-pengambilan.index')
            ->with('success', 'Ajuan pengambilan barang berhasil dibuat.');
    }

    public function approve(
        ApproveAjuanPengambilanProduksiRequest $request,
        AjuanPengambilanProduksi $ajuan
    ) {
        $this->service->approve($ajuan, $request->user());

        // Notifikasi -> karyawan pengaju
        $ajuan->loadMissing(['produk', 'keKaryawan']);
        if ($ajuan->keKaryawan) {
            $namaProduk = $ajuan->produk ? ($ajuan->produk->nama_produk . ' - ' . ucfirst($ajuan->produk->warna ?? '-')) : 'produk';
            $approverName = $request->user()->name . ' (' . ucfirst($request->user()->role) . ')';
            $qty = (int) $ajuan->qty_ajuan;

            app(NotificationService::class)->ajuanDisetujui(
                $ajuan->keKaryawan,
                $namaProduk,
                $qty,
                $approverName
            );
        }

        return redirect()
            ->route('produksi.ajuan-pengambilan.masuk')
            ->with('success', 'Ajuan pengambilan barang disetujui.');
    }

    public function reject(
        RespondAjuanPengambilanProduksiRequest $request,
        AjuanPengambilanProduksi $ajuan
    ) {
        $catatanRespon = $request->validated('catatan_respon');
        $this->service->reject(
            $ajuan,
            $request->user(),
            $catatanRespon
        );

        // Notifikasi -> karyawan pengaju
        $ajuan->loadMissing(['produk', 'keKaryawan']);
        if ($ajuan->keKaryawan) {
            $namaProduk = $ajuan->produk ? ($ajuan->produk->nama_produk . ' - ' . ucfirst($ajuan->produk->warna ?? '-')) : 'produk';
            $approverName = $request->user()->name . ' (' . ucfirst($request->user()->role) . ')';
            $qty = (int) $ajuan->qty_ajuan;

            app(NotificationService::class)->ajuanDitolak(
                $ajuan->keKaryawan,
                $namaProduk,
                $qty,
                $approverName,
                $catatanRespon
            );
        }

        return redirect()
            ->route('produksi.ajuan-pengambilan.masuk')
            ->with('success', 'Ajuan pengambilan barang ditolak.');
    }
}
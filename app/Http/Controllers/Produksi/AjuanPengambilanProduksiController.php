<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Produksi\RespondAjuanPengambilanProduksiRequest;
use App\Http\Requests\Produksi\StoreAjuanPengambilanProduksiRequest;
use App\Models\AjuanPengambilanProduksi;
use App\Models\StokVirtual;
use App\Services\AjuanPengambilanProduksiService;

class AjuanPengambilanProduksiController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $sourceRole = $user->role === 'jahit' ? 'potong' : ($user->role === 'finishing' ? 'jahit' : null);
        $search = trim((string) request('search', ''));
        $filterSumber = request('sumber', '');
        $filterTanggal = request('tanggal', '');
        $sort = request('sort', 'fifo');

        $barangReady = collect();
        if ($sourceRole) {
            $query = StokVirtual::with(['produk', 'karyawan', 'perintahProduksi'])
                ->where('peran', $sourceRole)
                ->where('qty_hold', '>', 0)
                ->where('status_barang', 'Ready');

            if ($filterSumber !== '') {
                $query->where('id_karyawan', $filterSumber);
            }

            if ($filterTanggal !== '') {
                $query->whereHas('perintahProduksi', function ($perintahQuery) use ($filterTanggal) {
                    $perintahQuery->whereDate('tgl_mulai', $filterTanggal);
                });
            }

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('produk', function ($produkQuery) use ($search) {
                        $produkQuery->where('nama_produk', 'like', "%{$search}%")
                            ->orWhere('warna', 'like', "%{$search}%");
                    })->orWhereHas('perintahProduksi', function ($perintahQuery) use ($search) {
                        $perintahQuery->where('nomor_wo', 'like', "%{$search}%");
                    });
                });
            }

            $barangReady = match ($sort) {
                'qty_terbesar' => $query->orderByDesc('qty_hold')->get(),
                'qty_terkecil' => $query->orderBy('qty_hold')->get(),
                'produk_az' => $query->get()->sortBy(fn ($stok) => $stok->produk->nama_produk ?? '')->values(),
                'wo_az' => $query->get()->sortBy(fn ($stok) => $stok->perintahProduksi->nomor_wo ?? '')->values(),
                default => $query->get()->sortBy(fn ($stok) => sprintf(
                    '%s-%010d-%010d',
                    $stok->perintahProduksi?->tgl_mulai?->format('Y-m-d') ?? '9999-12-31',
                    $stok->perintahProduksi?->created_at?->timestamp ?? 0,
                    $stok->perintahProduksi?->id ?? 0
                ))->values(),
            };

            $fifoWarnings = $this->buildFifoWarnings($barangReady, $sourceRole);
        }

        $fifoWarnings = $fifoWarnings ?? collect();

        $sumberOptions = $barangReady->pluck('karyawan')->filter()->unique('id')->values();
        if ($filterSumber !== '' && $sourceRole) {
            $sumberOptions = StokVirtual::with('karyawan')
                ->where('peran', $sourceRole)
                ->where('qty_hold', '>', 0)
                ->where('status_barang', 'Ready')
                ->get()
                ->pluck('karyawan')
                ->filter()
                ->unique('id')
                ->values();
        }

        $ajuanSaya = AjuanPengambilanProduksi::with(['produk', 'perintahProduksi', 'dariKaryawan', 'keKaryawan'])
            ->where('ke_karyawan_id', $user->id)
            ->latest()
            ->get();

        return view('produksi.ajuan-pengambilan.index', compact(
            'barangReady',
            'ajuanSaya',
            'search',
            'filterSumber',
            'filterTanggal',
            'sort',
            'sumberOptions',
            'fifoWarnings'
        ));
    }
    private function buildFifoWarnings($barangReady, string $sourceRole)
    {
        $allReady = StokVirtual::with(['produk', 'perintahProduksi'])
            ->where('peran', $sourceRole)
            ->where('qty_hold', '>', 0)
            ->where('status_barang', 'Ready')
            ->get();

        return $barangReady->mapWithKeys(function ($stok) use ($allReady) {
            $tanggalMulai = $stok->perintahProduksi?->tgl_mulai;
            if (! $tanggalMulai) {
                return [$stok->id => null];
            }

            $older = $allReady
                ->filter(function ($candidate) use ($stok, $tanggalMulai) {
                    if ((int) $candidate->id === (int) $stok->id || (int) $candidate->id_produk !== (int) $stok->id_produk) {
                        return false;
                    }

                    $candidateDate = $candidate->perintahProduksi?->tgl_mulai;
                    if (! $candidateDate) {
                        return false;
                    }

                    return $candidateDate->lt($tanggalMulai);
                })
                ->sortBy(fn ($candidate) => sprintf('%s-%010d', $candidate->perintahProduksi?->tgl_mulai?->format('Y-m-d') ?? '9999-12-31', $candidate->perintahProduksi?->id ?? 0))
                ->first();

            return [$stok->id => $older];
        });
    }
    public function redirectLegacy()
    {
        return redirect()->route('produksi.ajuan-pengambilan.index');
    }

    public function masuk()
    {
        $user = auth()->user();

        $ajuanMasuk = AjuanPengambilanProduksi::with(['produk', 'perintahProduksi', 'dariKaryawan', 'keKaryawan'])
            ->where('dari_karyawan_id', $user->id)
            ->where('status', 'pending')
            ->get()
            ->sortBy(fn ($ajuan) => sprintf(
                '%s-%010d-%010d',
                $ajuan->perintahProduksi?->tgl_mulai?->format('Y-m-d') ?? '9999-12-31',
                $ajuan->perintahProduksi?->created_at?->timestamp ?? 0,
                $ajuan->perintahProduksi?->id ?? 0
            ))
            ->values();

        return view('produksi.ajuan-pengambilan.masuk', compact('ajuanMasuk'));
    }

    public function store(StoreAjuanPengambilanProduksiRequest $request, AjuanPengambilanProduksiService $service)
    {
        $service->storeMany($request->ajuanItems(), $request->user(), $request->validated()['catatan_pengaju'] ?? null);

        return redirect()->route('produksi.ajuan-pengambilan.index')->with('success', 'Ajuan pengambilan barang berhasil dibuat.');
    }

    public function approve(AjuanPengambilanProduksi $ajuan, AjuanPengambilanProduksiService $service)
    {
        $service->approve($ajuan, auth()->user());

        return redirect()->route('produksi.ajuan-pengambilan.masuk')->with('success', 'Ajuan pengambilan barang disetujui.');
    }

    public function reject(RespondAjuanPengambilanProduksiRequest $request, AjuanPengambilanProduksi $ajuan, AjuanPengambilanProduksiService $service)
    {
        $service->reject($ajuan, $request->user(), $request->validated()['catatan_respon'] ?? null);

        return redirect()->route('produksi.ajuan-pengambilan.masuk')->with('success', 'Ajuan pengambilan barang ditolak.');
    }
}

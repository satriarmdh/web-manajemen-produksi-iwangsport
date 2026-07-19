<?php

namespace App\Services;

use App\Models\AjuanPengambilanProduksi;
use App\Models\MutasiProduksi;
use App\Models\StokVirtual;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AjuanPengambilanProduksiService
{
    public function getIndexData(User $user, array $filters): array
    {
        $sourceRole = match ($user->role) {
            'jahit' => 'potong',
            'finishing' => 'jahit',
            default => null,
        };

        $barangReady = $sourceRole
            ? $this->getBarangReady($sourceRole, $filters)
            : collect();

        $sumberOptions = $sourceRole
            ? StokVirtual::with('karyawan')
                ->where('peran', $sourceRole)
                ->whereRaw('total_selesai - total_dikeluarkan > 0') // ready_to_transfer untuk SEMUA tahap (Opsi A)
                ->get()
                ->pluck('karyawan')
                ->filter()
                ->unique('id')
                ->values()
            : collect();

        $ajuanSaya = AjuanPengambilanProduksi::with(['produk', 'perintahProduksi', 'dariKaryawan', 'keKaryawan'])
            ->where('ke_karyawan_id', $user->id)
            ->latest()
            ->get();

        return [
            'barangReady' => $barangReady,
            'barangReadyPerWo' => $barangReady->groupBy('id_perintah'),
            'ajuanSaya' => $ajuanSaya,
            'search' => $filters['search'],
            'filterSumber' => $filters['sumber'],
            'filterTanggal' => $filters['tanggal'],
            'sort' => $filters['sort'],
            'sumberOptions' => $sumberOptions,
            'fifoWarnings' => $sourceRole ? $this->buildFifoWarnings($barangReady, $sourceRole) : collect(),
            'totalProdukReady' => $barangReady->count(),
            'totalQtyReady' => $barangReady->sum(fn($stok) => max(0, (int) $stok->total_selesai - (int) $stok->total_dikeluarkan)),
            'totalPerintahReady' => $barangReady->groupBy('id_perintah')->count(),
            'totalAjuanSayaPending' => $ajuanSaya->where('status', 'pending')->groupBy('id_perintah')->count(),
        ];
    }

    public function getIncoming(User $user): Collection
    {
        return AjuanPengambilanProduksi::with(['produk', 'perintahProduksi', 'dariKaryawan', 'keKaryawan'])
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
    }

    private function getBarangReady(string $sourceRole, array $filters): Collection
    {
        $query = StokVirtual::with(['produk', 'karyawan', 'perintahProduksi'])
            ->where('peran', $sourceRole);

        if ($sourceRole === 'potong') {
            $query->whereRaw('total_selesai - total_dikeluarkan > 0'); // Opsi A: unify untuk semua tahap
        } else {
            $query->whereRaw('total_selesai - total_dikeluarkan > 0');
        }

        if ($filters['sumber'] !== '') {
            $query->where('id_karyawan', $filters['sumber']);
        }

        if ($filters['tanggal'] !== '') {
            $query->whereHas('perintahProduksi', fn ($q) => $q->whereDate('tgl_mulai', $filters['tanggal']));
        }

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('produk', fn ($produk) => $produk
                    ->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('warna', 'like', "%{$search}%"))
                    ->orWhereHas('perintahProduksi', fn ($perintah) => $perintah
                        ->where('nomor_wo', 'like', "%{$search}%"));
            });
        }

        $collection = $query->get();

        $sortByQty = function ($stok) {
            return max(0, (int) $stok->total_selesai - (int) $stok->total_dikeluarkan);
        };

        return match ($filters['sort']) {
            'qty_terbesar' => $collection->sortByDesc($sortByQty)->values(),
            'qty_terkecil' => $collection->sortBy($sortByQty)->values(),
            'produk_az' => $collection->sortBy(fn ($stok) => $stok->produk->nama_produk ?? '')->values(),
            'wo_az' => $collection->sortBy(fn ($stok) => $stok->perintahProduksi->nomor_wo ?? '')->values(),
            default => $collection->sortBy(fn ($stok) => sprintf(
                '%s-%010d-%010d',
                $stok->perintahProduksi?->tgl_mulai?->format('Y-m-d') ?? '9999-12-31',
                $stok->perintahProduksi?->created_at?->timestamp ?? 0,
                $stok->perintahProduksi?->id ?? 0
            ))->values(),
        };
    }

    private function buildFifoWarnings(Collection $barangReady, string $sourceRole): Collection
    {
        $allReadyQuery = StokVirtual::with(['produk', 'perintahProduksi'])
            ->where('peran', $sourceRole);

        if ($sourceRole === 'potong') {
            $allReadyQuery->whereRaw('total_selesai - total_dikeluarkan > 0'); // Opsi A: unify
        } else {
            $allReadyQuery->whereRaw('total_selesai - total_dikeluarkan > 0');
        }

        $allReady = $allReadyQuery->get();

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

                    return $candidateDate && $candidateDate->lt($tanggalMulai);
                })
                ->sortBy(fn ($candidate) => sprintf(
                    '%s-%010d',
                    $candidate->perintahProduksi?->tgl_mulai?->format('Y-m-d') ?? '9999-12-31',
                    $candidate->perintahProduksi?->id ?? 0
                ))
                ->first();

            return [$stok->id => $older];
        });
    }

    public function store(array $data, User $user): AjuanPengambilanProduksi
    {
        return DB::transaction(function () use ($data, $user) {
            $stokSumber = StokVirtual::lockForUpdate()->findOrFail($data['stok_virtual_id']);
            $qtyAjuan = (int) $data['qty_ajuan'];
            $this->ensureValidSource($stokSumber, $user, $qtyAjuan);

            return AjuanPengambilanProduksi::create([
                'id_perintah' => $stokSumber->id_perintah,
                'id_detail_perintah' => $stokSumber->id_detail_perintah,
                'id_produk' => $stokSumber->id_produk,
                'dari_karyawan_id' => $stokSumber->id_karyawan,
                'ke_karyawan_id' => $user->id,
                'dari_tahapan' => $stokSumber->peran,
                'ke_tahapan' => $user->role,
                'qty_ajuan' => $qtyAjuan,
                'status' => 'pending',
                'catatan_pengaju' => $data['catatan_pengaju'] ?? null,
                'tgl_ajuan' => now(),
            ]);
        });
    }

    public function storeMany(array $items, User $user, ?string $catatanPengaju = null): void
    {
        DB::transaction(function () use ($items, $user, $catatanPengaju) {
            foreach ($items as $item) {
                $stokSumber = StokVirtual::lockForUpdate()->findOrFail($item['stok_virtual_id']);
                $qtyAjuan = (int) $item['qty_ajuan'];
                $this->ensureValidSource($stokSumber, $user, $qtyAjuan);

                AjuanPengambilanProduksi::create([
                    'id_perintah' => $stokSumber->id_perintah,
                    'id_detail_perintah' => $stokSumber->id_detail_perintah,
                    'id_produk' => $stokSumber->id_produk,
                    'dari_karyawan_id' => $stokSumber->id_karyawan,
                    'ke_karyawan_id' => $user->id,
                    'dari_tahapan' => $stokSumber->peran,
                    'ke_tahapan' => $user->role,
                    'qty_ajuan' => $qtyAjuan,
                    'status' => 'pending',
                    'catatan_pengaju' => $catatanPengaju,
                    'tgl_ajuan' => now(),
                ]);
            }
        });
    }

    public function approve(AjuanPengambilanProduksi $ajuan, User $user): void
    {
        DB::transaction(function () use ($ajuan, $user) {
            $ajuan = AjuanPengambilanProduksi::lockForUpdate()->findOrFail($ajuan->id);
            $this->ensureCanRespond($ajuan, $user);

            if ($ajuan->status !== 'pending') {
                abort(403);
            }

            $stokSumber = StokVirtual::where('id_detail_perintah', $ajuan->id_detail_perintah)
                ->where('id_karyawan', $ajuan->dari_karyawan_id)
                ->where('peran', $ajuan->dari_tahapan)
                ->lockForUpdate()
                ->firstOrFail();

            // Opsi A: ready_to_transfer = total_selesai - total_dikeluarkan (untuk SEMUA tahap)
            $readyStock = max(0, (int) $stokSumber->total_selesai - (int) $stokSumber->total_dikeluarkan);
            if ($readyStock < (int) $ajuan->qty_ajuan) {
                abort(422, 'Stok ready sumber tidak mencukupi.');
            }

            $stokTujuan = StokVirtual::firstOrNew([
                'id_detail_perintah' => $ajuan->id_detail_perintah,
                'id_karyawan' => $ajuan->ke_karyawan_id,
                'peran' => $ajuan->ke_tahapan,
            ]);

            if (! $stokTujuan->exists) {
                $stokTujuan->fill([
                    'id_perintah' => $ajuan->id_perintah,
                    'id_produk' => $ajuan->id_produk,
                    'qty_hold' => 0,
                    'total_selesai' => 0,
                    'total_dikeluarkan' => 0,
                    'total_reject' => 0,
                    'status_barang' => 'Proses',
                    'is_selesai' => false,
                ]);
            }

            $qtyPindah = (int) $ajuan->qty_ajuan;
            // Opsi A: saat approve, qty_hold sumber TIDAK berubah (hanya total_dikeluarkan yang bertambah).
            // qty_hold = WIP input (barang belum dikerjakan), yang dipindahkan adalah barang SELESAI.
            $stokSumber->total_dikeluarkan = (int) $stokSumber->total_dikeluarkan + $qtyPindah;
            $stokSumber->save();

            // Opsi A: saat approve, qty_hold tujuan bertambah (menerima WIP input yang belum dikerjakan).
            $stokTujuan->qty_hold = (int) $stokTujuan->qty_hold + (int) $ajuan->qty_ajuan;
            if ($qtyPindah > 0 && (bool) $stokTujuan->is_selesai) {
                $stokTujuan->is_selesai = false;
                $stokTujuan->status_validasi = 'normal';
                $stokTujuan->alasan = null;
            }
            $stokTujuan->status_barang = 'Proses';
            $stokTujuan->save();

            $ajuan->update([
                'status' => 'disetujui',
                'tgl_respon' => now(),
            ]);

            MutasiProduksi::create([
                'id_ajuan' => $ajuan->id,
                'id_perintah' => $ajuan->id_perintah,
                'id_detail_perintah' => $ajuan->id_detail_perintah,
                'id_produk' => $ajuan->id_produk,
                'dari_karyawan_id' => $ajuan->dari_karyawan_id,
                'ke_karyawan_id' => $ajuan->ke_karyawan_id,
                'dari_tahapan' => $ajuan->dari_tahapan,
                'ke_tahapan' => $ajuan->ke_tahapan,
                'qty_pindah' => $ajuan->qty_ajuan,
                'tgl_transaksi' => now(),
            ]);
        });
    }

    public function reject(AjuanPengambilanProduksi $ajuan, User $user, ?string $catatan = null): void
    {
        DB::transaction(function () use ($ajuan, $user, $catatan) {
            $ajuan = AjuanPengambilanProduksi::lockForUpdate()->findOrFail($ajuan->id);
            $this->ensureCanRespond($ajuan, $user);

            if ($ajuan->status !== 'pending') {
                abort(403);
            }

            $ajuan->update([
                'status' => 'ditolak',
                'catatan_respon' => $catatan,
                'tgl_respon' => now(),
            ]);
        });
    }

    private function ensureValidSource(StokVirtual $stok, User $user, int $qty): void
    {
        $expectedRole = match ($user->role) {
            'jahit' => 'potong',
            'finishing' => 'jahit',
            default => null,
        };

        if ($expectedRole === null || $stok->peran !== $expectedRole) {
            abort(403);
        }

        // Opsi A: ready_to_transfer = total_selesai - total_dikeluarkan (untuk SEMUA tahap)
        $readyStock = max(0, (int) $stok->total_selesai - (int) $stok->total_dikeluarkan);
        if ($qty < 1 || $qty > $readyStock) {
            abort(422, 'Jumlah pengambilan tidak valid atau melebihi stok ready sumber.');
        }
    }

    private function ensureCanRespond(AjuanPengambilanProduksi $ajuan, User $user): void
    {
        if ((int) $ajuan->dari_karyawan_id !== (int) $user->id || $ajuan->dari_tahapan !== $user->role) {
            abort(403);
        }
    }
}

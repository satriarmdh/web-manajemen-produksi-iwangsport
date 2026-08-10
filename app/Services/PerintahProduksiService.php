<?php

namespace App\Services;

use App\Models\BahanBaku;
use App\Models\DetailPerintahProduksi;
use App\Models\PerintahProduksi;
use App\Models\Produk;
use App\Models\RiwayatPenggunaanKain;
use App\Models\RiwayatStok;
use App\Models\StandardBaselineProduksi;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PerintahProduksiService
{
    /**
     * Ambil semua perintah produksi dengan filter, search, dan pagination
     */
    public function getAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = PerintahProduksi::with(['user', 'approver', 'details.produk', 'details.bahanBaku']);

        // Filter berdasarkan status
        if (!empty($filters['status'])) {
            $query->where('status_produksi', $filters['status']);
        }

        // Search berdasarkan nomor WO
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nomor_wo', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Filter berdasarkan tanggal mulai
        if (!empty($filters['tanggal_mulai'])) {
            $query->whereDate('tgl_mulai', $filters['tanggal_mulai']);
        }

        // Sorting
        $sort = $filters['sort'] ?? 'terbaru';
        $query->when($sort === 'terbaru', fn($q) => $q->latest())
              ->when($sort === 'terlama', fn($q) => $q->oldest());

        return $query->paginate($perPage)->withQueryString();
    }

    public function getFormData(bool $includePreviewNomor = true): array
    {
        $data = [
            'produks' => Produk::where('is_aktif', true)->get(),
            'bahanBakus' => BahanBaku::where('is_aktif', true)
                ->where('kategori', 'kain')
                ->get(),
            'baselines' => StandardBaselineProduksi::where('is_aktif', true)
                ->with(['produk', 'bahanBaku'])
                ->get(),
        ];

        if ($includePreviewNomor) {
            $data['previewNomorWO'] = $this->generateNomorWO();
        }

        return $data;
    }

    public function loadForDetail(PerintahProduksi $perintahProduksi): PerintahProduksi
    {
        $perintahProduksi->load([
            'details.produk',
            'details.bahanBaku',
            'details.mutasiProduksi.dariKaryawan',
            'details.mutasiProduksi.keKaryawan',
            'user',
            'approver'
        ]);

        $perintahProduksi->stokVirtual = \App\Models\StokVirtual::with(['karyawan', 'produkCacat'])
            ->where('id_perintah', $perintahProduksi->id)
            ->get();

        return $perintahProduksi;
    }

    public function ensurePending(PerintahProduksi $perintahProduksi): void
    {
        abort_unless($perintahProduksi->status_produksi === 'pending', 403, 'Perintah produksi hanya bisa diproses saat status pending');
    }

    public function getForKaryawan(array $filters = [], int $perPage = 4): LengthAwarePaginator
    {
        $userId = auth()->id();

        $query = PerintahProduksi::with([
            'user', 'details.produk', 'details.bahanBaku',
            'stokVirtual' => fn($q) => $q->where('id_karyawan', $userId),
        ])
            ->whereIn('status_produksi', ['disetujui', 'dalam_produksi']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
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
        }

        if (in_array($filters['status'] ?? '', ['disetujui', 'dalam_produksi'], true)) {
            $query->where('status_produksi', $filters['status']);
        }

        if (!empty($filters['tanggal'])) {
            $query->whereDate('tgl_mulai', $filters['tanggal']);
        }

        // FIFO: WO where all karyawan's stok_virtual is_selesai → sink to bottom.
        // If NO stok_virtual row exists yet (e.g. potong belum input), treat as
        // incomplete (0 = stays on top) so new WOs float up for action.
        $completedExpr = match ($filters['sort'] ?? 'mulai_terlama') {
            'mulai_terbaru', 'wo_asc' => null,
            default => 'CASE WHEN NOT EXISTS(SELECT 1 FROM stok_virtual sv WHERE sv.id_perintah = perintah_produksi.id AND sv.id_karyawan = ' . (int) $userId . ') OR EXISTS(SELECT 1 FROM stok_virtual sv WHERE sv.id_perintah = perintah_produksi.id AND sv.id_karyawan = ' . (int) $userId . ' AND sv.is_selesai = 0) THEN 0 ELSE 1 END',
        };

        if ($completedExpr !== null) {
            $query->orderByRaw($completedExpr);
        }

        match ($filters['sort'] ?? 'mulai_terlama') {
            'mulai_terbaru' => $query->orderByDesc('tgl_mulai'),
            'wo_asc' => $query->orderBy('nomor_wo'),
            default => $query->orderBy('tgl_mulai')->orderBy('created_at')->orderBy('id'),
        };

        return $query->paginate($perPage)->withQueryString();
    }

    public function ensureVisibleForKaryawan(PerintahProduksi $perintahProduksi): void
    {
        abort_unless(in_array($perintahProduksi->status_produksi, ['disetujui', 'dalam_produksi'], true), 403);
    }

    /**
     * Generate nomor WO otomatis (format: PROD-YYYYMMDD-NNN)
     *
     * Tanggal mengikuti hari pembuatan WO, sedangkan nomor urut terakhir
     * bersifat global agar tidak reset menjadi 001 setiap berganti hari.
     */
    public function generateNomorWO(): string
    {
        $today = now()->format('Ymd');

        $lastWO = PerintahProduksi::withTrashed()
            ->where('nomor_wo', 'like', 'PROD-%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastWO) {
            return "PROD-{$today}-001";
        }

        $lastNumber = (int) substr($lastWO->nomor_wo, strrpos($lastWO->nomor_wo, '-') + 1);
        $newNumber = $lastNumber + 1;

        return "PROD-{$today}-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Simpan perintah produksi baru dengan detail dan riwayat penggunaan kain
     */
    public function create(array $data): PerintahProduksi
    {
        return DB::transaction(function () use ($data) {
            // Generate nomor WO
            $nomorWO = $this->generateNomorWO();

            // Buat perintah produksi
            $perintahProduksi = PerintahProduksi::create([
                'nomor_wo' => $nomorWO,
                'tgl_mulai' => $data['tgl_mulai'],
                'tgl_selesai' => $data['tgl_selesai'] ?? null,
                'status_produksi' => 'pending',
                'user_id' => auth()->id(),
            ]);

            // Buat detail dan riwayat penggunaan kain
            foreach ($data['details'] as $detail) {
                // Cari standard baseline untuk produk dan bahan baku ini
                $baseline = StandardBaselineProduksi::where('produk_id', $detail['produk_id'])
                    ->where('bahan_baku_id', $detail['bahan_baku_id'])
                    ->first();

                if (!$baseline) {
                    throw new \Exception("Standard baseline tidak ditemukan untuk produk ID {$detail['produk_id']} dan bahan baku ID {$detail['bahan_baku_id']}");
                }

                // Hitung estimasi PCS berdasarkan jumlah roll
                $estimasiPcs = $baseline->pcs_per_roll * $detail['qty_roll_pakai'];

                // Buat detail perintah produksi
                $detailProduksi = DetailPerintahProduksi::create([
                    'perintah_produksi_id' => $perintahProduksi->id,
                    'produk_id' => $detail['produk_id'],
                    'bahan_baku_id' => $detail['bahan_baku_id'],
                    'qty_roll_pakai' => $detail['qty_roll_pakai'],
                    'estimasi_pcs' => $estimasiPcs,
                    'toleransi_minus' => $baseline->toleransi_minus * $detail['qty_roll_pakai'],
                    'status_validasi_potong' => 'pending',
                ]);

                // Riwayat penggunaan kain dan pengurangan stok dicatat saat WO disetujui owner.
            }

            return $perintahProduksi;
        });
    }

    /**
     * Update perintah produksi (hanya jika status pending)
     */
    public function update(PerintahProduksi $perintahProduksi, array $data): PerintahProduksi
    {
        // Hanya bisa update jika status masih pending
        if ($perintahProduksi->status_produksi !== 'pending') {
            throw new \Exception('Perintah produksi hanya bisa diubah saat status masih pending');
        }

        return DB::transaction(function () use ($perintahProduksi, $data) {
            // Update header
            $perintahProduksi->update([
                'tgl_mulai' => $data['tgl_mulai'],
                'tgl_selesai' => $data['tgl_selesai'] ?? null,
            ]);

            // Hapus semua detail lama. Riwayat penggunaan kain belum dibuat selama status masih pending.
            DetailPerintahProduksi::where('perintah_produksi_id', $perintahProduksi->id)->delete();

            // Buat detail baru
            foreach ($data['details'] as $detail) {
                $baseline = StandardBaselineProduksi::where('produk_id', $detail['produk_id'])
                    ->where('bahan_baku_id', $detail['bahan_baku_id'])
                    ->first();

                if (!$baseline) {
                    throw new \Exception("Standard baseline tidak ditemukan");
                }

                $estimasiPcs = $baseline->pcs_per_roll * $detail['qty_roll_pakai'];

                $detailProduksi = DetailPerintahProduksi::create([
                    'perintah_produksi_id' => $perintahProduksi->id,
                    'produk_id' => $detail['produk_id'],
                    'bahan_baku_id' => $detail['bahan_baku_id'],
                    'qty_roll_pakai' => $detail['qty_roll_pakai'],
                    'estimasi_pcs' => $estimasiPcs,
                    'toleransi_minus' => $baseline->toleransi_minus * $detail['qty_roll_pakai'],
                    'status_validasi_potong' => 'pending',
                ]);

                // Riwayat penggunaan kain dan pengurangan stok dicatat saat WO disetujui owner.
            }

            return $perintahProduksi->fresh();
        });
    }

    /**
     * Hapus perintah produksi (soft delete, hanya jika status pending)
     */
    public function delete(PerintahProduksi $perintahProduksi): bool
    {
        // Hanya bisa delete jika status masih pending
        if ($perintahProduksi->status_produksi !== 'pending') {
            throw new \Exception('Perintah produksi hanya bisa dihapus saat status masih pending');
        }

        return $perintahProduksi->delete();
    }

    /**
     * Approve perintah produksi (oleh owner)
     */
    public function approve(PerintahProduksi $perintahProduksi): PerintahProduksi
    {
        if ($perintahProduksi->status_produksi !== 'pending') {
            throw new \Exception('Hanya perintah produksi dengan status pending yang bisa disetujui');
        }

        return DB::transaction(function () use ($perintahProduksi) {
            $perintahProduksi->loadMissing(['details.bahanBaku']);

            foreach ($perintahProduksi->details as $detail) {
                $bahanBaku = BahanBaku::lockForUpdate()->findOrFail($detail->bahan_baku_id);
                $jumlahPakai = (int) $detail->qty_roll_pakai;
                $stokSebelum = (int) $bahanBaku->stok;

                if ($stokSebelum < $jumlahPakai) {
                    throw new \Exception("Stok kain {$bahanBaku->nama_bahan} tidak mencukupi untuk WO {$perintahProduksi->nomor_wo}");
                }

                $stokSesudah = $stokSebelum - $jumlahPakai;

                $riwayatPenggunaanKain = RiwayatPenggunaanKain::create([
                    'perintah_produksi_id' => $perintahProduksi->id,
                    'detail_perintah_produksi_id' => $detail->id,
                    'bahan_baku_id' => $bahanBaku->id,
                    'jumlah_pakai' => $jumlahPakai,
                    'keterangan' => 'Penggunaan kain untuk ' . $perintahProduksi->nomor_wo,
                ]);

                RiwayatStok::create([
                    'jenis_item' => 'bahan_baku',
                    'id_item' => $bahanBaku->id,
                    'jenis_pergerakan' => 'keluar',
                    'jumlah' => $jumlahPakai,
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSesudah,
                    'user_id' => auth()->id(),
                    'keterangan' => 'Penggunaan kain untuk WO ' . $perintahProduksi->nomor_wo,
                    'referensi_type' => RiwayatPenggunaanKain::class,
                    'referensi_id' => $riwayatPenggunaanKain->id,
                ]);

                BahanBaku::withoutEvents(function () use ($bahanBaku, $stokSesudah) {
                    $bahanBaku->stok = $stokSesudah;
                    $bahanBaku->save();
                });
            }

            $perintahProduksi->update([
                'status_produksi' => 'disetujui',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            return $perintahProduksi->fresh(['details.bahanBaku', 'riwayatPenggunaanKain']);
        });
    }

    /**
     * Reject perintah produksi (oleh owner)
     */
    public function reject(PerintahProduksi $perintahProduksi, string $alasan = null): PerintahProduksi
    {
        if ($perintahProduksi->status_produksi !== 'pending') {
            throw new \Exception('Hanya perintah produksi dengan status pending yang bisa ditolak');
        }

        $perintahProduksi->update([
            'status_produksi' => 'ditolak',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'alasan_penolakan' => $alasan,
        ]);

        return $perintahProduksi;
    }

    /**
     * Input hasil potong (oleh karyawan potong)
     */
    public function inputHasilPotong(DetailPerintahProduksi $detail, int $qtyPcsPotong, ?string $alasan = null): DetailPerintahProduksi
    {
        // Hitung total reject milik karyawan potong jika ada
        $totalReject = (int) (\App\Models\StokVirtual::where('id_detail_perintah', $detail->id)
            ->where('peran', 'potong')
            ->value('total_reject') ?? 0);

        // Hitung batas bawah toleransi
        $batasBawah = $detail->estimasi_pcs - $detail->toleransi_minus;

        // Tentukan status validasi (termasuk barang cacat)
        $statusValidasi = ($qtyPcsPotong + $totalReject) >= $batasBawah ? 'normal' : 'flag';

        // Jika flag, alasan wajib diisi
        if ($statusValidasi === 'flag' && empty($alasan)) {
            throw new \Exception('Alasan wajib diisi jika hasil potong di bawah batas toleransi');
        }

        // Update detail
        $detail->update([
            'qty_pcs_potong' => $qtyPcsPotong,
            'status_validasi_potong' => $statusValidasi,
            'alasan' => $alasan,
        ]);

        // Cek apakah ini input pertama untuk WO ini
        $perintahProduksi = $detail->perintahProduksi;
        if ($perintahProduksi->status_produksi === 'disetujui') {
            $perintahProduksi->update(['status_produksi' => 'dalam_produksi']);
        }

        return $detail;
    }

    /**
     * Tandai perintah produksi selesai (oleh admin).
     *
     * Completion Gate (Opsi A):
     * 1. Block jika masih ada stok ready (total_selesai > total_dikeluarkan) yang belum diserahkan ke admin.
     * 2. Hitung ulang status_penerimaan untuk semua detail (otomatis set selisih_kurang jika perlu).
     */
    public function selesai(PerintahProduksi $perintahProduksi, string $tglSelesai): PerintahProduksi
    {
        if ($perintahProduksi->status_produksi !== 'dalam_produksi') {
            throw new \Exception('Perintah produksi hanya bisa diselesaikan jika status dalam_produksi');
        }

        return DB::transaction(function () use ($perintahProduksi, $tglSelesai) {
            $perintahProduksi->loadMissing(['details']);

            // 1. Completion Gate: cek apakah masih ada stok ready yang belum diserahkan ke admin
            foreach ($perintahProduksi->details as $detail) {
                $unreceivedReady = \App\Models\StokVirtual::where('id_detail_perintah', $detail->id)
                ->whereColumn('total_selesai', '>', 'total_dikeluarkan')
                ->exists();

            if ($unreceivedReady) {
                    throw new \Exception(
                        'Tidak dapat menyelesaikan WO. Masih ada barang ready di tangan karyawan finishing ' .
                        'yang belum diserahkan ke admin untuk produk: ' .
                        ($detail->produk->nama_produk ?? 'ID ' . $detail->id)
                    );
                }
            }

            // 2. Hitung ulang status_penerimaan untuk semua detail (otomatis set selisih_kurang/sesuai/selisih_lebih)
            $penerimaanService = app(\App\Services\PenerimaanHasilProduksiService::class);
            foreach ($perintahProduksi->details as $detail) {
                $penerimaanService->calculateAndSetStatus($detail);
            }

            $perintahProduksi->update([
                'status_produksi' => 'selesai',
                'tgl_selesai' => $tglSelesai,
            ]);

            return $perintahProduksi;
        });
    }

    /**
     * Memantau progres produksi (Daftar semua WO yang disetujui / dalam produksi / selesai / ditolak)
     */
    public function getPantauProgresPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = PerintahProduksi::with(['user', 'approver', 'details.produk', 'details.bahanBaku'])
            ->where('status_produksi', '!=', 'pending');

        // Filter berdasarkan status
        if (!empty($filters['status']) && $filters['status'] !== 'pending') {
            $query->where('status_produksi', $filters['status']);
        }

        // Search berdasarkan nomor WO atau pembuat
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nomor_wo', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Filter berdasarkan tanggal mulai
        if (!empty($filters['tanggal_mulai'])) {
            $query->whereDate('tgl_mulai', $filters['tanggal_mulai']);
        }

        // Sorting
        $sort = $filters['sort'] ?? 'terbaru';
        $query->when($sort === 'terbaru', fn($q) => $q->latest())
              ->when($sort === 'terlama', fn($q) => $q->oldest());

        return $query->paginate($perPage)->withQueryString();
    }
}

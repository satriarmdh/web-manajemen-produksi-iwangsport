<?php

namespace App\Services;

use App\Models\PerintahProduksi;
use App\Models\DetailPerintahProduksi;
use App\Models\RiwayatPenggunaanKain;
use App\Models\RiwayatStok;
use App\Models\BahanBaku;
use App\Models\StandardBaselineProduksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

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

        // Sorting
        $sort = $filters['sort'] ?? 'terbaru';
        $query->when($sort === 'terbaru', fn($q) => $q->latest())
              ->when($sort === 'terlama', fn($q) => $q->oldest());

        return $query->paginate($perPage)->withQueryString();
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
        // Hitung batas bawah toleransi
        $batasBawah = $detail->estimasi_pcs - $detail->toleransi_minus;

        // Tentukan status validasi
        $statusValidasi = $qtyPcsPotong >= $batasBawah ? 'normal' : 'flag';

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
     * Tandai perintah produksi selesai (oleh admin)
     */
    public function selesai(PerintahProduksi $perintahProduksi, string $tglSelesai): PerintahProduksi
    {
        if ($perintahProduksi->status_produksi !== 'dalam_produksi') {
            throw new \Exception('Perintah produksi hanya bisa diselesaikan jika status dalam_produksi');
        }

        $perintahProduksi->update([
            'status_produksi' => 'selesai',
            'tgl_selesai' => $tglSelesai,
        ]);

        return $perintahProduksi;
    }
}

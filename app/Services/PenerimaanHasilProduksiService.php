<?php

namespace App\Services;

use App\Models\DetailPerintahProduksi;
use App\Models\PenerimaanHasilProduksi;
use App\Models\Produk;
use App\Models\RiwayatStok;
use App\Models\StokVirtual;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PenerimaanHasilProduksiService
{
    /**
     * Create penerimaan hasil produksi and update related tables
     */
    public function create(
        DetailPerintahProduksi $detail,
        User $admin,
        array $data
    ): PenerimaanHasilProduksi {
        return DB::transaction(function () use ($detail, $admin, $data) {
            $jenisPenerimaan = $data['jenis_penerimaan'] ?? 'baik';

            $stokVirtual = StokVirtual::where([
                'id_detail_perintah' => $detail->id,
                'id_karyawan' => $data['dari_karyawan_id'],
            ])->lockForUpdate()->firstOrFail();

            if ($jenisPenerimaan === 'cacat') {
                // hitung sisa reject yang belum diserahkan
                $deliveredReject = (int) PenerimaanHasilProduksi::where([
                    'perintah_produksi_detail_id' => $detail->id,
                    'dari_karyawan_id' => $data['dari_karyawan_id'],
                    'jenis_penerimaan' => 'cacat',
                ])->sum('qty_diterima');

                $qtySisa = (int) $stokVirtual->total_reject - $deliveredReject;

                if ($qtySisa < $data['qty_diterima']) {
                    throw new \Exception(
                        "Qty cacat diterima ({$data['qty_diterima']}) melebihi sisa reject karyawan ({$qtySisa}). " .
                        "Total reject: {$stokVirtual->total_reject}, Sudah diserahkan: {$deliveredReject}"
                    );
                }

                // 2. Handle photo upload
                $buktiPath = $data['bukti_foto']->store('penerimaan-hasil-produksi', 'public');

                // 3. Create penerimaan record
                $penerimaan = PenerimaanHasilProduksi::create([
                    'perintah_produksi_detail_id' => $detail->id,
                    'admin_user_id' => $admin->id,
                    'dari_karyawan_id' => $data['dari_karyawan_id'],
                    'tanggal_terima' => $data['tanggal_terima'],
                    'qty_diterima' => $data['qty_diterima'],
                    'jenis_penerimaan' => 'cacat',
                    'catatan' => $data['catatan'] ?? null,
                    'bukti_foto' => $buktiPath,
                ]);

                // 4. Update detail total_qty_cacat_diterima
                $detail->increment('total_qty_cacat_diterima', $data['qty_diterima']);

                return $penerimaan->load(['admin', 'dariKaryawan']);
            }

            // Opsi A: ready_to_transfer = total_selesai - total_dikeluarkan (untuk SEMUA tahap).
            // Qty yang tersedia untuk diserahkan = total_selesai - total_dikeluarkan
            $qtySisa = (int) $stokVirtual->total_selesai - (int) $stokVirtual->total_dikeluarkan;
            
            if ($qtySisa < $data['qty_diterima']) {
                throw new \Exception(
                    "Qty diterima ({$data['qty_diterima']}) melebihi stok yang belum diserahkan ({$qtySisa}). " .
                    "Total selesai: {$stokVirtual->total_selesai}, Sudah diserahkan: {$stokVirtual->total_dikeluarkan}"
                );
            }

            // 2. Handle photo upload
            $buktiPath = $data['bukti_foto']->store('penerimaan-hasil-produksi', 'public');

            // 3. Create penerimaan record
            $penerimaan = PenerimaanHasilProduksi::create([
                'perintah_produksi_detail_id' => $detail->id,
                'admin_user_id' => $admin->id,
                'dari_karyawan_id' => $data['dari_karyawan_id'],
                'tanggal_terima' => $data['tanggal_terima'],
                'qty_diterima' => $data['qty_diterima'],
                'jenis_penerimaan' => 'baik',
                'catatan' => $data['catatan'] ?? null,
                'bukti_foto' => $buktiPath,
            ]);

            // 4. INCREMENT total_dikeluarkan
            $stokVirtual->increment('total_dikeluarkan', $data['qty_diterima']);

            // 5. Increase produk stock
            $produk = $detail->produk;
            $stokSebelum = $produk->stok;
            Produk::withoutEvents(function () use ($produk, $data) {
                $produk->increment('stok', $data['qty_diterima']);
            });
            $stokSesudah = $produk->fresh()->stok;

            // 6. Update detail totals
            $detail->increment('total_qty_diterima', $data['qty_diterima']);
            $this->calculateAndSetStatus($detail->fresh());

            // 7. Create audit trail in riwayat_stok
            $nomorWo = $detail->perintahProduksi->nomor_wo ?? '-';
            $karyawanName = $penerimaan->dariKaryawan->name ?? '-';
            $catatanStr = !empty($data['catatan']) ? " - Catatan: {$data['catatan']}" : '';

            RiwayatStok::create([
                'jenis_item' => 'produk',
                'id_item' => $produk->id,
                'jenis_pergerakan' => 'masuk',
                'jumlah' => $data['qty_diterima'],
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'keterangan' => "Penerimaan hasil produksi dari {$karyawanName} (WO: {$nomorWo}){$catatanStr}",
                'referencing_type' => 'penerimaan_hasil_produksi',
                'referensi_type' => 'penerimaan_hasil_produksi',
                'referensi_id' => $penerimaan->id,
            ]);

            return $penerimaan->load(['admin', 'dariKaryawan']);
        });
    }

    /**
     * Create reversal/correction entry
     */
    public function createReversal(
        PenerimaanHasilProduksi $original,
        User $admin,
        string $catatan
    ): PenerimaanHasilProduksi {
        $detail = $original->detail;
        
        return DB::transaction(function () use ($original, $admin, $catatan, $detail) {
            $jenisPenerimaan = $original->jenis_penerimaan ?? 'baik';

            if ($jenisPenerimaan === 'cacat') {
                // 1. Create reversal penerimaan (negative qty)
                $penerimaan = PenerimaanHasilProduksi::create([
                    'perintah_produksi_detail_id' => $detail->id,
                    'admin_user_id' => $admin->id,
                    'dari_karyawan_id' => $original->dari_karyawan_id,
                    'tanggal_terima' => today(),
                    'qty_diterima' => -1 * $original->qty_diterima, // NEGATIVE
                    'jenis_penerimaan' => 'cacat',
                    'catatan' => "REVERSAL: {$catatan}",
                    'bukti_foto' => $original->bukti_foto, // Reuse original photo
                ]);

                // 2. Decrement detail total_qty_cacat_diterima
                $detail->decrement('total_qty_cacat_diterima', $original->qty_diterima);

                return $penerimaan;
            }

            // 1. Find the stok_virtual record to return stock to
            $stokVirtual = StokVirtual::where([
                'id_detail_perintah' => $detail->id,
                'id_karyawan' => $original->dari_karyawan_id,
            ])->firstOrFail();

            // 2. Create reversal penerimaan (negative qty)
            $penerimaan = PenerimaanHasilProduksi::create([
                'perintah_produksi_detail_id' => $detail->id,
                'admin_user_id' => $admin->id,
                'dari_karyawan_id' => $original->dari_karyawan_id,
                'tanggal_terima' => today(),
                'qty_diterima' => -1 * $original->qty_diterima, // NEGATIVE
                'jenis_penerimaan' => 'baik',
                'catatan' => "REVERSAL: {$catatan}",
                'bukti_foto' => $original->bukti_foto, // Reuse original photo
            ]);

            // 3. DECREMENT total_dikeluarkan
            $stokVirtual->decrement('total_dikeluarkan', $original->qty_diterima);

            // 4. Decrease produk stock (reverse the increase)
            $produk = $detail->produk;
            $stokSebelum = $produk->stok;
            Produk::withoutEvents(function () use ($produk, $original) {
                $produk->decrement('stok', $original->qty_diterima);
            });
            $stokSesudah = $produk->fresh()->stok;

            // 5. Update detail totals
            $detail->decrement('total_qty_diterima', $original->qty_diterima);
            $this->calculateAndSetStatus($detail->fresh());

            // 6. Create audit trail
            $nomorWo = $detail->perintahProduksi->nomor_wo ?? '-';
            $karyawanName = $original->dariKaryawan->name ?? '-';
            $catatanStr = !empty($catatan) ? " - {$catatan}" : '';

            RiwayatStok::create([
                'jenis_item' => 'produk',
                'id_item' => $produk->id,
                'jenis_pergerakan' => 'keluar',
                'jumlah' => -1 * $original->qty_diterima,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'keterangan' => "Pembatalan penerimaan hasil produksi dari {$karyawanName} (WO: {$nomorWo}){$catatanStr}",
                'referencing_type' => 'penerimaan_hasil_produksi',
                'referensi_type' => 'penerimaan_hasil_produksi',
                'referensi_id' => $penerimaan->id,
            ]);

            return $penerimaan;
        });
    }

    /**
     * Pembatalan / Hapus Penerimaan Hasil Produksi (Reversal)
     */
    public function destroyPenerimaan(PenerimaanHasilProduksi $penerimaan, User $admin): void
    {
        if ($penerimaan->qty_diterima <= 0) {
            throw new \Exception('Transaksi reversal tidak dapat dihapus kembali.');
        }

        DB::transaction(function () use ($penerimaan, $admin) {
            $detail = $penerimaan->detail;
            $jenisPenerimaan = $penerimaan->jenis_penerimaan ?? 'baik';
            $qty = $penerimaan->qty_diterima;

            if ($jenisPenerimaan === 'cacat') {
                // 1. Decrement detail total_qty_cacat_diterima
                $detail->decrement('total_qty_cacat_diterima', $qty);

                // 2. Delete photo if exists
                if ($penerimaan->bukti_foto && Storage::disk('public')->exists($penerimaan->bukti_foto)) {
                    Storage::disk('public')->delete($penerimaan->bukti_foto);
                }

                // 3. Delete penerimaan record
                $penerimaan->delete();

                // 4. Recalculate status
                $this->calculateAndSetStatus($detail->fresh());
                return;
            }

            // For 'baik' penerimaan:
            $produk = $detail->produk;

            // Check if warehouse product stock is sufficient to reverse
            if ($produk->stok < $qty) {
                throw new \Exception("Stok produk '{$produk->nama_produk}' di gudang tersisa {$produk->stok} pcs, tidak mencukupi untuk membatalkan penerimaan ({$qty} pcs).");
            }

            // 1. Decrement product stock in warehouse
            $stokSebelum = $produk->stok;
            Produk::withoutEvents(function () use ($produk, $qty) {
                $produk->decrement('stok', $qty);
            });
            $stokSesudah = $produk->fresh()->stok;

            // 2. Decrement total_dikeluarkan in stok_virtual (returning stock to employee's virtual hold)
            $stokVirtual = StokVirtual::where([
                'id_detail_perintah' => $detail->id,
                'id_karyawan' => $penerimaan->dari_karyawan_id,
            ])->first();

            if ($stokVirtual) {
                $stokVirtual->decrement('total_dikeluarkan', $qty);
            }

            // 3. Decrement detail total_qty_diterima
            $detail->decrement('total_qty_diterima', $qty);

            // 4. Create audit trail in riwayat_stok
            $nomorWo = $detail->perintahProduksi->nomor_wo ?? '-';
            $karyawanName = $penerimaan->dariKaryawan->name ?? '-';

            RiwayatStok::create([
                'jenis_item' => 'produk',
                'id_item' => $produk->id,
                'jenis_pergerakan' => 'keluar',
                'jumlah' => -1 * $qty,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'keterangan' => "Pembatalan penerimaan hasil produksi dari {$karyawanName} (WO: {$nomorWo}) oleh Admin {$admin->name}",
                'referencing_type' => 'penerimaan_hasil_produksi',
                'referensi_type' => 'penerimaan_hasil_produksi',
                'referensi_id' => $penerimaan->id,
            ]);

            // 5. Delete photo if exists
            if ($penerimaan->bukti_foto && Storage::disk('public')->exists($penerimaan->bukti_foto)) {
                Storage::disk('public')->delete($penerimaan->bukti_foto);
            }

            // 6. Delete penerimaan record
            $penerimaan->delete();

            // 7. Recalculate status
            $this->calculateAndSetStatus($detail->fresh());
        });
    }

    /**
     * Calculate status penerimaan berdasarkan total_qty_diterima vs estimasi_pcs.
     * NOTE: Method ini TIDAK cek stok_virtual. Untuk status selisih_kurang otomatis,
     * gunakan calculateAndSetStatus() yang cek apakah masih ada stok ready yang belum diserahkan.
     */
    public function calculateStatus(DetailPerintahProduksi $detail): string
    {
        $estimasi = (int) $detail->estimasi_pcs;
        $toleransi = (int) $detail->toleransi_minus;
        $batasMinNormal = $estimasi - $toleransi;

        // Total akumulasi diterima (baik + cacat)
        $totalDiterima = (int) $detail->total_qty_diterima + (int) $detail->total_qty_cacat_diterima;

        if ($totalDiterima == 0) {
            return 'belum_diterima';
        }

        if ($totalDiterima > $estimasi) {
            return 'selisih_lebih';
        }

        // Cek apakah masih ada stok ready (baik/cacat) yang belum diserahkan ke admin
        $hasUnreceivedReady = StokVirtual::where('id_detail_perintah', $detail->id)
            ->whereColumn('total_selesai', '>', 'total_dikeluarkan')
            ->exists();

        $stokVirtuaList = StokVirtual::where('id_detail_perintah', $detail->id)
            ->where('total_reject', '>', 0)
            ->get();

        $hasUnreceivedDefect = false;
        foreach ($stokVirtuaList as $stok) {
            $deliveredReject = (int) PenerimaanHasilProduksi::where([
                'perintah_produksi_detail_id' => $detail->id,
                'dari_karyawan_id' => $stok->id_karyawan,
                'jenis_penerimaan' => 'cacat',
            ])->sum('qty_diterima');

            if ($stok->total_reject > $deliveredReject) {
                $hasUnreceivedDefect = true;
                break;
            }
        }

        // Jika masih ada stok belum diserahkan -> sebagian
        if ($hasUnreceivedReady || $hasUnreceivedDefect) {
            return 'sebagian';
        }

        // Jika semua stok telah diserahkan / WO diselesaikan:
        // Cek apakah total diterima memenuhi batas min normal
        if ($totalDiterima < $batasMinNormal) {
            return 'selisih_kurang'; // Flagged
        }

        return 'sesuai';
    }

    /**
     * Calculate AND set status penerimaan ke detail.
     * Digunakan setelah input penerimaan atau saat finalize completion.
     */
    public function calculateAndSetStatus(DetailPerintahProduksi $detail): string
    {
        $status = $this->calculateStatus($detail);
        $detail->update(['status_penerimaan' => $status]);
        return $status;
    }

    /**
     * Get history for a specific detail
     */
    public function getHistoryForDetail(int $detailId): Collection
    {
        return PenerimaanHasilProduksi::forDetail($detailId)
            ->with(['admin', 'dariKaryawan'])
            ->orderBy('tanggal_terima', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Calculate summary data for display
     */
    public function calculateSummary(DetailPerintahProduksi $detail): array
    {
        $estimasi = $detail->estimasi_pcs;
        $diterima = $detail->total_qty_diterima;
        $sisa = $estimasi - $diterima;
        $status = $detail->status_penerimaan;

        return [
            'estimasi' => $estimasi,
            'total_diterima' => $diterima,
            'sisa' => $sisa,
            'status_penerimaan' => $status,
            'status_label' => $this->getStatusLabel($status),
            'status_badge_class' => $this->getStatusBadgeClass($status),
        ];
    }

    /**
     * Get available karyawan finishing with ready stock
     * 
     * Logic:
     * - total_selesai = qty yang sudah diinputkan karyawan (barang ready)
     * - total_dikeluarkan = qty yang sudah diserahkan ke tahap berikutnya
     * - Sisa = total_selesai - total_dikeluarkan (yang belum diserahkan)
     */
    public function getAvailableKaryawanForDetail(DetailPerintahProduksi $detail, string $type = 'baik'): Collection
    {
        if ($type === 'cacat') {
            return StokVirtual::where('id_detail_perintah', $detail->id)
                ->where('total_reject', '>', 0)
                ->with('karyawan')
                ->get()
                ->map(function ($stok) use ($detail) {
                    $deliveredReject = (int) PenerimaanHasilProduksi::where([
                        'perintah_produksi_detail_id' => $detail->id,
                        'dari_karyawan_id' => $stok->id_karyawan,
                        'jenis_penerimaan' => 'cacat',
                    ])->sum('qty_diterima');

                    $qtyReady = (int) $stok->total_reject - $deliveredReject;

                    return [
                        'karyawan_id' => $stok->id_karyawan,
                        'karyawan_name' => $stok->karyawan->name . ' (' . ucfirst($stok->peran) . ')',
                        'qty_ready' => $qtyReady,
                        'qty_total' => (int) $stok->total_reject,
                        'qty_diserahkan' => $deliveredReject,
                    ];
                })
                ->filter(fn($item) => $item['qty_ready'] > 0)
                ->values();
        }

        return StokVirtual::where('id_detail_perintah', $detail->id)
            ->where('peran', 'finishing')
            ->whereColumn('total_selesai', '>', 'total_dikeluarkan')
            ->with('karyawan')
            ->get()
            ->map(function ($stok) {
                return [
                    'karyawan_id' => $stok->id_karyawan,
                    'karyawan_name' => $stok->karyawan->name,
                    'qty_ready' => (int) $stok->total_selesai - (int) $stok->total_dikeluarkan,
                    'qty_total' => (int) $stok->total_selesai,
                    'qty_diserahkan' => (int) $stok->total_dikeluarkan,
                ];
            });
    }

    /**
     * Check if can mark perintah produksi as complete
     */
    public function canMarkComplete(DetailPerintahProduksi $detail): bool
    {
        // Opsi A: Cek apakah masih ada stok ready yang belum diserahkan ke admin
        // ready_to_transfer = total_selesai - total_dikeluarkan > 0
        $unreceivedReady = StokVirtual::where('id_detail_perintah', $detail->id)
            ->whereColumn('total_selesai', '>', 'total_dikeluarkan')
            ->exists();

        if ($unreceivedReady) {
            return false; // Masih ada stok ready belum diterima admin
        }

        // Saat semua sudah diserahkan, hitung ulang status (otomatis set selisih_kurang jika perlu)
        return !in_array($detail->status_penerimaan, ['belum_diterima']);
    }

    // Helper methods

    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            'belum_diterima' => 'Belum Diterima',
            'sebagian' => 'Sebagian',
            'sesuai' => 'Sesuai',
            'selisih_kurang' => 'Selisih Kurang',
            'selisih_lebih' => 'Selisih Lebih',
            default => $status,
        };
    }

    private function getStatusBadgeClass(string $status): string
    {
        return match ($status) {
            'belum_diterima' => 'bg-gray-50 text-gray-600 border-gray-100',
            'sebagian' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
            'sesuai' => 'bg-green-50 text-green-700 border-green-100',
            'selisih_kurang' => 'bg-red-50 text-red-700 border-red-100',
            'selisih_lebih' => 'bg-orange-50 text-orange-700 border-orange-100',
            default => 'bg-gray-50 text-gray-600 border-gray-100',
        };
    }
}

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
            // Opsi A: ready_to_transfer = total_selesai - total_dikeluarkan (untuk SEMUA tahap).
            // Filter status_barang='Ready' dihapus karena tidak menjamin masih ada barang yang belum diserahkan.
            $stokVirtual = StokVirtual::where([
                'id_detail_perintah' => $detail->id,
                'id_karyawan' => $data['dari_karyawan_id'],
            ])
            ->whereColumn('total_selesai', '>', 'total_dikeluarkan')
            ->lockForUpdate()
            ->firstOrFail();

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
                'catatan' => $data['catatan'] ?? null,
                'bukti_foto' => $buktiPath,
            ]);

            // 4. INCREMENT total_dikeluarkan (stok karyawan yang diserahkan ke tahap berikutnya)
            $stokVirtual->increment('total_dikeluarkan', $data['qty_diterima']);

            // 5. Increase produk stock
            $produk = $detail->produk;
            $stokSebelum = $produk->stok;
            $produk->increment('stok', $data['qty_diterima']);
            $stokSesudah = $produk->fresh()->stok;

            // 6. Update detail totals dan hitung status penerimaan (otomatis cek stok_virtual untuk selisih_kurang)
            $detail->increment('total_qty_diterima', $data['qty_diterima']);
            $this->calculateAndSetStatus($detail->fresh());

            // 7. Create audit trail in riwayat_stok
            RiwayatStok::create([
                'jenis_item' => 'produk',
                'id_item' => $produk->id,
                'jenis_pergerakan' => 'masuk',
                'jumlah' => $data['qty_diterima'],
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'keterangan' => "Penerimaan hasil produksi dari {$penerimaan->dariKaryawan->name}: " . ($data['catatan'] ?? ''),
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
        // For reversal, we reverse the qty but need to add back to stok_virtual
        $detail = $original->detail;
        
        return DB::transaction(function () use ($original, $admin, $catatan, $detail) {
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
                'catatan' => "REVERSAL: {$catatan}",
                'bukti_foto' => $original->bukti_foto, // Reuse original photo
            ]);

            // 3. DECREMENT total_dikeluarkan (batalkan stok yang sudah diserahkan)
            $stokVirtual->decrement('total_dikeluarkan', $original->qty_diterima);

            // 4. Decrease produk stock (reverse the increase)
            $produk = $detail->produk;
            $stokSebelum = $produk->stok;
            $produk->decrement('stok', $original->qty_diterima);
            $stokSesudah = $produk->fresh()->stok;

            // 5. Update detail totals dan hitung status penerimaan (otomatis cek stok_virtual untuk selisih_kurang)
            $detail->decrement('total_qty_diterima', $original->qty_diterima);
            $this->calculateAndSetStatus($detail->fresh());

            // 6. Create audit trail
            RiwayatStok::create([
                'jenis_item' => 'produk',
                'id_item' => $produk->id,
                'jenis_pergerakan' => 'keluar',
                'jumlah' => -1 * $original->qty_diterima,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'keterangan' => "REVERSAL penerimaan #{$original->id}: {$catatan}",
                'referensi_type' => 'penerimaan_hasil_produksi',
                'referensi_id' => $penerimaan->id,
            ]);

            return $penerimaan;
        });
    }

    /**
     * Calculate status penerimaan berdasarkan total_qty_diterima vs estimasi_pcs.
     * NOTE: Method ini TIDAK cek stok_virtual. Untuk status selisih_kurang otomatis,
     * gunakan calculateAndSetStatus() yang cek apakah masih ada stok ready yang belum diserahkan.
     */
    public function calculateStatus(DetailPerintahProduksi $detail): string
    {
        $estimasi = $detail->estimasi_pcs;
        $diterima = (int) $detail->total_qty_diterima;

        if ($diterima == 0) {
            return 'belum_diterima';
        }

        if ($diterima == $estimasi) {
            return 'sesuai';
        }

        if ($diterima > $estimasi) {
            return 'selisih_lebih';
        }

        // diterima < estimasi: tentukan apakah sebagian atau selisih_kurang
        // Cek apakah masih ada stok ready yang belum diserahkan ke admin
        $unreceivedReady = StokVirtual::where('id_detail_perintah', $detail->id)
            ->whereColumn('total_selesai', '>', 'total_dikeluarkan')
            ->exists();

        // Jika masih ada stok ready belum diserahkan → sebagian (masih bisa diterima lagi)
        // Jika semua sudah diserahkan tapi diterima < estimasi → selisih_kurang (final)
        return $unreceivedReady ? 'sebagian' : 'selisih_kurang';
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
    public function getAvailableKaryawanForDetail(DetailPerintahProduksi $detail): Collection
    {
        // Opsi A: ready_to_transfer = total_selesai - total_dikeluarkan (untuk SEMUA tahap).
        // Filter status_barang='Ready' dihapus karena tidak menjamin masih ada yang belum diserahkan.
        return StokVirtual::where('id_detail_perintah', $detail->id)
            ->where('peran', 'finishing')
            ->whereColumn('total_selesai', '>', 'total_dikeluarkan') // Masih ada yang belum diserahkan
            ->with('karyawan')
            ->get()
            ->map(function ($stok) {
                return [
                    'karyawan_id' => $stok->id_karyawan,
                    'karyawan_name' => $stok->karyawan->name,
                    'qty_ready' => (int) $stok->total_selesai - (int) $stok->total_dikeluarkan, // Sisa yang belum diserahkan
                    'qty_selesai' => (int) $stok->total_selesai, // Total yang sudah selesai dikerjakan
                    'qty_diserahkan' => (int) $stok->total_dikeluarkan, // Total yang sudah diserahkan
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

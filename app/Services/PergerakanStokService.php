<?php

namespace App\Services;

use App\Models\BahanBaku;
use App\Models\RiwayatStok;
use App\Models\PergerakanStokBahanBaku;
use App\Models\DetailPergerakanStokBahanBaku;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

class PergerakanStokService
{
    /**
     * Get stok masuk dengan filter dan pagination
     */
    public function getStokMasukPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = PergerakanStokBahanBaku::with(['detailPergerakanStok.bahanBaku', 'supplier', 'user'])
            ->where('jenis_pergerakan', 'masuk')
            ->latest('tanggal')
            ->latest('id');

        // Filter pencarian
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nomor_transaksi', 'like', "%{$filters['search']}%")
                  ->orWhereHas('detailPergerakanStok.bahanBaku', function ($q2) use ($filters) {
                      $q2->where('nama_bahan', 'like', "%{$filters['search']}%")
                         ->orWhere('kode_bahan', 'like', "%{$filters['search']}%");
                  })->orWhereHas('supplier', function ($q2) use ($filters) {
                      $q2->where('nama_supplier', 'like', "%{$filters['search']}%");
                  });
            });
        }

        // Filter kategori bahan baku
        if (!empty($filters['kategori'])) {
            $query->whereHas('detailPergerakanStok.bahanBaku', function ($q) use ($filters) {
                $q->where('kategori', $filters['kategori']);
            });
        }

        // Filter tanggal
        if (!empty($filters['tanggal_mulai'])) {
            $query->whereDate('tanggal', '>=', $filters['tanggal_mulai']);
        }
        if (!empty($filters['tanggal_akhir'])) {
            $query->whereDate('tanggal', '<=', $filters['tanggal_akhir']);
        }

        return $query->paginate($perPage, ['*'], 'page_masuk')->withQueryString();
    }

    /**
     * Get stok keluar dengan filter dan pagination
     */
    public function getStokKeluarPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = PergerakanStokBahanBaku::with(['detailPergerakanStok.bahanBaku', 'user'])
            ->where('jenis_pergerakan', 'keluar')
            ->latest('tanggal')
            ->latest('id');

        // Filter pencarian
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nomor_transaksi', 'like', "%{$filters['search']}%")
                  ->orWhereHas('detailPergerakanStok.bahanBaku', function ($q2) use ($filters) {
                      $q2->where('nama_bahan', 'like', "%{$filters['search']}%")
                         ->orWhere('kode_bahan', 'like', "%{$filters['search']}%");
                  })->orWhere('penerima', 'like', "%{$filters['search']}%");
            });
        }

        // Filter kategori bahan baku
        if (!empty($filters['kategori'])) {
            $query->whereHas('detailPergerakanStok.bahanBaku', function ($q) use ($filters) {
                $q->where('kategori', $filters['kategori']);
            });
        }

        // Filter tanggal
        if (!empty($filters['tanggal_mulai'])) {
            $query->whereDate('tanggal', '>=', $filters['tanggal_mulai']);
        }
        if (!empty($filters['tanggal_akhir'])) {
            $query->whereDate('tanggal', '<=', $filters['tanggal_akhir']);
        }

        return $query->paginate($perPage, ['*'], 'page_keluar')->withQueryString();
    }

    /**
     * Get data untuk form
     */
    public function getFormData(): array
    {
        return [
            'bahanBakuAll' => BahanBaku::where('is_aktif', true)->orderBy('nama_bahan')->get(),
            'bahanBakuNonKain' => BahanBaku::where('is_aktif', true)
                ->where('kategori', '!=', 'kain')
                ->orderBy('nama_bahan')
                ->get(),
            'suppliers' => Supplier::where('is_aktif', true)->orderBy('nama_supplier')->get(),
            'karyawan' => User::whereNotIn('role', ['admin', 'owner'])->orderBy('name')->get(),
        ];
    }
    
    /**
     * Simpan transaksi pergerakan stok bulk.
     */
    public function store(array $data, ?UploadedFile $buktiFile = null): PergerakanStokBahanBaku
    {
        return DB::transaction(function () use ($data, $buktiFile) {
            $jenis = $data['jenis_pergerakan'];
            $nomorTransaksi = $this->generateNomorTransaksi($jenis);

            // Upload bukti
            $buktiPath = null;
            if ($buktiFile) {
                $subFolder = $jenis === 'masuk' ? 'bukti-pembelian' : 'bukti-pengeluaran';
                $buktiPath = $buktiFile->store("img/{$subFolder}", 'public');
            }

            // Simpan header
            $transaksi = PergerakanStokBahanBaku::create([
                'nomor_transaksi' => $nomorTransaksi,
                'jenis_pergerakan' => $jenis,
                'tanggal' => $data['tanggal'],
                'supplier_id' => $jenis === 'masuk' ? ($data['supplier_id'] ?? null) : null,
                'penerima' => $jenis === 'keluar' ? ($data['penerima'] ?? null) : null,
                'bukti' => $buktiPath,
                'catatan' => $data['catatan'] ?? null,
                'user_id' => auth()->id(),
            ]);

            // Simpan detail & update stok
            foreach ($data['items'] as $item) {
                $bahanBaku = BahanBaku::findOrFail($item['bahan_baku_id']);
                $qty = (int) $item['quantity'];

                $stokSebelum = (int) $bahanBaku->stok;
                $stokSesudah = $jenis === 'masuk' ? ($stokSebelum + $qty) : ($stokSebelum - $qty);

                // Buat detail
                $transaksi->detailPergerakanStok()->create([
                    'bahan_baku_id' => $bahanBaku->id,
                    'jumlah' => $qty,
                ]);

                // Catat riwayat stok
                $keteranganRiwayat = $jenis === 'masuk' 
                    ? ($data['catatan'] ?? 'Stok masuk bulk')
                    : 'Diberikan ke: ' . $transaksi->penerima . (($data['catatan'] ?? null) ? ' | ' . $data['catatan'] : '');

                RiwayatStok::create([
                    'jenis_item' => 'bahan_baku',
                    'id_item' => $bahanBaku->id,
                    'jenis_pergerakan' => $jenis,
                    'jumlah' => $qty,
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSesudah,
                    'user_id' => auth()->id(),
                    'keterangan' => $keteranganRiwayat,
                    'referencing_type' => PergerakanStokBahanBaku::class, // sesuaikan penamaan model lama jika referensi_type / referencing_type
                    'referensi_type' => PergerakanStokBahanBaku::class,
                    'referensi_id' => $transaksi->id,
                ]);

                // Update stok bahan baku
                BahanBaku::withoutEvents(function () use ($bahanBaku, $stokSesudah) {
                    $bahanBaku->stok = $stokSesudah;
                    $bahanBaku->save();
                });
            }

            return $transaksi;
        });
    }

    /**
     * Hapus (batal) transaksi pergerakan stok.
     */
    public function destroy(PergerakanStokBahanBaku $transaksi): void
    {
        DB::transaction(function () use ($transaksi) {
            $jenis = $transaksi->jenis_pergerakan;

            // Kembalikan stok untuk setiap item detail
            foreach ($transaksi->detailPergerakanStok as $detail) {
                $bahanBaku = $detail->bahanBaku;
                $stokSebelum = (int) $bahanBaku->stok;
                
                $stokSesudah = $jenis === 'masuk' 
                    ? max(0, $stokSebelum - $detail->jumlah)
                    : ($stokSebelum + $detail->jumlah);

                // Catat penyesuaian pembatalan
                RiwayatStok::create([
                    'jenis_item' => 'bahan_baku',
                    'id_item' => $bahanBaku->id,
                    'jenis_pergerakan' => 'penyesuaian',
                    'jumlah' => $detail->jumlah,
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSesudah,
                    'user_id' => auth()->id(),
                    'keterangan' => 'Pembatalan ' . ($jenis === 'masuk' ? 'stok masuk' : 'stok keluar') . ' bulk: ' . $transaksi->nomor_transaksi,
                ]);

                // Kembalikan stok bahan baku
                BahanBaku::withoutEvents(function () use ($bahanBaku, $stokSesudah) {
                    $bahanBaku->stok = $stokSesudah;
                    $bahanBaku->save();
                });
            }

            // Hapus file bukti
            if ($transaksi->bukti) {
                Storage::disk('public')->delete($transaksi->bukti);
            }

            $transaksi->delete();
        });
    }

    /**
     * Generate nomor transaksi otomatis
     */
    public function generateNomorTransaksi(string $jenis): string
    {
        $prefix = $jenis === 'masuk' ? 'TRX-BM' : 'TRX-BK';
        $date = now()->format('Ymd');
        
        $last = PergerakanStokBahanBaku::withTrashed()
            ->where('jenis_pergerakan', $jenis)
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastNum = (int) substr($last->nomor_transaksi, -4);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        do {
            $candidate = "{$prefix}-{$date}-" . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
            $exists = PergerakanStokBahanBaku::withTrashed()
                ->where('nomor_transaksi', $candidate)
                ->exists();
            if ($exists) {
                $nextNum++;
            }
        } while ($exists);

        return $candidate;
    }
}

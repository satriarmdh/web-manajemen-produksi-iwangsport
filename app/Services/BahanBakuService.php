<?php

namespace App\Services;

use App\Models\BahanBaku;
use App\Services\NotificationService;
use Illuminate\Pagination\LengthAwarePaginator;

class BahanBakuService
{
    public function getAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = BahanBaku::with('riwayatStokTerakhir');

        // 1. Fitur Pencarian (Berdasarkan Nama atau Kode)
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('nama_bahan', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('kode_bahan', 'like', '%' . $filters['search'] . '%');
            });
        }

        // 2. Fitur Filter Kategori
        if (!empty($filters['kategori'])) {
            $query->where('kategori', $filters['kategori']);
        }

        // 3. Fitur Filter Status Stok
        if (!empty($filters['stok'])) {
            if ($filters['stok'] === 'tersedia') {
                $query->where('stok', '>', 0);
            } elseif ($filters['stok'] === 'habis') {
                $query->where('stok', 0);
            } elseif ($filters['stok'] === 'menipis') {
                $query->menipis();
            }
        }

        // 4. Fitur Sorting (Pengurutan)
        if (!empty($filters['sort'])) {
            match ($filters['sort']) {
                'nama_asc'  => $query->orderBy('nama_bahan', 'asc'),
                'nama_desc' => $query->orderBy('nama_bahan', 'desc'),
                'stok_desc' => $query->orderBy('stok', 'desc'),
                'stok_asc'  => $query->orderBy('stok', 'asc'),
                'terlama'   => $query->orderBy('created_at', 'asc'),
                default     => $query->orderBy('created_at', 'desc'), // terbaru
            };
        } else {
            // Default urutan jika tidak memilih sort
            $query->latest(); 
        }

        // withQueryString() agar saat pindah halaman paginasi, filter & search tidak hilang
        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Mengumpulkan prediksi nomor selanjutnya untuk semua kategori
     */
    public function getNextNumbers(): array
    {
        return [
            'kain'      => $this->generateKodeBahan('kain'),
            'benang'    => $this->generateKodeBahan('benang'),
            'kancing'   => $this->generateKodeBahan('kancing'),
            'resleting' => $this->generateKodeBahan('resleting'),
            'aksesoris' => $this->generateKodeBahan('aksesoris'),
        ];
    }

    /**
     * Logika utama untuk generate kode bahan
     */
    public function generateKodeBahan(string $kategori): string
    {
        $prefixes = [
            'kain'            => 'KAIN',
            'bahan_pendukung' => 'BPD',
        ];

        $prefix = $prefixes[$kategori] ?? 'BPD';

        $lastData = BahanBaku::where('kategori', $kategori)->latest('id')->first();

        if (!$lastData) {
            return $prefix . '-001';
        }

        $lastCode = $lastData->kode_bahan;
        $lastNumber = (int) substr($lastCode, strpos($lastCode, '-') + 1);
        $newNumber = $lastNumber + 1;

        return $prefix . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Statistik ringkas bahan baku
     */
    public function getStats(): array
    {
        return [
            'total_items' => BahanBaku::count(),
            'stok_menipis' => BahanBaku::menipis()->count(),
            'stok_habis' => BahanBaku::where('stok', '=', 0)->count(),
            'total_kategori' => BahanBaku::distinct('kategori')->count('kategori'),
        ];
    }

    /**
     * Proses simpan data (termasuk inject kode_bahan otomatis)
     */
    public function store(array $data): BahanBaku
    {
        $data['kode_bahan'] = $this->generateKodeBahan($data['kategori']);
        
        return BahanBaku::create($data);
    }

    /**
     * Proses update data
     * Regenerate kode jika kategori berubah
     */
    public function update(BahanBaku $bahanBaku, array $data): bool
    {
        // Cek apakah kategori berubah
        if (isset($data['kategori']) && $data['kategori'] !== $bahanBaku->kategori) {
            $data['kode_bahan'] = $this->generateKodeBahan($data['kategori']);
        }

        // Cek perubahan stok manual -> notifikasi owner
        $stokLama = $bahanBaku->stok;
        $stokBaru = $data['stok'] ?? $stokLama;
        $result = $bahanBaku->update($data);

        if ($result && (int) $stokBaru !== (int) $stokLama) {
            app(NotificationService::class)->stokManualChanged(
                $bahanBaku->nama_bahan . ' (' . $bahanBaku->warna . ')',
                $stokLama,
                $stokBaru,
                'bahan_baku'
            );

            // Cek stok kritis setelah update
            $bahanBaku->refresh();
            if ($bahanBaku->isStokMenipis()) {
                app(NotificationService::class)->stokKritis(
                    $bahanBaku->nama_bahan . ' (' . $bahanBaku->warna . ')',
                    $bahanBaku->stok,
                    $bahanBaku->stok_minimal,
                    'bahan_baku'
                );
            }
        }

        return $result;
    }

    /**
     * Proses hapus data
     */
    public function delete(BahanBaku $bahanBaku): bool
    {
        return $bahanBaku->delete();
    }
}
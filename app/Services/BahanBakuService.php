<?php

namespace App\Services;

use App\Models\BahanBaku;

class BahanBakuService
{
    /**
     * Mengambil semua data bahan baku
     */
    // public function getAll()
    // {
    //     return BahanBaku::latest()->get();
    // }

    public function getAllPaginated(array $filters = [], int $perPage = 10)
    {
        $query = BahanBaku::query();

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
            'kain'      => 'KAIN',
            'benang'    => 'BNG',
            'kancing'   => 'KNC',
            'resleting' => 'RSL',
            'aksesoris' => 'AKS',
        ];

        $prefix = $prefixes[$kategori] ?? 'BRG';

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
     * Proses simpan data (termasuk inject kode_bahan otomatis)
     */
    public function store(array $data): BahanBaku
    {
        $data['kode_bahan'] = $this->generateKodeBahan($data['kategori']);
        
        return BahanBaku::create($data);
    }

    /**
     * Proses update data
     */
    public function update(BahanBaku $bahanBaku, array $data): bool
    {
        return $bahanBaku->update($data);
    }

    /**
     * Proses hapus data
     */
    public function delete(BahanBaku $bahanBaku): bool
    {
        return $bahanBaku->delete();
    }
}
<?php

namespace App\Services;

use App\Models\EstimasiProduksi;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class EstimasiProduksiService
{
    /**
     * Get estimasi produksi dengan filter, search, dan sorting
     */
    public function getEstimasi(array $filters = []): LengthAwarePaginator
    {
        $query = EstimasiProduksi::with(['produk', 'bahanBaku']);

        // Search berdasarkan nama produk atau nama bahan baku
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->whereHas('produk', function (Builder $sub) use ($search) {
                    $sub->where('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produk', 'like', "%{$search}%");
                })->orWhereHas('bahanBaku', function (Builder $sub) use ($search) {
                    $sub->where('nama_bahan', 'like', "%{$search}%")
                        ->orWhere('kode_bahan', 'like', "%{$search}%");
                });
            });
        }

        // Filter berdasarkan status aktif
        if (!empty($filters['status'])) {
            $isActive = $filters['status'] === 'aktif';
            $query->where('is_aktif', $isActive);
        }

        // Sorting
        if (!empty($filters['sort'])) {
            match ($filters['sort']) {
                'nama_produk_asc' => $query->whereHas('produk')->orderBy(
                    Produk::select('nama_produk')
                        ->whereColumn('produk.id', 'estimasi_produksi.produk_id'),
                    'asc'
                ),
                'nama_produk_desc' => $query->whereHas('produk')->orderBy(
                    Produk::select('nama_produk')
                        ->whereColumn('produk.id', 'estimasi_produksi.produk_id'),
                    'desc'
                ),
                'newest' => $query->latest(),
                'oldest' => $query->oldest(),
                default => $query->latest()
            };
        } else {
            $query->latest();
        }

        return $query->paginate(10)->withQueryString();
    }

    /**
     * Create estimasi produksi baru
     */
    public function create(array $data): EstimasiProduksi
    {
        // Set default is_aktif jika tidak ada
        $data['is_aktif'] = $data['is_aktif'] ?? true;
        $data['toleransi_minus'] = $data['toleransi_minus'] ?? 0;

        return EstimasiProduksi::create($data);
    }

    /**
     * Update estimasi produksi
     */
    public function update(EstimasiProduksi $estimasi, array $data): EstimasiProduksi
    {
        $estimasi->update($data);
        return $estimasi->fresh();
    }

    /**
     * Delete estimasi produksi
     */
    public function delete(EstimasiProduksi $estimasi): bool
    {
        return $estimasi->delete();
    }
}

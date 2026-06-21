<?php

namespace App\Services;

use App\Models\StandardBaselineProduksi;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class StandardBaselineProduksiService
{
    /**
     * Get standard baseline produksi dengan filter, search, dan sorting
     */
    public function getEstimasi(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = StandardBaselineProduksi::with(['produk', 'bahanBaku']);

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
                        ->whereColumn('produk.id', 'standard_baseline_produksi.produk_id'),
                    'asc'
                ),
                'nama_produk_desc' => $query->whereHas('produk')->orderBy(
                    Produk::select('nama_produk')
                        ->whereColumn('produk.id', 'standard_baseline_produksi.produk_id'),
                    'desc'
                ),
                'newest' => $query->latest(),
                'oldest' => $query->oldest(),
                default => $query->latest()
            };
        } else {
            $query->latest();
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Create standard baseline produksi baru
     */
    public function create(array $data): StandardBaselineProduksi
    {
        // Set default is_aktif jika tidak ada
        $data['is_aktif'] = $data['is_aktif'] ?? true;
        $data['toleransi_minus'] = $data['toleransi_minus'] ?? 0;

        return StandardBaselineProduksi::create($data);
    }

    /**
     * Update standard baseline produksi
     */
    public function update(StandardBaselineProduksi $estimasi, array $data): StandardBaselineProduksi
    {
        $estimasi->update($data);
        return $estimasi->fresh();
    }

    /**
     * Delete standard baseline produksi
     */
    public function delete(StandardBaselineProduksi $estimasi): bool
    {
        return $estimasi->delete();
    }
}

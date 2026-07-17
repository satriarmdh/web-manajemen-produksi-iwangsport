<?php

namespace App\Services;

use App\Models\Pelanggan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class PelangganService
{
    /**
     * Get pelanggan with filters, search, and pagination
     */
    public function getPelanggan(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Pelanggan::query();

        // Search by nama_pelanggan, email, or no_telp
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('no_telp', 'like', "%{$search}%")
                    ->orWhere('kode_pelanggan', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if (isset($filters['status'])) {
            $query->where('is_aktif', $filters['status'] === 'aktif');
        }

        // Sort
        $sort = $filters['sort'] ?? 'terbaru';
        $query->when($sort === 'nama_asc', fn($q) => $q->orderBy('nama_pelanggan', 'asc'))
            ->when($sort === 'nama_desc', fn($q) => $q->orderBy('nama_pelanggan', 'desc'))
            ->when($sort === 'terbaru', fn($q) => $q->latest())
            ->when($sort === 'terlama', fn($q) => $q->oldest());

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Generate next kode_pelanggan
     */
    public function generateKode(): string
    {
        $lastPelanggan = Pelanggan::withTrashed()
            ->where('kode_pelanggan', 'like', 'PLG-%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastPelanggan) {
            return 'PLG-001';
        }

        $lastNumber = (int) substr($lastPelanggan->kode_pelanggan, 4);
        $nextNumber = $lastNumber + 1;

        return 'PLG-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Create new pelanggan
     */
    public function create(array $data): Pelanggan
    {
        $data['kode_pelanggan'] = $this->generateKode();
        $data['is_aktif'] = $data['is_aktif'] ?? true;

        return Pelanggan::create($data);
    }

    /**
     * Update pelanggan
     */
    public function update(Pelanggan $pelanggan, array $data): Pelanggan
    {
        $pelanggan->update($data);
        return $pelanggan->fresh();
    }

    /**
     * Soft delete pelanggan
     */
    public function delete(Pelanggan $pelanggan): bool
    {
        return $pelanggan->delete();
    }

    /**
     * Load pelanggan detail relations
     */
    public function loadForDetail(Pelanggan $pelanggan): Pelanggan
    {
        return $pelanggan;
    }
}

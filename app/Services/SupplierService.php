<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierService
{
    /**
     * Generate kode supplier otomatis (format: SUP-001, SUP-002, dst)
     */
    public function generateKode(): string
    {
        $lastSupplier = Supplier::withTrashed()
            ->where('kode_supplier', 'like', 'SUP-%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastSupplier) {
            return 'SUP-001';
        }

        $lastNumber = (int) substr($lastSupplier->kode_supplier, 4);
        $nextNumber = $lastNumber + 1;

        return 'SUP-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get suppliers dengan filter, search, dan sorting
     */
    public function getSuppliers(array $filters = []): LengthAwarePaginator
    {
        $query = Supplier::query();

        // Search berdasarkan nama_supplier atau kode_supplier
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('nama_supplier', 'like', "%{$search}%")
                  ->orWhere('kode_supplier', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan kategori (JSON where)
        if (!empty($filters['kategori'])) {
            $query->whereJsonContains('kategori', $filters['kategori']);
        }

        // Filter berdasarkan status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Sorting
        if (!empty($filters['sort'])) {
            match ($filters['sort']) {
                'nama_asc' => $query->orderBy('nama_supplier', 'asc'),
                'nama_desc' => $query->orderBy('nama_supplier', 'desc'),
                'newest' => $query->orderBy('created_at', 'desc'),
                'oldest' => $query->orderBy('created_at', 'asc'),
                default => $query->latest()
            };
        } else {
            $query->latest();
        }

        return $query->paginate(10)->withQueryString();
    }

    /**
     * Create supplier baru
     */
    public function create(array $data): Supplier
    {
        // Generate kode jika belum ada
        if (empty($data['kode_supplier'])) {
            $data['kode_supplier'] = $this->generateKode();
        }

        // Pastikan kategori adalah array
        if (isset($data['kategori']) && !is_array($data['kategori'])) {
            $data['kategori'] = [$data['kategori']];
        }

        // Set default status
        $data['status'] = $data['status'] ?? 'aktif';

        return Supplier::create($data);
    }

    /**
     * Update supplier
     */
    public function update(Supplier $supplier, array $data): Supplier
    {
        // Pastikan kategori adalah array
        if (isset($data['kategori']) && !is_array($data['kategori'])) {
            $data['kategori'] = [$data['kategori']];
        }

        $supplier->update($data);

        return $supplier->fresh();
    }

    /**
     * Delete supplier (soft delete)
     */
    public function delete(Supplier $supplier): bool
    {
        return $supplier->delete();
    }
}

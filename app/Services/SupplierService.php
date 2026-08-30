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
    public function getSuppliers(array $filters = [], int $perPage = 10): LengthAwarePaginator
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

        // Filter berdasarkan status (is_aktif)
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'aktif') {
                $query->where('is_aktif', true);
            } elseif ($filters['status'] === 'nonaktif') {
                $query->where('is_aktif', false);
            }
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

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Create supplier baru
     */
    public function create(array $data): Supplier
    {
        // Pastikan kategori adalah array
        if (isset($data['kategori']) && !is_array($data['kategori'])) {
            $data['kategori'] = [$data['kategori']];
        }

        // Set default is_aktif
        $data['is_aktif'] = $data['is_aktif'] ?? true;

        // Cek apakah ada data supplier trashed dengan email / kode_supplier yang sama
        $trashed = Supplier::onlyTrashed()
            ->where(function ($q) use ($data) {
                if (!empty($data['email'])) {
                    $q->where('email', $data['email']);
                }
                if (!empty($data['kode_supplier'])) {
                    $q->orWhere('kode_supplier', $data['kode_supplier']);
                }
            })
            ->first();

        if ($trashed) {
            $trashed->restore();
            if (empty($data['kode_supplier'])) {
                $data['kode_supplier'] = $trashed->kode_supplier ?: $this->generateKode();
            }
            $trashed->update($data);
            return $trashed->fresh();
        }

        // Generate kode jika belum ada
        if (empty($data['kode_supplier'])) {
            $data['kode_supplier'] = $this->generateKode();
        }

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

        if (!empty($data['email']) || !empty($data['kode_supplier'])) {
            $trashed = Supplier::onlyTrashed()
                ->where('id', '!=', $supplier->id)
                ->where(function ($q) use ($data) {
                    if (!empty($data['email'])) {
                        $q->where('email', $data['email']);
                    }
                    if (!empty($data['kode_supplier'])) {
                        $q->orWhere('kode_supplier', $data['kode_supplier']);
                    }
                })
                ->first();

            if ($trashed) {
                $trashed->forceDelete();
            }
        }

        $supplier->update($data);

        return $supplier->fresh();
    }

    /**
     * Load supplier detail relations
     */
    public function loadForDetail(Supplier $supplier): Supplier
    {
        return $supplier;
    }

    /**
     * Delete supplier (soft delete)
     */
    public function delete(Supplier $supplier): bool
    {
        return $supplier->delete();
    }
}

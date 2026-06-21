<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

class UserManagementService
{
    /**
     * Get users dengan filter, search, sorting, dan pagination
     */
    public function getUsersPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = User::query();

        // Pencarian berdasarkan nama atau email
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Filter berdasarkan role
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        // Sorting
        if (!empty($filters['sort'])) {
            match ($filters['sort']) {
                'nama_asc' => $query->orderBy('name', 'asc'),
                'nama_desc' => $query->orderBy('name', 'desc'),
                'terlama' => $query->orderBy('created_at', 'asc'),
                default => $query->orderBy('created_at', 'desc'),
            };
        } else {
            $query->latest();
        }

        return $query->paginate($perPage)->withQueryString();
    }
    
    /**
     * Menyimpan pengguna baru beserta enkripsi password
     */
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        
        return User::create($data);
    }

    /**
     * Memperbarui data pengguna
     */
    public function updateUser(User $user, array $data): bool
    {
        return $user->update($data);
    }

    /**
     * Menghapus data pengguna
     */
    public function deleteUser(User $user): ?bool
    {
        return $user->delete();
    }
}
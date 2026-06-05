<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;

class UserManagementService
{
    /**
     * Mengambil semua data pengguna terbaru
     */
    public function getAllUsers(): Collection
    {
        return User::latest()->get();
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
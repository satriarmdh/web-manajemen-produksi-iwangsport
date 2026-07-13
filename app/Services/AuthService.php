<?php

namespace App\Services;

use App\Models\User;

class AuthService
{
    public function updateLoginMetadata(User $user): void
    {
        $user->update([
            'online_status' => true,
            'last_seen' => now(),
        ]);
    }

    public function getRedirectPath(string $role): string
    {
        return match ($role) {
            'admin'     => '/admin/dashboard',
            'owner'     => '/owner/dashboard',
            'potong'    => '/produksi/dashboard',
            'jahit'     => '/produksi/dashboard',
            'finishing' => '/produksi/dashboard',
            default     => '/login',
        };
    }

    public function updateLogoutMetadata(User $user): void
    {
        $user->forceFill([
            'online_status' => false,
            'last_seen' => now(),
        ])->saveQuietly();
    }
}
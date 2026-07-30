<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'jenis_kelamin',
        'no_hp',
        'alamat',
        'online_status',
        'last_seen',
    ];

    protected $casts = [
        'last_seen' => 'datetime',
        'online_status' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Accessor untuk mengecek status online secara otomatis.
     * Ditambahkan type-hint (mixed $value) dan return type (: bool) agar editor tidak warning.
     */
    public function getOnlineStatusAttribute(mixed $value): bool
    {
        // 1. Jika di DB memang sudah 0 (karena klik logout), langsung kembalikan false
        if ((bool) $value === false) {
            return false;
        }

        // 2. Jika di DB nilainya 1, tapi tidak ada aktivitas lebih dari 5 menit,
        // paksa jadi false otomatis (mengatasi user yang asal close browser)
        if ($this->last_seen && $this->last_seen->diffInMinutes(now()) > 5) {
            return false;
        }

        // 3. Jika baru aktif kurang dari 5 menit, berarti benar-benar Online
        return true;
    }
}

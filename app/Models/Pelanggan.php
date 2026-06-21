<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelanggan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pelanggan';

    protected $fillable = [
        'kode_pelanggan',
        'nama_pelanggan',
        'no_telp',
        'email',
        'alamat',
        'keterangan',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];
}

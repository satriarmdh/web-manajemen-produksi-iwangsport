<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode_supplier',
        'nama_supplier',
        'kategori',
        'kontak',
        'email',
        'alamat',
        'catatan',
        'status',
    ];

    protected $casts = [
        'kategori' => 'array',
    ];
}

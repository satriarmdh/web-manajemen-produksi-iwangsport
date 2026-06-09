<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produk';

    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'ukuran',
        'warna',
        'harga_satuan',
        'satuan',
        'stok',
    ];

    protected $casts = [
        'harga_satuan' => 'integer',
        'stok' => 'integer',
    ];
}

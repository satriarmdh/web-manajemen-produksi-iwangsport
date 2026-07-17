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
        'is_aktif',
    ];

    protected $casts = [
        'harga_satuan' => 'integer',
        'stok' => 'integer',
        'is_aktif' => 'boolean',
    ];

    /**
     * Relasi ke standard baseline produksi
     */
    public function standardBaselineProduksi()
    {
        return $this->hasMany(StandardBaselineProduksi::class, 'produk_id');
    }

    protected static function booted()
    {
        static::deleted(function ($produk) {
            $produk->standardBaselineProduksi()->delete();
        });
    }
}
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
        'stok_minimal',
        'is_aktif',
    ];

    protected $casts = [
        'harga_satuan' => 'integer',
        'stok' => 'integer',
        'stok_minimal' => 'integer',
        'is_aktif' => 'boolean',
    ];

    /**
     * Cek apakah stok di bawah ambang stok minimal.
     */
    public function isStokMenipis(): bool
    {
        return $this->stok > 0 && $this->stok_minimal > 0 && $this->stok < $this->stok_minimal;
    }

    public function riwayatStokTerakhir()
    {
        return $this->morphOne(RiwayatStok::class, 'item', 'jenis_item', 'id_item')->latestOfMany();
    }

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
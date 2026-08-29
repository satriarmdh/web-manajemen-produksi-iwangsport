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
     * Cek apakah stok di bawah/sama dengan ambang stok minimal.
     */
    public function isStokMenipis(): bool
    {
        if ($this->stok <= 0) {
            return false;
        }

        $threshold = ($this->stok_minimal && $this->stok_minimal > 0) ? $this->stok_minimal : 10;

        return $this->stok <= $threshold;
    }

    /**
     * Scope query untuk filter stok menipis.
     */
    public function scopeMenipis($query)
    {
        return $query->where('stok', '>', 0)
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereNotNull('stok_minimal')
                       ->where('stok_minimal', '>', 0)
                       ->whereColumn('stok', '<=', 'stok_minimal');
                })->orWhere(function ($q2) {
                    $q2->where(function ($q3) {
                        $q3->whereNull('stok_minimal')->orWhere('stok_minimal', '<=', 0);
                    })->where('stok', '<=', 10);
                });
            });
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
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BahanBaku extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bahan_baku';
    
    protected $fillable = [
        'kode_bahan',
        'nama_bahan',
        'warna',
        'kategori',
        'satuan',
        'stok',
        'stok_minimal',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
        'stok' => 'integer',
        'stok_minimal' => 'integer',
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
        return $this->hasMany(StandardBaselineProduksi::class, 'bahan_baku_id');
    }

    protected static function booted()
    {
        static::deleted(function ($bahanBaku) {
            $bahanBaku->standardBaselineProduksi()->delete();
        });
    }
}

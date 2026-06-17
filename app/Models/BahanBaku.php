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
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    /**
     * Relasi ke standard baseline produksi
     */
    public function standardBaselineProduksi()
    {
        return $this->hasMany(StandardBaselineProduksi::class, 'bahan_baku_id');
    }
}

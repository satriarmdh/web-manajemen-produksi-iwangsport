<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StandardBaselineProduksi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'standard_baseline_produksi';

    protected $fillable = [
        'produk_id',
        'bahan_baku_id',
        'pcs_per_roll',
        'toleransi_minus',
        'keterangan',
        'is_aktif',
    ];

    protected $casts = [
        'pcs_per_roll' => 'integer',
        'toleransi_minus' => 'integer',
        'is_aktif' => 'boolean',
    ];

    /**
     * Relasi ke produk
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    /**
     * Relasi ke bahan baku
     */
    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }

    /**
     * Hitung batas bawah hasil produksi (pcs_per_roll - toleransi_minus)
     */
    public function getRangeBawahAttribute(): int
    {
        return max(0, $this->pcs_per_roll - $this->toleransi_minus);
    }
}

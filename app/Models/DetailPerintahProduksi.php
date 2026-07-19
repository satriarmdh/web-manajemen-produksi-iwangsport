<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPerintahProduksi extends Model
{
    use HasFactory;
    protected $table = 'detail_perintah_produksi';

    protected $fillable = [
        'perintah_produksi_id',
        'produk_id',
        'bahan_baku_id',
        'qty_roll_pakai',
        'estimasi_pcs',
        'toleransi_minus',
        'qty_pcs_potong',
        'status_validasi_potong',
        'alasan',
        'total_qty_diterima',
        'status_penerimaan',
    ];

    protected $casts = [
        'qty_roll_pakai' => 'integer',
        'estimasi_pcs' => 'integer',
        'toleransi_minus' => 'integer',
        'qty_pcs_potong' => 'integer',
        'total_qty_diterima' => 'integer',
    ];

    public function perintahProduksi(): BelongsTo
    {
        return $this->belongsTo(PerintahProduksi::class);
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class);
    }

    public function mutasiProduksi(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MutasiProduksi::class, 'id_detail_perintah');
    }

    public function penerimaanHasilProduksi(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PenerimaanHasilProduksi::class, 'perintah_produksi_detail_id');
    }
}

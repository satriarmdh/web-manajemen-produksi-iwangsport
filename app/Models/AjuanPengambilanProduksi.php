<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AjuanPengambilanProduksi extends Model
{
    protected $table = 'ajuan_pengambilan_produksi';

    protected $fillable = [
        'id_perintah', 'id_detail_perintah', 'id_produk', 'dari_karyawan_id', 'ke_karyawan_id',
        'dari_tahapan', 'ke_tahapan', 'qty_ajuan', 'status', 'catatan_pengaju', 'catatan_respon',
        'tgl_ajuan', 'tgl_respon',
    ];

    protected $casts = [
        'tgl_ajuan' => 'datetime',
        'tgl_respon' => 'datetime',
    ];

    public function perintahProduksi(): BelongsTo { return $this->belongsTo(PerintahProduksi::class, 'id_perintah'); }
    public function detailPerintahProduksi(): BelongsTo { return $this->belongsTo(DetailPerintahProduksi::class, 'id_detail_perintah'); }
    public function produk(): BelongsTo { return $this->belongsTo(Produk::class, 'id_produk'); }
    public function dariKaryawan(): BelongsTo { return $this->belongsTo(User::class, 'dari_karyawan_id'); }
    public function keKaryawan(): BelongsTo { return $this->belongsTo(User::class, 'ke_karyawan_id'); }
    public function mutasiProduksi(): HasOne { return $this->hasOne(MutasiProduksi::class, 'id_ajuan'); }
}

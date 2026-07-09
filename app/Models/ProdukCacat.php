<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProdukCacat extends Model
{
    protected $table = 'produk_cacat';

    protected $fillable = [
        'id_perintah',
        'id_detail_perintah',
        'id_karyawan',
        'id_produk',
        'tahapan',
        'qty_reject',
        'keterangan',
        'tgl_lapor',
    ];

    protected $casts = [
        'tgl_lapor' => 'datetime',
    ];

    public function perintahProduksi(): BelongsTo
    {
        return $this->belongsTo(PerintahProduksi::class, 'id_perintah');
    }

    public function detailPerintahProduksi(): BelongsTo
    {
        return $this->belongsTo(DetailPerintahProduksi::class, 'id_detail_perintah');
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_karyawan');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}

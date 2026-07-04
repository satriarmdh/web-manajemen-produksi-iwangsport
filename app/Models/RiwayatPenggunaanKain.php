<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPenggunaanKain extends Model
{
    protected $table = 'riwayat_penggunaan_kain';

    protected $fillable = [
        'perintah_produksi_id',
        'detail_perintah_produksi_id',
        'bahan_baku_id',
        'jumlah_pakai',
        'keterangan',
    ];

    protected $casts = [
        'jumlah_pakai' => 'decimal:2',
    ];

    public function perintahProduksi(): BelongsTo
    {
        return $this->belongsTo(PerintahProduksi::class);
    }

    public function detailPerintahProduksi(): BelongsTo
    {
        return $this->belongsTo(DetailPerintahProduksi::class);
    }

    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RiwayatStok extends Model
{
    protected $table = 'riwayat_stok';

    protected $fillable = [
        'jenis_item',
        'id_item',
        'jenis_pergerakan',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'user_id',
        'keterangan',
        'referensi_type',
        'referensi_id',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'stok_sebelum' => 'integer',
        'stok_sesudah' => 'integer',
    ];

    /**
     * Relasi polymorphic ke item (BahanBaku atau Produk)
     */
    public function item(): MorphTo
    {
        return $this->morphTo('item', 'jenis_item', 'id_item');
    }

    /**
     * Relasi polymorphic ke referensi transaksi (StokMasukBahanBaku atau StokKeluarBahanBaku)
     */
    public function referensi(): MorphTo
    {
        return $this->morphTo('referensi', 'referensi_type', 'referensi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

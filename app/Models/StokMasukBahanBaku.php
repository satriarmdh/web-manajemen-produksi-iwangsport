<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class StokMasukBahanBaku extends Model
{
    use SoftDeletes;

    protected $table = 'stok_masuk_bahan_baku';

    protected $fillable = [
        'bahan_baku_id',
        'jumlah',
        'supplier_id',
        'bukti_pembelian',
        'user_id',
        'catatan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    /**
     * Relasi ke bahan baku
     */
    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class);
    }

    /**
     * Relasi ke supplier
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relasi ke user yang melakukan input
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke riwayat stok (polymorphic)
     */
    public function riwayatStok(): MorphOne
    {
        return $this->morphOne(RiwayatStok::class, 'referensi');
    }
}

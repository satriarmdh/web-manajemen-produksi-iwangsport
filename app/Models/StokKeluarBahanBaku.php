<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class StokKeluarBahanBaku extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stok_keluar_bahan_baku';

    protected $fillable = [
        'bahan_baku_id',
        'jumlah',
        'penerima',
        'bukti_pengeluaran',
        'keterangan',
        'user_id',
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

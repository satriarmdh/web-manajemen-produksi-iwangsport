<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PerintahProduksi extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'perintah_produksi';

    protected $fillable = [
        'nomor_wo',
        'tgl_mulai',
        'tgl_selesai',
        'status_produksi',
        'user_id',
        'approved_by',
        'approved_at',
        'alasan_penolakan',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailPerintahProduksi::class);
    }

    public function riwayatPenggunaanKain(): HasMany
    {
        return $this->hasMany(RiwayatPenggunaanKain::class);
    }

    public function stokVirtual(): HasMany
    {
        return $this->hasMany(StokVirtual::class, 'id_perintah');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penjualan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penjualan';

    protected $fillable = [
        'nomor_invoice',
        'pelanggan_id',
        'tanggal',
        'total_item',
        'total_harga',
        'catatan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_item' => 'integer',
        'total_harga' => 'integer',
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detailPenjualan(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    public function pembayaranPenjualan(): HasMany
    {
        return $this->hasMany(PembayaranPenjualan::class, 'penjualan_id')->orderBy('tanggal_bayar', 'asc')->orderBy('id', 'asc');
    }

    public function getTotalDibayarAttribute(): float
    {
        return (float) $this->pembayaranPenjualan->sum('jumlah_bayar');
    }

    public function getSisaPembayaranAttribute(): float
    {
        return max(0, (float) $this->total_harga - $this->total_dibayar);
    }

    public function getStatusPembayaranAttribute(): string
    {
        $paid = $this->total_dibayar;
        $total = (float) $this->total_harga;

        if ($paid >= $total && $total > 0) {
            return 'lunas';
        }

        if ($paid > 0) {
            return 'sebagian';
        }

        return 'belum_bayar';
    }
}

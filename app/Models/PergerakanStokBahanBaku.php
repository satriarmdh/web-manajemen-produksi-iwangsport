<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PergerakanStokBahanBaku extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pergerakan_stok_bahan_baku';

    protected $fillable = [
        'nomor_transaksi',
        'jenis_pergerakan',
        'tanggal',
        'supplier_id',
        'penerima',
        'bukti',
        'catatan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detailPergerakanStok()
    {
        return $this->hasMany(DetailPergerakanStokBahanBaku::class, 'pergerakan_stok_bahan_baku_id');
    }
}

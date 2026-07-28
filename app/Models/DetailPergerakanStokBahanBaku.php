<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPergerakanStokBahanBaku extends Model
{
    use HasFactory;

    protected $table = 'detail_pergerakan_stok_bahan_baku';

    protected $fillable = [
        'pergerakan_stok_bahan_baku_id',
        'bahan_baku_id',
        'jumlah',
    ];

    public function pergerakanStok()
    {
        return $this->belongsTo(PergerakanStokBahanBaku::class, 'pergerakan_stok_bahan_baku_id');
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }
}

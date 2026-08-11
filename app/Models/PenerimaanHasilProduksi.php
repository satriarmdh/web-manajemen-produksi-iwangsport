<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenerimaanHasilProduksi extends Model
{
    use HasFactory;

    protected $table = 'penerimaan_hasil_produksi';

    protected $fillable = [
        'perintah_produksi_detail_id',
        'admin_user_id',
        'dari_karyawan_id',
        'tanggal_terima',
        'qty_diterima',
        'jenis_penerimaan',
        'catatan',
        'bukti_foto',
    ];

    protected $casts = [
        'tanggal_terima' => 'date',
        'qty_diterima' => 'integer',
    ];

    // Relationships

    public function detail(): BelongsTo
    {
        return $this->belongsTo(DetailPerintahProduksi::class, 'perintah_produksi_detail_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function dariKaryawan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dari_karyawan_id');
    }

    // Scopes

    public function scopeForDetail($query, int $detailId)
    {
        return $query->where('perintah_produksi_detail_id', $detailId);
    }

    public function scopeFromKaryawan($query, int $karyawanId)
    {
        return $query->where('dari_karyawan_id', $karyawanId);
    }
}

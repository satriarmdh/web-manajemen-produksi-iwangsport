<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutasiProduksi extends Model
{
    protected $table = 'mutasi_produksi';

    protected $fillable = [
        'id_ajuan', 'id_perintah', 'id_detail_perintah', 'id_produk', 'dari_karyawan_id', 'ke_karyawan_id',
        'dari_tahapan', 'ke_tahapan', 'qty_pindah', 'tgl_transaksi', 'bukti_foto',
    ];

    protected $casts = [
        'tgl_transaksi' => 'datetime',
    ];

    public function ajuanPengambilanProduksi(): BelongsTo { return $this->belongsTo(AjuanPengambilanProduksi::class, 'id_ajuan'); }
    public function perintahProduksi(): BelongsTo { return $this->belongsTo(PerintahProduksi::class, 'id_perintah'); }
    public function detailPerintahProduksi(): BelongsTo { return $this->belongsTo(DetailPerintahProduksi::class, 'id_detail_perintah'); }
    public function produk(): BelongsTo { return $this->belongsTo(Produk::class, 'id_produk'); }
    public function dariKaryawan(): BelongsTo { return $this->belongsTo(User::class, 'dari_karyawan_id'); }
    public function keKaryawan(): BelongsTo { return $this->belongsTo(User::class, 'ke_karyawan_id'); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokVirtual extends Model
{
    protected $table = 'stok_virtual';

    protected $fillable = [
        'id_perintah',
        'id_detail_perintah',
        'id_karyawan',
        'id_produk',
        'peran',
        'qty_hold',
        'total_selesai',
        'total_dikeluarkan',
        'total_reject',
        'status_barang',
        'is_selesai',
        'status_validasi',
        'alasan',
    ];

    public function perintahProduksi(): BelongsTo
    {
        return $this->belongsTo(PerintahProduksi::class, 'id_perintah');
    }

    public function detailPerintahProduksi(): BelongsTo
    {
        return $this->belongsTo(DetailPerintahProduksi::class, 'id_detail_perintah');
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_karyawan');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    /**
     * Records produk cacat (reject) yang diinput karyawan ini
     * untuk perintah + produk + peran yang sama.
     * Diurutkan terbaru di atas.
     */
    public function produkCacat()
    {
        return $this->hasMany(ProdukCacat::class, 'id_karyawan', 'id_karyawan')
            ->where('id_perintah', $this->id_perintah)
            ->where('id_produk', $this->id_produk)
            ->orderBy('tgl_lapor', 'desc');
    }
}

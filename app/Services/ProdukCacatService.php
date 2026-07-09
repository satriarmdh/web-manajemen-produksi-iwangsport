<?php

namespace App\Services;

use App\Models\DetailPerintahProduksi;
use App\Models\ProdukCacat;
use App\Models\StokVirtual;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProdukCacatService
{
    public function store(array $data, User $user): ProdukCacat
    {
        return DB::transaction(function () use ($data, $user) {
            $detail = DetailPerintahProduksi::lockForUpdate()->findOrFail($data['detail_perintah_produksi_id']);
            $qtyReject = (int) $data['qty_reject'];

            $produkCacat = ProdukCacat::create([
                'id_perintah' => $detail->perintah_produksi_id,
                'id_detail_perintah' => $detail->id,
                'id_karyawan' => $user->id,
                'id_produk' => $detail->produk_id,
                'tahapan' => $user->role,
                'qty_reject' => $qtyReject,
                'keterangan' => $data['keterangan'],
                'tgl_lapor' => now(),
            ]);

            $stokVirtual = StokVirtual::firstOrNew([
                'id_detail_perintah' => $detail->id,
                'id_karyawan' => $user->id,
                'peran' => $user->role,
            ]);

            if (! $stokVirtual->exists) {
                $stokVirtual->fill([
                    'id_perintah' => $detail->perintah_produksi_id,
                    'id_produk' => $detail->produk_id,
                    'qty_hold' => 0,
                    'total_selesai' => 0,
                    'total_reject' => 0,
                    'status_barang' => 'Proses',
                    'is_selesai' => false,
                ]);
            }

            if (in_array($user->role, ['jahit', 'finishing'], true)) {
                $stokVirtual->qty_hold = max(0, ((int) $stokVirtual->qty_hold) - $qtyReject);
            }

            $stokVirtual->total_reject = ((int) $stokVirtual->total_reject) + $qtyReject;
            $stokVirtual->is_selesai = (bool) ($data['tandai_selesai'] ?? false) || (bool) $stokVirtual->is_selesai;
            $stokVirtual->save();

            return $produkCacat;
        });
    }
}

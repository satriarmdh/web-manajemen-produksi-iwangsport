<?php

namespace App\Services;

use App\Models\DetailPerintahProduksi;
use App\Models\StokVirtual;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InputHasilPekerjaanService
{
    public function store(array $data, User $user): void
    {
        DB::transaction(function () use ($data, $user) {
            if ($user->role === 'potong') {
                $this->storeHasilPotong($data, $user);
                return;
            }

            $this->storeHasilDariStokVirtual($data, $user);
        });
    }

    private function storeHasilPotong(array $data, User $user): void
    {
        $detail = DetailPerintahProduksi::with('perintahProduksi')
            ->lockForUpdate()
            ->findOrFail($data['detail_perintah_produksi_id']);

        $qtySelesai = (int) $data['qty_selesai'];
        $totalSetelahInput = ((int) $detail->qty_pcs_potong) + $qtySelesai;
        $totalReject = (int) (StokVirtual::where('id_detail_perintah', $detail->id)
            ->where('id_karyawan', $user->id)
            ->where('peran', $user->role)
            ->value('total_reject') ?? 0);
        $batasBawah = $detail->estimasi_pcs - $detail->toleransi_minus;
        $ditandaiSelesai = (bool) ($data['tandai_selesai'] ?? false);
        $statusValidasi = $ditandaiSelesai && ($totalSetelahInput + $totalReject) < $batasBawah ? 'flag' : 'normal';

        $detail->update([
            'qty_pcs_potong' => $totalSetelahInput,
            'status_validasi_potong' => $statusValidasi,
            'alasan' => $data['alasan'] ?? null,
        ]);

        $perintahProduksi = $detail->perintahProduksi;
        if ($perintahProduksi->status_produksi === 'disetujui') {
            $perintahProduksi->update(['status_produksi' => 'dalam_produksi']);
        }

        $stokVirtual = StokVirtual::firstOrNew([
            'id_detail_perintah' => $detail->id,
            'id_karyawan' => $user->id,
            'peran' => 'potong',
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

        $stokVirtual->qty_hold = ((int) $stokVirtual->qty_hold) + $qtySelesai;
        $stokVirtual->total_selesai = ((int) $stokVirtual->total_selesai) + $qtySelesai;
        $stokVirtual->status_barang = 'Ready';
        $stokVirtual->is_selesai = $ditandaiSelesai || (bool) $stokVirtual->is_selesai;
        $stokVirtual->save();
    }

    private function storeHasilDariStokVirtual(array $data, User $user): void
    {
        $stokVirtual = StokVirtual::lockForUpdate()->findOrFail($data['stok_virtual_id']);
        $qtySelesai = (int) $data['qty_selesai'];

        $target = (int) $stokVirtual->qty_hold + (int) $stokVirtual->total_selesai + (int) $stokVirtual->total_reject;
        $progressSetelahInput = (int) $stokVirtual->total_selesai + (int) $stokVirtual->total_reject + $qtySelesai;
        $ditandaiSelesai = (bool) ($data['tandai_selesai'] ?? false);

        $stokVirtual->qty_hold = max(0, ((int) $stokVirtual->qty_hold) - $qtySelesai);
        $stokVirtual->total_selesai = ((int) $stokVirtual->total_selesai) + $qtySelesai;
        $stokVirtual->status_barang = 'Ready';
        $stokVirtual->is_selesai = $ditandaiSelesai || (bool) $stokVirtual->is_selesai;
        if ($ditandaiSelesai) {
            $stokVirtual->status_validasi = $progressSetelahInput < $target ? 'flag' : 'normal';
            $stokVirtual->alasan = $progressSetelahInput < $target ? ($data['alasan'] ?? null) : null;
        }
        $stokVirtual->save();
    }
}

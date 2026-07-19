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

    /**
     * Input hasil potong.
     *
     * Opsi A semantics (konsisten untuk semua tahap):
     * - qty_hold       = WIP input (barang dipegang yang BELUM dikerjakan).
     *                    Untuk potong, bahan baku TIDAK ditrack via stok_virtual
     *                    (pemakaian kain sudah dicatat di riwayat_penggunaan_kain),
     *                    sehingga qty_hold selalu 0 untuk peran potong.
     * - total_selesai  = barang sudah dikerjakan (output).
     * - total_dikeluarkan = barang sudah diserahkan ke tahap berikutnya.
     * - ready_to_transfer = total_selesai - total_dikeluarkan (untuk SEMUA tahap).
     */
    private function storeHasilPotong(array $data, User $user): void
    {
        $detail = DetailPerintahProduksi::with('perintahProduksi')
            ->lockForUpdate()
            ->findOrFail($data['detail_perintah_produksi_id']);

        $qtySelesai = (int) $data['qty_selesai'];
        $qtyReject = (int) ($data['qty_reject'] ?? 0);
        $totalSetelahInput = ((int) $detail->qty_pcs_potong) + $qtySelesai;

        if ($qtyReject > 0) {
            \App\Models\ProdukCacat::create([
                'id_perintah' => $detail->perintah_produksi_id,
                'id_detail_perintah' => $detail->id,
                'id_karyawan' => $user->id,
                'id_produk' => $detail->produk_id,
                'tahapan' => 'potong',
                'qty_reject' => $qtyReject,
                'keterangan' => $data['keterangan_cacat'] ?? '',
                'tgl_lapor' => now(),
            ]);
        }

        $totalReject = (int) (StokVirtual::where('id_detail_perintah', $detail->id)
            ->where('id_karyawan', $user->id)
            ->where('peran', $user->role)
            ->value('total_reject') ?? 0) + $qtyReject;

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
                'qty_hold' => 0, // WIP input selalu 0 untuk potong (bahan baku ditrack terpisah)
                'total_selesai' => 0,
                'total_dikeluarkan' => 0,
                'total_reject' => 0,
                'status_barang' => 'Proses',
                'is_selesai' => false,
            ]);
        }

        // total_selesai bertambah (output pekerjaan). qty_hold TIDAK berubah (tetap 0 untuk potong).
        $stokVirtual->total_selesai = ((int) $stokVirtual->total_selesai) + $qtySelesai;
        $stokVirtual->total_reject = ((int) $stokVirtual->total_reject) + $qtyReject;
        $stokVirtual->status_barang = 'Ready';
        $stokVirtual->is_selesai = $ditandaiSelesai || (bool) $stokVirtual->is_selesai;
        $stokVirtual->save();
    }

    private function storeHasilDariStokVirtual(array $data, User $user): void
    {
        $stokVirtual = StokVirtual::lockForUpdate()->findOrFail($data['stok_virtual_id']);
        $qtySelesai = (int) $data['qty_selesai'];
        $qtyReject = (int) ($data['qty_reject'] ?? 0);

        if ($qtyReject > 0) {
            \App\Models\ProdukCacat::create([
                'id_perintah' => $stokVirtual->id_perintah,
                'id_detail_perintah' => $stokVirtual->id_detail_perintah,
                'id_karyawan' => $user->id,
                'id_produk' => $stokVirtual->id_produk,
                'tahapan' => $user->role,
                'qty_reject' => $qtyReject,
                'keterangan' => $data['keterangan_cacat'] ?? '',
                'tgl_lapor' => now(),
            ]);
        }

        $target = (int) $stokVirtual->qty_hold + (int) $stokVirtual->total_selesai + (int) $stokVirtual->total_reject;
        $progressSetelahInput = (int) $stokVirtual->total_selesai + (int) $stokVirtual->total_reject + $qtySelesai + $qtyReject;
        $ditandaiSelesai = (bool) ($data['tandai_selesai'] ?? false);

        $stokVirtual->qty_hold = max(0, ((int) $stokVirtual->qty_hold) - $qtySelesai - $qtyReject);
        $stokVirtual->total_selesai = ((int) $stokVirtual->total_selesai) + $qtySelesai;
        $stokVirtual->total_reject = ((int) $stokVirtual->total_reject) + $qtyReject;
        $stokVirtual->status_barang = 'Ready';
        $stokVirtual->is_selesai = $ditandaiSelesai || (bool) $stokVirtual->is_selesai;
        if ($ditandaiSelesai) {
            $stokVirtual->status_validasi = $progressSetelahInput < $target ? 'flag' : 'normal';
            $stokVirtual->alasan = $progressSetelahInput < $target ? ($data['alasan'] ?? null) : null;
        }
        $stokVirtual->save();
    }
}

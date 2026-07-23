<?php

namespace App\Services;

use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\RiwayatStok;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PenjualanService
{
    /**
     * Create a new penjualan transaction.
     * Decreases product stock and creates audit trail.
     */
    public function create(array $data, User $admin): Penjualan
    {
        return DB::transaction(function () use ($data, $admin) {
            // 1. Generate nomor invoice
            $nomorInvoice = $this->generateNomorInvoice($data['tanggal']);

            // 2. Calculate totals from items
            $totalItem = 0;
            $totalHarga = 0;
            $itemsData = [];

            foreach ($data['items'] as $item) {
                $produk = Produk::lockForUpdate()->findOrFail($item['produk_id']);
                $hargaSatuan = $produk->harga_satuan;
                $subtotal = $hargaSatuan * $item['qty'];

                $itemsData[] = [
                    'produk_id' => $item['produk_id'],
                    'qty' => $item['qty'],
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                ];

                $totalItem += $item['qty'];
                $totalHarga += $subtotal;
            }

            // 3. Create penjualan
            $penjualan = Penjualan::create([
                'nomor_invoice' => $nomorInvoice,
                'pelanggan_id' => $data['pelanggan_id'],
                'tanggal' => $data['tanggal'],
                'total_item' => $totalItem,
                'total_harga' => $totalHarga,
                'catatan' => $data['catatan'] ?? null,
                'user_id' => $admin->id,
            ]);

            // 4. Create detail + decrease stock + audit trail
            foreach ($itemsData as $itemData) {
                DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    ...$itemData,
                ]);

                $produk = Produk::find($itemData['produk_id']);
                $stokSebelum = $produk->stok;
                $produk->decrement('stok', $itemData['qty']);
                $stokSesudah = $produk->fresh()->stok;

                RiwayatStok::create([
                    'jenis_item' => 'produk',
                    'id_item' => $produk->id,
                    'jenis_pergerakan' => 'keluar',
                    'jumlah' => $itemData['qty'],
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSesudah,
                    'user_id' => $admin->id,
                    'keterangan' => "Penjualan {$nomorInvoice} ke {$penjualan->pelanggan->nama_pelanggan}",
                    'referensi_type' => 'penjualan',
                    'referensi_id' => $penjualan->id,
                ]);
            }

            return $penjualan->load(['pelanggan', 'detailPenjualan.produk']);
        });
    }

    /**
     * Update penjualan: reverse old stock, apply new stock.
     */
    public function update(Penjualan $penjualan, array $data, User $admin): Penjualan
    {
        return DB::transaction(function () use ($penjualan, $data, $admin) {
            // 1. Reverse old items: return stock + delete old details
            foreach ($penjualan->detailPenjualan as $oldDetail) {
                $produk = Produk::lockForUpdate()->find($oldDetail->produk_id);
                $stokSebelum = $produk->stok;
                $produk->increment('stok', $oldDetail->qty);
                $stokSesudah = $produk->fresh()->stok;

                RiwayatStok::create([
                    'jenis_item' => 'produk',
                    'id_item' => $produk->id,
                    'jenis_pergerakan' => 'masuk',
                    'jumlah' => $oldDetail->qty,
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSesudah,
                    'user_id' => $admin->id,
                    'keterangan' => "REVERSAL edit penjualan {$penjualan->nomor_invoice}",
                    'referensi_type' => 'penjualan',
                    'referensi_id' => $penjualan->id,
                ]);

                $oldDetail->delete();
            }

            // 2. Apply new items: decrease stock + create new details
            $totalItem = 0;
            $totalHarga = 0;

            foreach ($data['items'] as $item) {
                $produk = Produk::lockForUpdate()->findOrFail($item['produk_id']);
                $hargaSatuan = $produk->harga_satuan;
                $subtotal = $hargaSatuan * $item['qty'];

                DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'produk_id' => $item['produk_id'],
                    'qty' => $item['qty'],
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                ]);

                $stokSebelum = $produk->stok;
                $produk->decrement('stok', $item['qty']);
                $stokSesudah = $produk->fresh()->stok;

                RiwayatStok::create([
                    'jenis_item' => 'produk',
                    'id_item' => $produk->id,
                    'jenis_pergerakan' => 'keluar',
                    'jumlah' => $item['qty'],
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSesudah,
                    'user_id' => $admin->id,
                    'keterangan' => "Penjualan {$penjualan->nomor_invoice} (EDIT) ke {$penjualan->pelanggan->nama_pelanggan}",
                    'referensi_type' => 'penjualan',
                    'referensi_id' => $penjualan->id,
                ]);

                $totalItem += $item['qty'];
                $totalHarga += $subtotal;
            }

            // 3. Update penjualan
            $penjualan->update([
                'pelanggan_id' => $data['pelanggan_id'],
                'tanggal' => $data['tanggal'],
                'total_item' => $totalItem,
                'total_harga' => $totalHarga,
                'catatan' => $data['catatan'] ?? null,
            ]);

            return $penjualan->fresh(['pelanggan', 'detailPenjualan.produk']);
        });
    }

    /**
     * Soft delete penjualan: return stock to products.
     */
    public function delete(Penjualan $penjualan, User $admin): void
    {
        DB::transaction(function () use ($penjualan, $admin) {
            foreach ($penjualan->detailPenjualan as $detail) {
                $produk = Produk::lockForUpdate()->find($detail->produk_id);
                $stokSebelum = $produk->stok;
                $produk->increment('stok', $detail->qty);
                $stokSesudah = $produk->fresh()->stok;

                RiwayatStok::create([
                    'jenis_item' => 'produk',
                    'id_item' => $produk->id,
                    'jenis_pergerakan' => 'masuk',
                    'jumlah' => $detail->qty,
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSesudah,
                    'user_id' => $admin->id,
                    'keterangan' => "REVERSAL hapus penjualan {$penjualan->nomor_invoice}",
                    'referensi_type' => 'penjualan',
                    'referensi_id' => $penjualan->id,
                ]);
            }

            $penjualan->delete();
        });
    }

    /**
     * Generate nomor invoice: INV-YYYYMMDD-NNNN
     */
    private function generateNomorInvoice(string $tanggal): string
    {
        $prefix = 'INV-' . date('Ymd', strtotime($tanggal)) . '-';

        $latest = Penjualan::withTrashed()
            ->where('nomor_invoice', 'like', $prefix . '%')
            ->orderByDesc('nomor_invoice')
            ->first();

        $nextNum = 1;
        if ($latest) {
            $lastNum = (int) substr($latest->nomor_invoice, -4);
            $nextNum = $lastNum + 1;
        }

        return $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }
}

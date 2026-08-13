<?php

namespace App\Services;

use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Pelanggan;
use App\Models\RiwayatStok;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

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
                Produk::withoutEvents(function () use ($produk, $itemData) {
                    $produk->decrement('stok', $itemData['qty']);
                });
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

            // 5. Create initial payment if provided
            if (isset($data['jumlah_bayar']) && (float) $data['jumlah_bayar'] > 0) {
                $buktiPath = null;
                if (isset($data['bukti_pembayaran']) && $data['bukti_pembayaran'] instanceof \Illuminate\Http\UploadedFile) {
                    $buktiPath = $data['bukti_pembayaran']->store('bukti-penjualan', 'public');
                }

                \App\Models\PembayaranPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'user_id' => $admin->id,
                    'tanggal_bayar' => $data['tanggal'],
                    'jumlah_bayar' => $data['jumlah_bayar'],
                    'metode_pembayaran' => $data['metode_pembayaran'] ?? 'tunai',
                    'catatan' => $data['catatan_pembayaran'] ?? 'Pembayaran awal',
                    'bukti_pembayaran' => $buktiPath,
                ]);
            }

            return $penjualan->load(['pelanggan', 'detailPenjualan.produk', 'pembayaranPenjualan']);
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
                Produk::withoutEvents(function () use ($produk, $oldDetail) {
                    $produk->increment('stok', $oldDetail->qty);
                });
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
                Produk::withoutEvents(function () use ($produk, $item) {
                    $produk->decrement('stok', $item['qty']);
                });
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
                Produk::withoutEvents(function () use ($produk, $detail) {
                    $produk->increment('stok', $detail->qty);
                });
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

    /**
     * Get paginated penjualan with filters.
     */
    public function getPenjualanPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Penjualan::with(['pelanggan', 'user'])
            ->latest('tanggal')
            ->latest('id');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nomor_invoice', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function ($q2) use ($search) {
                      $q2->where('nama_pelanggan', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['tanggal_mulai'])) {
            $query->whereDate('tanggal', '>=', $filters['tanggal_mulai']);
        }

        if (!empty($filters['tanggal_akhir'])) {
            $query->whereDate('tanggal', '<=', $filters['tanggal_akhir']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get data for creating a new penjualan.
     */
    public function getCreateData(): array
    {
        return [
            'pelanggan' => Pelanggan::where('is_aktif', true)->orderBy('nama_pelanggan')->get(),
            'produk' => Produk::where('is_aktif', true)->where('stok', '>', 0)->orderBy('nama_produk')->get(),
        ];
    }

    /**
     * Get data for editing an existing penjualan.
     */
    public function getEditData(Penjualan $penjualan): array
    {
        $penjualan->load(['pelanggan', 'detailPenjualan.produk']);
        return [
            'penjualan' => $penjualan,
            'pelanggan' => Pelanggan::where('is_aktif', true)->orderBy('nama_pelanggan')->get(),
            'produk' => Produk::where('is_aktif', true)->orderBy('nama_produk')->get(),
        ];
    }

    /**
     * Tambah pembayaran / pelunasan baru.
     */
    public function tambahPembayaran(Penjualan $penjualan, array $data, User $admin): \App\Models\PembayaranPenjualan
    {
        return DB::transaction(function () use ($penjualan, $data, $admin) {
            $buktiPath = null;
            if (isset($data['bukti_pembayaran']) && $data['bukti_pembayaran'] instanceof \Illuminate\Http\UploadedFile) {
                $buktiPath = $data['bukti_pembayaran']->store('bukti-penjualan', 'public');
            }

            return \App\Models\PembayaranPenjualan::create([
                'penjualan_id' => $penjualan->id,
                'user_id' => $admin->id,
                'tanggal_bayar' => $data['tanggal_bayar'] ?? now(),
                'jumlah_bayar' => $data['jumlah_bayar'],
                'metode_pembayaran' => $data['metode_pembayaran'] ?? 'tunai',
                'catatan' => $data['catatan'] ?? null,
                'bukti_pembayaran' => $buktiPath,
            ]);
        });
    }

    /**
     * Hapus entri pembayaran.
     */
    public function hapusPembayaran(\App\Models\PembayaranPenjualan $pembayaran): void
    {
        DB::transaction(function () use ($pembayaran) {
            if ($pembayaran->bukti_pembayaran) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
            }
            $pembayaran->delete();
        });
    }
}

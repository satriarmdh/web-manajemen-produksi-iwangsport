<?php

namespace App\Services;

use App\Models\Produk;
use App\Services\NotificationService;

class ProdukService
{
    /**
     * Ambil semua data produk dengan pagination
     */
    public function getAllPaginated(array $filters = [], int $perPage = 10)
    {
        $query = Produk::with('riwayatStokTerakhir');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nama_produk', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('kode_produk', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['ukuran'])) {
            $query->where('ukuran', $filters['ukuran']);
        }

        if (!empty($filters['stok'])) {
            if ($filters['stok'] === 'tersedia') {
                $query->where('stok', '>', 0);
            } elseif ($filters['stok'] === 'habis') {
                $query->where('stok', 0);
            } elseif ($filters['stok'] === 'menipis') {
                $query->where('stok', '>', 0)->where('stok_minimal', '>', 0)->whereColumn('stok', '<', 'stok_minimal');
            }
        }

        // 4. Fitur Sorting (Pengurutan)
        if (!empty($filters['sort'])) {
            match ($filters['sort']) {
                'nama_asc'  => $query->orderBy('nama_produk', 'asc'),
                'nama_desc' => $query->orderBy('nama_produk', 'desc'),
                'stok_desc' => $query->orderBy('stok', 'desc'),
                'stok_asc'  => $query->orderBy('stok', 'asc'),
                'terlama'   => $query->orderBy('created_at', 'asc'),
                default     => $query->orderBy('created_at', 'desc'), // terbaru
            };
        } else {
            // Default urutan jika tidak memilih sort
            $query->latest(); 
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Generate kode produk otomatis (format: CLN-001, CLN-002, dst)
     */
    public function generateKodeProduk(): string
    {
        $lastData = Produk::withTrashed()
            ->where('kode_produk', 'like', 'CLN-%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastData) {
            return 'CLN-001';
        }

        $lastNumber = (int) substr($lastData->kode_produk, 4);
        $newNumber = $lastNumber + 1;

        return 'CLN-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Simpan produk baru (kode_produk auto-generate jika tidak dikirim)
     */
    public function store(array $data): Produk
    {
        if (empty($data['kode_produk'])) {
            $data['kode_produk'] = $this->generateKodeProduk();
        }

        return Produk::create($data);
    }

    /**
     * Update produk
     * Pencatatan riwayat stok ditangani oleh ProdukObserver
     */
    public function update(Produk $produk, array $data): bool
    {
        $stokLama = $produk->stok;
        $hargaLama = $produk->harga_satuan;
        $result = $produk->update($data);

        if ($result) {
            // Notifikasi perubahan harga -> owner
            $hargaBaru = $data['harga_satuan'] ?? $hargaLama;
            if ((int) $hargaBaru !== (int) $hargaLama) {
                app(NotificationService::class)->hargaProdukChanged(
                    $produk->nama_produk . ' (' . $produk->ukuran . ' - ' . $produk->warna . ')',
                    $hargaLama,
                    $hargaBaru
                );
            }

            // Notifikasi perubahan stok manual -> owner
            $stokBaru = $data['stok'] ?? $stokLama;
            if ((int) $stokBaru !== (int) $stokLama) {
                app(NotificationService::class)->stokManualChanged(
                    $produk->nama_produk . ' (' . $produk->ukuran . ' - ' . $produk->warna . ')',
                    $stokLama,
                    $stokBaru,
                    'produk'
                );

                $produk->refresh();
                if ($produk->isStokMenipis()) {
                    app(NotificationService::class)->stokKritis(
                        $produk->nama_produk . ' (' . $produk->ukuran . ' - ' . $produk->warna . ')',
                        $produk->stok,
                        $produk->stok_minimal,
                        'produk'
                    );
                }
            }
        }

        return $result;
    }

    /**
     * Hapus produk (soft delete)
     */
    public function delete(Produk $produk): bool
    {
        return $produk->delete();
    }
}

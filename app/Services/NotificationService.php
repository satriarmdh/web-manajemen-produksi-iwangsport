<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SystemNotification;

class NotificationService
{
    public const TYPE_STOK_KRITIS = 'stok_kritis';
    public const TYPE_WO_BARU = 'wo_baru';
    public const TYPE_WO_APPROVED = 'wo_approved';
    public const TYPE_WO_REJECTED = 'wo_rejected';
    public const TYPE_WO_SELESAI = 'wo_selesai';
    public const TYPE_WO_ASSIGNED = 'wo_assigned';
    public const TYPE_AJUAN_BARU = 'ajuan_baru';
    public const TYPE_AJUAN_APPROVED = 'ajuan_approved';
    public const TYPE_AJUAN_REJECTED = 'ajuan_rejected';
    public const TYPE_HARGA_CHANGED = 'harga_changed';
    public const TYPE_STOK_MANUAL = 'stok_manual';
    public const TYPE_STOK_READY_POTONG = 'stok_ready_potong';
    public const TYPE_STOK_READY_JAHIT = 'stok_ready_jahit';
    public const TYPE_STOK_READY_FINISHING = 'stok_ready_finishing';

    public function notifyRoles(array $roles, string $title, string $message, string $type, ?string $url = null): void
    {
        try {
            User::whereIn('role', $roles)->get()->each(function ($user) use ($title, $message, $type, $url) {
                $user->notify(new SystemNotification($title, $message, $type, $url));
            });
        } catch (\Throwable $e) {
            \Log::warning('NotificationService.notifyRoles failed: ' . $e->getMessage());
        }
    }

    public function notifyUser(User $user, string $title, string $message, string $type, ?string $url = null): void
    {
        try {
            $user->notify(new SystemNotification($title, $message, $type, $url));
        } catch (\Throwable $e) {
            \Log::warning('NotificationService.notifyUser failed: ' . $e->getMessage());
        }
    }

    public function stokKritis(string $itemName, int $stok, int $stokMinimal, string $tipe = 'bahan_baku'): void
    {
        $status = $stok == 0 ? 'habis' : 'menipis';
        $tipeLabel = $tipe === 'bahan_baku' ? 'Bahan Baku' : 'Produk';
        $this->notifyRoles(
            ['admin'],
            "Stok {$tipeLabel} {$status}",
            "{$itemName} stoknya {$status} ({$stok}/min {$stokMinimal})",
            self::TYPE_STOK_KRITIS,
            $tipe === 'bahan_baku' ? '/admin/bahan-baku' : '/admin/produk'
        );
        $this->notifyRoles(
            ['owner'],
            "Stok {$tipeLabel} {$status}",
            "{$itemName} stoknya {$status} ({$stok}/min {$stokMinimal})",
            self::TYPE_STOK_KRITIS,
            '/owner/inventori'
        );
    }

    /**
     * Admin kirim WO baru → owner perlu approve.
     * URL: /owner/perintah-produksi (pending list)
     */
    public function woBaru(string $nomorWo, string $adminName): void
    {
        $this->notifyRoles(
            ['owner'],
            'Perintah Produksi Baru',
            "Admin {$adminName} membuat perintah produksi {$nomorWo} dan menunggu persetujuan Anda.",
            self::TYPE_WO_BARU,
            '/owner/perintah-produksi'
        );
    }

    /**
     * Owner approve WO → admin + karyawan dapat notif.
     * Admin URL: /admin/perintah-produksi
     * Karyawan URL: /produksi/perintah-produksi (jika ada assigned karyawan)
     */
    public function woApproved(string $nomorWo, ?User $assignedKaryawan = null): void
    {
        $this->notifyRoles(
            ['admin'],
            'WO Disetujui',
            "Perintah produksi {$nomorWo} telah disetujui oleh Owner.",
            self::TYPE_WO_APPROVED,
            '/admin/perintah-produksi'
        );

        if ($assignedKaryawan) {
            $this->notifyUser(
                $assignedKaryawan,
                'WO Disetujui - Siap Dikerjakan',
                "Perintah produksi {$nomorWo} telah disetujui. Silakan cek pekerjaan Anda.",
                self::TYPE_WO_ASSIGNED,
                '/produksi/perintah-produksi'
            );
        }
    }

    /**
     * Owner reject WO → admin dapat notif.
     * URL: /admin/perintah-produksi
     */
    public function woRejected(string $nomorWo, string $alasan): void
    {
        $this->notifyRoles(
            ['admin'],
            'WO Ditolak',
            "Perintah produksi {$nomorWo} ditolak Owner. Alasan: {$alasan}",
            self::TYPE_WO_REJECTED,
            '/admin/perintah-produksi'
        );
    }

    /**
     * WO selesai (finishing) → admin + owner.
     * URL: /admin/perintah-produksi (admin), /owner/pantau-progres (owner)
     */
    public function woSelesai(string $nomorWo): void
    {
        $this->notifyRoles(
            ['admin'],
            'WO Selesai',
            "Perintah produksi {$nomorWo} telah selesai diproduksi.",
            self::TYPE_WO_SELESAI,
            '/admin/perintah-produksi'
        );
        $this->notifyRoles(
            ['owner'],
            'WO Selesai',
            "Perintah produksi {$nomorWo} telah selesai diproduksi.",
            self::TYPE_WO_SELESAI,
            '/owner/pantau-progres'
        );
    }

    /**
     * Karyawan buat ajuan pengambilan bahan baku → notif ke karyawan penerima (target).
     * URL: /produksi/ajuan-pengambilan (karena karyawan mengakses ajuan dari halaman produksi)
     *
     * @param string $pengajuName Nama karyawan yang mengajukan
     * @param string $targetRole Role karyawan tujuan (contoh: 'jahit', 'potong', 'finishing')
     */
    public function ajuanBaru(string $pengajuName, string $targetRole): void
    {
        $this->notifyRoles(
            [$targetRole],
            'Ajuan Pengambilan Baru',
            "{$pengajuName} mengajukan pengambilan bahan baku kepada Anda.",
            self::TYPE_AJUAN_BARU,
            '/produksi/ajuan-pengambilan'
        );
    }

    /**
     * Ajuan disetujui → notif ke karyawan pengaju dengan pesan detail produk, qty, dan approver.
     * URL: /produksi/ajuan-pengambilan
     */
    public function ajuanDisetujui(User $karyawan, string $namaProduk, int $qty, string $approverName): void
    {
        $this->notifyUser(
            $karyawan,
            'Ajuan Disetujui',
            "Ajuan Anda untuk produk {$namaProduk} dengan jumlah {$qty} pcs telah disetujui oleh {$approverName}.",
            self::TYPE_AJUAN_APPROVED,
            '/produksi/ajuan-pengambilan'
        );
    }

    /**
     * Ajuan ditolak → notif ke karyawan pengaju dengan pesan detail produk, qty, dan approver.
     * URL: /produksi/ajuan-pengambilan
     */
    public function ajuanDitolak(User $karyawan, string $namaProduk, int $qty, string $approverName, ?string $alasan = null): void
    {
        $message = "Ajuan Anda untuk produk {$namaProduk} dengan jumlah {$qty} pcs telah ditolak oleh {$approverName}.";
        if ($alasan) {
            $message .= " Catatan: {$alasan}";
        }

        $this->notifyUser(
            $karyawan,
            'Ajuan Ditolak',
            $message,
            self::TYPE_AJUAN_REJECTED,
            '/produksi/ajuan-pengambilan'
        );
    }

    /**
     * Potong input hasil selesai -> notif ke role jahit.
     */
    public function stokReadyPotong(string $namaProduk, int $qty): void
    {
        $this->notifyRoles(
            ['jahit'],
            'Stok Ready dari Potong',
            "Terdapat {$qty} pcs produk {$namaProduk} ready di tukang potong, siap untuk diambil.",
            self::TYPE_STOK_READY_POTONG,
            '/produksi/ajuan-pengambilan'
        );
    }

    /**
     * Jahit input hasil selesai -> notif ke role finishing.
     */
    public function stokReadyJahit(string $namaProduk, int $qty): void
    {
        $this->notifyRoles(
            ['finishing'],
            'Stok Ready dari Penjahit',
            "Terdapat {$qty} pcs produk {$namaProduk} ready di penjahit, siap untuk diambil.",
            self::TYPE_STOK_READY_JAHIT,
            '/produksi/ajuan-pengambilan'
        );
    }

    /**
     * Finishing input hasil selesai -> notif ke admin (routing langsung ke detail WO).
     */
    public function stokReadyFinishing(int $perintahProduksiId, string $namaProduk, int $qty): void
    {
        $this->notifyRoles(
            ['admin'],
            'Stok Ready di Finishing',
            "Terdapat {$qty} pcs produk {$namaProduk} ready di finishing, siap untuk diterima admin.",
            self::TYPE_STOK_READY_FINISHING,
            "/admin/perintah-produksi/{$perintahProduksiId}"
        );
    }

    /**
     * Harga produk diubah admin → owner.
     * URL: /owner/inventori (bukan /admin/produk)
     */
    public function hargaProdukChanged(string $namaProduk, int $hargaLama, int $hargaBaru): void
    {
        $this->notifyRoles(
            ['owner'],
            'Harga Produk Diubah',
            "{$namaProduk}: Rp " . number_format($hargaLama, 0, ',', '.') . " → Rp " . number_format($hargaBaru, 0, ',', '.'),
            self::TYPE_HARGA_CHANGED,
            '/owner/inventori'
        );
    }

    /**
     * Stok diubah manual oleh admin → owner.
     * URL: /owner/inventori (bukan /admin/...)
     */
    public function stokManualChanged(string $itemName, int $stokLama, int $stokBaru, string $tipe = 'bahan_baku'): void
    {
        $this->notifyRoles(
            ['owner'],
            'Stok Diubah Manual',
            "{$itemName}: stok {$stokLama} → {$stokBaru} (perubahan manual oleh admin)",
            self::TYPE_STOK_MANUAL,
            '/owner/inventori'
        );
    }
}

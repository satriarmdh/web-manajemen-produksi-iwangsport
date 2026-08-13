<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PerintahProduksi extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'perintah_produksi';

    protected $fillable = [
        'nomor_wo',
        'tgl_mulai',
        'tgl_selesai',
        'tgl_selesai_aktual',
        'status_produksi',
        'user_id',
        'approved_by',
        'approved_at',
        'alasan_penolakan',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'tgl_selesai_aktual' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Get structured deadline status info for badges and alerts.
     */
    public function getDeadlineInfo(): array
    {
        // 1. If WO is completed:
        if ($this->status_produksi === 'selesai') {
            $targetDate = $this->tgl_selesai ? \Carbon\Carbon::parse($this->tgl_selesai)->startOfDay() : null;
            $aktualDate = $this->tgl_selesai_aktual 
                ? \Carbon\Carbon::parse($this->tgl_selesai_aktual)->startOfDay() 
                : ($this->updated_at ? \Carbon\Carbon::parse($this->updated_at)->startOfDay() : null);

            if ($targetDate && $aktualDate && $aktualDate->gt($targetDate)) {
                $lateDays = (int) $targetDate->diffInDays($aktualDate);
                return [
                    'statusType' => 'late_completed',
                    'isLate' => true,
                    'diffDays' => $lateDays,
                    'label' => "Terlambat {$lateDays} Hari",
                    'badgeClass' => 'bg-amber-50 text-amber-800 border-amber-200',
                    'badgeClassFull' => 'bg-amber-50 text-amber-800 border-amber-200',
                ];
            }

            return [
                'statusType' => 'on_time',
                'isLate' => false,
                'diffDays' => 0,
                'label' => 'Tepat Waktu',
                'badgeClass' => 'bg-green-50 text-green-700 border-green-200',
                'badgeClassFull' => 'bg-green-50 text-green-700 border-green-200',
            ];
        }

        // 2. If WO is active (dalam_produksi, disetujui, or pending):
        if (! $this->tgl_selesai) {
            return [
                'statusType' => 'none',
                'isLate' => false,
                'diffDays' => 0,
                'label' => '-',
                'badgeClass' => 'bg-gray-50 text-gray-600 border-gray-100',
                'badgeClassFull' => 'bg-gray-50 text-gray-600 border-gray-100',
            ];
        }

        $today = \Carbon\Carbon::today();
        $targetDate = \Carbon\Carbon::parse($this->tgl_selesai)->startOfDay();

        $diffDays = (int) $today->diffInDays($targetDate, false);

        if ($diffDays < 0) {
            $overdueDays = abs($diffDays);
            return [
                'statusType' => 'overdue',
                'isLate' => true,
                'diffDays' => $overdueDays,
                'label' => "Terlambat {$overdueDays} Hari",
                'badgeClass' => 'bg-red-100 text-red-800 border-red-300 animate-pulse',
                'badgeClassFull' => 'bg-red-50 text-red-800 border-red-200',
            ];
        }

        if ($diffDays === 0) {
            return [
                'statusType' => 'today',
                'isLate' => false,
                'diffDays' => 0,
                'label' => 'Hari Ini Deadline',
                'badgeClass' => 'bg-red-50 text-red-700 border-red-200',
                'badgeClassFull' => 'bg-red-50 text-red-700 border-red-200',
            ];
        }

        if ($diffDays === 1) {
            return [
                'statusType' => 'h1',
                'isLate' => false,
                'diffDays' => 1,
                'label' => 'H-1 Deadline',
                'badgeClass' => 'bg-rose-50 text-rose-700 border-rose-200',
                'badgeClassFull' => 'bg-rose-50 text-rose-700 border-rose-200',
            ];
        }

        if ($diffDays >= 2 && $diffDays <= 3) {
            return [
                'statusType' => 'warning',
                'isLate' => false,
                'diffDays' => $diffDays,
                'label' => "H-{$diffDays} Deadline",
                'badgeClass' => 'bg-amber-50 text-amber-700 border-amber-200',
                'badgeClassFull' => 'bg-amber-50 text-amber-700 border-amber-200',
            ];
        }

        return [
            'statusType' => 'normal',
            'isLate' => false,
            'diffDays' => $diffDays,
            'label' => "Sisa {$diffDays} Hari",
            'badgeClass' => 'bg-gray-50 text-gray-600 border-gray-200',
            'badgeClassFull' => 'bg-gray-50 text-gray-600 border-gray-200',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailPerintahProduksi::class);
    }

    public function riwayatPenggunaanKain(): HasMany
    {
        return $this->hasMany(RiwayatPenggunaanKain::class);
    }

    public function stokVirtual(): HasMany
    {
        return $this->hasMany(StokVirtual::class, 'id_perintah');
    }
}

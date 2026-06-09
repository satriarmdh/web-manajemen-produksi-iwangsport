<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_type',
        'item_id',
        'movement_type',
        'quantity',
        'previous_stock',
        'new_stock',
        'reason',
        'user_id',
    ];

    /**
     * Relasi ke user yang melakukan perubahan stok
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper untuk mencatat pergerakan stok
     * 
     * @param string $itemType 'produk' atau 'bahan_baku'
     * @param int $itemId ID item
     * @param string $movementType 'in', 'out', atau 'adjustment'
     * @param int $quantity Jumlah perubahan (positif/negatif)
     * @param int $previousStock Stok sebelumnya
     * @param int $newStock Stok baru
     * @param string|null $reason Alasan perubahan
     * @param int|null $userId ID user yang melakukan
     */
    public static function record(
        string $itemType,
        int $itemId,
        string $movementType,
        int $quantity,
        int $previousStock,
        int $newStock,
        ?string $reason = null,
        ?int $userId = null
    ) {
        return self::create([
            'item_type' => $itemType,
            'item_id' => $itemId,
            'movement_type' => $movementType,
            'quantity' => $quantity,
            'previous_stock' => $previousStock,
            'new_stock' => $newStock,
            'reason' => $reason,
            'user_id' => $userId ?? auth()->id(),
        ]);
    }
}

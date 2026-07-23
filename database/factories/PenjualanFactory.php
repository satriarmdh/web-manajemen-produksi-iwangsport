<?php

namespace Database\Factories;

use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PenjualanFactory extends Factory
{
    protected $model = Penjualan::class;

    public function definition(): array
    {
        return [
            'nomor_invoice' => 'INV-' . date('Ymd') . '-' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'pelanggan_id' => Pelanggan::factory(),
            'tanggal' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'total_item' => 0,
            'total_harga' => 0,
            'catatan' => $this->faker->optional()->sentence(),
            'user_id' => User::factory()->create(['role' => 'admin'])->id,
        ];
    }
}

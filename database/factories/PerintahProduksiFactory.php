<?php

namespace Database\Factories;

use App\Models\PerintahProduksi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PerintahProduksiFactory extends Factory
{
    protected $model = PerintahProduksi::class;

    public function definition(): array
    {
        return [
            'nomor_wo' => 'PROD-' . date('Ymd') . '-' . str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'tgl_mulai' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'tgl_selesai' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
            'status_produksi' => 'pending',
            'user_id' => User::factory(),
            'approved_by' => null,
            'approved_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_produksi' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    public function disetujui(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_produksi' => 'disetujui',
            'approved_by' => User::factory()->create(['role' => 'owner'])->id,
            'approved_at' => now(),
        ]);
    }

    public function dalamProduksi(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_produksi' => 'dalam_produksi',
            'approved_by' => User::factory()->create(['role' => 'owner'])->id,
            'approved_at' => now()->subDays(2),
        ]);
    }

    public function selesai(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_produksi' => 'selesai',
            'tgl_selesai' => now(),
            'approved_by' => User::factory()->create(['role' => 'owner'])->id,
            'approved_at' => now()->subDays(5),
        ]);
    }

    public function ditolak(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_produksi' => 'ditolak',
            'approved_by' => User::factory()->create(['role' => 'owner'])->id,
            'approved_at' => now(),
        ]);
    }
}

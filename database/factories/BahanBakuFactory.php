<?php

namespace Database\Factories;

use App\Models\BahanBaku;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BahanBaku>
 */
class BahanBakuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_bahan' => 'BB-' . $this->faker->unique()->numerify('####'),
            'nama_bahan' => $this->faker->words(3, true),
            'warna' => $this->faker->randomElement(['hitam', 'navy', 'abu']),
            'kategori' => $this->faker->randomElement(['kain', 'benang', 'aksesoris']),
            'satuan' => $this->faker->randomElement(['roll', 'pcs', 'kg']),
            'stok' => $this->faker->numberBetween(0, 100),
        ];
    }
}

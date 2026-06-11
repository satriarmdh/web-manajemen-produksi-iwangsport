<?php

namespace Database\Factories;

use App\Models\EstimasiProduksi;
use App\Models\Produk;
use App\Models\BahanBaku;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EstimasiProduksi>
 */
class EstimasiProduksiFactory extends Factory
{
    protected $model = EstimasiProduksi::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'produk_id' => Produk::factory(),
            'bahan_baku_id' => BahanBaku::factory(['kategori' => 'kain']),
            'pcs_per_roll' => $this->faker->numberBetween(100, 200),
            'toleransi_minus' => $this->faker->numberBetween(0, 20),
            'keterangan' => $this->faker->optional()->sentence(),
            'is_aktif' => true,
        ];
    }

    /**
     * State untuk estimasi nonaktif
     */
    public function nonaktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_aktif' => false,
        ]);
    }

    /**
     * State dengan toleransi tinggi
     */
    public function toleransiTinggi(): static
    {
        return $this->state(fn (array $attributes) => [
            'toleransi_minus' => 30,
        ]);
    }
}

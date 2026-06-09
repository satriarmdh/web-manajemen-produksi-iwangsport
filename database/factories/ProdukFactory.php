<?php

namespace Database\Factories;

use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Produk>
 */
class ProdukFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_produk' => 'CLN-' . $this->faker->unique()->numerify('####'),
            'nama_produk' => $this->faker->randomElement([
                'Celana Basket',
                'Celana Training',
                'Celana Jogger',
                'Celana Sport',
            ]),
            'ukuran' => $this->faker->randomElement(['normal', 'jumbo']),
            'warna' => $this->faker->randomElement(['hitam', 'biru', 'abu', 'navy']),
            'harga_satuan' => $this->faker->numberBetween(25000, 150000),
            'satuan' => 'pcs',
            'stok' => $this->faker->numberBetween(0, 200),
        ];
    }
}

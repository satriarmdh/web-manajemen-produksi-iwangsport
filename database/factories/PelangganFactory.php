<?php

namespace Database\Factories;

use App\Models\Pelanggan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PelangganFactory extends Factory
{
    protected $model = Pelanggan::class;

    protected static int $counter = 0;

    public function definition(): array
    {
        static::$counter++;

        return [
            'kode_pelanggan' => 'PLG-' . str_pad(static::$counter, 3, '0', STR_PAD_LEFT),
            'nama_pelanggan' => fake()->name(),
            'no_telp' => fake()->numerify('08##########'),
            'email' => fake()->unique()->safeEmail(),
            'alamat' => fake()->address(),
            'keterangan' => fake()->optional()->sentence(),
            'is_aktif' => true,
        ];
    }

    public function nonaktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_aktif' => false,
        ]);
    }

    public static function resetCounter(): void
    {
        static::$counter = 0;
    }
}

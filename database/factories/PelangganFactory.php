<?php

namespace Database\Factories;

use App\Models\Pelanggan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PelangganFactory extends Factory
{
    protected $model = Pelanggan::class;

    public function definition(): array
    {
        $lastPelanggan = Pelanggan::withTrashed()
            ->where('kode_pelanggan', 'like', 'PLG-%')
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastPelanggan
            ? (int) substr($lastPelanggan->kode_pelanggan, 4) + 1
            : 1;

        return [
            'kode_pelanggan' => 'PLG-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT),
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
}

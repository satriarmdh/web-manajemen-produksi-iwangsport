<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'kode_supplier' => 'SUP-' . $this->faker->unique()->numerify('###'),
            'nama_supplier' => $this->faker->company(),
            'kategori'      => $this->faker->randomElements(['kain', 'bahan_pendukung'], rand(1, 2)),
            'kontak'        => '08' . $this->faker->numerify('##########'),
            'email'         => $this->faker->unique()->safeEmail(),
            'alamat'        => $this->faker->address(),
            'catatan'       => $this->faker->optional()->sentence(),
            'is_aktif'      => $this->faker->boolean(),
        ];
    }
}

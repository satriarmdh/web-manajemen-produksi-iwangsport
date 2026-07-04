<?php

namespace Database\Factories;

use App\Models\DetailPerintahProduksi;
use App\Models\PerintahProduksi;
use App\Models\Produk;
use App\Models\BahanBaku;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetailPerintahProduksiFactory extends Factory
{
    protected $model = DetailPerintahProduksi::class;

    public function definition(): array
    {
        return [
            'perintah_produksi_id' => PerintahProduksi::factory(),
            'produk_id' => Produk::factory(),
            'bahan_baku_id' => BahanBaku::factory(),
            'qty_roll_pakai' => $this->faker->numberBetween(1, 10),
            'estimasi_pcs' => $this->faker->numberBetween(100, 200),
            'toleransi_minus' => $this->faker->numberBetween(5, 20),
            'qty_pcs_potong' => null,
            'status_validasi_potong' => 'pending',
            'alasan' => null,
        ];
    }

    public function denganHasilPotong(): static
    {
        return $this->state(function (array $attributes) {
            $estimasi = $attributes['estimasi_pcs'] ?? 150;
            $toleransi = $attributes['toleransi_minus'] ?? 10;
            $qty = $this->faker->numberBetween($estimasi - $toleransi - 5, $estimasi + 10);
            
            $status = 'normal';
            $alasan = null;
            
            if ($qty < ($estimasi - $toleransi)) {
                $status = 'flag';
                $alasan = 'Kain cacat atau penyusutan berlebih';
            }
            
            return [
                'qty_pcs_potong' => $qty,
                'status_validasi_potong' => $status,
                'alasan' => $alasan,
            ];
        });
    }

    public function validasiNormal(): static
    {
        return $this->state(function (array $attributes) {
            $estimasi = $attributes['estimasi_pcs'] ?? 150;
            $toleransi = $attributes['toleransi_minus'] ?? 10;
            
            return [
                'qty_pcs_potong' => $estimasi - $toleransi + 5,
                'status_validasi_potong' => 'normal',
                'alasan' => null,
            ];
        });
    }

    public function validasiFlag(): static
    {
        return $this->state(function (array $attributes) {
            $estimasi = $attributes['estimasi_pcs'] ?? 150;
            $toleransi = $attributes['toleransi_minus'] ?? 10;
            
            return [
                'qty_pcs_potong' => $estimasi - $toleransi - 10,
                'status_validasi_potong' => 'flag',
                'alasan' => 'Kain cacat atau penyusutan berlebih',
            ];
        });
    }
}

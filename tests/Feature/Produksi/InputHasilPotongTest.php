<?php

namespace Tests\Feature\Produksi;

use App\Models\DetailPerintahProduksi;
use App\Models\PerintahProduksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InputHasilPotongTest extends TestCase
{
    use RefreshDatabase;

    protected User $karyawanPotong;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->karyawanPotong = User::factory()->create(['role' => 'potong']);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    // ============================================
    // WORKFLOW - Status Transisi saat Input Hasil
    // ============================================

    public function test_status_berubah_ke_dalam_produksi_saat_tukang_potong_input_hasil_pertama_kali()
    {
        $wo = PerintahProduksi::factory()->disetujui()->create();
        $detail = DetailPerintahProduksi::factory()->create([
            'perintah_produksi_id' => $wo->id,
            'estimasi_pcs' => 120,
            'toleransi_minus' => 10,
            'qty_pcs_potong' => null,
            'status_validasi_potong' => 'pending',
        ]);

        $response = $this->actingAs($this->karyawanPotong)
            ->post("/produksi/potong/{$detail->id}/input-hasil", [
                'qty_pcs_potong' => 118,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('detail_perintah_produksi', [
            'id' => $detail->id,
            'qty_pcs_potong' => 118,
            'status_validasi_potong' => 'normal',
        ]);

        $this->assertDatabaseHas('perintah_produksi', [
            'id' => $wo->id,
            'status_produksi' => 'dalam_produksi',
        ]);
    }

    // ============================================
    // VALIDASI POTONG - Status Logic
    // ============================================

    public function test_status_validasi_normal_ketika_qty_potong_dalam_batas_toleransi()
    {
        $wo = PerintahProduksi::factory()->disetujui()->create();
        $detail = DetailPerintahProduksi::factory()->create([
            'perintah_produksi_id' => $wo->id,
            'estimasi_pcs' => 120,
            'toleransi_minus' => 10,
        ]);

        $response = $this->actingAs($this->karyawanPotong)
            ->post("/produksi/potong/{$detail->id}/input-hasil", [
                'qty_pcs_potong' => 115, // 120 - 10 = 110 (batas bawah), 115 > 110 = normal
            ]);

        $this->assertDatabaseHas('detail_perintah_produksi', [
            'id' => $detail->id,
            'status_validasi_potong' => 'normal',
        ]);
    }

    public function test_status_validasi_flag_ketika_qty_potong_dibawah_batas_toleransi()
    {
        $wo = PerintahProduksi::factory()->disetujui()->create();
        $detail = DetailPerintahProduksi::factory()->create([
            'perintah_produksi_id' => $wo->id,
            'estimasi_pcs' => 150,
            'toleransi_minus' => 10,
        ]);

        $response = $this->actingAs($this->karyawanPotong)
            ->post("/produksi/potong/{$detail->id}/input-hasil", [
                'qty_pcs_potong' => 130, // 150 - 10 = 140 (batas bawah), 130 < 140 = flag
                'alasan' => 'Kain cacat',
            ]);

        $this->assertDatabaseHas('detail_perintah_produksi', [
            'id' => $detail->id,
            'status_validasi_potong' => 'flag',
            'alasan' => 'Kain cacat',
        ]);
    }

    // ============================================
    // VALIDASI - Field Required
    // ============================================

    public function test_validasi_qty_pcs_potong_harus_diisi_saat_input_hasil()
    {
        $wo = PerintahProduksi::factory()->disetujui()->create();
        $detail = DetailPerintahProduksi::factory()->create([
            'perintah_produksi_id' => $wo->id,
        ]);

        $response = $this->actingAs($this->karyawanPotong)
            ->post("/produksi/potong/{$detail->id}/input-hasil", []);

        $response->assertSessionHasErrors(['qty_pcs_potong']);
    }

    public function test_validasi_alasan_wajib_diisi_ketika_status_flag()
    {
        $wo = PerintahProduksi::factory()->disetujui()->create();
        $detail = DetailPerintahProduksi::factory()->create([
            'perintah_produksi_id' => $wo->id,
            'estimasi_pcs' => 150,
            'toleransi_minus' => 10,
        ]);

        $response = $this->actingAs($this->karyawanPotong)
            ->post("/produksi/potong/{$detail->id}/input-hasil", [
                'qty_pcs_potong' => 130, // akan jadi flag
                'alasan' => null, // tapi alasan kosong
            ]);

        $response->assertSessionHasErrors(['alasan']);
    }

    // ============================================
    // ACCESS CONTROL
    // ============================================

    public function test_admin_tidak_dapat_menginput_hasil_potong()
    {
        $wo = PerintahProduksi::factory()->disetujui()->create();
        $detail = DetailPerintahProduksi::factory()->create([
            'perintah_produksi_id' => $wo->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->post("/produksi/potong/{$detail->id}/input-hasil", [
                'qty_pcs_potong' => 120,
            ]);

        $response->assertStatus(403);
    }
}

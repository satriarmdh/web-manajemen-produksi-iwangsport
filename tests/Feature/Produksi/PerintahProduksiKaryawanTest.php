<?php

namespace Tests\Feature\Produksi;

use App\Models\BahanBaku;
use App\Models\DetailPerintahProduksi;
use App\Models\PerintahProduksi;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerintahProduksiKaryawanTest extends TestCase
{
    use RefreshDatabase;

    protected User $karyawan;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->karyawan = User::factory()->create(['role' => 'potong']);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_karyawan_produksi_dapat_melihat_daftar_perintah_produksi_disetujui()
    {
        PerintahProduksi::factory()->disetujui()->create(['nomor_wo' => 'PROD-20260713-001']);

        $response = $this->actingAs($this->karyawan)->get('/produksi/perintah-produksi');

        $response->assertStatus(200)
            ->assertViewIs('produksi.perintah-produksi.index')
            ->assertSee('PROD-20260713-001');
    }

    public function test_karyawan_tidak_melihat_perintah_produksi_pending_ditolak_dan_selesai()
    {
        PerintahProduksi::factory()->pending()->create(['nomor_wo' => 'PROD-PENDING']);
        PerintahProduksi::factory()->create(['status_produksi' => 'ditolak', 'nomor_wo' => 'PROD-DITOLAK']);
        PerintahProduksi::factory()->create(['status_produksi' => 'selesai', 'nomor_wo' => 'PROD-SELESAI']);
        PerintahProduksi::factory()->disetujui()->create(['nomor_wo' => 'PROD-DISETUJUI']);

        $response = $this->actingAs($this->karyawan)->get('/produksi/perintah-produksi');

        $response->assertSee('PROD-DISETUJUI')
            ->assertDontSee('PROD-PENDING')
            ->assertDontSee('PROD-DITOLAK')
            ->assertDontSee('PROD-SELESAI');
    }

    public function test_karyawan_dapat_filter_perintah_produksi_berdasarkan_status()
    {
        PerintahProduksi::factory()->disetujui()->create(['nomor_wo' => 'PROD-DISETUJUI']);
        PerintahProduksi::factory()->create(['status_produksi' => 'dalam_produksi', 'nomor_wo' => 'PROD-DALAM']);

        $response = $this->actingAs($this->karyawan)->get('/produksi/perintah-produksi?status=dalam_produksi');

        $response->assertSee('PROD-DALAM')
            ->assertDontSee('PROD-DISETUJUI');
    }

    public function test_karyawan_dapat_mencari_perintah_produksi_berdasarkan_nomor_wo()
    {
        PerintahProduksi::factory()->disetujui()->create(['nomor_wo' => 'PROD-CARI-001']);
        PerintahProduksi::factory()->disetujui()->create(['nomor_wo' => 'PROD-LAIN-001']);

        $response = $this->actingAs($this->karyawan)->get('/produksi/perintah-produksi?search=CARI');

        $response->assertSee('PROD-CARI-001')
            ->assertDontSee('PROD-LAIN-001');
    }

    public function test_karyawan_dapat_melihat_detail_perintah_produksi_disetujui()
    {
        $wo = PerintahProduksi::factory()->disetujui()->create(['nomor_wo' => 'PROD-DETAIL-001']);
        DetailPerintahProduksi::factory()->create([
            'perintah_produksi_id' => $wo->id,
            'produk_id' => Produk::factory()->create()->id,
            'bahan_baku_id' => BahanBaku::factory()->create(['kategori' => 'kain'])->id,
        ]);

        $response = $this->actingAs($this->karyawan)->get("/produksi/perintah-produksi/{$wo->id}");

        $response->assertStatus(200)
            ->assertViewIs('produksi.perintah-produksi.show')
            ->assertSee('PROD-DETAIL-001');
    }

    public function test_karyawan_tidak_dapat_melihat_detail_perintah_produksi_pending()
    {
        $wo = PerintahProduksi::factory()->pending()->create();

        $response = $this->actingAs($this->karyawan)->get("/produksi/perintah-produksi/{$wo->id}");

        $response->assertStatus(403);
    }

    public function test_admin_tidak_dapat_mengakses_halaman_produksi()
    {
        $response = $this->actingAs($this->admin)->get('/produksi/perintah-produksi');

        $response->assertStatus(403);
    }

    public function test_karyawan_dapat_mengurutkan_perintah_produksi_berdasarkan_nomor_wo()
    {
        PerintahProduksi::factory()->disetujui()->create(['nomor_wo' => 'WO-ZZZ']);
        PerintahProduksi::factory()->disetujui()->create(['nomor_wo' => 'WO-AAA']);

        $response = $this->actingAs($this->karyawan)->get('/produksi/perintah-produksi?sort=wo_asc');

        $response->assertStatus(200);
        $content = $response->getContent();
        $posAAA = strpos($content, 'WO-AAA');
        $posZZZ = strpos($content, 'WO-ZZZ');
        $this->assertLessThan($posZZZ, $posAAA);
    }

    public function test_halaman_perintah_produksi_kosong_ketika_tidak_ada_wo()
    {
        $response = $this->actingAs($this->karyawan)->get('/produksi/perintah-produksi');

        $response->assertStatus(200)
            ->assertSee('Belum ada pekerjaan');
    }

    public function test_semua_role_produksi_dapat_mengakses_halaman_perintah_produksi()
    {
        $wo = PerintahProduksi::factory()->disetujui()->create();

        foreach (['potong', 'jahit', 'finishing'] as $role) {
            $karyawan = User::factory()->create(['role' => $role]);
            $response = $this->actingAs($karyawan)->get('/produksi/perintah-produksi');
            $response->assertStatus(200);
        }
    }

    public function test_owner_tidak_dapat_mengakses_halaman_produksi()
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)->get('/produksi/perintah-produksi');

        $response->assertStatus(403);
    }
}

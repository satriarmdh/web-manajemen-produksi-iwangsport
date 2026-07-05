<?php

namespace Tests\Feature\Owner;

use App\Models\PerintahProduksi;
use App\Models\DetailPerintahProduksi;
use App\Models\BahanBaku;
use App\Models\RiwayatPenggunaanKain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerintahProduksiApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['role' => 'owner']);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    // ============================================
    // ACCESS CONTROL
    // ============================================

    public function test_owner_dapat_mengakses_halaman_approval_perintah_produksi()
    {
        $response = $this->actingAs($this->owner)->get('/owner/perintah-produksi');

        $response->assertStatus(200)
            ->assertViewIs('owner.perintah-produksi.index');
    }

    public function test_admin_tidak_dapat_mengakses_halaman_approval_owner()
    {
        $response = $this->actingAs($this->admin)->get('/owner/perintah-produksi');

        $response->assertStatus(403);
    }

    // ============================================
    // APPROVE
    // ============================================

    public function test_owner_dapat_menyetujui_perintah_produksi()
    {
        $wo = PerintahProduksi::factory()->pending()->create();

        $response = $this->actingAs($this->owner)
            ->post("/owner/perintah-produksi/{$wo->id}/approve");

        $response->assertRedirect('/owner/perintah-produksi');

        $this->assertDatabaseHas('perintah_produksi', [
            'id' => $wo->id,
            'status_produksi' => 'disetujui',
            'approved_by' => $this->owner->id,
        ]);

        $wo->refresh();
        $this->assertNotNull($wo->approved_at);
    }

    public function test_approval_owner_mengurangi_stok_kain_dan_mencatat_riwayat_stok()
    {
        $wo = PerintahProduksi::factory()->pending()->create();
        $bahanBaku = BahanBaku::factory()->create([
            'kategori' => 'kain',
            'stok' => 10,
        ]);

        DetailPerintahProduksi::factory()->create([
            'perintah_produksi_id' => $wo->id,
            'bahan_baku_id' => $bahanBaku->id,
            'qty_roll_pakai' => 3,
        ]);

        $response = $this->actingAs($this->owner)
            ->post("/owner/perintah-produksi/{$wo->id}/approve");

        $response->assertRedirect('/owner/perintah-produksi');

        $this->assertDatabaseHas('bahan_baku', [
            'id' => $bahanBaku->id,
            'stok' => 7,
        ]);

        $riwayatPenggunaanKain = RiwayatPenggunaanKain::where('perintah_produksi_id', $wo->id)->first();

        $this->assertNotNull($riwayatPenggunaanKain);
        $this->assertDatabaseHas('riwayat_penggunaan_kain', [
            'id' => $riwayatPenggunaanKain->id,
            'bahan_baku_id' => $bahanBaku->id,
            'jumlah_pakai' => 3,
        ]);
        $this->assertDatabaseHas('riwayat_stok', [
            'jenis_item' => 'bahan_baku',
            'id_item' => $bahanBaku->id,
            'jenis_pergerakan' => 'keluar',
            'jumlah' => 3,
            'stok_sebelum' => 10,
            'stok_sesudah' => 7,
            'referensi_type' => RiwayatPenggunaanKain::class,
            'referensi_id' => $riwayatPenggunaanKain->id,
        ]);
    }

    // ============================================
    // REJECT
    // ============================================

    public function test_owner_dapat_menolak_perintah_produksi()
    {
        $wo = PerintahProduksi::factory()->pending()->create();

        $response = $this->actingAs($this->owner)
            ->post("/owner/perintah-produksi/{$wo->id}/reject", [
                'alasan_penolakan' => 'Data tidak lengkap',
            ]);

        $response->assertRedirect('/owner/perintah-produksi');

        $this->assertDatabaseHas('perintah_produksi', [
            'id' => $wo->id,
            'status_produksi' => 'ditolak',
            'approved_by' => $this->owner->id,
        ]);
    }

    // ============================================
    // FILTER - Owner hanya lihat pending
    // ============================================

    public function test_owner_hanya_melihat_perintah_produksi_dengan_status_pending()
    {
        PerintahProduksi::factory()->pending()->create(['nomor_wo' => 'PROD-20260622-001']);
        PerintahProduksi::factory()->disetujui()->create(['nomor_wo' => 'PROD-20260622-002']);

        $response = $this->actingAs($this->owner)->get('/owner/perintah-produksi');

        $response->assertSee('PROD-20260622-001')
            ->assertDontSee('PROD-20260622-002');
    }
}

<?php

namespace Tests\Feature\Owner;

use App\Models\BahanBaku;
use App\Models\DetailPerintahProduksi;
use App\Models\PerintahProduksi;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PantauProgresProduksiOwnerTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $admin;
    protected User $karyawan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create(['role' => 'owner']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->karyawan = User::factory()->create(['role' => 'potong']);
    }

    /** @test */
    public function owner_dapat_mengakses_halaman_index_pantau_progres()
    {
        $wo = PerintahProduksi::factory()->create([
            'nomor_wo' => 'WO-PANTAU-INDEX-1',
            'status_produksi' => 'dalam_produksi',
        ]);

        $response = $this->actingAs($this->owner)
            ->get(route('owner.pantau-progres.index'));

        $response->assertStatus(200)
            ->assertViewIs('owner.pantau-progres.index')
            ->assertSee($wo->nomor_wo);
    }

    /** @test */
    public function owner_dapat_mengakses_halaman_detail_pantau_progres()
    {
        $bahan = BahanBaku::factory()->create(['kategori' => 'kain']);
        $produk = Produk::factory()->create();
        $wo = PerintahProduksi::factory()->create([
            'nomor_wo' => 'WO-PANTAU-DETAIL-2',
            'status_produksi' => 'dalam_produksi',
        ]);

        $detailWo = DetailPerintahProduksi::factory()->create([
            'perintah_produksi_id' => $wo->id,
            'produk_id' => $produk->id,
            'bahan_baku_id' => $bahan->id,
            'qty_roll_pakai' => 4,
            'estimasi_pcs' => 150,
        ]);

        $response = $this->actingAs($this->owner)
            ->get(route('owner.pantau-progres.show', $wo));

        $response->assertStatus(200)
            ->assertViewIs('owner.pantau-progres.show')
            ->assertSee($wo->nomor_wo)
            ->assertSee($produk->nama_produk)
            ->assertSee($bahan->nama_bahan)
            ->assertSee('150 pcs');
    }

    /** @test */
    public function owner_dapat_mencari_dan_memfilter_perintah_produksi()
    {
        $wo1 = PerintahProduksi::factory()->create([
            'nomor_wo' => 'WO-TARGET-SEARCH',
            'status_produksi' => 'dalam_produksi',
        ]);

        $wo2 = PerintahProduksi::factory()->create([
            'nomor_wo' => 'WO-OTHER-SEARCH',
            'status_produksi' => 'selesai',
        ]);

        // Uji Search
        $responseSearch = $this->actingAs($this->owner)
            ->get(route('owner.pantau-progres.index', ['search' => 'TARGET']));

        $responseSearch->assertStatus(200)
            ->assertSee('WO-TARGET-SEARCH')
            ->assertDontSee('WO-OTHER-SEARCH');

        // Uji Filter Status
        $responseFilter = $this->actingAs($this->owner)
            ->get(route('owner.pantau-progres.index', ['status' => 'selesai']));

        $responseFilter->assertStatus(200)
            ->assertSee('WO-OTHER-SEARCH')
            ->assertDontSee('WO-TARGET-SEARCH');
    }

    /** @test */
    public function owner_tidak_dapat_melihat_wo_pending_di_pantau_progres()
    {
        $woPending = PerintahProduksi::factory()->create([
            'nomor_wo' => 'WO-PENDING-UNSEEN',
            'status_produksi' => 'pending',
        ]);

        $woDisetujui = PerintahProduksi::factory()->create([
            'nomor_wo' => 'WO-APPROVED-SEEN',
            'status_produksi' => 'disetujui',
        ]);

        // Uji Index: pending WO tidak boleh kelihatan
        $responseIndex = $this->actingAs($this->owner)
            ->get(route('owner.pantau-progres.index'));

        $responseIndex->assertStatus(200)
            ->assertSee('WO-APPROVED-SEEN')
            ->assertDontSee('WO-PENDING-UNSEEN');

        // Uji Detail Direct Access: pending WO tidak boleh diakses (harus 404)
        $responseDetail = $this->actingAs($this->owner)
            ->get(route('owner.pantau-progres.show', $woPending));

        $responseDetail->assertStatus(404);
    }

    /** @test */
    public function non_owner_tidak_dapat_mengakses_pantau_progres()
    {
        $responseAdmin = $this->actingAs($this->admin)
            ->get(route('owner.pantau-progres.index'));
        $responseAdmin->assertStatus(403);

        $responseKaryawan = $this->actingAs($this->karyawan)
            ->get(route('owner.pantau-progres.index'));
        $responseKaryawan->assertStatus(403);
    }
}

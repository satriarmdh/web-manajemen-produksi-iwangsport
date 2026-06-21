<?php

namespace Tests\Feature\Admin;

use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\StandardBaselineProduksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StandardBaselineProduksiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Produk $produk;
    protected BahanBaku $bahanBaku;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'admin']);
        $this->produk = Produk::factory()->create();
        $this->bahanBaku = BahanBaku::factory()->create(['kategori' => 'kain']);
    }

    /**
     * Test index page loads successfully
     */
    public function test_index_page_loads_successfully()
    {
        $response = $this->actingAs($this->user)->get(route('admin.standard-baseline-produksi.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.standard-baseline-produksi.index');
        $response->assertViewHas(['estimasi', 'produks', 'bahanBaku']);
    }

    /**
     * Test store creates new standard baseline produksi
     */
    public function test_store_creates_new_standard_baseline_produksi()
    {
        $data = [
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 150,
            'toleransi_minus' => 10,
            'keterangan' => 'Test keterangan',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('admin.standard-baseline-produksi.store'), $data);

        $response->assertRedirect(route('admin.standard-baseline-produksi.index'));
        $response->assertSessionHas('success', 'Standard baseline produksi berhasil ditambahkan');

        $this->assertDatabaseHas('standard_baseline_produksi', [
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 150,
            'toleransi_minus' => 10,
            'keterangan' => 'Test keterangan',
        ]);
    }

    /**
     * Test store validation fails with invalid data
     */
    public function test_store_validation_fails_with_invalid_data()
    {
        $data = [
            'produk_id' => '',
            'bahan_baku_id' => '',
            'pcs_per_roll' => -1,
            'toleransi_minus' => -1,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('admin.standard-baseline-produksi.store'), $data);

        $response->assertSessionHasErrors(['produk_id', 'bahan_baku_id', 'pcs_per_roll', 'toleransi_minus']);
    }

    /**
     * Test update modifies existing standard baseline produksi
     */
    public function test_update_modifies_existing_standard_baseline_produksi()
    {
        $baseline = StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        $data = [
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 200,
            'toleransi_minus' => 15,
            'keterangan' => 'Updated keterangan',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('admin.standard-baseline-produksi.update', $baseline), $data);

        $response->assertRedirect(route('admin.standard-baseline-produksi.index'));
        $response->assertSessionHas('success', 'Standard baseline produksi berhasil diperbarui.');

        $this->assertDatabaseHas('standard_baseline_produksi', [
            'id' => $baseline->id,
            'pcs_per_roll' => 200,
            'toleransi_minus' => 15,
            'keterangan' => 'Updated keterangan',
        ]);
    }

    /**
     * Test destroy deletes standard baseline produksi
     */
    public function test_destroy_deletes_standard_baseline_produksi()
    {
        $baseline = StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('admin.standard-baseline-produksi.destroy', $baseline));

        $response->assertRedirect(route('admin.standard-baseline-produksi.index'));
        $response->assertSessionHas('success', 'Standard baseline produksi berhasil dihapus.');

        $this->assertSoftDeleted('standard_baseline_produksi', ['id' => $baseline->id]);
    }

    /**
     * Test filter by status aktif
     */
    public function test_filter_by_status_aktif()
    {
        $baselineAktif = StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'is_aktif' => true,
        ]);

        $baselineNonaktif = StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'is_aktif' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.standard-baseline-produksi.index', ['status' => 'aktif']));

        $response->assertStatus(200);
        $response->assertSee($baselineAktif->id);
        $response->assertDontSee($baselineNonaktif->id);
    }

    /**
     * Test filter by status nonaktif
     */
    public function test_filter_by_status_nonaktif()
    {
        $baselineAktif = StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'is_aktif' => true,
        ]);

        $baselineNonaktif = StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'is_aktif' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.standard-baseline-produksi.index', ['status' => 'nonaktif']));

        $response->assertStatus(200);
        $response->assertDontSee($baselineAktif->id);
        $response->assertSee($baselineNonaktif->id);
    }

    /**
     * Test sort by newest
     */
    public function test_sort_by_newest()
    {
        StandardBaselineProduksi::factory()->count(3)->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.standard-baseline-produksi.index', ['sort' => 'newest']));

        $response->assertStatus(200);
    }

    /**
     * Test sort by oldest
     */
    public function test_sort_by_oldest()
    {
        StandardBaselineProduksi::factory()->count(3)->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.standard-baseline-produksi.index', ['sort' => 'oldest']));

        $response->assertStatus(200);
    }

    /**
     * Test search by produk name
     */
    public function test_search_by_produk_name()
    {
        $produk = Produk::factory()->create(['nama_produk' => 'Kaos Polos']);
        
        StandardBaselineProduksi::factory()->create([
            'produk_id' => $produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.standard-baseline-produksi.index', ['search' => 'Kaos Polos']));

        $response->assertStatus(200);
        $response->assertSee('Kaos Polos');
    }

    /**
     * Test search by bahan baku name
     */
    public function test_search_by_bahan_baku_name()
    {
        $bahanBaku = BahanBaku::factory()->create([
            'nama_bahan' => 'Kain Katun',
            'kategori' => 'kain'
        ]);
        
        StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $bahanBaku->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.standard-baseline-produksi.index', ['search' => 'Kain Katun']));

        $response->assertStatus(200);
        $response->assertSee('Kain Katun');
    }

    /**
     * Test range_bawah accessor
     */
    public function test_range_bawah_accessor()
    {
        $baseline = StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 150,
            'toleransi_minus' => 10,
        ]);

        $this->assertEquals(140, $baseline->range_bawah);
    }

    /**
     * Test range_bawah accessor with negative result returns zero
     */
    public function test_range_bawah_accessor_returns_zero_when_negative()
    {
        $baseline = StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 5,
            'toleransi_minus' => 10,
        ]);

        $this->assertEquals(0, $baseline->range_bawah);
    }

    /**
     * Test relationship with produk
     */
    public function test_relationship_with_produk()
    {
        $baseline = StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        $this->assertInstanceOf(Produk::class, $baseline->produk);
        $this->assertEquals($this->produk->id, $baseline->produk->id);
    }

    /**
     * Test relationship with bahan baku
     */
    public function test_relationship_with_bahan_baku()
    {
        $baseline = StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        $this->assertInstanceOf(BahanBaku::class, $baseline->bahanBaku);
        $this->assertEquals($this->bahanBaku->id, $baseline->bahanBaku->id);
    }

    /**
     * Test unauthenticated user cannot access index
     */
    public function test_unauthenticated_user_cannot_access_index()
    {
        $response = $this->get(route('admin.standard-baseline-produksi.index'));

        $response->assertRedirect('/login');
    }

    /**
     * Test unauthenticated user cannot store
     */
    public function test_unauthenticated_user_cannot_store()
    {
        $data = [
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 150,
        ];

        $response = $this->post(route('admin.standard-baseline-produksi.store'), $data);

        $response->assertRedirect('/login');
    }

    /**
     * Test unauthenticated user cannot update
     */
    public function test_unauthenticated_user_cannot_update()
    {
        $baseline = StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        $data = [
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 200,
        ];

        $response = $this->put(route('admin.standard-baseline-produksi.update', $baseline), $data);

        $response->assertRedirect('/login');
    }

    /**
     * Test unauthenticated user cannot destroy
     */
    public function test_unauthenticated_user_cannot_destroy()
    {
        $baseline = StandardBaselineProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        $response = $this->delete(route('admin.standard-baseline-produksi.destroy', $baseline));

        $response->assertRedirect('/login');
    }
}

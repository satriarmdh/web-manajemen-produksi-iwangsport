<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Produk;
use App\Models\BahanBaku;
use App\Models\EstimasiProduksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstimasiProduksiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Produk $produk;
    protected BahanBaku $bahanBaku;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->produk = Produk::factory()->create();
        $this->bahanBaku = BahanBaku::factory()->create(['kategori' => 'kain']);
    }

    /**
     * Skenario: Admin dapat mengakses halaman estimasi produksi
     */
    public function test_admin_dapat_mengakses_halaman_estimasi_produksi()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.estimasi-produksi.index'));

        $response->assertStatus(200);
        $response->assertSee('Standard Baseline Produksi');
    }

    /**
     * Skenario: Admin dapat membuat estimasi produksi baru dengan data valid
     */
    public function test_admin_dapat_membuat_estimasi_produksi_baru()
    {
        $data = [
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 150,
            'toleransi_minus' => 10,
            'keterangan' => 'Estimasi untuk produksi massal',
            'is_aktif' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.estimasi-produksi.store'), $data);

        $response->assertRedirect(route('admin.estimasi-produksi.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('estimasi_produksi', [
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 150,
            'toleransi_minus' => 10,
            'is_aktif' => true,
        ]);
    }

    /**
     * Skenario: Validasi gagal jika produk_id tidak diisi
     */
    public function test_validasi_gagal_jika_produk_id_kosong()
    {
        $data = [
            'produk_id' => '',
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 150,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.estimasi-produksi.store'), $data);

        $response->assertSessionHasErrors('produk_id');
    }

    /**
     * Skenario: Validasi gagal jika bahan_baku_id tidak diisi
     */
    public function test_validasi_gagal_jika_bahan_baku_id_kosong()
    {
        $data = [
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => '',
            'pcs_per_roll' => 150,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.estimasi-produksi.store'), $data);

        $response->assertSessionHasErrors('bahan_baku_id');
    }

    /**
     * Skenario: Validasi gagal jika pcs_per_roll kurang dari 1
     */
    public function test_validasi_gagal_jika_pcs_per_roll_kurang_dari_satu()
    {
        $data = [
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 0,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.estimasi-produksi.store'), $data);

        $response->assertSessionHasErrors('pcs_per_roll');
    }

    /**
     * Skenario: Validasi gagal jika toleransi_minus negatif
     */
    public function test_validasi_gagal_jika_toleransi_minus_negatif()
    {
        $data = [
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 150,
            'toleransi_minus' => -5,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.estimasi-produksi.store'), $data);

        $response->assertSessionHasErrors('toleransi_minus');
    }

    /**
     * Skenario: Validasi gagal jika kombinasi produk dan bahan_baku sudah ada
     */
    public function test_validasi_gagal_jika_kombinasi_produk_dan_bahan_sudah_ada()
    {
        // Buat estimasi pertama
        EstimasiProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        // Coba buat dengan kombinasi yang sama
        $data = [
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 150,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.estimasi-produksi.store'), $data);

        $response->assertSessionHasErrors('bahan_baku_id');
    }

    /**
     * Skenario: Default value is_aktif adalah true jika tidak diisi
     */
    public function test_default_is_aktif_adalah_true()
    {
        $data = [
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 150,
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.estimasi-produksi.store'), $data);

        $estimasi = EstimasiProduksi::first();
        $this->assertTrue($estimasi->is_aktif);
    }

    /**
     * Skenario: Default value toleransi_minus adalah 0 jika tidak diisi
     */
    public function test_default_toleransi_minus_adalah_nol()
    {
        $data = [
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 150,
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.estimasi-produksi.store'), $data);

        $estimasi = EstimasiProduksi::first();
        $this->assertEquals(0, $estimasi->toleransi_minus);
    }

    /**
     * Skenario: Admin dapat mengupdate estimasi produksi
     */
    public function test_admin_dapat_mengupdate_estimasi_produksi()
    {
        $estimasi = EstimasiProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 100,
        ]);

        $data = [
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 200,
            'toleransi_minus' => 15,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.estimasi-produksi.update', $estimasi), $data);

        $response->assertRedirect(route('admin.estimasi-produksi.index'));
        
        $this->assertDatabaseHas('estimasi_produksi', [
            'id' => $estimasi->id,
            'pcs_per_roll' => 200,
            'toleransi_minus' => 15,
        ]);
    }

    /**
     * Skenario: Admin dapat menghapus estimasi produksi
     */
    public function test_admin_dapat_menghapus_estimasi_produksi()
    {
        $estimasi = EstimasiProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.estimasi-produksi.destroy', $estimasi));

        $response->assertRedirect(route('admin.estimasi-produksi.index'));
        $this->assertDatabaseMissing('estimasi_produksi', ['id' => $estimasi->id]);
    }

    /**
     * Skenario: Fitur search berdasarkan nama produk
     */
    public function test_dapat_mencari_berdasarkan_nama_produk()
    {
        $produk = Produk::factory()->create(['nama_produk' => 'Celana Basket Premium']);
        $estimasi = EstimasiProduksi::factory()->create([
            'produk_id' => $produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.estimasi-produksi.index', ['search' => 'Basket Premium']));

        $response->assertStatus(200);
        $response->assertSee('Celana Basket Premium');
    }

    /**
     * Skenario: Fitur search berdasarkan nama bahan baku
     */
    public function test_dapat_mencari_berdasarkan_nama_bahan_baku()
    {
        $bahan = BahanBaku::factory()->create([
            'nama_bahan' => 'Kain Katun Premium',
            'kategori' => 'kain',
        ]);
        $estimasi = EstimasiProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $bahan->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.estimasi-produksi.index', ['search' => 'Katun Premium']));

        $response->assertStatus(200);
        $response->assertSee('Kain Katun Premium');
    }

    /**
     * Skenario: Filter berdasarkan status aktif
     */
    public function test_dapat_filter_berdasarkan_status_aktif()
    {
        $estimasiAktif = EstimasiProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'is_aktif' => true,
        ]);

        $bahanLain = BahanBaku::factory()->create(['kategori' => 'kain']);
        $estimasiNonaktif = EstimasiProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $bahanLain->id,
            'is_aktif' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.estimasi-produksi.index', ['status' => 'aktif']));

        $response->assertStatus(200);
        $response->assertSee($this->produk->nama_produk);
    }

    /**
     * Skenario: Sorting berdasarkan terbaru
     */
    public function test_dapat_sorting_berdasarkan_terbaru()
    {
        $estimasi1 = EstimasiProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'created_at' => now()->subDays(2),
        ]);

        $bahanLain = BahanBaku::factory()->create(['kategori' => 'kain']);
        $estimasi2 = EstimasiProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $bahanLain->id,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.estimasi-produksi.index', ['sort' => 'newest']));

        $response->assertStatus(200);
    }

    /**
     * Skenario: Sorting berdasarkan nama produk A-Z
     */
    public function test_dapat_sorting_berdasarkan_nama_produk_asc()
    {
        $produkA = Produk::factory()->create(['nama_produk' => 'Celana A']);
        $produkZ = Produk::factory()->create(['nama_produk' => 'Celana Z']);

        $bahan1 = BahanBaku::factory()->create(['kategori' => 'kain']);
        $bahan2 = BahanBaku::factory()->create(['kategori' => 'kain']);

        EstimasiProduksi::factory()->create([
            'produk_id' => $produkZ->id,
            'bahan_baku_id' => $bahan1->id,
        ]);

        EstimasiProduksi::factory()->create([
            'produk_id' => $produkA->id,
            'bahan_baku_id' => $bahan2->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.estimasi-produksi.index', ['sort' => 'nama_produk_asc']));

        $response->assertStatus(200);
    }

    /**
     * Skenario: Model memiliki relasi ke Produk
     */
    public function test_model_memiliki_relasi_ke_produk()
    {
        $estimasi = EstimasiProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        $this->assertInstanceOf(Produk::class, $estimasi->produk);
        $this->assertEquals($this->produk->id, $estimasi->produk->id);
    }

    /**
     * Skenario: Model memiliki relasi ke BahanBaku
     */
    public function test_model_memiliki_relasi_ke_bahan_baku()
    {
        $estimasi = EstimasiProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
        ]);

        $this->assertInstanceOf(BahanBaku::class, $estimasi->bahanBaku);
        $this->assertEquals($this->bahanBaku->id, $estimasi->bahanBaku->id);
    }

    /**
     * Skenario: Accessor range_bawah menghitung dengan benar
     */
    public function test_accessor_range_bawah_menghitung_dengan_benar()
    {
        $estimasi = EstimasiProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 150,
            'toleransi_minus' => 20,
        ]);

        $this->assertEquals(130, $estimasi->range_bawah);
    }

    /**
     * Skenario: Accessor range_bawah tidak negatif
     */
    public function test_accessor_range_bawah_tidak_negatif()
    {
        $estimasi = EstimasiProduksi::factory()->create([
            'produk_id' => $this->produk->id,
            'bahan_baku_id' => $this->bahanBaku->id,
            'pcs_per_roll' => 10,
            'toleransi_minus' => 15, // toleransi lebih besar dari pcs_per_roll
        ]);

        $this->assertEquals(0, $estimasi->range_bawah);
    }

    /**
     * Skenario: Dropdown bahan baku hanya menampilkan kategori kain
     */
    public function test_dropdown_bahan_baku_hanya_kategori_kain()
    {
        $bahanKain = BahanBaku::factory()->create(['kategori' => 'kain']);
        $bahanBenang = BahanBaku::factory()->create(['kategori' => 'benang']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.estimasi-produksi.index'));

        $response->assertStatus(200);
        $response->assertSee($bahanKain->nama_bahan);
        $response->assertDontSee($bahanBenang->nama_bahan);
    }

    /**
     * Skenario: User non-admin tidak dapat mengakses halaman estimasi produksi
     */
    public function test_user_non_admin_tidak_dapat_mengakses_halaman()
    {
        $user = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($user)
            ->get(route('admin.estimasi-produksi.index'));

        $response->assertStatus(403);
    }

    /**
     * Skenario: Guest tidak dapat mengakses halaman estimasi produksi
     */
    public function test_guest_tidak_dapat_mengakses_halaman()
    {
        $response = $this->get(route('admin.estimasi-produksi.index'));

        $response->assertRedirect(route('login'));
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProdukManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $karyawanJahit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->karyawanJahit = User::factory()->create(['role' => 'jahit']);
    }

    public function test_admin_dapat_mengakses_halaman_katalog_produk()
    {
        $response = $this->actingAs($this->admin)->get('/admin/produk');

        $response->assertStatus(200)
            ->assertViewIs('admin.produk.index')
            ->assertViewHas('produk');
    }

    public function test_karyawan_non_admin_tidak_dapat_mengakses_halaman_katalog_produk()
    {
        $response = $this->actingAs($this->karyawanJahit)->get('/admin/produk');

        $response->assertStatus(403);
    }

    public function test_admin_dapat_menambahkan_produk_baru()
    {
        $dataProduk = [
            'nama_produk'  => 'Celana Basket',
            'ukuran'       => 'normal',
            'warna'        => 'hitam',
            'harga_satuan' => 30000,
            'satuan'       => 'pcs',
            'stok'         => 100,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/produk', $dataProduk);

        $response->assertRedirect('/admin/produk')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('produk', [
            'nama_produk' => 'Celana Basket',
            'ukuran' => 'normal',
        ]);

        // Pastikan kode_produk auto-generated oleh service
        $produk = Produk::where('nama_produk', 'Celana Basket')->first();
        $this->assertNotNull($produk->kode_produk);
        $this->assertStringStartsWith('CLN-', $produk->kode_produk);
    }

    public function test_sistem_auto_generate_kode_produk_sequential()
    {
        // Tambahkan produk pertama - auto-generate CLN-001
        $this->actingAs($this->admin)->post('/admin/produk', [
            'nama_produk'  => 'Celana Basket',
            'ukuran'       => 'normal',
            'warna'        => 'hitam',
            'harga_satuan' => 80000,
            'satuan'       => 'pcs',
            'stok'         => 30,
        ]);

        // Tambahkan produk kedua - auto-generate CLN-002
        $this->actingAs($this->admin)->post('/admin/produk', [
            'nama_produk'  => 'Celana Training',
            'ukuran'       => 'normal',
            'warna'        => 'biru',
            'harga_satuan' => 90000,
            'satuan'       => 'pcs',
            'stok'         => 50,
        ]);

        $this->assertDatabaseCount('produk', 2);

        $produkPertama = Produk::where('nama_produk', 'Celana Basket')->first();
        $this->assertEquals('CLN-001', $produkPertama->kode_produk);

        $produkKedua = Produk::where('nama_produk', 'Celana Training')->first();
        $this->assertEquals('CLN-002', $produkKedua->kode_produk);
    }

    public function test_admin_dapat_memperbarui_data_produk()
    {
        $produk = Produk::factory()->create([
            'nama_produk' => 'Celana Jogger Lama',
            'ukuran' => 'normal',
            'warna' => 'hitam',
            'harga_satuan' => 50000,
            'satuan' => 'pcs',
            'stok' => 50,
        ]);

        $dataUpdate = [
            'nama_produk'  => 'Celana Jogger Premium',
            'ukuran'       => 'jumbo',
            'warna'        => 'abu',
            'harga_satuan' => 75000,
            'satuan'       => 'pcs',
            'stok'         => 80,
        ];

        $response = $this->actingAs($this->admin)->put('/admin/produk/' . $produk->id, $dataUpdate);

        $response->assertRedirect('/admin/produk');
        $this->assertDatabaseHas('produk', [
            'id'          => $produk->id,
            'nama_produk' => 'Celana Jogger Premium',
            'ukuran' => 'jumbo',
            'harga_satuan' => 75000,
        ]);
    }

    public function test_admin_dapat_menghapus_produk_secara_soft_delete()
    {
        $produk = Produk::factory()->create();

        $response = $this->actingAs($this->admin)->delete('/admin/produk/' . $produk->id);

        $response->assertRedirect('/admin/produk');

        $this->assertSoftDeleted('produk', [
            'id' => $produk->id,
        ]);
    }

    public function test_admin_dapat_mencari_produk_berdasarkan_nama_atau_kode()
    {
        Produk::factory()->create(['nama_produk' => 'Celana Basket Premium', 'kode_produk' => 'CLN-001']);
        Produk::factory()->create(['nama_produk' => 'Celana Training', 'kode_produk' => 'CLN-002']);

        $response = $this->actingAs($this->admin)
            ->get('/admin/produk?search=Basket');

        $response->assertStatus(200)
            ->assertSee('Celana Basket Premium')
            ->assertDontSee('Celana Training');

        // Test search by kode
        $response = $this->actingAs($this->admin)
            ->get('/admin/produk?search=CLN-002');

        $response->assertStatus(200)
            ->assertSee('Celana Training')
            ->assertDontSee('Celana Basket Premium');
    }

    public function test_admin_dapat_memfilter_produk_berdasarkan_ukuran()
    {
        Produk::factory()->create(['nama_produk' => 'Produk Normal', 'ukuran' => 'normal']);
        Produk::factory()->create(['nama_produk' => 'Produk Jumbo', 'ukuran' => 'jumbo']);

        $response = $this->actingAs($this->admin)
            ->get('/admin/produk?ukuran=normal');

        $response->assertStatus(200)
            ->assertSee('Produk Normal')
            ->assertDontSee('Produk Jumbo');

        $response = $this->actingAs($this->admin)
            ->get('/admin/produk?ukuran=jumbo');

        $response->assertStatus(200)
            ->assertSee('Produk Jumbo')
            ->assertDontSee('Produk Normal');
    }

    public function test_admin_dapat_memfilter_produk_berdasarkan_status_stok()
    {
        Produk::factory()->create(['nama_produk' => 'Produk Tersedia', 'stok' => 10]);
        Produk::factory()->create(['nama_produk' => 'Produk Habis', 'stok' => 0]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/produk?stok=tersedia');

        $response->assertStatus(200)
            ->assertSee('Produk Tersedia')
            ->assertDontSee('Produk Habis');

        $response = $this->actingAs($this->admin)
            ->get('/admin/produk?stok=habis');

        $response->assertStatus(200)
            ->assertSee('Produk Habis')
            ->assertDontSee('Produk Tersedia');
    }

    public function test_admin_dapat_mengurutkan_produk_berdasarkan_nama_asc()
    {
        Produk::factory()->create(['nama_produk' => 'Zebra Produk']);
        Produk::factory()->create(['nama_produk' => 'Alpha Produk']);

        $response = $this->actingAs($this->admin)
            ->get('/admin/produk?sort=nama_asc');

        $response->assertStatus(200);
        $produk = $response->viewData('produk');
        $this->assertEquals('Alpha Produk', $produk->first()->nama_produk);
    }

    public function test_admin_dapat_mengurutkan_produk_berdasarkan_nama_desc()
    {
        Produk::factory()->create(['nama_produk' => 'Alpha Produk']);
        Produk::factory()->create(['nama_produk' => 'Zebra Produk']);

        $response = $this->actingAs($this->admin)
            ->get('/admin/produk?sort=nama_desc');

        $response->assertStatus(200);
        $produk = $response->viewData('produk');
        $this->assertEquals('Zebra Produk', $produk->first()->nama_produk);
    }

    public function test_admin_dapat_mengurutkan_produk_berdasarkan_stok_desc()
    {
        Produk::factory()->create(['nama_produk' => 'Stok Rendah', 'stok' => 5]);
        Produk::factory()->create(['nama_produk' => 'Stok Tinggi', 'stok' => 100]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/produk?sort=stok_desc');

        $response->assertStatus(200);
        $produk = $response->viewData('produk');
        $this->assertEquals('Stok Tinggi', $produk->first()->nama_produk);
    }

    public function test_admin_dapat_mengurutkan_produk_berdasarkan_stok_asc()
    {
        Produk::factory()->create(['nama_produk' => 'Stok Tinggi', 'stok' => 100]);
        Produk::factory()->create(['nama_produk' => 'Stok Rendah', 'stok' => 5]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/produk?sort=stok_asc');

        $response->assertStatus(200);
        $produk = $response->viewData('produk');
        $this->assertEquals('Stok Rendah', $produk->first()->nama_produk);
    }

    public function test_admin_dapat_mengurutkan_produk_berdasarkan_waktu_terbaru()
    {
        Produk::factory()->create(['nama_produk' => 'Produk Lama', 'created_at' => now()->subDays(5)]);
        Produk::factory()->create(['nama_produk' => 'Produk Baru', 'created_at' => now()]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/produk?sort=terbaru');

        $response->assertStatus(200);
        $produk = $response->viewData('produk');
        $this->assertEquals('Produk Baru', $produk->first()->nama_produk);
    }

    public function test_admin_dapat_mengurutkan_produk_berdasarkan_waktu_terlama()
    {
        Produk::factory()->create(['nama_produk' => 'Produk Baru', 'created_at' => now()]);
        Produk::factory()->create(['nama_produk' => 'Produk Lama', 'created_at' => now()->subDays(5)]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/produk?sort=terlama');

        $response->assertStatus(200);
        $produk = $response->viewData('produk');
        $this->assertEquals('Produk Lama', $produk->first()->nama_produk);
    }

    public function test_halaman_index_menampilkan_pagination_ketika_data_lebih_dari_10()
    {
        Produk::factory()->count(15)->create();

        $response = $this->actingAs($this->admin)->get('/admin/produk');

        $response->assertStatus(200)
            ->assertSee('Pagination Navigation')
            ->assertSee('Menampilkan');
    }

    public function test_validasi_field_wajib_diisi_saat_menambahkan_produk()
    {
        $response = $this->actingAs($this->admin)->post('/admin/produk', []);

        $response->assertSessionHasErrors(['nama_produk', 'ukuran', 'warna', 'harga_satuan', 'satuan']);
    }

    public function test_validasi_ukuran_harus_normal_atau_jumbo()
    {
        $data = [
            'nama_produk'  => 'Test',
            'ukuran'       => 'kecil', // Invalid
            'warna'        => 'hitam',
            'harga_satuan' => 50000,
            'satuan'       => 'pcs',
            'stok'         => 10,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/produk', $data);

        $response->assertSessionHasErrors(['ukuran']);
    }

    public function test_validasi_harga_satuan_harus_angka_positif()
    {
        $data = [
            'nama_produk'  => 'Test',
            'ukuran'       => 'normal',
            'warna'        => 'hitam',
            'harga_satuan' => -1000, // Invalid
            'satuan'       => 'pcs',
            'stok'         => 10,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/produk', $data);

        $response->assertSessionHasErrors(['harga_satuan']);
    }

    public function test_validasi_stok_harus_angka_tidak_negatif()
    {
        $data = [
            'nama_produk'  => 'Test',
            'ukuran'       => 'normal',
            'warna'        => 'hitam',
            'harga_satuan' => 50000,
            'satuan'       => 'pcs',
            'stok'         => -5, // Invalid
        ];

        $response = $this->actingAs($this->admin)->post('/admin/produk', $data);

        $response->assertSessionHasErrors(['stok']);
    }

    public function test_admin_dapat_menggabungkan_filter_dan_search()
    {
        Produk::factory()->create(['nama_produk' => 'Celana Basket A', 'ukuran' => 'normal', 'stok' => 10]);
        Produk::factory()->create(['nama_produk' => 'Celana Basket B', 'ukuran' => 'normal', 'stok' => 0]);
        Produk::factory()->create(['nama_produk' => 'Celana Training A', 'ukuran' => 'jumbo', 'stok' => 10]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/produk?search=Basket&ukuran=normal&stok=tersedia');

        $response->assertStatus(200)
            ->assertSee('Celana Basket A')
            ->assertDontSee('Celana Basket B')
            ->assertDontSee('Celana Training A');
    }

    public function test_admin_dapat_mengubah_status_aktif_produk()
    {
        $produk = Produk::factory()->create(['is_aktif' => true]);

        $dataUpdate = [
            'nama_produk'  => $produk->nama_produk,
            'ukuran'       => $produk->ukuran,
            'warna'        => $produk->warna,
            'harga_satuan' => $produk->harga_satuan,
            'satuan'       => $produk->satuan,
            'stok'         => $produk->stok,
            'is_aktif'     => 0,
        ];

        $response = $this->actingAs($this->admin)->put('/admin/produk/' . $produk->id, $dataUpdate);

        $response->assertRedirect('/admin/produk');
        $this->assertDatabaseHas('produk', [
            'id' => $produk->id,
            'is_aktif' => false,
        ]);
    }

    public function test_admin_dapat_menggabungkan_filter_ukuran_dan_sort_stok()
    {
        Produk::factory()->create(['nama_produk' => 'Produk Normal Stok Rendah', 'ukuran' => 'normal', 'stok' => 5]);
        Produk::factory()->create(['nama_produk' => 'Produk Normal Stok Tinggi', 'ukuran' => 'normal', 'stok' => 100]);
        Produk::factory()->create(['nama_produk' => 'Produk Jumbo Stok Tinggi', 'ukuran' => 'jumbo', 'stok' => 100]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/produk?ukuran=normal&sort=stok_desc');

        $response->assertStatus(200);
        $produk = $response->viewData('produk');
        $this->assertEquals('Produk Normal Stok Tinggi', $produk->first()->nama_produk);
        $this->assertCount(2, $produk);
    }

    public function test_pencarian_tidak_menemukan_hasil()
    {
        Produk::factory()->create(['nama_produk' => 'Celana Basket']);

        $response = $this->actingAs($this->admin)
            ->get('/admin/produk?search=Sepatu');

        $response->assertStatus(200)
            ->assertSee('Data tidak ditemukan');
    }

    public function test_default_sort_adalah_terbaru()
    {
        Produk::factory()->create(['nama_produk' => 'Produk Lama', 'created_at' => now()->subDays(5)]);
        Produk::factory()->create(['nama_produk' => 'Produk Baru', 'created_at' => now()]);

        $response = $this->actingAs($this->admin)->get('/admin/produk');

        $response->assertStatus(200);
        $produk = $response->viewData('produk');
        $this->assertEquals('Produk Baru', $produk->first()->nama_produk);
    }

    public function test_validasi_nama_produk_maksimal_255_karakter()
    {
        $data = [
            'nama_produk'  => str_repeat('A', 256),
            'ukuran'       => 'normal',
            'warna'        => 'hitam',
            'harga_satuan' => 50000,
            'satuan'       => 'pcs',
            'stok'         => 10,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/produk', $data);

        $response->assertSessionHasErrors(['nama_produk']);
    }

    public function test_validasi_warna_maksimal_100_karakter()
    {
        $data = [
            'nama_produk'  => 'Test Produk',
            'ukuran'       => 'normal',
            'warna'        => str_repeat('A', 101),
            'harga_satuan' => 50000,
            'satuan'       => 'pcs',
            'stok'         => 10,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/produk', $data);

        $response->assertSessionHasErrors(['warna']);
    }

    public function test_admin_dapat_mengurutkan_berdasarkan_waktu_dengan_filter_ukuran()
    {
        Produk::factory()->create(['nama_produk' => 'Normal Lama', 'ukuran' => 'normal', 'created_at' => now()->subDays(5)]);
        Produk::factory()->create(['nama_produk' => 'Normal Baru', 'ukuran' => 'normal', 'created_at' => now()]);
        Produk::factory()->create(['nama_produk' => 'Jumbo Baru', 'ukuran' => 'jumbo', 'created_at' => now()]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/produk?ukuran=normal&sort=terlama');

        $response->assertStatus(200);
        $produk = $response->viewData('produk');
        $this->assertEquals('Normal Lama', $produk->first()->nama_produk);
        $this->assertCount(2, $produk);
    }
}
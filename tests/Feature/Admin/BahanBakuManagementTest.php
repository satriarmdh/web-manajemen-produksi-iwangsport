<?php

namespace Tests\Feature\Admin;

use App\Models\BahanBaku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BahanBakuManagementTest extends TestCase
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

    public function test_admin_dapat_melihat_halaman_katalog_bahan_baku()
    {
        $response = $this->actingAs($this->admin)->get('/admin/bahan-baku');

        $response->assertStatus(200)
            ->assertViewIs('admin.bahan-baku.index')
            ->assertViewHas('bahanBaku');
    }

    public function test_karyawan_non_admin_tidak_dapat_mengakses_halaman_katalog()
    {
        $response = $this->actingAs($this->karyawanJahit)->get('/admin/bahan-baku');

        $response->assertStatus(403);
    }

    public function test_admin_dapat_menambahkan_bahan_baku_baru()
    {
        $dataBahan = [
            'nama_bahan' => 'Kain Cotton Combed 30s',
            'warna' => 'hitam',
            'kategori' => 'kain',
            'satuan' => 'roll',
            'stok' => 0,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/bahan-baku', $dataBahan);

        $response->assertRedirect('/admin/bahan-baku');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('bahan_baku', [
            'nama_bahan' => 'Kain Cotton Combed 30s',
            'kategori' => 'kain',
        ]);

        // Pastikan kode_bahan auto-generated oleh service
        $bahan = BahanBaku::where('nama_bahan', 'Kain Cotton Combed 30s')->first();
        $this->assertNotNull($bahan->kode_bahan);
        $this->assertStringStartsWith('KAIN-', $bahan->kode_bahan);
    }

    public function test_sistem_menolak_duplikasi_kode_bahan_baku()
    {
        // Buat bahan baku pertama dengan kategori kain
        BahanBaku::factory()->create([
            'kode_bahan' => 'KAIN-001',
            'kategori' => 'kain',
        ]);

        // Coba tambahkan lagi dengan kategori yang sama — service akan auto-generate KAIN-002 (bukan duplikat)
        $dataKedua = [
            'nama_bahan' => 'Kain Berbeda',
            'warna' => 'navy',
            'kategori' => 'kain',
            'satuan' => 'roll',
            'stok' => 5,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/bahan-baku', $dataKedua);

        $response->assertRedirect('/admin/bahan-baku');

        // Harus ada 2 record karena kode auto-increment (KAIN-001, KAIN-002)
        $this->assertDatabaseCount('bahan_baku', 2);

        $bahanKedua = BahanBaku::where('nama_bahan', 'Kain Berbeda')->first();
        $this->assertEquals('KAIN-002', $bahanKedua->kode_bahan);
    }

    public function test_admin_dapat_memperbarui_data_bahan_baku()
    {
        $bahan = BahanBaku::factory()->create([
            'nama_bahan' => 'Benang Merah Lama',
            'warna' => 'merah',
            'kategori' => 'benang',
            'satuan' => 'roll',
            'stok' => 10,
        ]);

        $dataUpdate = [
            'nama_bahan' => 'Benang Jahit Merah Super',
            'warna' => 'abu',
            'kategori' => 'benang',
            'satuan' => 'pcs',
            'stok' => 10,
        ];

        $response = $this->actingAs($this->admin)->put('/admin/bahan-baku/' . $bahan->id, $dataUpdate);

        $response->assertRedirect('/admin/bahan-baku');
        $this->assertDatabaseHas('bahan_baku', [
            'id' => $bahan->id,
            'nama_bahan' => 'Benang Jahit Merah Super',
            'warna' => 'abu',
            'satuan' => 'pcs',
        ]);
    }

    public function test_admin_dapat_menghapus_bahan_baku_secara_soft_delete()
    {
        $bahan = BahanBaku::factory()->create();

        $response = $this->actingAs($this->admin)->delete('/admin/bahan-baku/' . $bahan->id);

        $response->assertRedirect('/admin/bahan-baku');

        $this->assertSoftDeleted('bahan_baku', [
            'id' => $bahan->id,
        ]);
    }

    public function test_admin_dapat_mencari_bahan_baku_berdasarkan_nama_atau_kode()
    {
        BahanBaku::factory()->create(['nama_bahan' => 'Kain Katun Premium', 'kode_bahan' => 'KAIN-001']);
        BahanBaku::factory()->create(['nama_bahan' => 'Benang Jahit', 'kode_bahan' => 'BNG-001']);

        $response = $this->actingAs($this->admin)
            ->get('/admin/bahan-baku?search=Katun');

        $response->assertStatus(200)
            ->assertSee('Kain Katun Premium')
            ->assertDontSee('Benang Jahit');
    }

    public function test_admin_dapat_memfilter_bahan_baku_berdasarkan_kategori()
    {
        BahanBaku::factory()->create(['nama_bahan' => 'Kain A', 'kategori' => 'kain']);
        BahanBaku::factory()->create(['nama_bahan' => 'Benang B', 'kategori' => 'benang']);
        BahanBaku::factory()->create(['nama_bahan' => 'Kancing C', 'kategori' => 'kancing']);

        $response = $this->actingAs($this->admin)
            ->get('/admin/bahan-baku?kategori=kain');

        $response->assertStatus(200)
            ->assertSee('Kain A')
            ->assertDontSee('Benang B')
            ->assertDontSee('Kancing C');
    }

    public function test_admin_dapat_memfilter_bahan_baku_berdasarkan_status_stok()
    {
        BahanBaku::factory()->create(['nama_bahan' => 'Bahan Tersedia', 'stok' => 10]);
        BahanBaku::factory()->create(['nama_bahan' => 'Bahan Habis', 'stok' => 0]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/bahan-baku?stok=tersedia');

        $response->assertStatus(200)
            ->assertSee('Bahan Tersedia')
            ->assertDontSee('Bahan Habis');

        $response = $this->actingAs($this->admin)
            ->get('/admin/bahan-baku?stok=habis');

        $response->assertStatus(200)
            ->assertSee('Bahan Habis')
            ->assertDontSee('Bahan Tersedia');
    }

    public function test_admin_dapat_mengurutkan_bahan_baku_berdasarkan_nama_asc()
    {
        BahanBaku::factory()->create(['nama_bahan' => 'Zebra Bahan']);
        BahanBaku::factory()->create(['nama_bahan' => 'Alpha Bahan']);

        $response = $this->actingAs($this->admin)
            ->get('/admin/bahan-baku?sort=nama_asc');

        $response->assertStatus(200);
        $bahanBaku = $response->viewData('bahanBaku');
        $this->assertEquals('Alpha Bahan', $bahanBaku->first()->nama_bahan);
    }

    public function test_admin_dapat_mengurutkan_bahan_baku_berdasarkan_stok_desc()
    {
        BahanBaku::factory()->create(['nama_bahan' => 'Stok Rendah', 'stok' => 5]);
        BahanBaku::factory()->create(['nama_bahan' => 'Stok Tinggi', 'stok' => 100]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/bahan-baku?sort=stok_desc');

        $response->assertStatus(200);
        $bahanBaku = $response->viewData('bahanBaku');
        $this->assertEquals('Stok Tinggi', $bahanBaku->first()->nama_bahan);
    }

    public function test_admin_dapat_mengurutkan_bahan_baku_berdasarkan_stok_asc()
    {
        BahanBaku::factory()->create(['nama_bahan' => 'Stok Tinggi', 'stok' => 100]);
        BahanBaku::factory()->create(['nama_bahan' => 'Stok Rendah', 'stok' => 5]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/bahan-baku?sort=stok_asc');

        $response->assertStatus(200);
        $bahanBaku = $response->viewData('bahanBaku');
        $this->assertEquals('Stok Rendah', $bahanBaku->first()->nama_bahan);
    }

    public function test_admin_dapat_mengurutkan_bahan_baku_berdasarkan_waktu_terbaru()
    {
        BahanBaku::factory()->create(['nama_bahan' => 'Bahan Lama', 'created_at' => now()->subDays(5)]);
        BahanBaku::factory()->create(['nama_bahan' => 'Bahan Baru', 'created_at' => now()]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/bahan-baku?sort=terbaru');

        $response->assertStatus(200);
        $bahanBaku = $response->viewData('bahanBaku');
        $this->assertEquals('Bahan Baru', $bahanBaku->first()->nama_bahan);
    }

    public function test_admin_dapat_mengurutkan_bahan_baku_berdasarkan_waktu_terlama()
    {
        BahanBaku::factory()->create(['nama_bahan' => 'Bahan Baru', 'created_at' => now()]);
        BahanBaku::factory()->create(['nama_bahan' => 'Bahan Lama', 'created_at' => now()->subDays(5)]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/bahan-baku?sort=terlama');

        $response->assertStatus(200);
        $bahanBaku = $response->viewData('bahanBaku');
        $this->assertEquals('Bahan Lama', $bahanBaku->first()->nama_bahan);
    }

    public function test_admin_dapat_mengurutkan_bahan_baku_berdasarkan_nama_desc()
    {
        BahanBaku::factory()->create(['nama_bahan' => 'Alpha Bahan']);
        BahanBaku::factory()->create(['nama_bahan' => 'Zebra Bahan']);

        $response = $this->actingAs($this->admin)
            ->get('/admin/bahan-baku?sort=nama_desc');

        $response->assertStatus(200);
        $bahanBaku = $response->viewData('bahanBaku');
        $this->assertEquals('Zebra Bahan', $bahanBaku->first()->nama_bahan);
    }

    public function test_halaman_index_menampilkan_pagination_ketika_data_lebih_dari_10()
    {
        BahanBaku::factory()->count(15)->create();

        $response = $this->actingAs($this->admin)->get('/admin/bahan-baku');

        $response->assertStatus(200)
            ->assertSee('Pagination Navigation')
            ->assertSee('Menampilkan');
    }

    public function test_validasi_field_wajib_diisi_saat_menambahkan_bahan_baku()
    {
        $response = $this->actingAs($this->admin)->post('/admin/bahan-baku', []);

        $response->assertSessionHasErrors(['nama_bahan', 'warna', 'kategori', 'satuan']);
    }

    public function test_validasi_stok_harus_angka_tidak_negatif()
    {
        $data = [
            'nama_bahan' => 'Test',
            'warna' => 'hitam',
            'kategori' => 'kain',
            'satuan' => 'roll',
            'stok' => -5,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/bahan-baku', $data);

        $response->assertSessionHasErrors(['stok']);
    }

    public function test_kode_bahan_auto_generate_sesuai_kategori()
    {
        $dataKain = [
            'nama_bahan' => 'Kain Test',
            'warna' => 'hitam',
            'kategori' => 'kain',
            'satuan' => 'roll',
            'stok' => 0,
        ];

        $dataBenang = [
            'nama_bahan' => 'Benang Test',
            'warna' => 'putih',
            'kategori' => 'benang',
            'satuan' => 'roll',
            'stok' => 0,
        ];

        $this->actingAs($this->admin)->post('/admin/bahan-baku', $dataKain);
        $this->actingAs($this->admin)->post('/admin/bahan-baku', $dataBenang);

        $kain = BahanBaku::where('nama_bahan', 'Kain Test')->first();
        $benang = BahanBaku::where('nama_bahan', 'Benang Test')->first();

        $this->assertEquals('KAIN-001', $kain->kode_bahan);
        $this->assertEquals('BNG-001', $benang->kode_bahan);
    }

    public function test_admin_dapat_menggabungkan_filter_dan_search()
    {
        BahanBaku::factory()->create(['nama_bahan' => 'Kain Katun A', 'kategori' => 'kain', 'stok' => 10]);
        BahanBaku::factory()->create(['nama_bahan' => 'Kain Katun B', 'kategori' => 'kain', 'stok' => 0]);
        BahanBaku::factory()->create(['nama_bahan' => 'Benang Katun', 'kategori' => 'benang', 'stok' => 10]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/bahan-baku?search=Katun&kategori=kain&stok=tersedia');

        $response->assertStatus(200)
            ->assertSee('Kain Katun A')
            ->assertDontSee('Kain Katun B')
            ->assertDontSee('Benang Katun');
    }

    public function test_default_sort_adalah_terbaru()
    {
        BahanBaku::factory()->create(['nama_bahan' => 'Bahan Lama', 'created_at' => now()->subDays(5)]);
        BahanBaku::factory()->create(['nama_bahan' => 'Bahan Baru', 'created_at' => now()]);

        $response = $this->actingAs($this->admin)->get('/admin/bahan-baku');

        $response->assertStatus(200);
        $bahanBaku = $response->viewData('bahanBaku');
        $this->assertEquals('Bahan Baru', $bahanBaku->first()->nama_bahan);
    }

    public function test_pencarian_tidak_menemukan_hasil()
    {
        BahanBaku::factory()->create(['nama_bahan' => 'Kain Katun']);

        $response = $this->actingAs($this->admin)
            ->get('/admin/bahan-baku?search=Sepatu');

        $response->assertStatus(200)
            ->assertDontSee('Kain Katun');
    }

    public function test_data_bahan_baku_yang_dihapus_tidak_muncul_di_halaman_index()
    {
        BahanBaku::factory()->create(['nama_bahan' => 'Bahan Aktif']);
        $deleted = BahanBaku::factory()->create(['nama_bahan' => 'Bahan Dihapus']);
        $deleted->delete();

        $response = $this->actingAs($this->admin)->get('/admin/bahan-baku');

        $response->assertSee('Bahan Aktif')
            ->assertDontSee('Bahan Dihapus');
    }

    public function test_admin_dapat_menggabungkan_filter_kategori_dan_sort_stok()
    {
        BahanBaku::factory()->create(['nama_bahan' => 'Kain Stok Rendah', 'kategori' => 'kain', 'stok' => 5]);
        BahanBaku::factory()->create(['nama_bahan' => 'Kain Stok Tinggi', 'kategori' => 'kain', 'stok' => 100]);
        BahanBaku::factory()->create(['nama_bahan' => 'Benang Stok Tinggi', 'kategori' => 'benang', 'stok' => 100]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/bahan-baku?kategori=kain&sort=stok_desc');

        $response->assertStatus(200);
        $bahanBaku = $response->viewData('bahanBaku');
        $this->assertEquals('Kain Stok Tinggi', $bahanBaku->first()->nama_bahan);
        $this->assertCount(2, $bahanBaku);
    }

    public function test_gagal_tambah_bahan_baku_dengan_kombinasi_nama_warna_kategori_satuan_yang_sama()
    {
        BahanBaku::factory()->create([
            'nama_bahan' => 'Diadora',
            'warna' => 'Biru',
            'kategori' => 'kain',
            'satuan' => 'roll',
        ]);

        $data = [
            'nama_bahan' => 'diadora',
            'warna' => 'biru',
            'kategori' => 'kain',
            'satuan' => 'roll',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/bahan-baku', $data);
        $response->assertSessionHasErrors(['nama_bahan']);
    }

    public function test_satuan_otomatis_mengikuti_kategori_saat_tambah_bahan_baku()
    {
        // 1. Kategori Kain -> Satuan Roll
        $dataKain = [
            'nama_bahan' => 'Combed 30s',
            'warna' => 'Hitam',
            'kategori' => 'kain',
        ];
        $this->actingAs($this->admin)->post('/admin/bahan-baku', $dataKain);
        $this->assertDatabaseHas('bahan_baku', [
            'nama_bahan' => 'Combed 30s',
            'kategori' => 'kain',
            'satuan' => 'roll',
        ]);

        // 2. Kategori Non-Kain -> Satuan Pcs
        $dataBenang = [
            'nama_bahan' => 'Benang Jahit 500yd',
            'warna' => 'Putih',
            'kategori' => 'benang',
        ];
        $this->actingAs($this->admin)->post('/admin/bahan-baku', $dataBenang);
        $this->assertDatabaseHas('bahan_baku', [
            'nama_bahan' => 'Benang Jahit 500yd',
            'kategori' => 'benang',
            'satuan' => 'pcs',
        ]);
    }
}
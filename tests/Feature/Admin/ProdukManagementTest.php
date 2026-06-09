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

        // Siapkan user dengan role Admin
        $this->admin = User::factory()->create(['role' => 'admin']);

        // Siapkan user dengan role jahit (untuk test otorisasi)
        $this->karyawanJahit = User::factory()->create(['role' => 'jahit']);
    }

    public function test_admin_dapat_mengakses_halaman_katalog_produk(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/produk');

        $response->assertStatus(200);
        $response->assertViewIs('admin.produk.index');
    }

    public function test_karyawan_non_admin_tidak_dapat_mengakses_halaman_katalog_produk(): void
    {
        $response = $this->actingAs($this->karyawanJahit)->get('/admin/produk');

        $response->assertStatus(403);
    }

    public function test_admin_dapat_menambahkan_produk_baru(): void
    {
        // kode_produk tidak dikirim — akan di-generate otomatis oleh service
        $dataProduk = [
            'nama_produk'  => 'Celana Basket',
            'kategori'     => 'basket',
            'ukuran'       => 'normal',
            'warna'        => 'hitam',
            'harga_satuan' => 30000,
            'satuan'       => 'pcs',
            'stok'        => 100,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/produk', $dataProduk);

        $response->assertRedirect('/admin/produk');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('produk', ['nama_produk' => 'Celana Basket']);
    }

    public function test_data_produk_baru_harus_valid(): void
    {
        // Sengaja mengirim data kosong
        $response = $this->actingAs($this->admin)->post('/admin/produk', []);

        // Harus memunculkan error validasi pada field wajib
        $response->assertSessionHasErrors(['nama_produk', 'ukuran', 'warna', 'harga_satuan', 'satuan']);
    }

    public function test_sistem_menolak_duplikasi_kode_produk(): void
    {
        // Buat produk awal dengan kode tertentu
        Produk::factory()->create(['kode_produk' => 'CLN-001']);

        // Coba input dengan kode_produk yang sama (user eksplisit memasukkan kode duplikat)
        $dataDuplikat = [
            'kode_produk'  => 'CLN-001',
            'nama_produk'  => 'Celana Training',
            'ukuran'       => 'normal',
            'warna'        => 'biru',
            'harga_satuan' => 90000,
            'satuan'       => 'pcs',
            'stok'         => 50,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/produk', $dataDuplikat);

        // Harus ada error pada field 'kode_produk'
        $response->assertSessionHasErrors('kode_produk');
        $this->assertDatabaseCount('produk', 1); // Data tidak boleh bertambah
    }

    public function test_admin_dapat_memperbarui_data_produk(): void
    {
        $produk = Produk::factory()->create([
            'nama_produk' => 'Celana Jogger Lama',
        ]);

        $dataUpdate = [
            'kode_produk'  => $produk->kode_produk,
            'nama_produk'  => 'Celana Jogger Premium',
            'ukuran'       => 'normal',
            'warna'        => 'hitam',
            'harga_satuan' => 55000,
            'satuan'       => 'pcs',
            'stok'         => 80,
        ];

        $response = $this->actingAs($this->admin)->put('/admin/produk/' . $produk->id, $dataUpdate);

        $response->assertRedirect('/admin/produk');
        $this->assertDatabaseHas('produk', [
            'id'          => $produk->id,
            'nama_produk' => 'Celana Jogger Premium',
        ]);
    }

    public function test_admin_dapat_menghapus_produk_secara_soft_delete(): void
    {
        $produk = Produk::factory()->create();

        $response = $this->actingAs($this->admin)->delete('/admin/produk/' . $produk->id);

        $response->assertRedirect('/admin/produk');

        // Memastikan tidak terhapus permanen dari DB, hanya ditandai deleted_at
        $this->assertSoftDeleted('produk', [
            'id' => $produk->id,
        ]);
    }
}

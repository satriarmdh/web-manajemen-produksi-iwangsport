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
        
        // Siapkan user dengan role Admin
        $this->admin = User::factory()->create(['role' => 'admin']);
        
        // Siapkan user dengan role Produksi (untuk test otorisasi)
        $this->karyawanJahit = User::factory()->create(['role' => 'jahit']);
    }

    /** @test */
    public function test_admin_dapat_melihat_halaman_katalog_bahan_baku()
    {
        $response = $this->actingAs($this->admin)->get('/admin/bahan-baku');

        $response->assertStatus(200);
        $response->assertViewIs('admin.bahan-baku.index');
    }

    /** @test */
    public function test_karyawan_non_admin_tidak_dapat_mengakses_halaman_katalog()
    {
        $response = $this->actingAs($this->karyawanJahit)->get('/admin/bahan-baku');

        // Harus dikembalikan (redirect) atau dilarang (403)
        $response->assertStatus(403); 
    }

    /** @test */
    public function test_admin_dapat_menambahkan_bahan_baku_baru()
    {
        $dataBahan = [
            'kode_bahan' => 'KAIN-001',
            'nama_bahan' => 'Kain Cotton Combed 30s',
            'warna' => 'hitam',
            'kategori' => 'kain',
            'satuan' => 'roll',
            'stok' => 0, // Default awal katalog
        ];

        $response = $this->actingAs($this->admin)->post('/admin/bahan-baku', $dataBahan);

        $response->assertRedirect('/admin/bahan-baku');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('bahan_baku', ['kode_bahan' => 'KAIN-001']);
    }

    /** @test */
    public function test_sistem_menolak_duplikasi_kode_bahan_baku()
    {
        // Buat bahan baku awal
        BahanBaku::factory()->create(['kode_bahan' => 'KAIN-001']);

        // Coba input dengan kode yang sama
        $dataDuplikat = [
            'kode_bahan' => 'KAIN-001',
            'nama_bahan' => 'Kain Berbeda',
            'warna' => 'navy',
            'kategori' => 'kain',
            'satuan' => 'roll',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/bahan-baku', $dataDuplikat);

        // Harus ada error pada field 'kode_bahan'
        $response->assertSessionHasErrors('kode_bahan');
        $this->assertDatabaseCount('bahan_baku', 1); // Data tidak boleh bertambah
    }

    /** @test */
    public function test_admin_dapat_memperbarui_data_bahan_baku()
    {
        $bahan = BahanBaku::factory()->create([
            'nama_bahan' => 'Benang Merah Lama'
        ]);

        $dataUpdate = [
            'kode_bahan' => $bahan->kode_bahan,
            'nama_bahan' => 'Benang Jahit Merah Super',
            'warna' => 'abu',
            'kategori' => 'benang',
            'satuan' => 'pcs',
        ];

        $response = $this->actingAs($this->admin)->put('/admin/bahan-baku/' . $bahan->id, $dataUpdate);

        $response->assertRedirect('/admin/bahan-baku');
        $this->assertDatabaseHas('bahan_baku', [
            'id' => $bahan->id,
            'nama_bahan' => 'Benang Jahit Merah Super'
        ]);
    }

    /** @test */
    public function test_admin_dapat_menghapus_bahan_baku_secara_soft_delete()
    {
        $bahan = BahanBaku::factory()->create();

        $response = $this->actingAs($this->admin)->delete('/admin/bahan-baku/' . $bahan->id);

        $response->assertRedirect('/admin/bahan-baku');
        
        // Memastikan tidak terhapus permanen dari DB, hanya ditandai deleted_at
        $this->assertSoftDeleted('bahan_baku', [
            'id' => $bahan->id,
        ]);
    }
}
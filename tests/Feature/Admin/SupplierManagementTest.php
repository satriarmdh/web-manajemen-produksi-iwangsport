<?php

namespace Tests\Feature\Admin;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierManagementTest extends TestCase
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

    /**
     * SCENARIO 1: Admin dapat mengakses halaman daftar supplier
     */
    public function test_admin_dapat_mengakses_halaman_daftar_supplier(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/supplier');

        $response->assertStatus(200);
        $response->assertViewIs('admin.supplier.index');
    }

    /**
     * SCENARIO 2: Non-admin tidak dapat mengakses halaman supplier
     */
    public function test_karyawan_non_admin_tidak_dapat_mengakses_halaman_supplier(): void
    {
        $response = $this->actingAs($this->karyawanJahit)->get('/admin/supplier');

        $response->assertStatus(403);
    }

    /**
     * SCENARIO 3: Admin dapat menambahkan supplier baru dengan data valid
     */
    public function test_admin_dapat_menambahkan_supplier_baru(): void
    {
        $dataSupplier = [
            'nama_supplier' => 'PT Tekstil Jaya Abadi',
            'kategori'      => ['kain', 'bahan_pendukung'],
            'kontak'        => '08123456789',
            'email'         => 'info@tekstiljaya.com',
            'alamat'        => 'Jl. Industri No. 123, Bandung',
            'catatan'       => 'Supplier kain cotton berkualitas premium',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/supplier', $dataSupplier);

        $response->assertRedirect('/admin/supplier');
        $response->assertSessionHas('success');
        
        $supplier = Supplier::where('nama_supplier', 'PT Tekstil Jaya Abadi')->first();
        $this->assertNotNull($supplier);
        $this->assertContains('kain', $supplier->kategori);
        $this->assertContains('bahan_pendukung', $supplier->kategori);
    }

    /**
     * SCENARIO 4: Sistem menolak data supplier yang tidak valid (validasi wajib)
     */
    public function test_data_supplier_baru_harus_valid(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/supplier', []);

        $response->assertSessionHasErrors([
            'nama_supplier',
            'kategori',
            'kontak',
            'email',
            'alamat',
        ]);
    }

    /**
     * SCENARIO 5: Sistem menolak email yang sudah terdaftar
     */
    public function test_sistem_menolak_email_supplier_yang_sudah_terdaftar(): void
    {
        Supplier::factory()->create(['email' => 'supplier@existing.com']);

        $dataSupplier = [
            'nama_supplier' => 'Supplier Baru',
            'kategori'      => ['bahan_pendukung'],
            'kontak'        => '08987654321',
            'email'         => 'supplier@existing.com',
            'alamat'        => 'Jl. Baru No. 456',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/supplier', $dataSupplier);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('suppliers', 1);
    }

    /**
     * SCENARIO 6: Sistem menolak kode supplier duplikat
     */
    public function test_sistem_menolak_kode_supplier_duplikat(): void
    {
        Supplier::factory()->create(['kode_supplier' => 'SUP-001']);

        $dataDuplikat = [
            'kode_supplier' => 'SUP-001',
            'nama_supplier' => 'Supplier Berbeda',
            'kategori'      => ['bahan_pendukung'],
            'kontak'        => '08111222333',
            'email'         => 'berbeda@supplier.com',
            'alamat'        => 'Jl. Berbeda No. 789',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/supplier', $dataDuplikat);

        $response->assertSessionHasErrors('kode_supplier');
        $this->assertDatabaseCount('suppliers', 1);
    }

    /**
     * SCENARIO 7: Admin dapat memperbarui data supplier
     */
    public function test_admin_dapat_memperbarui_data_supplier(): void
    {
        $supplier = Supplier::factory()->create([
            'nama_supplier' => 'Supplier Lama',
            'kategori'      => ['kain'],
        ]);

        $dataUpdate = [
            'nama_supplier' => 'Supplier Premium',
            'kategori'      => ['bahan_pendukung'],
            'kontak'        => '08999888777',
            'email'         => 'premium@supplier.com',
            'alamat'        => 'Jl. Premium No. 100',
            'catatan'       => 'Supplier bahan premium untuk produk eksklusif',
            'is_aktif'      => 1,
        ];

        $response = $this->actingAs($this->admin)->put('/admin/supplier/' . $supplier->id, $dataUpdate);

        $response->assertRedirect('/admin/supplier');

        $this->assertDatabaseHas('suppliers', [
            'id'            => $supplier->id,
            'nama_supplier' => 'Supplier Premium',
        ]);

        $updated = Supplier::find($supplier->id);
        $this->assertContains('bahan_pendukung', $updated->kategori);
    }

    /**
     * SCENARIO 8: Admin dapat mengubah status supplier (aktif/nonaktif)
     */
    public function test_admin_dapat_mengubah_status_supplier(): void
    {
        $supplier = Supplier::factory()->create(['is_aktif' => true]);

        $dataUpdate = [
            'nama_supplier' => $supplier->nama_supplier,
            'kategori'      => $supplier->kategori,
            'kontak'        => $supplier->kontak,
            'email'         => $supplier->email,
            'alamat'        => $supplier->alamat,
            'is_aktif'      => 0,
        ];

        $response = $this->actingAs($this->admin)->put('/admin/supplier/' . $supplier->id, $dataUpdate);

        $response->assertRedirect('/admin/supplier');

        $this->assertDatabaseHas('suppliers', [
            'id'       => $supplier->id,
            'is_aktif' => false,
        ]);
    }

    /**
     * SCENARIO 9: Admin dapat menghapus supplier (soft delete)
     */
    public function test_admin_dapat_menghapus_supplier_secara_soft_delete(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->admin)->delete('/admin/supplier/' . $supplier->id);

        $response->assertRedirect('/admin/supplier');

        $this->assertSoftDeleted('suppliers', [
            'id' => $supplier->id,
        ]);
    }

    /**
     * SCENARIO 10: Sistem menghasilkan kode supplier otomatis jika tidak diisi
     */
    public function test_sistem_generate_kode_supplier_otomatis(): void
    {
        $dataSupplier = [
            'nama_supplier' => 'Supplier Auto Code',
            'kategori'      => ['kain'],
            'kontak'        => '08123123123',
            'email'         => 'autocode@supplier.com',
            'alamat'        => 'Jl. Auto No. 1',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/supplier', $dataSupplier);

        $response->assertRedirect('/admin/supplier');
        
        // Pastikan kode supplier ter-generate (format: SUP-001, SUP-002, dst)
        $supplier = Supplier::where('nama_supplier', 'Supplier Auto Code')->first();
        $this->assertNotNull($supplier);
        $this->assertNotNull($supplier->kode_supplier);
        $this->assertStringStartsWith('SUP-', $supplier->kode_supplier);
    }

    public function test_admin_dapat_mencari_supplier_berdasarkan_nama(): void
    {
        Supplier::factory()->create(['nama_supplier' => 'PT Tekstil Jaya']);
        Supplier::factory()->create(['nama_supplier' => 'CV Benang Berkualitas']);
        Supplier::factory()->create(['nama_supplier' => 'UD Kancing Murah']);

        $response = $this->actingAs($this->admin)->get('/admin/supplier?search=Tekstil');

        $response->assertStatus(200);
        $response->assertSee('PT Tekstil Jaya');
        $response->assertDontSee('CV Benang Berkualitas');
        $response->assertDontSee('UD Kancing Murah');
    }

    /**
     * SCENARIO 12: Admin dapat memfilter supplier berdasarkan kategori
     */
    public function test_admin_dapat_memfilter_supplier_berdasarkan_kategori(): void
    {
        Supplier::factory()->create(['nama_supplier' => 'Supplier Kain A', 'kategori' => ['kain']]);
        Supplier::factory()->create(['nama_supplier' => 'Supplier Pendukung B', 'kategori' => ['bahan_pendukung']]);
        Supplier::factory()->create(['nama_supplier' => 'Supplier Kain C', 'kategori' => ['kain']]);

        $response = $this->actingAs($this->admin)->get('/admin/supplier?kategori=kain');

        $response->assertStatus(200);
        $response->assertSee('Supplier Kain A');
        $response->assertSee('Supplier Kain C');
        $response->assertDontSee('Supplier Pendukung B');
    }

    /**
     * SCENARIO 13: Admin dapat memfilter supplier berdasarkan status
     */
    public function test_admin_dapat_memfilter_supplier_berdasarkan_status(): void
    {
        Supplier::factory()->create(['nama_supplier' => 'Supplier Aktif 1', 'is_aktif' => true]);
        Supplier::factory()->create(['nama_supplier' => 'Supplier Aktif 2', 'is_aktif' => true]);
        Supplier::factory()->create(['nama_supplier' => 'Supplier Nonaktif', 'is_aktif' => false]);

        $response = $this->actingAs($this->admin)->get('/admin/supplier?status=aktif');

        $response->assertStatus(200);
        $response->assertSee('Supplier Aktif 1');
        $response->assertSee('Supplier Aktif 2');
        $response->assertDontSee('Supplier Nonaktif');
    }

    /**
     * SCENARIO 14: Admin dapat mengurutkan supplier berdasarkan nama
     */
    public function test_admin_dapat_mengurutkan_supplier_berdasarkan_nama(): void
    {
        Supplier::factory()->create(['nama_supplier' => 'Zebra Supplier']);
        Supplier::factory()->create(['nama_supplier' => 'Alpha Supplier']);
        Supplier::factory()->create(['nama_supplier' => 'Middle Supplier']);

        $response = $this->actingAs($this->admin)->get('/admin/supplier?sort=nama_asc');

        $response->assertStatus(200);
        $response->assertSeeInOrder(['Alpha Supplier', 'Middle Supplier', 'Zebra Supplier']);
    }

    public function test_sistem_auto_generate_kode_supplier_sequential()
    {
        $service = new \App\Services\SupplierService();
        
        $supplier1 = Supplier::create([
            'nama_supplier' => 'Supplier Pertama',
            'kategori' => ['kain'],
            'kontak' => '08111111111',
            'email' => 'supplier1@test.com',
            'alamat' => 'Alamat 1',
            'kode_supplier' => $service->generateKode(),
        ]);
        
        $supplier2 = Supplier::create([
            'nama_supplier' => 'Supplier Kedua',
            'kategori' => ['bahan_pendukung'],
            'kontak' => '08222222222',
            'email' => 'supplier2@test.com',
            'alamat' => 'Alamat 2',
            'kode_supplier' => $service->generateKode(),
        ]);
        
        $supplier3 = Supplier::create([
            'nama_supplier' => 'Supplier Ketiga',
            'kategori' => ['bahan_pendukung'],
            'kontak' => '08333333333',
            'email' => 'supplier3@test.com',
            'alamat' => 'Alamat 3',
            'kode_supplier' => $service->generateKode(),
        ]);

        $this->assertEquals('SUP-001', $supplier1->kode_supplier);
        $this->assertEquals('SUP-002', $supplier2->kode_supplier);
        $this->assertEquals('SUP-003', $supplier3->kode_supplier);
    }

    public function test_admin_dapat_mengurutkan_supplier_berdasarkan_nama_desc(): void
    {
        Supplier::factory()->create(['nama_supplier' => 'Alpha Supplier']);
        Supplier::factory()->create(['nama_supplier' => 'Zebra Supplier']);

        $response = $this->actingAs($this->admin)->get('/admin/supplier?sort=nama_desc');

        $response->assertStatus(200);
        $suppliers = $response->viewData('suppliers');
        $this->assertEquals('Zebra Supplier', $suppliers->first()->nama_supplier);
    }

    public function test_admin_dapat_mengurutkan_supplier_berdasarkan_waktu_terbaru(): void
    {
        Supplier::factory()->create(['nama_supplier' => 'Supplier Lama', 'created_at' => now()->subDays(5)]);
        Supplier::factory()->create(['nama_supplier' => 'Supplier Baru', 'created_at' => now()]);

        $response = $this->actingAs($this->admin)->get('/admin/supplier?sort=newest');

        $response->assertStatus(200);
        $suppliers = $response->viewData('suppliers');
        $this->assertEquals('Supplier Baru', $suppliers->first()->nama_supplier);
    }

    public function test_admin_dapat_mengurutkan_supplier_berdasarkan_waktu_terlama(): void
    {
        Supplier::factory()->create(['nama_supplier' => 'Supplier Baru', 'created_at' => now()]);
        Supplier::factory()->create(['nama_supplier' => 'Supplier Lama', 'created_at' => now()->subDays(5)]);

        $response = $this->actingAs($this->admin)->get('/admin/supplier?sort=oldest');

        $response->assertStatus(200);
        $suppliers = $response->viewData('suppliers');
        $this->assertEquals('Supplier Lama', $suppliers->first()->nama_supplier);
    }

    public function test_default_sort_adalah_terbaru(): void
    {
        Supplier::factory()->create(['nama_supplier' => 'Supplier Lama', 'created_at' => now()->subDays(5)]);
        Supplier::factory()->create(['nama_supplier' => 'Supplier Baru', 'created_at' => now()]);

        $response = $this->actingAs($this->admin)->get('/admin/supplier');

        $response->assertStatus(200);
        $suppliers = $response->viewData('suppliers');
        $this->assertEquals('Supplier Baru', $suppliers->first()->nama_supplier);
    }

    public function test_halaman_index_menampilkan_pagination_ketika_data_lebih_dari_10(): void
    {
        Supplier::factory()->count(15)->create();

        $response = $this->actingAs($this->admin)->get('/admin/supplier');

        $response->assertStatus(200)
            ->assertSee('Pagination Navigation')
            ->assertSee('Menampilkan');
    }

    public function test_pencarian_tidak_menemukan_hasil(): void
    {
        Supplier::factory()->create(['nama_supplier' => 'CV Makmur Sejahtera']);

        $response = $this->actingAs($this->admin)->get('/admin/supplier?search=Zzzzzz');

        $response->assertStatus(200);
        $suppliers = $response->viewData('suppliers');
        $this->assertCount(0, $suppliers);
    }

    public function test_data_supplier_yang_dihapus_tidak_muncul_di_halaman_index(): void
    {
        Supplier::factory()->create(['nama_supplier' => 'Supplier Aktif']);
        $deleted = Supplier::factory()->create(['nama_supplier' => 'Supplier Dihapus']);
        $deleted->delete();

        $response = $this->actingAs($this->admin)->get('/admin/supplier');

        $response->assertSee('Supplier Aktif')
            ->assertDontSee('Supplier Dihapus');
    }

    public function test_admin_dapat_menggabungkan_filter_dan_search(): void
    {
        Supplier::factory()->create(['nama_supplier' => 'Supplier Kain Aktif', 'kategori' => ['kain'], 'is_aktif' => true]);
        Supplier::factory()->create(['nama_supplier' => 'Supplier Kain Nonaktif', 'kategori' => ['kain'], 'is_aktif' => false]);
        Supplier::factory()->create(['nama_supplier' => 'Supplier Pendukung Aktif', 'kategori' => ['bahan_pendukung'], 'is_aktif' => true]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/supplier?search=Supplier Kain&status=aktif');

        $response->assertStatus(200)
            ->assertSee('Supplier Kain Aktif')
            ->assertDontSee('Supplier Kain Nonaktif')
            ->assertDontSee('Supplier Pendukung Aktif');
    }

    public function test_admin_dapat_menggabungkan_filter_kategori_dan_sort(): void
    {
        Supplier::factory()->create(['nama_supplier' => 'Zebra Kain', 'kategori' => ['kain'], 'created_at' => now()]);
        Supplier::factory()->create(['nama_supplier' => 'Alpha Kain', 'kategori' => ['kain'], 'created_at' => now()->subDay()]);
        Supplier::factory()->create(['nama_supplier' => 'Beta Pendukung', 'kategori' => ['bahan_pendukung'], 'created_at' => now()]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/supplier?kategori=kain&sort=nama_asc');

        $response->assertStatus(200);
        $suppliers = $response->viewData('suppliers');
        $this->assertEquals('Alpha Kain', $suppliers->first()->nama_supplier);
        $this->assertCount(2, $suppliers);
    }

    public function test_dapat_membuat_supplier_baru_dengan_email_yang_sama_setelah_di_soft_delete(): void
    {
        $supplier1 = Supplier::factory()->create([
            'email' => 'satria0010101@gmail.com',
            'kode_supplier' => 'SUP-001',
        ]);

        // Soft delete supplier pertama
        $this->actingAs($this->admin)->delete('/admin/supplier/' . $supplier1->id);
        $this->assertSoftDeleted('suppliers', ['id' => $supplier1->id]);

        // Re-create supplier dengan email yang sama persis
        $response = $this->actingAs($this->admin)->post('/admin/supplier', [
            'nama_supplier' => 'Supplier Baru',
            'email' => 'satria0010101@gmail.com',
            'kontak' => '08123456789',
            'alamat' => 'Alamat Baru',
            'kategori' => ['kain'],
            'is_aktif' => 1,
        ]);

        $response->assertRedirect('/admin/supplier');
        $this->assertDatabaseHas('suppliers', [
            'email' => 'satria0010101@gmail.com',
            'deleted_at' => null,
        ]);
    }
}

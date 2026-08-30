<?php

namespace Tests\Feature\Admin;

use App\Models\Pelanggan;
use App\Models\User;
use Database\Factories\PelangganFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PelangganManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $karyawanJahit;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->karyawanJahit = User::factory()->create(['role' => 'jahit']);
        PelangganFactory::resetCounter();
    }

    // ============================================
    // VIEW & ACCESS
    // ============================================

    public function test_admin_dapat_mengakses_halaman_daftar_pelanggan()
    {
        $response = $this->actingAs($this->admin)->get('/admin/pelanggan');

        $response->assertStatus(200)
            ->assertViewIs('admin.pelanggan.index')
            ->assertViewHas('pelanggan');
    }

    public function test_karyawan_non_admin_tidak_dapat_mengakses_halaman_pelanggan()
    {
        $response = $this->actingAs($this->karyawanJahit)->get('/admin/pelanggan');

        $response->assertStatus(403);
    }

    // ============================================
    // CREATE
    // ============================================

    public function test_admin_dapat_menambahkan_pelanggan_baru()
    {
        $data = [
            'nama_pelanggan' => 'Pelanggan Baru',
            'no_telp' => '081234567890',
            'email' => 'pelanggan@example.com',
            'alamat' => 'Jl. Merdeka No. 123',
            'keterangan' => 'Pelanggan VIP',
            'is_aktif' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pelanggan', $data);

        $response->assertRedirect('/admin/pelanggan')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pelanggan', [
            'nama_pelanggan' => 'Pelanggan Baru',
            'email' => 'pelanggan@example.com',
        ]);

        // Pastikan kode_pelanggan auto-generated
        $pelanggan = Pelanggan::where('email', 'pelanggan@example.com')->first();
        $this->assertNotNull($pelanggan->kode_pelanggan);
        $this->assertStringStartsWith('PLG-', $pelanggan->kode_pelanggan);
    }

    public function test_sistem_auto_generate_kode_pelanggan_sequential()
    {
        $pelanggan1 = Pelanggan::factory()->create();
        $pelanggan2 = Pelanggan::factory()->create();
        $pelanggan3 = Pelanggan::factory()->create();

        $this->assertEquals('PLG-001', $pelanggan1->kode_pelanggan);
        $this->assertEquals('PLG-002', $pelanggan2->kode_pelanggan);
        $this->assertEquals('PLG-003', $pelanggan3->kode_pelanggan);
    }

    // ============================================
    // UPDATE
    // ============================================

    public function test_admin_dapat_memperbarui_data_pelanggan()
    {
        $pelanggan = Pelanggan::factory()->create([
            'nama_pelanggan' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $data = [
            'nama_pelanggan' => 'New Name',
            'no_telp' => '089876543210',
            'email' => 'new@example.com',
            'alamat' => 'New Address',
            'keterangan' => 'Updated',
            'is_aktif' => false,
        ];

        $response = $this->actingAs($this->admin)
            ->put("/admin/pelanggan/{$pelanggan->id}", $data);

        $response->assertRedirect('/admin/pelanggan')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pelanggan', [
            'id' => $pelanggan->id,
            'nama_pelanggan' => 'New Name',
            'email' => 'new@example.com',
            'is_aktif' => false,
        ]);
    }

    // ============================================
    // DELETE
    // ============================================

    public function test_admin_dapat_menghapus_pelanggan_secara_soft_delete()
    {
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete("/admin/pelanggan/{$pelanggan->id}");

        $response->assertRedirect('/admin/pelanggan')
            ->assertSessionHas('success');

        $this->assertSoftDeleted('pelanggan', ['id' => $pelanggan->id]);
    }

    public function test_data_pelanggan_yang_dihapus_tidak_muncul_di_halaman_index()
    {
        Pelanggan::factory()->create(['nama_pelanggan' => 'Pelanggan Aktif']);
        $deleted = Pelanggan::factory()->create(['nama_pelanggan' => 'Pelanggan Dihapus']);
        $deleted->delete();

        $response = $this->actingAs($this->admin)->get('/admin/pelanggan');

        $response->assertSee('Pelanggan Aktif')
            ->assertDontSee('Pelanggan Dihapus');
    }

    // ============================================
    // SEARCH
    // ============================================

    public function test_admin_dapat_mencari_pelanggan_berdasarkan_nama_atau_kode()
    {
        Pelanggan::factory()->create(['nama_pelanggan' => 'Budi Santoso', 'kode_pelanggan' => 'PLG-001']);
        Pelanggan::factory()->create(['nama_pelanggan' => 'Ani Wijaya', 'kode_pelanggan' => 'PLG-002']);

        // Search by nama
        $response = $this->actingAs($this->admin)
            ->get('/admin/pelanggan?search=Budi');

        $response->assertStatus(200)
            ->assertSee('Budi Santoso')
            ->assertDontSee('Ani Wijaya');

        // Search by kode
        $response = $this->actingAs($this->admin)
            ->get('/admin/pelanggan?search=PLG-002');

        $response->assertStatus(200)
            ->assertSee('Ani Wijaya')
            ->assertDontSee('Budi Santoso');
    }

    public function test_pencarian_tidak_menemukan_hasil()
    {
        Pelanggan::factory()->create(['nama_pelanggan' => 'Budi Santoso']);

        $response = $this->actingAs($this->admin)
            ->get('/admin/pelanggan?search=Zzzzzz');

        $response->assertStatus(200)
            ->assertDontSee('Budi Santoso');
    }

    // ============================================
    // FILTER
    // ============================================

    public function test_admin_dapat_memfilter_pelanggan_berdasarkan_status()
    {
        Pelanggan::factory()->create(['nama_pelanggan' => 'Pelanggan Aktif', 'is_aktif' => true]);
        Pelanggan::factory()->create(['nama_pelanggan' => 'Pelanggan Nonaktif', 'is_aktif' => false]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/pelanggan?status=aktif');

        $response->assertStatus(200)
            ->assertSee('Pelanggan Aktif')
            ->assertDontSee('Pelanggan Nonaktif');
    }

    // ============================================
    // SORT
    // ============================================

    public function test_admin_dapat_mengurutkan_pelanggan_berdasarkan_nama_asc()
    {
        Pelanggan::factory()->create(['nama_pelanggan' => 'Zebra Pelanggan']);
        Pelanggan::factory()->create(['nama_pelanggan' => 'Alpha Pelanggan']);

        $response = $this->actingAs($this->admin)
            ->get('/admin/pelanggan?sort=nama_asc');

        $response->assertStatus(200);
        $pelanggan = $response->viewData('pelanggan');
        $this->assertEquals('Alpha Pelanggan', $pelanggan->first()->nama_pelanggan);
    }

    public function test_admin_dapat_mengurutkan_pelanggan_berdasarkan_nama_desc()
    {
        Pelanggan::factory()->create(['nama_pelanggan' => 'Alpha Pelanggan']);
        Pelanggan::factory()->create(['nama_pelanggan' => 'Zebra Pelanggan']);

        $response = $this->actingAs($this->admin)
            ->get('/admin/pelanggan?sort=nama_desc');

        $response->assertStatus(200);
        $pelanggan = $response->viewData('pelanggan');
        $this->assertEquals('Zebra Pelanggan', $pelanggan->first()->nama_pelanggan);
    }

    public function test_admin_dapat_mengurutkan_pelanggan_berdasarkan_waktu_terbaru()
    {
        Pelanggan::factory()->create(['nama_pelanggan' => 'Pelanggan Lama', 'created_at' => now()->subDays(5)]);
        Pelanggan::factory()->create(['nama_pelanggan' => 'Pelanggan Baru', 'created_at' => now()]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/pelanggan?sort=terbaru');

        $response->assertStatus(200);
        $pelanggan = $response->viewData('pelanggan');
        $this->assertEquals('Pelanggan Baru', $pelanggan->first()->nama_pelanggan);
    }

    public function test_admin_dapat_mengurutkan_pelanggan_berdasarkan_waktu_terlama()
    {
        Pelanggan::factory()->create(['nama_pelanggan' => 'Pelanggan Baru', 'created_at' => now()]);
        Pelanggan::factory()->create(['nama_pelanggan' => 'Pelanggan Lama', 'created_at' => now()->subDays(5)]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/pelanggan?sort=terlama');

        $response->assertStatus(200);
        $pelanggan = $response->viewData('pelanggan');
        $this->assertEquals('Pelanggan Lama', $pelanggan->first()->nama_pelanggan);
    }

    public function test_default_sort_adalah_terbaru()
    {
        Pelanggan::factory()->create(['nama_pelanggan' => 'Pelanggan Lama', 'created_at' => now()->subDays(5)]);
        Pelanggan::factory()->create(['nama_pelanggan' => 'Pelanggan Baru', 'created_at' => now()]);

        $response = $this->actingAs($this->admin)->get('/admin/pelanggan');

        $response->assertStatus(200);
        $pelanggan = $response->viewData('pelanggan');
        $this->assertEquals('Pelanggan Baru', $pelanggan->first()->nama_pelanggan);
    }

    // ============================================
    // PAGINATION
    // ============================================

    public function test_halaman_index_menampilkan_pagination_ketika_data_lebih_dari_10()
    {
        Pelanggan::factory()->count(15)->create();

        $response = $this->actingAs($this->admin)->get('/admin/pelanggan');

        $response->assertStatus(200)
            ->assertSee('Pagination Navigation')
            ->assertSee('Menampilkan');
    }

    // ============================================
    // VALIDATION
    // ============================================

    public function test_validasi_field_wajib_diisi_saat_menambahkan_pelanggan()
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/pelanggan', []);

        $response->assertSessionHasErrors([
            'nama_pelanggan',
            'no_telp',
            'email',
            'alamat',
        ]);
    }

    public function test_validasi_email_harus_format_valid()
    {
        $data = [
            'nama_pelanggan' => 'Test',
            'no_telp' => '081234567890',
            'email' => 'invalid-email',
            'alamat' => 'Test Address',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pelanggan', $data);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_validasi_email_harus_unik()
    {
        Pelanggan::factory()->create(['email' => 'existing@example.com']);

        $data = [
            'nama_pelanggan' => 'Test',
            'no_telp' => '081234567890',
            'email' => 'existing@example.com',
            'alamat' => 'Test Address',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pelanggan', $data);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_validasi_nomor_telepon_harus_format_valid()
    {
        $data = [
            'nama_pelanggan' => 'Test',
            'no_telp' => 'invalid',
            'email' => 'test@example.com',
            'alamat' => 'Test Address',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/pelanggan', $data);

        $response->assertSessionHasErrors(['no_telp']);
    }

    // ============================================
    // STATUS
    // ============================================

    public function test_admin_dapat_mengubah_status_aktif_pelanggan()
    {
        $pelanggan = Pelanggan::factory()->create(['is_aktif' => true]);

        $data = [
            'nama_pelanggan' => $pelanggan->nama_pelanggan,
            'no_telp' => $pelanggan->no_telp,
            'email' => $pelanggan->email,
            'alamat' => $pelanggan->alamat,
            'is_aktif' => false,
        ];

        $response = $this->actingAs($this->admin)
            ->put("/admin/pelanggan/{$pelanggan->id}", $data);

        $response->assertRedirect('/admin/pelanggan');
        $this->assertDatabaseHas('pelanggan', [
            'id' => $pelanggan->id,
            'is_aktif' => false,
        ]);
    }

    // ============================================
    // COMBINED FILTERS
    // ============================================

    public function test_admin_dapat_menggabungkan_filter_dan_search()
    {
        Pelanggan::factory()->create(['nama_pelanggan' => 'Budi Aktif', 'email' => 'budi.aktif@example.com', 'is_aktif' => true]);
        Pelanggan::factory()->create(['nama_pelanggan' => 'Budi Nonaktif', 'email' => 'budi.nonaktif@example.com', 'is_aktif' => false]);
        Pelanggan::factory()->create(['nama_pelanggan' => 'Ani Aktif', 'email' => 'ani.aktif@example.com', 'is_aktif' => true]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/pelanggan?search=Budi&status=aktif');

        $response->assertStatus(200)
            ->assertSee('Budi Aktif')
            ->assertDontSee('Budi Nonaktif')
            ->assertDontSee('Ani Aktif');
    }

    public function test_admin_dapat_menggabungkan_filter_status_dan_sort()
    {
        Pelanggan::factory()->create(['nama_pelanggan' => 'Zebra Aktif', 'is_aktif' => true, 'created_at' => now()]);
        Pelanggan::factory()->create(['nama_pelanggan' => 'Alpha Aktif', 'is_aktif' => true, 'created_at' => now()->subDay()]);
        Pelanggan::factory()->create(['nama_pelanggan' => 'Beta Nonaktif', 'is_aktif' => false, 'created_at' => now()]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/pelanggan?status=aktif&sort=nama_asc');

        $response->assertStatus(200);
        $pelanggan = $response->viewData('pelanggan');
        $this->assertEquals('Alpha Aktif', $pelanggan->first()->nama_pelanggan);
        $this->assertCount(2, $pelanggan);
    }

    public function test_dapat_membuat_pelanggan_baru_dengan_email_yang_sama_setelah_di_soft_delete(): void
    {
        $pelanggan1 = Pelanggan::factory()->create([
            'email' => 'pelanggan.lama@example.com',
        ]);

        // Soft delete pelanggan
        $this->actingAs($this->admin)->delete('/admin/pelanggan/' . $pelanggan1->id);
        $this->assertSoftDeleted('pelanggan', ['id' => $pelanggan1->id]);

        // Re-create pelanggan dengan email yang sama persis
        $response = $this->actingAs($this->admin)->post('/admin/pelanggan', [
            'nama_pelanggan' => 'Pelanggan Baru',
            'email' => 'pelanggan.lama@example.com',
            'no_telp' => '081234567899',
            'alamat' => 'Alamat Baru',
            'is_aktif' => 1,
        ]);

        $response->assertRedirect('/admin/pelanggan');
        $this->assertDatabaseHas('pelanggan', [
            'email' => 'pelanggan.lama@example.com',
            'deleted_at' => null,
        ]);
    }
}

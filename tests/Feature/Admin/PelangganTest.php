<?php

namespace Tests\Feature\Admin;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PelangganTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function index_displays_pelanggan_list_with_pagination()
    {
        Pelanggan::factory()->count(15)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pelanggan.index'));

        $response->assertStatus(200)
            ->assertViewIs('admin.pelanggan.index')
            ->assertViewHas('pelanggan')
            ->assertSee('Daftar Pelanggan');
    }

    /** @test */
    public function index_can_search_pelanggan_by_name()
    {
        Pelanggan::factory()->create(['nama_pelanggan' => 'Budi Santoso']);
        Pelanggan::factory()->create(['nama_pelanggan' => 'Ani Wijaya']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pelanggan.index', ['search' => 'Budi']));

        $response->assertStatus(200)
            ->assertSee('Budi Santoso')
            ->assertDontSee('Ani Wijaya');
    }

    /** @test */
    public function index_can_filter_pelanggan_by_status()
    {
        Pelanggan::factory()->create(['nama_pelanggan' => 'Aktif Customer', 'is_aktif' => true]);
        Pelanggan::factory()->create(['nama_pelanggan' => 'Nonaktif Customer', 'is_aktif' => false]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pelanggan.index', ['status' => 'aktif']));

        $response->assertStatus(200)
            ->assertSee('Aktif Customer')
            ->assertDontSee('Nonaktif Customer');
    }

    /** @test */
    public function index_can_sort_pelanggan_by_name()
    {
        Pelanggan::factory()->create(['nama_pelanggan' => 'Zebra']);
        Pelanggan::factory()->create(['nama_pelanggan' => 'Alpha']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pelanggan.index', ['sort' => 'nama_asc']));

        $response->assertStatus(200);
        $pelanggan = $response->viewData('pelanggan');
        $this->assertEquals('Alpha', $pelanggan->first()->nama_pelanggan);
    }

    /** @test */
    public function store_creates_new_pelanggan_with_valid_data()
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
            ->post(route('admin.pelanggan.store'), $data);

        $response->assertRedirect(route('admin.pelanggan.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pelanggan', [
            'nama_pelanggan' => 'Pelanggan Baru',
            'email' => 'pelanggan@example.com',
        ]);

        $pelanggan = Pelanggan::where('email', 'pelanggan@example.com')->first();
        $this->assertNotNull($pelanggan->kode_pelanggan);
        $this->assertStringStartsWith('PLG-', $pelanggan->kode_pelanggan);
    }

    /** @test */
    public function store_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.pelanggan.store'), []);

        $response->assertSessionHasErrors([
            'nama_pelanggan',
            'no_telp',
            'email',
            'alamat',
        ]);
    }

    /** @test */
    public function store_validates_email_format()
    {
        $data = [
            'nama_pelanggan' => 'Test',
            'no_telp' => '081234567890',
            'email' => 'invalid-email',
            'alamat' => 'Test Address',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pelanggan.store'), $data);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function store_validates_unique_email()
    {
        Pelanggan::factory()->create(['email' => 'existing@example.com']);

        $data = [
            'nama_pelanggan' => 'Test',
            'no_telp' => '081234567890',
            'email' => 'existing@example.com',
            'alamat' => 'Test Address',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pelanggan.store'), $data);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function store_validates_phone_number_format()
    {
        $data = [
            'nama_pelanggan' => 'Test',
            'no_telp' => 'invalid',
            'email' => 'test@example.com',
            'alamat' => 'Test Address',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pelanggan.store'), $data);

        $response->assertSessionHasErrors(['no_telp']);
    }

    /** @test */
    public function update_modifies_pelanggan_data()
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
            ->put(route('admin.pelanggan.update', $pelanggan), $data);

        $response->assertRedirect(route('admin.pelanggan.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pelanggan', [
            'id' => $pelanggan->id,
            'nama_pelanggan' => 'New Name',
            'email' => 'new@example.com',
            'is_aktif' => false,
        ]);
    }

    /** @test */
    public function update_validates_unique_email_except_current()
    {
        $pelanggan1 = Pelanggan::factory()->create(['email' => 'first@example.com']);
        $pelanggan2 = Pelanggan::factory()->create(['email' => 'second@example.com']);

        $data = [
            'nama_pelanggan' => 'Test',
            'no_telp' => '081234567890',
            'email' => 'first@example.com', // Already exists in pelanggan1
            'alamat' => 'Test Address',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.pelanggan.update', $pelanggan2), $data);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function destroy_soft_deletes_pelanggan()
    {
        $pelanggan = Pelanggan::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.pelanggan.destroy', $pelanggan));

        $response->assertRedirect(route('admin.pelanggan.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('pelanggan', ['id' => $pelanggan->id]);
    }

    /** @test */
    public function show_displays_pelanggan_details()
    {
        $pelanggan = Pelanggan::factory()->create([
            'nama_pelanggan' => 'Detail Customer',
            'email' => 'detail@example.com',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pelanggan.show', $pelanggan));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $pelanggan->id,
                'nama_pelanggan' => 'Detail Customer',
                'email' => 'detail@example.com',
            ]);
    }

    /** @test */
    public function auto_generates_sequential_kode_pelanggan()
    {
        $pelanggan1 = Pelanggan::factory()->create();
        $pelanggan2 = Pelanggan::factory()->create();
        $pelanggan3 = Pelanggan::factory()->create();

        $this->assertEquals('PLG-001', $pelanggan1->kode_pelanggan);
        $this->assertEquals('PLG-002', $pelanggan2->kode_pelanggan);
        $this->assertEquals('PLG-003', $pelanggan3->kode_pelanggan);
    }

    /** @test */
    public function index_excludes_soft_deleted_pelanggan()
    {
        $active = Pelanggan::factory()->create(['nama_pelanggan' => 'Active Customer']);
        $deleted = Pelanggan::factory()->create(['nama_pelanggan' => 'Deleted Customer']);
        $deleted->delete();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pelanggan.index'));

        $response->assertSee('Active Customer')
            ->assertDontSee('Deleted Customer');
    }
}

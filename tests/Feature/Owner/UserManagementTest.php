<?php

namespace Tests\Feature\Owner;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create(['role' => 'owner']);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_owner_dapat_mengakses_halaman_manajemen_pengguna()
    {
        $response = $this->actingAs($this->owner)->get('/owner/users');

        $response->assertStatus(200)
            ->assertViewIs('owner.manajemen-pengguna.index')
            ->assertViewHas('users');
    }

    public function test_admin_tidak_dapat_mengakses_halaman_manajemen_pengguna()
    {
        $response = $this->actingAs($this->admin)->get('/owner/users');

        $response->assertStatus(403);
    }

    public function test_owner_dapat_menambahkan_pengguna_baru()
    {
        $userData = [
            'name' => 'Budi Penjahit',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'jahit',
            'jenis_kelamin' => 'Laki-laki',
        ];

        $response = $this->actingAs($this->owner)
            ->post('/owner/users', $userData);

        $response->assertRedirect('/owner/users')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
            'role' => 'jahit',
            'jenis_kelamin' => 'Laki-laki',
        ]);
    }

    public function test_owner_dapat_mengubah_peran_pengguna_yang_ada()
    {
        $karyawan = User::factory()->create([
            'name' => 'Siti',
            'email' => 'siti@example.com',
            'role' => 'jahit',
            'jenis_kelamin' => 'Perempuan',
        ]);

        $updateData = [
            'name' => 'Siti (Promoted)',
            'email' => 'siti@example.com',
            'role' => 'admin',
            'jenis_kelamin' => 'Perempuan',
        ];

        $response = $this->actingAs($this->owner)
            ->put("/owner/users/{$karyawan->id}", $updateData);

        $response->assertRedirect('/owner/users')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $karyawan->id,
            'name' => 'Siti (Promoted)',
            'role' => 'admin',
        ]);
    }

    public function test_owner_dapat_menghapus_user_dengan_soft_delete()
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'name' => 'User to Delete',
        ]);

        $response = $this->actingAs($this->owner)->delete("/owner/users/{$user->id}");

        $response->assertRedirect('/owner/users');
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_data_user_yang_dihapus_tidak_muncul_di_halaman_index()
    {
        $activeUser = User::factory()->create(['name' => 'Active User']);
        $deletedUser = User::factory()->create(['name' => 'Deleted User']);
        $deletedUser->delete();

        $response = $this->actingAs($this->owner)->get('/owner/users');

        $response->assertStatus(200);
        $response->assertSee('Active User');
        $response->assertDontSee('Deleted User');
    }

    public function test_validasi_field_wajib_diisi_saat_menambahkan_user()
    {
        $response = $this->actingAs($this->owner)->post('/owner/users', []);

        $response->assertSessionHasErrors(['name', 'email', 'role', 'jenis_kelamin']);
    }

    public function test_validasi_email_harus_unik()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->actingAs($this->owner)->post('/owner/users', [
            'name' => 'New User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
            'jenis_kelamin' => 'Laki-laki',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_validasi_password_minimal_8_karakter()
    {
        $response = $this->actingAs($this->owner)->post('/owner/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
            'role' => 'admin',
            'jenis_kelamin' => 'Laki-laki',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_validasi_konfirmasi_password_harus_sama()
    {
        $response = $this->actingAs($this->owner)->post('/owner/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password456',
            'role' => 'admin',
            'jenis_kelamin' => 'Laki-laki',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_validasi_role_harus_ada_di_daftar_role()
    {
        $response = $this->actingAs($this->owner)->post('/owner/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'invalid_role',
            'jenis_kelamin' => 'Laki-laki',
        ]);

        $response->assertSessionHasErrors(['role']);
    }

    public function test_validasi_jenis_kelamin_harus_laki_laki_atau_perempuan()
    {
        $response = $this->actingAs($this->owner)->post('/owner/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
            'jenis_kelamin' => 'invalid',
        ]);

        $response->assertSessionHasErrors(['jenis_kelamin']);
    }

    public function test_owner_dapat_mencari_pengguna_berdasarkan_nama_atau_email()
    {
        User::factory()->create(['name' => 'Budi Santoso', 'email' => 'budi@example.com']);
        User::factory()->create(['name' => 'Ani Wijaya', 'email' => 'ani@example.com']);

        $response = $this->actingAs($this->owner)
            ->get('/owner/users?search=Budi');

        $response->assertStatus(200)
            ->assertSee('Budi Santoso')
            ->assertDontSee('Ani Wijaya');

        $response = $this->actingAs($this->owner)
            ->get('/owner/users?search=ani@example.com');

        $response->assertStatus(200)
            ->assertSee('Ani Wijaya')
            ->assertDontSee('Budi Santoso');
    }

    public function test_owner_dapat_memfilter_pengguna_berdasarkan_role()
    {
        User::factory()->create(['name' => 'Admin User', 'role' => 'admin']);
        User::factory()->create(['name' => 'Jahit User', 'role' => 'jahit']);
        User::factory()->create(['name' => 'Potong User', 'role' => 'potong']);

        $response = $this->actingAs($this->owner)
            ->get('/owner/users?role=jahit');

        $response->assertStatus(200)
            ->assertSee('Jahit User')
            ->assertDontSee('Admin User')
            ->assertDontSee('Potong User');
    }

    public function test_owner_dapat_mengurutkan_pengguna_berdasarkan_nama_asc()
    {
        User::factory()->create(['name' => 'Zebra User']);
        User::factory()->create(['name' => 'Alpha User']);
        User::factory()->create(['name' => 'Mike User']);

        $response = $this->actingAs($this->owner)
            ->get('/owner/users?sort=nama_asc');

        $response->assertStatus(200);
        $content = $response->getContent();
        
        $alphaPos = strpos($content, 'Alpha User');
        $mikePos = strpos($content, 'Mike User');
        $zebraPos = strpos($content, 'Zebra User');
        
        $this->assertLessThan($mikePos, $alphaPos);
        $this->assertLessThan($zebraPos, $mikePos);
    }

    public function test_owner_dapat_mengurutkan_pengguna_berdasarkan_nama_desc()
    {
        User::factory()->create(['name' => 'Alpha User']);
        User::factory()->create(['name' => 'Zebra User']);
        User::factory()->create(['name' => 'Mike User']);

        $response = $this->actingAs($this->owner)
            ->get('/owner/users?sort=nama_desc');

        $response->assertStatus(200);
        $content = $response->getContent();
        
        $zebraPos = strpos($content, 'Zebra User');
        $mikePos = strpos($content, 'Mike User');
        $alphaPos = strpos($content, 'Alpha User');
        
        $this->assertLessThan($mikePos, $zebraPos);
        $this->assertLessThan($alphaPos, $mikePos);
    }

    public function test_owner_dapat_mengurutkan_pengguna_berdasarkan_waktu_terbaru()
    {
        $oldUser = User::factory()->create(['name' => 'Old User', 'created_at' => now()->subDays(5)]);
        $newUser = User::factory()->create(['name' => 'New User', 'created_at' => now()]);

        $response = $this->actingAs($this->owner)
            ->get('/owner/users?sort=terbaru');

        $response->assertStatus(200);
        $content = $response->getContent();
        
        $newPos = strpos($content, 'New User');
        $oldPos = strpos($content, 'Old User');
        
        $this->assertLessThan($oldPos, $newPos);
    }

    public function test_owner_dapat_mengurutkan_pengguna_berdasarkan_waktu_terlama()
    {
        $oldUser = User::factory()->create(['name' => 'Old User', 'created_at' => now()->subDays(5)]);
        $newUser = User::factory()->create(['name' => 'New User', 'created_at' => now()]);

        $response = $this->actingAs($this->owner)
            ->get('/owner/users?sort=terlama');

        $response->assertStatus(200);
        $content = $response->getContent();
        
        $oldPos = strpos($content, 'Old User');
        $newPos = strpos($content, 'New User');
        
        $this->assertLessThan($newPos, $oldPos);
    }

    public function test_halaman_index_menampilkan_pagination_ketika_data_lebih_dari_10()
    {
        User::factory()->count(15)->create();

        $response = $this->actingAs($this->owner)
            ->get('/owner/users');

        $response->assertStatus(200)
            ->assertSee('Pagination Navigation')
            ->assertSee('Menampilkan');
    }

    public function test_owner_dapat_menggabungkan_filter_dan_search()
    {
        User::factory()->create(['name' => 'Budi Admin', 'role' => 'admin']);
        User::factory()->create(['name' => 'Budi Jahit', 'role' => 'jahit']);
        User::factory()->create(['name' => 'Ani Admin', 'role' => 'admin']);

        $response = $this->actingAs($this->owner)
            ->get('/owner/users?search=Budi&role=admin');

        $response->assertStatus(200)
            ->assertSee('Budi Admin')
            ->assertDontSee('Budi Jahit')
            ->assertDontSee('Ani Admin');
    }
}

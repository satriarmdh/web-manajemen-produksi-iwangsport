<?php

namespace Tests\Feature\Owner;

use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;

class UserManagementTest extends TestCase
{
    // Me-reset database setiap kali tes dijalankan agar data tidak bentrok
    use RefreshDatabase;

    private User $owner;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan data dummy sebelum setiap tes berjalan
        $this->owner = User::factory()->create(['role' => 'owner']);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function test_halaman_manajemen_pengguna_tidak_dapat_diakses_oleh_peran_selain_owner()
    {
        // Simulasi login sebagai admin
        $response = $this->actingAs($this->admin)->get('/owner/users');

        // Harus ditolak (403 Forbidden)
        $response->assertStatus(403);
    }

    /** @test */
    public function test_owner_dapat_mengakses_halaman_manajemen_pengguna()
    {
        // Simulasi login sebagai owner
        $response = $this->actingAs($this->owner)->get('/owner/users');

        // Harus berhasil masuk (200 OK) dan melihat teks tertentu
        $response->assertStatus(200);
        $response->assertSee('Manajemen Pengguna');
    }

    /** @test */
    public function test_owner_dapat_menambahkan_pengguna_baru()
    {
        $userData = [
            'name' => 'Budi Penjahit',
            'email' => 'budi@iwangsport.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'jahit',
        ];

        // Owner mengirim form tambah user
        $response = $this->actingAs($this->owner)->post('/owner/users', $userData);

        // Harus diarahkan kembali ke halaman index tanpa error
        $response->assertRedirect('/owner/users');
        $response->assertSessionHasNoErrors();

        // Pastikan data benar-benar tersimpan di database
        $this->assertDatabaseHas('users', [
            'email' => 'budi@iwangsport.com',
            'role' => 'jahit',
        ]);
    }

    /** @test */
    public function test_data_pengguna_baru_yang_ditambahkan_harus_valid()
    {
        // Sengaja mengirim data kosong
        $response = $this->actingAs($this->owner)->post('/owner/users', []);

        // Harus memunculkan error validasi
        $response->assertSessionHasErrors(['name', 'email', 'password', 'role']);
    }

    /** @test */
    public function test_owner_dapat_mengubah_peran_pengguna_yang_ada()
    {
        // Bikin user dummy sebagai penjahit
        $karyawan = User::factory()->create([
            'name' => 'Siti',
            'email' => 'siti@iwangsport.com',
            'role' => 'jahit',
        ]);

        // Owner mengubah rolenya jadi admin
        $response = $this->actingAs($this->owner)->put('/owner/users/' . $karyawan->id, [
            'name' => 'Siti (Promoted)',
            'email' => 'siti@iwangsport.com',
            'role' => 'admin',
        ]);

        $response->assertRedirect('/owner/users');

        // Pastikan di database rolenya sudah berubah
        $this->assertDatabaseHas('users', [
            'id' => $karyawan->id,
            'name' => 'Siti (Promoted)',
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function test_owner_dapat_menghapus_data_pengguna()
    {
        $karyawan = User::factory()->create();

        $response = $this->actingAs($this->owner)->delete('/owner/users/' . $karyawan->id);

        $response->assertRedirect('/owner/users');

        // Pastikan datanya sudah hilang dari database
        $this->assertDatabaseMissing('users', [
            'id' => $karyawan->id,
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@iwangsport.com',
            'jenis_kelamin' => 'laki-laki',
            'password' => bcrypt('password123'),
        ]);
    }

    /**
     * Test halaman profile dapat diakses oleh user terautentikasi
     */
    public function test_halaman_profile_dapat_diakses_oleh_user_yang_terautentikasi(): void
    {
        $response = $this->actingAs($this->user)->get('/profile');

        $response->assertStatus(200)
            ->assertViewIs('profile.edit')
            ->assertSee('Profile Settings')
            ->assertSee('budi@iwangsport.com')
            ->assertSee('Budi Santoso');
    }

    /**
     * Test halaman profile tidak dapat diakses oleh guest
     */
    public function test_halaman_profile_tidak_dapat_diakses_oleh_guest(): void
    {
        $response = $this->get('/profile');

        $response->assertRedirect('/login');
    }

    /**
     * Test user dapat memperbarui nama, jenis kelamin, no hp, dan alamat tanpa memasukkan current_password
     */
    public function test_user_dapat_memperbarui_nama_dan_jenis_kelamin_tanpa_current_password(): void
    {
        $response = $this->actingAs($this->user)->put('/profile', [
            'name' => 'Budi Santoso Update',
            'email' => 'budi@iwangsport.com',
            'jenis_kelamin' => 'perempuan',
            'no_hp' => '081299998888',
            'alamat' => 'Jl. Baru No. 99, Kota Baru',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile');

        $this->user->refresh();
        $this->assertEquals('Budi Santoso Update', $this->user->name);
        $this->assertEquals('perempuan', $this->user->jenis_kelamin);
        $this->assertEquals('081299998888', $this->user->no_hp);
        $this->assertEquals('Jl. Baru No. 99, Kota Baru', $this->user->alamat);
    }

    /**
     * Test user dapat memperbarui email dengan memasukkan current_password yang benar
     */
    public function test_user_dapat_memperbarui_email_dengan_current_password_yang_benar(): void
    {
        $response = $this->actingAs($this->user)->put('/profile', [
            'name' => 'Budi Santoso',
            'email' => 'budi.update@iwangsport.com',
            'jenis_kelamin' => 'laki-laki',
            'current_password' => 'password123',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile');

        $this->user->refresh();
        $this->assertEquals('budi.update@iwangsport.com', $this->user->email);
    }

    /**
     * Test user dapat memperbarui password dengan current password yang benar
     */
    public function test_user_dapat_memperbarui_password_dengan_current_password_yang_benar(): void
    {
        $response = $this->actingAs($this->user)->put('/profile', [
            'name' => 'Budi Santoso',
            'email' => 'budi@iwangsport.com',
            'jenis_kelamin' => 'laki-laki',
            'current_password' => 'password123',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile');

        $this->user->refresh();
        $this->assertTrue(Hash::check('passwordbaru123', $this->user->password));
    }

    /**
     * Test user tidak dapat memperbarui password dengan current password yang salah
     */
    public function test_user_tidak_dapat_memperbarui_password_dengan_current_password_yang_salah(): void
    {
        $response = $this->actingAs($this->user)->put('/profile', [
            'name' => 'Budi Santoso',
            'email' => 'budi@iwangsport.com',
            'jenis_kelamin' => 'laki-laki',
            'current_password' => 'passwordsalah',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ]);

        $response->assertSessionHasErrors('current_password');
        
        $this->user->refresh();
        $this->assertTrue(Hash::check('password123', $this->user->password));
    }

    /**
     * Test user tidak dapat memperbarui email jika email sudah digunakan oleh user lain
     */
    public function test_user_tidak_dapat_memperbarui_email_jika_email_sudah_digunakan_oleh_user_lain(): void
    {
        User::factory()->create([
            'email' => 'userlain@iwangsport.com',
        ]);

        $response = $this->actingAs($this->user)->put('/profile', [
            'name' => 'Budi Santoso',
            'email' => 'userlain@iwangsport.com',
            'jenis_kelamin' => 'laki-laki',
            'current_password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }
}

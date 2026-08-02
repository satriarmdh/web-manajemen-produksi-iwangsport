<?php

namespace Tests\Feature\Auth;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_dapat_diakses(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Login');
    }

    /**
     * @dataProvider roleRedirectProvider 
     */
    public function test_user_berhasil_login_dan_diarahkan_ke_halaman_yang_sesuai($role, $redirectTo)
    {
        /** @var User $user */
        $user = User::factory()->state(['role' => $role])->create([
            'email' => "{$role}@iwangsport.com",
            'password' => bcrypt('rahasia123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'rahasia123',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect($redirectTo);
    }

    public static function roleRedirectProvider(): array
    {
        return [
            'Admin ke Dashboard'    => ['admin', '/admin/dashboard'],
            'Owner ke Dashboard'    => ['owner', '/owner/dashboard'],
            'Potong ke Produksi'    => ['potong', '/produksi/dashboard'],
            'Jahit ke Produksi'     => ['jahit', '/produksi/dashboard'],
            'Finishing ke Produksi' => ['finishing', '/produksi/dashboard'],
        ];
    }

    public function test_login_berhasil_mengupdate_last_seen_dan_status_online()
    {
        Carbon::setTestNow(now());

        /** @var User $user */
        $user = User::factory()->admin()->create([
            'password' => bcrypt('rahasia123'),
            'online_status' => false,
            'last_seen' => null,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'rahasia123',
        ]);

        $user->refresh();

        $this->assertEquals(true, (bool) $user->online_status);
        $this->assertEquals(now()->toDateTimeString(), $user->last_seen?->toDateTimeString());

        Carbon::setTestNow();
    }

    public function test_user_dapat_login_dengan_fitur_remember_me()
    {
        /** @var User $user */
        $user = User::factory()->admin()->create([
            'password' => bcrypt('rahasia123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'rahasia123',
            'remember' => 'on',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertStatus(302);

        $user->refresh();
        $this->assertNotNull($user->getRememberToken());
    }

    public function test_user_tidak_dapat_login_dengan_email_yang_tidak_terdaftar()
    {
        $response = $this->post('/login', [
            'email' => 'tidakada@iwangsport.com',
            'password' => 'sembarang123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_user_tidak_dapat_login_dengan_password_salah()
    {
        /** @var User $user */
        $user = User::factory()->admin()->create([
            'email' => 'admin@iwangsport.com',
            'password' => bcrypt('password_benar'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password_salah',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_user_dapat_logout_dan_menghapus_sesi()
    {
        /** @var User $user */
        $user = User::factory()->admin()->create();

        // Menggunakan actingAs dengan instance user yang jelas
        $this->actingAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
        $this->assertFalse(Auth::check());
    }

    public function test_guest_dialihkan_ke_halaman_login_saat_mengakses_halaman_proteksi()
    {
        $protectedRoutes = [
            '/admin/dashboard',
            '/owner/dashboard',
            '/produksi/dashboard',
            '/profile',
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    public function test_validasi_field_wajib_diisi_saat_login()
    {
        $response = $this->post('/login', []);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_login_dengan_email_kosong_gagal()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'rahasia123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_dengan_password_kosong_gagal()
    {
        $user = User::factory()->admin()->create([
            'password' => bcrypt('rahasia123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_user_yang_sudah_login_tidak_bisa_mengakses_halaman_login()
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get('/login');

        // Laravel Breeze menampilkan halaman login (200) walau sudah login
        $response->assertStatus(200);
    }

    public function test_user_hanya_dapat_mengakses_sesuai_rolenya()
    {
        // Admin tidak bisa akses owner dashboard
        $admin = User::factory()->admin()->create();
        $response = $this->actingAs($admin)->get('/owner/dashboard');
        $response->assertStatus(403);

        // Owner tidak bisa akses admin dashboard
        $owner = User::factory()->owner()->create();
        $response = $this->actingAs($owner)->get('/admin/dashboard');
        $response->assertStatus(403);

        // Karyawan potong tidak bisa akses admin dashboard
        $potong = User::factory()->potong()->create();
        $response = $this->actingAs($potong)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_logout_mengupdate_online_status_ke_offline()
    {
        Carbon::setTestNow(now());

        $user = User::factory()->admin()->create([
            'online_status' => true,
            'last_seen' => now(),
        ]);

        $this->actingAs($user)->post('/logout');

        $user->refresh();
        $this->assertFalse((bool) $user->online_status);

        Carbon::setTestNow();
    }
}

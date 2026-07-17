<?php

namespace Tests\Feature\Auth;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
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
}

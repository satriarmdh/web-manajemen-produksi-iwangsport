<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email' => 'iwang@example.com',
            'password' => Hash::make('oldpassword123'),
        ]);
    }

    /**
     * Test halaman forgot password dapat diakses.
     */
    public function test_halaman_forgot_password_dapat_diakses(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200)
            ->assertViewIs('auth.forgot-password');
    }

    /**
     * Test pengiriman email reset password gagal jika email tidak terdaftar.
     */
    public function test_kirim_reset_link_gagal_jika_email_tidak_terdaftar(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test pengiriman email reset password sukses dengan email terdaftar.
     */
    public function test_kirim_reset_link_sukses_jika_email_terdaftar(): void
    {
        Notification::fake();

        $response = $this->post('/forgot-password', [
            'email' => 'iwang@example.com',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        Notification::assertSentTo($this->user, ResetPasswordNotification::class);
    }

    /**
     * Test halaman reset password dapat diakses dengan token valid.
     */
    public function test_halaman_reset_password_dapat_diakses_dengan_token_valid(): void
    {
        $token = Password::createToken($this->user);

        $response = $this->get("/reset-password/{$token}?email=iwang@example.com");

        $response->assertStatus(200)
            ->assertViewIs('auth.reset-password')
            ->assertSee($token);
    }

    /**
     * Test reset password gagal jika konfirmasi kata sandi tidak cocok.
     */
    public function test_reset_password_gagal_jika_konfirmasi_tidak_cocok(): void
    {
        $token = Password::createToken($this->user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'iwang@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertSessionHasErrors(['password']);
        
        $this->user->refresh();
        $this->assertTrue(Hash::check('oldpassword123', $this->user->password));
    }

    /**
     * Test reset password sukses dengan token dan data valid.
     */
    public function test_reset_password_sukses_dengan_token_dan_data_valid(): void
    {
        $token = Password::createToken($this->user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'iwang@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasNoErrors();

        $this->user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->user->password));
    }

    /**
     * Test halaman forgot password mengisi email otomatis jika user sudah login.
     */
    public function test_halaman_forgot_password_mengisi_email_otomatis_jika_user_sudah_login(): void
    {
        $response = $this->actingAs($this->user)->get('/forgot-password');

        $response->assertStatus(200)
            ->assertViewIs('auth.forgot-password')
            ->assertViewHas('email', 'iwang@example.com');
    }
}

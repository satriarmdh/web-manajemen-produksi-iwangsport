<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Carbon;

class LoginController extends Controller
{
    // protected AuthService $authService;

    // public function __construct(AuthService $authService)
    // {
    //     $this->authService = $authService;
    // }

    public function __construct(
        protected AuthService $authService
    ) {}

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        // Validasi otomatis dilakukan oleh LoginRequest
        $credentials = $request->validated();

        // Mencoba login dengan fitur remember me
        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Refactoring: Logika berat dipindah ke Service
            $this->authService->updateLoginMetadata($user);
            $redirectPath = $this->authService->getRedirectPath($user->role);

            return redirect()->intended($redirectPath);
        }

        // Jika gagal, kembali dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password yang dimasukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $user->last_seen = now();
            $user->online_status = 0;
            $user->saveQuietly(); // Pakai saveQuietly() juga di sini biar rapi
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

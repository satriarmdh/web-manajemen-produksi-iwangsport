<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Iwangsport</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex items-center justify-center bg-gray-100 p-4 sm:p-8 text-gray-800">
    
    {{-- WRAPPER UTAMA --}}
    <div class="w-full max-w-6xl bg-white rounded-xl shadow-2xl p-4 flex flex-col md:flex-row min-h-150 gap-6 lg:gap-12">
        
        {{-- KIRI (GAMBAR & SLIDER) --}}
        <div class="hidden md:flex md:w-1/2 relative flex-col justify-between p-12 text-white rounded-xl overflow-hidden group">
            
            @php
                $loginSlides = [
                    ['image' => asset('images/bg-login.jpg'), 'text' => 'Satu Dasbor untuk Semua Kendali Produksi.'],
                    ['image' => asset('images/bg-login2.jpg'), 'text' => 'Pantau Aktivitas Produksi Secara Real-Time.'],
                    ['image' => asset('images/bg-login3.jpg'), 'text' => 'Siklus Produksi Terintegrasi Penuh.'],
                ];
            @endphp

            {{-- LAYER 1: Wadah Track untuk Slide Gambar --}}
            <div class="absolute inset-0 z-0 overflow-hidden">
                <div
                    id="slider-track"
                    class="flex h-full w-full transition-transform duration-700 ease-in-out"
                    data-login-slides='@json($loginSlides)'
                >
                    </div>
            </div>
            
            {{-- LAYER 2: Overlay Gradien --}}
            <div class="absolute inset-0 z-10 bg-linear-to-t from-[rgba(15,3,77,0.9)] to-[rgba(15,3,77,0.2)]"></div>
            
            {{-- LAYER 3: Konten (Logo & Teks) --}}
            <div class="flex items-center gap-3 z-20">
                <img src="{{ asset('images/logo-primary.png') }}" alt="Logo Iwangsport" class="w-8 h-8 rounded-full object-cover bg-white shadow-sm">
                <span class="text-xl font-bold text-white">Iwangsport</span>
            </div>
            
            <div class="z-20">
                <div class="overflow-hidden mb-6 h-24 flex items-end">
                    <h1 id="slider-text" class="text-3xl font-bold leading-tight transition-all duration-500 ease-in-out translate-y-0 opacity-100">Satu Dasbor untuk Semua Kendali Produksi.</h1>
                </div>
                
                <div id="slider-dots" class="flex items-center gap-2 h-3">
                    </div>
            </div>
        </div>
        
        {{-- KANAN (FORM) --}}
        <div class="w-full md:w-1/2 flex flex-col justify-center py-8 lg:py-12 px-2 sm:px-8">
            <div class="w-full max-w-md mx-auto">
                
                <div class="md:hidden flex justify-center items-center mb-8 gap-3 z-10">
                    <img src="{{ asset('images/logo-secondary.png') }}" alt="Logo Iwangsport" class="w-8 h-8 rounded-full object-cover bg-white shadow-sm">
                    <span class="text-2xl font-bold text-[#0F034D]">Iwangsport</span>
                </div>

                <h2 class="text-2xl lg:text-3xl font-bold text-[#0F034D] mb-2">Selamat Datang!</h2>
                <p class="text-sm text-gray-500 mb-8">Silahkan masuk untuk memulai.</p>

                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 p-3 rounded-md mb-6 text-sm border border-red-200">
                        <p>Email atau kata sandi Anda salah.</p>
                    </div>
                @endif

                <form action="{{ url('/login') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Anda</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="iwang@gmail.com" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0F034D] focus:border-transparent transition text-sm">
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" placeholder="********" required 
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0F034D] focus:border-transparent transition text-sm">
                            <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-[#0F034D] transition-colors focus:outline-none cursor-pointer">
                                <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg id="eye-slash-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                                    <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path>
                                    <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                                    <line x1="2" x2="22" y1="2" y2="22"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between text-sm">
                        <div data-remember-wrapper class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="remember" id="remember" class="hidden">
                            <div id="remember_box" class="flex shrink-0 items-center justify-center w-5 h-5 rounded border-2 border-gray-300 transition-all">
                                <svg id="remember_icon" class="w-3 h-3 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-sm text-gray-600">Ingat Saya</span>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full py-3 px-4 bg-[#0F034D] hover:bg-[#0a0235] text-white font-semibold rounded-xl shadow-md focus:outline-none focus:ring-2 focus:ring-[#0F034D] focus:ring-offset-2 transition-all mt-4 cursor-pointer">
                        Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>


    @vite([
        'resources/js/auth/login-slider.js',
        'resources/js/auth/password-toggle.js',
        'resources/js/auth/ingatsaya-toggle.js',
    ])

</body>
</html>
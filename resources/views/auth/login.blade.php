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
            
            {{-- LAYER 1: Wadah Track untuk Slide Gambar --}}
            <div class="absolute inset-0 z-0 overflow-hidden">
                <div id="slider-track" class="flex h-full w-full transition-transform duration-700 ease-in-out">
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
                        <input type="password" name="password" id="password" placeholder="••••••••" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0F034D] focus:border-transparent transition text-sm">
                    </div>
                    
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-[#0F034D] rounded border-gray-300 focus:ring-[#0F034D] accent-[#0F034D]">
                            <label for="remember" class="text-gray-600 select-none cursor-pointer">Ingat Saya</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full py-3 px-4 bg-[#0F034D] hover:bg-[#0a0235] text-white font-semibold rounded-xl shadow-md focus:outline-none focus:ring-2 focus:ring-[#0F034D] focus:ring-offset-2 transition-all mt-4 cursor-pointer">
                        Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        window.loginSlides = [
            {
                image: "{{ asset('images/bg-login.jpg') }}",
                text: "Satu Dasbor untuk Semua Kendali Produksi."
            },
            {
                image: "{{ asset('images/bg-login2.jpg') }}",
                text: "Pantau Aktivitas Produksi Secara Real-Time."
            },
            {
                image: "{{ asset('images/bg-login3.jpg') }}",
                text: "Siklus Produksi Terintegrasi Penuh."
            }
        ];
    </script>

    @vite(['resources/js/login-slider.js'])

</body>
</html>
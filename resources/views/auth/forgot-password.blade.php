<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi | Iwangsport</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex items-center justify-center bg-gray-100 p-4 sm:p-8 text-gray-800">
    
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sm:p-8">
        {{-- LOGO --}}
        <div class="flex flex-col items-center mb-6">
            <img src="{{ asset('images/logo-secondary.png') }}" alt="Logo Iwangsport" class="w-12 h-12 rounded-full object-cover bg-white shadow-sm mb-3">
            <h2 class="text-2xl font-bold text-[#0F034D]">Lupa Kata Sandi?</h2>
            <p class="text-xs text-gray-500 mt-1 text-center px-4">Masukkan email terdaftar Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.</p>
        </div>

        {{-- STATUS SUCCESS --}}
        @if (session('status'))
            <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 text-xs border border-green-100 leading-relaxed">
                {{ session('status') }}
            </div>
        @endif

        {{-- FORM --}}
        <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Alamat Email</label>
                <div class="relative">
                    <input type="email" name="email" id="email" value="{{ old('email', $email ?? '') }}" placeholder="contoh: iwang@gmail.com" required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0F034D] focus:border-transparent transition text-sm @error('email') border-red-500 @enderror">
                </div>
                @error('email')
                    <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>
            
            <button type="submit" class="w-full py-3 px-4 bg-[#0F034D] hover:bg-[#0a0235] text-white font-semibold rounded-xl shadow-md shadow-[#0F034D]/20 focus:outline-none focus:ring-2 focus:ring-[#0F034D] focus:ring-offset-2 transition-all mt-2 cursor-pointer text-sm">
                Kirim Tautan Reset
            </button>
        </form>

        {{-- BACK TO LOGIN --}}
        <div class="mt-6 pt-5 border-t border-gray-100 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#0F034D] hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Halaman Login
            </a>
        </div>
    </div>

</body>
</html>

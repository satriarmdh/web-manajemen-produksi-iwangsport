<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Kata Sandi | Iwangsport</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex items-center justify-center bg-gray-100 p-4 sm:p-8 text-gray-800">
    
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sm:p-8">
        {{-- LOGO --}}
        <div class="flex flex-col items-center mb-6">
            <img src="{{ asset('images/logo-secondary.png') }}" alt="Logo Iwangsport" class="w-12 h-12 rounded-full object-cover bg-white shadow-sm mb-3">
            <h2 class="text-2xl font-bold text-[#0F034D]">Atur Ulang Kata Sandi</h2>
            <p class="text-xs text-gray-500 mt-1 text-center px-4">Silakan buat kata sandi baru untuk mengamankan kembali akun Anda.</p>
        </div>

        {{-- FORM --}}
        <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $email) }}" required readonly
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none bg-gray-50 text-gray-500 cursor-not-allowed">
                @error('email')
                    <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password Baru --}}
            <div>
                <label for="password" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Kata Sandi Baru</label>
                <div class="relative">
                    <input type="password" name="password" id="password" required placeholder="Minimal 8 karakter..."
                        class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0F034D] focus:border-transparent transition text-sm @error('password') border-red-500 @enderror">
                    <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-[#0F034D] transition-colors focus:outline-none cursor-pointer">
                        <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path>
                            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                            <line x1="2" x2="22" y1="2" y2="22"></line>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Konfirmasi Password Baru --}}
            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Konfirmasi Kata Sandi</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Ulangi kata sandi baru..."
                        class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0F034D] focus:border-transparent transition text-sm">
                    <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-[#0F034D] transition-colors focus:outline-none cursor-pointer">
                        <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path>
                            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                            <line x1="2" x2="22" y1="2" y2="22"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full py-3 px-4 bg-[#0F034D] hover:bg-[#0a0235] text-white font-semibold rounded-xl shadow-md shadow-[#0F034D]/20 focus:outline-none focus:ring-2 focus:ring-[#0F034D] focus:ring-offset-2 transition-all mt-4 cursor-pointer text-sm">
                Perbarui Kata Sandi
            </button>
        </form>
    </div>

    <script>
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;

            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');

            const eyeOpen = btn.querySelector('.eye-open');
            const eyeClosed = btn.querySelector('.eye-closed');

            if (isPassword) {
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
    </script>
</body>
</html>

<form action="{{ route('profile.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        <!-- Card 1: Informasi Profil -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-6">
            <h3 class="text-base font-bold text-gray-900 pb-2 border-b border-gray-50 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Informasi Profil
            </h3>
            
            <div class="space-y-4">
                <!-- Nama -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors bg-white shadow-sm @error('name') border-red-500 @enderror" placeholder="Masukkan nama lengkap...">
                    @error('name')
                        <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors bg-white shadow-sm @error('email') border-red-500 @enderror" placeholder="Masukkan email...">
                    @error('email')
                        <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Kelamin (Radio Buttons, mencerminkan Manajemen Pengguna) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <div class="flex gap-2 sm:gap-3">
                        <div class="relative flex-1">
                            <input type="radio" id="gender_male" name="jenis_kelamin" value="Laki-laki" class="peer sr-only" required {{ old('jenis_kelamin', $user->jenis_kelamin) == 'Laki-laki' || old('jenis_kelamin', $user->jenis_kelamin) == 'laki-laki' ? 'checked' : '' }}>
                            <label for="gender_male" class="flex items-center justify-center lg:justify-start gap-2 w-full px-3 py-3 border @error('jenis_kelamin') border-red-300 @else border-gray-200 @enderror rounded-xl cursor-pointer hover:bg-gray-50 transition-all text-gray-500 peer-checked:border-[#0F034D] peer-checked:bg-[#0F034D]/5 peer-checked:ring-1 peer-checked:ring-[#0F034D] peer-checked:text-[#0F034D] peer-checked:[&_.radio-circle]:border-[#0F034D] peer-checked:[&_.radio-dot]:scale-100 shadow-sm">
                                <div class="radio-circle relative flex shrink-0 items-center justify-center w-4 h-4 sm:w-5 sm:h-5 rounded-full border-2 border-gray-300 transition-colors pointer-events-none">
                                    <div class="radio-dot w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-[#0F034D] scale-0 transition-transform"></div>
                                </div>
                                <span class="text-sm font-medium pointer-events-none">
                                    <span class="inline">Laki-laki</span>
                                </span>
                            </label>
                        </div>

                        <div class="relative flex-1">
                            <input type="radio" id="gender_female" name="jenis_kelamin" value="Perempuan" class="peer sr-only" required {{ old('jenis_kelamin', $user->jenis_kelamin) == 'Perempuan' || old('jenis_kelamin', $user->jenis_kelamin) == 'perempuan' ? 'checked' : '' }}>    
                            <label for="gender_female" class="flex items-center justify-center lg:justify-start gap-2 w-full px-3 py-3 border @error('jenis_kelamin') border-red-300 @else border-gray-200 @enderror rounded-xl cursor-pointer hover:bg-gray-50 transition-all text-gray-500 peer-checked:border-[#0F034D] peer-checked:bg-[#0F034D]/5 peer-checked:ring-1 peer-checked:ring-[#0F034D] peer-checked:text-[#0F034D] peer-checked:[&_.radio-circle]:border-[#0F034D] peer-checked:[&_.radio-dot]:scale-100 shadow-sm">
                                <div class="radio-circle relative flex shrink-0 items-center justify-center w-4 h-4 sm:w-5 sm:h-5 rounded-full border-2 border-gray-300 transition-colors pointer-events-none">
                                    <div class="radio-dot w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-[#0F034D] scale-0 transition-transform"></div>
                                </div>
                                <span class="text-sm font-medium pointer-events-none">
                                    <span class="inline">Perempuan</span>
                                </span>
                            </label>
                        </div>
                    </div>
                    @error('jenis_kelamin')
                        <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor HP -->
                <div>
                    <label for="no_hp" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nomor HP</label>
                    <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $user->no_hp) }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors bg-white shadow-sm @error('no_hp') border-red-500 @enderror" placeholder="Masukkan nomor HP (opsional)...">
                    @error('no_hp')
                        <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Alamat -->
                <div>
                    <label for="alamat" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Alamat Tempat Tinggal</label>
                    <textarea name="alamat" id="alamat" rows="3" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors bg-white shadow-sm @error('alamat') border-red-500 @enderror" placeholder="Masukkan alamat lengkap (opsional)...">{{ old('alamat', $user->alamat) }}</textarea>
                    @error('alamat')
                        <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Card 2: Keamanan / Ubah Password -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
            <h3 class="text-base font-bold text-gray-900 pb-2 border-b border-gray-50 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Ubah Password / Verifikasi
            </h3>

            <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-3.5 text-xs text-blue-700 leading-relaxed">
                Lengkapi bagian ini hanya jika Anda ingin memperbarui password atau mengubah email. Masukkan password saat ini untuk memverifikasi identitas Anda.
            </div>

            <div class="space-y-4">
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Password Saat Ini</label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current_password" class="w-full px-4 py-2.5 pr-12 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors bg-white shadow-sm @error('current_password') border-red-500 @enderror" placeholder="Masukkan password saat ini...">
                        <button type="button" onclick="togglePasswordVisibility('current_password', this)" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-[#0F034D] transition-colors focus:outline-none cursor-pointer">
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
                    @error('current_password')
                        <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                    <div class="mt-1.5 flex justify-end">
                        <a href="{{ route('password.request') }}" target="_blank" class="text-xs font-semibold text-[#0F034D] hover:underline">Lupa Password?</a>
                    </div>
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Password Baru</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" class="w-full px-4 py-2.5 pr-12 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors bg-white shadow-sm @error('password') border-red-500 @enderror" placeholder="Masukkan password baru (min. 8 karakter)...">
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
                        <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-2.5 pr-12 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors bg-white shadow-sm" placeholder="Ulangi password baru...">
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
            </div>
        </div>
    </div>

    <!-- Tombol Simpan -->
    <div class="mt-6 flex justify-end">
        <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-sm font-semibold rounded-xl transition-all shadow-md shadow-[#0F034D]/20 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            Simpan Perubahan
        </button>
    </div>
</form>
@vite('resources/js/utils/toggle-password.js')

<x-layouts.owner>
    <x-slot:breadcrumb>
        <li class="flex items-center">
            <span class="text-gray-400 select-none">Pengaturan Sistem</span>
        </li>
        <li class="flex items-center">
            <svg class="w-4 h-4 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <a href="{{ route('owner.users.index') }}" class="text-gray-400 hover:text-[#0F034D] transition-colors">Manajemen Pengguna</a>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold">
            <svg class="w-4 h-4 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            Tambah Pengguna
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Tambah Pengguna Baru
    </x-slot:header>

    <form action="{{ route('owner.users.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            
            <!-- KOLOM KIRI (Informasi Pribadi) -->
            <div class="xl:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                    <h3 class="text-lg font-bold text-[#0F034D] mb-6">Informasi Pribadi</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Nama Lengkap -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Masukkan nama lengkap" class="w-full px-4 py-3 border rounded-xl transition-colors text-sm {{ $errors->has('name') ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-[#0F034D]/20 focus:border-[#0F034D]' }}">
                            @error('name') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <!-- Email -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="email@iwangsport.com" class="w-full px-4 py-3 border rounded-xl transition-colors text-sm {{ $errors->has('email') ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-[#0F034D]/20 focus:border-[#0F034D]' }}">
                            @error('email') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <!-- No HP -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor HP</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="Contoh: 08123456789" class="w-full px-4 py-3 border rounded-xl transition-colors text-sm {{ $errors->has('no_hp') ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-[#0F034D]/20 focus:border-[#0F034D]' }}">
                            @error('no_hp') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <!-- Jenis Kelamin -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <div class="flex gap-2 sm:gap-3">
                                <div class="relative flex-1">
                                    <input type="radio" id="gender_male" name="jenis_kelamin" value="Laki-laki" class="peer sr-only" required {{ old('jenis_kelamin') == 'Laki-laki' ? 'checked' : '' }}>
                                    <label for="gender_male" class="flex items-center justify-center lg:justify-start gap-2 w-full px-3 py-3 border {{ $errors->has('jenis_kelamin') ? 'border-red-300' : 'border-gray-300' }} rounded-xl cursor-pointer hover:bg-gray-50 transition-all text-gray-500 peer-checked:border-[#0F034D] peer-checked:bg-[#0F034D]/5 peer-checked:ring-1 peer-checked:ring-[#0F034D] peer-checked:text-[#0F034D] peer-checked:[&_.radio-circle]:border-[#0F034D] peer-checked:[&_.radio-dot]:scale-100">
                                        <div class="radio-circle relative flex shrink-0 items-center justify-center w-4 h-4 sm:w-5 sm:h-5 rounded-full border-2 border-gray-300 transition-colors pointer-events-none">
                                            <div class="radio-dot w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-[#0F034D] scale-0 transition-transform"></div>
                                        </div>
                                        <span class="text-sm font-medium pointer-events-none">
                                            <span class="inline">Laki-laki</span>
                                            {{-- <span class="lg:hidden">L</span> --}}
                                        </span>
                                    </label>
                                </div>

                                <div class="relative flex-1">
                                    <input type="radio" id="gender_female" name="jenis_kelamin" value="Perempuan" class="peer sr-only" required {{ old('jenis_kelamin') == 'Perempuan' ? 'checked' : '' }}>    
                                    <label for="gender_female" class="flex items-center justify-center lg:justify-start gap-2 w-full px-3 py-3 border {{ $errors->has('jenis_kelamin') ? 'border-red-300' : 'border-gray-300' }} rounded-xl cursor-pointer hover:bg-gray-50 transition-all text-gray-500 peer-checked:border-[#0F034D] peer-checked:bg-[#0F034D]/5 peer-checked:ring-1 peer-checked:ring-[#0F034D] peer-checked:text-[#0F034D] peer-checked:[&_.radio-circle]:border-[#0F034D] peer-checked:[&_.radio-dot]:scale-100">
                                        <div class="radio-circle relative flex shrink-0 items-center justify-center w-4 h-4 sm:w-5 sm:h-5 rounded-full border-2 border-gray-300 transition-colors pointer-events-none">
                                            <div class="radio-dot w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-[#0F034D] scale-0 transition-transform"></div>
                                        </div>
                                        <span class="text-sm font-medium pointer-events-none">
                                            <span class="inline">Perempuan</span>
                                            {{-- <span class="lg:hidden">P</span> --}}
                                        </span>
                                    </label>
                                </div>
                            </div>
                            @error('jenis_kelamin') 
                                <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> 
                            @enderror
                        </div>

                        <!-- Alamat -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap</label>
                            <textarea name="alamat" rows="3" placeholder="Masukkan alamat lengkap" class="w-full px-4 py-3 border rounded-xl transition-colors text-sm resize-none {{ $errors->has('alamat') ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-[#0F034D]/20 focus:border-[#0F034D]' }}">{{ old('alamat') }}</textarea>
                            @error('alamat') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN (Akses Sistem) -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                    <h3 class="text-lg font-bold text-[#0F034D] mb-6">Akses Sistem</h3>
                    
                    <div class="space-y-6">
                        <!-- Role -->
                        <div class="relative">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Peran / Hak Akses <span class="text-red-500">*</span></label>
                            <input type="hidden" name="role" id="role_input" value="{{ old('role') }}" required>
                            <button type="button" id="role_dropdown_btn" class="w-full flex items-center justify-between px-4 py-3 border {{ $errors->has('role') ? 'border-red-300' : 'border-gray-300' }} rounded-xl bg-white focus:outline-none focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors cursor-pointer">
                                <div id="role_selected_container" class="flex items-center gap-2">
                                    <div id="role_selected_icon" class="hidden text-[#0F034D]"></div>
                                    <span id="role_selected_text" class="text-sm {{ old('role') ? 'text-gray-900 font-medium' : 'text-gray-500' }}">-- Pilih Peran --</span>
                                </div>
                                <svg id="role_chevron" class="w-4 h-4 text-gray-400 transition-transform duration-300 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div id="role_dropdown_menu" class="absolute z-20 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] py-1.5 hidden opacity-0 transition-all duration-200 transform origin-top scale-95">
                                
                                <div class="role-option group flex items-center justify-between px-4 py-2.5 mx-1.5 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors [&.is-active]:bg-gray-100" data-value="admin">
                                    <span class="option-text flex items-center gap-2 text-sm text-gray-700 group-hover:text-[#0F034D] transition-colors group-[.is-active]:text-[#0F034D] group-[.is-active]:font-bold">
                                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D] transition-colors group-[.is-active]:text-[#0F034D]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                            <path d="M11 22c-3.806-1.45-7-3.966-7-9V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1v4"/>
                                            <path d="M14.923 16.547 14 16.164"/>
                                            <path d="m14.923 18.843-.923.383"/>
                                            <path d="M16.547 14.923 16.164 14"/>
                                            <path d="m16.547 20.467-.383.924"/>
                                            <path d="m18.843 14.923.383-.923"/>
                                            <path d="m19.225 21.391-.382-.924"/>
                                            <path d="m20.467 16.547.923-.383"/>
                                            <path d="m20.467 18.843.923.383"/>
                                            <circle cx="17.695" cy="17.695" r="3"/>
                                        </svg>
                                        Admin 
                                    </span>
                                    <svg class="check-icon w-4 h-4 text-[#0F034D] hidden group-[.is-active]:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>

                                <div class="role-option group flex items-center justify-between px-4 py-2.5 mx-1.5 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors [&.is-active]:bg-gray-100" data-value="potong">
                                    <span class="option-text flex items-center gap-2 text-sm text-gray-700 group-hover:text-[#0F034D] transition-colors group-[.is-active]:text-[#0F034D] group-[.is-active]:font-bold">
                                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D] transition-colors group-[.is-active]:text-[#0F034D]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                            <circle cx="6" cy="6" r="3"/>
                                            <path d="M8.12 8.12 12 12"/>
                                            <path d="M20 4 8.12 15.88"/>
                                            <circle cx="6" cy="18" r="3"/>
                                            <path d="M14.8 14.8 20 20"/>
                                        </svg>
                                        Produksi Potong 
                                    </span>
                                    <svg class="check-icon w-4 h-4 text-[#0F034D] hidden group-[.is-active]:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>

                                <div class="role-option group flex items-center justify-between px-4 py-2.5 mx-1.5 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors [&.is-active]:bg-gray-100" data-value="jahit">
                                    <span class="option-text flex items-center gap-2 text-sm text-gray-700 group-hover:text-[#0F034D] transition-colors group-[.is-active]:text-[#0F034D] group-[.is-active]:font-bold">
                                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D] transition-colors group-[.is-active]:text-[#0F034D]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                            <path d="M17 13.44 4.442 17.082A2 2 0 0 0 4.982 21H19a2 2 0 0 0 .558-3.921l-1.115-.32A2 2 0 0 1 17 14.837V7.66"/>
                                            <path d="m7 10.56 12.558-3.642A2 2 0 0 0 19.018 3H5a2 2 0 0 0-.558 3.921l1.115.32A2 2 0 0 1 7 9.163v7.178"/>
                                        </svg>
                                        Produksi Jahit 
                                    </span>
                                    <svg class="check-icon w-4 h-4 text-[#0F034D] hidden group-[.is-active]:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>

                                <div class="role-option group flex items-center justify-between px-4 py-2.5 mx-1.5 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors [&.is-active]:bg-gray-100" data-value="finishing">
                                    <span class="option-text flex items-center gap-2 text-sm text-gray-700 group-hover:text-[#0F034D] transition-colors group-[.is-active]:text-[#0F034D] group-[.is-active]:font-bold">
                                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D] transition-colors group-[.is-active]:text-[#0F034D]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                            <path d="M12 22V12"/>
                                            <path d="m16 17 2 2 4-4"/>
                                            <path d="M21 11.127V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.729l7 4a2 2 0 0 0 2 .001l1.32-.753"/>
                                            <path d="M3.29 7 12 12l8.71-5"/>
                                            <path d="m7.5 4.27 8.997 5.148"/>
                                        </svg>
                                        Produksi Finishing 
                                    </span>
                                    <svg class="check-icon w-4 h-4 text-[#0F034D] hidden group-[.is-active]:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>

                                <div class="role-option group flex items-center justify-between px-4 py-2.5 mx-1.5 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors [&.is-active]:bg-gray-100" data-value="owner">
                                    <span class="option-text flex items-center gap-2 text-sm text-gray-700 group-hover:text-[#0F034D] transition-colors group-[.is-active]:text-[#0F034D] group-[.is-active]:font-bold">
                                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D] transition-colors group-[.is-active]:text-[#0F034D]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                            <path d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z"/>
                                            <path d="M5 21h14"/>
                                        </svg>
                                        Owner 
                                    </span>
                                    <svg class="check-icon w-4 h-4 text-[#0F034D] hidden group-[.is-active]:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('role') 
                                <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> 
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kata Sandi Awal <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="password" name="password" id="password_input" required placeholder="Minimal 8 karakter" class="w-full pl-4 pr-12 py-3 border rounded-xl transition-colors text-sm {{ $errors->has('password') ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-[#0F034D]/20 focus:border-[#0F034D]' }}">
                                <button type="button" id="toggle_password_btn" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-[#0F034D] transition-colors cursor-pointer focus:outline-none">
                                    <svg id="eye_icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg id="eye_off_icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Gunakan kata sandi sementara. Pengguna dapat mengubahnya nanti.</p>
                            @error('password') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Tombol Action -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('owner.users.index') }}" class="flex-1 px-5 py-3 text-sm font-medium text-center text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 rounded-xl transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 px-5 py-3 text-sm font-medium text-center text-white bg-[#0F034D] hover:bg-[#0a0235] shadow-md shadow-[#0F034D]/20 rounded-xl transition-all">
                        Simpan Data
                    </button>
                </div>
            </div>
        </div>
    </form>
    @vite([
        'resources/js/owner/manajemen-pengguna/toggle-dropdown-role.js',
        'resources/js/owner/manajemen-pengguna/toggle-show-password.js'
    ])
</x-layouts.owner>
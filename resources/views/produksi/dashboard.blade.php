<x-layouts.produksi>
    <x-slot:header>
        Dashboard Produksi
    </x-slot:header>

    @php
        $role = auth()->user()->role;
        $roleLabels = ['potong' => 'Tukang Potong', 'jahit' => 'Penjahit', 'finishing' => 'Finishing'];
        $roleLabel = $roleLabels[$role] ?? 'Karyawan Produksi';
    @endphp

    {{-- WELCOME CARD & MINI ACHIEVEMENTS --}}
    <div class="rounded-3xl bg-gradient-to-br from-[#0F034D] to-[#24116f] p-5 sm:p-6 text-white shadow-xl shadow-[#0F034D]/20 mb-6 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <p class="text-sm text-white/70">Selamat bekerja,</p>
            <h2 class="text-2xl font-bold mt-1">{{ auth()->user()->name }}</h2>
            <p class="text-sm text-white/75 mt-2">Anda masuk sebagai <span class="font-semibold text-amber-300">{{ $roleLabel }}</span>.</p>
        </div>
        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 shrink-0">
            <p class="text-[10px] uppercase tracking-wider text-white/60 font-semibold">Selesai Hari Ini</p>
            <p class="text-2xl font-bold mt-0.5 text-white">{{ $selesaiHariIni }} <span class="text-xs font-normal text-white/70">pekerjaan</span></p>
        </div>
    </div>

    {{-- PUSAT TUGAS & INFORMASI HARI INI --}}
    <div class="mb-6">
        <h3 class="text-base font-bold text-[#0F034D] mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                <path d="m9 12 2 2 4-4"/>
            </svg>
            Tugas & Informasi Hari Ini
        </h3>

        <div class="space-y-4">
            @php $hasTask = false; @endphp

            {{-- 1. TUKANG POTONG --}}
            @if ($role === 'potong')
                {{-- Perintah Kerja Baru --}}
                @if ($jumlahPerintahKerja > 0)
                    @php $hasTask = true; @endphp
                    <div class="p-5 bg-amber-50 border border-amber-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
                        <div class="flex items-start gap-3.5">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100/60 shadow-sm">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="9.8" y1="8.2" x2="20" y2="18.4"/><line x1="9.8" y1="15.8" x2="20" y2="5.6"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-amber-900 text-sm">Perintah Kerja Potong Baru</h4>
                                <p class="text-xs text-amber-800 mt-1 leading-relaxed">Ada <strong>{{ $jumlahPerintahKerja }}</strong> perintah kerja aktif yang menunggu untuk Anda potong.</p>
                            </div>
                        </div>
                        <a href="{{ route('produksi.perintah-produksi.index') }}" class="inline-flex items-center justify-center px-4.5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-xl text-xs transition-colors shrink-0 shadow-sm shadow-amber-600/10 cursor-pointer">
                            Mulai Potong Kain
                        </a>
                    </div>
                @endif

                {{-- Ajuan Pengambilan Baru --}}
                @if ($jumlahAjuanMasuk > 0)
                    @php $hasTask = true; @endphp
                    <div class="p-5 bg-blue-50 border border-blue-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
                        <div class="flex items-start gap-3.5">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100/60 shadow-sm">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-blue-900 text-sm">Ajuan Masuk dari Penjahit</h4>
                                <p class="text-xs text-blue-800 mt-1 leading-relaxed">Ada <strong>{{ $jumlahAjuanMasuk }}</strong> ajuan pengambilan bahan dari penjahit yang perlu Anda setujui/verifikasi.</p>
                            </div>
                        </div>
                        <a href="{{ route('produksi.ajuan-pengambilan.masuk') }}" class="inline-flex items-center justify-center px-4.5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs transition-colors shrink-0 shadow-sm shadow-blue-600/10 cursor-pointer">
                            Lihat Ajuan Masuk
                        </a>
                    </div>
                @endif
            @endif

            {{-- 2. PENJAHIT --}}
            @if ($role === 'jahit')
                {{-- Barang Ready di Potong --}}
                @if ($jumlahBarangReady > 0)
                    @php $hasTask = true; @endphp
                    <div class="p-5 bg-blue-50 border border-blue-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
                        <div class="flex items-start gap-3.5">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100/60 shadow-sm">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-blue-900 text-sm">Bahan Siap Diambil</h4>
                                <p class="text-xs text-blue-800 mt-1 leading-relaxed">Ada <strong>{{ $jumlahBarangReady }}</strong> bahan yang sudah selesai dipotong di Tukang Potong dan siap Anda ambil.</p>
                            </div>
                        </div>
                        <a href="{{ route('produksi.ajuan-pengambilan.index') }}" class="inline-flex items-center justify-center px-4.5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs transition-colors shrink-0 shadow-sm shadow-blue-600/10 cursor-pointer">
                            Ajukan Pengambilan
                        </a>
                    </div>
                @endif

                {{-- Pekerjaan Aktif (Hold) --}}
                @if ($jumlahPekerjaanAktif > 0)
                    @php $hasTask = true; @endphp
                    <div class="p-5 bg-amber-50 border border-amber-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
                        <div class="flex items-start gap-3.5">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100/60 shadow-sm">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-amber-900 text-sm">Selesaikan Pekerjaan Jahit</h4>
                                <p class="text-xs text-amber-800 mt-1 leading-relaxed">Anda memegang <strong>{{ $jumlahPekerjaanAktif }}</strong> pekerjaan jahit aktif. Jangan lupa untuk menginput hasil jahitan jika sudah selesai.</p>
                            </div>
                        </div>
                        <a href="{{ route('produksi.perintah-produksi.index') }}" class="inline-flex items-center justify-center px-4.5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-xl text-xs transition-colors shrink-0 shadow-sm shadow-amber-600/10 cursor-pointer">
                            Input Hasil Jahit
                        </a>
                    </div>
                @endif

                {{-- Ajuan Masuk dari Finishing --}}
                @if ($jumlahAjuanMasuk > 0)
                    @php $hasTask = true; @endphp
                    <div class="p-5 bg-blue-50 border border-blue-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
                        <div class="flex items-start gap-3.5">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100/60 shadow-sm">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-blue-900 text-sm">Ajuan Masuk dari Finishing</h4>
                                <p class="text-xs text-blue-800 mt-1 leading-relaxed">Ada <strong>{{ $jumlahAjuanMasuk }}</strong> ajuan pengambilan barang dari bagian finishing yang perlu Anda setujui/verifikasi.</p>
                            </div>
                        </div>
                        <a href="{{ route('produksi.ajuan-pengambilan.masuk') }}" class="inline-flex items-center justify-center px-4.5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs transition-colors shrink-0 shadow-sm shadow-blue-600/10 cursor-pointer">
                            Lihat Ajuan Masuk
                        </a>
                    </div>
                @endif
            @endif

            {{-- 3. FINISHING --}}
            @if ($role === 'finishing')
                {{-- Barang Ready di Jahit --}}
                @if ($jumlahBarangReady > 0)
                    @php $hasTask = true; @endphp
                    <div class="p-5 bg-blue-50 border border-blue-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
                        <div class="flex items-start gap-3.5">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100/60 shadow-sm">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-blue-900 text-sm">Barang Siap Diambil</h4>
                                <p class="text-xs text-blue-800 mt-1 leading-relaxed">Ada <strong>{{ $jumlahBarangReady }}</strong> hasil jahit yang sudah selesai di Penjahit dan siap Anda ambil untuk finishing.</p>
                            </div>
                        </div>
                        <a href="{{ route('produksi.ajuan-pengambilan.index') }}" class="inline-flex items-center justify-center px-4.5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs transition-colors shrink-0 shadow-sm shadow-blue-600/10 cursor-pointer">
                            Ajukan Pengambilan
                        </a>
                    </div>
                @endif

                {{-- Pekerjaan Aktif (Hold) --}}
                @if ($jumlahPekerjaanAktif > 0)
                    @php $hasTask = true; @endphp
                    <div class="p-5 bg-amber-50 border border-amber-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
                        <div class="flex items-start gap-3.5">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100/60 shadow-sm">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-amber-900 text-sm">Selesaikan Pekerjaan Finishing</h4>
                                <p class="text-xs text-amber-800 mt-1 leading-relaxed">Anda memegang <strong>{{ $jumlahPekerjaanAktif }}</strong> pekerjaan finishing aktif. Silakan catat/input hasil finishing ke sistem.</p>
                            </div>
                        </div>
                        <a href="{{ route('produksi.perintah-produksi.index') }}" class="inline-flex items-center justify-center px-4.5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-xl text-xs transition-colors shrink-0 shadow-sm shadow-amber-600/10 cursor-pointer">
                            Input Hasil Finishing
                        </a>
                    </div>
                @endif
            @endif

            {{-- 4. STATE: SEMUA BERSIH/TIDAK ADA TUGAS --}}
            @if (!$hasTask)
                <div class="p-6 bg-green-50 border border-green-200 rounded-2xl text-center shadow-sm">
                    <svg class="w-12 h-12 text-green-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <h4 class="font-bold text-green-900 text-sm mt-3">Semua Pekerjaan Selesai</h4>
                    <p class="text-xs text-green-800 mt-1.5 leading-relaxed">Tidak ada tugas tertunda yang membutuhkan tindakan Anda saat ini. Kerja bagus!</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ALUR KERJA SISTEM --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-start justify-between gap-3 mb-5">
            <div>
                <h3 class="font-bold text-[#0F034D]">Alur Kerja Sistem</h3>
                <p class="text-sm text-gray-500 mt-1">Ikuti langkah berikut agar pekerjaan produksi tercatat rapi di sistem.</p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-[#0F034D]/5 border border-[#0F034D]/10 text-[11px] font-semibold text-[#0F034D] shrink-0">3 Langkah</span>
        </div>

        <div class="space-y-0">
            <div class="relative flex gap-3 pb-5">
                <div class="absolute left-3.5 top-8 bottom-0 w-px bg-[#0F034D]/20"></div>
                <div class="relative z-10 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#0F034D] text-white text-xs font-bold shadow-md shadow-[#0F034D]/20">1</div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-[#0F034D]">Lihat pekerjaan</p>
                    <p class="text-xs text-gray-500 leading-relaxed mt-0.5">Cek Perintah Produksi yang sudah disetujui owner dan siap diproses sesuai peran Anda.</p>
                    <a href="{{ route('produksi.perintah-produksi.index') }}" class="inline-flex items-center gap-1.5 mt-2 text-xs font-bold text-[#0F034D] hover:text-[#24116f] transition-colors">
                        Klik di sini untuk melihat pekerjaan
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>

            <div class="relative flex gap-3 pb-5">
                <div class="absolute left-3.5 top-8 bottom-0 w-px bg-gray-200"></div>
                <div class="relative z-10 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-500 text-xs font-bold">2</div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-700">Input hasil pekerjaan</p>
                    <p class="text-xs text-gray-500 leading-relaxed mt-0.5">Catat hasil produksi, jumlah selesai, dan barang reject agar stok virtual selalu terbarui.</p>
                    <a href="{{ route('produksi.perintah-produksi.index') }}" class="inline-flex items-center gap-1.5 mt-2 text-xs font-bold text-[#0F034D] hover:text-[#24116f] transition-colors">
                        Klik di sini untuk pilih pekerjaan dan input hasil
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>

            <div class="relative flex gap-3">
                <div class="relative z-10 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-500 text-xs font-bold">3</div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-700">Kelola ajuan barang</p>
                    <p class="text-xs text-gray-500 leading-relaxed mt-0.5">Buat atau cek status ajuan pengambilan barang antar tahap produksi.</p>
                    <a href="{{ route('produksi.ajuan-pengambilan.index') }}" class="inline-flex items-center gap-1.5 mt-2 text-xs font-bold text-[#0F034D] hover:text-[#24116f] transition-colors">
                        Klik di sini untuk kelola ajuan barang
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.produksi>

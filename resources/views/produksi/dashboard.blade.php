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
    @php
        $hasAlerts = ($role === 'potong' && ($jumlahPerintahKerja > 0 || $jumlahAjuanMasuk > 0))
            || ($role === 'jahit' && ($jumlahBarangReady > 0 || $jumlahAjuanMasuk > 0))
            || ($role === 'finishing' && ($jumlahBarangReady > 0));
        $hasActiveJobs = isset($pekerjaanAktifList) && $pekerjaanAktifList->count() > 0;
    @endphp

    <div class="mb-6">
        <h3 class="text-base font-bold text-[#0F034D] mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                <path d="m9 12 2 2 4-4"/>
            </svg>
            Pekerjaan & Informasi Hari Ini
        </h3>

        <div class="space-y-4">
            {{-- 1. TUKANG POTONG --}}
            @if ($role === 'potong')
                {{-- Perintah Kerja Baru --}}
                @if ($jumlahPerintahKerja > 0)
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

                {{-- Ajuan Masuk dari Finishing --}}
                @if ($jumlahAjuanMasuk > 0)
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
            @endif

            {{-- DAFTAR PEKERJAAN SEDANG DIKERJAKAN --}}
            @if($hasActiveJobs)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                    @if($role === 'potong')
                        @foreach($pekerjaanAktifList as $wo)
                            @php
                                $totalRoll = $wo->details->sum('qty_roll_pakai');
                                $totalPcs = $wo->details->sum('estimasi_pcs');
                                $doneCount = $wo->details->filter(fn($d) => $wo->stokVirtual->where('id_detail_perintah', $d->id)->first()?->is_selesai)->count();
                                $totalDetails = $wo->details->count();
                            @endphp
                            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:border-[#0F034D]/20 hover:shadow-md transition-all flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="font-bold text-[#0F034D] text-base">{{ $wo->nomor_wo }}</h4>
                                                <span class="px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 border border-purple-100 text-[10px] font-bold">Dalam Produksi</span>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">{{ $totalDetails }} jenis produk ({{ number_format($totalRoll, 0, ',', '.') }} Roll Kain)</p>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-full bg-[#0F034D]/5 text-[#0F034D] text-xs font-bold shrink-0">
                                            {{ $doneCount }}/{{ $totalDetails }} Selesai
                                        </span>
                                    </div>

                                    <div class="space-y-2 mb-4">
                                        @foreach($wo->details->take(2) as $det)
                                            <div class="flex items-center justify-between text-xs p-2 bg-gray-50 rounded-xl border border-gray-100">
                                                <span class="font-medium text-gray-800">{{ $det->produk->nama_produk ?? 'Produk' }} - {{ ucfirst($det->produk->warna ?? '-') }} ({{ strtoupper($det->produk->ukuran ?? '-') }})</span>
                                                <span class="font-bold text-[#0F034D]">{{ number_format($det->qty_pcs_potong ?? 0) }} / {{ number_format($det->estimasi_pcs) }} pcs</span>
                                            </div>
                                        @endforeach
                                        @if($wo->details->count() > 2)
                                            <p class="text-[11px] text-gray-400 text-right">+ {{ $wo->details->count() - 2 }} produk lainnya</p>
                                        @endif
                                    </div>
                                </div>

                                <a href="{{ route('produksi.perintah-produksi.show', $wo->id) }}" class="w-full py-2.5 px-4 rounded-xl bg-[#0F034D] hover:bg-[#24116f] text-white text-xs font-bold flex items-center justify-center gap-2 transition-colors shadow-sm cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Lihat Perintah Produksi & Input Hasil
                                </a>
                            </div>
                        @endforeach
                    @else
                        @foreach($pekerjaanAktifList as $sv)
                            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:border-[#0F034D]/20 hover:shadow-md transition-all flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="font-bold text-[#0F034D] text-base">{{ $sv->perintahProduksi->nomor_wo ?? 'WO' }}</h4>
                                                <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold">Diproses</span>
                                            </div>
                                            @php
                                                $produkObj = $sv->detailPerintahProduksi->produk ?? $sv->produk;
                                            @endphp
                                            <p class="text-xs font-semibold text-gray-800 mt-1">
                                                {{ $produkObj->nama_produk ?? 'Produk' }} - {{ ucfirst($produkObj->warna ?? '-') }} ({{ strtoupper($produkObj->ukuran ?? '-') }})
                                            </p>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="text-xs text-gray-400 block">Diproses</span>
                                            <span class="text-base font-bold text-amber-600 tabular-nums">{{ number_format($sv->qty_hold, 0, ',', '.') }} pcs</span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2 mb-4 text-xs">
                                        <div class="p-2 bg-emerald-50/50 rounded-xl border border-emerald-100">
                                            <span class="text-[10px] text-emerald-600 block">Selesai Diinput</span>
                                            <span class="font-bold text-emerald-700">{{ number_format($sv->total_selesai, 0, ',', '.') }} pcs</span>
                                        </div>
                                        <div class="p-2 bg-amber-50/50 rounded-xl border border-amber-100">
                                            <span class="text-[10px] text-amber-600 block">Barang Cacat</span>
                                            <span class="font-bold text-amber-700">{{ number_format($sv->total_reject, 0, ',', '.') }} pcs</span>
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ route('produksi.perintah-produksi.show', $sv->id_perintah) }}" class="w-full py-2.5 px-4 rounded-xl bg-[#0F034D] hover:bg-[#24116f] text-white text-xs font-bold flex items-center justify-center gap-2 transition-colors shadow-sm cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Lihat Perintah Produksi & Input Hasil
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
            @endif

            {{-- 4. SINGLE UNIFIED EMPTY STATE SANGAT BERSIH (Hanya jika tidak ada alert DAN tidak ada pekerjaan aktif) --}}
            @if(!$hasAlerts && !$hasActiveJobs)
                <div class="p-6 bg-gray-50 border border-gray-200/60 rounded-2xl text-center shadow-sm">
                    <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    </div>
                    <p class="text-xs font-semibold text-gray-500">Belum ada pekerjaan aktif atau pemberitahuan baru saat ini.</p>
                    @if($role === 'potong')
                        <p class="text-[11px] text-gray-400 mt-0.5">Pekerjaan akan muncul di sini setelah perintah kerja disetujui owner dan Anda mengklik "Mulai Kerjakan WO Ini".</p>
                    @else
                        <p class="text-[11px] text-gray-400 mt-0.5">Pekerjaan akan muncul di sini setelah ajuan pengambilan barang Anda disetujui oleh penyetuju.</p>
                    @endif
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

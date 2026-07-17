<x-layouts.produksi>
    <x-slot:header>
        Dashboard Produksi
    </x-slot:header>

    @php
        $role = auth()->user()->role;
        $roleLabels = ['potong' => 'Tukang Potong', 'jahit' => 'Penjahit', 'finishing' => 'Finishing'];
        $roleLabel = $roleLabels[$role] ?? 'Karyawan Produksi';
    @endphp

    <div class="rounded-3xl bg-gradient-to-br from-[#0F034D] to-[#24116f] p-5 sm:p-6 text-white shadow-xl shadow-[#0F034D]/20 mb-5">
        <p class="text-sm text-white/70">Selamat bekerja,</p>
        <h2 class="text-2xl font-bold mt-1">{{ auth()->user()->name }}</h2>
        <p class="text-sm text-white/75 mt-2">Anda masuk sebagai {{ $roleLabel }}.</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-5">
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-500">Pekerjaan Aktif</p>
            <p class="text-2xl font-bold mt-1 text-[#0F034D]">{{ $pekerjaanAktif }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Perintah produksi yang harus dikerjakan</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-500">Menunggu Input</p>
            <p class="text-2xl font-bold mt-1 text-amber-600">{{ $menungguInput }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Perintah produksi menunggu input anda</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-500">Ajuan Masuk</p>
            <p class="text-2xl font-bold mt-1 text-blue-700">{{ $ajuanMasuk }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Ajuan menunggu persetujuan Anda</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-500">Selesai Hari Ini</p>
            <p class="text-2xl font-bold mt-1 text-green-600">{{ $selesaiHariIni }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Produk yang sudah ditandai selesai</p>
        </div>
    </div>

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

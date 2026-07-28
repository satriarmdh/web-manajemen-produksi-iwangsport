<x-layouts.owner>
    <x-slot:breadcrumb>
        <li class="flex items-center gap-1.5 text-gray-400">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <span class="select-none">Produksi &amp; Persetujuan</span>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold gap-1.5">
            <svg class="w-3 h-3 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            Persetujuan Perintah Produksi
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Persetujuan Perintah Produksi
    </x-slot:header>

    @php
        $totalPending = $perintahProduksi->total();
        $totalProdukHalaman = $perintahProduksi->getCollection()->sum(fn ($wo) => $wo->details->count());
        $totalRollHalaman = $perintahProduksi->getCollection()->sum(fn ($wo) => $wo->details->sum('qty_roll_pakai'));
        $totalEstimasiHalaman = $perintahProduksi->getCollection()->sum(fn ($wo) => $wo->details->sum('estimasi_pcs'));
    @endphp

    <!-- Stat Cards Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <!-- Card 1: WO Pending (Amber - butuh perhatian) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between gap-3 min-h-[92px] hover:border-amber-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider truncate">WO Pending</p>
                <h3 class="text-2xl font-bold {{ $totalPending > 0 ? 'text-amber-600' : 'text-gray-950' }} mt-1 leading-none tabular-nums">{{ number_format($totalPending, 0, ',', '.') }} <span class="text-xs font-normal text-gray-400">perintah produksi</span></h3>
                <span class="text-[11px] text-gray-400 mt-1.5 block font-medium">Menunggu persetujuan Anda</span>
            </div>
            <div class="w-11 h-11 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-amber-100 group-hover:scale-105 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Card 2: Produk Ditinjau -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between gap-3 min-h-[92px] hover:border-gray-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider truncate">Produk Ditinjau</p>
                <h3 class="text-2xl font-bold text-gray-950 mt-1 leading-none tabular-nums">{{ number_format($totalProdukHalaman, 0, ',', '.') }} <span class="text-xs font-normal text-gray-400">jenis</span></h3>
                <span class="text-[11px] text-gray-400 mt-1.5 block font-medium">Di halaman ini</span>
            </div>
            <div class="w-11 h-11 bg-gray-50 text-gray-500 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-gray-100 group-hover:scale-105 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
        </div>

        <!-- Card 3: Total Roll -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between gap-3 min-h-[92px] hover:border-gray-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider truncate">Total Roll</p>
                <h3 class="text-2xl font-bold text-gray-950 mt-1 leading-none tabular-nums">{{ number_format($totalRollHalaman, 0, ',', '.') }} <span class="text-xs font-normal text-gray-400">roll</span></h3>
                <span class="text-[11px] text-gray-400 mt-1.5 block font-medium">Dipakai di halaman ini</span>
            </div>
            <div class="w-11 h-11 bg-gray-50 text-gray-500 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-gray-100 group-hover:scale-105 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>

        <!-- Card 4: Estimasi PCS -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between gap-3 min-h-[92px] hover:border-gray-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider truncate">Estimasi PCS</p>
                <h3 class="text-2xl font-bold text-gray-950 mt-1 leading-none tabular-nums">{{ number_format($totalEstimasiHalaman, 0, ',', '.') }} <span class="text-xs font-normal text-gray-400">pcs</span></h3>
                <span class="text-[11px] text-gray-400 mt-1.5 block font-medium">Perkiraan hasil produksi</span>
            </div>
            <div class="w-11 h-11 bg-gray-50 text-gray-500 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-gray-100 group-hover:scale-105 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m-6 4h.01M13 11h.01M9 15h.01M13 15h.01M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
            </div>
        </div>
    </div>

    <!-- Combined Card: Judul + Deskripsi + Search + Sort -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6">
        <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-[#0F034D]">Daftar Perintah Produksi Menunggu Persetujuan</h3>
                <p class="text-sm text-gray-500 mt-1 max-w-3xl">Review perintah produksi dari admin sebelum masuk siklus produksi. Pastikan produk, penggunaan roll, dan estimasi hasil sudah sesuai.</p>
            </div>
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-50 border border-amber-100 text-amber-700 text-sm font-semibold shrink-0">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-500 opacity-60"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                </span>
                {{ $totalPending }} WO Pending
            </div>
        </div>

        <div class="px-6 py-3 bg-gray-50/50 border-b border-gray-100 flex flex-col sm:flex-row items-center gap-4 rounded-b-2xl">
            @if(request()->hasAny(['search', 'sort']))
                <a href="{{ route('owner.perintah-produksi.index') }}" title="Hapus Filter & Pencarian" class="hidden sm:flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 rounded-xl transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
            @endif

            <form method="GET" action="{{ route('owner.perintah-produksi.index') }}" class="relative flex-1 w-full">
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor Perintah Produksi atau nama admin pembuat..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors bg-white shadow-sm">
            </form>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'terbaru']) }}" class="flex-1 sm:flex-none px-4 py-2.5 text-center text-sm font-medium rounded-xl border transition-colors {{ !request('sort') || request('sort') === 'terbaru' ? 'border-[#0F034D] text-[#0F034D] bg-[#0F034D]/5' : 'border-gray-200 text-gray-600 bg-white hover:bg-gray-50' }}">Terbaru</a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'terlama']) }}" class="flex-1 sm:flex-none px-4 py-2.5 text-center text-sm font-medium rounded-xl border transition-colors {{ request('sort') === 'terlama' ? 'border-[#0F034D] text-[#0F034D] bg-[#0F034D]/5' : 'border-gray-200 text-gray-600 bg-white hover:bg-gray-50' }}">Terlama</a>
            </div>
        </div>
    </div>

    @if($perintahProduksi->count() > 0)
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
            @foreach($perintahProduksi as $wo)
                @php
                    $totalRoll = $wo->details->sum('qty_roll_pakai');
                    $totalEstimasi = $wo->details->sum('estimasi_pcs');
                @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-[0_12px_40px_rgba(15,3,77,0.08)] transition-all h-full flex flex-col">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 mb-4">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-bold text-[#0F034D] text-base">{{ $wo->nomor_wo }}</h3>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border bg-amber-50 text-amber-700 border-amber-100">Pending Approval</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Dibuat {{ $wo->created_at->format('d M Y, H:i') }} oleh {{ $wo->user->name ?? '-' }}</p>
                        </div>
                        <div class="text-left sm:text-right">
                            <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold">Mulai Produksi</p>
                            <p class="text-sm font-bold text-[#0F034D]">{{ \Carbon\Carbon::parse($wo->tgl_mulai)->format('d M Y') }}</p>
                            @if($wo->tgl_selesai)
                                <p class="text-xs text-gray-400">Target selesai {{ \Carbon\Carbon::parse($wo->tgl_selesai)->format('d M Y') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <div class="rounded-xl bg-[#0F034D]/5 border border-[#0F034D]/10 p-3"><p class="text-[11px] text-[#0F034D]/60 mb-1">Jenis Produk</p><p class="text-sm font-bold text-[#0F034D]">{{ $wo->details->count() }}</p></div>
                        <div class="rounded-xl bg-[#0F034D]/5 border border-[#0F034D]/10 p-3"><p class="text-[11px] text-[#0F034D]/60 mb-1">Total Roll</p><p class="text-sm font-bold text-[#0F034D]">{{ number_format($totalRoll, 0, ',', '.') }}</p></div>
                        <div class="rounded-xl bg-green-50 border border-green-100 p-3"><p class="text-[11px] text-green-700/70 mb-1">Estimasi</p><p class="text-sm font-bold text-green-800">{{ number_format($totalEstimasi, 0, ',', '.') }}</p></div>
                    </div>

                    <div class="rounded-xl border border-gray-100 overflow-hidden mb-4 flex-1 flex flex-col">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Detail Produk</p>
                            <p class="text-xs text-gray-400">Estimasi berdasarkan baseline produksi</p>
                        </div>
                        <div class="divide-y divide-gray-100 max-h-56 overflow-y-auto flex-1">
                            @foreach($wo->details as $detail)
                                <div class="px-4 py-3 flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        @php
                                            $warnaProduk = strtolower($detail->produk->warna ?? '-');
                                            $warnaDotMap = [
                                                'hitam' => '#111827',
                                                'navy' => '#061952',
                                                'abu-abu' => '#9CA3AF',
                                                'abu' => '#9CA3AF',
                                                'putih' => '#FFFFFF',
                                            ];
                                            $warnaDot = $warnaDotMap[$warnaProduk] ?? '#CBD5E1';
                                            $needsStroke = in_array($warnaProduk, ['abu-abu', 'abu', 'putih'], true);
                                        @endphp
                                        <div class="flex items-center gap-2 min-w-0">
                                            <p class="text-sm font-semibold text-[#0F034D] truncate">{{ $detail->produk->nama_produk ?? '-' }} - {{ ucfirst($detail->produk->warna ?? '-') }}</p>
                                            <span class="inline-block w-3 h-3 rounded-full shrink-0 {{ $needsStroke ? 'ring-1 ring-gray-300' : '' }}" style="background-color: {{ $warnaDot }}" title="Warna {{ ucfirst($detail->produk->warna ?? '-') }}"></span>
                                        </div>
                                        <p class="text-xs text-gray-500 truncate">{{ $detail->bahanBaku->nama_bahan ?? '-' }}</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 shrink-0 text-right">
                                        <div><p class="text-[11px] text-gray-400">Roll</p><p class="text-xs font-bold text-[#0F034D]">{{ number_format($detail->qty_roll_pakai, 0, ',', '.') }}</p></div>
                                        <div><p class="text-[11px] text-gray-400">PCS</p><p class="text-xs font-bold text-green-700">{{ number_format($detail->estimasi_pcs, 0, ',', '.') }}</p></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-xl bg-blue-50 border border-blue-100 p-3 mb-4">
                        <p class="text-xs text-blue-700 leading-relaxed">
                            Jika disetujui, Perintah Produksi akan berubah menjadi <span class="font-semibold">Disetujui</span> dan dapat dilanjutkan ke siklus produksi oleh tim terkait.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-3 border-t border-gray-50 mt-auto">
                        <form action="{{ route('owner.perintah-produksi.approve', $wo) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" data-confirm-action="Setujui perintah produksi {{ $wo->nomor_wo }}?"
                                class="w-full inline-flex items-center justify-center gap-2 py-2.5 text-sm font-semibold text-white bg-green-600 rounded-xl hover:bg-green-700 transition-colors shadow-md shadow-green-600/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                Setujui WO
                            </button>
                        </form>
                        <form action="{{ route('owner.perintah-produksi.reject', $wo) }}" method="POST" class="flex-1" onsubmit="return confirm('Tolak perintah produksi {{ $wo->nomor_wo }}?')">
                            @csrf
                            <input type="hidden" name="alasan_penolakan" value="Ditolak oleh owner setelah review perintah produksi">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-2.5 text-sm font-semibold text-red-600 bg-red-50 border border-red-100 rounded-xl hover:bg-red-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Tolak
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6"><x-pagination.custom-global-pagination :paginator="$perintahProduksi" /></div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12">
            <div class="flex flex-col items-center justify-center gap-3 text-center">
                <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center">
                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-500">Tidak ada perintah produksi menunggu persetujuan</p>
                    <p class="text-xs text-gray-400 mt-0.5">Semua perintah produksi sudah direview. Perintah baru dari admin akan muncul di sini.</p>
                </div>
            </div>
        </div>
    @endif
    @vite('resources/js/admin/confirm-action.js')
</x-layouts.owner>

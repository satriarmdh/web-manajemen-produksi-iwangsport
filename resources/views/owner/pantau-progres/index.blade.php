<x-layouts.owner>
    <x-slot:breadcrumb>
        <li class="flex items-center text-gray-400">
            <span class="select-none">Produksi &amp; Persetujuan</span>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold gap-1.5">
            <svg class="w-3 h-3 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            Pantau Progres Produksi
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Pantau Progres Produksi
    </x-slot:header>

    @php
        $statusColors = [
            'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
            'disetujui' => 'bg-blue-50 text-blue-700 border-blue-100',
            'dalam_produksi' => 'bg-purple-50 text-purple-700 border-purple-100',
            'selesai' => 'bg-green-50 text-green-700 border-green-100',
            'ditolak' => 'bg-red-50 text-red-700 border-red-100',
        ];
        $statusLabels = [
            'disetujui' => 'Disetujui',
            'dalam_produksi' => 'Dalam Produksi',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ];
    @endphp

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
        <!-- Header -->
        <div class="px-6 pt-6 pb-4 border-b border-gray-100 rounded-t-xl">
            <h3 class="text-lg font-bold text-[#0F034D]">Pantau Progres Produksi</h3>
            <p class="text-sm text-gray-500 mt-1">Lacak jalannya proses produksi pakaian mulai dari pemotongan, penjahitan, hingga finishing.</p>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="px-6 py-3 bg-gray-50/50 border-b border-gray-100 flex flex-col sm:flex-row items-center gap-4 relative z-20">
            <div class="flex items-center gap-3 w-full sm:w-auto shrink-0 relative">
                <!-- Filter Status -->
                <div class="relative" data-dropdown="status">
                    <button type="button" id="btn-dropdown-status" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-white border {{ request('status') ? 'border-[#0F034D] text-[#0F034D] bg-blue-50/50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        {{ request('status') ? ($statusLabels[request('status')] ?? 'Filter') : 'Status' }}
                        @if(request('status'))
                            <span class="flex h-2 w-2 relative"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#0F034D] opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-[#0F034D]"></span></span>
                        @endif
                    </button>
                    <div id="dropdown-status" class="absolute left-0 mt-2 w-full sm:w-56 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden py-2">
                        <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">Status Produksi</div>
                        <div class="px-2 space-y-0.5">
                            <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ !request('status') ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Semua Status</a>
                            @foreach($statusLabels as $value => $label)
                                <a href="{{ request()->fullUrlWithQuery(['status' => $value]) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('status') == $value ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">{{ $label }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Filter Tgl Mulai -->
                <div class="relative" data-dropdown="tanggal-mulai">
                    <button type="button" id="btn-dropdown-tanggal-mulai" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-white border {{ request('tanggal_mulai') ? 'border-[#0F034D] text-[#0F034D] bg-blue-50/50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ request('tanggal_mulai') ? \Carbon\Carbon::parse(request('tanggal_mulai'))->format('d M Y') : 'Tanggal Mulai' }}
                        <svg class="dropdown-arrow w-3.5 h-3.5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div id="dropdown-tanggal-mulai" class="absolute left-0 mt-2 w-72 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden p-4">
                        <form method="GET" action="{{ route('owner.pantau-progres.index') }}">
                            @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Pilih Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors">
                                </div>
                                <div class="flex gap-2 pt-1">
                                    <a href="{{ route('owner.pantau-progres.index', ['status' => request('status'), 'sort' => request('sort'), 'search' => request('search')]) }}" class="flex-1 px-3 py-1.5 text-center bg-gray-100 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
                                    <button type="submit" class="flex-1 px-3 py-1.5 bg-[#0F034D] text-white text-xs font-medium rounded-lg hover:bg-[#0a0235] transition-colors">Terapkan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Sort -->
                <div class="relative" data-dropdown="sort">
                    <button type="button" id="btn-dropdown-sort" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-white border {{ request('sort') ? 'border-[#0F034D] text-[#0F034D] bg-blue-50/50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 16 4 4 4-4"/><path d="M7 20V4"/><path d="m21 8-4-4-4 4"/><path d="M17 4v16"/></svg>
                        {{ request('sort') == 'terlama' ? 'Terlama' : 'Terbaru' }}
                    </button>
                    <div id="dropdown-sort" class="absolute left-0 mt-2 w-full sm:w-56 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden py-2">
                        <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">Waktu Dibuat</div>
                        <div class="px-2 space-y-0.5">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'terbaru']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ !request('sort') || request('sort') == 'terbaru' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Terbaru</a>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'terlama']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('sort') == 'terlama' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Terlama</a>
                        </div>
                    </div>
                </div>
            </div>

            @if(request()->hasAny(['search', 'status', 'sort', 'tanggal_mulai']))
                <a href="{{ route('owner.pantau-progres.index') }}" title="Hapus Semua Filter & Pencarian" class="hidden sm:flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 rounded-xl transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </a>
            @endif

            <!-- Search -->
            <div class="flex-1 w-full">
                <form method="GET" action="{{ route('owner.pantau-progres.index') }}" class="relative w-full">
                    @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                    @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                    @if(request('tanggal_mulai')) <input type="hidden" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"> @endif
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nomor WO untuk mencari..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors bg-white shadow-sm">
                </form>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <p class="text-sm text-gray-500">Menampilkan <span class="font-semibold text-[#0F034D]">{{ $perintahProduksi->count() }}</span> dari <span class="font-semibold text-[#0F034D]">{{ $perintahProduksi->total() }}</span> perintah produksi</p>
    </div>

    @if($perintahProduksi->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($perintahProduksi as $wo)
                @php
                    $totalRoll = $wo->details->sum('qty_roll_pakai');
                    $totalEstimasi = $wo->details->sum('estimasi_pcs');
                    $statusClass = $statusColors[$wo->status_produksi] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                    $statusLabel = $statusLabels[$wo->status_produksi] ?? $wo->status_produksi;
                @endphp
                <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-[0_12px_40px_rgba(15,3,77,0.08)] hover:-translate-y-0.5 transition-all duration-200 h-full flex flex-col">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div>
                            <h3 class="font-bold text-[#0F034D] text-sm tracking-tight">{{ $wo->nomor_wo }}</h3>
                            <p class="text-xs text-gray-400 mt-1">Dibuat {{ $wo->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-gray-50/80 rounded-xl p-3 border border-gray-100">
                            <p class="text-[10px] text-gray-400 mb-0.5">Estimasi</p>
                            <p class="text-sm font-bold text-[#0F034D]">{{ number_format($totalEstimasi, 0, ',', '.') }} pcs</p>
                        </div>
                        <div class="bg-gray-50/80 rounded-xl p-3 border border-gray-100">
                            <p class="text-[10px] text-gray-400 mb-0.5">Penggunaan Kain</p>
                            <p class="text-sm font-bold text-[#0F034D]">{{ number_format($totalRoll, 0, ',', '.') }} roll</p>
                        </div>
                    </div>

                    <div class="space-y-2 mb-4 flex-1">
                        <div class="flex justify-between text-xs py-1 border-b border-gray-50">
                            <span class="text-gray-400">Tanggal Mulai</span>
                            <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($wo->tgl_mulai)->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between text-xs py-1 border-b border-gray-50">
                            <span class="text-gray-400">Target Selesai</span>
                            <span class="font-medium text-gray-700">{{ $wo->tgl_selesai ? \Carbon\Carbon::parse($wo->tgl_selesai)->format('d M Y') : '-' }}</span>
                        </div>
                    </div>

                    <a href="{{ route('owner.pantau-progres.show', $wo) }}" class="w-full text-center py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-xs font-bold rounded-xl transition-all duration-200 cursor-pointer shadow-md shadow-[#0F034D]/10">
                        Pantau Progres
                    </a>
                </div>
            @endforeach
        </div>

        @if($perintahProduksi->hasPages())
            <div class="mt-6">
                <x-pagination.custom-global-pagination :paginator="$perintahProduksi" />
            </div>
        @endif
    @else
        <div class="bg-white rounded-xl border border-gray-100 p-16 text-center shadow-sm">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <h3 class="text-sm font-bold text-gray-800">Belum Ada Perintah Produksi</h3>
            <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">Daftar progres pengerjaan barang kosong karena admin belum membuat perintah produksi.</p>
        </div>
    @endif

    @vite([
        'resources/js/owner/pantau-progres/index.js'
    ])
</x-layouts.owner>

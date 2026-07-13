<x-layouts.admin>
    <x-slot:breadcrumb>
        <li class="flex items-center">
            <span class="text-gray-400 select-none">Produksi</span>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            Perintah Produksi
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Perintah Produksi
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
            'pending' => 'Pending',
            'disetujui' => 'Disetujui',
            'dalam_produksi' => 'Dalam Produksi',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ];
    @endphp

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-t-xl">
            <div>
                <h3 class="text-lg font-bold text-[#0F034D]">Daftar Perintah Produksi</h3>
                <p class="text-sm text-gray-500 mt-1">Kelola perintah produksi, detail penggunaan kain, dan status pengerjaan.</p>
            </div>
            <a href="{{ route('admin.perintah-produksi.create') }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-sm font-medium rounded-xl transition-all shadow-md shadow-[#0F034D]/20 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Buat Perintah Produksi
            </a>
        </div>

        <div class="px-6 py-3 bg-gray-50/50 border-b border-gray-100 flex flex-col sm:flex-row items-center gap-4 relative z-20">
            <div class="flex items-center gap-3 w-full sm:w-auto shrink-0 relative">
                <div class="relative w-1/2 sm:w-auto">
                    <button type="button" data-toggle-filter-menu="filterDropdown" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-white border {{ request('status') ? 'border-[#0F034D] text-[#0F034D] bg-blue-50/50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        Filter
                        @if(request('status'))
                            <span class="flex h-2 w-2 relative"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#0F034D] opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-[#0F034D]"></span></span>
                        @endif
                    </button>

                    <div id="filterDropdown" class="absolute left-0 mt-2 w-full sm:w-56 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden py-2">
                        <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">Status Produksi</div>
                        <div class="px-2 space-y-0.5">
                            <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ !request('status') ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Semua Status</a>
                            @foreach($statusLabels as $value => $label)
                                <a href="{{ request()->fullUrlWithQuery(['status' => $value]) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('status') == $value ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">{{ $label }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="relative w-1/2 sm:w-auto">
                    <button type="button" data-toggle-filter-menu="sortDropdown" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-white border {{ request('sort') ? 'border-[#0F034D] text-[#0F034D] bg-blue-50/50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 16 4 4 4-4"/><path d="M7 20V4"/><path d="m21 8-4-4-4 4"/><path d="M17 4v16"/></svg>
                        Urutkan
                    </button>
                    <div id="sortDropdown" class="absolute left-0 mt-2 w-full sm:w-56 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden py-2">
                        <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">Waktu Dibuat</div>
                        <div class="px-2 space-y-0.5">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'terbaru']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ !request('sort') || request('sort') == 'terbaru' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Terbaru</a>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'terlama']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('sort') == 'terlama' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Terlama</a>
                        </div>
                    </div>
                </div>
            </div>

            @if(request()->hasAny(['search', 'status', 'sort']))
                <a href="{{ route('admin.perintah-produksi.index') }}" title="Hapus Semua Filter & Pencarian" class="hidden sm:flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 rounded-xl transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </a>
            @endif

            <form method="GET" action="{{ route('admin.perintah-produksi.index') }}" class="relative flex-1 w-full">
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nomor WO untuk mencari..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors bg-white shadow-sm">
            </form>
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

                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <div class="rounded-xl bg-[#0F034D]/5 border border-[#0F034D]/10 p-3"><p class="text-[11px] text-[#0F034D]/60 mb-1">Jenis Produk</p><p class="text-sm font-bold text-[#0F034D]">{{ $wo->details->count() }}</p></div>
                        <div class="rounded-xl bg-amber-50 border border-amber-100 p-3"><p class="text-[11px] text-amber-700/70 mb-1">Total Roll</p><p class="text-sm font-bold text-amber-800">{{ number_format($totalRoll, 0, ',', '.') }}</p></div>
                        <div class="rounded-xl bg-green-50 border border-green-100 p-3"><p class="text-[11px] text-green-700/70 mb-1">Total Estimasi</p><p class="text-sm font-bold text-green-800">{{ number_format($totalEstimasi, 0, ',', '.') }} pcs</p></div>
                    </div>

                    <div class="space-y-2.5 mb-4">
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Mulai: {{ \Carbon\Carbon::parse($wo->tgl_mulai)->format('d M Y') }}</span>
                            @if($wo->tgl_selesai)<span class="text-gray-300">•</span><span>Selesai: {{ \Carbon\Carbon::parse($wo->tgl_selesai)->format('d M Y') }}</span>@endif
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span>Admin: {{ $wo->user->name ?? '-' }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Disetujui: {{ $wo->approver->name ?? '-' }}</span>
                        </div>
                    </div>

                    @if($wo->details->count() > 0)
                        <div class="mb-4 pt-3 border-t border-gray-50">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Produk dalam Perintah Produksi</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($wo->details->take(3) as $detail)
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg bg-[#0F034D]/5 text-[#0F034D] text-[11px] font-medium">
                                        {{ $detail->produk->nama_produk ?? '-' }} - {{ ucfirst($detail->produk->warna ?? '-') }}
                                    </span>
                                @endforeach
                                @if($wo->details->count() > 3)
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg bg-gray-100 text-gray-600 border border-gray-200 text-[11px] font-semibold">+{{ $wo->details->count() - 3 }}</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center gap-2 pt-3 border-t border-gray-50 mt-auto">
                        <a href="{{ route('admin.perintah-produksi.show', $wo) }}" class="flex-1 text-center py-2 text-xs font-semibold text-[#0F034D] bg-gray-50 rounded-lg hover:bg-[#0F034D]/5 transition-colors">Detail</a>
                        @if($wo->status_produksi === 'pending')
                            <a href="{{ route('admin.perintah-produksi.edit', $wo) }}" class="flex-1 text-center py-2 text-xs font-semibold text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">Edit</a>
                            <form action="{{ route('admin.perintah-produksi.destroy', $wo) }}" method="POST" class="flex-1">
                                @csrf @method('DELETE')
                                <button type="submit" data-confirm-action="Yakin hapus perintah produksi ini?" class="w-full py-2 text-xs font-semibold text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">Hapus</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $perintahProduksi->links('pagination::tailwind') }}</div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <p class="text-[#0F034D] font-semibold text-sm">Belum ada perintah produksi</p>
            <p class="text-gray-500 text-sm mt-1">Buat work order pertama untuk memulai proses produksi.</p>
            <a href="{{ route('admin.perintah-produksi.create') }}" class="inline-flex items-center justify-center gap-2 mt-4 px-4 py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-sm font-medium rounded-xl transition-colors">Buat Perintah Produksi</a>
        </div>
    @endif
    @vite([
        'resources/js/admin/confirm-action.js',
        'resources/js/admin/perintah-produksi/toggle-filter.js',
        'resources/js/admin/filter-dropdown.js',
    ])
</x-layouts.admin>

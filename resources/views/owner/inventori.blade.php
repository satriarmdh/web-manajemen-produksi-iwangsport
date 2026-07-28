<x-layouts.owner>
    <x-slot:breadcrumb>
        <li class="flex items-center text-[#0F034D] font-semibold gap-1.5">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Laporan Inventori
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Laporan Inventori Gudang
    </x-slot:header>

    <!-- Stat Cards Section: 2 Menipis (Kuning), 2 Habis (Merah) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Card 1: Bahan Baku Menipis -->
        <div onclick="filterBahanFromCard('menipis')" class="relative bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between gap-3 min-h-[92px] hover:border-amber-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group cursor-pointer select-none">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider truncate">Bahan Baku Menipis</p>
                <h3 class="text-2xl font-bold text-amber-600 mt-1 leading-none tabular-nums">{{ $stats['bahan_menipis_count'] }} <span class="text-xs font-normal text-gray-400">item &lt; 10</span></h3>
                <span class="text-[11px] text-gray-400 mt-1.5 flex items-center gap-1 font-medium group-hover:text-amber-600 transition-colors">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                    Klik untuk lihat item
                </span>
            </div>
            <div class="w-11 h-11 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-amber-100 group-hover:scale-105 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>

        <!-- Card 2: Produk Jadi Menipis -->
        <div onclick="filterProdukFromCard('menipis')" class="relative bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between gap-3 min-h-[92px] hover:border-amber-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group cursor-pointer select-none">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider truncate">Produk Jadi Menipis</p>
                <h3 class="text-2xl font-bold text-amber-600 mt-1 leading-none tabular-nums">{{ $stats['produk_menipis_count'] }} <span class="text-xs font-normal text-gray-400">item &lt; 100</span></h3>
                <span class="text-[11px] text-gray-400 mt-1.5 flex items-center gap-1 font-medium group-hover:text-amber-600 transition-colors">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                    Klik untuk lihat item
                </span>
            </div>
            <div class="w-11 h-11 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-amber-100 group-hover:scale-105 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
        </div>

        <!-- Card 3: Bahan Baku Habis -->
        <div onclick="filterBahanFromCard('habis')" class="relative bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between gap-3 min-h-[92px] hover:border-rose-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group cursor-pointer select-none">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider truncate">Bahan Baku Habis</p>
                <h3 class="text-2xl font-bold text-rose-600 mt-1 leading-none tabular-nums">{{ $stats['bahan_habis_count'] }} <span class="text-xs font-normal text-gray-400">item kosong</span></h3>
                <span class="text-[11px] text-gray-400 mt-1.5 flex items-center gap-1 font-medium group-hover:text-rose-600 transition-colors">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                    Klik untuk lihat item
                </span>
            </div>
            <div class="w-11 h-11 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-rose-100 group-hover:scale-105 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
            </div>
        </div>

        <!-- Card 4: Produk Jadi Habis -->
        <div onclick="filterProdukFromCard('habis')" class="relative bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between gap-3 min-h-[92px] hover:border-rose-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group cursor-pointer select-none">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider truncate">Produk Jadi Habis</p>
                <h3 class="text-2xl font-bold text-rose-600 mt-1 leading-none tabular-nums">{{ $stats['produk_habis_count'] }} <span class="text-xs font-normal text-gray-400">item kosong</span></h3>
                <span class="text-[11px] text-gray-400 mt-1.5 flex items-center gap-1 font-medium group-hover:text-rose-600 transition-colors">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                    Klik untuk lihat item
                </span>
            </div>
            <div class="w-11 h-11 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-rose-100 group-hover:scale-105 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
            </div>
        </div>
    </div>

    <!-- Alert Filter Aktif (Dinamis dari Client-side) -->
    <div id="alert-card-filter" class="hidden mb-6 px-4 py-3.5 rounded-xl text-sm flex items-center justify-between gap-3 shadow-sm transition-all duration-300">
        <div class="flex items-center gap-2.5">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span id="alert-card-filter-text"></span>
        </div>
        <button type="button" onclick="clearCardFilter()" class="text-xs font-bold px-3 py-1.5 rounded-lg transition-colors cursor-pointer shrink-0">Hapus Filter</button>
    </div>

    <!-- Tab Navigation (Segmented Control - Konsisten dengan Pergerakan Stok Admin) -->
    <div class="inline-flex flex-wrap gap-1.5 mb-6 bg-gray-100/80 p-1.5 rounded-2xl border border-gray-200/60 shadow-inner">
        <button onclick="switchTab('stok-bahan')" id="tab-stok-bahan" class="tab-btn flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition-all bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Stok Bahan Baku
        </button>
        <button onclick="switchTab('stok-produk')" id="tab-stok-produk" class="tab-btn flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition-all bg-white text-gray-500 hover:text-gray-800 hover:shadow-sm cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"/></svg>
            Stok Produk Jadi
        </button>
        <button onclick="switchTab('mutasi-gudang')" id="tab-mutasi-gudang" class="tab-btn flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition-all bg-white text-gray-500 hover:text-gray-800 hover:shadow-sm cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Log Aktivitas Stok
        </button>
    </div>

    <!-- Tab Contents Container -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-10">
        
        <!-- Tab Content: Log Aktivitas Stok -->
        <div id="content-mutasi-gudang" class="tab-content hidden relative z-30">
            <!-- Header Tab Log (Justify-between dengan button filter!) -->
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Log Aktivitas Stok</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Jejak perubahan stok yang direkam otomatis oleh sistem yang mencakup inisiasi data, penambahan, pengurangan, dan penyesuaian bahan baku maupun produk secara real-time.</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    @php $hasActiveMutasiFilter = request('jenis_item') || request('jenis_pergerakan'); @endphp
                    <!-- Custom Dropdown Filter Mutasi -->
                    <div class="relative">
                        <button type="button" onclick="toggleCustomDropdown('dropdown-filter-mutasi')" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer select-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></polygon></svg>
                            Filter
                            <span id="badge-active-filter-mutasi" class="{{ $hasActiveMutasiFilter ? 'flex' : 'hidden' }} h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#0F034D] opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-[#0F034D]"></span>
                            </span>
                        </button>
                        
                        <!-- Dropdown Menu Melayang (Nested list visual dengan URL redirect) -->
                        <div id="dropdown-filter-mutasi" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 py-2 hidden opacity-0 scale-95 pointer-events-none transition-all duration-150 z-50 origin-top-right">
                            <!-- Nested Tipe Item -->
                            <div class="relative group">
                                <button type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0F034D] transition-colors">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        Tipe Item
                                    </span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </button>
                                <div class="absolute top-0 right-full pr-1 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                                    <div class="w-48 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 p-2 space-y-0.5">
                                        <button type="button" onclick="applyMutasiFilter('jenis_item', 'semua')" class="mutasi-type-btn block w-full text-left px-3 py-2 text-xs rounded-lg transition-colors {{ !request('jenis_item') || request('jenis_item') == 'semua' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Semua Tipe</button>
                                        <button type="button" onclick="applyMutasiFilter('jenis_item', 'bahan_baku')" class="mutasi-type-btn block w-full text-left px-3 py-2 text-xs rounded-lg transition-colors {{ request('jenis_item') == 'bahan_baku' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Bahan Baku</button>
                                        <button type="button" onclick="applyMutasiFilter('jenis_item', 'produk')" class="mutasi-type-btn block w-full text-left px-3 py-2 text-xs rounded-lg transition-colors {{ request('jenis_item') == 'produk' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Produk Jadi</button>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-100 my-1.5"></div>

                            <!-- Nested Pergerakan -->
                            <div class="relative group">
                                <button type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0F034D] transition-colors">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                        Pergerakan
                                    </span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </button>
                                <div class="absolute top-0 right-full pr-1 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                                    <div class="w-48 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 p-2 space-y-0.5">
                                        <button type="button" onclick="applyMutasiFilter('jenis_pergerakan', 'semua')" class="mutasi-mov-btn block w-full text-left px-3 py-2 text-xs rounded-lg transition-colors {{ !request('jenis_pergerakan') || request('jenis_pergerakan') == 'semua' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Semua Pergerakan</button>
                                        <button type="button" onclick="applyMutasiFilter('jenis_pergerakan', 'inisiasi data')" class="mutasi-mov-btn block w-full text-left px-3 py-2 text-xs rounded-lg transition-colors {{ request('jenis_pergerakan') == 'inisiasi data' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Inisiasi Data</button>
                                        <button type="button" onclick="applyMutasiFilter('jenis_pergerakan', 'masuk')" class="mutasi-mov-btn block w-full text-left px-3 py-2 text-xs rounded-lg transition-colors {{ request('jenis_pergerakan') == 'masuk' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Masuk</button>
                                        <button type="button" onclick="applyMutasiFilter('jenis_pergerakan', 'keluar')" class="mutasi-mov-btn block w-full text-left px-3 py-2 text-xs rounded-lg transition-colors {{ request('jenis_pergerakan') == 'keluar' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Keluar</button>
                                        <button type="button" onclick="applyMutasiFilter('jenis_pergerakan', 'penyesuaian')" class="mutasi-mov-btn block w-full text-left px-3 py-2 text-xs rounded-lg transition-colors {{ request('jenis_pergerakan') == 'penyesuaian' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Penyesuaian</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Reset Filter Mutasi -->
                    <button type="button" id="btn-reset-mutasi" onclick="resetMutasiFilters()" class="{{ $hasActiveMutasiFilter ? 'flex' : 'hidden' }} items-center justify-center w-10 h-10 text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-colors cursor-pointer shrink-0 shadow-sm" title="Reset Filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-500 whitespace-nowrap">
                    <thead class="text-xs text-gray-400 uppercase bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Waktu & PIC</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Nama Item</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold text-center">Tipe</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold text-center">Pergerakan</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold text-right">Mutasi</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold text-right">Stok Akhir</th>
                        </tr>
                    </thead>
                    <tbody id="table-mutasi-body" class="divide-y divide-gray-100">
                        @forelse($mutasiStok as $mutasi)
                            @php
                                $mutasiName = ($mutasi->jenis_item === 'bahan_baku') ? ($mutasi->item->nama_bahan ?? '') : ($mutasi->item->nama_produk ?? '');
                                $mutasiKode = ($mutasi->jenis_item === 'bahan_baku') ? ($mutasi->item->kode_bahan ?? '') : ($mutasi->item->kode_produk ?? '');
                            @endphp
                            <tr class="mutasi-row hover:bg-gray-50/50 transition-colors" data-nama="{{ strtolower($mutasiName) }}" data-kode="{{ strtolower($mutasiKode) }}" data-jenis-item="{{ $mutasi->jenis_item }}" data-pergerakan="{{ $mutasi->jenis_pergerakan }}">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900 text-sm" title="{{ $mutasi->created_at->diffForHumans() }}">{{ $mutasi->created_at->translatedFormat('d M Y, H:i') }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">PIC: {{ $mutasi->user->name ?? 'Sistem' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 text-sm">{{ $mutasiName ?: '-' }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">{{ $mutasiKode ?: '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($mutasi->jenis_item === 'bahan_baku')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100">Bahan</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Produk</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $pergerakanClass = match($mutasi->jenis_pergerakan) {
                                            'masuk' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'keluar' => 'bg-rose-50 text-rose-600 border-rose-100',
                                            'penyesuaian' => 'bg-amber-50 text-amber-600 border-amber-100',
                                            default => 'bg-gray-50 text-gray-600 border-gray-100'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $pergerakanClass }} capitalize">
                                        {{ $mutasi->jenis_pergerakan }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-sm tabular-nums">
                                    @php
                                        $selisih = $mutasi->stok_sesudah - $mutasi->stok_sebelum;
                                    @endphp
                                    @if($selisih > 0)
                                        <span class="text-emerald-600">+{{ number_format($selisih, 0, ',', '.') }}</span>
                                    @elseif($selisih < 0)
                                        <span class="text-rose-600">{{ number_format($selisih, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-gray-400">0</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-gray-900 text-sm tabular-nums">
                                    {{ number_format($mutasi->stok_sesudah, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center">
                                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-500">Belum ada aktivitas stok</p>
                                            <p class="text-xs text-gray-400 mt-0.5">Perubahan stok akan otomatis tercatat di sini.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($mutasiStok->hasPages())
                <div class="p-4 border-t border-gray-100 rounded-b-xl bg-white relative z-10">
                    <x-pagination.custom-global-pagination :paginator="$mutasiStok->appends(request()->query())" />
                </div>
            @endif
        </div>

        <!-- Tab Content 2: Katalog Stok Bahan Baku (Default aktif) -->
        <div id="content-stok-bahan" class="tab-content block relative z-30">
            <!-- Header Tab 2 (Jangan dihilangkan!) -->
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Katalog Stok Bahan Baku</h3>
                <p class="text-xs text-gray-500 mt-0.5">Daftar sisa kuantitas stok bahan baku kain dan aksesoris di gudang secara real-time.</p>
            </div>

            <!-- Filter & Search Bar Bahan Baku (Konsisten dengan Admin) -->
            <div class="px-6 py-3 bg-gray-50/50 border-b border-gray-100 flex flex-row items-center gap-3 relative z-40 w-full">
                <!-- Custom Dropdown Filter (Nested design identical to admin/bahan-baku) -->
                <div class="shrink-0 relative">
                    <button type="button" onclick="toggleCustomDropdown('dropdown-filter-bahan')" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer select-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></polygon></svg>
                        Filter
                        <span id="badge-active-filter-bahan" class="hidden flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#0F034D] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#0F034D]"></span>
                        </span>
                    </button>
                    
                    <!-- Dropdown Menu Melayang (Nested list visual) -->
                    <div id="dropdown-filter-bahan" class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 py-2 hidden opacity-0 scale-95 pointer-events-none transition-all duration-150 z-50 origin-top-left">
                        
                        <!-- Nested Kategori -->
                        <div class="relative group">
                            <button type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0F034D] transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    Kategori Bahan
                                </span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            
                            <!-- Submenu Flyout (Tampil ke kanan saat hover) -->
                            <div class="absolute top-0 left-full pl-1 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                                <div class="w-48 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 p-2 space-y-0.5">
                                    <button type="button" onclick="selectBahanCategory('semua')" class="bahan-cat-btn block w-full text-left px-3 py-2 text-xs font-bold rounded-lg transition-colors bg-[#0F034D]/5 text-[#0F034D]">Semua Kategori</button>
                                    <button type="button" onclick="selectBahanCategory('kain')" class="bahan-cat-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900">Kain</button>
                                    <button type="button" onclick="selectBahanCategory('benang')" class="bahan-cat-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900">Benang</button>
                                    <button type="button" onclick="selectBahanCategory('kancing')" class="bahan-cat-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900">Kancing</button>
                                    <button type="button" onclick="selectBahanCategory('resleting')" class="bahan-cat-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900">Resleting</button>
                                    <button type="button" onclick="selectBahanCategory('aksesoris')" class="bahan-cat-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900">Aksesoris</button>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 my-1.5"></div>

                        <!-- Nested Status Stok -->
                        <div class="relative group">
                            <button type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0F034D] transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    Status Stok
                                </span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            
                            <!-- Submenu Flyout (Tampil ke kanan saat hover) -->
                            <div class="absolute top-0 left-full pl-1 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                                <div class="w-44 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 p-2 space-y-0.5">
                                    <button type="button" onclick="selectBahanStatus('semua')" class="bahan-status-btn block w-full text-left px-3 py-2 text-xs font-bold rounded-lg transition-colors bg-[#0F034D]/5 text-[#0F034D]">Semua Status</button>
                                    <button type="button" onclick="selectBahanStatus('aman')" class="bahan-status-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900">Aman</button>
                                    <button type="button" onclick="selectBahanStatus('menipis')" class="bahan-status-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900">Menipis</button>
                                    <button type="button" onclick="selectBahanStatus('habis')" class="bahan-status-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900">Habis</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Reset Filter Bahan Baku (Hanya muncul jika filter aktif) -->
                <button type="button" id="btn-reset-bahan" onclick="resetBahanFilters()" class="hidden items-center justify-center w-10 h-10 text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-colors cursor-pointer shrink-0 shadow-sm" title="Reset Pencarian & Filter">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <!-- Input Pencarian (Jadikan Full Width / flex-1) -->
                <div class="relative flex-1">
                    <input type="text" id="search-bahan" onkeyup="filterBahanBaku()" placeholder="Cari nama atau kode bahan baku..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D] focus:border-[#0F034D] outline-none bg-white placeholder-gray-400 text-gray-700 shadow-sm transition-colors">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="overflow-x-auto rounded-b-2xl">
                <table class="w-full text-left text-sm text-gray-500 whitespace-nowrap">
                    <thead class="text-xs text-gray-400 uppercase bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Kode & Nama Bahan</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Kategori</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold text-left">Warna</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold text-right">Sisa Stok</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="table-bahan-body" class="divide-y divide-gray-100">
                        @forelse($bahanBaku as $bahan)
                            @php
                                $statusBahan = 'aman';
                                if ($bahan->stok == 0) {
                                    $statusBahan = 'habis';
                                } elseif ($bahan->stok < 10) {
                                    $statusBahan = 'menipis';
                                }
                            @endphp
                            <tr class="bahan-row hover:bg-gray-50/50 transition-colors" data-nama="{{ strtolower($bahan->nama_bahan) }}" data-kode="{{ strtolower($bahan->kode_bahan) }}" data-kategori="{{ strtolower($bahan->kategori) }}" data-status="{{ $statusBahan }}" data-stok="{{ $bahan->stok }}">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 text-sm">{{ $bahan->nama_bahan }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">{{ $bahan->kode_bahan }}</div>
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-gray-700 capitalize">
                                    {{ $bahan->kategori }}
                                </td>
                                <td class="px-6 py-4 text-left">
                                    @php
                                        $warnaBahan = strtolower($bahan->warna ?? '-');
                                        $warnaDotMap = [
                                            'hitam' => '#111827',
                                            'navy' => '#061952',
                                            'abu-abu' => '#9CA3AF',
                                            'abu' => '#9CA3AF',
                                            'putih' => '#FFFFFF',
                                        ];
                                        $warnaDot = $warnaDotMap[$warnaBahan] ?? '#CBD5E1';
                                        $needsStroke = in_array($warnaBahan, ['abu-abu', 'abu', 'putih'], true);
                                    @endphp
                                    <span class="inline-flex items-center gap-2 text-xs font-medium text-gray-700 capitalize">
                                        <span class="inline-block w-2.5 h-2.5 rounded-full shrink-0 {{ $needsStroke ? 'ring-1 ring-gray-300' : '' }}" style="background-color: {{ $warnaDot }}"></span>
                                        {{ $bahan->warna }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-gray-900 text-sm tabular-nums">
                                    {{ number_format($bahan->stok, 0, ',', '.') }} <span class="font-normal text-gray-400 text-[10px] capitalize">{{ $bahan->satuan }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($bahan->stok == 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600 border border-red-100">Habis</span>
                                    @elseif($bahan->stok < 10)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-100">Menipis</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Aman</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center">
                                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-500">Tidak ada data bahan baku</p>
                                            <p class="text-xs text-gray-400 mt-0.5">Coba ubah kata kunci pencarian atau filter.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab Content 3: Katalog Stok Produk Jadi -->
        <div id="content-stok-produk" class="tab-content hidden relative z-30">
            <!-- Header Tab 3 (Jangan dihilangkan!) -->
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Katalog Stok Produk Jadi</h3>
                <p class="text-xs text-gray-500 mt-0.5">Daftar sisa kuantitas stok produk celana siap jual di gudang secara real-time.</p>
            </div>

            <!-- Filter & Search Bar Produk (Konsisten dengan Admin) -->
            <div class="px-6 py-3 bg-gray-50/50 border-b border-gray-100 flex flex-row items-center gap-3 relative z-40 w-full">
                <!-- Custom Dropdown Filter (Nested design identical to admin/produk) -->
                <div class="shrink-0 relative">
                    <button type="button" onclick="toggleCustomDropdown('dropdown-filter-produk')" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer select-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></polygon></svg>
                        Filter
                        <span id="badge-active-filter-produk" class="hidden flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#0F034D] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#0F034D]"></span>
                        </span>
                    </button>
                    
                    <!-- Dropdown Menu Melayang (Nested list visual) -->
                    <div id="dropdown-filter-produk" class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 py-2 hidden opacity-0 scale-95 pointer-events-none transition-all duration-150 z-50 origin-top-left">
                        
                        <!-- Nested Ukuran -->
                        <div class="relative group">
                            <button type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0F034D] transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    Ukuran Produk
                                </span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            
                            <!-- Submenu Flyout (Tampil ke kanan saat hover) -->
                            <div class="absolute top-0 left-full pl-1 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                                <div class="w-48 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 p-2 space-y-0.5">
                                    <button type="button" onclick="selectProdukSize('semua')" class="produk-size-btn block w-full text-left px-3 py-2 text-xs font-bold rounded-lg transition-colors bg-[#0F034D]/5 text-[#0F034D]">Semua Ukuran</button>
                                    <button type="button" onclick="selectProdukSize('normal')" class="produk-size-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900">Normal</button>
                                    <button type="button" onclick="selectProdukSize('jumbo')" class="produk-size-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900">Jumbo</button>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 my-1.5"></div>

                        <!-- Nested Status Stok -->
                        <div class="relative group">
                            <button type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0F034D] transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    Status Stok
                                </span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            
                            <!-- Submenu Flyout (Tampil ke kanan saat hover) -->
                            <div class="absolute top-0 left-full pl-1 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                                <div class="w-44 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 p-2 space-y-0.5">
                                    <button type="button" onclick="selectProdukStatus('semua')" class="produk-status-btn block w-full text-left px-3 py-2 text-xs font-bold rounded-lg transition-colors bg-[#0F034D]/5 text-[#0F034D]">Semua Status</button>
                                    <button type="button" onclick="selectProdukStatus('aman')" class="produk-status-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900">Aman</button>
                                    <button type="button" onclick="selectProdukStatus('menipis')" class="produk-status-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900">Menipis</button>
                                    <button type="button" onclick="selectProdukStatus('habis')" class="produk-status-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900">Habis</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Reset Filter Produk Jadi (Hanya muncul jika filter aktif) -->
                <button type="button" id="btn-reset-produk" onclick="resetProdukFilters()" class="hidden items-center justify-center w-10 h-10 text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-colors cursor-pointer shrink-0 shadow-sm" title="Reset Pencarian & Filter">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <!-- Input Pencarian (Jadikan Full Width / flex-1) -->
                <div class="relative flex-1">
                    <input type="text" id="search-produk" onkeyup="filterProduk()" placeholder="Cari nama atau kode produk..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D] focus:border-[#0F034D] outline-none bg-white placeholder-gray-400 text-gray-700 shadow-sm transition-colors">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="overflow-x-auto rounded-b-2xl">
                <table class="w-full text-left text-sm text-gray-500 whitespace-nowrap">
                    <thead class="text-xs text-gray-400 uppercase bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Kode & Nama Produk</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold text-center">Ukuran</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold text-left">Warna</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold text-right">Sisa Stok</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="table-produk-body" class="divide-y divide-gray-100">
                        @forelse($produk as $item)
                            @php
                                $statusProduk = 'aman';
                                if ($item->stok == 0) {
                                    $statusProduk = 'habis';
                                } elseif ($item->stok < 100) {
                                    $statusProduk = 'menipis';
                                }
                            @endphp
                            <tr class="produk-row hover:bg-gray-50/50 transition-colors" data-nama="{{ strtolower($item->nama_produk) }}" data-kode="{{ strtolower($item->kode_produk) }}" data-ukuran="{{ strtolower($item->ukuran) }}" data-status="{{ $statusProduk }}" data-stok="{{ $item->stok }}">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 text-sm">{{ $item->nama_produk }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">{{ $item->kode_produk }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold capitalize border {{ $item->ukuran == 'jumbo' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-blue-50 text-blue-700 border-blue-200' }}">
                                        {{ $item->ukuran }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-left">
                                    @php
                                        $warnaProduk = strtolower($item->warna ?? '-');
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
                                    <span class="inline-flex items-center gap-2 text-xs font-medium text-gray-700 capitalize">
                                        <span class="inline-block w-2.5 h-2.5 rounded-full shrink-0 {{ $needsStroke ? 'ring-1 ring-gray-300' : '' }}" style="background-color: {{ $warnaDot }}"></span>
                                        {{ $item->warna }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-gray-900 text-sm tabular-nums">
                                    {{ number_format($item->stok, 0, ',', '.') }} <span class="font-normal text-gray-400 text-[10px]">pcs</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($item->stok == 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600 border border-red-100">Habis</span>
                                    @elseif($item->stok < 100)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-100">Menipis</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Aman</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center">
                                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-500">Tidak ada data produk jadi</p>
                                            <p class="text-xs text-gray-400 mt-0.5">Coba ubah kata kunci pencarian atau filter.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Script JS Tab Switcher & Filter -->
    <script>
        window.inventoriConfig = {
            initialBahanStatus: "{{ request('stok') === 'menipis' ? 'menipis' : 'semua' }}",
            initialProdukStatus: "{{ request('stok') === 'menipis' ? 'menipis' : 'semua' }}",
            requestStok: "{{ request('stok') }}"
        };
    </script>
    @vite([
        'resources/js/owner/inventori.js'
    ])
</x-layouts.owner>

<x-layouts.admin>
    <x-slot:breadcrumb>
        <li class="flex items-center">
            <span class="text-gray-400 select-none">Manajemen Data</span>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            Bahan Baku
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Bahan Baku
    </x-slot:header>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <!-- Header -->
        <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-t-xl">
            <div>
                <h3 class="text-lg font-bold text-[#0F034D]">Daftar Katalog Bahan Baku</h3>
                <p class="text-sm text-gray-500 mt-1">Kelola data katalog bahan baku yang digunakan untuk proses produksi.</p>
            </div>
            <button onclick="window.toggleModal('add-modal')" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-sm font-medium rounded-xl transition-all shadow-md shadow-[#0F034D]/20 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Tambah Bahan Baku
            </button>
        </div>

        <!-- Filter, Sorting & Search Bar-->
        <div class="px-6 py-3 bg-gray-50/50 border-b border-gray-100 flex flex-col sm:flex-row items-center gap-4 relative z-20">
            <!-- KIRI: Grup Tombol Aksi (Filter & Sort) -->
            <div class="flex items-center gap-3 w-full sm:w-auto shrink-0 relative">
                <!-- TOMBOL & MENU FILTER -->
                <div class="relative w-1/2 sm:w-auto">
                    <button type="button" onclick="toggleFilterMenu('filterDropdown')" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-white border {{ request()->hasAny(['kategori', 'stok']) ? 'border-[#0F034D] text-[#0F034D] bg-blue-50/50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        Filter
                        @if(request()->hasAny(['kategori', 'stok']))
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#0F034D] opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-[#0F034D]"></span>
                            </span>
                        @endif
                    </button>
                    <!-- Dropdown ditambat ke kiri (left-0 origin-top-left) -->
                    <div id="filterDropdown" class="absolute left-0 mt-2 w-full sm:w-56 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden py-2">
                        <!-- Nested Kategori -->
                        <div class="relative group">
                            <button type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0F034D] transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    Kategori Bahan
                                </span>
                                <!-- Panah Kanan -->
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            <!-- Submenu terbuka ke kanan (left-full pl-1) -->
                            <div class="absolute top-0 left-full pl-1 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                                <div class="w-48 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 p-2 space-y-0.5">
                                    <a href="{{ request()->fullUrlWithQuery(['kategori' => null]) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ !request('kategori') ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Semua Kategori</a>
                                    <a href="{{ request()->fullUrlWithQuery(['kategori' => 'kain']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('kategori') == 'kain' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Kain</a>
                                    <a href="{{ request()->fullUrlWithQuery(['kategori' => 'benang']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('kategori') == 'benang' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Benang</a>
                                    <a href="{{ request()->fullUrlWithQuery(['kategori' => 'kancing']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('kategori') == 'kancing' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Kancing</a>
                                    <a href="{{ request()->fullUrlWithQuery(['kategori' => 'resleting']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('kategori') == 'resleting' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Resleting</a>
                                    <a href="{{ request()->fullUrlWithQuery(['kategori' => 'aksesoris']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('kategori') == 'aksesoris' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Aksesoris</a>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-100"></div>

                        <!-- Nested Status Stok -->
                        <div class="relative group">
                            <button type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0F034D] transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    Status Stok
                                </span>
                                <!-- Panah Kanan -->
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            <!-- Submenu terbuka ke kanan (left-full pl-1) -->
                            <div class="absolute top-0 left-full pl-1 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                                <div class="w-44 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 p-2 space-y-0.5">
                                    <a href="{{ request()->fullUrlWithQuery(['stok' => null]) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ !request('stok') ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Semua Status</a>
                                    <a href="{{ request()->fullUrlWithQuery(['stok' => 'tersedia']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('stok') == 'tersedia' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Tersedia (> 0)</a>
                                    <a href="{{ request()->fullUrlWithQuery(['stok' => 'habis']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('stok') == 'habis' ? 'bg-red-50 text-red-600 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Stockout (Habis)</a>
                                </div>
                            </div>
                        </div>
                        @if(request()->hasAny(['kategori', 'stok']))
                            <div class="px-4 pt-2 mt-2 border-t border-gray-100">
                                <a href="{{ request()->fullUrlWithQuery(['kategori' => null, 'stok' => null]) }}" class="block w-full text-center px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors">Reset Filter</a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- TOMBOL & MENU SORTING -->
                <div class="relative w-1/2 sm:w-auto">
                    <button type="button" onclick="toggleFilterMenu('sortDropdown')" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-white border {{ request('sort') ? 'border-[#0F034D] text-[#0F034D] bg-blue-50/50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 16 4 4 4-4"/><path d="M7 20V4"/><path d="m21 8-4-4-4 4"/><path d="M17 4v16"/></svg>
                        Urutkan
                    </button>
                    <!-- Dropdown ditambat ke kiri (left-0 origin-top-left) -->
                    <div id="sortDropdown" class="absolute left-0 mt-2 w-full sm:w-56 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden py-2">
                        <!-- Waktu -->
                        <div class="relative group">
                            <button type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0F034D] transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Waktu Ditambahkan
                                </span>
                                <!-- Panah Kanan -->
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            <div class="absolute top-0 left-full pl-1 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                                <div class="w-48 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 p-2 space-y-0.5">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => null]) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ !request('sort') || request('sort') == 'terbaru' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Terbaru</a>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'terlama']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('sort') == 'terlama' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Terlama</a>
                                </div>
                            </div>
                        </div>
                        <!-- Abjad -->
                        <div class="relative group">
                            <button type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0F034D] transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
                                    Abjad Nama
                                </span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            <div class="absolute top-0 left-full pl-1 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                                <div class="w-48 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 p-2 space-y-0.5">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_asc']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('sort') == 'nama_asc' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">A - Z</a>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_desc']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('sort') == 'nama_desc' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Z - A</a>
                                </div>
                            </div>
                        </div>
                        <!-- Stok -->
                        <div class="relative group">
                            <button type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0F034D] transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                                    Jumlah Stok
                                </span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            <div class="absolute top-0 left-full pl-1 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                                <div class="w-48 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 p-2 space-y-0.5">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'stok_desc']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('sort') == 'stok_desc' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Paling Banyak</a>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'stok_asc']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('sort') == 'stok_asc' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Paling Sedikit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Hapus Filter/Pencarian -->
            @if(request()->hasAny(['search', 'kategori', 'stok', 'sort']))
                <a href="{{ route('admin.bahan-baku.index') }}" title="Hapus Semua Filter & Pencarian" class="hidden sm:flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 rounded-xl transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </a>
            @endif
            <!-- KANAN: Search Bar -->
            <form method="GET" action="{{ route('admin.bahan-baku.index') }}" class="relative flex-1">
                @if(request('kategori')) <input type="hidden" name="kategori" value="{{ request('kategori') }}"> @endif
                @if(request('stok')) <input type="hidden" name="stok" value="{{ request('stok') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama bahan baku untuk mencari..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors bg-white shadow-sm">
            </form>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto rounded-b-xl relative z-10">
            <table class="w-full text-left text-sm text-gray-500 whitespace-nowrap">
                <thead class="text-xs text-gray-400 uppercase bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Kode & Nama Bahan</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Warna</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Kategori</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Satuan</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Total Stok</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bahanBaku as $bahan)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $bahan->nama_bahan }}</div>
                                        <div class="text-xs font-medium text-gray-400 mt-0.5">{{ $bahan->kode_bahan }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-medium text-gray-600 capitalize">{{ $bahan->warna }}</span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border bg-gray-100 text-gray-700 border-gray-200">
                                    {{ ucfirst($bahan->kategori) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-medium text-gray-600 capitalize">{{ $bahan->satuan }}</span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                @if($bahan->stok == 0)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600 border border-red-100">
                                        <div class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></div>
                                        Stockout
                                    </span>
                                @else
                                    <span class="text-sm font-bold {{ $bahan->stok < 10 ? 'text-amber-600' : 'text-gray-900' }}">
                                        {{ number_format($bahan->stok, 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button"
                                            onclick="window.openEditModal(this)"
                                            data-id="{{ $bahan->id }}"
                                            data-kode="{{ $bahan->kode_bahan }}"
                                            data-nama="{{ $bahan->nama_bahan }}"
                                            data-warna="{{ $bahan->warna }}"
                                            data-kategori="{{ $bahan->kategori }}"
                                            data-satuan="{{ $bahan->satuan }}"
                                            data-stok="{{ $bahan->stok }}"
                                            class="p-2 text-gray-400 hover:text-[#0F034D] hover:bg-gray-100 rounded-lg transition-colors cursor-pointer" title="Edit Bahan Baku">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                    </button>

                                    <form action="{{ route('admin.bahan-baku.destroy', $bahan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan baku {{ $bahan->nama_bahan }}? Data terkait produksi mungkin akan terpengaruh.');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Hapus Bahan Baku">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    <p class="text-gray-500 font-medium">Data tidak ditemukan.</p>
                                    <p class="text-sm text-gray-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bahanBaku->hasPages())
            <div class="p-4 border-t border-gray-100 rounded-b-xl bg-white relative z-10">
                {{ $bahanBaku->links() }}
            </div>
        @endif
    </div>

    {{-- ========================================= --}}
    {{-- MODAL TAMBAH BAHAN BAKU --}}
    {{-- ========================================= --}}
    <div id="add-modal" class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300">
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm" onclick="toggleModal('add-modal')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300 flex flex-col max-h-[90vh]">

                <div class="p-6 border-b border-gray-100 flex items-center justify-between shrink-0">
                    <h3 class="text-lg font-bold text-[#0F034D]">Tambah Bahan Baku</h3>
                    <button onclick="toggleModal('add-modal')" class="text-gray-400 hover:text-red-500 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto">
                    <form action="{{ route('admin.bahan-baku.store') }}" method="POST" id="addForm">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Bahan</label>
                                <div class="relative">
                                    <input type="text" id="add_kode_bahan" disabled placeholder="Dibuat otomatis oleh sistem" class="w-full px-4 py-2.5 border border-gray-300 bg-gray-50 text-gray-500 rounded-xl text-sm italic cursor-not-allowed transition-colors duration-300">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Kode menyesuaikan kategori yang dipilih (Misal: KAIN-001).</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Bahan <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_bahan" required placeholder="Contoh: Kain Cotton Combed 30s Hitam" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Warna Dasar <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="hidden" name="warna" id="add_warna_value" required>
                                    <input type="text" id="add_warna_input" placeholder="Pilih Warna..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                    <div id="add_warna_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                        <div class="p-2">
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="hitam" data-text="Hitam"><span class="text-sm font-medium text-gray-700">Hitam</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="putih" data-text="Putih"><span class="text-sm font-medium text-gray-700">Putih</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="abu-abu" data-text="Abu-abu"><span class="text-sm font-medium text-gray-700">Abu-abu</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="navy" data-text="Navy / Biru Dongker"><span class="text-sm font-medium text-gray-700">Navy / Biru Dongker</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="merah" data-text="Merah"><span class="text-sm font-medium text-gray-700">Merah</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="maroon" data-text="Maroon"><span class="text-sm font-medium text-gray-700">Maroon</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="kuning" data-text="Kuning"><span class="text-sm font-medium text-gray-700">Kuning</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="hijau" data-text="Hijau"><span class="text-sm font-medium text-gray-700">Hijau</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="biru" data-text="Biru"><span class="text-sm font-medium text-gray-700">Biru</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="coklat" data-text="Coklat"><span class="text-sm font-medium text-gray-700">Coklat</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="lainnya" data-text="Lainnya (Bebas / Multi-warna)"><span class="text-sm font-medium text-gray-700">Lainnya (Bebas / Multi-warna)</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                        </div>
                                        <div id="add_warna_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="hidden" name="kategori" id="add_kategori_value" data-prefixes="{{ json_encode($nextNumbers) }}" required>
                                        <input type="text" id="add_kategori_input" placeholder="Pilih..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                        <div id="add_kategori_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                            <div class="p-2">
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="kain" data-text="Kain"><span class="text-sm font-medium text-gray-700">Kain</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="benang" data-text="Benang"><span class="text-sm font-medium text-gray-700">Benang</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="kancing" data-text="Kancing"><span class="text-sm font-medium text-gray-700">Kancing</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="resleting" data-text="Resleting"><span class="text-sm font-medium text-gray-700">Resleting</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="aksesoris" data-text="Aksesoris Lainnya"><span class="text-sm font-medium text-gray-700">Aksesoris Lainnya</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            </div>
                                            <div id="add_kategori_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="hidden" name="satuan" id="add_satuan_value" required>
                                        <input type="text" id="add_satuan_input" placeholder="Pilih..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                        <div id="add_satuan_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                            <div class="p-2">
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="roll" data-text="Roll"><span class="text-sm font-medium text-gray-700">Roll</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="kg" data-text="Kilogram (Kg)"><span class="text-sm font-medium text-gray-700">Kilogram (Kg)</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="pcs" data-text="Pieces (Pcs)"><span class="text-sm font-medium text-gray-700">Pieces (Pcs)</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="meter" data-text="Meter"><span class="text-sm font-medium text-gray-700">Meter</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="yard" data-text="Yard"><span class="text-sm font-medium text-gray-700">Yard</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            </div>
                                            <div id="add_satuan_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Stok Awal</label>
                                <input type="number" name="stok" value="0" min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                                <p class="text-xs text-gray-500 mt-1">Stok awal hanya dapat diatur saat pertama kali ditambahkan. Perubahan selanjutnya melalui menu Pemasukan/Pengeluaran Bahan.</p>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="p-6 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl flex items-center justify-end gap-3 shrink-0">
                    <button type="button" onclick="toggleModal('add-modal')" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-200 rounded-xl transition-colors cursor-pointer">Batal</button>
                    <button type="submit" form="addForm" class="px-5 py-2.5 text-sm font-medium text-white bg-[#0F034D] hover:bg-[#0a0235] shadow-md rounded-xl transition-all cursor-pointer">Simpan Data</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- MODAL EDIT BAHAN BAKU --}}
    {{-- ========================================= --}}
    <div id="edit-modal" class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300">
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm" onclick="toggleModal('edit-modal')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300 flex flex-col max-h-[90vh]">

                <div class="p-6 border-b border-gray-100 flex items-center justify-between shrink-0">
                    <h3 class="text-lg font-bold text-[#0F034D]">Edit Bahan Baku</h3>
                    <button onclick="toggleModal('edit-modal')" class="text-gray-400 hover:text-red-500 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto">
                    <form action="" method="POST" id="editForm">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Bahan</label>
                                <input type="text" id="edit_kode" readonly class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 text-gray-600 font-bold rounded-xl text-sm focus:outline-none cursor-not-allowed">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Bahan <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_bahan" id="edit_nama" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Warna Dasar <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="hidden" name="warna" id="edit_warna_value" required>
                                    <input type="text" id="edit_warna_input" placeholder="Pilih Warna..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                    <div id="edit_warna_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                        <div class="p-2">
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="hitam" data-text="Hitam"><span class="text-sm font-medium text-gray-700">Hitam</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="putih" data-text="Putih"><span class="text-sm font-medium text-gray-700">Putih</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="abu-abu" data-text="Abu-abu"><span class="text-sm font-medium text-gray-700">Abu-abu</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="navy" data-text="Navy / Biru Dongker"><span class="text-sm font-medium text-gray-700">Navy / Biru Dongker</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="merah" data-text="Merah"><span class="text-sm font-medium text-gray-700">Merah</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="maroon" data-text="Maroon"><span class="text-sm font-medium text-gray-700">Maroon</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="kuning" data-text="Kuning"><span class="text-sm font-medium text-gray-700">Kuning</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="hijau" data-text="Hijau"><span class="text-sm font-medium text-gray-700">Hijau</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="biru" data-text="Biru"><span class="text-sm font-medium text-gray-700">Biru</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="coklat" data-text="Coklat"><span class="text-sm font-medium text-gray-700">Coklat</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="lainnya" data-text="Lainnya (Bebas / Multi-warna)"><span class="text-sm font-medium text-gray-700">Lainnya (Bebas / Multi-warna)</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                        </div>
                                        <div id="edit_warna_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="hidden" name="kategori" id="edit_kategori_value" required>
                                        <input type="text" id="edit_kategori_input" placeholder="Pilih..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                        <div id="edit_kategori_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                            <div class="p-2">
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="kain" data-text="Kain"><span class="text-sm font-medium text-gray-700">Kain</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="benang" data-text="Benang"><span class="text-sm font-medium text-gray-700">Benang</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="kancing" data-text="Kancing"><span class="text-sm font-medium text-gray-700">Kancing</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="resleting" data-text="Resleting"><span class="text-sm font-medium text-gray-700">Resleting</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="aksesoris" data-text="Aksesoris Lainnya"><span class="text-sm font-medium text-gray-700">Aksesoris Lainnya</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            </div>
                                            <div id="edit_kategori_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="hidden" name="satuan" id="edit_satuan_value" required>
                                        <input type="text" id="edit_satuan_input" placeholder="Pilih..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                        <div id="edit_satuan_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                            <div class="p-2">
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="roll" data-text="Roll"><span class="text-sm font-medium text-gray-700">Roll</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="kg" data-text="Kilogram (Kg)"><span class="text-sm font-medium text-gray-700">Kilogram (Kg)</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="pcs" data-text="Pieces (Pcs)"><span class="text-sm font-medium text-gray-700">Pieces (Pcs)</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="meter" data-text="Meter"><span class="text-sm font-medium text-gray-700">Meter</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="yard" data-text="Yard"><span class="text-sm font-medium text-gray-700">Yard</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            </div>
                                            <div id="edit_satuan_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Stok Saat Ini</label>
                                <input type="text" id="edit_stok" readonly class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 text-gray-600 font-bold rounded-xl text-sm focus:outline-none cursor-not-allowed">
                                <p class="text-xs text-gray-500 mt-1">Stok tidak dapat diubah manual. Perubahan stok dilakukan melalui menu Pemasukan/Pengeluaran Bahan.</p>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="p-6 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl flex items-center justify-end gap-3 shrink-0">
                    <button type="button" onclick="toggleModal('edit-modal')" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-200 rounded-xl transition-colors cursor-pointer">Batal</button>
                    <button type="submit" form="editForm" class="px-5 py-2.5 text-sm font-medium text-white bg-[#0F034D] hover:bg-[#0a0235] shadow-md rounded-xl transition-all cursor-pointer">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    @vite([
        'resources/js/admin/custom-forms.js',
        'resources/js/admin/bahan-baku/toggle-modal.js',
        'resources/js/admin/bahan-baku/generate-kode.js'
    ])
</x-layouts.admin>

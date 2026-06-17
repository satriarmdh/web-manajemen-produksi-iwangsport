<x-layouts.admin>
    <x-slot:breadcrumb>
        <li class="flex items-center">
            <span class="text-gray-400 select-none">Manajemen Data</span>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            Produk
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Produk
    </x-slot:header>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <!-- Header -->
        <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-t-xl">
            <div>
                <h3 class="text-lg font-bold text-[#0F034D]">Daftar Katalog Produk</h3>
                <p class="text-sm text-gray-500 mt-1">Kelola data katalog produk celana yang siap dijual.</p>
            </div>
            <button onclick="window.togglePanel('add-modal')" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-sm font-medium rounded-xl transition-all shadow-md shadow-[#0F034D]/20 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Tambah Produk
            </button>
        </div>

        <!-- Filter, Sorting & Search Bar-->
        <div class="px-6 py-3 bg-gray-50/50 border-b border-gray-100 flex flex-col sm:flex-row items-center gap-4 relative z-20">
            <!-- KIRI: Grup Tombol Aksi (Filter & Sort) -->
            <div class="flex items-center gap-3 w-full sm:w-auto shrink-0 relative">
                <!-- TOMBOL & MENU FILTER -->
                <div class="relative w-1/2 sm:w-auto">
                    <button type="button" onclick="toggleFilterMenu('filterDropdown')" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-white border {{ request()->hasAny(['ukuran', 'stok']) ? 'border-[#0F034D] text-[#0F034D] bg-blue-50/50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        Filter
                        @if(request()->hasAny(['ukuran', 'stok']))
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#0F034D] opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-[#0F034D]"></span>
                            </span>
                        @endif
                    </button>
                    <!-- Dropdown -->
                    <div id="filterDropdown" class="absolute left-0 mt-2 w-full sm:w-56 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden py-2">
                        <!-- Nested Ukuran -->
                        <div class="relative group">
                            <button type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0F034D] transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                    Ukuran
                                </span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            <div class="absolute top-0 left-full pl-1 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                                <div class="w-48 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 p-2 space-y-0.5">
                                    <a href="{{ request()->fullUrlWithQuery(['ukuran' => null]) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ !request('ukuran') ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Semua Ukuran</a>
                                    <a href="{{ request()->fullUrlWithQuery(['ukuran' => 'normal']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('ukuran') == 'normal' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Normal</a>
                                    <a href="{{ request()->fullUrlWithQuery(['ukuran' => 'jumbo']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('ukuran') == 'jumbo' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Jumbo</a>
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
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            <div class="absolute top-0 left-full pl-1 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                                <div class="w-44 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 p-2 space-y-0.5">
                                    <a href="{{ request()->fullUrlWithQuery(['stok' => null]) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ !request('stok') ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Semua Status</a>
                                    <a href="{{ request()->fullUrlWithQuery(['stok' => 'tersedia']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('stok') == 'tersedia' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Tersedia (> 0)</a>
                                    <a href="{{ request()->fullUrlWithQuery(['stok' => 'habis']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('stok') == 'habis' ? 'bg-red-50 text-red-600 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Stockout (Habis)</a>
                                </div>
                            </div>
                        </div>
                        @if(request()->hasAny(['ukuran', 'stok']))
                            <div class="px-4 pt-2 mt-2 border-t border-gray-100">
                                <a href="{{ request()->fullUrlWithQuery(['ukuran' => null, 'stok' => null]) }}" class="block w-full text-center px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors">Reset Filter</a>
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
                    <div id="sortDropdown" class="absolute left-0 mt-2 w-full sm:w-56 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden py-2">
                        <!-- Waktu -->
                        <div class="relative group">
                            <button type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0F034D] transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Waktu Ditambahkan
                                </span>
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
            @if(request()->hasAny(['search', 'ukuran', 'stok', 'sort']))
                <a href="{{ route('admin.produk.index') }}" title="Hapus Semua Filter & Pencarian" class="hidden sm:flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 rounded-xl transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </a>
            @endif
            <!-- KANAN: Search Bar -->
            <form method="GET" action="{{ route('admin.produk.index') }}" class="relative flex-1">
                @if(request('ukuran')) <input type="hidden" name="ukuran" value="{{ request('ukuran') }}"> @endif
                @if(request('stok')) <input type="hidden" name="stok" value="{{ request('stok') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama produk untuk mencari..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors bg-white shadow-sm">
            </form>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto rounded-b-xl relative z-10">
            <table class="w-full text-left text-sm text-gray-500 whitespace-nowrap">
                <thead class="text-xs text-gray-400 uppercase bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Kode & Nama Produk</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Ukuran</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Warna</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Harga Satuan</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Satuan</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Total Stok</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Aktif</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($produk as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $item->nama_produk }}</div>
                                        <div class="text-xs font-medium text-gray-400 mt-0.5">{{ $item->kode_produk }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $item->ukuran == 'jumbo' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-blue-50 text-blue-700 border-blue-200' }}">
                                    {{ ucfirst($item->ukuran) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-medium text-gray-600 capitalize">{{ $item->warna }}</span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-gray-900">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-medium text-gray-600 capitalize">{{ $item->satuan }}</span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                @if($item->stok == 0)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600 border border-red-100">
                                        <div class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></div>
                                        Stockout
                                    </span>
                                @else
                                    <span class="text-sm font-bold {{ $item->stok < 10 ? 'text-amber-600' : 'text-gray-900' }}">
                                        {{ number_format($item->stok, 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($item->is_aktif)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600 border border-green-100">
                                        <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600 border border-red-100">
                                        <div class="w-1.5 h-1.5 rounded-full bg-red-500"></div>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button"
                                            onclick="window.openEditModal(this)"
                                            data-id="{{ $item->id }}"
                                            data-kode="{{ $item->kode_produk }}"
                                            data-nama="{{ $item->nama_produk }}"
                                            data-ukuran="{{ $item->ukuran }}"
                                            data-warna="{{ $item->warna }}"
                                            data-harga="{{ $item->harga_satuan }}"
                                            data-satuan="{{ $item->satuan }}"
                                            data-stok="{{ $item->stok }}"
                                            data-is-aktif="{{ $item->is_aktif ? '1' : '0' }}"
                                            class="p-2 text-gray-400 hover:text-[#0F034D] hover:bg-gray-100 rounded-lg transition-colors cursor-pointer" title="Edit Produk">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                    </button>

                                    <form action="{{ route('admin.produk.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ $item->nama_produk }}? Data terkait mungkin akan terpengaruh.');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Hapus Produk">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
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

        @if($produk->hasPages())
            <div class="p-4 border-t border-gray-100 rounded-b-xl bg-white relative z-10">
                {{ $produk->links() }}
            </div>
        @endif
    </div>

    {{-- ========================================= --}}
    {{-- MODAL TAMBAH PRODUK --}}
    {{-- ========================================= --}}
    <div id="add-modal" class="slide-panel">
        <div class="slide-panel-backdrop" data-panel-close></div>
        <div class="slide-panel-body">
            <div class="slide-panel-header">
                <div class="slide-panel-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <h2 class="slide-panel-header-title">Tambah Produk</h2>
                <button class="slide-panel-close" data-panel-close><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form action="{{ route('admin.produk.store') }}" method="POST" id="addForm" class="slide-panel-content">
                @csrf
                        <div class="space-y-4">
                            <!-- Hidden input untuk kode produk (digenerate otomatis oleh backend) -->
                            <input type="hidden" id="add_kode_produk" data-next-number="{{ $nextNumber }}">

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_produk" required placeholder="Contoh: Celana Basket Polos" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ukuran <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="hidden" name="ukuran" id="add_ukuran_value" required>
                                        <input type="text" id="add_ukuran_input" placeholder="Pilih Ukuran..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                        <div id="add_ukuran_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                            <div class="p-2">
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="normal" data-text="Normal">
                                                    <span class="text-sm font-medium text-gray-700">Normal</span>
                                                    <svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="jumbo" data-text="Jumbo">
                                                    <span class="text-sm font-medium text-gray-700">Jumbo</span>
                                                    <svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                            </div>
                                            <div id="add_ukuran_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Warna <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="hidden" name="warna" id="add_warna_value" required>
                                        <input type="text" id="add_warna_input" placeholder="Pilih Warna..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                        <div id="add_warna_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                            <div class="p-2">
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="hitam" data-text="Hitam"><span class="text-sm font-medium text-gray-700">Hitam</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="abu" data-text="Abu"><span class="text-sm font-medium text-gray-700">Abu</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="navy" data-text="Navy"><span class="text-sm font-medium text-gray-700">Navy</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="biru" data-text="Biru"><span class="text-sm font-medium text-gray-700">Biru</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="misty" data-text="Misty"><span class="text-sm font-medium text-gray-700">Misty</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                            </div>
                                            <div id="add_warna_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Harga Satuan <span class="text-red-500">*</span></label>
                                    <input type="number" name="harga_satuan" required min="0" placeholder="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                                    <input type="text" name="satuan" value="pcs" readonly class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 text-gray-600 font-semibold rounded-xl text-sm cursor-not-allowed">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Stok Awal</label>
                                <input type="number" name="stok" value="0" min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                            </div>
                        </div>
                    </form>
            </form>
            <div class="slide-panel-footer">
                <button type="button" class="btn-panel-cancel" data-panel-close>Batal</button>
                <button type="submit" form="addForm" class="btn-panel-submit">Simpan Data</button>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- MODAL EDIT PRODUK --}}
    {{-- ========================================= --}}
    <div id="edit-modal" class="slide-panel">
        <div class="slide-panel-backdrop" data-panel-close></div>
        <div class="slide-panel-body">
            <div class="slide-panel-header">
                <div class="slide-panel-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <h2 class="slide-panel-header-title">Edit Produk</h2>
                <button class="slide-panel-close" data-panel-close><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form action="" method="POST" id="editForm" class="slide-panel-content">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <input type="hidden" id="edit_kode">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_produk" id="edit_nama" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Ukuran <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="hidden" name="ukuran" id="edit_ukuran_value" required>
                                <input type="text" id="edit_ukuran_input" placeholder="Pilih Ukuran..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                                <div id="edit_ukuran_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                    <div class="p-2">
                                        <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="normal" data-text="Normal"><span class="text-sm font-medium text-gray-700">Normal</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                        <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="jumbo" data-text="Jumbo"><span class="text-sm font-medium text-gray-700">Jumbo</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                    </div>
                                    <div id="edit_ukuran_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Warna <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="hidden" name="warna" id="edit_warna_value" required>
                                <input type="text" id="edit_warna_input" placeholder="Pilih Warna..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                                <div id="edit_warna_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                    <div class="p-2">
                                        <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="hitam" data-text="Hitam"><span class="text-sm font-medium text-gray-700">Hitam</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                        <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="abu" data-text="Abu"><span class="text-sm font-medium text-gray-700">Abu</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                        <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="navy" data-text="Navy"><span class="text-sm font-medium text-gray-700">Navy</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                        <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="biru" data-text="Biru"><span class="text-sm font-medium text-gray-700">Biru</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                        <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="misty" data-text="Misty"><span class="text-sm font-medium text-gray-700">Misty</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                    </div>
                                    <div id="edit_warna_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Harga Satuan <span class="text-red-500">*</span></label>
                            <input type="number" name="harga_satuan" id="edit_harga" required min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                            <input type="text" name="satuan" id="edit_satuan" readonly class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 text-gray-600 font-semibold rounded-xl text-sm cursor-not-allowed">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Stok <span class="text-red-500">*</span></label>
                        <input type="number" name="stok" id="edit_stok" min="0" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                        <p class="text-xs text-gray-500 mt-1">Perubahan stok akan otomatis tercatat di riwayat pergerakan stok.</p>
                    </div>

                    <!-- Checkbox is_aktif -->
                    <div>
                        <input type="hidden" name="is_aktif" value="0">
                        <input type="checkbox" name="is_aktif" id="edit_is_aktif" value="1" class="hidden" onchange="updateCheckbox(this, 'edit_cb')">
                        <div id="edit_cb_wrapper" onclick="document.getElementById('edit_is_aktif').click()" class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-all">
                            <div id="edit_cb_box" class="relative flex shrink-0 items-center justify-center w-5 h-5 rounded border-2 border-gray-300 transition-all">
                                <svg id="edit_cb_icon" class="w-3 h-3 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <span id="edit_cb_text" class="text-sm font-semibold text-gray-700">Aktif</span>
                                <p class="text-xs text-gray-500">Produk ini tersedia untuk proses estimasi produksi.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="slide-panel-footer">
                <button type="button" class="btn-panel-cancel" data-panel-close>Batal</button>
                <button type="submit" form="editForm" class="btn-panel-submit">Simpan Perubahan</button>
            </div>
        </div>
    </div>

    @vite([
        'resources/css/global-modal.css',
        'resources/js/admin/custom-forms.js',
        'resources/js/admin/produk/toggle-modal.js',
        'resources/js/admin/produk/generate-kode.js'
    ])
</x-layouts.admin>

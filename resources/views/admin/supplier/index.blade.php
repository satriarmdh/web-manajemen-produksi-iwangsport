<x-layouts.admin>
    <x-slot:breadcrumb>
        <li class="flex items-center">
            <span class="text-gray-400 select-none">Manajemen Data</span>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            Supplier
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Supplier
    </x-slot:header>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <!-- Header -->
        <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-t-xl">
            <div>
                <h3 class="text-lg font-bold text-[#0F034D]">Daftar Supplier</h3>
                <p class="text-sm text-gray-500 mt-1">Kelola data supplier bahan baku untuk memastikan rantai pasokan selalu aman.</p>
            </div>
            <button onclick="window.toggleModal('add-modal')" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-sm font-medium rounded-xl transition-all shadow-md shadow-[#0F034D]/20 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Tambah Supplier
            </button>
        </div>

        <!-- Filter, Sorting & Search Bar-->
        <div class="px-6 py-3 bg-gray-50/50 border-b border-gray-100 flex flex-col sm:flex-row items-center gap-4 relative z-20">
            <!-- KIRI: Grup Tombol Aksi (Filter & Sort) -->
            <div class="flex items-center gap-3 w-full sm:w-auto shrink-0 relative">
                <!-- TOMBOL & MENU FILTER -->
                <div class="relative w-1/2 sm:w-auto">
                    <button type="button" onclick="toggleFilterMenu('filterDropdown')" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-white border {{ request()->hasAny(['kategori', 'status']) ? 'border-[#0F034D] text-[#0F034D] bg-blue-50/50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        Filter
                        @if(request()->hasAny(['kategori', 'status']))
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#0F034D] opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-[#0F034D]"></span>
                            </span>
                        @endif
                    </button>
                    <!-- Dropdown -->
                    <div id="filterDropdown" class="absolute left-0 mt-2 w-full sm:w-56 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden py-2">
                        <!-- Nested Kategori -->
                        <div class="relative group">
                            <button type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0F034D] transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    Kategori Bahan
                                </span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
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

                        <!-- Nested Status -->
                        <div class="relative group">
                            <button type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0F034D] transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Status Supplier
                                </span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            <div class="absolute top-0 left-full pl-1 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                                <div class="w-44 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 p-2 space-y-0.5">
                                    <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ !request('status') ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Semua Status</a>
                                    <a href="{{ request()->fullUrlWithQuery(['status' => 'aktif']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('status') == 'aktif' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Aktif</a>
                                    <a href="{{ request()->fullUrlWithQuery(['status' => 'nonaktif']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('status') == 'nonaktif' ? 'bg-red-50 text-red-600 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Nonaktif</a>
                                </div>
                            </div>
                        </div>
                        @if(request()->hasAny(['kategori', 'status']))
                            <div class="px-4 pt-2 mt-2 border-t border-gray-100">
                                <a href="{{ request()->fullUrlWithQuery(['kategori' => null, 'status' => null]) }}" class="block w-full text-center px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors">Reset Filter</a>
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
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => null]) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ !request('sort') || request('sort') == 'newest' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Terbaru</a>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('sort') == 'oldest' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Terlama</a>
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
                    </div>
                </div>
            </div>

            <!-- Tombol Hapus Filter/Pencarian -->
            @if(request()->hasAny(['search', 'kategori', 'status', 'sort']))
                <a href="{{ route('admin.supplier.index') }}" title="Hapus Semua Filter & Pencarian" class="hidden sm:flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 rounded-xl transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </a>
            @endif
            <!-- KANAN: Search Bar -->
            <form method="GET" action="{{ route('admin.supplier.index') }}" class="relative flex-1">
                @if(request('kategori')) <input type="hidden" name="kategori" value="{{ request('kategori') }}"> @endif
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama supplier untuk mencari..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors bg-white shadow-sm">
            </form>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto rounded-b-xl relative z-10">
            <table class="w-full text-left text-sm text-gray-500 whitespace-nowrap">
                <thead class="text-xs text-gray-400 uppercase bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Kode & Nama Supplier</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Kategori</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Kontak</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Email</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Status</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $supplier->nama_supplier }}</div>
                                        <div class="text-xs font-medium text-gray-400 mt-0.5">{{ $supplier->kode_supplier }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-wrap gap-1 justify-center">
                                    @foreach($supplier->kategori as $kat)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border bg-gray-100 text-gray-700 border-gray-200">
                                            {{ ucfirst($kat) }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-medium text-gray-600">{{ $supplier->kontak }}</span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-medium text-gray-600">{{ $supplier->email }}</span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($supplier->status == 'aktif')
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
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Tombol View Detail -->
                                    <button type="button"
                                            onclick="window.openDetailModal(this)"
                                            data-id="{{ $supplier->id }}"
                                            data-kode="{{ $supplier->kode_supplier }}"
                                            data-nama="{{ $supplier->nama_supplier }}"
                                            data-kategori="{{ json_encode($supplier->kategori) }}"
                                            data-kontak="{{ $supplier->kontak }}"
                                            data-email="{{ $supplier->email }}"
                                            data-alamat="{{ $supplier->alamat }}"
                                            data-catatan="{{ $supplier->catatan }}"
                                            data-status="{{ $supplier->status }}"
                                            data-created="{{ $supplier->created_at->format('d M Y, H:i') }}"
                                            class="p-2 text-gray-400 hover:text-[#0F034D] hover:bg-blue-50 rounded-lg transition-colors cursor-pointer" title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>

                                    <!-- Dropdown Aksi (Edit & Hapus) -->
                                    <div class="relative">
                                        <button type="button" onclick="window.toggleActionDropdown(this)" class="p-2 text-gray-400 hover:text-[#0F034D] hover:bg-gray-100 rounded-lg transition-colors cursor-pointer" title="Aksi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                                        </button>
                                        <div class="action-dropdown w-40 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.12)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-right hidden py-1">
                                            <button type="button"
                                                    onclick="window.openEditModal(this); this.closest('.action-dropdown').classList.add('hidden');"
                                                    data-id="{{ $supplier->id }}"
                                                    data-kode="{{ $supplier->kode_supplier }}"
                                                    data-nama="{{ $supplier->nama_supplier }}"
                                                    data-kategori="{{ json_encode($supplier->kategori) }}"
                                                    data-kontak="{{ $supplier->kontak }}"
                                                    data-email="{{ $supplier->email }}"
                                                    data-alamat="{{ $supplier->alamat }}"
                                                    data-catatan="{{ $supplier->catatan }}"
                                                    data-status="{{ $supplier->status }}"
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-gray-600 hover:text-[#0F034D] hover:bg-gray-50 transition-colors cursor-pointer">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                                Edit Supplier
                                            </button>
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <form action="{{ route('admin.supplier.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier {{ $supplier->nama_supplier }}? Data terkait mungkin akan terpengaruh.');" class="w-full">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-red-500 hover:bg-red-50 transition-colors cursor-pointer">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                                    Hapus Supplier
                                                </button>
                                            </form>
                                        </div>
                                    </div>
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

        @if($suppliers->hasPages())
            <div class="p-4 border-t border-gray-100 rounded-b-xl bg-white relative z-10">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>

    {{-- ========================================= --}}
    {{-- MODAL TAMBAH SUPPLIER --}}
    {{-- ========================================= --}}
    <div id="add-modal" class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300">
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm" onclick="toggleModal('add-modal')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300 flex flex-col max-h-[90vh]">

                <div class="p-6 border-b border-gray-100 flex items-center justify-between shrink-0">
                    <h3 class="text-lg font-bold text-[#0F034D]">Tambah Supplier</h3>
                    <button onclick="toggleModal('add-modal')" class="text-gray-400 hover:text-red-500 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto">
                    <form action="{{ route('admin.supplier.store') }}" method="POST" id="addForm">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Supplier</label>
                                <div class="relative">
                                    <input type="text" id="add_kode_supplier" data-next-number="{{ $nextNumber }}" disabled placeholder="Dibuat otomatis oleh sistem" class="w-full px-4 py-2.5 border border-gray-300 bg-gray-50 text-gray-500 rounded-xl text-sm italic cursor-not-allowed transition-colors duration-300">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Kode akan dibuat otomatis (Format: SUP-001, SUP-002, dst).</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Supplier <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_supplier" required placeholder="Contoh: PT Tekstil Jaya Abadi" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori Bahan <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                        <input type="checkbox" name="kategori[]" value="kain" class="w-4 h-4 text-[#0F034D] rounded focus:ring-[#0F034D]">
                                        <span class="text-sm text-gray-700">Kain</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                        <input type="checkbox" name="kategori[]" value="benang" class="w-4 h-4 text-[#0F034D] rounded focus:ring-[#0F034D]">
                                        <span class="text-sm text-gray-700">Benang</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                        <input type="checkbox" name="kategori[]" value="kancing" class="w-4 h-4 text-[#0F034D] rounded focus:ring-[#0F034D]">
                                        <span class="text-sm text-gray-700">Kancing</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                        <input type="checkbox" name="kategori[]" value="resleting" class="w-4 h-4 text-[#0F034D] rounded focus:ring-[#0F034D]">
                                        <span class="text-sm text-gray-700">Resleting</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                        <input type="checkbox" name="kategori[]" value="aksesoris" class="w-4 h-4 text-[#0F034D] rounded focus:ring-[#0F034D]">
                                        <span class="text-sm text-gray-700">Aksesoris</span>
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Pilih satu atau lebih kategori bahan yang disuplai.</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kontak <span class="text-red-500">*</span></label>
                                    <input type="text" name="kontak" required placeholder="08xxxxxxxxxx" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" required placeholder="supplier@email.com" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat <span class="text-red-500">*</span></label>
                                <textarea name="alamat" required rows="3" placeholder="Alamat lengkap supplier" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan</label>
                                <textarea name="catatan" rows="2" placeholder="Catatan tambahan (opsional)" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm"></textarea>
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
    {{-- MODAL EDIT SUPPLIER --}}
    {{-- ========================================= --}}
    <div id="edit-modal" class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300">
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm" onclick="toggleModal('edit-modal')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300 flex flex-col max-h-[90vh]">

                <div class="p-6 border-b border-gray-100 flex items-center justify-between shrink-0">
                    <h3 class="text-lg font-bold text-[#0F034D]">Edit Supplier</h3>
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
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Supplier</label>
                                <input type="text" id="edit_kode" readonly class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 text-gray-600 font-bold rounded-xl text-sm focus:outline-none cursor-not-allowed">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Supplier <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_supplier" id="edit_nama" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori Bahan <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-2 gap-2" id="edit_kategori_container">
                                    <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                        <input type="checkbox" name="kategori[]" value="kain" class="edit_kategori w-4 h-4 text-[#0F034D] rounded focus:ring-[#0F034D]">
                                        <span class="text-sm text-gray-700">Kain</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                        <input type="checkbox" name="kategori[]" value="benang" class="edit_kategori w-4 h-4 text-[#0F034D] rounded focus:ring-[#0F034D]">
                                        <span class="text-sm text-gray-700">Benang</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                        <input type="checkbox" name="kategori[]" value="kancing" class="edit_kategori w-4 h-4 text-[#0F034D] rounded focus:ring-[#0F034D]">
                                        <span class="text-sm text-gray-700">Kancing</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                        <input type="checkbox" name="kategori[]" value="resleting" class="edit_kategori w-4 h-4 text-[#0F034D] rounded focus:ring-[#0F034D]">
                                        <span class="text-sm text-gray-700">Resleting</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                        <input type="checkbox" name="kategori[]" value="aksesoris" class="edit_kategori w-4 h-4 text-[#0F034D] rounded focus:ring-[#0F034D]">
                                        <span class="text-sm text-gray-700">Aksesoris</span>
                                    </label>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kontak <span class="text-red-500">*</span></label>
                                    <input type="text" name="kontak" id="edit_kontak" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" id="edit_email" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat <span class="text-red-500">*</span></label>
                                <textarea name="alamat" id="edit_alamat" required rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan</label>
                                <textarea name="catatan" id="edit_catatan" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                                <select name="status" id="edit_status" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm bg-white cursor-pointer">
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
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

    {{-- ========================================= --}}
    {{-- MODAL DETAIL SUPPLIER --}}
    {{-- ========================================= --}}
    <div id="detail-modal" class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300">
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm" onclick="toggleModal('detail-modal')"></div>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300 flex flex-col max-h-[90vh]">

                <div class="p-6 border-b border-gray-100 flex items-center justify-between shrink-0">
                    <h3 class="text-lg font-bold text-[#0F034D]">Detail Supplier</h3>
                    <button onclick="toggleModal('detail-modal')" class="text-gray-400 hover:text-red-500 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto">
                    <div class="space-y-5">
                        {{-- Kode Supplier --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Kode Supplier</label>
                            <p id="detail_kode" class="text-sm font-bold text-[#0F034D]">-</p>
                        </div>

                        {{-- Nama Supplier --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Nama Supplier</label>
                            <p id="detail_nama" class="text-sm font-medium text-gray-900">-</p>
                        </div>

                        {{-- Kategori Bahan --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Kategori Bahan</label>
                            <div id="detail_kategori" class="flex flex-wrap gap-1.5">
                                {{-- Populated by JS --}}
                            </div>
                        </div>

                        {{-- Kontak & Email --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Kontak</label>
                                <p id="detail_kontak" class="text-sm font-medium text-gray-900">-</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Email</label>
                                <p id="detail_email" class="text-sm font-medium text-gray-900 break-all">-</p>
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Alamat</label>
                            <p id="detail_alamat" class="text-sm text-gray-700 leading-relaxed whitespace-normal">-</p>
                        </div>

                        {{-- Catatan --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Catatan</label>
                            <p id="detail_catatan" class="text-sm text-gray-700 leading-relaxed whitespace-normal">-</p>
                        </div>

                        {{-- Status & Tanggal --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Status</label>
                                <div id="detail_status"></div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Ditambahkan</label>
                                <p id="detail_created" class="text-sm font-medium text-gray-900">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl flex items-center justify-end shrink-0">
                    <button type="button" onclick="toggleModal('detail-modal')" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-200 rounded-xl transition-colors cursor-pointer">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @vite([
        'resources/js/admin/supplier/toggle-modal.js',
        'resources/js/admin/supplier/generate-kode.js'
    ])
</x-layouts.admin>

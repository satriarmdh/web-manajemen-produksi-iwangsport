<x-layouts.admin>
    <x-slot:breadcrumb>
        <li class="flex items-center">
            <span class="text-gray-400 select-none">Manajemen Data</span>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            Pelanggan
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Pelanggan
    </x-slot:header>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <!-- Header -->
        <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-t-xl">
            <div>
                <h3 class="text-lg font-bold text-[#0F034D]">Daftar Pelanggan</h3>
                <p class="text-sm text-gray-500 mt-1">Kelola data pelanggan untuk memastikan hubungan bisnis berjalan lancar.</p>
            </div>
            <button onclick="window.togglePanel('add-modal')" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-sm font-medium rounded-xl transition-all shadow-md shadow-[#0F034D]/20 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Tambah Pelanggan
            </button>
        </div>

        <!-- Filter, Sorting & Search Bar -->
        <div class="px-6 py-3 bg-gray-50/50 border-b border-gray-100 flex flex-col sm:flex-row items-center gap-4 relative z-20">
            <!-- KIRI: Grup Tombol Aksi (Filter & Sort) -->
            <div class="flex items-center gap-3 w-full sm:w-auto shrink-0 relative">
                <!-- TOMBOL & MENU FILTER -->
                <div class="relative w-1/2 sm:w-auto">
                    <button type="button" onclick="toggleFilterMenu('filterDropdown')" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-white border {{ request('status') ? 'border-[#0F034D] text-[#0F034D] bg-blue-50/50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        Filter
                        @if(request('status'))
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#0F034D] opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-[#0F034D]"></span>
                            </span>
                        @endif
                    </button>
                    <!-- Dropdown -->
                    <div id="filterDropdown" class="absolute left-0 mt-2 w-full sm:w-56 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden py-2">
                        <!-- Status -->
                        <div class="relative group">
                            <button type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0F034D] transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Status Pelanggan
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
                        @if(request('status'))
                            <div class="px-4 pt-2 mt-2 border-t border-gray-100">
                                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="block w-full text-center px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors">Reset Filter</a>
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
                    </div>
                </div>
            </div>

            <!-- Tombol Hapus Filter/Pencarian -->
            @if(request()->hasAny(['search', 'status', 'sort']))
                <a href="{{ route('admin.pelanggan.index') }}" title="Hapus Semua Filter & Pencarian" class="hidden sm:flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 rounded-xl transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </a>
            @endif
            <!-- KANAN: Search Bar -->
            <form method="GET" action="{{ route('admin.pelanggan.index') }}" class="relative flex-1">
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama, email, atau no. telp pelanggan..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors bg-white shadow-sm">
            </form>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto rounded-b-xl relative z-10">
            <table class="w-full text-left text-sm text-gray-500 whitespace-nowrap">
                <thead class="text-xs text-gray-400 uppercase bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Kode & Nama Pelanggan</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">No. Telepon</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Email</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Alamat</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Status</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pelanggan as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $item->nama_pelanggan }}</div>
                                        <div class="text-xs font-medium text-gray-400 mt-0.5">{{ $item->kode_pelanggan }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-medium text-gray-600">{{ $item->no_telp }}</span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-medium text-gray-600">{{ $item->email }}</span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-medium text-gray-600 max-w-[200px] truncate block">{{ $item->alamat }}</span>
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
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Tombol View Detail -->
                                    <button type="button"
                                            onclick="window.openDetailModal(this)"
                                            data-id="{{ $item->id }}"
                                            data-kode="{{ $item->kode_pelanggan }}"
                                            data-nama="{{ $item->nama_pelanggan }}"
                                            data-no-telp="{{ $item->no_telp }}"
                                            data-email="{{ $item->email }}"
                                            data-alamat="{{ $item->alamat }}"
                                            data-keterangan="{{ $item->keterangan }}"
                                            data-status="{{ $item->is_aktif ? 'aktif' : 'nonaktif' }}"
                                            data-created="{{ $item->created_at->format('d M Y, H:i') }}"
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
                                                    data-id="{{ $item->id }}"
                                                    data-kode="{{ $item->kode_pelanggan }}"
                                                    data-nama="{{ $item->nama_pelanggan }}"
                                                    data-no-telp="{{ $item->no_telp }}"
                                                    data-email="{{ $item->email }}"
                                                    data-alamat="{{ $item->alamat }}"
                                                    data-keterangan="{{ $item->keterangan }}"
                                                    data-is-aktif="{{ $item->is_aktif ? '1' : '0' }}"
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-gray-600 hover:text-[#0F034D] hover:bg-gray-50 transition-colors cursor-pointer">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                                Edit Pelanggan
                                            </button>
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <form action="{{ route('admin.pelanggan.destroy', $item->id) }}" method="POST" class="w-full" onsubmit="return handleDeletePelanggan(this, '{{ $item->nama_pelanggan }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-red-500 hover:bg-red-50 transition-colors cursor-pointer">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                                    Hapus Pelanggan
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

        @if($pelanggan->hasPages())
            <div class="p-4 border-t border-gray-100 rounded-b-xl bg-white relative z-10">
                <x-pagination.custom-global-pagination :paginator="$pelanggan" />
            </div>
        @endif
    </div>

    {{-- ========================================= --}}
    {{-- MODAL TAMBAH PELANGGAN --}}
    {{-- ========================================= --}}
    <div id="add-modal" class="slide-panel">
        <div class="slide-panel-backdrop" data-panel-close></div>
        <div class="slide-panel-body">
            <div class="slide-panel-header">
                <div class="slide-panel-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <h2 class="slide-panel-header-title">Tambah Pelanggan</h2>
                <button class="slide-panel-close" data-panel-close><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form action="{{ route('admin.pelanggan.store') }}" method="POST" id="addForm" class="slide-panel-content">
                @csrf
                <div class="space-y-4">
                    <!-- Kode Pelanggan (Auto Generate Preview) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Pelanggan</label>
                        <input type="text" id="add_kode_pelanggan" readonly data-next-number="{{ $nextNumber }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm bg-gray-50 text-gray-500 italic cursor-not-allowed">
                        <p class="text-xs text-gray-400 mt-1">Otomatis digenerate oleh sistem.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Pelanggan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_pelanggan" required placeholder="Contoh: PT Mitra Jaya Abadi" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">No. Telepon <span class="text-red-500">*</span></label>
                            <input type="text" name="no_telp" required placeholder="08xxxxxxxxxx" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required placeholder="pelanggan@email.com" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat <span class="text-red-500">*</span></label>
                        <textarea name="alamat" required rows="3" placeholder="Alamat lengkap pelanggan" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="2" placeholder="Keterangan tambahan (opsional)" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm"></textarea>
                    </div>
                </div>
            </form>
            <div class="slide-panel-footer">
                <button type="button" class="btn-panel-cancel" data-panel-close>Batal</button>
                <button type="submit" form="addForm" class="btn-panel-submit">Simpan Data</button>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- MODAL EDIT PELANGGAN --}}
    {{-- ========================================= --}}
    <div id="edit-modal" class="slide-panel">
        <div class="slide-panel-backdrop" data-panel-close></div>
        <div class="slide-panel-body">
            <div class="slide-panel-header">
                <div class="slide-panel-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <h2 class="slide-panel-header-title">Edit Pelanggan</h2>
                <button class="slide-panel-close" data-panel-close><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form action="" method="POST" id="editForm" class="slide-panel-content">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <input type="hidden" id="edit_kode">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Pelanggan</label>
                        <input type="text" id="edit_kode_display" readonly class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Pelanggan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_pelanggan" id="edit_nama" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">No. Telepon <span class="text-red-500">*</span></label>
                            <input type="text" name="no_telp" id="edit_no_telp" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
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
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" id="edit_keterangan" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm"></textarea>
                    </div>

                    <!-- Checkbox is_aktif -->
                    <div>
                        <input type="hidden" name="is_aktif" value="0">
                        <input type="checkbox" name="is_aktif" id="edit_is_aktif" value="1" class="hidden" onchange="updateCheckbox(this, 'edit_cb')">
                        <div id="edit_cb_wrapper" onclick="document.getElementById('edit_is_aktif').click()" class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-all">
                            <div id="edit_cb_box" class="relative flex shrink-0 items-center justify-center w-5 h-5 rounded border-2 border-gray-300 transition-all">
                                <svg id="edit_cb_icon" class="w-3 h-3 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <span id="edit_cb_text" class="text-sm font-semibold text-gray-700">Aktif</span>
                                <p class="text-xs text-gray-500">Pelanggan ini dapat digunakan dalam proses bisnis.</p>
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

    {{-- ========================================= --}}
    {{-- MODAL DETAIL PELANGGAN --}}
    {{-- ========================================= --}}
    <div id="detail-modal" class="slide-panel">
        <div class="slide-panel-backdrop" data-panel-close></div>
        <div class="slide-panel-body">
            <div class="slide-panel-header">
                <div class="slide-panel-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <h2 class="slide-panel-header-title">Detail Pelanggan</h2>
                <button class="slide-panel-close" data-panel-close><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="slide-panel-content">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Kode Pelanggan</label>
                        <p id="detail_kode" class="text-sm text-gray-900 font-bold">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Nama Pelanggan</label>
                        <p id="detail_nama" class="text-sm text-gray-900 font-bold">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">No. Telepon</label>
                        <p id="detail_no_telp" class="text-sm text-gray-900 font-bold">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Email</label>
                        <p id="detail_email" class="text-sm text-gray-900 font-bold">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Alamat</label>
                        <p id="detail_alamat" class="text-sm text-gray-900">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Keterangan</label>
                        <p id="detail_keterangan" class="text-sm text-gray-900">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Status</label>
                        <div id="detail_status"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Dibuat</label>
                        <p id="detail_created" class="text-sm text-gray-900">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 CDN untuk konfirmasi hapus -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite([
        'resources/css/global-modal.css',
        'resources/js/admin/custom-forms.js',
        'resources/js/admin/pelanggan/toggle-modal.js',
        'resources/js/admin/pelanggan/generate-kode.js'
    ])
</x-layouts.admin>

<x-layouts.admin>
    <x-slot:breadcrumb>
        <li class="flex items-center gap-1.5 text-gray-400">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
            <span class="select-none">Manajemen Data</span>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            Standard Baseline Produksi
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Standard Baseline Produksi
    </x-slot:header>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <!-- Header -->
        <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-t-xl">
            <div>
                <h3 class="text-lg font-bold text-[#0F034D]">Daftar Baseline Produksi</h3>
                <p class="text-sm text-gray-500 mt-1">Kelola standar estimasi hasil produksi per roll bahan baku untuk setiap produk.</p>
            </div>
            <button onclick="window.togglePanel('add-modal')" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-sm font-medium rounded-xl transition-all shadow-md shadow-[#0F034D]/20 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Tambah Baseline
            </button>
        </div>

        <!-- Filter, Sorting & Search Bar -->
        <div class="px-6 py-3 bg-gray-50/50 border-b border-gray-100 flex flex-col sm:flex-row items-center gap-4 relative z-20">
            <!-- KIRI: Grup Tombol Aksi (Filter & Sort) -->
            <div class="flex items-center gap-3 w-full sm:w-auto shrink-0 relative">
                <!-- TOMBOL & MENU FILTER -->
                <div class="relative w-1/2 sm:w-auto">
                    <button type="button" data-toggle-filter-menu="filterDropdown" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-white border {{ request()->hasAny(['kategori', 'status']) ? 'border-[#0F034D] text-[#0F034D] bg-blue-50/50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        Filter
                        @if(request()->has('status'))
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#0F034D] opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-[#0F034D]"></span>
                            </span>
                        @endif
                    </button>
                    <!-- Dropdown Filter -->
                    <div id="filterDropdown" class="absolute left-0 mt-2 w-full sm:w-56 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden py-2">
                        <!-- Nested Status -->
                        <div class="relative group">
                            <button type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0F034D] transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Status
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
                        @if(request()->has('status'))
                            <div class="px-4 pt-2 mt-2 border-t border-gray-100">
                                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="block w-full text-center px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors">Reset Filter</a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- TOMBOL & MENU SORTING -->
                <div class="relative w-1/2 sm:w-auto">
                    <button type="button" data-toggle-filter-menu="sortDropdown" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-white border {{ request('sort') ? 'border-[#0F034D] text-[#0F034D] bg-blue-50/50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
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
                                    Abjad Nama Produk
                                </span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            <div class="absolute top-0 left-full pl-1 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                                <div class="w-48 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 p-2 space-y-0.5">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_produk_asc']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('sort') == 'nama_produk_asc' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">A - Z</a>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_produk_desc']) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request('sort') == 'nama_produk_desc' ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Z - A</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Hapus Filter/Pencarian -->
            @if(request()->hasAny(['search', 'kategori', 'status', 'sort']))
                <a href="{{ route('admin.standard-baseline-produksi.index') }}" title="Hapus Semua Filter & Pencarian" class="hidden sm:flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 rounded-xl transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </a>
            @endif

            <!-- KANAN: Search Bar -->
            <form method="GET" action="{{ route('admin.standard-baseline-produksi.index') }}" class="relative flex-1">
                @if(request('kategori')) <input type="hidden" name="kategori" value="{{ request('kategori') }}"> @endif
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk atau bahan baku..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors bg-white shadow-sm">
            </form>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto rounded-b-xl relative z-10">
            <table class="w-full text-left text-sm text-gray-500 whitespace-nowrap">
                <thead class="text-xs text-gray-400 uppercase bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Produk</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Bahan Baku</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Pcs per Roll</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Toleransi (-)</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Status</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($estimasi as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $item->produk?->nama_produk ?? 'Produk Terhapus' }}</div>
                                        <div class="text-xs font-medium text-gray-400 mt-0.5">
                                            @if($item->produk)
                                                {{ $item->produk->kode_produk }}  -  {{ ucfirst($item->produk->ukuran) }}  -  {{ ucfirst($item->produk->warna) }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $item->bahanBaku?->nama_bahan ?? 'Bahan Baku Terhapus' }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    @if($item->bahanBaku)
                                        {{ $item->bahanBaku->kode_bahan }}  -  {{ ucfirst($item->bahanBaku->warna) }}  -  {{ ucfirst($item->bahanBaku->kategori) }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="text-lg font-bold text-[#0F034D]">{{ number_format($item->pcs_per_roll) }}</span>
                                <span class="text-xs text-gray-400 ml-1">pcs</span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($item->toleransi_minus > 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-600 border border-amber-100">
                                        -{{ $item->toleransi_minus }} pcs
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
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
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Tombol View Detail -->
                                    <button type="button"
                                            onclick="window.openDetailModal(this)"
                                            data-id="{{ $item->id }}"
                                            data-produk-id="{{ $item->produk_id }}"
                                            data-produk-nama="{{ $item->produk->nama_produk }}"
                                            data-produk-kode="{{ $item->produk->kode_produk }}"
                                            data-produk-ukuran="{{ $item->produk->ukuran }}"
                                            data-produk-warna="{{ ucfirst($item->produk->warna) }}"
                                            data-bahan-id="{{ $item->bahan_baku_id }}"
                                            data-bahan-nama="{{ $item->bahanBaku->nama_bahan }}"
                                            data-bahan-kode="{{ $item->bahanBaku->kode_bahan }}"
                                            data-bahan-warna="{{ ucfirst($item->bahanBaku->warna) }}"
                                            data-bahan-kategori="{{ ucfirst($item->bahanBaku->kategori) }}"
                                            data-pcs="{{ $item->pcs_per_roll }}"
                                            data-toleransi="{{ $item->toleransi_minus }}"
                                            data-range-bawah="{{ $item->range_bawah }}"
                                            data-keterangan="{{ $item->keterangan ?? '' }}"
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
                                                    data-produk-id="{{ $item->produk_id }}"
                                                    data-bahan-id="{{ $item->bahan_baku_id }}"
                                                    data-pcs="{{ $item->pcs_per_roll }}"
                                                    data-toleransi="{{ $item->toleransi_minus }}"
                                                    data-keterangan="{{ $item->keterangan ?? '' }}"
                                                    data-status="{{ $item->is_aktif ? '1' : '0' }}"
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-gray-600 hover:text-[#0F034D] hover:bg-gray-50 transition-colors cursor-pointer">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                                Edit Baseline
                                            </button>
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <form action="{{ route('admin.standard-baseline-produksi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus baseline ini? Data terkait mungkin akan terpengaruh.');" class="w-full">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-red-500 hover:bg-red-50 transition-colors cursor-pointer">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                                    Hapus Baseline
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    <p class="text-gray-500 font-medium">Belum ada data baseline.</p>
                                    <p class="text-sm text-gray-400 mt-1">Tambahkan baseline produksi pertama Anda untuk memulai.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($estimasi->hasPages())
            <div class="p-4 border-t border-gray-100 rounded-b-xl bg-white relative z-10">
                <x-pagination.custom-global-pagination :paginator="$estimasi" />
            </div>
        @endif
    </div>

    {{-- ========================================= --}}
    @include('admin.standard-baseline-produksi.partials._tambah-baseline')

    @include('admin.standard-baseline-produksi.partials._edit-baseline')

    @include('admin.standard-baseline-produksi.partials._detail-baseline')


    @vite([
        'resources/css/global-modal.css',
        'resources/js/admin/filter-dropdown.js',
        'resources/js/admin/standard-baseline-produksi/toggle-modal.js'
    ])
</x-layouts.admin>






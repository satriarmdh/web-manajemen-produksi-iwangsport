<x-layouts.admin>
    <x-slot:breadcrumb>
        <li class="flex items-center">
            <span class="text-gray-400 select-none">Inventory & Stok</span>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            Pergerakan Stok Bahan Baku
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Pergerakan Stok Bahan Baku
    </x-slot:header>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px" aria-label="Tabs">
                <a href="{{ route('admin.pergerakan-stok.index', ['tab' => 'masuk']) }}" 
                   class="group flex items-center gap-2 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors {{ $tab === 'masuk' ? 'border-[#0F034D] text-[#0F034D]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <svg class="w-5 h-5 {{ $tab === 'masuk' ? 'text-green-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 19V5"/><path d="m5 12 7 7 7-7"/>
                    </svg>
                    Masuk
                </a>
                <a href="{{ route('admin.pergerakan-stok.index', ['tab' => 'keluar']) }}" 
                   class="group flex items-center gap-2 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors {{ $tab === 'keluar' ? 'border-[#0F034D] text-[#0F034D]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <svg class="w-5 h-5 {{ $tab === 'keluar' ? 'text-red-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 5v14"/><path d="m19 12-7-7-7 7"/>
                    </svg>
                    Keluar
                </a>
            </nav>
        </div>

        @php
            $searchName = $tab === 'masuk' ? 'search_masuk' : 'search_keluar';
            $kategoriName = $tab === 'masuk' ? 'kategori_masuk' : 'kategori_keluar';
            $tanggalName = $tab === 'masuk' ? 'tanggal_masuk' : 'tanggal_keluar';
            $supplierName = 'supplier_masuk';
            $searchPlaceholder = $tab === 'masuk' ? 'Cari bahan baku atau supplier...' : 'Cari bahan baku atau penerima...';
        @endphp

        <!-- Toolbar: Search + Filter + Tambah -->
        <div class="px-6 py-4 border-b border-gray-100 relative z-20">
            <div class="flex flex-wrap items-center gap-2">
                @php $hasActiveFilter = request($kategoriName) || ($tab === 'masuk' && request($supplierName)) || request($tanggalName); @endphp
                <div class="relative md:hidden">
                    <button type="button" onclick="toggleFilterMenu('filterDropdownMobile')" class="flex items-center gap-2 px-3 py-2.5 bg-white border {{ $hasActiveFilter ? 'border-[#0F034D] text-[#0F034D]' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        @if($hasActiveFilter)
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#0F034D] opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-[#0F034D]"></span>
                            </span>
                        @endif
                    </button>
                    <div id="filterDropdownMobile" class="absolute left-0 mt-2 w-64 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.12)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden py-2">
                        <!-- Nested: Kategori -->
                        <div class="relative group">
                            <button type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0F034D] transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    Kategori
                                </span>
                                <svg class="nested-arrow w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/></svg>
                            </button>
                            <div class="hidden mt-1 px-2 pb-1 nested-submenu">
                                <div class="bg-gray-50 rounded-lg border border-gray-100 p-2 space-y-0.5">
                                    @php
                                        $kategoriList = $tab === 'masuk' 
                                            ? ['kain','benang','kancing','resleting','aksesoris'] 
                                            : ['benang','kancing','resleting','aksesoris'];
                                    @endphp
                                    <a href="{{ route('admin.pergerakan-stok.index', ['tab' => $tab, $searchName => request($searchName), $tanggalName => request($tanggalName)] + ($tab === 'masuk' ? [$supplierName => request($supplierName)] : [])) }}" 
                                       class="block px-3 py-2 text-sm rounded-lg transition-colors {{ !request($kategoriName) ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Semua Kategori</a>
                                    @foreach($kategoriList as $kat)
                                        <a href="{{ route('admin.pergerakan-stok.index', ['tab' => $tab, $kategoriName => $kat, $searchName => request($searchName), $tanggalName => request($tanggalName)] + ($tab === 'masuk' ? [$supplierName => request($supplierName)] : [])) }}" 
                                           class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request($kategoriName) == $kat ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">{{ ucfirst($kat) }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        @if($tab === 'masuk')
                        <div class="border-t border-gray-100"></div>
                        <!-- Nested: Supplier -->
                        <div class="relative group">
                            <button type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0F034D] transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    Supplier
                                </span>
                                <svg class="nested-arrow w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/></svg>
                            </button>
                            <div class="hidden mt-1 px-2 pb-1 nested-submenu">
                                <div class="bg-gray-50 rounded-lg border border-gray-100 p-2 space-y-0.5 max-h-48 overflow-y-auto">
                                    <a href="{{ route('admin.pergerakan-stok.index', ['tab' => 'masuk', $searchName => request($searchName), $kategoriName => request($kategoriName), $tanggalName => request($tanggalName)]) }}" 
                                       class="block px-3 py-2 text-sm rounded-lg transition-colors {{ !request($supplierName) ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Semua Supplier</a>
                                    @foreach($suppliers as $s)
                                        <a href="{{ route('admin.pergerakan-stok.index', ['tab' => 'masuk', $supplierName => $s->id, $searchName => request($searchName), $kategoriName => request($kategoriName), $tanggalName => request($tanggalName)]) }}" 
                                           class="block px-3 py-2 text-sm rounded-lg transition-colors {{ request($supplierName) == $s->id ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">{{ $s->nama_supplier }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="border-t border-gray-100"></div>
                        <!-- Nested: Tanggal -->
                        <div class="p-3">
                            <form method="GET" action="{{ route('admin.pergerakan-stok.index') }}" class="space-y-2">
                                <input type="hidden" name="tab" value="{{ $tab }}">
                                @if(request($searchName)) <input type="hidden" name="{{ $searchName }}" value="{{ request($searchName) }}"> @endif
                                @if(request($kategoriName)) <input type="hidden" name="{{ $kategoriName }}" value="{{ request($kategoriName) }}"> @endif
                                @if($tab === 'masuk' && request($supplierName)) <input type="hidden" name="{{ $supplierName }}" value="{{ request($supplierName) }}"> @endif
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-sm font-medium text-gray-700">Tanggal</span>
                                </div>
                                <input type="date" name="{{ $tanggalName }}" value="{{ request($tanggalName) }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#0F034D]/20 focus:border-[#0F034D]">
                                <div class="flex gap-2">
                                    <button type="submit" class="flex-1 px-3 py-1.5 bg-[#0F034D] text-white text-xs font-medium rounded-lg hover:bg-[#0a0235] transition-colors">Terapkan</button>
                                    <a href="{{ route('admin.pergerakan-stok.index', ['tab' => $tab, $searchName => request($searchName), $kategoriName => request($kategoriName)] + ($tab === 'masuk' ? [$supplierName => request($supplierName)] : [])) }}" class="flex-1 px-3 py-1.5 text-center bg-gray-100 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
                                </div>
                            </form>
                        </div>

                        <!-- Reset semua filter -->
                        @if($hasActiveFilter)
                            <div class="px-4 pt-2 mt-2 border-t border-gray-100">
                                <a href="{{ route('admin.pergerakan-stok.index', ['tab' => $tab, $searchName => request($searchName)]) }}" class="block w-full text-center px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors">Reset Semua Filter</a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Search Bar -->
                <form method="GET" action="{{ route('admin.pergerakan-stok.index') }}" class="flex">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    @if(request($kategoriName)) <input type="hidden" name="{{ $kategoriName }}" value="{{ request($kategoriName) }}"> @endif
                    @if($tab === 'masuk' && request($supplierName)) <input type="hidden" name="{{ $supplierName }}" value="{{ request($supplierName) }}"> @endif
                    @if(request($tanggalName)) <input type="hidden" name="{{ $tanggalName }}" value="{{ request($tanggalName) }}"> @endif
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        <input type="text" name="{{ $searchName }}" value="{{ request($searchName) }}" placeholder="{{ $searchPlaceholder }}" class="w-48 sm:w-full md:w-64 pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors bg-white shadow-sm">
                    </div>
                </form>

                {{-- ===== DESKTOP: Tombol Filter Terpisah (hanya layar md+) ===== --}}
                <!-- Filter: Kategori -->
                <div class="relative hidden md:block" data-dropdown="kategori">
                    <button type="button" onclick="toggleDropdown('kategori')" class="flex items-center gap-2 px-4 py-2.5 bg-white border {{ request($kategoriName) ? 'border-[#0F034D] text-[#0F034D]' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        {{ request($kategoriName) ? ucfirst(request($kategoriName)) : 'Kategori' }}
                        <svg class="dropdown-arrow w-3.5 h-3.5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div id="dropdown-kategori" class="absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden py-2">
                        @php
                            $kategoriList = $tab === 'masuk' 
                                ? ['kain','benang','kancing','resleting','aksesoris'] 
                                : ['benang','kancing','resleting','aksesoris'];
                        @endphp
                        <a href="{{ route('admin.pergerakan-stok.index', ['tab' => $tab, $searchName => request($searchName), $tanggalName => request($tanggalName)] + ($tab === 'masuk' ? [$supplierName => request($supplierName)] : [])) }}" 
                           class="block px-4 py-2 text-sm transition-colors {{ !request($kategoriName) ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Semua Kategori</a>
                        @foreach($kategoriList as $kat)
                            <a href="{{ route('admin.pergerakan-stok.index', ['tab' => $tab, $kategoriName => $kat, $searchName => request($searchName), $tanggalName => request($tanggalName)] + ($tab === 'masuk' ? [$supplierName => request($supplierName)] : [])) }}" 
                               class="block px-4 py-2 text-sm transition-colors {{ request($kategoriName) == $kat ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">{{ ucfirst($kat) }}</a>
                        @endforeach
                    </div>
                </div>

                @if($tab === 'masuk')
                <!-- Filter: Supplier (hanya tab masuk, desktop only) -->
                <div class="relative hidden md:block" data-dropdown="supplier">
                    <button type="button" onclick="toggleDropdown('supplier')" class="flex items-center gap-2 px-4 py-2.5 bg-white border {{ request($supplierName) ? 'border-[#0F034D] text-[#0F034D]' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        @if(request($supplierName))
                            @php $supplierName_display = $suppliers->firstWhere('id', request($supplierName)); @endphp
                            {{ \Illuminate\Support\Str::limit($supplierName_display?->nama_supplier ?? 'Supplier', 7) }}
                        @else
                            Supplier
                        @endif
                        <svg class="dropdown-arrow w-3.5 h-3.5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div id="dropdown-supplier" class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden py-2 max-h-64 overflow-y-auto">
                        <a href="{{ route('admin.pergerakan-stok.index', ['tab' => 'masuk', $searchName => request($searchName), $kategoriName => request($kategoriName), $tanggalName => request($tanggalName)]) }}" 
                           class="block px-4 py-2 text-sm transition-colors {{ !request($supplierName) ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Semua Supplier</a>
                        @foreach($suppliers as $s)
                            <a href="{{ route('admin.pergerakan-stok.index', ['tab' => 'masuk', $supplierName => $s->id, $searchName => request($searchName), $kategoriName => request($kategoriName), $tanggalName => request($tanggalName)]) }}" 
                               class="block px-4 py-2 text-sm transition-colors {{ request($supplierName) == $s->id ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">{{ $s->nama_supplier }}</a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Filter: Tanggal (desktop only) -->
                <div class="relative hidden md:block" data-dropdown="tanggal">
                    <button type="button" onclick="toggleDropdown('tanggal')" class="flex items-center gap-2 px-4 py-2.5 bg-white border {{ request($tanggalName) ? 'border-[#0F034D] text-[#0F034D]' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @if(request($tanggalName))
                            {{ \Carbon\Carbon::parse(request($tanggalName))->format('d M Y') }}
                        @else
                            Pilih Tanggal
                        @endif
                        <svg class="dropdown-arrow w-3.5 h-3.5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div id="dropdown-tanggal" class="absolute left-0 mt-2 w-64 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden p-4">
                        <form method="GET" action="{{ route('admin.pergerakan-stok.index') }}">
                            <input type="hidden" name="tab" value="{{ $tab }}">
                            @if(request($searchName)) <input type="hidden" name="{{ $searchName }}" value="{{ request($searchName) }}"> @endif
                            @if(request($kategoriName)) <input type="hidden" name="{{ $kategoriName }}" value="{{ request($kategoriName) }}"> @endif
                            @if($tab === 'masuk' && request($supplierName)) <input type="hidden" name="{{ $supplierName }}" value="{{ request($supplierName) }}"> @endif
                            <label class="block text-xs font-medium text-gray-500 mb-2">Pilih Tanggal</label>
                            <input type="date" name="{{ $tanggalName }}" value="{{ request($tanggalName) }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#0F034D]/20 focus:border-[#0F034D] mb-3">
                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 px-3 py-1.5 bg-[#0F034D] text-white text-xs font-medium rounded-lg hover:bg-[#0a0235] transition-colors">Terapkan</button>
                                <a href="{{ route('admin.pergerakan-stok.index', ['tab' => $tab, $searchName => request($searchName), $kategoriName => request($kategoriName)] + ($tab === 'masuk' ? [$supplierName => request($supplierName)] : [])) }}" class="flex-1 px-3 py-1.5 text-center bg-gray-100 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Reset Filter Desktop (muncul jika ada filter aktif) -->
                @if($hasActiveFilter)
                    <a href="{{ route('admin.pergerakan-stok.index', ['tab' => $tab, $searchName => request($searchName)]) }}" 
                       class="hidden md:flex items-center gap-1.5 px-3 py-2.5 text-red-500 bg-red-50 hover:bg-red-100 rounded-xl text-sm font-medium transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif

                <!-- Spacer -->
                <div class="flex-1"></div>

                <!-- Tombol Tambah -->
                <button onclick="togglePanel('add-modal-{{ $tab }}')" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-sm font-medium rounded-xl transition-all shadow-md shadow-[#0F034D]/20 cursor-pointer shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    <span class="hidden sm:inline">Tambah Stok {{ $tab === 'masuk' ? 'Masuk' : 'Keluar' }}</span>
                    <span class="sm:hidden">Tambah</span>
                </button>
            </div>
        </div>

        <!-- Tab Content: Stok Masuk -->
        <div class="{{ $tab === 'masuk' ? '' : 'hidden' }}">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50/80 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Bahan Baku</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Supplier</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Admin</th>
                            <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @forelse($stokMasuk as $item)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                    {{ $item->created_at->format('d M Y') }}
                                    <span class="block text-xs text-gray-400 mt-0.5">{{ $item->created_at->format('H:i') }} WIB</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->bahanBaku?->nama_bahan }}</div>
                                    <div class="text-xs text-gray-400">{{ $item->bahanBaku?->kode_bahan }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 text-green-700 font-semibold bg-green-50 px-2.5 py-1 rounded-lg text-xs">
                                        +{{ $item->jumlah }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item->supplier?->nama_supplier ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $item->user?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <button onclick="showDetail('masuk', {{ json_encode([
                                            'tanggal' => $item->created_at->format('d M Y H:i'),
                                            'bahan_baku' => $item->bahanBaku?->nama_bahan,
                                            'kode_bahan' => $item->bahanBaku?->kode_bahan,
                                            'jumlah' => $item->jumlah,
                                            'satuan' => $item->bahanBaku?->satuan ?? '-',
                                            'supplier' => $item->supplier?->nama_supplier,
                                            'admin' => $item->user?->name,
                                            'catatan' => $item->catatan,
                                            'bukti' => $item->bukti_pembelian ? asset('storage/' . $item->bukti_pembelian) : null,
                                        ]) }})" class="p-2 text-[#0F034D] hover:bg-[#0F034D]/5 rounded-lg transition-colors cursor-pointer" title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.pemasukan-bahan.destroy', $item) }}" onsubmit="return confirm('Yakin hapus transaksi ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <p class="text-sm text-gray-400">Belum ada data stok masuk.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $stokMasuk->links() }}
            </div>
        </div>

        <!-- Tab Content: Stok Keluar -->
        <div class="{{ $tab === 'keluar' ? '' : 'hidden' }}">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50/80 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Bahan Baku</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Penerima</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Admin</th>
                            <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @forelse($stokKeluar as $item)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                    {{ $item->created_at->format('d M Y') }}
                                    <span class="block text-xs text-gray-400 mt-0.5">{{ $item->created_at->format('H:i') }} WIB</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->bahanBaku?->nama_bahan }}</div>
                                    <div class="text-xs text-gray-400">{{ $item->bahanBaku?->kode_bahan }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 text-red-700 font-semibold bg-red-50 px-2.5 py-1 rounded-lg text-xs">
                                        -{{ $item->jumlah }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item->penerima }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $item->user?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <button onclick="showDetail('keluar', {{ json_encode([
                                            'tanggal' => $item->created_at->format('d M Y H:i'),
                                            'bahan_baku' => $item->bahanBaku?->nama_bahan,
                                            'kode_bahan' => $item->bahanBaku?->kode_bahan,
                                            'jumlah' => $item->jumlah,
                                            'satuan' => $item->bahanBaku?->satuan ?? '-',
                                            'penerima' => $item->penerima,
                                            'admin' => $item->user?->name,
                                            'keterangan' => $item->keterangan,
                                            'bukti' => $item->bukti_pengeluaran ? asset('storage/' . $item->bukti_pengeluaran) : null,
                                        ]) }})" class="p-2 text-[#0F034D] hover:bg-[#0F034D]/5 rounded-lg transition-colors cursor-pointer" title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.pengeluaran-bahan.destroy', $item) }}" onsubmit="return confirm('Yakin hapus transaksi ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <p class="text-sm text-gray-400">Belum ada data stok keluar.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $stokKeluar->links() }}
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- MODAL DETAIL TRANSAKSI --}}
    {{-- ========================================= --}}
    <div id="detail-modal" class="slide-panel">
        <!-- Backdrop -->
        <div class="slide-panel-backdrop" data-panel-close="detail-modal"></div>

        <!-- Panel Body -->
        <div class="slide-panel-body">
            <!-- Header -->
            <div class="slide-panel-header">
                <div class="slide-panel-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="slide-panel-header-title" id="detail-title">Detail Transaksi</h3>
                    <p class="text-xs text-gray-500 mt-0.5" id="detail-date"></p>
                </div>
                <button class="slide-panel-close" data-panel-close="detail-modal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="slide-panel-content">
                <div id="detail-body">
                    <!-- Filled by JS -->
                </div>
            </div>

            <!-- Footer -->
            <div class="slide-panel-footer">
                <button type="button" onclick="closePanel('detail-modal')" class="btn-panel-cancel">Tutup</button>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- SLIDE PANEL: TAMBAH STOK MASUK --}}
    {{-- ========================================= --}}
    <div id="add-modal-masuk" class="slide-panel">
        <div class="slide-panel-backdrop" data-panel-close="add-modal-masuk"></div>
        <div class="slide-panel-body">
            <div class="slide-panel-header">
                <div class="slide-panel-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m7-7-7 7-7-7"/>
                    </svg>
                </div>
                <h3 class="slide-panel-header-title">Tambah Stok Masuk</h3>
                <button class="slide-panel-close" data-panel-close="add-modal-masuk">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.pemasukan-bahan.store') }}" enctype="multipart/form-data" id="addFormMasuk" class="slide-panel-content">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Bahan Baku <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="hidden" name="bahan_baku_id" id="masuk_bahan_baku_value" required>
                            <input type="text" id="masuk_bahan_baku_input" placeholder="Pilih Bahan Baku..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="dropdown-arrow w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            <div id="masuk_bahan_baku_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                <div class="p-2">
                                    @foreach($bahanBakuAll as $b)
                                    <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="{{ $b->id }}" data-text="{{ $b->nama_bahan }}">
                                        <span class="text-sm font-medium text-gray-700">{{ $b->nama_bahan }} <span class="text-gray-400">({{ $b->kode_bahan }})</span></span>
                                        <svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    @endforeach
                                </div>
                                <div id="masuk_bahan_baku_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
                        <input type="number" name="quantity" min="1" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] text-sm transition-colors" placeholder="Masukkan jumlah">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Supplier</label>
                        <div class="relative">
                            <input type="hidden" name="supplier_id" id="masuk_supplier_value">
                            <input type="text" id="masuk_supplier_input" placeholder="Pilih Supplier (opsional)..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="dropdown-arrow w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            <div id="masuk_supplier_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                <div class="p-2">
                                    <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="" data-text="Tidak Ada">
                                        <span class="text-sm font-medium text-gray-700">Tidak Ada (opsional)</span>
                                        <svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    @foreach($suppliers as $s)
                                    <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="{{ $s->id }}" data-text="{{ $s->nama_supplier }}">
                                        <span class="text-sm font-medium text-gray-700">{{ $s->nama_supplier }}</span>
                                        <svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    @endforeach
                                </div>
                                <div id="masuk_supplier_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] text-sm transition-colors" placeholder="Catatan tambahan..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Bukti Pembelian (Invoice/Foto)</label>
                        <input type="file" name="bukti_pembelian" accept="image/*,.pdf" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:bg-gray-100 file:text-gray-700 transition-colors">
                        <p class="text-xs text-gray-500 mt-1">Pilih file atau ambil foto langsung dari perangkat</p>
                    </div>
                </div>
            </form>

            <div class="slide-panel-footer">
                <button type="button" onclick="closePanel('add-modal-masuk')" class="btn-panel-cancel">Batal</button>
                <button type="submit" form="addFormMasuk" class="btn-panel-submit">Simpan</button>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- SLIDE PANEL: TAMBAH STOK KELUAR --}}
    {{-- ========================================= --}}
    <div id="add-modal-keluar" class="slide-panel">
        <div class="slide-panel-backdrop" data-panel-close="add-modal-keluar"></div>
        <div class="slide-panel-body">
            <div class="slide-panel-header">
                <div class="slide-panel-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19V5m-7 7 7-7 7 7"/>
                    </svg>
                </div>
                <h3 class="slide-panel-header-title">Tambah Stok Keluar</h3>
                <button class="slide-panel-close" data-panel-close="add-modal-keluar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.pengeluaran-bahan.store') }}" enctype="multipart/form-data" id="addFormKeluar" class="slide-panel-content">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Bahan Baku <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="hidden" name="bahan_baku_id" id="keluar_bahan_baku_value" required>
                            <input type="text" id="keluar_bahan_baku_input" placeholder="Pilih Bahan Baku (Non-Kain)..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="dropdown-arrow w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            <div id="keluar_bahan_baku_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                <div class="p-2">
                                    @foreach($bahanBakuNonKain as $b)
                                    <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="{{ $b->id }}" data-text="{{ $b->nama_bahan }}">
                                        <span class="text-sm font-medium text-gray-700">{{ $b->nama_bahan }} <span class="text-gray-400">({{ $b->kode_bahan }})</span> <span class="text-xs text-gray-400">- Stok: {{ $b->stok }}</span></span>
                                        <svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    @endforeach
                                </div>
                                <div id="keluar_bahan_baku_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
                        <input type="number" name="quantity" min="1" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] text-sm transition-colors" placeholder="Masukkan jumlah">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Penerima <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="hidden" name="penerima" id="keluar_penerima_value" required>
                            <input type="text" id="keluar_penerima_input" placeholder="Pilih Penerima..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="dropdown-arrow w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            <div id="keluar_penerima_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                <div class="p-2">
                                    @foreach($karyawan as $k)
                                    <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="{{ $k->name }}" data-text="{{ $k->name }}">
                                        <span class="text-sm font-medium text-gray-700">{{ $k->name }} <span class="text-xs text-gray-400">({{ ucfirst($k->role) }})</span></span>
                                        <svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    @endforeach
                                </div>
                                <div id="keluar_penerima_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] text-sm transition-colors" placeholder="Catatan tambahan..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Bukti Pengeluaran (Foto)</label>
                        <input type="file" name="bukti_pengeluaran" accept="image/*,.pdf" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:bg-gray-100 file:text-gray-700 transition-colors">
                        <p class="text-xs text-gray-500 mt-1">Pilih file atau ambil foto langsung dari perangkat</p>
                    </div>
                </div>
            </form>

            <div class="slide-panel-footer">
                <button type="button" onclick="closePanel('add-modal-keluar')" class="btn-panel-cancel">Batal</button>
                <button type="submit" form="addFormKeluar" class="btn-panel-submit">Simpan</button>
            </div>
        </div>
    </div>

    @vite([
        'resources/css/global-modal.css',
        'resources/js/admin/custom-forms.js',
        'resources/js/admin/pergerakan-stok/toggle-modal.js',
    ])
</x-layouts.admin>

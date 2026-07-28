<x-layouts.owner>
    <x-slot:breadcrumb>
        <li class="flex items-center">
            <span class="text-gray-400 select-none">Laporan & Riwayat</span>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            Mutasi Bahan Baku
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Mutasi Bahan Baku
    </x-slot:header>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <!-- Header -->
        <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-t-xl">
            <div>
                <h3 class="text-lg font-bold text-[#0F034D]">Riwayat Mutasi Bahan Baku</h3>
                <p class="text-sm text-gray-500 mt-1">Daftar lengkap riwayat pemasukan dan penggunaan/penyerahan bahan baku (read-only).</p>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="px-6 pt-5 pb-3 border-b border-gray-100 flex gap-2">
            <a href="{{ route('owner.mutasi-bahan-baku.index', ['tab' => 'masuk']) }}" 
               class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition-all {{ $tab === 'masuk' ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 19V5"/><path d="m5 12 7 7 7-7"/>
                </svg>
                Bahan Masuk (Pembelian)
            </a>
            <a href="{{ route('owner.mutasi-bahan-baku.index', ['tab' => 'keluar']) }}" 
               class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition-all {{ $tab === 'keluar' ? 'bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">
                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 5v14"/><path d="m19 12-7-7-7 7"/>
                </svg>
                Bahan Keluar (Penyerahan & Penggunaan)
            </a>
        </div>

        @php
            $searchName = $tab === 'masuk' ? 'search_masuk' : 'search_keluar';
            $kategoriName = $tab === 'masuk' ? 'kategori_masuk' : 'kategori_keluar';
            $tanggalMulaiName = $tab === 'masuk' ? 'tanggal_mulai_masuk' : 'tanggal_mulai_keluar';
            $tanggalAkhirName = $tab === 'masuk' ? 'tanggal_akhir_masuk' : 'tanggal_akhir_keluar';
            $searchPlaceholder = $tab === 'masuk' ? 'Cari nomor transaksi atau supplier...' : 'Cari nomor transaksi atau penerima...';
        @endphp

        <!-- Toolbar: Search + Filter -->
        <div class="px-6 py-4 border-b border-gray-100 relative z-20">
            <div class="flex flex-wrap items-center gap-2">
                @php $hasActiveFilter = request($kategoriName) || request($tanggalMulaiName) || request($tanggalAkhirName) || request($searchName); @endphp

                <!-- Search Bar -->
                <form method="GET" action="{{ route('owner.mutasi-bahan-baku.index') }}" class="flex">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    @if(request($kategoriName)) <input type="hidden" name="{{ $kategoriName }}" value="{{ request($kategoriName) }}"> @endif
                    @if(request($tanggalMulaiName)) <input type="hidden" name="{{ $tanggalMulaiName }}" value="{{ request($tanggalMulaiName) }}"> @endif
                    @if(request($tanggalAkhirName)) <input type="hidden" name="{{ $tanggalAkhirName }}" value="{{ request($tanggalAkhirName) }}"> @endif
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        <input type="text" name="{{ $searchName }}" value="{{ request($searchName) }}" placeholder="{{ $searchPlaceholder }}" class="w-48 sm:w-full md:w-64 pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors bg-white shadow-sm">
                    </div>
                </form>

                <!-- Filter: Kategori -->
                <div class="relative" data-dropdown="kategori">
                    <button type="button" id="btn-dropdown-kategori" class="flex items-center gap-2 px-4 py-2.5 bg-white border {{ request($kategoriName) ? 'border-[#0F034D] text-[#0F034D]' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        {{ request($kategoriName) ? ucfirst(request($kategoriName)) : 'Kategori' }}
                        <svg class="dropdown-arrow w-3.5 h-3.5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div id="dropdown-kategori" class="absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden p-1.5">
                        @php
                            $kategoriList = ['kain','benang','kancing','resleting','aksesoris'];
                        @endphp
                        <a href="{{ route('owner.mutasi-bahan-baku.index', ['tab' => $tab, $searchName => request($searchName), $tanggalMulaiName => request($tanggalMulaiName), $tanggalAkhirName => request($tanggalAkhirName)]) }}" 
                           class="block px-3 py-2 rounded-lg text-sm transition-colors {{ !request($kategoriName) ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Semua Kategori</a>
                        @foreach($kategoriList as $kat)
                            <a href="{{ route('owner.mutasi-bahan-baku.index', ['tab' => $tab, $kategoriName => $kat, $searchName => request($searchName), $tanggalMulaiName => request($tanggalMulaiName), $tanggalAkhirName => request($tanggalAkhirName)]) }}" 
                               class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request($kategoriName) == $kat ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">{{ ucfirst($kat) }}</a>
                        @endforeach
                    </div>
                </div>

                <!-- Filter: Tanggal -->
                <div class="relative" data-dropdown="tanggal">
                    <button type="button" id="btn-dropdown-tanggal" class="flex items-center gap-2 px-4 py-2.5 bg-white border {{ (request($tanggalMulaiName) || request($tanggalAkhirName)) ? 'border-[#0F034D] text-[#0F034D]' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @if(request($tanggalMulaiName) || request($tanggalAkhirName))
                            @php
                                $mulai = request($tanggalMulaiName);
                                $akhir = request($tanggalAkhirName);
                                if ($mulai && $akhir) {
                                    $labelText = \Carbon\Carbon::parse($mulai)->format('d M Y') . ' — ' . \Carbon\Carbon::parse($akhir)->format('d M Y');
                                } elseif ($mulai) {
                                    $labelText = \Carbon\Carbon::parse($mulai)->format('d M Y') . ' — Sekarang';
                                } else {
                                    $labelText = 's/d ' . \Carbon\Carbon::parse($akhir)->format('d M Y');
                                }
                            @endphp
                            {{ $labelText }}
                        @else
                            Rentang Tanggal
                        @endif
                        <svg class="dropdown-arrow w-3.5 h-3.5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div id="dropdown-tanggal" class="absolute left-0 mt-2 w-72 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden p-4">
                        <form method="GET" action="{{ route('owner.mutasi-bahan-baku.index') }}">
                            <input type="hidden" name="tab" value="{{ $tab }}">
                            @if(request($searchName)) <input type="hidden" name="{{ $searchName }}" value="{{ request($searchName) }}"> @endif
                            @if(request($kategoriName)) <input type="hidden" name="{{ $kategoriName }}" value="{{ request($kategoriName) }}"> @endif
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Tanggal Awal</label>
                                    <input type="date" name="{{ $tanggalMulaiName }}" value="{{ request($tanggalMulaiName) }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Tanggal Akhir</label>
                                    <input type="date" name="{{ $tanggalAkhirName }}" value="{{ request($tanggalAkhirName) }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors">
                                </div>
                                <div class="flex gap-2 pt-1">
                                    <a href="{{ route('owner.mutasi-bahan-baku.index', ['tab' => $tab, $searchName => request($searchName), $kategoriName => request($kategoriName)]) }}" class="flex-1 px-3 py-1.5 text-center bg-gray-100 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
                                    <button type="submit" class="flex-1 px-3 py-1.5 bg-[#0F034D] text-white text-xs font-medium rounded-lg hover:bg-[#0a0235] transition-colors">Terapkan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Reset All Filter -->
                @if($hasActiveFilter)
                    <a href="{{ route('owner.mutasi-bahan-baku.index', ['tab' => $tab]) }}" 
                       class="flex items-center gap-1.5 px-3 py-2.5 text-red-500 bg-red-50 hover:bg-red-100 rounded-xl text-sm font-medium transition-colors cursor-pointer" title="Reset Semua Filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </div>

        <!-- Tab Content: Stok Masuk -->
        <div class="{{ $tab === 'masuk' ? '' : 'hidden' }}">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50/80 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor Transaksi</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Supplier (Pembelian)</th>
                            <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Item</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                            <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @forelse($stokMasuk as $item)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#0F034D]">{{ $item->nomor_transaksi }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                    {{ $item->tanggal->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item->supplier?->nama_supplier ?? '-' }}</td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 text-green-700 font-semibold bg-green-50 px-2.5 py-1 rounded-lg text-xs">
                                        +{{ $item->detailPergerakanStok->sum('jumlah') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $item->user?->name ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('owner.mutasi-bahan-baku.show', $item) }}" class="p-2 text-[#0F034D] hover:bg-[#0F034D]/5 rounded-lg transition-colors cursor-pointer" title="Lihat Detail & Bukti">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <p class="text-sm text-gray-400">Belum ada data bahan masuk.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($stokMasuk->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    <x-pagination.custom-global-pagination :paginator="$stokMasuk" />
                </div>
            @endif
        </div>

        <!-- Tab Content: Stok Keluar -->
        <div class="{{ $tab === 'keluar' ? '' : 'hidden' }}">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50/80 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor Referensi</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Penerima (Penggunaan)</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Bahan & Summary</th>
                            <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Item</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                            <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @forelse($stokKeluar as $item)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#0F034D]">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $item->nomor }}</span>
                                        @if($item->tipe_mutasi === 'kain')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700">Kain / WO</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600">Non-Kain</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item->detail_tujuan ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $item->items_summary }}">
                                    {{ $item->items_summary }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 {{ $item->tipe_mutasi === 'kain' ? 'text-indigo-700 bg-indigo-50' : 'text-red-700 bg-red-50' }} px-2.5 py-1 rounded-lg text-xs font-semibold">
                                        -{{ $item->total_qty }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $item->creator ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('owner.mutasi-bahan-baku.show', ['pergerakanStok' => $item->id, 'type' => $item->tipe_mutasi]) }}" class="p-2 text-[#0F034D] hover:bg-[#0F034D]/5 rounded-lg transition-colors cursor-pointer" title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <p class="text-sm text-gray-400">Belum ada data bahan keluar.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($stokKeluar->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    <x-pagination.custom-global-pagination :paginator="$stokKeluar" />
                </div>
            @endif
        </div>
    </div>

    @vite([
        'resources/js/owner/mutasi-bahan-baku/index.js'
    ])
</x-layouts.owner>

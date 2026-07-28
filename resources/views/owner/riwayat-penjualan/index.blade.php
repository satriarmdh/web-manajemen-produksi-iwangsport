<x-layouts.owner>
    <x-slot:breadcrumb>
        <li class="flex items-center">
            <span class="text-gray-400 select-none">Laporan & Riwayat</span>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            Riwayat Penjualan
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Riwayat Penjualan
    </x-slot:header>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <!-- Header -->
        <div class="px-6 pt-6 pb-4 border-b border-gray-100 rounded-t-xl">
            <h3 class="text-lg font-bold text-[#0F034D]">Daftar Transaksi Penjualan</h3>
            <p class="text-sm text-gray-500 mt-1">Daftar transaksi penjualan produk jadi Iwangsport secara real-time.</p>
        </div>

        <!-- Toolbar: Search + Custom Dropdown Filter -->
        @php $hasActiveFilter = request('search') || request('tanggal_mulai') || request('tanggal_akhir'); @endphp
        <div class="px-6 py-4 border-b border-gray-100 relative z-20">
            <div class="flex flex-wrap items-center gap-2">
                <!-- Search Bar -->
                <form method="GET" action="{{ route('owner.riwayat-penjualan.index') }}" class="flex">
                    @if(request('tanggal_mulai')) <input type="hidden" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"> @endif
                    @if(request('tanggal_akhir')) <input type="hidden" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}"> @endif
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari invoice / pelanggan..." class="w-48 sm:w-full md:w-64 pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors bg-white shadow-sm">
                    </div>
                </form>

                <!-- Filter: Tanggal (Custom Dropdown) -->
                <div class="relative">
                    <button type="button" data-toggle-filter-menu="dropdown-tanggal" class="flex items-center gap-2 px-4 py-2.5 bg-white border {{ (request('tanggal_mulai') || request('tanggal_akhir')) ? 'border-[#0F034D] text-[#0F034D]' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @if(request('tanggal_mulai') || request('tanggal_akhir'))
                            @php
                                $mulai = request('tanggal_mulai');
                                $akhir = request('tanggal_akhir');
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
                        <form method="GET" action="{{ route('owner.riwayat-penjualan.index') }}">
                            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Tanggal Awal</label>
                                    <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Tanggal Akhir</label>
                                    <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors">
                                </div>
                                <div class="flex gap-2 pt-1">
                                    <a href="{{ route('owner.riwayat-penjualan.index', ['search' => request('search')]) }}" class="flex-1 px-3 py-1.5 text-center bg-gray-100 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
                                    <button type="submit" class="flex-1 px-3 py-1.5 bg-[#0F034D] text-white text-xs font-medium rounded-lg hover:bg-[#0a0235] transition-colors">Terapkan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Reset All Filter -->
                @if($hasActiveFilter)
                    <a href="{{ route('owner.riwayat-penjualan.index') }}" class="flex items-center gap-1.5 px-3 py-2.5 text-red-500 bg-red-50 hover:bg-red-100 rounded-xl text-sm font-medium transition-colors cursor-pointer" title="Reset semua filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor Invoice</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Item</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Harga</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 bg-white">
                    @forelse($penjualan as $row)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-[#0F034D]">{{ $row->nomor_invoice }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $row->tanggal->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $row->pelanggan->nama_pelanggan }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $row->pelanggan->kode_pelanggan }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="inline-flex items-center gap-1 text-[#0F034D] font-semibold bg-[#0F034D]/5 px-2.5 py-1 rounded-lg text-xs">{{ $row->total_item }} pcs</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-[#0F034D] text-right">Rp {{ number_format($row->total_harga, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="{{ route('owner.riwayat-penjualan.show', $row) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#0F034D] hover:bg-[#0a0235] text-white text-xs font-bold rounded-xl transition-all duration-200 cursor-pointer shadow-md shadow-[#0F034D]/10">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-700">Belum ada transaksi penjualan</p>
                                <p class="text-xs text-gray-400 mt-1">Transaksi penjualan yang dicatat oleh Admin akan muncul di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($penjualan->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                <x-pagination.custom-global-pagination :paginator="$penjualan" />
            </div>
        @endif
    </div>

    @vite([
        'resources/js/admin/filter-dropdown.js',
    ])
</x-layouts.owner>

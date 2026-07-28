<x-layouts.owner>
    <x-slot:breadcrumb>
        <li class="inline-flex items-center text-[#0F034D] font-semibold">
            <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-1.5">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                    </path>
                </svg>
                Dashboard
            </a>
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Dashboard Utama Owner
    </x-slot:header>

    <!-- Alert Banner Inventori Kritis -->
    @if($stats['bahan_menipis_count'] > 0 || $stats['produk_menipis_count'] > 0)
        <div class="mb-6 px-4 py-3.5 bg-amber-50 border border-amber-100 text-amber-800 rounded-xl text-sm flex items-center justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Perhatian! Ada <strong>{{ $stats['bahan_menipis_count'] }}</strong> bahan baku dan <strong>{{ $stats['produk_menipis_count'] }}</strong> produk dengan stok hampir habis.</span>
            </div>
            <a href="{{ route('owner.inventori') }}" class="text-xs font-bold bg-amber-100 hover:bg-amber-200 text-amber-900 px-3 py-1.5 rounded-lg transition-colors cursor-pointer shrink-0">Lihat Inventori</a>
        </div>
    @endif

    <!-- Stat Cards Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Card 1: WO Menunggu Persetujuan (Amber - butuh perhatian) -->
        <a href="{{ route('owner.perintah-produksi.index') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between gap-3 min-h-[92px] hover:border-amber-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider truncate">Persetujuan WO</p>
                <h3 class="text-2xl font-bold {{ $stats['wo_pending_count'] > 0 ? 'text-amber-600' : 'text-gray-950' }} mt-1 leading-none flex items-center gap-2 tabular-nums">
                    {{ $stats['wo_pending_count'] }} <span class="text-xs font-normal text-gray-400">perintah produksi</span>
                    @if($stats['wo_pending_count'] > 0)
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                    @endif
                </h3>
                <span class="text-[11px] text-gray-400 mt-1.5 flex items-center gap-1 font-medium group-hover:text-amber-600 transition-colors">
                    Butuh approval
                    <svg class="w-3 h-3 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </span>
            </div>
            <div class="w-11 h-11 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-amber-100 group-hover:scale-105 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
        </a>

        <!-- Card 2: Total Staff / Karyawan -->
        <a href="{{ route('owner.users.index') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between gap-3 min-h-[92px] hover:border-gray-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider truncate">Total Karyawan</p>
                <h3 class="text-2xl font-bold text-gray-950 mt-1 leading-none tabular-nums">{{ $stats['total_staff_count'] }} <span class="text-xs font-normal text-gray-400">staff</span></h3>
                <span class="text-[11px] text-gray-400 mt-1.5 flex items-center gap-1 font-medium group-hover:text-[#0F034D] transition-colors">
                    Admin &amp; Karyawan Produksi
                    <svg class="w-3 h-3 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </span>
            </div>
            <div class="w-11 h-11 bg-gray-50 text-gray-500 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-gray-100 group-hover:scale-105 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
        </a>

        <!-- Card 3: Total Bahan Baku -->
        <a href="{{ route('owner.inventori') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between gap-3 min-h-[92px] hover:border-gray-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider truncate">Total Bahan Baku</p>
                <h3 class="text-2xl font-bold text-gray-950 mt-1 leading-none tabular-nums">{{ number_format($stats['total_bahan_count'], 0, ',', '.') }} <span class="text-xs font-normal text-gray-400">jenis</span></h3>
                <span class="text-[11px] text-gray-400 mt-1.5 flex items-center gap-1 font-medium group-hover:text-[#0F034D] transition-colors">
                    Kain &amp; aksesoris
                    <svg class="w-3 h-3 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </span>
            </div>
            <div class="w-11 h-11 bg-gray-50 text-gray-500 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-gray-100 group-hover:scale-105 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </a>

        <!-- Card 4: Total Varian Produk -->
        <a href="{{ route('owner.inventori') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between gap-3 min-h-[92px] hover:border-gray-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider truncate">Total Produk</p>
                <h3 class="text-2xl font-bold text-gray-950 mt-1 leading-none tabular-nums">{{ number_format($stats['total_produk_count'], 0, ',', '.') }} <span class="text-xs font-normal text-gray-400">varian</span></h3>
                <span class="text-[11px] text-gray-400 mt-1.5 flex items-center gap-1 font-medium group-hover:text-[#0F034D] transition-colors">
                    Celana siap jual
                    <svg class="w-3 h-3 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </span>
            </div>
            <div class="w-11 h-11 bg-gray-50 text-gray-500 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-gray-100 group-hover:scale-105 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
        </a>
    </div>

    <!-- Main Content Layout: 2 Kolom -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <!-- Kolom Kiri: Tren Penjualan (Bar Chart Berfilter) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-4 border-b border-gray-50">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Tren Penjualan</h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Total kuantitas produk terjual (Pcs) pada periode terpilih.
                        <span id="sales-granularity" class="text-gray-400"></span>
                    </p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Total Terjual</p>
                    <p id="sales-total" class="text-xl font-bold text-[#0F034D] tabular-nums leading-none mt-0.5">0 <span class="text-xs font-normal text-gray-400">Pcs</span></p>
                    <p id="sales-delta" class="text-[11px] font-medium mt-1 hidden"></p>
                </div>
            </div>

            <!-- Filter Periode -->
            <div class="flex flex-wrap items-center gap-2 mb-5">
                <div class="inline-flex gap-1 bg-gray-100/80 p-1 rounded-xl border border-gray-200/60">
                    <button type="button" data-range="7d" class="range-btn px-3 py-1.5 rounded-lg text-xs font-bold transition-all text-gray-500 hover:text-gray-800">1 Minggu</button>
                    <button type="button" data-range="30d" class="range-btn px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-[#0F034D] text-white shadow-sm">1 Bulan</button>
                    <button type="button" data-range="1y" class="range-btn px-3 py-1.5 rounded-lg text-xs font-bold transition-all text-gray-500 hover:text-gray-800">1 Tahun</button>
                </div>

                <!-- Filter: Rentang Tanggal (dropdown, meniru pola admin/pergerakan-stok) -->
                <div class="relative ml-auto" data-date-dropdown>
                    <button type="button" id="sales-date-toggle" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl text-xs font-semibold transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span id="sales-date-label">Rentang Tanggal</span>
                        <svg id="sales-date-arrow" class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div id="sales-date-dropdown" class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-right hidden p-4">
                        <div class="space-y-3">
                            <div>
                                <label for="sales-start" class="block text-xs font-medium text-gray-500 mb-1.5">Tanggal Awal</label>
                                <input type="date" id="sales-start" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors">
                            </div>
                            <div>
                                <label for="sales-end" class="block text-xs font-medium text-gray-500 mb-1.5">Tanggal Akhir</label>
                                <input type="date" id="sales-end" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors">
                            </div>
                            <div class="flex gap-2 pt-1">
                                <button type="button" id="sales-reset" class="flex-1 px-3 py-1.5 text-center bg-gray-100 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors">Reset</button>
                                <button type="button" id="sales-apply" class="flex-1 px-3 py-1.5 bg-[#0F034D] text-white text-xs font-medium rounded-lg hover:bg-[#0a0235] transition-colors">Terapkan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Canvas Chart -->
            <div class="relative h-64">
                <canvas id="salesTrendChart"></canvas>
                <div id="sales-empty" class="hidden absolute inset-0 flex flex-col items-center justify-center gap-3">
                    <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center">
                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-semibold text-gray-500">Belum ada penjualan</p>
                        <p class="text-xs text-gray-400 mt-0.5">Tidak ada transaksi pada periode ini.</p>
                    </div>
                </div>
                <div id="sales-loading" class="hidden absolute inset-0 flex items-center justify-center bg-white/60">
                    <svg class="w-6 h-6 text-[#0F034D] animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Widget Informasi -->
        <div class="space-y-6">
            <!-- Widget: Produk Terlaris -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-start justify-between gap-2 mb-4 pb-2 border-b border-gray-50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Produk Terlaris</h3>
                        <p class="text-[11px] text-gray-400 mt-0.5">30 hari terakhir</p>
                    </div>
                    <a href="{{ route('owner.inventori') }}" class="text-[11px] font-semibold text-[#0F034D] hover:underline shrink-0 mt-1">Lihat semua</a>
                </div>

                @php $maxQty = $topProduk->max('total_qty') ?: 1; @endphp

                @forelse($topProduk as $i => $item)
                    <div class="flex items-center gap-3 {{ !$loop->last ? 'mb-3.5' : '' }}">
                        <span class="w-5 h-5 rounded-md bg-gray-100 text-gray-500 text-[10px] font-bold flex items-center justify-center shrink-0 tabular-nums">{{ $i + 1 }}</span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-baseline justify-between gap-2">
                                <p class="text-xs font-semibold text-gray-700 truncate">
                                    {{ $item->produk->nama_produk ?? 'Produk dihapus' }}
                                    @if($item->produk?->warna)
                                        <span class="text-gray-400 font-normal">· {{ ucfirst($item->produk->warna) }}</span>
                                    @endif
                                </p>
                                <span class="text-xs font-bold text-[#0F034D] tabular-nums shrink-0">{{ number_format($item->total_qty, 0, ',', '.') }}</span>
                            </div>
                            <div class="mt-1.5 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#0F034D] rounded-full" style="width: {{ round(($item->total_qty / $maxQty) * 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center gap-3 text-center py-6">
                        <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center">
                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500">Belum ada penjualan</p>
                            <p class="text-xs text-gray-400 mt-0.5">Produk terlaris muncul setelah ada transaksi.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Widget: Aktivitas Terbaru -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-start justify-between gap-2 mb-4 pb-2 border-b border-gray-50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Aktivitas Terbaru</h3>
                        <p class="text-[11px] text-gray-400 mt-0.5">Mutasi stok terakhir</p>
                    </div>
                </div>

                @forelse($recentActivity['mutasiStok'] as $mutasi)
                    @php
                        // Arah ditentukan dari perubahan stok yang sebenarnya, bukan sekadar
                        // label jenis_pergerakan (yang bisa berupa 'inisiasi data'/'penyesuaian').
                        $selisih = (int) $mutasi->stok_sesudah - (int) $mutasi->stok_sebelum;
                        $arah = $selisih > 0 ? 'naik' : ($selisih < 0 ? 'turun' : 'netral');
                        $jumlahTampil = $selisih !== 0 ? abs($selisih) : (int) $mutasi->jumlah;
                        $warna = match ($arah) {
                            'naik' => 'bg-green-50 text-green-600',
                            'turun' => 'bg-rose-50 text-rose-600',
                            default => 'bg-gray-100 text-gray-400',
                        };
                        $warnaTeks = match ($arah) {
                            'naik' => 'text-green-600',
                            'turun' => 'text-rose-600',
                            default => 'text-gray-400',
                        };
                        $tanda = match ($arah) {
                            'naik' => '+',
                            'turun' => '−',
                            default => '',
                        };
                        $ikonPath = match ($arah) {
                            'naik' => 'M12 19V5m0 0l-6 6m6-6l6 6',
                            'turun' => 'M12 5v14m0 0l6-6m-6 6l-6-6',
                            default => 'M5 12h14',
                        };
                    @endphp
                    <div class="flex items-start gap-3 {{ !$loop->last ? 'mb-3.5 pb-3.5 border-b border-gray-50' : '' }}">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ $warna }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $ikonPath }}"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-gray-700 truncate">
                                {{ $mutasi->item->nama_bahan ?? $mutasi->item->nama_produk ?? 'Item dihapus' }}
                            </p>
                            <p class="text-[11px] text-gray-400 mt-0.5">
                                <span class="{{ $warnaTeks }} font-semibold tabular-nums">
                                    {{ $tanda }}{{ number_format($jumlahTampil, 0, ',', '.') }}
                                </span>
                                · {{ ucfirst($mutasi->jenis_pergerakan) }}
                                · {{ $mutasi->created_at->diffForHumans() }}
                                @if($mutasi->user)
                                    · {{ $mutasi->user->name }}
                                @endif
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center gap-3 text-center py-6">
                        <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center">
                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500">Belum ada aktivitas</p>
                            <p class="text-xs text-gray-400 mt-0.5">Mutasi stok akan tercatat di sini.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const initial = @json($salesTrend);
            const endpoint = "{{ route('owner.dashboard.sales-trend') }}";
            const canvas = document.getElementById('salesTrendChart');
            if (!canvas) return;

            const totalEl = document.getElementById('sales-total');
            const deltaEl = document.getElementById('sales-delta');
            const granularityEl = document.getElementById('sales-granularity');
            const emptyEl = document.getElementById('sales-empty');
            const loadingEl = document.getElementById('sales-loading');
            const rangeBtns = document.querySelectorAll('.range-btn');
            const startInput = document.getElementById('sales-start');
            const endInput = document.getElementById('sales-end');
            const applyBtn = document.getElementById('sales-apply');
            const resetBtn = document.getElementById('sales-reset');
            const dateToggle = document.getElementById('sales-date-toggle');
            const dateDropdown = document.getElementById('sales-date-dropdown');
            const dateArrow = document.getElementById('sales-date-arrow');
            const dateLabel = document.getElementById('sales-date-label');

            const NAVY = '#0F034D';
            let chart;

            function formatNumber(n) {
                return new Intl.NumberFormat('id-ID').format(n);
            }

            function renderChart(data) {
                const hasData = (data.values || []).some(v => v > 0);
                emptyEl.classList.toggle('hidden', hasData);
                totalEl.innerHTML = `${formatNumber(data.total || 0)} <span class="text-xs font-normal text-gray-400">Pcs</span>`;

                // Keterangan granularitas.
                if (granularityEl) {
                    granularityEl.textContent = data.granularity === 'month'
                        ? '(Dikelompokkan per bulan)'
                        : '(Dikelompokkan per hari)';
                }

                // Indikator perbandingan periode sebelumnya.
                if (deltaEl) {
                    const pct = data.change_percent;
                    deltaEl.classList.remove('hidden', 'text-green-600', 'text-rose-600', 'text-gray-400');
                    if (pct === null || pct === undefined) {
                        deltaEl.classList.add('text-gray-400');
                        deltaEl.innerHTML = 'Baru · tidak ada data periode lalu';
                    } else {
                        const up = pct >= 0;
                        deltaEl.classList.add(up ? 'text-green-600' : 'text-rose-600');
                        const arrow = up
                            ? '<svg class="w-3 h-3 inline -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>'
                            : '<svg class="w-3 h-3 inline -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>';
                        deltaEl.innerHTML = `${arrow} ${Math.abs(pct)}% <span class="text-gray-400 font-normal">dari periode lalu</span>`;
                    }
                }

                const cfgData = {
                    labels: data.labels || [],
                    datasets: [{
                        label: 'Terjual (Pcs)',
                        data: data.values || [],
                        backgroundColor: NAVY,
                        hoverBackgroundColor: '#1a0a6b',
                        borderRadius: 6,
                        maxBarThickness: 34,
                    }]
                };

                if (chart) {
                    chart.data = cfgData;
                    chart.update();
                    return;
                }

                chart = new Chart(canvas, {
                    type: 'bar',
                    data: cfgData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: NAVY,
                                padding: 10,
                                cornerRadius: 8,
                                titleFont: { size: 12 },
                                bodyFont: { size: 12, weight: 'bold' },
                                callbacks: {
                                    label: (ctx) => ` ${formatNumber(ctx.parsed.y)} Pcs`
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 10 }, color: '#9CA3AF', maxRotation: 0, autoSkip: true, maxTicksLimit: 12 }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: '#F3F4F6' },
                                ticks: { font: { size: 10 }, color: '#9CA3AF', precision: 0 }
                            }
                        }
                    }
                });
            }

            function setActiveButton(btn) {
                rangeBtns.forEach(b => {
                    b.classList.remove('bg-[#0F034D]', 'text-white', 'shadow-sm');
                    b.classList.add('text-gray-500', 'hover:text-gray-800');
                });
                if (btn) {
                    btn.classList.add('bg-[#0F034D]', 'text-white', 'shadow-sm');
                    btn.classList.remove('text-gray-500', 'hover:text-gray-800');
                }
            }

            async function fetchTrend(params, activeBtn) {
                loadingEl.classList.remove('hidden');
                setActiveButton(activeBtn);
                try {
                    const url = new URL(endpoint, window.location.origin);
                    Object.entries(params).forEach(([k, v]) => v && url.searchParams.set(k, v));
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) throw new Error('Gagal memuat data');
                    const data = await res.json();
                    renderChart(data);
                } catch (e) {
                    console.error(e);
                } finally {
                    loadingEl.classList.add('hidden');
                }
            }

            // ---- Dropdown rentang tanggal ----
            function openDropdown() {
                dateDropdown.classList.remove('hidden');
                requestAnimationFrame(() => {
                    dateDropdown.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
                    dateDropdown.classList.add('opacity-100', 'scale-100');
                });
                dateArrow.classList.add('rotate-180');
            }

            function closeDropdown() {
                dateDropdown.classList.remove('opacity-100', 'scale-100');
                dateDropdown.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                dateArrow.classList.remove('rotate-180');
                setTimeout(() => dateDropdown.classList.add('hidden'), 150);
            }

            function isDropdownOpen() {
                return !dateDropdown.classList.contains('hidden');
            }

            function formatDateLabel(value) {
                const [y, m, d] = value.split('-');
                const bulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                return `${parseInt(d)} ${bulan[parseInt(m) - 1]} ${y}`;
            }

            function markCustomActive(start, end) {
                dateLabel.textContent = `${formatDateLabel(start)} — ${formatDateLabel(end)}`;
                dateToggle.classList.remove('border-gray-200', 'text-gray-600', 'hover:bg-gray-50');
                dateToggle.classList.add('border-[#0F034D]', 'text-[#0F034D]');
                setActiveButton(null);
            }

            function resetCustomLabel() {
                dateLabel.textContent = 'Rentang Tanggal';
                dateToggle.classList.add('border-gray-200', 'text-gray-600', 'hover:bg-gray-50');
                dateToggle.classList.remove('border-[#0F034D]', 'text-[#0F034D]');
            }

            dateToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                isDropdownOpen() ? closeDropdown() : openDropdown();
            });

            document.addEventListener('click', (e) => {
                if (!e.target.closest('[data-date-dropdown]')) closeDropdown();
            });

            rangeBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    startInput.value = '';
                    endInput.value = '';
                    resetCustomLabel();
                    fetchTrend({ range: btn.dataset.range }, btn);
                });
            });

            applyBtn.addEventListener('click', () => {
                if (!startInput.value || !endInput.value) {
                    alert('Silakan isi tanggal awal dan tanggal akhir.');
                    return;
                }
                if (startInput.value > endInput.value) {
                    alert('Tanggal awal tidak boleh melebihi tanggal akhir.');
                    return;
                }
                markCustomActive(startInput.value, endInput.value);
                closeDropdown();
                fetchTrend({ start: startInput.value, end: endInput.value }, null);
            });

            resetBtn.addEventListener('click', () => {
                startInput.value = '';
                endInput.value = '';
                resetCustomLabel();
                closeDropdown();
                const defaultBtn = document.querySelector('.range-btn[data-range="30d"]');
                fetchTrend({ range: '30d' }, defaultBtn);
            });

            // Render awal (default 1 Bulan / 30d dari server).
            renderChart(initial);
        })();
    </script>
</x-layouts.owner>
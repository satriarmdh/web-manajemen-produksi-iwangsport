<x-layouts.admin>
    <x-slot:breadcrumb>
        <li class="flex items-center text-[#0F034D] font-semibold gap-1.5">
            <svg class="w-4 h-4 shrink-0 text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            Dashboard
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Dashboard Admin
    </x-slot:header>

    @php
        $statusColors = [
            'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
            'disetujui' => 'bg-blue-50 text-blue-700 border-blue-100',
            'dalam_produksi' => 'bg-purple-50 text-purple-700 border-purple-100',
            'selesai' => 'bg-green-50 text-green-700 border-green-100',
            'ditolak' => 'bg-red-50 text-red-700 border-red-100',
        ];
        $statusLabels = [
            'pending' => 'Pending',
            'disetujui' => 'Disetujui',
            'dalam_produksi' => 'Dalam Produksi',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ];
    @endphp

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Card 1: WO Aktif -->
        <a href="{{ route('admin.perintah-produksi.index', ['status' => 'dalam_produksi']) }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between gap-3 min-h-[92px] hover:border-purple-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider truncate">Perintah Produksi Aktif</p>
                <h3 class="text-2xl font-bold text-gray-950 mt-1 leading-none tabular-nums">{{ $stats['active_wo'] }} <span class="text-xs font-normal text-gray-400">WO</span></h3>
                <span class="text-[11px] text-gray-400 mt-1.5 flex items-center gap-1 font-medium group-hover:text-purple-600 transition-colors">
                    Tahap pengerjaan produksi
                    <svg class="w-3 h-3 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </span>
            </div>
            <div class="w-11 h-11 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-purple-100 group-hover:scale-105 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
        </a>

        <!-- Card 2: Stok Menipis/Habis -->
        <a href="{{ route('admin.bahan-baku.index', ['stok' => 'menipis']) }}" class="bg-white p-5 rounded-2xl shadow-sm border {{ $stats['low_stock'] > 0 ? 'border-amber-200 bg-amber-50/10' : 'border-gray-100' }} flex items-center justify-between gap-3 min-h-[92px] hover:border-amber-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider truncate">Kritis / Stok Menipis</p>
                <h3 class="text-2xl font-bold text-gray-950 mt-1 leading-none tabular-nums">{{ $stats['low_stock'] }} <span class="text-xs font-normal text-gray-400">item</span></h3>
                <span class="text-[11px] {{ $stats['low_stock'] > 0 ? 'text-amber-600' : 'text-gray-400' }} mt-1.5 flex items-center gap-1 font-medium group-hover:underline">
                    Bahan &amp; produk hampir habis
                    <svg class="w-3 h-3 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </span>
            </div>
            <div class="w-11 h-11 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-amber-100 group-hover:scale-105 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </a>

        <!-- Card 3: Supplier & Pelanggan -->
        <a href="{{ route('admin.supplier.index') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between gap-3 min-h-[92px] hover:border-blue-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider truncate">Supplier &amp; Pelanggan</p>
                <h3 class="text-2xl font-bold text-gray-950 mt-1 leading-none tabular-nums">{{ $stats['partners'] }} <span class="text-xs font-normal text-gray-400">mitra</span></h3>
                <span class="text-[11px] text-gray-400 mt-1.5 flex items-center gap-1 font-medium group-hover:text-blue-600 transition-colors">
                    Kelola data partner bisnis
                    <svg class="w-3 h-3 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </span>
            </div>
            <div class="w-11 h-11 bg-blue-50 text-[#0F034D] rounded-xl flex items-center justify-center shrink-0 group-hover:bg-blue-100 group-hover:scale-105 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </a>

        <!-- Card 4: Transaksi Hari Ini -->
        <a href="{{ route('admin.pergerakan-stok.index') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between gap-3 min-h-[92px] hover:border-green-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider truncate">Transaksi Hari Ini</p>
                <h3 class="text-2xl font-bold text-gray-950 mt-1 leading-none tabular-nums">{{ $stats['today_transactions'] }} <span class="text-xs font-normal text-gray-400">trx</span></h3>
                <span class="text-[11px] text-gray-400 mt-1.5 flex items-center gap-1 font-medium group-hover:text-green-600 transition-colors">
                    Stok &amp; Penjualan baru
                    <svg class="w-3 h-3 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </span>
            </div>
            <div class="w-11 h-11 bg-green-50 text-green-600 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-green-100 group-hover:scale-105 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
        </a>
    </div>

    <!-- Main Content Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <!-- Kolom Kiri: WO Terbaru & Penjualan Terbaru -->
        <div class="lg:col-span-2 space-y-6">
            <!-- 1. Perintah Produksi Terbaru -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Perintah Produksi Terbaru</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Daftar Surat Perintah Kerja (WO) yang baru saja diterbitkan.</p>
                    </div>
                    <a href="{{ route('admin.perintah-produksi.index') }}" class="text-xs font-bold text-[#0F034D] hover:underline flex items-center gap-1">
                        Lihat Semua
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                @if($recentWo->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[10px] font-bold text-gray-400 uppercase border-b border-gray-50 tracking-wider">
                                    <th class="pb-3 pr-2">Nomor WO</th>
                                    <th class="pb-3 px-2">Tanggal Mulai</th>
                                    <th class="pb-3 px-2">Pembuat</th>
                                    <th class="pb-3 px-2 text-center">Status</th>
                                    <th class="pb-3 pl-2 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($recentWo as $wo)
                                    <tr class="hover:bg-gray-50/30 transition-colors text-sm">
                                        <td class="py-3.5 pr-2 font-semibold text-gray-900 font-mono">{{ $wo->nomor_wo }}</td>
                                        <td class="py-3.5 px-2 text-gray-600">{{ \Carbon\Carbon::parse($wo->tgl_mulai)->format('d M Y') }}</td>
                                        <td class="py-3.5 px-2 text-gray-500">{{ $wo->user->name ?? 'Admin' }}</td>
                                        <td class="py-3.5 px-2 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $statusColors[$wo->status_produksi] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                                {{ $statusLabels[$wo->status_produksi] ?? $wo->status_produksi }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 pl-2 text-right">
                                            <a href="{{ route('admin.perintah-produksi.show', $wo->id) }}" class="inline-flex items-center justify-center p-1.5 text-gray-400 hover:text-[#0F034D] hover:bg-gray-100 rounded-lg transition-all" title="Detail Perintah Produksi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-400 text-sm">
                        Belum ada data perintah produksi terdaftar.
                    </div>
                @endif
            </div>

            <!-- 2. Pergerakan Stok Terbaru -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Pergerakan Stok Terbaru</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Daftar mutasi masuk dan keluar bahan baku terbaru.</p>
                    </div>
                    <a href="{{ route('admin.pergerakan-stok.index') }}" class="text-xs font-bold text-[#0F034D] hover:underline flex items-center gap-1">
                        Lihat Semua
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                @if($recentStockMovements->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[10px] font-bold text-gray-400 uppercase border-b border-gray-50 tracking-wider">
                                    <th class="pb-3 pr-2">No. Transaksi</th>
                                    <th class="pb-3 px-2">Tanggal</th>
                                    <th class="pb-3 px-2 text-center">Tipe</th>
                                    <th class="pb-3 px-2">Partner/Penerima</th>
                                    <th class="pb-3 px-2 text-right">Jumlah Item</th>
                                    <th class="pb-3 pl-2 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($recentStockMovements as $move)
                                    <tr class="hover:bg-gray-50/30 transition-colors text-sm">
                                        <td class="py-3.5 pr-2 font-semibold text-gray-900 font-mono">{{ $move->nomor_transaksi }}</td>
                                        <td class="py-3.5 px-2 text-gray-600">{{ $move->tanggal->format('d M Y') }}</td>
                                        <td class="py-3.5 px-2 text-center">
                                            @if($move->jenis_pergerakan === 'masuk')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border bg-green-50 text-green-700 border-green-100">Masuk</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border bg-red-50 text-red-700 border-red-100">Keluar</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-2 text-gray-500">
                                            {{ $move->supplier->nama_supplier ?? ($move->penerima ?: '-') }}
                                        </td>
                                        <td class="py-3.5 px-2 text-right font-semibold text-gray-900">
                                            {{ $move->detailPergerakanStok->count() }} item
                                        </td>
                                        <td class="py-3.5 pl-2 text-right">
                                            <a href="{{ route('admin.pergerakan-stok.show', $move->id) }}" class="inline-flex items-center justify-center p-1.5 text-gray-400 hover:text-[#0F034D] hover:bg-gray-100 rounded-lg transition-all" title="Detail Pergerakan Stok">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-400 text-sm">
                        Belum ada data pergerakan stok terdaftar.
                    </div>
                @endif
            </div>

            <!-- 3. Transaksi Penjualan Terbaru -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Transaksi Penjualan Terbaru</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Daftar nota penjualan/invoice pesanan yang baru diselesaikan.</p>
                    </div>
                    <a href="{{ route('admin.penjualan.index') }}" class="text-xs font-bold text-[#0F034D] hover:underline flex items-center gap-1">
                        Lihat Semua
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                @if($recentSales->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[10px] font-bold text-gray-400 uppercase border-b border-gray-50 tracking-wider">
                                    <th class="pb-3 pr-2">No. Invoice</th>
                                    <th class="pb-3 px-2">Tanggal</th>
                                    <th class="pb-3 px-2">Pelanggan</th>
                                    <th class="pb-3 px-2 text-right">Total Bayar</th>
                                    <th class="pb-3 pl-2 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($recentSales as $sale)
                                    <tr class="hover:bg-gray-50/30 transition-colors text-sm">
                                        <td class="py-3.5 pr-2 font-semibold text-gray-900 font-mono">{{ $sale->nomor_invoice }}</td>
                                        <td class="py-3.5 px-2 text-gray-600">{{ \Carbon\Carbon::parse($sale->tanggal)->format('d M Y') }}</td>
                                        <td class="py-3.5 px-2 text-gray-500">{{ $sale->pelanggan->nama_pelanggan ?? 'Umum' }}</td>
                                        <td class="py-3.5 px-2 text-right font-semibold text-green-600 font-mono">Rp {{ number_format($sale->total_bayar, 0, ',', '.') }}</td>
                                        <td class="py-3.5 pl-2 text-right">
                                            <a href="{{ route('admin.penjualan.show', $sale->id) }}" class="inline-flex items-center justify-center p-1.5 text-gray-400 hover:text-[#0F034D] hover:bg-gray-100 rounded-lg transition-all" title="Detail Penjualan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-400 text-sm">
                        Belum ada data penjualan tercatat.
                    </div>
                @endif
            </div>
        </div>

        <!-- Kolom Kanan: Low Stock Alerts & Quick Actions -->
        <div class="space-y-6">
            <!-- 1. Low Stock Alerts -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4 pb-4 border-b border-gray-50 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Stok Hampir Habis
                </h3>

                @if($lowStockAlerts->count() > 0)
                    <div class="space-y-3.5">
                        @foreach($lowStockAlerts as $item)
                            <div class="flex items-center justify-between gap-3 p-3 rounded-xl border {{ $item->stok == 0 ? 'border-red-100 bg-red-50/20' : 'border-amber-100 bg-amber-50/20' }} transition-all">
                                <div class="min-w-0">
                                    <h4 class="font-bold text-gray-900 text-xs truncate">{{ $item->nama }}</h4>
                                    <span class="inline-block text-[10px] font-semibold text-gray-400 mt-1 uppercase tracking-wider">{{ $item->tipe }}</span>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-sm font-bold {{ $item->stok == 0 ? 'text-red-600 font-extrabold' : 'text-amber-600' }}">{{ number_format($item->stok, 0, ',', '.') }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5 lowercase font-medium">{{ $item->satuan }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-gray-400 text-xs flex flex-col items-center justify-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-green-50 text-green-500 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        Aman! Semua persediaan dalam kondisi normal.
                    </div>
                @endif
            </div>

            <!-- 2. Quick Actions -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-4 flex items-center gap-2 relative z-10">
                    <svg class="w-4 h-4 text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    Aksi Cepat Admin
                </h3>

                <div class="space-y-3 relative z-10">
                    <a href="{{ route('admin.perintah-produksi.create') }}" class="w-full flex items-center justify-between p-3.5 bg-gray-50 hover:bg-[#0F034D]/5 border border-gray-100 hover:border-[#0F034D]/25 rounded-xl transition-all group font-semibold text-xs text-gray-700 hover:text-[#0F034D]">
                        <span>Buat Perintah Produksi (WO)</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D] -translate-x-1 group-hover:translate-x-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                    <a href="{{ route('admin.pergerakan-stok.create', ['tab' => 'masuk']) }}" class="w-full flex items-center justify-between p-3.5 bg-gray-50 hover:bg-[#0F034D]/5 border border-gray-100 hover:border-[#0F034D]/25 rounded-xl transition-all group font-semibold text-xs text-gray-700 hover:text-[#0F034D]">
                        <span>Catat Transaksi Stok Masuk</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D] -translate-x-1 group-hover:translate-x-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                    <a href="{{ route('admin.pergerakan-stok.create', ['tab' => 'keluar']) }}" class="w-full flex items-center justify-between p-3.5 bg-gray-50 hover:bg-[#0F034D]/5 border border-gray-100 hover:border-[#0F034D]/25 rounded-xl transition-all group font-semibold text-xs text-gray-700 hover:text-[#0F034D]">
                        <span>Catat Transaksi Stok Keluar</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D] -translate-x-1 group-hover:translate-x-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                    <a href="{{ route('admin.penjualan.create') }}" class="w-full flex items-center justify-between p-3.5 bg-gray-50 hover:bg-[#0F034D]/5 border border-gray-100 hover:border-[#0F034D]/25 rounded-xl transition-all group font-semibold text-xs text-gray-700 hover:text-[#0F034D]">
                        <span>Catat Transaksi Penjualan Baru</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0F034D] -translate-x-1 group-hover:translate-x-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
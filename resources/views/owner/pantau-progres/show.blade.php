<x-layouts.owner>
    <x-slot:breadcrumb>
        <li class="flex items-center gap-1.5 text-gray-400">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <span class="select-none">Produksi &amp; Persetujuan</span>
        </li>
        <li class="flex items-center">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <a href="{{ route('owner.pantau-progres.index') }}" class="text-gray-400 hover:text-[#0F034D] transition-colors">Pantau Progres Produksi</a>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            {{ $perintahProduksi->nomor_wo }}
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Detail Progres Produksi
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
        $totalRoll = $perintahProduksi->details->sum('qty_roll_pakai');
        $totalEstimasi = $perintahProduksi->details->sum('estimasi_pcs');
        $totalPotong = $perintahProduksi->details->sum('qty_pcs_potong');
    @endphp

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex flex-col lg:flex-row lg:items-start justify-between gap-4 rounded-t-xl">
            <div class="flex items-start gap-3">
                <a href="{{ route('owner.pantau-progres.index') }}" class="inline-flex items-center justify-center w-9 h-9 hover:bg-gray-50 rounded-xl text-gray-500 hover:text-[#0F034D] transition-colors border border-gray-200 shrink-0 mt-0.5" title="Kembali ke Daftar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <h3 class="text-lg font-bold text-[#0F034D]">{{ $perintahProduksi->nomor_wo }}</h3>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusColors[$perintahProduksi->status_produksi] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                            {{ $statusLabels[$perintahProduksi->status_produksi] ?? $perintahProduksi->status_produksi }}
                        </span>
                        @php $dlInfo = $perintahProduksi->getDeadlineInfo(); @endphp
                        @if($dlInfo['statusType'] !== 'none' && $dlInfo['statusType'] !== 'normal')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $dlInfo['badgeClass'] }}">
                                {{ $dlInfo['label'] }}
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 mt-1">
                        Dibuat oleh <span class="font-semibold text-[#0F034D]">{{ $perintahProduksi->user->name ?? '-' }}</span>
                        pada {{ $perintahProduksi->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 mb-1">Tanggal Mulai</p>
                    <p class="font-bold text-sm text-[#0F034D]">{{ \Carbon\Carbon::parse($perintahProduksi->tgl_mulai)->format('d M Y') }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 mb-1">Tanggal Selesai</p>
                    <p class="font-bold text-sm text-[#0F034D]">{{ $perintahProduksi->tgl_selesai ? \Carbon\Carbon::parse($perintahProduksi->tgl_selesai)->format('d M Y') : '-' }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 mb-1">Total Produk</p>
                    <p class="font-bold text-sm text-[#0F034D]">{{ $perintahProduksi->details->count() }} item</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 mb-1">Total Roll</p>
                    <p class="font-bold text-sm text-[#0F034D]">{{ number_format($totalRoll, 0, ',', '.') }} roll</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 mb-1">Estimasi PCS</p>
                    <p class="font-bold text-sm text-[#0F034D]">{{ number_format($totalEstimasi, 0, ',', '.') }} pcs</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-[#0F034D]/5 rounded-xl p-4 border border-[#0F034D]/10">
                    <p class="text-xs text-gray-500 mb-1">Disetujui Oleh</p>
                    <p class="font-semibold text-sm text-[#0F034D]">{{ $perintahProduksi->approver->name ?? '-' }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $perintahProduksi->approved_at ? $perintahProduksi->approved_at->format('d M Y, H:i') : 'Menunggu persetujuan owner' }}</p>
                </div>
                @php
                    $isWOFinished = $perintahProduksi->status_produksi === 'selesai';
                    $hasWOFlag = $isWOFinished && $perintahProduksi->details->contains(function($d) {
                        return ((int)$d->total_qty_diterima + (int)$d->total_qty_cacat_diterima) < ((int)$d->estimasi_pcs - (int)$d->toleransi_minus);
                    });
                    
                    $progAdminBg = 'bg-blue-50 border-blue-100';
                    $progAdminTextClass = 'text-blue-700';
                    $progAdminLabel = $statusLabels[$perintahProduksi->status_produksi] ?? $perintahProduksi->status_produksi;

                    if ($isWOFinished) {
                        if ($hasWOFlag) {
                            $progAdminBg = 'bg-amber-50 border-amber-200';
                            $progAdminTextClass = 'text-amber-800 font-bold';
                            $progAdminLabel = 'Selesai - Flag/Selisih';
                        } else {
                            $progAdminBg = 'bg-green-50 border-green-200';
                            $progAdminTextClass = 'text-green-800 font-bold';
                            $progAdminLabel = 'Selesai';
                        }
                    }
                @endphp
                <div class="{{ $progAdminBg }} rounded-xl p-4 border">
                    <p class="text-xs text-gray-500 mb-1">Status Progres</p>
                    <p class="font-semibold text-sm {{ $progAdminTextClass }}">{{ $progAdminLabel }}</p>
                    <p class="text-xs text-gray-400 mt-1">Status administrasi WO saat ini</p>
                </div>
            </div>
        </div>
    </div>

    <!-- detail produk and tahapan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 pt-6 pb-4 mb-4">
        <h3 class="text-lg font-bold text-[#0F034D]">Detail Produk &amp; Tahapan Produksi</h3>
        <p class="text-sm text-gray-500 mt-1">Pantau estimasi, hasil potong, validasi, dan posisi proses pengerjaan setiap produk.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-4">
        <!-- Sidebar Produk -->
        <aside class="self-start">
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden max-h-[calc(100vh-8rem)] flex flex-col">
                <div class="p-3 border-b border-gray-100">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-gray-400 mb-2">Daftar Produk</p>
                    <div class="relative">
                        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" id="productSearch" data-product-search placeholder="Cari produk..." class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg bg-gray-50 border border-gray-100 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0F034D]/20 focus:border-[#0F034D]/30 transition-all">
                    </div>
                </div>
                <div class="p-2 flex lg:flex-col gap-2 overflow-x-auto lg:overflow-y-auto lg:overflow-x-visible pb-3 min-h-0">
                    @foreach($perintahProduksi->details as $detail)
                        @php
                            $validasiColors = [
                                'pending' => 'bg-gray-50 text-gray-600 border-gray-100',
                                'normal' => 'bg-green-50 text-green-700 border-green-100',
                                'flag' => 'bg-red-50 text-red-700 border-red-100',
                            ];
                            $validasiLabel = [
                                'pending' => 'Pending',
                                'normal' => 'Normal',
                                'flag' => 'Flag',
                            ];
                            $penerimaanColors = [
                                'belum_diterima' => 'bg-gray-50 text-gray-600 border-gray-100',
                                'sebagian' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                                'sesuai' => 'bg-green-50 text-green-700 border-green-100',
                                'selisih_kurang' => 'bg-red-50 text-red-700 border-red-100',
                                'selisih_lebih' => 'bg-orange-50 text-orange-700 border-orange-100',
                            ];
                            $penerimaanLabel = [
                                'belum_diterima' => 'Belum Diterima',
                                'sebagian' => 'Sebagian',
                                'sesuai' => 'Sesuai',
                                'selisih_kurang' => 'Kurang',
                                'selisih_lebih' => 'Lebih',
                            ];
                            $warnaProduk = strtolower(trim($detail->produk->warna ?? '-'));
                            $warnaDotMap = [
                                'hitam' => '#000000',
                                'putih' => '#FFFFFF',
                                'abu-abu' => '#9CA3AF',
                                'abu' => '#9CA3AF',
                                'navy' => '#061952',
                                'silver' => '#C0C0C0',
                                'biru' => '#2563EB',
                            ];
                            $warnaDot = $warnaDotMap[$warnaProduk] ?? (
                                str_contains($warnaProduk, 'navy') ? '#061952' : (
                                str_contains($warnaProduk, 'biru') ? '#2563EB' : (
                                str_contains($warnaProduk, 'hitam') ? '#000000' : (
                                str_contains($warnaProduk, 'putih') ? '#FFFFFF' : (
                                str_contains($warnaProduk, 'silver') ? '#C0C0C0' : (
                                str_contains($warnaProduk, 'abu') ? '#9CA3AF' : '#CBD5E1'
                                )))))
                            );
                            $needsStroke = in_array($warnaProduk, ['abu-abu', 'abu', 'putih', 'silver'], true) || str_contains($warnaProduk, 'putih');
                        @endphp
                        <button type="button" data-product-selector="{{ $detail->id }}" data-product-name="{{ strtolower($detail->produk->nama_produk ?? '') }}" class="product-list-item shrink-0 lg:shrink w-64 lg:w-full text-left p-3 rounded-xl border transition-all duration-200 {{ $loop->first ? 'bg-[#0F034D]/5 border-[#0F034D] shadow-sm' : 'bg-white border-gray-300 hover:bg-gray-50 hover:border-gray-400 hover:-translate-y-0.5 hover:shadow-md' }}">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-block w-3 h-3 rounded-full shrink-0 {{ $needsStroke ? 'ring-1 ring-gray-300' : '' }}" style="background-color: {{ $warnaDot }}"></span>
                                <p class="text-xs font-bold text-[#0F034D] truncate flex-1 min-w-0">{{ $detail->produk->nama_produk ?? '-' }}</p>
                            </div>
                            <p class="text-[11px] text-gray-500 mb-2">{{ ucfirst($detail->produk->warna ?? '-') }}</p>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[9px] px-2 py-0.5 rounded-full border font-semibold {{ $validasiColors[$detail->status_validasi_potong] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                    {{ $validasiLabel[$detail->status_validasi_potong] ?? ucfirst($detail->status_validasi_potong) }}
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded-full border font-semibold {{ $penerimaanColors[$detail->status_penerimaan] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                    {{ $penerimaanLabel[$detail->status_penerimaan] ?? ucfirst($detail->status_penerimaan) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100">
                                <span class="text-[10px] text-gray-400">Estimasi</span>
                                <span class="text-xs font-bold text-[#0F034D]">{{ number_format($detail->estimasi_pcs, 0, ',', '.') }} pcs</span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </aside>

        <!-- Detail Panel -->
        <div>
            @foreach($perintahProduksi->details as $detail)
                @php
                    $warnaProduk = strtolower(trim($detail->produk->warna ?? '-'));
                    $warnaDotMap = [
                        'hitam' => '#000000',
                        'putih' => '#FFFFFF',
                        'abu-abu' => '#9CA3AF',
                        'abu' => '#9CA3AF',
                        'navy' => '#061952',
                        'silver' => '#C0C0C0',
                        'biru' => '#2563EB',
                    ];
                    $warnaDot = $warnaDotMap[$warnaProduk] ?? (
                        str_contains($warnaProduk, 'navy') ? '#061952' : (
                        str_contains($warnaProduk, 'biru') ? '#2563EB' : (
                        str_contains($warnaProduk, 'hitam') ? '#000000' : (
                        str_contains($warnaProduk, 'putih') ? '#FFFFFF' : (
                        str_contains($warnaProduk, 'silver') ? '#C0C0C0' : (
                        str_contains($warnaProduk, 'abu') ? '#9CA3AF' : '#CBD5E1'
                        )))))
                    );
                    $needsStroke = in_array($warnaProduk, ['abu-abu', 'abu', 'putih', 'silver'], true) || str_contains($warnaProduk, 'putih');
                    $statusDescriptions = [
                        'pending' => 'Belum ada hasil potong yang divalidasi.',
                        'normal' => 'Hasil potong memenuhi batas toleransi.',
                        'flag' => 'Hasil potong di bawah batas toleransi dan memiliki selisih tukang potong.',
                    ];
                    $statusColors = [
                        'pending' => 'bg-gray-50 text-gray-600 border-gray-200',
                        'normal' => 'bg-green-50 text-green-700 border-green-200',
                        'flag' => 'bg-red-50 text-red-700 border-red-200',
                    ];
                    $penerimaanDescriptions = [
                        'belum_diterima' => 'Belum ada hasil produksi yang diterima admin.',
                        'sebagian' => 'Sebagian hasil produksi sudah diterima admin.',
                        'sesuai' => 'Jumlah hasil diterima sesuai estimasi produksi.',
                        'selisih_kurang' => 'Jumlah hasil diterima lebih sedikit dari estimasi.',
                        'selisih_lebih' => 'Jumlah hasil diterima lebih banyak dari estimasi.',
                    ];
                    $penerimaanColors = [
                        'belum_diterima' => 'bg-gray-50 text-gray-600 border-gray-200',
                        'sebagian' => 'bg-amber-50 text-amber-800 border-amber-200',
                        'sesuai' => 'bg-green-50 text-green-700 border-green-200',
                        'selisih_kurang' => 'bg-red-50 text-red-700 border-red-200',
                        'selisih_lebih' => 'bg-orange-50 text-orange-700 border-orange-200',
                    ];
                @endphp
                <div data-product-panel="{{ $detail->id }}" class="{{ $loop->first ? '' : 'hidden' }}">
                    <div class="bg-white rounded-xl border border-gray-300 overflow-hidden max-h-[calc(100vh-8rem)] flex flex-col">
                        <div class="p-3 border-b border-gray-200 shrink-0">
                            <p class="text-[11px] font-bold uppercase tracking-wide text-gray-400">Detail Produk Terpilih</p>
                            <p class="text-xs text-gray-500 mt-0.5">Informasi detail progres pergerakan produk</p>
                        </div>
                        <div class="p-4 space-y-4 overflow-y-auto min-h-0">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div class="rounded-lg border px-3 py-2 {{ $statusColors[$detail->status_validasi_potong] ?? $statusColors['pending'] }}">
                                    <p class="text-[10px] font-bold uppercase tracking-wide">Status Produksi: {{ ucfirst($detail->status_validasi_potong ?? 'pending') }}</p>
                                    <p class="text-[11px] mt-0.5 leading-relaxed">{{ $statusDescriptions[$detail->status_validasi_potong] ?? $statusDescriptions['pending'] }}</p>
                                </div>
                                @php
                                    $isWOFinished = $perintahProduksi->status_produksi === 'selesai';
                                    $totalDiterimaAkumulasi = (int) $detail->total_qty_diterima + (int) $detail->total_qty_cacat_diterima;
                                    $batasMinNormal = (int) $detail->estimasi_pcs - (int) $detail->toleransi_minus;
                                    $isBelowMin = $isWOFinished && ($totalDiterimaAkumulasi < $batasMinNormal);

                                    $dispPenerimaanClass = $penerimaanColors[$detail->status_penerimaan] ?? $penerimaanColors['belum_diterima'];
                                    $dispPenerimaanTitle = ucfirst(str_replace('_', ' ', $detail->status_penerimaan ?? 'belum_diterima'));
                                    $dispPenerimaanDesc = $penerimaanDescriptions[$detail->status_penerimaan] ?? $penerimaanDescriptions['belum_diterima'];

                                    if ($isWOFinished) {
                                        if ($isBelowMin) {
                                            $dispPenerimaanClass = 'bg-red-50 text-red-800 border-red-200';
                                            $dispPenerimaanTitle = 'Selesai - Flag/Selisih';
                                            $dispPenerimaanDesc = 'Perintah selesai. Hasil diterima di bawah batas toleransi normal.';
                                        } else {
                                            $dispPenerimaanClass = 'bg-green-50 text-green-800 border-green-200';
                                            $dispPenerimaanTitle = 'Selesai';
                                            $dispPenerimaanDesc = 'Perintah selesai. Hasil diterima memenuhi batas toleransi normal.';
                                        }
                                    }
                                @endphp
                                <div class="rounded-lg border px-3 py-2 {{ $dispPenerimaanClass }}">
                                    <p class="text-[10px] font-bold uppercase tracking-wide">Status Penerimaan: {{ $dispPenerimaanTitle }}</p>
                                    <p class="text-[11px] mt-0.5 leading-relaxed">{{ $dispPenerimaanDesc }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <!-- Card 1: detail produk -->
                                <div class="rounded-xl border border-gray-300 overflow-hidden">
                                    <div class="px-5 py-4 border-b border-gray-200">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="inline-flex items-center gap-2 text-sm font-bold text-[#0F034D] min-w-0">
                                                <span class="truncate">{{ $detail->produk->nama_produk ?? '-' }} - {{ ucfirst($detail->produk->warna ?? '-') }}</span>
                                                <span class="inline-block w-3 h-3 rounded-full shrink-0 {{ $needsStroke ? 'ring-1 ring-gray-300' : '' }}" style="background-color: {{ $warnaDot }}"></span>
                                            </h4>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Informasi dasar produk dalam perintah produksi</p>
                                    </div>
                                    <div class="p-5 space-y-3">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="rounded-lg bg-gray-50 px-3 py-2 col-span-2">
                                                <p class="text-[10px] text-gray-400 mb-0.5">Bahan Baku Kain</p>
                                                <p class="text-xs font-semibold text-[#0F034D] truncate">{{ $detail->bahanBaku->nama_bahan ?? '-' }}</p>
                                            </div>
                                            <div class="rounded-lg bg-gray-50 px-3 py-2">
                                                <p class="text-[10px] text-gray-400 mb-0.5">Qty Roll Pakai</p>
                                                <p class="text-sm font-bold text-[#0F034D]">{{ number_format($detail->qty_roll_pakai) }} Roll</p>
                                            </div>
                                            <div class="rounded-lg bg-gray-50 px-3 py-2">
                                                <p class="text-[10px] text-gray-400 mb-0.5">Toleransi -</p>
                                                <p class="text-sm font-bold text-[#0F034D]">{{ number_format($detail->toleransi_minus ?? 0) }} Pcs</p>
                                            </div>
                                        </div>

                                        @php
                                            if ($isWOFinished) {
                                                if ($totalDiterimaAkumulasi < $batasMinNormal) {
                                                    $selisihNet = $totalDiterimaAkumulasi - $batasMinNormal;
                                                } elseif ($totalDiterimaAkumulasi > (int) $detail->estimasi_pcs) {
                                                    $selisihNet = $totalDiterimaAkumulasi - (int) $detail->estimasi_pcs;
                                                } else {
                                                    $selisihNet = 0;
                                                }
                                            } else {
                                                $selisihNet = 0;
                                            }
                                        @endphp

                                        {{-- Box Ringkasan Realisasi & Selisih --}}
                                        <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-3">
                                            <div class="flex items-center justify-between gap-2 mb-2 pb-1.5 border-b border-gray-200">
                                                <span class="text-[11px] font-bold text-[#0F034D] uppercase tracking-wide">Ringkasan Realisasi & Selisih</span>
                                                @if($isWOFinished)
                                                    @if($isBelowMin)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-red-100 text-red-800 border border-red-300">
                                                            Flag/Selisih
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-green-100 text-green-800 border border-green-300">
                                                            Sesuai
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>

                                            <div class="grid grid-cols-2 gap-2 text-left">
                                                <div class="bg-white rounded-lg p-2 border border-gray-200">
                                                    <p class="text-[10px] text-gray-400 font-medium">Estimasi Target</p>
                                                    <p class="text-xs font-bold text-[#0F034D] mt-0.5">{{ number_format($detail->estimasi_pcs, 0, ',', '.') }} <span class="text-[10px] font-normal text-gray-500">pcs</span></p>
                                                    <p class="text-[9px] text-gray-400">Min: {{ number_format($batasMinNormal, 0, ',', '.') }} pcs</p>
                                                </div>
                                                <div class="bg-white rounded-lg p-2 border border-gray-200">
                                                    <p class="text-[10px] text-gray-400 font-medium">Total Diterima</p>
                                                    <p class="text-xs font-bold text-green-700 mt-0.5">{{ number_format($totalDiterimaAkumulasi, 0, ',', '.') }} <span class="text-[10px] font-normal text-gray-500">pcs</span></p>
                                                    <p class="text-[9px] text-gray-500">({{ number_format($detail->total_qty_diterima, 0, ',', '.') }} baik, {{ number_format($detail->total_qty_cacat_diterima, 0, ',', '.') }} cacat)</p>
                                                </div>
                                                <div class="bg-white rounded-lg p-2 border border-gray-200">
                                                    <p class="text-[10px] text-gray-400 font-medium">Selisih</p>
                                                    <p class="text-xs font-bold {{ $isWOFinished ? ($selisihNet < 0 ? 'text-red-600' : ($selisihNet > 0 ? 'text-blue-600' : 'text-green-700')) : 'text-green-700' }} mt-0.5">
                                                        {{ ($isWOFinished && $selisihNet > 0) ? '+' : '' }}{{ number_format($selisihNet, 0, ',', '.') }} <span class="text-[10px] font-normal text-gray-500">pcs</span>
                                                    </p>
                                                </div>
                                                <div class="bg-white rounded-lg p-2 border border-gray-200">
                                                    <p class="text-[10px] text-gray-400 font-medium">Status Toleransi</p>
                                                    <p class="text-xs font-bold {{ $isWOFinished ? ($isBelowMin ? 'text-red-600' : 'text-green-700') : 'text-gray-600' }} mt-0.5">
                                                        {{ $isWOFinished ? ($isBelowMin ? 'Di Bawah Min' : 'Normal / Aman') : 'Dalam Proses' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 2: Stepper tahapan -->
                                <div class="rounded-xl border border-gray-300 overflow-hidden">
                                    <div class="px-5 py-4 border-b border-gray-200">
                                        <h4 class="text-sm font-bold text-[#0F034D]">Tahapan Produksi</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">Alur status produksi real-time</p>
                                    </div>
                                    <div class="p-5">
                                        @include('admin.perintah-produksi.partials._stepper')
                                    </div>
                                </div>
                            </div>

                            <!-- Card 3: Tabbed detail distribusi -->
                            <div class="rounded-xl border border-gray-300 overflow-hidden">
                                <div class="px-5 py-3 border-b border-gray-200">
                                    <h4 class="text-sm font-bold text-[#0F034D]">Detail Distribusi Pengerjaan</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">Distribusi stok pengerjaan dan riwayat penerimaan</p>
                                </div>
                                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between gap-3 flex-wrap">
                                    <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-0.5">
                                        <button type="button" data-distribusi-tab="stok" class="px-3 py-1.5 text-xs font-semibold rounded-md bg-[#0F034D] text-white shadow-sm transition-all">
                                            Stok Karyawan
                                        </button>
                                        <button type="button" data-distribusi-tab="riwayat" class="px-3 py-1.5 text-xs font-semibold rounded-md text-gray-500 hover:text-[#0F034D] transition-all">
                                            Riwayat Penerimaan
                                        </button>
                                    </div>
                                </div>

                                <!-- Tab Content: Stok -->
                                <div data-distribusi-content="stok" class="p-4 space-y-2">
                                    @include('admin.perintah-produksi.partials._distribusi-stok')
                                </div>

                                <!-- Tab Content: Riwayat -->
                                <div data-distribusi-content="riwayat" class="p-4 hidden">
                                    @include('admin.perintah-produksi.partials._distribusi-riwayat')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Stok Detail Slide Panel -->
    <div id="stokDetailPanel" class="slide-panel">
        <div class="slide-panel-backdrop" data-close-stok-modal></div>
        <div class="slide-panel-body">
            <!-- Header -->
            <div class="slide-panel-header">
                <div class="slide-panel-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="slide-panel-header-title" id="modal-karyawan-name">-</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Detail Stok Karyawan - <span id="modal-peran" class="font-semibold">-</span></p>
                </div>
                <button type="button" class="slide-panel-close" data-close-stok-modal>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Top-Level Tabs --}}
            <div class="px-5 py-3 border-b border-gray-200 shrink-0 bg-white">
                <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-0.5 max-w-max">
                    <button type="button" data-modal-tab="info" id="modal-tab-btn-info" class="px-3 py-1.5 text-xs font-semibold rounded-md bg-[#0F034D] text-white shadow-sm transition-all">
                        Informasi Stok
                    </button>
                    <button type="button" data-modal-tab="serah-terima" id="modal-tab-btn-serah" class="px-3 py-1.5 text-xs font-semibold rounded-md text-gray-500 hover:text-[#0F034D] transition-all">
                        Riwayat Serah Terima
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="slide-panel-content">
                {{-- Panel 1: Informasi Stok (Default) --}}
                <div id="modal-tab-content-info" class="space-y-5">
                    <div class="grid grid-cols-3 gap-2.5">
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                            <p class="text-[11px] text-gray-500 mb-1 font-medium">Diproses</p>
                            <p class="text-xl font-bold text-gray-700"><span id="modal-qty-hold">0</span> <span class="text-xs">pcs</span></p>
                        </div>
                        <div class="bg-emerald-50 rounded-xl p-3 border border-emerald-100">
                            <p class="text-[11px] text-emerald-600 mb-1 font-medium">Siap Diserahkan</p>
                            <p class="text-xl font-bold text-emerald-700"><span id="modal-ready-qty">0</span> <span class="text-xs">pcs</span></p>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-3 border border-blue-100">
                            <p class="text-[11px] text-blue-600 mb-1 font-medium">Sudah Diserahkan</p>
                            <p class="text-xl font-bold text-blue-700"><span id="modal-total-dikeluarkan">0</span> <span class="text-xs">pcs</span></p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl border border-gray-100 divide-y divide-gray-100">
                        <div class="flex justify-between items-center gap-3 px-4 py-2.5">
                            <span class="text-xs font-bold text-gray-700">Status Barang</span>
                            <span id="modal-status-barang-badge" class="px-2.5 py-1 rounded-full text-[11px] font-semibold shrink-0">-</span>
                        </div>
                        <div class="flex justify-between items-center gap-3 px-4 py-2.5">
                            <div>
                                <span class="text-xs font-bold text-gray-700 block">Total Hasil Selesai</span>
                                <span class="text-[10px] text-gray-400 font-medium block mt-0.5">(Hasil Baik + Barang Cacat)</span>
                            </div>
                            <span class="text-xs font-bold text-gray-700 shrink-0"><span id="modal-total-selesai">0</span> pcs</span>
                        </div>
                        <div class="flex justify-between items-center gap-3 px-4 py-2.5">
                            <span class="text-xs font-bold text-gray-700">Status Pengerjaan</span>
                            <span id="modal-status-pengerjaan" class="text-xs font-semibold text-gray-700 shrink-0">-</span>
                        </div>
                    </div>

                    <div id="recordsSummarySection" class="hidden">
                        <h3 class="text-sm font-bold text-[#0F034D] mb-2">Ringkasan Catatan</h3>
                        <div class="space-y-2.5">
                            {{-- Top Row: Side-by-side Cacat Cards --}}
                            <div class="grid grid-cols-2 gap-2.5">
                                <div class="bg-amber-50 rounded-xl p-3 border border-amber-200">
                                    <p class="text-[11px] text-amber-700 font-semibold">Total Barang Cacat</p>
                                    <p class="text-lg font-bold text-amber-700 mt-1"><span id="summary-total-cacat">0</span> <span class="text-xs">pcs</span></p>
                                    <p class="text-[10px] text-amber-600 mt-0.5"><span id="summary-count-cacat">0</span> laporan</p>
                                </div>
                                <div class="bg-amber-50 rounded-xl p-3 border border-amber-200">
                                    <p class="text-[11px] text-amber-700 font-semibold">Barang Cacat Diserahkan</p>
                                    <p class="text-lg font-bold text-amber-700 mt-1"><span id="summary-cacat-diserahkan">0</span> <span class="text-xs">pcs</span></p>
                                </div>
                            </div>
                            {{-- Bottom Row: Full-width Selisih Card --}}
                            <div class="bg-red-50 rounded-xl p-3 border border-red-200 flex justify-between items-center">
                                <div>
                                    <p class="text-[11px] text-red-700 font-semibold">Total Selisih</p>
                                    <p class="text-lg font-bold text-red-700 mt-1"><span id="summary-total-selisih">0</span> <span class="text-xs">pcs</span></p>
                                </div>
                                <p class="text-[10px] text-red-600 font-medium"><span id="summary-count-selisih">0</span> laporan</p>
                            </div>
                        </div>
                    </div>

                    <div id="recordsListSection" class="hidden">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-bold text-[#0F034D]">Riwayat Catatan</h3>
                            <span class="text-[10px] text-gray-400" id="records-count-label">0 catatan</span>
                        </div>
                        <div class="flex border-b border-gray-100 pb-2 mb-3 mt-2" role="tablist" aria-label="Jenis riwayat catatan">
                            <button type="button" id="records-tab-cacat" data-record-type="cacat" role="tab" aria-selected="true" class="flex-1 text-center pb-1.5 text-xs font-bold border-b-2 border-[#0F034D] text-[#0F034D] transition-all">Barang Cacat</button>
                            <button type="button" id="records-tab-selisih" data-record-type="selisih" role="tab" aria-selected="false" class="flex-1 text-center pb-1.5 text-xs font-semibold border-b-2 border-transparent text-gray-500 hover:text-[#0F034D] transition-all">Selisih</button>
                        </div>
                        <div id="records-list" class="space-y-1.5"></div>
                        <div id="records-pagination" class="flex items-center justify-center gap-1.5 mt-3 hidden">
                            <button type="button" id="records-prev" class="px-2.5 py-1 text-xs rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium transition-colors disabled:opacity-40 disabled:cursor-not-allowed">‹ Sebelumnya</button>
                            <span id="records-page-info" class="text-xs text-gray-500 px-2">1 / 1</span>
                            <button type="button" id="records-next" class="px-2.5 py-1 text-xs rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium transition-colors disabled:opacity-40 disabled:cursor-not-allowed">Berikutnya ›</button>
                        </div>
                        <div id="records-empty" class="hidden text-center py-5">
                            <p class="text-xs text-gray-500">Tidak ada catatan pada riwayat ini</p>
                        </div>
                    </div>
                </div>

                {{-- Panel 2: Riwayat Serah Terima (Hidden) --}}
                <div id="modal-tab-content-serah-terima" class="space-y-4 hidden">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-bold text-[#0F034D]">Log Serah Terima Barang</h3>
                        <span class="text-[10px] text-gray-400" id="mutasi-count-label">0 log</span>
                    </div>
                    <div id="mutasi-list" class="space-y-3"></div>
                    <div id="mutasi-pagination" class="flex items-center justify-center gap-1.5 mt-4 hidden">
                        <button type="button" id="mutasi-prev" class="px-2.5 py-1 text-xs rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium transition-colors disabled:opacity-40 disabled:cursor-not-allowed">‹ Sebelumnya</button>
                        <span id="mutasi-page-info" class="text-xs text-gray-500 px-2">1 / 1</span>
                        <button type="button" id="mutasi-next" class="px-2.5 py-1 text-xs rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium transition-colors disabled:opacity-40 disabled:cursor-not-allowed">Berikutnya ›</button>
                    </div>
                    <div id="mutasi-empty" class="hidden text-center py-5">
                        <p class="text-xs text-gray-500">Belum ada aktivitas serah terima</p>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <div class="slide-panel-footer">
                <button type="button" class="btn-panel-cancel" data-close-stok-modal>Tutup</button>
            </div>
        </div>
    </div>
    <div id="photoViewerModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-200">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative max-w-2xl w-full">
                <button type="button" data-close-photo class="absolute -top-3 -right-3 w-9 h-9 rounded-full bg-white shadow-lg flex items-center justify-center hover:bg-gray-100 transition-colors z-10">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <img id="photoViewerImg" src="" alt="Bukti Penerimaan" class="w-full rounded-xl shadow-2xl">
            </div>
        </div>
    </div>

    @vite('resources/js/admin/perintah-produksi/show.js')
</x-layouts.owner>

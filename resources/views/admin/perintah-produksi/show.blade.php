<x-layouts.admin>
    <x-slot:breadcrumb>
        <li class="flex items-center">
            <span class="text-gray-400 select-none">Produksi</span>
        </li>
        <li class="flex items-center">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <a href="{{ route('admin.perintah-produksi.index') }}" class="text-gray-400 hover:text-[#0F034D] transition-colors">Perintah Produksi</a>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            {{ $perintahProduksi->nomor_wo }}
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Detail Perintah Produksi
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
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <h3 class="text-lg font-bold text-[#0F034D]">{{ $perintahProduksi->nomor_wo }}</h3>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusColors[$perintahProduksi->status_produksi] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                        {{ $statusLabels[$perintahProduksi->status_produksi] ?? $perintahProduksi->status_produksi }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-1">
                    Dibuat oleh <span class="font-semibold text-[#0F034D]">{{ $perintahProduksi->user->name ?? '-' }}</span>
                    pada {{ $perintahProduksi->created_at->format('d M Y, H:i') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.perintah-produksi.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </a>

                @if($perintahProduksi->status_produksi === 'pending')
                    <a href="{{ route('admin.perintah-produksi.edit', $perintahProduksi) }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-sm font-medium rounded-xl transition-all shadow-md shadow-[#0F034D]/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit
                    </a>
                @endif

                @if($perintahProduksi->status_produksi === 'dalam_produksi')
                    <form action="{{ route('admin.perintah-produksi.selesai', $perintahProduksi) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="tgl_selesai" value="{{ now()->format('Y-m-d') }}">
                        <button type="submit" data-confirm-action="Tandai perintah produksi ini sebagai selesai?"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl transition-colors shadow-md shadow-green-600/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Tandai Selesai
                        </button>
                    </form>
                @endif
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
                <div class="bg-[#0F034D]/5 rounded-xl p-4 border border-[#0F034D]/10">
                    <p class="text-xs text-gray-500 mb-1">Disetujui Oleh</p>
                    <p class="font-semibold text-sm text-[#0F034D]">{{ $perintahProduksi->approver->name ?? '-' }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $perintahProduksi->approved_at ? $perintahProduksi->approved_at->format('d M Y, H:i') : 'Menunggu persetujuan owner' }}</p>
                </div>
                <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                    <p class="text-xs text-gray-500 mb-1">Hasil Potong Tercatat</p>
                    <p class="font-semibold text-sm text-green-700">{{ number_format($totalPotong, 0, ',', '.') }} pcs</p>
                    <p class="text-xs text-gray-400 mt-1">Akumulasi dari input hasil produksi saat ini</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                    <p class="text-xs text-gray-500 mb-1">Progress Administratif</p>
                    <p class="font-semibold text-sm text-blue-700">{{ $statusLabels[$perintahProduksi->status_produksi] ?? $perintahProduksi->status_produksi }}</p>
                    <p class="text-xs text-gray-400 mt-1">Status utama perintah produksi</p>
                </div>
            </div>

            @if($perintahProduksi->alasan_penolakan)
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                    <p class="text-xs text-red-500 mb-1">Alasan Penolakan</p>
                    <p class="text-sm text-red-700 font-medium">{{ $perintahProduksi->alasan_penolakan }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 pt-6 pb-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-[#0F034D]">Detail Produk & Tahapan Produksi</h3>
            <p class="text-sm text-gray-500 mt-1">Pantau estimasi, hasil potong, validasi, dan posisi proses setiap produk dalam perintah produksi.</p>
        </div>

        <div class="divide-y divide-gray-100">
            @foreach($perintahProduksi->details as $detail)
                @php
                    $stokVirtualAll = $perintahProduksi->stokVirtual->where('id_detail_perintah', $detail->id);
                    $isFinished = $perintahProduksi->status_produksi === 'selesai';

                    // Potong: progress = diteruskan / total hasil potong
                    $potongStok = $stokVirtualAll->where('peran', 'potong');
                    $potongTotalMasuk = (int) ($detail->qty_pcs_potong ?? 0);
                    $potongDiteruskan = (int) $potongStok->sum('total_dikeluarkan');
                    $potongProgress = $potongTotalMasuk > 0 ? min(100, (int) round($potongDiteruskan / $potongTotalMasuk * 100)) : 0;

                    // Jahit: progress = selesai / total masuk
                    $jahitStok = $stokVirtualAll->where('peran', 'jahit');
                    $jahitTotalMasuk = (int) $jahitStok->sum(fn($s) => (int) $s->qty_hold + (int) $s->total_selesai);
                    $jahitSelesai = (int) $jahitStok->sum('total_selesai');
                    $jahitProgress = $jahitTotalMasuk > 0 ? min(100, (int) round($jahitSelesai / $jahitTotalMasuk * 100)) : 0;

                    // Finishing: progress = selesai / total masuk
                    $finishingStok = $stokVirtualAll->where('peran', 'finishing');
                    $finishingTotalMasuk = (int) $finishingStok->sum(fn($s) => (int) $s->qty_hold + (int) $s->total_selesai);
                    $finishingSelesai = (int) $finishingStok->sum('total_selesai');
                    $finishingProgress = $finishingTotalMasuk > 0 ? min(100, (int) round($finishingSelesai / $finishingTotalMasuk * 100)) : 0;

                    // Ready: qty siap diterima dari finishing
                    $readyQty = (int) $finishingStok->sum(fn($s) => max(0, (int) $s->total_selesai - (int) $s->total_dikeluarkan));

                    $steps = [
                        ['key' => 'wo', 'label' => 'WO', 'sub' => number_format($detail->estimasi_pcs, 0, ',', '.') . ' pcs', 'progress' => 100, 'started' => true],
                        ['key' => 'potong', 'label' => 'Potong', 'sub' => $potongTotalMasuk > 0 ? number_format($potongDiteruskan, 0, ',', '.') . '/' . number_format($potongTotalMasuk, 0, ',', '.') . ' diteruskan' : 'Menunggu', 'progress' => $isFinished ? 100 : $potongProgress, 'started' => $potongTotalMasuk > 0],
                        ['key' => 'jahit', 'label' => 'Jahit', 'sub' => $jahitTotalMasuk > 0 ? number_format($jahitSelesai, 0, ',', '.') . '/' . number_format($jahitTotalMasuk, 0, ',', '.') . ' pcs' : 'Menunggu', 'progress' => $isFinished ? 100 : $jahitProgress, 'started' => $jahitTotalMasuk > 0],
                        ['key' => 'finishing', 'label' => 'Finishing', 'sub' => $finishingTotalMasuk > 0 ? number_format($finishingSelesai, 0, ',', '.') . '/' . number_format($finishingTotalMasuk, 0, ',', '.') . ' pcs' : 'Menunggu', 'progress' => $isFinished ? 100 : $finishingProgress, 'started' => $finishingTotalMasuk > 0],
                        ['key' => 'ready', 'label' => 'Siap Diterima', 'sub' => $readyQty > 0 ? number_format($readyQty, 0, ',', '.') . ' pcs' : 'Belum ada', 'progress' => $readyQty > 0 || $isFinished ? 100 : 0, 'started' => $readyQty > 0 || $isFinished],
                    ];

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
                @endphp

                <div class="p-6">
                    <!-- Product Identity Header (global) -->
                    <div class="flex flex-wrap items-center gap-3 mb-5">
                        @php
                            $warnaProduk = strtolower($detail->produk->warna ?? '-');
                            $warnaDotMap = [
                                'hitam' => '#111827',
                                'navy' => '#061952',
                                'abu-abu' => '#9CA3AF',
                                'abu' => '#9CA3AF',
                                'putih' => '#FFFFFF',
                            ];
                            $warnaDot = $warnaDotMap[$warnaProduk] ?? '#CBD5E1';
                            $needsStroke = in_array($warnaProduk, ['abu-abu', 'abu', 'putih'], true);
                        @endphp
                        <h4 class="inline-flex items-center gap-2 text-base font-bold text-[#0F034D] min-w-0">
                            <span class="truncate">{{ $detail->produk->nama_produk ?? '-' }} - {{ ucfirst($detail->produk->warna ?? '-') }}</span>
                            <span class="inline-block w-3 h-3 rounded-full shrink-0 {{ $needsStroke ? 'ring-1 ring-gray-300' : '' }}" style="background-color: {{ $warnaDot }}" title="Warna {{ ucfirst($detail->produk->warna ?? '-') }}"></span>
                        </h4>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $validasiColors[$detail->status_validasi_potong] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                            Validasi: {{ $validasiLabel[$detail->status_validasi_potong] ?? ucfirst($detail->status_validasi_potong) }}
                        </span>
                    </div>

                    <!-- Main Tabs -->
                    <div class="flex border-b border-gray-100 pb-3 mb-5 gap-2">
                        <button type="button" data-main-tab-trigger="produk-{{ $detail->id }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#0F034D] text-white hover:bg-[#24116f] transition-all shadow-sm">Detail Produk</button>
                        <button type="button" data-main-tab-trigger="tahapan-{{ $detail->id }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-gray-50 text-gray-500 hover:bg-gray-100 transition-all">Tahapan Produksi & Aliran Barang</button>
                    </div>

                    <!-- Main Tab Content 1: Detail Produk -->
                    <div data-main-tab-content="produk-{{ $detail->id }}">
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                            <div class="rounded-xl bg-gray-50 p-3">
                                <p class="text-[11px] text-gray-400 mb-1">Bahan Baku</p>
                                <p class="text-xs font-semibold text-[#0F034D] truncate">{{ $detail->bahanBaku->nama_bahan ?? '-' }}</p>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-3">
                                <p class="text-[11px] text-gray-400 mb-1">Qty Roll</p>
                                <p class="text-sm font-bold text-[#0F034D]">{{ number_format($detail->qty_roll_pakai, 0, ',', '.') }}</p>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-3">
                                <p class="text-[11px] text-gray-400 mb-1">Estimasi</p>
                                <p class="text-sm font-bold text-[#0F034D]">{{ number_format($detail->estimasi_pcs, 0, ',', '.') }}</p>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-3">
                                <p class="text-[11px] text-gray-400 mb-1">Toleransi -</p>
                                <p class="text-sm font-bold text-[#0F034D]">{{ number_format($detail->toleransi_minus, 0, ',', '.') }}</p>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-3">
                                <p class="text-[11px] text-gray-400 mb-1">Hasil Potong</p>
                                <p class="text-sm font-bold text-[#0F034D]">{{ $detail->qty_pcs_potong ? number_format($detail->qty_pcs_potong, 0, ',', '.') : '-' }}</p>
                            </div>
                        </div>

                        @if($detail->alasan)
                            <div class="mt-3 rounded-xl bg-red-50 border border-red-100 p-3">
                                <p class="text-xs text-red-500 mb-1">Catatan / Alasan Flag</p>
                                <p class="text-sm text-red-700 font-medium">{{ $detail->alasan }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Main Tab Content 2: Tahapan Produksi & Aliran Barang -->
                    <div data-main-tab-content="tahapan-{{ $detail->id }}" class="hidden">
                        <!-- Alur Produksi -->
                        <div class="mb-6">
                            <p class="text-xs font-bold text-[#0F034D] mb-4">Alur Produksi</p>
                            <div class="relative flex items-start justify-between">
                                @foreach($steps as $index => $step)
                                    @php
                                        $circumference = 100.5;
                                        $dashOffset = $circumference * (1 - $step['progress'] / 100);
                                        $isComplete = $step['progress'] >= 100;
                                        $isActive = $step['started'] && !$isComplete;
                                        $ringColor = $isComplete ? '#22c55e' : ($isActive ? '#0F034D' : '#e5e7eb');
                                        $labelColor = $step['started'] ? 'text-[#0F034D]' : 'text-gray-400';
                                    @endphp
                                    <div class="flex-1 relative flex flex-col items-center text-center min-w-0 px-1">
                                        @if(!$loop->last)
                                            @php
                                                $nextStep = $steps[$index + 1];
                                                $lineClass = 'border-gray-200 border-solid';
                                                if ($nextStep['started']) {
                                                    $lineClass = $nextStep['progress'] >= 100 ? 'border-green-400 border-solid' : 'border-[#0F034D]/40 border-solid';
                                                } elseif ($step['progress'] >= 100) {
                                                    $lineClass = 'border-[#0F034D]/30 border-dashed';
                                                }
                                            @endphp
                                            <div class="absolute top-5 left-1/2 w-full border-t-2 {{ $lineClass }} z-0"></div>
                                        @endif
                                        <div class="relative z-10 w-10 h-10 shrink-0 rounded-full bg-white">
                                            <svg class="w-10 h-10 -rotate-90" viewBox="0 0 40 40">
                                                <circle cx="20" cy="20" r="16" fill="none" stroke="#e5e7eb" stroke-width="3"></circle>
                                                <circle cx="20" cy="20" r="16" fill="none" stroke="{{ $ringColor }}" stroke-width="3"
                                                    stroke-dasharray="{{ $circumference }}"
                                                    stroke-dashoffset="{{ $dashOffset }}"
                                                    stroke-linecap="round"
                                                    class="transition-all duration-500"></circle>
                                            </svg>
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                @if($isComplete)
                                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                @else
                                                    <span class="text-[10px] font-bold {{ $isActive ? 'text-[#0F034D]' : 'text-gray-400' }}">{{ $step['progress'] }}%</span>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="mt-2 text-xs font-semibold {{ $labelColor }} leading-tight">{{ $step['label'] }}</p>
                                        <p class="text-[11px] text-gray-400 leading-tight mt-0.5">{{ $step['sub'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Detail Distribusi -->
                        <div class="w-full">
                            <div class="border border-gray-100 rounded-xl p-3 bg-white shadow-sm">
                                <div class="flex items-center justify-between border-b border-gray-100 pb-2 mb-2">
                                    <span class="text-xs font-bold text-[#0F034D]">Detail Distribusi</span>
                                    <div class="flex gap-1.5">
                                        <button type="button" data-tab-trigger="posisi-{{ $detail->id }}" class="px-2 py-1 rounded-md text-[10px] font-bold bg-[#0F034D] text-white hover:bg-[#24116f] transition-all shadow-sm">Stok Aktif</button>
                                        <button type="button" data-tab-trigger="log-{{ $detail->id }}" class="px-2 py-1 rounded-md text-[10px] font-bold bg-gray-50 text-gray-500 hover:bg-gray-100 transition-all">Log Serah Terima</button>
                                    </div>
                                </div>

                                <!-- Tab 1: Posisi Barang Aktif (Stok Virtual) -->
                                <div data-tab-content="posisi-{{ $detail->id }}" class="space-y-1.5">
                                    @php
                                        $stokVirtualAll = $perintahProduksi->stokVirtual->where('id_detail_perintah', $detail->id);
                                        $displayStok = collect();

                                        foreach ($stokVirtualAll as $stok) {
                                            // 1. Ready Stock
                                            if ($stok->peran === 'potong') {
                                                if ($stok->qty_hold > 0) {
                                                    $displayStok->push([
                                                        'karyawan_name' => $stok->karyawan->name ?? '-',
                                                        'peran' => $stok->peran,
                                                        'qty' => $stok->qty_hold,
                                                        'status' => 'Ready',
                                                    ]);
                                                }
                                            } else {
                                                $readyQty = max(0, (int) $stok->total_selesai - (int) $stok->total_dikeluarkan);
                                                if ($readyQty > 0) {
                                                    $displayStok->push([
                                                        'karyawan_name' => $stok->karyawan->name ?? '-',
                                                        'peran' => $stok->peran,
                                                        'qty' => $readyQty,
                                                        'status' => 'Ready',
                                                    ]);
                                                }
                                                // 2. Dalam Pengerjaan / Selisih
                                                if ($stok->qty_hold > 0) {
                                                    $statusLabel = $stok->is_selesai ? 'Selisih (Selesai)' : 'Dalam Pengerjaan';
                                                    $displayStok->push([
                                                        'karyawan_name' => $stok->karyawan->name ?? '-',
                                                        'peran' => $stok->peran,
                                                        'qty' => $stok->qty_hold,
                                                        'status' => $statusLabel,
                                                    ]);
                                                }
                                            }
                                        }
                                    @endphp
                                    @forelse($displayStok as $item)
                                        <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50 text-[11px] leading-tight">
                                            <div class="min-w-0">
                                                <span class="font-bold text-gray-800 block truncate">{{ $item['karyawan_name'] }}</span>
                                                <span class="text-gray-400 text-[10px]">Role: {{ ucfirst($item['peran']) }}</span>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <span class="font-bold text-indigo-700 block">{{ number_format($item['qty']) }} pcs</span>
                                                <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded-full {{ $item['status'] === 'Ready' ? 'bg-green-50 text-green-700' : ($item['status'] === 'Dalam Pengerjaan' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                                                    {{ $item['status'] }}
                                                </span>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-[11px] text-gray-400 py-1.5 text-center">Belum ada barang aktif di tangan karyawan.</p>
                                    @endforelse
                                </div>

                                <!-- Tab 2: Log Serah Terima -->
                                <div data-tab-content="log-{{ $detail->id }}" class="hidden space-y-1.5 max-h-48 overflow-y-auto pr-1">
                                    @forelse($detail->mutasiProduksi->sortByDesc('created_at') as $mutasi)
                                        <div class="p-2 rounded-lg bg-gray-50 border border-gray-100 text-[11px] leading-snug">
                                            <div class="flex items-center justify-between gap-2 mb-1">
                                                <span class="font-bold text-[#0F034D]">+{{ number_format($mutasi->qty_pindah) }} pcs</span>
                                                <span class="text-[9px] text-gray-400 font-medium">
                                                    {{ $mutasi->tgl_transaksi ? $mutasi->tgl_transaksi->format('d M H:i') : $mutasi->created_at->format('d M H:i') }}
                                                </span>
                                            </div>
                                            <p class="text-gray-500">
                                                Dari <span class="font-semibold text-gray-700">{{ $mutasi->dariKaryawan->name ?? 'Sistem' }}</span> ({{ ucfirst($mutasi->dari_tahapan ?? 'awal') }})
                                                ke <span class="font-semibold text-gray-700">{{ $mutasi->keKaryawan->name ?? '-' }}</span> ({{ ucfirst($mutasi->ke_tahapan) }})
                                            </p>
                                        </div>
                                    @empty
                                        <p class="text-[11px] text-gray-400 py-1.5 text-center">Belum ada aktivitas serah terima.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        // Main tabs switcher (Detail Produk vs Tahapan Produksi)
        document.querySelectorAll('[data-main-tab-trigger]').forEach(btn => {
            btn.addEventListener('click', () => {
                const targetKey = btn.dataset.mainTabTrigger;
                const isProduk = targetKey.startsWith('produk-');
                const id = targetKey.replace('produk-', '').replace('tahapan-', '');

                const produkBtn = document.querySelector(`[data-main-tab-trigger="produk-${id}"]`);
                const tahapanBtn = document.querySelector(`[data-main-tab-trigger="tahapan-${id}"]`);
                const produkContent = document.querySelector(`[data-main-tab-content="produk-${id}"]`);
                const tahapanContent = document.querySelector(`[data-main-tab-content="tahapan-${id}"]`);

                if (isProduk) {
                    produkBtn.classList.replace('bg-gray-50', 'bg-[#0F034D]');
                    produkBtn.classList.replace('text-gray-500', 'text-white');
                    produkBtn.classList.replace('hover:bg-gray-100', 'hover:bg-[#24116f]');
                    produkBtn.classList.add('shadow-sm');
                    tahapanBtn.classList.replace('bg-[#0F034D]', 'bg-gray-50');
                    tahapanBtn.classList.replace('text-white', 'text-gray-500');
                    tahapanBtn.classList.replace('hover:bg-[#24116f]', 'hover:bg-gray-100');
                    tahapanBtn.classList.remove('shadow-sm');

                    produkContent.classList.remove('hidden');
                    tahapanContent.classList.add('hidden');
                } else {
                    tahapanBtn.classList.replace('bg-gray-50', 'bg-[#0F034D]');
                    tahapanBtn.classList.replace('text-gray-500', 'text-white');
                    tahapanBtn.classList.replace('hover:bg-gray-100', 'hover:bg-[#24116f]');
                    tahapanBtn.classList.add('shadow-sm');
                    produkBtn.classList.replace('bg-[#0F034D]', 'bg-gray-50');
                    produkBtn.classList.replace('text-white', 'text-gray-500');
                    produkBtn.classList.replace('hover:bg-[#24116f]', 'hover:bg-gray-100');
                    produkBtn.classList.remove('shadow-sm');

                    tahapanContent.classList.remove('hidden');
                    produkContent.classList.add('hidden');
                }
            });
        });

        // Sub-tabs switcher (Stok Aktif vs Log Serah Terima)
        document.querySelectorAll('[data-tab-trigger]').forEach(btn => {
            btn.addEventListener('click', () => {
                const targetKey = btn.dataset.tabTrigger;
                const isPosisi = targetKey.startsWith('posisi-');
                const id = targetKey.replace('posisi-', '').replace('log-', '');

                const posisiBtn = document.querySelector(`[data-tab-trigger="posisi-${id}"]`);
                const logBtn = document.querySelector(`[data-tab-trigger="log-${id}"]`);
                const posisiContent = document.querySelector(`[data-tab-content="posisi-${id}"]`);
                const logContent = document.querySelector(`[data-tab-content="log-${id}"]`);

                if (isPosisi) {
                    posisiBtn.classList.replace('bg-gray-50', 'bg-[#0F034D]');
                    posisiBtn.classList.replace('text-gray-500', 'text-white');
                    posisiBtn.classList.replace('hover:bg-gray-100', 'hover:bg-[#24116f]');
                    logBtn.classList.replace('bg-[#0F034D]', 'bg-gray-50');
                    logBtn.classList.replace('text-white', 'text-gray-500');
                    logBtn.classList.replace('hover:bg-[#24116f]', 'hover:bg-gray-100');

                    posisiContent.classList.remove('hidden');
                    logContent.classList.add('hidden');
                } else {
                    logBtn.classList.replace('bg-gray-50', 'bg-[#0F034D]');
                    logBtn.classList.replace('text-gray-500', 'text-white');
                    logBtn.classList.replace('hover:bg-gray-100', 'hover:bg-[#24116f]');
                    posisiBtn.classList.replace('bg-[#0F034D]', 'bg-gray-50');
                    posisiBtn.classList.replace('text-white', 'text-gray-500');
                    posisiBtn.classList.replace('hover:bg-[#24116f]', 'hover:bg-gray-100');

                    logContent.classList.remove('hidden');
                    posisiContent.classList.add('hidden');
                }
            });
        });
    </script>

    @vite('resources/js/admin/confirm-action.js')
</x-layouts.admin>


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
@php
    $totalDiterimaAkumulasi = (int) $detail->total_qty_diterima + (int) $detail->total_qty_cacat_diterima;
    $batasMinNormal = (int) $detail->estimasi_pcs - (int) $detail->toleransi_minus;
    $isWOFinished = $perintahProduksi->status_produksi === 'selesai';
    
    if ($isWOFinished) {
        if ($totalDiterimaAkumulasi < $batasMinNormal) {
            $selisihNet = $totalDiterimaAkumulasi - $batasMinNormal; // Misal 568 - 575 = -7 pcs
        } elseif ($totalDiterimaAkumulasi > (int) $detail->estimasi_pcs) {
            $selisihNet = $totalDiterimaAkumulasi - (int) $detail->estimasi_pcs; // Misal 610 - 600 = +10 pcs
        } else {
            $selisihNet = 0;
        }
    } else {
        $selisihNet = 0;
    }
    $isBelowMin = $isWOFinished && ($totalDiterimaAkumulasi < $batasMinNormal);
@endphp
<div class="bg-white rounded-xl border border-gray-300 overflow-hidden max-h-[calc(100vh-8rem)] flex flex-col">
    {{-- Fixed Header --}}
    <div class="p-3 border-b border-gray-200 shrink-0">
        <p class="text-[11px] font-bold uppercase tracking-wide text-gray-400">Detail Produk Terpilih</p>
        <p class="text-xs text-gray-500 mt-0.5">Informasi detail produk yang dipilih dari daftar</p>
    </div>
    {{-- Scrollable Content --}}
    <div class="p-4 space-y-4 overflow-y-auto min-h-0">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <div class="rounded-lg border px-3 py-2 {{ $statusColors[$detail->status_validasi_potong] ?? $statusColors['pending'] }}">
                <p class="text-[10px] font-bold uppercase tracking-wide">Status Produksi: {{ ucfirst($detail->status_validasi_potong ?? 'pending') }}</p>
                <p class="text-[11px] mt-0.5 leading-relaxed">{{ $statusDescriptions[$detail->status_validasi_potong] ?? $statusDescriptions['pending'] }}</p>
            </div>
            @php
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

        {{-- Row 1: Card 1 (Detail Produk) + Card 2 (Stepper) side by side --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
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
                        <p class="text-[10px] text-gray-400 mb-0.5">Bahan Baku</p>
                        <p class="text-xs font-semibold text-[#0F034D] truncate">{{ $detail->bahanBaku->nama_bahan ?? '-' }}</p>
                    </div>
                    <div class="rounded-lg bg-gray-50 px-3 py-2">
                        <p class="text-[10px] text-gray-400 mb-0.5">Qty Roll</p>
                        <p class="text-sm font-bold text-[#0F034D]">{{ number_format($detail->qty_roll_pakai, 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-lg bg-gray-50 px-3 py-2">
                        <p class="text-[10px] text-gray-400 mb-0.5">Toleransi -</p>
                        <p class="text-sm font-bold text-[#0F034D]">{{ number_format($detail->toleransi_minus, 0, ',', '.') }}</p>
                    </div>
                </div>

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

        <div class="rounded-xl border border-gray-300 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200">
                <h4 class="text-sm font-bold text-[#0F034D]">Tahapan Produksi</h4>
                <p class="text-xs text-gray-500 mt-0.5">Alur produksi dari WO hingga selesai diterima admin</p>
            </div>
            <div class="p-5">
                @include('admin.perintah-produksi.partials._stepper')
            </div>
        </div>
    </div>

    {{-- Card 3: Distribusi (Tabbed: Stok | Log | Riwayat) --}}
    <div class="rounded-xl border border-gray-300 overflow-hidden">
        {{-- Card Header: Title --}}
        <div class="px-5 py-3 border-b border-gray-200">
            <h4 class="text-sm font-bold text-[#0F034D]">Detail Distribusi</h4>
            <p class="text-xs text-gray-500 mt-0.5">Distribusi stok pengerjaan dan riwayat penerimaan</p>
        </div>
        {{-- Tab Header with Input Penerimaan button --}}
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-0.5">
                <button type="button" data-distribusi-tab="stok"
                    class="px-3 py-1.5 text-xs font-semibold rounded-md bg-[#0F034D] text-white shadow-sm transition-all">
                    Stok Karyawan
                </button>
                <button type="button" data-distribusi-tab="riwayat"
                    class="px-3 py-1.5 text-xs font-semibold rounded-md text-gray-500 hover:text-[#0F034D] transition-all">
                    Riwayat Penerimaan
                </button>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" data-open-penerimaan-modal
                    data-detail-id="{{ $detail->id }}"
                    data-produk-nama="{{ $detail->produk->nama_produk }} - {{ ucfirst($detail->produk->warna) }}"
                    data-estimasi="{{ $detail->estimasi_pcs }}"
                    data-diterima="{{ $detail->total_qty_diterima }}"
                    data-sisa="{{ $detail->estimasi_pcs - $detail->total_qty_diterima }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-xs font-semibold rounded-lg transition-all shadow-sm cursor-pointer shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Input Penerimaan Hasil
                </button>
            </div>
        </div>

        {{-- Tab Content: Stok (default) --}}
        <div data-distribusi-content="stok" class="p-4 space-y-2">
            @include('admin.perintah-produksi.partials._distribusi-stok')
        </div>

        {{-- Tab Content: Riwayat --}}
        <div data-distribusi-content="riwayat" class="p-4 hidden">
            @include('admin.perintah-produksi.partials._distribusi-riwayat')
        </div>
    </div>
    {{-- End Scrollable Content --}}
    </div>
</div>

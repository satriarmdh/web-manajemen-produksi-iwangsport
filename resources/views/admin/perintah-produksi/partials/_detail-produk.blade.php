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
        'selisih_kurang' => 'Selisih Kurang',
        'selisih_lebih' => 'Selisih Lebih',
    ];
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
    $estimasi = $detail->estimasi_pcs;
    $diterima = $detail->total_qty_diterima;
    $sisa = $estimasi - $diterima;
@endphp
<div class="p-6">
    <!-- Product Identity Header (global) -->
    <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
        <div class="flex flex-wrap items-center gap-2">
            <h4 class="inline-flex items-center gap-2 text-base font-bold text-[#0F034D] min-w-0">
                <span class="truncate">{{ $detail->produk->nama_produk ?? '-' }} - {{ ucfirst($detail->produk->warna ?? '-') }}</span>
                <span class="inline-block w-3 h-3 rounded-full shrink-0 {{ $needsStroke ? 'ring-1 ring-gray-300' : '' }}" style="background-color: {{ $warnaDot }}"></span>
            </h4>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $validasiColors[$detail->status_validasi_potong] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                Validasi: {{ $validasiLabel[$detail->status_validasi_potong] ?? ucfirst($detail->status_validasi_potong) }}
            </span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $penerimaanColors[$detail->status_penerimaan] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                Penerimaan: {{ $penerimaanLabel[$detail->status_penerimaan] ?? ucfirst($detail->status_penerimaan) }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            @if($detail->status_penerimaan !== 'sesuai')
                <button type="button" data-open-penerimaan-modal
                    data-detail-id="{{ $detail->id }}"
                    data-produk-nama="{{ $detail->produk->nama_produk }} - {{ ucfirst($detail->produk->warna) }}"
                    data-estimasi="{{ $estimasi }}"
                    data-diterima="{{ $diterima }}"
                    data-sisa="{{ $sisa }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-lg transition-colors border border-emerald-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Input Penerimaan
                </button>
            @endif
        </div>
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
        @include('admin.perintah-produksi.partials._stepper')
        @include('admin.perintah-produksi.partials._distribusi')
    </div>
</div>

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
@endphp
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

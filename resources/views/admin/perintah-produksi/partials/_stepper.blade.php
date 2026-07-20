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
        ['key' => 'wo', 'label' => 'Work Order', 'sub' => number_format($detail->estimasi_pcs, 0, ',', '.') . ' pcs estimasi', 'progress' => 100, 'started' => true],
        ['key' => 'potong', 'label' => 'Potong', 'sub' => $potongTotalMasuk > 0 ? number_format($potongDiteruskan, 0, ',', '.') . '/' . number_format($potongTotalMasuk, 0, ',', '.') . ' diteruskan' : 'Menunggu hasil potong', 'progress' => $isFinished ? 100 : $potongProgress, 'started' => $potongTotalMasuk > 0],
        ['key' => 'jahit', 'label' => 'Jahit', 'sub' => $jahitTotalMasuk > 0 ? number_format($jahitSelesai, 0, ',', '.') . '/' . number_format($jahitTotalMasuk, 0, ',', '.') . ' pcs' : 'Menunggu transfer dari potong', 'progress' => $isFinished ? 100 : $jahitProgress, 'started' => $jahitTotalMasuk > 0],
        ['key' => 'finishing', 'label' => 'Finishing', 'sub' => $finishingTotalMasuk > 0 ? number_format($finishingSelesai, 0, ',', '.') . '/' . number_format($finishingTotalMasuk, 0, ',', '.') . ' pcs' : 'Menunggu transfer dari jahit', 'progress' => $isFinished ? 100 : $finishingProgress, 'started' => $finishingTotalMasuk > 0],
        ['key' => 'ready', 'label' => 'Siap Diterima', 'sub' => $readyQty > 0 ? number_format($readyQty, 0, ',', '.') . ' pcs ready' : ($isFinished ? 'Selesai' : 'Belum ada'), 'progress' => $readyQty > 0 || $isFinished ? 100 : 0, 'started' => $readyQty > 0 || $isFinished],
    ];
@endphp
<!-- Alur Produksi (Vertical Timeline) -->
<div class="relative">
    @foreach($steps as $index => $step)
        @php
            $circumference = 100.5;
            $dashOffset = $circumference * (1 - $step['progress'] / 100);
            $isComplete = $step['progress'] >= 100;
            $isActive = $step['started'] && !$isComplete;
            $ringColor = $isComplete ? '#22c55e' : ($isActive ? '#0F034D' : '#e5e7eb');
            $labelColor = $step['started'] ? 'text-[#0F034D]' : 'text-gray-400';
            $isLast = $loop->last;
        @endphp
        <div class="flex items-start gap-3 {{ $isLast ? '' : 'pb-3' }}">
            {{-- Left: Progress Ring + Connector Line --}}
            <div class="relative flex flex-col items-center shrink-0">
                <div class="relative w-7 h-7 rounded-full bg-white shrink-0">
                    <svg class="w-7 h-7 -rotate-90" viewBox="0 0 40 40">
                        <circle cx="20" cy="20" r="16" fill="none" stroke="#e5e7eb" stroke-width="4"></circle>
                        <circle cx="20" cy="20" r="16" fill="none" stroke="{{ $ringColor }}" stroke-width="4"
                            stroke-dasharray="{{ $circumference }}"
                            stroke-dashoffset="{{ $dashOffset }}"
                            stroke-linecap="round"
                            class="transition-all duration-500"></circle>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        @if($isComplete)
                            <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        @else
                            <span class="text-[8px] font-bold {{ $isActive ? 'text-[#0F034D]' : 'text-gray-400' }}">{{ $step['progress'] }}%</span>
                        @endif
                    </div>
                </div>
                @if(!$isLast)
                    @php
                        $nextStep = $steps[$index + 1];
                        $lineClass = $nextStep['progress'] >= 100 ? 'bg-green-400' : ($nextStep['started'] ? 'bg-[#0F034D]/40' : 'bg-gray-200');
                    @endphp
                    <div class="w-0.5 grow {{ $lineClass }} mt-1 transition-all duration-500" style="min-height: 1rem;"></div>
                @endif
            </div>

            {{-- Right: Label + Sub-info --}}
            <div class="pt-1 pb-0.5 min-w-0 flex-1">
                <p class="text-xs font-bold {{ $labelColor }} leading-tight">{{ $step['label'] }}</p>
                <p class="text-[11px] text-gray-400 leading-tight mt-0.5">{{ $step['sub'] }}</p>
            </div>
        </div>
    @endforeach
</div>

@php
    $stokVirtualAll = $perintahProduksi->stokVirtual->where('id_detail_perintah', $detail->id);
    $isFinished = $perintahProduksi->status_produksi === 'selesai';

    // Potong
    $potongStok = $stokVirtualAll->where('peran', 'potong');
    $potongSelesai = (int) ($detail->qty_pcs_potong ?? 0) + (int) $potongStok->sum('total_reject');
    $potongDiserahkan = (int) $potongStok->sum('total_dikeluarkan');
    $potongIsSelesai = $potongStok->where('is_selesai', 1)->isNotEmpty() || ($detail->status_validasi_potong !== 'pending' && $potongSelesai > 0);
    $potongProgress = $detail->estimasi_pcs > 0 ? min(100, (int) round($potongSelesai / $detail->estimasi_pcs * 100)) : 0;
    $potongComplete = $potongIsSelesai || $isFinished;

    // Jahit
    $jahitStok = $stokVirtualAll->where('peran', 'jahit');
    $jahitTotalMasuk = (int) $jahitStok->sum(fn($s) => (int) $s->qty_hold + (int) $s->total_selesai + (int) $s->total_reject);
    $jahitSelesai = (int) $jahitStok->sum('total_selesai') + (int) $jahitStok->sum('total_reject');
    $jahitDiserahkan = (int) $jahitStok->sum('total_dikeluarkan');
    $jahitIsSelesai = $jahitStok->count() > 0 && $jahitStok->where('is_selesai', 0)->count() === 0;
    $jahitProgress = $jahitTotalMasuk > 0 ? min(100, (int) round($jahitSelesai / $jahitTotalMasuk * 100)) : 0;
    $jahitComplete = $jahitIsSelesai || $isFinished;

    // Finishing
    $finishingStok = $stokVirtualAll->where('peran', 'finishing');
    $finishingTotalMasuk = (int) $finishingStok->sum(fn($s) => (int) $s->qty_hold + (int) $s->total_selesai + (int) $s->total_reject);
    $finishingSelesai = (int) $finishingStok->sum('total_selesai') + (int) $finishingStok->sum('total_reject');
    $finishingDiserahkan = (int) $finishingStok->sum('total_dikeluarkan');
    $finishingIsSelesai = $finishingStok->count() > 0 && $finishingStok->where('is_selesai', 0)->count() === 0;
    $finishingProgress = $finishingTotalMasuk > 0 ? min(100, (int) round($finishingSelesai / $finishingTotalMasuk * 100)) : 0;
    $finishingComplete = $finishingIsSelesai || $isFinished;

    // Selesai (Penerimaan Admin)
    $diterimaAdmin = (int) ($detail->qty_diterima_admin ?? $finishingDiserahkan);
    $readyComplete = $isFinished || ($diterimaAdmin >= $detail->estimasi_pcs && $detail->estimasi_pcs > 0);

    $steps = [
        [
            'key' => 'wo',
            'label' => 'Work Order',
            'lines' => [number_format($detail->estimasi_pcs, 0, ',', '.') . ' pcs estimasi'],
            'progress' => 100,
            'isComplete' => true,
            'started' => true,
        ],
        [
            'key' => 'potong',
            'label' => 'Potong',
            'lines' => [
                'Selesai: ' . number_format($potongSelesai, 0, ',', '.') . ' pcs',
            ],
            'progress' => $potongComplete ? 100 : $potongProgress,
            'isComplete' => $potongComplete,
            'started' => $potongSelesai > 0 || $potongIsSelesai,
        ],
        [
            'key' => 'jahit',
            'label' => 'Jahit',
            'lines' => [
                'Selesai: ' . number_format($jahitSelesai, 0, ',', '.') . ' pcs',
            ],
            'progress' => $jahitComplete ? 100 : $jahitProgress,
            'isComplete' => $jahitComplete,
            'started' => $jahitTotalMasuk > 0 || $jahitSelesai > 0 || $jahitIsSelesai,
        ],
        [
            'key' => 'finishing',
            'label' => 'Finishing',
            'lines' => [
                'Selesai: ' . number_format($finishingSelesai, 0, ',', '.') . ' pcs',
            ],
            'progress' => $finishingComplete ? 100 : $finishingProgress,
            'isComplete' => $finishingComplete,
            'started' => $finishingTotalMasuk > 0 || $finishingSelesai > 0 || $finishingIsSelesai,
        ],
        [
            'key' => 'selesai',
            'label' => 'Selesai',
            'lines' => [
                number_format($diterimaAdmin, 0, ',', '.') . ' pcs diterima admin',
            ],
            'progress' => $readyComplete ? 100 : ($diterimaAdmin > 0 && $detail->estimasi_pcs > 0 ? min(100, (int) round($diterimaAdmin / $detail->estimasi_pcs * 100)) : 0),
            'isComplete' => $readyComplete,
            'started' => $diterimaAdmin > 0 || $isFinished,
        ],
    ];
@endphp
<!-- Alur Produksi (Vertical Timeline) -->
<div class="relative">
    @foreach($steps as $index => $step)
        @php
            $circumference = 100.5;
            $dashOffset = $circumference * (1 - $step['progress'] / 100);
            $isComplete = $step['isComplete'];
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
                        $lineClass = $nextStep['isComplete'] ? 'bg-green-400' : ($nextStep['started'] ? 'bg-[#0F034D]/40' : 'bg-gray-200');
                    @endphp
                    <div class="w-0.5 grow {{ $lineClass }} mt-1 transition-all duration-500" style="min-height: 1rem;"></div>
                @endif
            </div>

            {{-- Right: Label + Sub-info --}}
            <div class="pt-1 pb-0.5 min-w-0 flex-1">
                <p class="text-xs font-bold {{ $labelColor }} leading-tight">{{ $step['label'] }}</p>
                @foreach($step['lines'] as $line)
                    <p class="text-[11px] text-gray-500 leading-tight mt-0.5">{{ $line }}</p>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

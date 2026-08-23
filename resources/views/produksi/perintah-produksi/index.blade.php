<x-layouts.produksi>
    <x-slot:header>
        Pekerjaan Saya
    </x-slot:header>

    @php
        $roleLabels = ['potong' => 'Potong', 'jahit' => 'Jahit', 'finishing' => 'Finishing'];
        $stageLabel = $roleLabels[$role] ?? 'Produksi';
    @endphp

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-5">
        <div class="px-5 pt-5 pb-4 border-b border-gray-100">
            <h3 class="font-bold text-[#0F034D]">Daftar Perintah Produksi</h3>
            <p class="text-sm text-gray-500 mt-1">Perintah produksi yang sudah disetujui owner dan siap dikerjakan.</p>
        </div>

        @php
            $statusOptions = [
                '' => 'Semua Status',
                'disetujui' => 'Disetujui',
                'dalam_produksi' => 'Dalam Produksi',
            ];
            $sortOptions = [
                'mulai_terlama' => 'Urutan Pengerjaan',
                'mulai_terbaru' => 'Mulai terbaru',
                'terbaru' => 'Terbaru dibuat',
                'wo_asc' => 'Nomor perintah produksi A-Z',
            ];
            $hasActiveFilters = $search !== '' || $status !== '' || $filterTanggal !== '' || $sort !== 'mulai_terlama';
        @endphp

        <div class="px-5 py-3 bg-gray-50/50 border-b border-gray-100 flex flex-col sm:flex-row items-center gap-3 relative z-20">
            <div class="flex items-center gap-2 w-full sm:w-auto shrink-0">
                <!-- Filter Status -->
                <div class="relative" data-dropdown="status">
                    <button type="button" id="btn-dropdown-status" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white border {{ $status !== '' ? 'border-[#0F034D] text-[#0F034D] bg-blue-50/50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        <span>{{ $statusOptions[$status] ?? 'Status' }}</span>
                        @if($status !== '')
                            <span class="flex h-2 w-2 relative"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#0F034D] opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-[#0F034D]"></span></span>
                        @endif
                        <svg class="dropdown-arrow w-3.5 h-3.5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div id="dropdown-status" class="absolute left-0 mt-2 w-full sm:w-56 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden py-2">
                        <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">Status Produksi</div>
                        <div class="px-2 space-y-0.5">
                            @foreach($statusOptions as $value => $label)
                                <a href="{{ request()->fullUrlWithQuery(['status' => $value ?: null, 'page' => null]) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ $status === $value ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">{{ $label }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Filter Tanggal -->
                <div class="relative" data-dropdown="tanggal">
                    <button type="button" id="btn-dropdown-tanggal" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white border {{ $filterTanggal !== '' ? 'border-[#0F034D] text-[#0F034D] bg-blue-50/50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ $filterTanggal !== '' ? \Carbon\Carbon::parse($filterTanggal)->format('d M Y') : 'Tanggal' }}
                        <svg class="dropdown-arrow w-3.5 h-3.5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div id="dropdown-tanggal" class="absolute left-0 mt-2 w-72 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden p-4">
                        <form method="GET" action="{{ route('produksi.perintah-produksi.index') }}">
                            @if($status) <input type="hidden" name="status" value="{{ $status }}"> @endif
                            @if($sort !== 'mulai_terlama') <input type="hidden" name="sort" value="{{ $sort }}"> @endif
                            @if($search) <input type="hidden" name="search" value="{{ $search }}"> @endif
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Pilih Tanggal Mulai</label>
                                    <input type="date" name="tanggal" value="{{ $filterTanggal }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors">
                                </div>
                                <div class="flex gap-2 pt-1">
                                    <button type="submit" class="flex-1 py-2 px-3 bg-[#0F034D] hover:bg-[#1a0a6e] text-white text-xs font-semibold rounded-lg transition-colors">Terapkan</button>
                                    <a href="{{ route('produksi.perintah-produksi.index', ['status' => $status, 'sort' => $sort, 'search' => $search]) }}" class="py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold rounded-lg transition-colors">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Sort -->
                <div class="relative" data-dropdown="sort">
                    <button type="button" id="btn-dropdown-sort" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white border {{ $sort !== 'mulai_terlama' ? 'border-[#0F034D] text-[#0F034D] bg-blue-50/50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 16 4 4 4-4"/><path d="M7 20V4"/><path d="m21 8-4-4-4 4"/><path d="M17 4v16"/></svg>
                        {{ $sortOptions[$sort] ?? 'Urutan Pengerjaan' }}
                        <svg class="dropdown-arrow w-3.5 h-3.5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div id="dropdown-sort" class="absolute left-0 mt-2 w-full sm:w-56 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 opacity-0 scale-95 pointer-events-none transition-all duration-200 z-50 origin-top-left hidden py-2">
                        <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">Urutan</div>
                        <div class="px-2 space-y-0.5">
                            @foreach($sortOptions as $value => $label)
                                <a href="{{ request()->fullUrlWithQuery(['sort' => $value, 'page' => null]) }}" class="block px-3 py-2 text-sm rounded-lg transition-colors {{ $sort === $value ? 'bg-[#0F034D]/5 text-[#0F034D] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">{{ $label }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            @if($hasActiveFilters)
                <a href="{{ route('produksi.perintah-produksi.index') }}" title="Hapus Semua Filter" class="hidden sm:flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 rounded-xl transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </a>
            @endif

            <!-- Search -->
            <div class="flex-1 w-full">
                <form method="GET" action="{{ route('produksi.perintah-produksi.index') }}" class="relative w-full">
                    @if($status) <input type="hidden" name="status" value="{{ $status }}"> @endif
                    @if($sort !== 'mulai_terlama') <input type="hidden" name="sort" value="{{ $sort }}"> @endif
                    @if($filterTanggal) <input type="hidden" name="tanggal" value="{{ $filterTanggal }}"> @endif
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nomor perintah produksi untuk mencari..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors bg-white shadow-sm">
                </form>
            </div>
        </div>
    </div>

    @if($perintahProduksi->count() > 0)
        <div class="space-y-4 lg:grid lg:grid-cols-2 lg:gap-5 lg:space-y-0">
            @foreach($perintahProduksi as $wo)
                @php
                    $fifoIndex = $loop->iteration + (($perintahProduksi->currentPage() - 1) * $perintahProduksi->perPage());
                    $totalRoll = $wo->details->sum('qty_roll_pakai');
                    $totalEstimasi = $wo->details->sum('estimasi_pcs');
                    $displayDetails = $wo->details;

                    // Hitung progres WO level
                    $countDetails = $wo->details->count();
                    $doneDetails = 0;
                    $inProgressDetails = 0;

                    foreach($wo->details as $d) {
                        $sv = $wo->stokVirtual->where('id_detail_perintah', $d->id)->first();
                        $isDone = (bool) ($sv?->is_selesai);
                        if ($role === 'potong') {
                            $isStarted = (int)$d->qty_pcs_potong > 0;
                        } else {
                            $isStarted = $sv && (((int)$sv->total_selesai + (int)$sv->total_reject > 0) || (int)$sv->qty_hold > 0);
                        }
                        if ($isDone) $doneDetails++;
                        elseif ($isStarted) $inProgressDetails++;
                    }

                    $woDone = $countDetails > 0 && $doneDetails === $countDetails;
                    $woInProgress = $doneDetails > 0 || $inProgressDetails > 0;
                @endphp
                <div class="bg-white rounded-2xl border border-[#0F034D]/10 p-4 sm:p-5 shadow-[0_10px_30px_rgba(15,3,77,0.08)] h-full flex flex-col">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 mb-4">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h4 class="font-bold text-[#0F034D] text-base">{{ $wo->nomor_wo }}</h4>
                                <span class="rounded-full bg-[#0F034D]/5 px-2.5 py-1 text-xs font-bold text-[#0F034D]">Prioritas Pengerjaan #{{ $fifoIndex }}</span>

                                @if($woDone)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-1 text-xs font-bold text-emerald-700">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Selesai ({{ $doneDetails }}/{{ $countDetails }})
                                    </span>
                                @elseif($woInProgress)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-200 px-2.5 py-1 text-xs font-bold text-amber-700">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                        Diproses ({{ $doneDetails }}/{{ $countDetails }} produk selesai)
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 border border-gray-200 px-2.5 py-1 text-xs font-medium text-gray-600">
                                        Belum Dikerjakan
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400">Periode: {{ \Carbon\Carbon::parse($wo->tgl_mulai)->format('d M Y') }} - {{ $wo->tgl_selesai ? \Carbon\Carbon::parse($wo->tgl_selesai)->format('d M Y') : '-' }}</p>
                        </div>
                        <div class="flex flex-wrap sm:flex-col items-start sm:items-end gap-1.5 shrink-0">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">{{ ucfirst(str_replace('_', ' ', $wo->status_produksi)) }}</span>
                            @php $dlInfo = $wo->getDeadlineInfo(); @endphp
                            @if($dlInfo['statusType'] !== 'none' && $dlInfo['statusType'] !== 'normal')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $dlInfo['badgeClass'] }}">
                                    {{ $dlInfo['label'] }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div class="rounded-xl bg-[#0F034D]/5 border border-[#0F034D]/10 p-3"><p class="text-[11px] text-[#0F034D]/60 mb-0.5">Jenis Produk</p><p class="font-bold text-sm text-[#0F034D]">{{ $wo->details->count() }} Produk</p></div>
                        <div class="rounded-xl bg-[#0F034D]/5 border border-[#0F034D]/10 p-3"><p class="text-[11px] text-[#0F034D]/60 mb-0.5">Total Roll</p><p class="font-bold text-sm text-[#0F034D]">{{ number_format($totalRoll, 0, ',', '.') }} Roll</p></div>
                    </div>

                    <div class="space-y-3 mb-4 flex-1">
                        @foreach($displayDetails as $detail)
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

                                $stokV = $wo->stokVirtual->where('id_detail_perintah', $detail->id)->first();
                                $isDetDone = (bool) ($stokV?->is_selesai);

                                if ($role === 'potong') {
                                    $detSelesai = (int) ($detail->qty_pcs_potong ?? 0);
                                    $detTarget = (int) $detail->estimasi_pcs;
                                    $detProg = $detTarget > 0 ? min(100, (int) round($detSelesai / $detTarget * 100)) : 0;
                                    $progText = number_format($detSelesai, 0, ',', '.') . ' / ' . number_format($detTarget, 0, ',', '.') . ' pcs';
                                    $hasStarted = $detSelesai > 0;
                                } else {
                                    $detHold = (int) ($stokV?->qty_hold ?? 0);
                                    $detDoneCount = (int) ($stokV?->total_selesai ?? 0);
                                    $detRejCount = (int) ($stokV?->total_reject ?? 0);
                                    $detTarget = $detHold + $detDoneCount + $detRejCount;
                                    $detSelesai = $detDoneCount + $detRejCount;
                                    $detProg = $detTarget > 0 ? min(100, (int) round($detSelesai / $detTarget * 100)) : 0;
                                    $progText = $detTarget > 0 ? (number_format($detSelesai, 0, ',', '.') . ' / ' . number_format($detTarget, 0, ',', '.') . ' pcs') : 'Belum dipegang';
                                    $hasStarted = $stokV && ($detSelesai > 0 || $detHold > 0);
                                }
                            @endphp

                            <div class="rounded-xl bg-white border border-gray-200 p-3 shadow-sm hover:border-[#0F034D]/30 transition-all">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <p class="text-sm font-bold text-[#0F034D] truncate">
                                                {{ $detail->produk->nama_produk ?? '-' }} - {{ ucfirst($detail->produk->warna ?? '-') }}
                                            </p>
                                            <span class="inline-block w-2.5 h-2.5 rounded-full shrink-0 {{ $needsStroke ? 'ring-1 ring-gray-300' : '' }}" style="background-color: {{ $warnaDot }}" title="Warna {{ ucfirst($detail->produk->warna ?? '-') }}"></span>
                                        </div>
                                        <p class="text-xs text-gray-500 truncate mt-0.5">{{ $detail->bahanBaku->nama_bahan ?? '-' }} - {{ ucfirst($detail->bahanBaku->warna ?? '-') }} - {{ number_format($detail->qty_roll_pakai ?? 0) }} Roll</p>
                                    </div>

                                    {{-- Status Badge per Produk (Hanya: Selesai [Hijau], Diproses [Amber], Belum Input [Abu-abu]) --}}
                                    @if($isDetDone)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            Selesai
                                        </span>
                                    @elseif($hasStarted)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shrink-0">
                                            Diproses
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-gray-100 text-gray-500 border border-gray-200 shrink-0">
                                            Belum Input
                                        </span>
                                    @endif
                                </div>

                                {{-- Progress bar per produk --}}
                                <div class="mt-2 pt-2 border-t border-gray-100">
                                    <div class="flex items-center justify-between text-[11px] mb-1">
                                        <span class="font-medium text-gray-500">Progres Input Pekerjaan</span>
                                        <span class="font-bold text-[#0F034D]">{{ $progText }} ({{ $detProg }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-1.5 rounded-full transition-all duration-300 {{ $isDetDone ? 'bg-emerald-500' : ($hasStarted ? 'bg-amber-500' : 'bg-gray-200') }}" style="width: {{ $detProg }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-3 border-t border-gray-100 mt-auto">
                        @if($role === 'potong' && $wo->status_produksi === 'disetujui')
                            <form action="{{ route('produksi.perintah-produksi.mulai', $wo) }}" method="POST"
                                data-swal-confirm
                                data-confirm-title="Mulai Kerjakan WO Ini?"
                                data-confirm-message="Status perintah kerja {{ $wo->nomor_wo }} akan berubah menjadi Dalam Produksi."
                                data-confirm-button="Ya, Mulai Kerjakan">
                                @csrf
                                <button type="submit" class="w-full py-2.5 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-xl transition-colors shadow-md shadow-green-600/20 block text-center cursor-pointer">
                                    Mulai Kerjakan
                                </button>
                            </form>
                        @else
                            <a href="{{ route('produksi.perintah-produksi.show', $wo) }}" class="block w-full text-center py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-xs font-bold rounded-xl transition-all duration-200 cursor-pointer shadow-md shadow-[#0F034D]/10">
                                {{ $woDone ? 'Lihat Detail Pekerjaan' : 'Input Hasil Pekerjaan' }}
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6"><x-pagination.custom-global-pagination :paginator="$perintahProduksi" /></div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <p class="text-[#0F034D] font-semibold text-sm">Belum ada pekerjaan</p>
            <p class="text-gray-500 text-sm mt-1">Daftar perintah produksi akan muncul ketika disetujui owner.</p>
        </div>
    @endif

    @vite([
        'resources/js/admin/filter-dropdown.js',
    ])
</x-layouts.produksi>

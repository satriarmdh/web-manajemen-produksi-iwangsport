<x-layouts.produksi>
    <x-slot:header>
        Pekerjaan Saya
    </x-slot:header>

    @php
        $roleLabels = ['potong' => 'Potong', 'jahit' => 'Jahit', 'finishing' => 'Finishing'];
        $stageLabel = $roleLabels[$role] ?? 'Produksi';
    @endphp

    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-[#0F034D]">Daftar Work Order</h3>
                <p class="text-sm text-gray-500 mt-1">WO yang sudah disetujui owner dan siap diproses pada tahap {{ $stageLabel }}.</p>
            </div>
        </div>
    </div>

    @php
        $statusOptions = [
            '' => 'Semua Status',
            'disetujui' => 'Disetujui',
            'dalam_produksi' => 'Dalam Produksi',
        ];
        $sortOptions = [
            'terbaru' => 'Terbaru dibuat',
            'mulai_terbaru' => 'Mulai terbaru',
            'mulai_terlama' => 'Mulai terlama',
            'wo_asc' => 'Nomor WO A-Z',
        ];
        $hasActiveFilters = $search !== '' || $status !== '' || $sort !== 'terbaru';
    @endphp

    <form method="GET" action="{{ route('produksi.perintah-produksi.index') }}" class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm mb-5">
        <div class="grid gap-3 {{ $hasActiveFilters ? 'lg:grid-cols-[1fr_180px_180px_auto]' : 'lg:grid-cols-[1fr_180px_180px]' }} lg:items-end">
            <div>
                <label for="search-pekerjaan" class="block text-xs font-bold text-gray-500 mb-1.5">Cari pekerjaan</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21 21-4.35-4.35"></path><circle cx="11" cy="11" r="8"></circle></svg>
                    <input id="search-pekerjaan" type="text" name="search" value="{{ $search }}" placeholder="Cari nomor WO, produk, warna, bahan..." class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-4 py-3 text-sm focus:bg-white focus:border-[#0F034D] focus:ring-1 focus:ring-[#0F034D]/20" data-search-input>
                </div>
            </div>

            <div class="relative" data-custom-dropdown>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">Status</label>
                <input type="hidden" name="status" value="{{ $status }}" data-dropdown-input>
                <button type="button" class="w-full flex items-center justify-between gap-2 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-left text-sm font-semibold text-[#0F034D]" data-dropdown-button>
                    <span data-dropdown-label>{{ $statusOptions[$status] ?? 'Semua Status' }}</span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" data-dropdown-arrow fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"></path></svg>
                </button>
                <div class="absolute left-0 right-0 top-full mt-2 z-20 rounded-xl border border-gray-100 bg-white shadow-xl shadow-[#0F034D]/10 p-1 opacity-0 scale-95 pointer-events-none transition-all" data-dropdown-menu>
                    @foreach($statusOptions as $value => $label)
                        <button type="button" class="w-full text-left px-3 py-2 rounded-lg text-sm font-semibold {{ $status === $value ? 'bg-[#0F034D]/5 text-[#0F034D]' : 'text-gray-600 hover:bg-gray-50' }}" data-dropdown-option data-value="{{ $value }}" data-label="{{ $label }}">{{ $label }}</button>
                    @endforeach
                </div>
            </div>

            <div class="relative" data-custom-dropdown>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">Urutkan</label>
                <input type="hidden" name="sort" value="{{ $sort }}" data-dropdown-input>
                <button type="button" class="w-full flex items-center justify-between gap-2 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-left text-sm font-semibold text-[#0F034D]" data-dropdown-button>
                    <span data-dropdown-label>{{ $sortOptions[$sort] ?? 'Terbaru dibuat' }}</span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" data-dropdown-arrow fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"></path></svg>
                </button>
                <div class="absolute left-0 right-0 top-full mt-2 z-20 rounded-xl border border-gray-100 bg-white shadow-xl shadow-[#0F034D]/10 p-1 opacity-0 scale-95 pointer-events-none transition-all" data-dropdown-menu>
                    @foreach($sortOptions as $value => $label)
                        <button type="button" class="w-full text-left px-3 py-2 rounded-lg text-sm font-semibold {{ $sort === $value ? 'bg-[#0F034D]/5 text-[#0F034D]' : 'text-gray-600 hover:bg-gray-50' }}" data-dropdown-option data-value="{{ $value }}" data-label="{{ $label }}">{{ $label }}</button>
                    @endforeach
                </div>
            </div>

            @if($hasActiveFilters)
                <div class="lg:flex lg:items-end">
                    <a href="{{ route('produksi.perintah-produksi.index') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-red-50 border border-red-100 px-4 py-3 text-sm font-bold text-red-600 hover:bg-red-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
                        Reset
                    </a>
                </div>
            @endif
        </div>
    </form>

    @if($perintahProduksi->count() > 0)
        <div class="space-y-4 lg:grid lg:grid-cols-2 lg:gap-5 lg:space-y-0">
            @foreach($perintahProduksi as $wo)
                @php
                    $totalRoll = $wo->details->sum('qty_roll_pakai');
                    $totalEstimasi = $wo->details->sum('estimasi_pcs');
                    $displayDetails = $wo->details->take(2);
                    $remainingDetails = max(0, $wo->details->count() - $displayDetails->count());
                @endphp
                <div class="bg-white rounded-2xl border border-[#0F034D]/10 p-4 sm:p-5 shadow-[0_10px_30px_rgba(15,3,77,0.08)] h-full flex flex-col">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div>
                            <h4 class="font-bold text-[#0F034D]">{{ $wo->nomor_wo }}</h4>
                            <p class="text-xs text-gray-400 mt-1">Mulai {{ \Carbon\Carbon::parse($wo->tgl_mulai)->format('d M Y') }}</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">{{ ucfirst(str_replace('_', ' ', $wo->status_produksi)) }}</span>
                    </div>

                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div class="rounded-xl bg-[#0F034D]/5 border border-[#0F034D]/10 p-3"><p class="text-[11px] text-[#0F034D]/60">Jenis Produk</p><p class="font-bold text-sm text-[#0F034D]">{{ $wo->details->count() }}</p></div>
                        <div class="rounded-xl bg-[#0F034D]/5 border border-[#0F034D]/10 p-3"><p class="text-[11px] text-[#0F034D]/60">Total Roll</p><p class="font-bold text-sm text-[#0F034D]">{{ number_format($totalRoll, 0, ',', '.') }}</p></div>
                        <div class="rounded-xl bg-green-50 border border-green-100 p-3"><p class="text-[11px] text-green-700/70">Total Estimasi</p><p class="font-bold text-sm text-green-800">{{ number_format($totalEstimasi, 0, ',', '.') }} pcs</p></div>
                    </div>

                    <div class="space-y-2 mb-4 flex-1">
                        @foreach($displayDetails as $detail)
                            <div class="rounded-xl bg-white border border-gray-200 px-3 py-3 shadow-sm">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="min-w-0">
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
                                        <div class="flex items-center gap-2 min-w-0">
                                            <p class="text-sm font-bold text-[#0F034D] truncate">
                                                {{ $detail->produk->nama_produk ?? '-' }} - {{ ucfirst($detail->produk->warna ?? '-') }}
                                            </p>
                                            <span class="inline-block w-3 h-3 rounded-full shrink-0 {{ $needsStroke ? 'ring-1 ring-gray-300' : '' }}" style="background-color: {{ $warnaDot }}" title="Warna {{ ucfirst($detail->produk->warna ?? '-') }}"></span>
                                        </div>
                                        <p class="text-xs text-gray-500 truncate mt-0.5">Bahan: {{ $detail->bahanBaku->nama_bahan ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="rounded-lg bg-[#0F034D]/5 border border-[#0F034D]/10 px-3 py-2">
                                        <p class="text-[10px] text-[#0F034D] uppercase tracking-wide">Roll produk ini</p>
                                        <p class="text-sm font-bold text-[#0F034D]">{{ number_format($detail->qty_roll_pakai, 0, ',', '.') }} roll</p>
                                    </div>
                                    <div class="rounded-lg bg-green-50 border border-green-100 px-3 py-2">
                                        <p class="text-[10px] text-green-700/70 uppercase tracking-wide">Estimasi hasil</p>
                                        <p class="text-sm font-bold text-green-800">{{ number_format($detail->estimasi_pcs, 0, ',', '.') }} pcs</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @if($remainingDetails > 0)
                            <div class="rounded-xl bg-[#0F034D]/5 border border-dashed border-[#0F034D]/20 px-3 py-3 text-center">
                                <p class="text-sm font-bold text-[#0F034D]">+{{ $remainingDetails }} produk lainnya</p>
                                <p class="text-xs text-gray-500 mt-0.5">Lihat detail pekerjaan untuk daftar lengkap dan input hasil.</p>
                            </div>
                        @endif
                    </div>

                    <a href="{{ route('produksi.perintah-produksi.show', $wo) }}" class="block w-full text-center py-2.5 rounded-xl bg-[#0F034D] text-white text-sm font-semibold shadow-md shadow-[#0F034D]/20 hover:bg-[#24116f] transition-colors mt-auto">Detail pekerjaan & input hasil</a>
                </div>
            @endforeach
        </div>
        <div class="mt-5">{{ $perintahProduksi->links('pagination::tailwind') }}</div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 p-10 text-center shadow-sm">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"></path></svg>
            </div>
            <p class="font-semibold text-[#0F034D]">Belum ada pekerjaan</p>
            <p class="text-sm text-gray-500 mt-1">WO yang sudah disetujui owner akan muncul di sini.</p>
        </div>
    @endif
    <script>
        document.querySelectorAll('[data-custom-dropdown]').forEach((dropdown) => {
            const button = dropdown.querySelector('[data-dropdown-button]');
            const menu = dropdown.querySelector('[data-dropdown-menu]');
            const input = dropdown.querySelector('[data-dropdown-input]');
            const label = dropdown.querySelector('[data-dropdown-label]');
            const arrow = dropdown.querySelector('[data-dropdown-arrow]');

            button?.addEventListener('click', (event) => {
                event.stopPropagation();
                const isOpen = menu.classList.contains('opacity-100');

                document.querySelectorAll('[data-dropdown-menu]').forEach((otherMenu) => {
                    otherMenu.classList.remove('opacity-100', 'scale-100');
                    otherMenu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                });
                document.querySelectorAll('[data-dropdown-arrow]').forEach((otherArrow) => otherArrow.classList.remove('rotate-180'));

                if (!isOpen) {
                    menu.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
                    menu.classList.add('opacity-100', 'scale-100');
                    arrow?.classList.add('rotate-180');
                }
            });

            dropdown.querySelectorAll('[data-dropdown-option]').forEach((option) => {
                option.addEventListener('click', () => {
                    input.value = option.dataset.value ?? '';
                    label.textContent = option.dataset.label ?? option.textContent.trim();
                    menu.classList.remove('opacity-100', 'scale-100');
                    menu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                    arrow?.classList.remove('rotate-180');
                    dropdown.closest('form')?.submit();
                });
            });
        });

        let searchSubmitTimer;
        document.querySelectorAll('[data-search-input]').forEach((input) => {
            input.addEventListener('input', () => {
                clearTimeout(searchSubmitTimer);
                searchSubmitTimer = setTimeout(() => {
                    input.closest('form')?.submit();
                }, 600);
            });
        });

        document.addEventListener('click', () => {
            document.querySelectorAll('[data-dropdown-menu]').forEach((menu) => {
                menu.classList.remove('opacity-100', 'scale-100');
                menu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
            });
            document.querySelectorAll('[data-dropdown-arrow]').forEach((arrow) => arrow.classList.remove('rotate-180'));
        });
    </script>
</x-layouts.produksi>

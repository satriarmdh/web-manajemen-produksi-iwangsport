<x-layouts.produksi>
    <x-slot:header>Ajuan Saya</x-slot:header>

    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-[#0F034D]">Ajuan Saya</h3>
                <p class="text-sm text-gray-500 mt-1">Buat pengajuan pengambilan barang dan pantau status ajuan yang sudah Anda kirim.</p>
            </div>
            <div class="grid grid-cols-3 gap-2 sm:w-auto">
                <div class="rounded-xl bg-[#0F034D]/5 border border-[#0F034D]/10 px-3 py-2">
                    <p class="text-[10px] uppercase tracking-wide text-[#0F034D]/60">Ready</p>
                    <p class="text-sm font-bold text-[#0F034D]">{{ number_format($totalQtyReady, 0, ',', '.') }} pcs</p>
                </div>
                <div class="rounded-xl bg-indigo-50 border border-indigo-100 px-3 py-2">
                    <p class="text-[10px] uppercase tracking-wide text-indigo-700/70">Perintah</p>
                    <p class="text-sm font-bold text-indigo-700">{{ $totalPerintahReady }}</p>
                </div>
                <div class="rounded-xl bg-green-50 border border-green-100 px-3 py-2">
                    <p class="text-[10px] uppercase tracking-wide text-green-700/70">Produk</p>
                    <p class="text-sm font-bold text-green-700">{{ $totalProdukReady }}</p>
                </div>
            </div>
        </div>
    </div>


    <div class="bg-white rounded-2xl border border-gray-100 p-2 shadow-sm mb-5 grid grid-cols-2 gap-2">
        <button type="button" data-ajuan-tab-button="input" class="rounded-xl px-4 py-3 text-sm font-bold transition-colors bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20">Input Ajuan</button>
        <button type="button" data-ajuan-tab-button="riwayat" class="rounded-xl px-4 py-3 text-sm font-bold transition-colors text-gray-500 hover:bg-gray-50">
            Riwayat Ajuan Saya
            @if($totalAjuanSayaPending > 0)
                <span class="ml-1 inline-flex min-w-5 h-5 items-center justify-center rounded-full bg-red-500 px-1.5 text-[11px] font-bold text-white">{{ $totalAjuanSayaPending }}</span>
            @endif
        </button>
    </div>

    <section data-ajuan-tab-panel="input" class="space-y-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-4">
            <div class="px-5 pt-5 pb-4 border-b border-gray-100">
                <h3 class="font-bold text-[#0F034D]">Daftar Perintah Produksi</h3>
                <p class="text-sm text-gray-500 mt-1">Cari perintah produksi atau produk ready yang ingin diajukan.</p>
            </div>

            @php
                $hasActiveFilters = $search !== '' || $filterSumber !== '' || $filterTanggal !== '' || $sort !== 'fifo';
            @endphp

            <div class="px-5 py-3 bg-gray-50/50 border-b border-gray-100 flex flex-col sm:flex-row items-center gap-3 relative z-20">
                <div class="flex items-center gap-2 w-full sm:w-auto shrink-0">
                    <!-- Filter Tanggal -->
                    <div class="relative">
                        <button type="button" data-custom-dropdown-button="tanggal" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white border {{ $filterTanggal !== '' ? 'border-[#0F034D] text-[#0F034D] bg-blue-50/50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>{{ $filterTanggal !== '' ? \Carbon\Carbon::parse($filterTanggal)->format('d M Y') : 'Tanggal' }}</span>
                            @if($filterTanggal !== '')
                                <span class="flex h-2 w-2 relative"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#0F034D] opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-[#0F034D]"></span></span>
                            @endif
                            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform" data-custom-dropdown-arrow="tanggal" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div data-custom-dropdown-menu="tanggal" class="hidden absolute left-0 mt-2 w-72 rounded-xl border border-gray-100 bg-white shadow-[0_10px_40px_rgba(15,3,77,0.12)] z-50 p-4">
                            <form method="GET" action="{{ route('produksi.ajuan-pengambilan.index') }}">
                                @if($search) <input type="hidden" name="search" value="{{ $search }}"> @endif
                                @if($filterSumber) <input type="hidden" name="sumber" value="{{ $filterSumber }}"> @endif
                                @if($sort !== 'fifo') <input type="hidden" name="sort" value="{{ $sort }}"> @endif
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Pilih Tanggal Mulai</label>
                                        <input type="date" name="tanggal" value="{{ $filterTanggal }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors">
                                    </div>
                                    <div class="flex gap-2 pt-1">
                                        <button type="submit" class="flex-1 py-2 px-3 bg-[#0F034D] hover:bg-[#1a0a6e] text-white text-xs font-semibold rounded-lg transition-colors cursor-pointer">Terapkan</button>
                                        <a href="{{ route('produksi.ajuan-pengambilan.index', array_filter(['sumber' => $filterSumber, 'sort' => $sort !== 'fifo' ? $sort : null, 'search' => $search])) }}" class="py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold rounded-lg transition-colors text-center">Reset</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Sumber -->
                    <div class="relative">
                        <button type="button" data-custom-dropdown-button="sumber" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white border {{ $filterSumber !== '' ? 'border-[#0F034D] text-[#0F034D] bg-blue-50/50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <span class="truncate max-w-[100px]">
                                @if($filterSumber !== '')
                                    @php $sumberTerpilih = $sumberOptions->firstWhere('id', (int) $filterSumber); @endphp
                                    {{ $sumberTerpilih ? $sumberTerpilih->name : 'Sumber' }}
                                @else
                                    Semua Sumber
                                @endif
                            </span>
                            @if($filterSumber !== '')
                                <span class="flex h-2 w-2 relative"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#0F034D] opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-[#0F034D]"></span></span>
                            @endif
                            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform" data-custom-dropdown-arrow="sumber" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div data-custom-dropdown-menu="sumber" class="hidden absolute left-0 mt-2 w-60 rounded-xl border border-gray-100 bg-white shadow-[0_10px_40px_rgba(15,3,77,0.12)] z-40 p-2">
                            <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">Sumber Barang</div>
                            <div class="space-y-0.5">
                                <button type="button" data-custom-dropdown-option="sumber" data-value="" class="w-full flex items-center justify-between rounded-lg px-3 py-2 text-left text-sm font-medium hover:bg-gray-50 {{ $filterSumber === '' ? 'text-[#0F034D] bg-[#0F034D]/5' : 'text-gray-600' }}">
                                    <span>Semua sumber</span>
                                    @if($filterSumber === '')<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>@endif
                                </button>
                                @foreach($sumberOptions as $sumber)
                                    <button type="button" data-custom-dropdown-option="sumber" data-value="{{ $sumber->id }}" class="w-full flex items-center justify-between rounded-lg px-3 py-2 text-left text-sm font-medium hover:bg-gray-50 {{ (string) $filterSumber === (string) $sumber->id ? 'text-[#0F034D] bg-[#0F034D]/5' : 'text-gray-600' }}">
                                        <span class="truncate">{{ $sumber->name }} - {{ ucfirst($sumber->role) }}</span>
                                        @if((string) $filterSumber === (string) $sumber->id)<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>@endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Sort -->
                    <div class="relative">
                        @php
                            $sortLabels = ['fifo' => 'Urutan Pengerjaan', 'wo_az' => 'Nomor perintah A-Z', 'produk_az' => 'Produk A-Z', 'qty_terbesar' => 'Qty terbesar', 'qty_terkecil' => 'Qty terkecil'];
                        @endphp
                        <button type="button" data-custom-dropdown-button="sort" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white border {{ $sort !== 'fifo' ? 'border-[#0F034D] text-[#0F034D] bg-blue-50/50' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} rounded-xl text-sm font-medium transition-colors shadow-sm cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 16 4 4 4-4"/><path d="M7 20V4"/><path d="m21 8-4-4-4 4"/><path d="M17 4v16"/></svg>
                            <span class="truncate">{{ $sortLabels[$sort] ?? 'Urutan Pengerjaan' }}</span>
                            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform" data-custom-dropdown-arrow="sort" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div data-custom-dropdown-menu="sort" class="hidden absolute left-0 mt-2 w-56 rounded-xl border border-gray-100 bg-white shadow-[0_10px_40px_rgba(15,3,77,0.12)] z-40 p-2">
                            <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">Urutan</div>
                            <div class="space-y-0.5">
                                @foreach($sortLabels as $value => $label)
                                    <button type="button" data-custom-dropdown-option="sort" data-value="{{ $value }}" class="w-full flex items-center justify-between rounded-lg px-3 py-2 text-left text-sm font-medium hover:bg-gray-50 {{ $sort === $value ? 'text-[#0F034D] bg-[#0F034D]/5' : 'text-gray-600' }}">
                                        <span>{{ $label }}</span>
                                        @if($sort === $value)<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>@endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                @if($hasActiveFilters)
                    <a href="{{ route('produksi.ajuan-pengambilan.index') }}" title="Hapus Semua Filter" class="hidden sm:flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 rounded-xl transition-colors shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </a>
                @endif

                <!-- Search -->
                <div class="flex-1 w-full">
                    <form action="{{ route('produksi.ajuan-pengambilan.index') }}" method="GET" class="relative w-full">
                        <input type="hidden" name="sumber" value="{{ $filterSumber }}">
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        @if($filterTanggal) <input type="hidden" name="tanggal" value="{{ $filterTanggal }}"> @endif
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input id="ajuan-search" type="text" name="search" value="{{ $search }}" placeholder="Ketik nomor perintah produksi, produk, warna..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors bg-white shadow-sm">
                    </form>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-800">
            Ikuti urutan dari atas ke bawah. Perintah Produksi dengan tanggal mulai lebih lama sebaiknya dikerjakan lebih dulu.
        </div>

        @forelse($barangReadyPerWo as $idPerintah => $stokDalamWo)
            @php
                $fifoIndex = $loop->iteration;
                $perintah = $stokDalamWo->first()->perintahProduksi;
                $totalReady = $stokDalamWo->sum(fn($s) => max(0, (int) $s->total_selesai - (int) $s->total_dikeluarkan));
                $totalProduk = $stokDalamWo->count();
            @endphp
            <details class="group bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <summary class="list-none cursor-pointer p-4 sm:p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h4 class="font-bold text-[#0F034D] text-sm sm:text-base">{{ $perintah->nomor_wo ?? 'Perintah Produksi' }}</h4>
                                <span class="rounded-full bg-[#0F034D]/5 px-2.5 py-1 text-xs font-bold text-[#0F034D]">Prioritas #{{ $fifoIndex }}</span>
                                <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-bold text-green-700">{{ $totalProduk }} produk ready</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Periode: {{ $perintah?->tgl_mulai ? \Carbon\Carbon::parse($perintah->tgl_mulai)->format('d M Y') : '-' }} - {{ $perintah?->tgl_selesai ? \Carbon\Carbon::parse($perintah->tgl_selesai)->format('d M Y') : '-' }}</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 transition-transform group-open:rotate-180 shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"></path></svg>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mt-4">
                        <div class="rounded-xl bg-green-50 border border-green-100 p-3">
                            <p class="text-[10px] uppercase tracking-wide text-green-700/70">Total ready</p>
                            <p class="text-sm font-bold text-green-700">{{ number_format($totalReady, 0, ',', '.') }} pcs</p>
                        </div>
                        <div class="rounded-xl bg-gray-50 border border-gray-100 p-3">
                            <p class="text-[10px] uppercase tracking-wide text-gray-400">Sumber</p>
                            <p class="text-sm font-bold text-gray-700">{{ ucfirst($stokDalamWo->first()->peran) }}</p>
                        </div>
                    </div>
                </summary>
                <form action="{{ route('produksi.ajuan-pengambilan.store') }}" method="POST"
                    data-swal-confirm
                    data-confirm-title="Kirim Ajuan Pengambilan?"
                    data-confirm-message="Ajuan akan dikirim ke karyawan sebelumnya untuk diverifikasi. Pastikan jumlah yang diajukan sudah benar."
                    data-confirm-button="Ya, Ajukan">
                    @csrf
                    <div class="border-t border-gray-100 bg-gray-50/50 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-white text-[11px] uppercase tracking-wide text-gray-400">
                                <tr>
                                    <th class="px-4 py-3 text-left font-bold">Produk</th>
                                    <th class="px-4 py-3 text-left font-bold">Sumber</th>
                                    <th class="px-4 py-3 text-right font-bold">Ready</th>
                                    <th class="px-4 py-3 text-left font-bold min-w-[180px]">Qty Ajuan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($stokDalamWo as $stok)
                                    <tr class="hover:bg-gray-50/70 transition-colors">
                                        <td class="px-4 py-3 align-top">
                                            <p class="font-bold text-[#0F034D] whitespace-nowrap">{{ $stok->produk->nama_produk ?? '-' }} - {{ ucfirst($stok->produk->warna ?? '-') }}</p>
                                            @if($fifoWarnings->get($stok->id))
                                                <div class="mt-2 rounded-xl border border-amber-200 bg-amber-50/70 px-3 py-3">
                                                    <div class="flex items-start gap-3">
                                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4"></path><path d="M12 17h.01"></path><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"></path></svg>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs font-bold text-amber-800">Ada perintah lebih lama</p>
                                                            <p class="mt-1 text-xs leading-relaxed text-amber-700">Masih ada {{ $stok->produk->nama_produk ?? 'produk ini' }} pada PP {{ $fifoWarnings->get($stok->id)->perintahProduksi->nomor_wo ?? '-' }}. Utamakan PP tersebut lebih dulu jika barangnya masih dibutuhkan.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 align-top whitespace-nowrap">
                                            <p class="font-semibold text-gray-700">{{ $stok->karyawan->name ?? '-' }} - {{ ucfirst($stok->peran) }}</p>
                                        </td>
                                        <td class="px-4 py-3 align-top text-right whitespace-nowrap">
                                            <span class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-700">{{ number_format(max(0, (int) $stok->total_selesai - (int) $stok->total_dikeluarkan), 0, ',', '.') }} pcs</span>
                                        </td>
                                        <td class="px-4 py-3 align-top">
                                            <input type="hidden" name="items[{{ $stok->id }}][stok_virtual_id]" value="{{ $stok->id }}">
                                            <input type="number" name="items[{{ $stok->id }}][qty_ajuan]" min="1" max="{{ max(0, (int) $stok->total_selesai - (int) $stok->total_dikeluarkan) }}" class="w-36 rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-[#0F034D] focus:ring-1 focus:ring-[#0F034D]/20" placeholder="Qty">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-gray-100 bg-white p-4 sm:p-5">
                        <label class="block text-xs font-bold uppercase tracking-wide text-gray-400 mb-2">Catatan Pengajuan</label>
                        <div class="grid gap-3 lg:grid-cols-[1fr_auto]">
                            <input type="text" name="catatan_pengaju" maxlength="1000" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-[#0F034D] focus:ring-1 focus:ring-[#0F034D]/20" placeholder="Catatan opsional untuk produk terpilih, contoh: ambil untuk dijahit hari ini">
                            <button type="submit" class="rounded-xl bg-[#0F034D] px-5 py-3 text-sm font-bold text-white hover:bg-[#24116f] transition-colors">Ajukan</button>
                        </div>
                        <p class="mt-2 text-xs text-gray-400">*Isi qty pada produk yang ingin diajukan. Produk dengan qty kosong tidak akan diajukan.</p>
                    </div>
                </form>
            </details>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center text-sm text-gray-500">Belum ada barang ready yang bisa diajukan.</div>
        @endforelse
    </section>

    @php
        $totalPending = $ajuanSaya->where('status', 'pending')->count();
        $totalDisetujui = $ajuanSaya->where('status', 'disetujui')->count();
        $totalDitolak = $ajuanSaya->where('status', 'ditolak')->count();
    @endphp
    <section data-ajuan-tab-panel="riwayat" class="hidden space-y-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="font-bold text-[#0F034D]">Riwayat Ajuan Saya</h3>
                    <p class="text-sm text-gray-500 mt-1">Status pengajuan yang pernah Anda kirim.</p>
                </div>
            </div>
            {{-- Status Toggle Filter --}}
            <div class="flex flex-wrap items-center gap-2" data-riwayat-filter>
                <button type="button" data-riwayat-status="" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold border transition-colors shadow-sm cursor-pointer bg-[#0F034D] text-white border-[#0F034D]">
                    Semua <span class="opacity-80">({{ $ajuanSaya->count() }})</span>
                </button>
                <button type="button" data-riwayat-status="pending" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold border transition-colors shadow-sm cursor-pointer bg-white text-gray-600 border-gray-200 hover:bg-gray-50">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span> Pending <span class="opacity-60">({{ $totalPending }})</span>
                </button>
                <button type="button" data-riwayat-status="disetujui" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold border transition-colors shadow-sm cursor-pointer bg-white text-gray-600 border-gray-200 hover:bg-gray-50">
                    <span class="w-2 h-2 rounded-full bg-green-400"></span> Disetujui <span class="opacity-60">({{ $totalDisetujui }})</span>
                </button>
                <button type="button" data-riwayat-status="ditolak" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold border transition-colors shadow-sm cursor-pointer bg-white text-gray-600 border-gray-200 hover:bg-gray-50">
                    <span class="w-2 h-2 rounded-full bg-red-400"></span> Ditolak <span class="opacity-60">({{ $totalDitolak }})</span>
                </button>
            </div>
        </div>
        <div class="space-y-4" data-riwayat-list>
            @forelse($ajuanSaya->groupBy('id_perintah') as $idPerintah => $ajuanDalamPerintah)
                @php
                    $perintah = $ajuanDalamPerintah->first()->perintahProduksi;
                    $totalQty = $ajuanDalamPerintah->sum('qty_ajuan');
                    $pendingCount = $ajuanDalamPerintah->where('status', 'pending')->count();
                    $approvedCount = $ajuanDalamPerintah->where('status', 'disetujui')->count();
                    $rejectedCount = $ajuanDalamPerintah->where('status', 'ditolak')->count();
                @endphp
                <details class="group rounded-2xl border border-gray-100 overflow-hidden shadow-sm" open>
                    <summary class="list-none cursor-pointer bg-[#0F034D] p-4 text-white">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-white">{{ $perintah->nomor_wo ?? 'Perintah Produksi' }}</p>
                                <p class="text-xs text-blue-200 mt-1">Periode: {{ $perintah?->tgl_mulai ? \Carbon\Carbon::parse($perintah->tgl_mulai)->format('d M Y') : '-' }} - {{ $perintah?->tgl_selesai ? \Carbon\Carbon::parse($perintah->tgl_selesai)->format('d M Y') : '-' }}</p>
                                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                    <span class="text-xs text-blue-200">{{ $ajuanDalamPerintah->count() }} ajuan - <span class="text-amber-300 font-bold bg-white/10 px-2 py-0.5 rounded-lg">{{ number_format($totalQty, 0, ',', '.') }} pcs</span> diajukan</span>
                                    @if($pendingCount > 0)
                                        <span class="inline-flex items-center rounded-full bg-amber-400/20 px-2 py-0.5 text-[10px] font-bold text-amber-300">{{ $pendingCount }} pending</span>
                                    @endif
                                    @if($approvedCount > 0)
                                        <span class="inline-flex items-center rounded-full bg-green-400/20 px-2 py-0.5 text-[10px] font-bold text-green-300">{{ $approvedCount }} disetujui</span>
                                    @endif
                                    @if($rejectedCount > 0)
                                        <span class="inline-flex items-center rounded-full bg-red-400/20 px-2 py-0.5 text-[10px] font-bold text-red-300">{{ $rejectedCount }} ditolak</span>
                                    @endif
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-blue-200 transition-transform group-open:rotate-180 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"></path></svg>
                        </div>
                    </summary>
                    <div class="divide-y divide-gray-100 bg-white">
                        @foreach($ajuanDalamPerintah as $ajuan)
                            <div class="p-4" data-riwayat-ajuan-status="{{ $ajuan->status }}">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex items-start gap-4">
                                        <!-- Large Qty Badge -->
                                        <div class="flex h-12 w-20 shrink-0 flex-col items-center justify-center rounded-xl {{ $ajuan->status === 'disetujui' ? 'bg-green-50 border-green-100 text-green-700' : ($ajuan->status === 'ditolak' ? 'bg-red-50 border-red-100 text-red-700' : 'bg-blue-50 border-blue-100 text-[#0F034D]') }} border">
                                            <span class="text-[9px] uppercase font-semibold {{ $ajuan->status === 'disetujui' ? 'text-green-500/80' : ($ajuan->status === 'ditolak' ? 'text-red-500/80' : 'text-blue-500/80') }}">Jumlah</span>
                                            <span class="text-sm font-black mt-0.5">{{ number_format($ajuan->qty_ajuan, 0, ',', '.') }} <span class="text-[9px] font-normal opacity-70">pcs</span></span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $ajuan->produk->nama_produk ?? '-' }} - {{ ucfirst($ajuan->produk->warna ?? '-') }}</p>
                                            <p class="text-xs text-gray-500 mt-1">Ke <span class="font-semibold text-gray-700">{{ $ajuan->dariKaryawan->name ?? '-' }}</span> - {{ ucfirst($ajuan->dari_tahapan) }}</p>
                                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                                <span class="inline-flex items-center rounded-full bg-gray-50 px-2.5 py-0.5 text-[10px] font-medium text-gray-400">Diajukan {{ $ajuan->created_at?->format('d M Y H:i') }}</span>
                                                <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold {{ $ajuan->status === 'pending' ? 'bg-amber-50 text-amber-700' : ($ajuan->status === 'disetujui' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700') }}">{{ ucfirst($ajuan->status) }}</span>
                                            </div>
                                            @if($ajuan->catatan_pengaju)
                                                <p class="mt-2.5 rounded-lg bg-gray-50 px-3 py-2 text-xs text-gray-600 border border-gray-100">Catatan: {{ $ajuan->catatan_pengaju }}</p>
                                            @endif
                                            @if($ajuan->status === 'ditolak' && $ajuan->catatan_respon)
                                                <p class="mt-2 rounded-lg bg-red-50 px-3 py-2 text-xs text-red-700 border border-red-100">Alasan ditolak: {{ $ajuan->catatan_respon }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </details>
            @empty
                <div class="rounded-xl bg-gray-50 border border-gray-100 p-6 text-center">
                    <p class="text-sm font-semibold text-gray-600">Belum ada ajuan yang dibuat.</p>
                    <p class="text-xs text-gray-400 mt-1">Riwayat ajuan yang pernah Anda kirim akan tampil di sini.</p>
                </div>
            @endforelse
        </div>
    </section>
    @vite('resources/js/produksi/ajuan-pengambilan/index.js')
</x-layouts.produksi>

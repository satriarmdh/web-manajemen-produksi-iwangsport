<x-layouts.produksi>
    <x-slot:header>Ajuan Saya</x-slot:header>

    @if(session('success'))
        <div class="mb-4 rounded-2xl bg-green-50 border border-green-100 p-4 text-sm font-semibold text-green-700">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-2xl bg-red-50 border border-red-100 p-4 text-sm text-red-700">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @php
        $barangReadyPerWo = $barangReady->groupBy('id_perintah');
        $totalProdukReady = $barangReady->count();
        $totalQtyReady = $barangReady->sum('qty_hold');
        $totalPerintahReady = $barangReadyPerWo->count();
        $totalAjuanSaya = $ajuanSaya->count();
        $totalAjuanSayaPending = $ajuanSaya->where('status', 'pending')->groupBy('id_perintah')->count();
    @endphp

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
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <h3 class="font-bold text-[#0F034D]">Daftar Perintah Produksi</h3>
            <p class="text-sm text-gray-500 mt-1">Cari perintah produksi atau produk ready yang ingin diajukan.</p>

            @php
                $hasActiveFilters = $search !== '' || $filterSumber !== '' || $filterTanggal !== '' || $sort !== 'fifo';
            @endphp

            <form id="filter-ajuan-form" action="{{ route('produksi.ajuan-pengambilan.index') }}" method="GET" class="mt-4 grid gap-3 {{ $hasActiveFilters ? 'lg:grid-cols-[1fr_170px_180px_180px_auto]' : 'lg:grid-cols-[1fr_170px_180px_180px]' }} lg:items-end">
                <div class="relative">
                    <label for="ajuan-search" class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-gray-400">Cari Data</label>
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21 21-4.35-4.35"></path><circle cx="11" cy="11" r="8"></circle></svg>
                        <input id="ajuan-search" type="search" name="search" value="{{ $search }}" class="w-full rounded-xl border border-gray-200 pl-11 pr-4 py-3 text-sm focus:border-[#0F034D] focus:ring-1 focus:ring-[#0F034D]/20" placeholder="Cari nomor perintah, produk, warna...">
                    </div>
                </div>

                <input type="hidden" name="sumber" value="{{ $filterSumber }}" data-custom-dropdown-input="sumber">
                <input type="hidden" name="sort" value="{{ $sort }}" data-custom-dropdown-input="sort">

                <div class="relative">
                    <label for="ajuan-date-filter" class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-gray-400">Tanggal Mulai Produksi</label>
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"></path></svg>
                        <input id="ajuan-date-filter" type="date" name="tanggal" value="{{ $filterTanggal }}" class="w-full rounded-xl border {{ $filterTanggal !== '' ? 'border-[#0F034D] bg-[#0F034D]/5 text-[#0F034D]' : 'border-gray-200 bg-white text-gray-600' }} pl-11 pr-4 py-3 text-sm font-semibold focus:border-[#0F034D] focus:ring-1 focus:ring-[#0F034D]/20">
                    </div>
                </div>

                <div class="relative">
                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-gray-400">Sumber Barang</label>
                    <button type="button" data-custom-dropdown-button="sumber" class="w-full flex items-center justify-between gap-3 rounded-xl border {{ $filterSumber !== '' ? 'border-[#0F034D] bg-[#0F034D]/5 text-[#0F034D]' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50' }} px-4 py-3 text-sm font-semibold transition-colors shadow-sm">
                        <span class="truncate">
                            @if($filterSumber !== '')
                                @php
                                    $sumberTerpilih = $sumberOptions->firstWhere('id', (int) $filterSumber);
                                @endphp
                                {{ $sumberTerpilih ? $sumberTerpilih->name . ' - ' . ucfirst($sumberTerpilih->role) : 'Sumber terpilih' }}
                            @else
                                Semua sumber
                            @endif
                        </span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" data-custom-dropdown-arrow="sumber" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"></path></svg>
                    </button>
                    <div data-custom-dropdown-menu="sumber" class="hidden absolute left-0 right-0 mt-2 rounded-xl border border-gray-100 bg-white shadow-[0_10px_40px_rgba(15,3,77,0.12)] z-40 p-2">
                        <button type="button" data-custom-dropdown-option="sumber" data-value="" class="w-full flex items-center justify-between rounded-lg px-3 py-2 text-left text-sm font-medium hover:bg-gray-50 {{ $filterSumber === '' ? 'text-[#0F034D] bg-[#0F034D]/5' : 'text-gray-600' }}">
                            <span>Semua sumber</span>
                            @if($filterSumber === '')<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>@endif
                        </button>
                        @foreach($sumberOptions as $sumber)
                            <button type="button" data-custom-dropdown-option="sumber" data-value="{{ $sumber->id }}" class="w-full flex items-center justify-between rounded-lg px-3 py-2 text-left text-sm font-medium hover:bg-gray-50 {{ (string) $filterSumber === (string) $sumber->id ? 'text-[#0F034D] bg-[#0F034D]/5' : 'text-gray-600' }}">
                                <span class="truncate">{{ $sumber->name }} - {{ ucfirst($sumber->role) }}</span>
                                @if((string) $filterSumber === (string) $sumber->id)<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>@endif
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="relative">
                    @php
                        $sortLabels = [
                            'fifo' => 'Urutan Pengerjaan',
                            'wo_az' => 'Nomor perintah A-Z',
                            'produk_az' => 'Produk A-Z',
                            'qty_terbesar' => 'Qty terbesar',
                            'qty_terkecil' => 'Qty terkecil',
                        ];
                    @endphp
                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-gray-400">Urutkan</label>
                    <button type="button" data-custom-dropdown-button="sort" class="w-full flex items-center justify-between gap-3 rounded-xl border {{ $sort !== 'fifo' ? 'border-[#0F034D] bg-[#0F034D]/5 text-[#0F034D]' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50' }} px-4 py-3 text-sm font-semibold transition-colors shadow-sm">
                        <span class="truncate">{{ $sortLabels[$sort] ?? 'Urutan Pengerjaan' }}</span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" data-custom-dropdown-arrow="sort" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"></path></svg>
                    </button>
                    <div data-custom-dropdown-menu="sort" class="hidden absolute left-0 right-0 mt-2 rounded-xl border border-gray-100 bg-white shadow-[0_10px_40px_rgba(15,3,77,0.12)] z-40 p-2">
                        @foreach($sortLabels as $value => $label)
                            <button type="button" data-custom-dropdown-option="sort" data-value="{{ $value }}" class="w-full flex items-center justify-between rounded-lg px-3 py-2 text-left text-sm font-medium hover:bg-gray-50 {{ $sort === $value ? 'text-[#0F034D] bg-[#0F034D]/5' : 'text-gray-600' }}">
                                <span>{{ $label }}</span>
                                @if($sort === $value)<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>@endif
                            </button>
                        @endforeach
                    </div>
                </div>

                @if($hasActiveFilters)
                    <a href="{{ route('produksi.ajuan-pengambilan.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-800">
            Ikuti urutan dari atas ke bawah. Perintah Produksi dengan tanggal mulai lebih lama sebaiknya dikerjakan lebih dulu.
        </div>

        @forelse($barangReadyPerWo as $idPerintah => $stokDalamWo)
            @php
                $fifoIndex = $loop->iteration;
                $perintah = $stokDalamWo->first()->perintahProduksi;
                $totalReady = $stokDalamWo->sum('qty_hold');
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
                <form action="{{ route('produksi.ajuan-pengambilan.store') }}" method="POST">
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
                                            <span class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-700">{{ number_format($stok->qty_hold, 0, ',', '.') }} pcs</span>
                                        </td>
                                        <td class="px-4 py-3 align-top">
                                            <input type="hidden" name="items[{{ $stok->id }}][stok_virtual_id]" value="{{ $stok->id }}">
                                            <input type="number" name="items[{{ $stok->id }}][qty_ajuan]" min="1" max="{{ $stok->qty_hold }}" class="w-36 rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-[#0F034D] focus:ring-1 focus:ring-[#0F034D]/20" placeholder="Qty">
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
                        <p class="mt-2 text-xs text-gray-400">Isi qty pada produk yang ingin diajukan. Produk dengan qty kosong tidak akan diajukan.</p>
                    </div>
                </form>
            </details>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center text-sm text-gray-500">Belum ada barang ready yang bisa diajukan.</div>
        @endforelse
    </section>

    <section data-ajuan-tab-panel="riwayat" class="hidden mt-5 bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-start justify-between gap-3 mb-3">
            <div>
                <h3 class="font-bold text-[#0F034D]">Riwayat Ajuan Saya</h3>
                <p class="text-sm text-gray-500 mt-1">Status pengajuan yang pernah Anda kirim.</p>
            </div>
        </div>
        <div class="space-y-4">
            @forelse($ajuanSaya->groupBy('id_perintah') as $idPerintah => $ajuanDalamPerintah)
                @php
                    $perintah = $ajuanDalamPerintah->first()->perintahProduksi;
                    $totalQty = $ajuanDalamPerintah->sum('qty_ajuan');
                @endphp
                <details class="group rounded-2xl border border-gray-100 overflow-hidden" open>
                    <summary class="list-none cursor-pointer bg-gray-50/80 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-[#0F034D]">{{ $perintah->nomor_wo ?? 'Perintah Produksi' }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $ajuanDalamPerintah->count() }} ajuan • {{ number_format($totalQty, 0, ',', '.') }} pcs diajukan</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 transition-transform group-open:rotate-180 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"></path></svg>
                        </div>
                    </summary>
                    <div class="divide-y divide-gray-100 bg-white">
                        @foreach($ajuanDalamPerintah as $ajuan)
                            <div class="p-4">
                                <p class="text-sm font-bold text-[#0F034D]">{{ $ajuan->produk->nama_produk ?? '-' }} - {{ ucfirst($ajuan->produk->warna ?? '-') }}</p>
                                <p class="text-xs text-gray-500 mt-1">Ke {{ $ajuan->dariKaryawan->name ?? '-' }} - {{ ucfirst($ajuan->dari_tahapan) }} • {{ number_format($ajuan->qty_ajuan, 0, ',', '.') }} pcs</p>
                                <p class="mt-1 inline-flex items-center rounded-full bg-gray-50 px-2.5 py-1 text-[11px] font-semibold text-gray-500">Diajukan {{ $ajuan->created_at?->format('d M Y H:i') }}</p>
                                @if($ajuan->catatan_pengaju)
                                    <p class="mt-2 rounded-lg bg-gray-50 px-3 py-2 text-xs text-gray-600">Catatan: {{ $ajuan->catatan_pengaju }}</p>
                                @endif
                                @if($ajuan->status === 'ditolak' && $ajuan->catatan_respon)
                                    <p class="mt-2 rounded-lg bg-red-50 px-3 py-2 text-xs text-red-700">Alasan ditolak: {{ $ajuan->catatan_respon }}</p>
                                @endif
                                <span class="inline-flex mt-2 rounded-full px-2.5 py-1 text-xs font-bold {{ $ajuan->status === 'pending' ? 'bg-amber-50 text-amber-700' : ($ajuan->status === 'disetujui' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700') }}">{{ ucfirst($ajuan->status) }}</span>
                            </div>
                        @endforeach
                    </div>
                </details>
            @empty
                <p class="text-sm text-gray-500">Belum ada ajuan yang dibuat.</p>
            @endforelse
        </div>
    </section>

    <script>
        const ajuanTabButtons = document.querySelectorAll('[data-ajuan-tab-button]');
        const ajuanTabPanels = document.querySelectorAll('[data-ajuan-tab-panel]');

        ajuanTabButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const target = button.dataset.ajuanTabButton;

                ajuanTabButtons.forEach((item) => {
                    const active = item.dataset.ajuanTabButton === target;
                    item.classList.toggle('bg-[#0F034D]', active);
                    item.classList.toggle('text-white', active);
                    item.classList.toggle('shadow-md', active);
                    item.classList.toggle('shadow-[#0F034D]/20', active);
                    item.classList.toggle('text-gray-500', !active);
                    item.classList.toggle('hover:bg-gray-50', !active);
                });

                ajuanTabPanels.forEach((panel) => {
                    panel.classList.toggle('hidden', panel.dataset.ajuanTabPanel !== target);
                });
            });
        });

        const ajuanSearch = document.getElementById('ajuan-search');
        const filterAjuanForm = document.getElementById('filter-ajuan-form');
        let ajuanSearchTimer;

        ajuanSearch?.addEventListener('input', () => {
            clearTimeout(ajuanSearchTimer);
            ajuanSearchTimer = setTimeout(() => filterAjuanForm?.submit(), 450);
        });

        document.getElementById('ajuan-date-filter')?.addEventListener('change', () => {
            filterAjuanForm?.submit();
        });

        const closeCustomDropdowns = (except = null) => {
            document.querySelectorAll('[data-custom-dropdown-menu]').forEach((menu) => {
                if (menu.dataset.customDropdownMenu === except) return;
                menu.classList.add('hidden');
            });

            document.querySelectorAll('[data-custom-dropdown-arrow]').forEach((arrow) => {
                if (arrow.dataset.customDropdownArrow === except) return;
                arrow.classList.remove('rotate-180');
            });
        };

        document.querySelectorAll('[data-custom-dropdown-button]').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.stopPropagation();
                const key = button.dataset.customDropdownButton;
                const menu = document.querySelector(`[data-custom-dropdown-menu="${key}"]`);
                const arrow = document.querySelector(`[data-custom-dropdown-arrow="${key}"]`);

                closeCustomDropdowns(key);
                menu?.classList.toggle('hidden');
                arrow?.classList.toggle('rotate-180');
            });
        });

        document.querySelectorAll('[data-custom-dropdown-option]').forEach((option) => {
            option.addEventListener('click', () => {
                const key = option.dataset.customDropdownOption;
                const input = document.querySelector(`[data-custom-dropdown-input="${key}"]`);
                if (!input) return;

                input.value = option.dataset.value ?? '';
                filterAjuanForm?.submit();
            });
        });

        document.addEventListener('click', () => closeCustomDropdowns());
    </script>
</x-layouts.produksi>

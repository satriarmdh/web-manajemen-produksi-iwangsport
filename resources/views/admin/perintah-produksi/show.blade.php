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
            <div class="flex items-start gap-3">
                <a href="{{ route('admin.perintah-produksi.index') }}" class="inline-flex items-center justify-center w-9 h-9 hover:bg-gray-50 rounded-xl text-gray-500 hover:text-[#0F034D] transition-colors border border-gray-200 shrink-0 mt-0.5" title="Kembali ke Daftar Perintah Produksi">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </a>
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
            </div>

            <div class="flex flex-wrap items-center gap-2">

                @if($perintahProduksi->status_produksi === 'pending')
                    <a href="{{ route('admin.perintah-produksi.edit', $perintahProduksi) }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-600 text-sm font-medium rounded-xl transition-colors cursor-pointer">
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

    {{-- Card 1: Header (full width, atas) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 pt-6 pb-4 mb-4">
        <h3 class="text-lg font-bold text-[#0F034D]">Detail Produk & Tahapan Produksi</h3>
        <p class="text-sm text-gray-500 mt-1">Pantau estimasi, hasil potong, validasi, dan posisi proses setiap produk dalam perintah produksi.</p>
    </div>

    {{-- Card 2 & 3: Daftar Produk (kiri) + Detail Produk (kanan) --}}
    <div class="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-4">
        <aside class="self-start">
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden max-h-[calc(100vh-8rem)] flex flex-col">
                {{-- Card Header: Title + Search --}}
                <div class="p-3 border-b border-gray-100">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-gray-400 mb-2">Daftar Produk</p>
                    <div class="relative">
                        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" id="productSearch" data-product-search placeholder="Cari produk..."
                            class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg bg-gray-50 border border-gray-100 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0F034D]/20 focus:border-[#0F034D]/30 transition-all">
                    </div>
                </div>
                {{-- Sidebar: vertical list on lg, horizontal scroll carousel on mobile --}}
                <div class="p-2 flex lg:flex-col gap-2 overflow-x-auto lg:overflow-y-auto lg:overflow-x-visible pb-3 min-h-0">
                    @foreach($perintahProduksi->details as $detail)
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
                                'belum_diterima' => 'Belum',
                                'sebagian' => 'Sebagian',
                                'sesuai' => 'Sesuai',
                                'selisih_kurang' => 'Kurang',
                                'selisih_lebih' => 'Lebih',
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
                        @endphp
                        <button type="button"
                            data-product-selector="{{ $detail->id }}"
                            data-product-name="{{ strtolower($detail->produk->nama_produk ?? '') }}"
                            class="product-list-item shrink-0 lg:shrink w-64 lg:w-full text-left p-3 rounded-xl border transition-all duration-200 {{ $loop->first ? 'bg-[#0F034D]/5 border-[#0F034D] shadow-sm' : 'bg-white border-gray-300 hover:bg-gray-50 hover:border-gray-400 hover:-translate-y-0.5 hover:shadow-md' }}">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-block w-3 h-3 rounded-full shrink-0 {{ $needsStroke ? 'ring-1 ring-gray-300' : '' }}" style="background-color: {{ $warnaDot }}"></span>
                                <p class="text-xs font-bold text-[#0F034D] truncate flex-1 min-w-0">{{ $detail->produk->nama_produk ?? '-' }}</p>
                            </div>
                            <p class="text-[11px] text-gray-500 mb-2">{{ ucfirst($detail->produk->warna ?? '-') }}</p>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[9px] px-2 py-0.5 rounded-full border font-semibold {{ $validasiColors[$detail->status_validasi_potong] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                    {{ $validasiLabel[$detail->status_validasi_potong] ?? ucfirst($detail->status_validasi_potong) }}
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded-full border font-semibold {{ $penerimaanColors[$detail->status_penerimaan] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                    {{ $penerimaanLabel[$detail->status_penerimaan] ?? ucfirst($detail->status_penerimaan) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100">
                                <span class="text-[10px] text-gray-400">Estimasi</span>
                                <span class="text-xs font-bold text-[#0F034D]">{{ number_format($detail->estimasi_pcs, 0, ',', '.') }} pcs</span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </aside>

        {{-- RIGHT: Detail Panel (cards dari _detail-produk) --}}
        <div>
            @foreach($perintahProduksi->details as $detail)
                <div data-product-panel="{{ $detail->id }}" class="{{ $loop->first ? '' : 'hidden' }}">
                    @include('admin.perintah-produksi.partials._detail-produk')
                </div>
            @endforeach
        </div>
    </div>

    {{-- Penerimaan Modal --}}
    @include('admin.perintah-produksi.partials._input-penerimaan-modal')

    {{-- Stok Detail Slide Panel --}}
    <div id="stokDetailPanel" class="slide-panel">
        <div class="slide-panel-backdrop" data-close-stok-modal></div>
        <div class="slide-panel-body">
            {{-- Header --}}
            <div class="slide-panel-header">
                <div class="slide-panel-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="slide-panel-header-title" id="modal-karyawan-name">-</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Detail Stok Karyawan - <span id="modal-peran" class="font-semibold">-</span></p>
                </div>
                <button type="button" class="slide-panel-close" data-close-stok-modal>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Content --}}
            <div class="slide-panel-content">
                <div class="space-y-5">
                    {{-- Stat Cards --}}
                    <div class="grid grid-cols-3 gap-2.5">
                        <div class="bg-indigo-50 rounded-xl p-3 border border-indigo-100">
                            <p class="text-[11px] text-indigo-600 mb-1 font-medium">Dipegang</p>
                            <p class="text-xl font-bold text-indigo-700"><span id="modal-qty-hold">0</span> <span class="text-xs">pcs</span></p>
                        </div>
                        <div class="bg-emerald-50 rounded-xl p-3 border border-emerald-100">
                            <p class="text-[11px] text-emerald-600 mb-1 font-medium">Hasil Selesai</p>
                            <p class="text-xl font-bold text-emerald-700"><span id="modal-total-selesai">0</span> <span class="text-xs">pcs</span></p>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-3 border border-blue-100">
                            <p class="text-[11px] text-blue-600 mb-1 font-medium">Sudah Diserahkan</p>
                            <p class="text-xl font-bold text-blue-700"><span id="modal-total-dikeluarkan">0</span> <span class="text-xs">pcs</span></p>
                        </div>
                    </div>

                    {{-- Status Rows --}}
                    <div class="bg-gray-50 rounded-xl border border-gray-100 divide-y divide-gray-100">
                        <div class="flex justify-between items-center gap-3 px-4 py-2.5">
                            <span class="text-xs font-bold text-gray-700">Status Barang</span>
                            <span id="modal-status-barang-badge" class="px-2.5 py-1 rounded-full text-[11px] font-semibold shrink-0">-</span>
                        </div>
                        <div class="flex justify-between items-center gap-3 px-4 py-2.5">
                            <span class="text-xs font-bold text-gray-700">Barang Siap Diserahkan</span>
                            <span class="text-xs font-bold text-emerald-700 shrink-0"><span id="modal-ready-qty">0</span> pcs</span>
                        </div>
                        <div class="flex justify-between items-center gap-3 px-4 py-2.5">
                            <span class="text-xs font-bold text-gray-700">Status Pengerjaan</span>
                            <span id="modal-status-pengerjaan" class="text-xs font-semibold text-gray-700 shrink-0">-</span>
                        </div>
                    </div>

                    {{-- Ringkasan catatan --}}
                    <div id="recordsSummarySection" class="hidden">
                        <h3 class="text-sm font-bold text-[#0F034D] mb-2">Ringkasan Catatan</h3>
                        <div class="grid grid-cols-2 gap-2.5">
                            <div class="bg-amber-50 rounded-xl p-3 border border-amber-200">
                                <p class="text-[11px] text-amber-700 font-semibold">Total Cacat</p>
                                <p class="text-lg font-bold text-amber-700 mt-1"><span id="summary-total-cacat">0</span> <span class="text-xs">pcs</span></p>
                                <p class="text-[10px] text-amber-600 mt-0.5"><span id="summary-count-cacat">0</span> laporan</p>
                            </div>
                            <div class="bg-red-50 rounded-xl p-3 border border-red-200">
                                <p class="text-[11px] text-red-700 font-semibold">Total Selisih</p>
                                <p class="text-lg font-bold text-red-700 mt-1"><span id="summary-total-selisih">0</span> <span class="text-xs">pcs</span></p>
                                <p class="text-[10px] text-red-600 mt-0.5"><span id="summary-count-selisih">0</span> laporan</p>
                            </div>
                        </div>
                    </div>

                    {{-- Riwayat catatan --}}
                    <div id="recordsListSection" class="hidden">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-bold text-[#0F034D]">Riwayat Catatan</h3>
                            <span class="text-[10px] text-gray-400" id="records-count-label">0 catatan</span>
                        </div>
                        <div class="grid grid-cols-2 gap-1 p-1 bg-gray-100 rounded-lg mb-2.5" role="tablist" aria-label="Jenis riwayat catatan">
                            <button type="button" id="records-tab-cacat" data-record-type="cacat" role="tab" aria-selected="true" class="px-2 py-1.5 text-[11px] font-semibold rounded-md bg-[#0F034D] text-white shadow-sm transition-colors">Barang Cacat</button>
                            <button type="button" id="records-tab-selisih" data-record-type="selisih" role="tab" aria-selected="false" class="px-2 py-1.5 text-[11px] font-semibold rounded-md text-gray-500 hover:text-[#0F034D] transition-colors">Selisih</button>
                        </div>
                        <div id="records-list" class="space-y-1.5"></div>
                        <div id="records-pagination" class="flex items-center justify-center gap-1.5 mt-3 hidden">
                            <button type="button" id="records-prev" class="px-2.5 py-1 text-xs rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium transition-colors disabled:opacity-40 disabled:cursor-not-allowed">‹ Sebelumnya</button>
                            <span id="records-page-info" class="text-xs text-gray-500 px-2">1 / 1</span>
                            <button type="button" id="records-next" class="px-2.5 py-1 text-xs rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium transition-colors disabled:opacity-40 disabled:cursor-not-allowed">Berikutnya ›</button>
                        </div>
                        <div id="records-empty" class="hidden text-center py-5">
                            <p class="text-xs text-gray-500">Tidak ada catatan pada riwayat ini</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="slide-panel-footer">
                <button type="button" class="btn-panel-cancel" data-close-stok-modal>Tutup</button>
            </div>
        </div>
    </div>

    {{-- Photo Viewer Modal --}}
    <div id="photoViewerModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-200">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative max-w-2xl w-full">
                <button type="button" data-close-photo class="absolute -top-3 -right-3 w-9 h-9 rounded-full bg-white shadow-lg flex items-center justify-center hover:bg-gray-100 transition-colors z-10">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <img id="photoViewerImg" src="" alt="Bukti Penerimaan" class="w-full rounded-xl shadow-2xl">
            </div>
        </div>
    </div>

    @vite('resources/js/admin/perintah-produksi/show.js')
    @vite('resources/js/admin/perintah-produksi/penerimaan.js')
    @vite('resources/js/admin/confirm-action.js')
</x-layouts.admin>


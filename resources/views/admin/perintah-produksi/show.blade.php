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

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.perintah-produksi.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </a>

                @if($perintahProduksi->status_produksi === 'pending')
                    <a href="{{ route('admin.perintah-produksi.edit', $perintahProduksi) }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-sm font-medium rounded-xl transition-all shadow-md shadow-[#0F034D]/20">
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

    {{-- Stok Detail Modal (dipindah dari _distribusi lama) --}}
    <div id="stokDetailModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-200">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform scale-95 transition-transform duration-200" id="stokDetailModalContent">
                <div class="flex items-center justify-between p-5 border-b border-gray-100">
                    <div>
                        <h3 class="text-lg font-bold text-[#0F034D]" id="modal-karyawan-name">-</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Detail Stok Virtual - <span id="modal-peran" class="font-semibold">-</span></p>
                    </div>
                    <button type="button" data-close-stok-modal class="w-9 h-9 rounded-xl hover:bg-gray-100 flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-indigo-50 rounded-xl p-3 border border-indigo-100">
                            <p class="text-xs text-indigo-600 mb-1">Dipegang (Proses)</p>
                            <p class="text-xl font-bold text-indigo-700"><span id="modal-qty-hold">0</span> pcs</p>
                        </div>
                        <div class="bg-green-50 rounded-xl p-3 border border-green-100">
                            <p class="text-xs text-green-600 mb-1">Selesai Dikerjakan</p>
                            <p class="text-xl font-bold text-green-700"><span id="modal-total-selesai">0</span> pcs</p>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-3 border border-blue-100">
                            <p class="text-xs text-blue-600 mb-1">Sudah Diserahkan</p>
                            <p class="text-xl font-bold text-blue-700"><span id="modal-total-dikeluarkan">0</span> pcs</p>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-3 border border-amber-100">
                            <p class="text-xs text-amber-600 mb-1">Cacat/Reject</p>
                            <p class="text-xl font-bold text-amber-700"><span id="modal-total-reject">0</span> pcs</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Status Barang:</span>
                            <span id="modal-status-barang-badge" class="px-3 py-1 rounded-full text-xs font-semibold">-</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Barang Ready (Belum Diserahkan):</span>
                            <span class="font-bold text-green-700"><span id="modal-ready-qty">0</span> pcs</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Status Pengerjaan:</span>
                            <span id="modal-status-pengerjaan" class="font-semibold text-gray-700">-</span>
                        </div>
                    </div>
                    <div id="modal-selisih-warning" class="hidden bg-red-50 border border-red-200 rounded-xl p-3">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div class="flex-1">
                                <p class="font-semibold text-red-700 text-sm">Ada Selisih yang Perlu Dipertanggungjawabkan</p>
                                <p class="text-xs text-red-600 mt-1">Karyawan sudah menandai pekerjaan selesai, tetapi masih ada stok yang dipegang (<span id="modal-qty-hold-warning">0</span> pcs). Perlu investigasi untuk memastikan tidak ada kehilangan barang.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                    <button type="button" data-close-stok-modal class="px-4 py-2 text-gray-700 bg-white hover:bg-gray-100 border border-gray-200 font-medium rounded-xl transition-colors">
                        Tutup
                    </button>
                </div>
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


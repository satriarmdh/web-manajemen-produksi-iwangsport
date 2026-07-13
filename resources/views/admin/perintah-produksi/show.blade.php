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

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 pt-6 pb-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-[#0F034D]">Detail Produk & Tahapan Produksi</h3>
            <p class="text-sm text-gray-500 mt-1">Pantau estimasi, hasil potong, validasi, dan posisi proses setiap produk dalam perintah produksi.</p>
        </div>

        <div class="divide-y divide-gray-100">
            @foreach($perintahProduksi->details as $detail)
                @php
                    $hasPotong = !is_null($detail->qty_pcs_potong);
                    $isFinished = $perintahProduksi->status_produksi === 'selesai';
                    $isRejected = $perintahProduksi->status_produksi === 'ditolak';
                    $steps = [
                        ['key' => 'wo', 'label' => 'WO', 'sub' => 'Dibuat', 'done' => true, 'active' => !$hasPotong && !$isFinished],
                        ['key' => 'potong', 'label' => 'Potong', 'sub' => $hasPotong ? number_format($detail->qty_pcs_potong, 0, ',', '.') . ' pcs' : 'Menunggu hasil', 'done' => $hasPotong || $isFinished, 'active' => $hasPotong && !$isFinished],
                        ['key' => 'jahit', 'label' => 'Jahit', 'sub' => 'Menunggu mutasi', 'done' => false, 'active' => false],
                        ['key' => 'finishing', 'label' => 'Finishing', 'sub' => 'Menunggu mutasi', 'done' => false, 'active' => false],
                        ['key' => 'ready', 'label' => 'Ready', 'sub' => $isFinished ? 'Selesai' : 'Belum ready', 'done' => $isFinished, 'active' => $isFinished],
                    ];
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
                @endphp

                <div class="p-6">
                    <div class="flex flex-col xl:flex-row xl:items-start justify-between gap-6">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-3 mb-3">
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
                                <h4 class="inline-flex items-center gap-2 text-base font-bold text-[#0F034D] min-w-0">
                                    <span class="truncate">{{ $detail->produk->nama_produk ?? '-' }} - {{ ucfirst($detail->produk->warna ?? '-') }}</span>
                                    <span class="inline-block w-3 h-3 rounded-full shrink-0 {{ $needsStroke ? 'ring-1 ring-gray-300' : '' }}" style="background-color: {{ $warnaDot }}" title="Warna {{ ucfirst($detail->produk->warna ?? '-') }}"></span>
                                </h4>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $validasiColors[$detail->status_validasi_potong] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                    Validasi: {{ $validasiLabel[$detail->status_validasi_potong] ?? ucfirst($detail->status_validasi_potong) }}
                                </span>
                            </div>

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

                        <div class="w-full xl:w-[420px]">
                            <div class="relative flex items-start justify-between gap-2">
                                @foreach($steps as $index => $step)
                                    <div class="flex-1 relative text-center">
                                        @if(!$loop->last)
                                            <div class="absolute top-4 left-1/2 w-full h-px {{ $steps[$index + 1]['done'] || $step['done'] ? 'bg-[#0F034D]/30' : 'bg-gray-200' }}"></div>
                                        @endif
                                        <div class="relative z-10 mx-auto w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border transition-colors
                                            {{ $step['done'] ? 'bg-green-500 border-green-500 text-white' : ($step['active'] ? 'bg-[#0F034D] border-[#0F034D] text-white' : 'bg-gray-100 border-gray-200 text-gray-500') }}">
                                            @if($step['done'])
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </div>
                                        <p class="mt-2 text-xs font-semibold {{ $step['active'] || $step['done'] ? 'text-[#0F034D]' : 'text-gray-500' }}">{{ $step['label'] }}</p>
                                        <p class="text-[11px] text-gray-400 leading-tight">{{ $step['sub'] }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4 rounded-xl bg-amber-50 border border-amber-100 p-3">
                                <p class="text-xs text-amber-700 leading-relaxed">
                                    Tahapan Jahit, Finishing, dan Ready sementara ditampilkan sebagai kerangka progress. Nantinya bisa dihubungkan ke tabel <span class="font-semibold">stok_virtual</span> dan <span class="font-semibold">mutasi produksi</span> untuk menampilkan posisi barang per karyawan secara real-time.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @vite('resources/js/admin/confirm-action.js')
</x-layouts.admin>


@php
    $stokVirtualAll = $perintahProduksi->stokVirtual->where('id_detail_perintah', $detail->id);
    
    // Aggregate stok info per karyawan
    $karyawanStok = collect();
    foreach ($stokVirtualAll as $stok) {
        $karyawanStok->push([
            'karyawan_id' => $stok->id_karyawan,
            'karyawan_name' => $stok->karyawan->name ?? '-',
            'peran' => $stok->peran,
            'qty_hold' => (int) $stok->qty_hold,
            'total_selesai' => (int) $stok->total_selesai,
            'total_dikeluarkan' => (int) $stok->total_dikeluarkan,
            'total_reject' => (int) $stok->total_reject,
            'is_selesai' => $stok->is_selesai,
            'status_barang' => $stok->status_barang,
        ]);
    }
@endphp
<!-- Detail Distribusi -->
<div class="w-full">
    <div class="border border-gray-100 rounded-xl p-3 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 pb-2 mb-2">
            <span class="text-xs font-bold text-[#0F034D]">Detail Distribusi</span>
            <div class="flex gap-1.5">
                <button type="button" data-tab-trigger="posisi-{{ $detail->id }}" class="px-2 py-1 rounded-md text-[10px] font-bold bg-[#0F034D] text-white hover:bg-[#24116f] transition-all shadow-sm">Info Stok Karyawan</button>
                <button type="button" data-tab-trigger="log-{{ $detail->id }}" class="px-2 py-1 rounded-md text-[10px] font-bold bg-gray-50 text-gray-500 hover:bg-gray-100 transition-all">Log Serah Terima</button>
                <button type="button" data-tab-trigger="riwayat-{{ $detail->id }}" class="px-2 py-1 rounded-md text-[10px] font-bold bg-gray-50 text-gray-500 hover:bg-gray-100 transition-all">Riwayat Terima</button>
            </div>
        </div>

        <!-- Tab 1: Informasi Stok Karyawan (Stok Virtual) -->
        <div data-tab-content="posisi-{{ $detail->id }}" class="space-y-1.5">
            @forelse($karyawanStok as $index => $item)
                @php
                    // Opsi A: komputasi konsisten untuk SEMUA tahap
                    // qty_hold = WIP input (barang dipegang yang belum dikerjakan)
                    // ready_to_transfer = total_selesai - total_dikeluarkan (barang siap yang belum diserahkan)
                    $readyToTransfer = max(0, $item['total_selesai'] - $item['total_dikeluarkan']);
                    $wipInput = (int) $item['qty_hold'];
                    
                    // Status Barang Logic (best practice: pakai field status_barang + komputasi)
                    $statusBadge = '';
                    $statusClass = '';
                    if ($item['status_barang'] === 'Proses') {
                        // Masih dalam proses pengerjaan
                        $statusBadge = '🟡 Proses';
                        $statusClass = 'bg-amber-50 text-amber-700 border-amber-100';
                    } elseif ($item['status_barang'] === 'Ready' && $readyToTransfer > 0) {
                        // Sudah selesai dikerjakan & masih ada yang belum diserahkan
                        $statusBadge = '🟢 Ready';
                        $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                    } elseif ($item['status_barang'] === 'Ready' && $readyToTransfer == 0) {
                        // Sudah selesai & semua sudah diserahkan (habis)
                        $statusBadge = '✓ Selesai';
                        $statusClass = 'bg-gray-50 text-gray-600 border-gray-100';
                    }
                    
                    // Flag selisih: karyawan menandai selesai tapi masih ada WIP input yang belum dikerjakan
                    $hasSelisih = $item['is_selesai'] && $wipInput > 0;
                @endphp
                <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50 border border-gray-100">
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        <span class="font-bold text-gray-800 text-xs truncate">{{ $item['karyawan_name'] }}</span>
                        <span class="text-[9px] px-1.5 py-0.5 rounded-full bg-blue-50 text-blue-700 font-semibold shrink-0">{{ ucfirst($item['peran']) }}</span>
                        
                        @if($statusBadge)
                            <span class="text-[9px] px-2 py-0.5 rounded-full {{ $statusClass }} font-bold shrink-0 border">{{ $statusBadge }}</span>
                        @endif
                        
                        <!-- Summary Badges -->
                        <div class="flex items-center gap-1.5 ml-auto mr-2">
                            <!-- Good Items -->
                            <span class="text-[9px] px-2 py-0.5 rounded-full bg-green-50 text-green-700 font-semibold shrink-0">
                                ✓ {{ number_format($item['total_selesai']) }}
                            </span>
                            
                            <!-- Reject Items -->
                            @if($item['total_reject'] > 0)
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 font-semibold shrink-0">
                                    ✕ {{ number_format($item['total_reject']) }}
                                </span>
                            @endif
                            
                            <!-- Selisih Flag: WIP input belum dikerjakan saat karyawan tandai selesai -->
                            @if($hasSelisih)
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-red-50 text-red-600 font-semibold shrink-0">
                                    {{ number_format($wipInput) }} Selisih
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Detail Button -->
                    <button type="button" 
                        data-view-stok-detail
                        data-karyawan-name="{{ $item['karyawan_name'] }}"
                        data-peran="{{ $item['peran'] }}"
                        data-qty-hold="{{ $item['qty_hold'] }}"
                        data-total-selesai="{{ $item['total_selesai'] }}"
                        data-total-dikeluarkan="{{ $item['total_dikeluarkan'] }}"
                        data-total-reject="{{ $item['total_reject'] }}"
                        data-ready-qty="{{ $readyToTransfer }}"
                        data-status-barang="{{ $item['status_barang'] }}"
                        data-is-selesai="{{ $item['is_selesai'] ? '1' : '0' }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-medium rounded-lg transition-colors border border-blue-100 cursor-pointer shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Detail
                    </button>
                </div>
            @empty
                <p class="text-[11px] text-gray-400 py-1.5 text-center">Belum ada barang aktif di tangan karyawan.</p>
            @endforelse
        </div>

        <!-- Tab 2: Log Serah Terima -->
        <div data-tab-content="log-{{ $detail->id }}" class="hidden space-y-1.5 max-h-48 overflow-y-auto pr-1">
            @forelse($detail->mutasiProduksi->sortByDesc('created_at') as $mutasi)
                <div class="p-2 rounded-lg bg-gray-50 border border-gray-100 text-[11px] leading-snug">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <span class="font-bold text-[#0F034D]">+{{ number_format($mutasi->qty_pindah) }} pcs</span>
                        <span class="text-[9px] text-gray-400 font-medium">
                            {{ $mutasi->tgl_transaksi ? $mutasi->tgl_transaksi->format('d M H:i') : $mutasi->created_at->format('d M H:i') }}
                        </span>
                    </div>
                    <p class="text-gray-500">
                        Dari <span class="font-semibold text-gray-700">{{ $mutasi->dariKaryawan->name ?? 'Sistem' }}</span> ({{ ucfirst($mutasi->dari_tahapan ?? 'awal') }})
                        ke <span class="font-semibold text-gray-700">{{ $mutasi->keKaryawan->name ?? '-' }}</span> ({{ ucfirst($mutasi->ke_tahapan) }})
                    </p>
                </div>
            @empty
                <p class="text-[11px] text-gray-400 py-1.5 text-center">Belum ada aktivitas serah terima.</p>
            @endforelse
        </div>

        <!-- Tab 3: Riwayat Penerimaan dari Finishing -->
        <div data-tab-content="riwayat-{{ $detail->id }}" class="hidden space-y-1.5 max-h-48 overflow-y-auto pr-1">
            @forelse($detail->penerimaanHasilProduksi->sortByDesc('tanggal_terima') as $penerimaan)
                <div class="p-2 rounded-lg bg-gray-50 border border-gray-100 text-[11px] leading-snug">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <span class="font-bold {{ $penerimaan->qty_diterima > 0 ? 'text-emerald-700' : 'text-red-700' }}">
                            {{ $penerimaan->qty_diterima > 0 ? '+' : '' }}{{ number_format($penerimaan->qty_diterima) }} pcs
                        </span>
                        <span class="text-[9px] text-gray-500 font-medium">
                            {{ $penerimaan->tanggal_terima->format('d M Y') }}
                        </span>
                    </div>
                    <p class="text-gray-600 mb-1">
                        Dari <span class="font-semibold text-gray-800">{{ $penerimaan->dariKaryawan->name ?? '-' }}</span> (finishing)
                        → Admin: <span class="font-semibold text-gray-800">{{ $penerimaan->admin->name ?? '-' }}</span>
                    </p>
                    @if($penerimaan->catatan)
                        <p class="text-[10px] text-gray-500 italic mt-1">{{ Str::limit($penerimaan->catatan, 60) }}</p>
                    @endif
                    @if($penerimaan->bukti_foto)
                        <button type="button" data-view-photo="{{ asset('storage/' . $penerimaan->bukti_foto) }}" 
                            class="inline-flex items-center gap-1.5 mt-2 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-medium rounded-lg transition-colors border border-blue-100 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Lihat Foto
                        </button>
                    @endif
                </div>
            @empty
                <p class="text-[11px] text-gray-400 py-1.5 text-center">Belum ada penerimaan dari finishing.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Modal Detail Stok Karyawan --}}
<div id="stokDetailModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-200">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform scale-95 transition-transform duration-200" id="stokDetailModalContent">
            {{-- Header --}}
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

            {{-- Body --}}
            <div class="p-5 space-y-4">
                {{-- Metrics Grid --}}
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

                {{-- Detail Info --}}
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

                {{-- Warning Selisih --}}
                <div id="modal-selisih-warning" class="hidden bg-red-50 border border-red-200 rounded-xl p-3">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold text-red-700 text-sm">⚠️ Ada Selisih yang Perlu Dipertanggungjawabkan</p>
                            <p class="text-xs text-red-600 mt-1">Karyawan sudah menandai pekerjaan selesai, tetapi masih ada stok yang dipegang (<span id="modal-qty-hold-warning">0</span> pcs). Perlu investigasi untuk memastikan tidak ada kehilangan barang.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                <button type="button" data-close-stok-modal class="px-4 py-2 text-gray-700 bg-white hover:bg-gray-100 border border-gray-200 font-medium rounded-xl transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Detail Stok Karyawan Modal
(function() {
    const modal = document.getElementById('stokDetailModal');
    const modalContent = document.getElementById('stokDetailModalContent');
    
    // Open modal
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-view-stok-detail]');
        if (btn) {
            const data = btn.dataset;
            
            // Populate modal
            document.getElementById('modal-karyawan-name').textContent = data.karyawanName;
            document.getElementById('modal-peran').textContent = data.peran.toUpperCase();
            document.getElementById('modal-qty-hold').textContent = parseInt(data.qtyHold).toLocaleString('id-ID');
            document.getElementById('modal-total-selesai').textContent = parseInt(data.totalSelesai).toLocaleString('id-ID');
            document.getElementById('modal-total-dikeluarkan').textContent = parseInt(data.totalDikeluarkan).toLocaleString('id-ID');
            document.getElementById('modal-total-reject').textContent = parseInt(data.totalReject).toLocaleString('id-ID');
            document.getElementById('modal-ready-qty').textContent = parseInt(data.readyQty).toLocaleString('id-ID');
            
            // Status barang badge
            const statusBadge = document.getElementById('modal-status-barang-badge');
            if (data.statusBarang === 'Ready') {
                statusBadge.textContent = 'Ready';
                statusBadge.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200';
            } else {
                statusBadge.textContent = 'Dalam Proses';
                statusBadge.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200';
            }
            
            // Status pengerjaan
            document.getElementById('modal-status-pengerjaan').textContent = data.isSelesai === '1' ? 'Sudah Selesai' : 'Dalam Pengerjaan';
            
            // Selisih warning (qty_hold > 0 && is_selesai = true)
            const selisihWarning = document.getElementById('modal-selisih-warning');
            if (parseInt(data.qtyHold) > 0 && data.isSelesai === '1') {
                selisihWarning.classList.remove('hidden');
                document.getElementById('modal-qty-hold-warning').textContent = parseInt(data.qtyHold).toLocaleString('id-ID');
            } else {
                selisihWarning.classList.add('hidden');
            }
            
            // Show modal
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }
    });
    
    // Close modal
    document.querySelectorAll('[data-close-stok-modal]').forEach(btn => {
        btn.addEventListener('click', closeModal);
    });
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    function closeModal() {
        modal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }
})();
</script>

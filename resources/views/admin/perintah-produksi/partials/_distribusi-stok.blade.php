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

    $estimasi = $detail->estimasi_pcs;
    $diterima = $detail->total_qty_diterima;
    $sisa = $estimasi - $diterima;
@endphp
<!-- Tab Content: Stok Karyawan -->
<div class="space-y-2">
    @foreach($karyawanStok as $index => $item)
        @php
            $readyToTransfer = max(0, $item['total_selesai'] - $item['total_dikeluarkan']);
            $wipInput = (int) $item['qty_hold'];

            $statusBadge = '';
            $statusClass = '';
            if ($item['status_barang'] === 'Proses') {
                $statusBadge = 'Proses';
                $statusClass = 'bg-amber-50 text-amber-700 border-amber-100';
            } elseif ($item['status_barang'] === 'Ready' && $readyToTransfer > 0) {
                $statusBadge = 'Ready';
                $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-100';
            } elseif ($item['status_barang'] === 'Ready' && $readyToTransfer == 0) {
                $statusBadge = 'Selesai';
                $statusClass = 'bg-gray-50 text-gray-600 border-gray-100';
            }

            $hasSelisih = $item['is_selesai'] && $wipInput > 0;
        @endphp
        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100 hover:border-gray-200 transition-colors">
            <div class="flex items-center gap-2 min-w-0 flex-1 flex-wrap">
                <span class="font-bold text-gray-800 text-xs">{{ $item['karyawan_name'] }}</span>
                <span class="text-[9px] px-1.5 py-0.5 rounded-full bg-blue-50 text-blue-700 font-semibold shrink-0">{{ ucfirst($item['peran']) }}</span>

                @if($statusBadge)
                    <span class="text-[9px] px-2 py-0.5 rounded-full {{ $statusClass }} font-bold shrink-0 border">{{ $statusBadge }}</span>
                @endif

                <div class="flex items-center gap-1.5 ml-auto mr-2">
                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-green-50 text-green-700 font-semibold shrink-0">
                        {{ number_format($item['total_selesai']) }} selesai
                    </span>

                    @if($item['total_reject'] > 0)
                        <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 font-semibold shrink-0">
                            {{ number_format($item['total_reject']) }} reject
                        </span>
                    @endif

                    @if($hasSelisih)
                        <span class="text-[9px] px-2 py-0.5 rounded-full bg-red-50 text-red-600 font-semibold shrink-0">
                            {{ number_format($wipInput) }} selisih
                        </span>
                    @endif
                </div>
            </div>

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
    @endforeach
</div>

{{-- Status Penerimaan Hasil Produksi Section --}}
@php
    $statusColors = [
        'belum_diterima' => 'bg-gray-50 text-gray-600 border-gray-100',
        'sebagian' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
        'sesuai' => 'bg-green-50 text-green-700 border-green-100',
        'selisih_kurang' => 'bg-red-50 text-red-700 border-red-100',
        'selisih_lebih' => 'bg-orange-50 text-orange-700 border-orange-100',
    ];
    $statusLabels = [
        'belum_diterima' => 'Belum Diterima',
        'sebagian' => 'Sebagian',
        'sesuai' => 'Sesuai',
        'selisih_kurang' => 'Selisih Kurang',
        'selisih_lebih' => 'Selisih Lebih',
    ];
    $statusIcons = [
        'belum_diterima' => '⏳',
        'sebagian' => '🟡',
        'sesuai' => '✅',
        'selisih_kurang' => '⚠️',
        'selisih_lebih' => '🔺',
    ];
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex items-center gap-3 mb-5">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <h3 class="font-bold text-[#0F034D] text-lg">Penerimaan Hasil Produksi</h3>
            <p class="text-sm text-gray-500">Status penerimaan dari finishing</p>
        </div>
    </div>

    @foreach($perintahProduksi->details as $detail)
        @php
            $estimasi = $detail->estimasi_pcs;
            $diterima = $detail->total_qty_diterima;
            $sisa = $estimasi - $diterima;
            $status = $detail->status_penerimaan;
            $statusClass = $statusColors[$status] ?? 'bg-gray-50 text-gray-600 border-gray-100';
            $statusLabel = $statusLabels[$status] ?? $status;
            $statusIcon = $statusIcons[$status] ?? '📦';
        @endphp

        <div class="border border-gray-100 rounded-xl p-5 mb-4 last:mb-0 hover:border-gray-200 transition-colors">
            {{-- Product Header --}}
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h4 class="font-bold text-[#0F034D] text-base mb-2">
                        {{ $detail->produk->nama_produk }} - {{ ucfirst($detail->produk->warna) }}
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                            <p class="text-xs text-blue-600 mb-1">Estimasi</p>
                            <p class="text-lg font-bold text-blue-700">{{ number_format($estimasi) }} <span class="text-xs font-normal">pcs</span></p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-3 border border-green-100">
                            <p class="text-xs text-green-600 mb-1">Diterima</p>
                            <p class="text-lg font-bold text-green-700">{{ number_format($diterima) }} <span class="text-xs font-normal">pcs</span></p>
                        </div>
                        <div class="bg-amber-50 rounded-lg p-3 border border-amber-100">
                            <p class="text-xs text-amber-600 mb-1">Sisa</p>
                            <p class="text-lg font-bold text-amber-700">{{ number_format($sisa) }} <span class="text-xs font-normal">pcs</span></p>
                        </div>
                        <div class="flex items-center justify-center">
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold border {{ $statusClass }}">
                                <span>{{ $statusIcon }}</span>
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-gray-100">
                @if($status !== 'sesuai')
                    <button 
                        type="button"
                        data-open-penerimaan-modal
                        data-detail-id="{{ $detail->id }}"
                        data-produk-nama="{{ $detail->produk->nama_produk }} - {{ ucfirst($detail->produk->warna) }}"
                        data-estimasi="{{ $estimasi }}"
                        data-diterima="{{ $diterima }}"
                        data-sisa="{{ $sisa }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-sm font-medium rounded-xl transition-all shadow-md shadow-[#0F034D]/20 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Input Penerimaan
                    </button>
                @endif

                <button 
                    type="button"
                    data-open-riwayat-modal
                    data-detail-id="{{ $detail->id }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-700 text-sm font-medium rounded-xl transition-colors border border-gray-200 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Lihat Riwayat
                    @if($diterima > 0)
                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-2 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">
                            {{ $detail->penerimaanHasilProduksi->count() }}
                        </span>
                    @endif
                </button>

                @if($status === 'sebagian' && $sisa < 0)
                    <div class="flex-1"></div>
                    <div class="inline-flex items-center gap-2 px-3 py-2 bg-red-50 text-red-700 text-xs font-medium rounded-lg border border-red-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Perhatian: Ada barang ready yang belum diserahkan
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>

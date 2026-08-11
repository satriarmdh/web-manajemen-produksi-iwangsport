@php
    $estimasi = $detail->estimasi_pcs;
    $diterima = $detail->total_qty_diterima;
    $sisa = $estimasi - $diterima;
@endphp
<!-- Tab Content: Riwayat Penerimaan -->
<div>
    @forelse($detail->penerimaanHasilProduksi->sortByDesc('tanggal_terima') as $penerimaan)
        <div class="relative pl-6 pb-4 {{ $loop->last ? 'pb-0' : '' }}">
            {{-- Vertical line --}}
            @if(!$loop->last)
                <div class="absolute left-2 top-4 bottom-0 w-0.5 bg-gray-200"></div>
            @endif
            {{-- Dot --}}
            @php
                $dotColor = 'bg-emerald-500';
                if ($penerimaan->qty_diterima < 0) {
                    $dotColor = 'bg-red-500';
                } elseif (($penerimaan->jenis_penerimaan ?? 'baik') === 'cacat') {
                    $dotColor = 'bg-amber-500';
                }
            @endphp
            <div class="absolute left-0 top-1 w-4 h-4 rounded-full {{ $dotColor }} ring-4 ring-white"></div>

            <div class="p-3 rounded-xl bg-gray-50 border border-gray-100 text-xs leading-snug">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <div class="flex items-center gap-1.5">
                        <span class="font-bold {{ $penerimaan->qty_diterima > 0 ? 'text-emerald-700' : 'text-red-700' }}">
                            {{ $penerimaan->qty_diterima > 0 ? '+' : '' }}{{ number_format($penerimaan->qty_diterima) }} pcs
                        </span>
                        @if(($penerimaan->jenis_penerimaan ?? 'baik') === 'cacat')
                            <span class="px-1.5 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 text-[9px] rounded-md font-semibold">Cacat/Reject</span>
                        @else
                            <span class="px-1.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 text-[9px] rounded-md font-semibold">Hasil Baik</span>
                        @endif
                    </div>
                    <span class="text-[10px] text-gray-500 font-medium">
                        {{ $penerimaan->tanggal_terima->format('d M Y') }}
                    </span>
                </div>
                <p class="text-gray-600 mb-1">
                    Dari <span class="font-semibold text-gray-800">{{ $penerimaan->dariKaryawan->name ?? '-' }}</span> 
                    @php
                        $svRole = \App\Models\StokVirtual::where('id_detail_perintah', $detail->id)
                            ->where('id_karyawan', $penerimaan->dari_karyawan_id)
                            ->value('peran');
                    @endphp
                    ({{ $svRole ?? 'karyawan' }})
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
        </div>
    @empty
        <p class="text-xs text-gray-400 py-4 text-center">Belum ada penerimaan dari karyawan.</p>
    @endforelse
</div>

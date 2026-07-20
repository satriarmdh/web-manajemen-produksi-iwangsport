<!-- Tab Content: Log Serah Terima -->
<div>
    @forelse($detail->mutasiProduksi->sortByDesc('created_at') as $mutasi)
        <div class="relative pl-6 pb-4 {{ $loop->last ? 'pb-0' : '' }}">
            {{-- Vertical line --}}
            @if(!$loop->last)
                <div class="absolute left-2 top-4 bottom-0 w-0.5 bg-gray-200"></div>
            @endif
            {{-- Dot --}}
            <div class="absolute left-0 top-1 w-4 h-4 rounded-full bg-[#0F034D] ring-4 ring-white"></div>

            <div class="p-3 rounded-xl bg-gray-50 border border-gray-100 text-xs leading-snug">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <span class="font-bold text-[#0F034D]">+{{ number_format($mutasi->qty_pindah) }} pcs</span>
                    <span class="text-[10px] text-gray-400 font-medium">
                        {{ $mutasi->tgl_transaksi ? $mutasi->tgl_transaksi->format('d M H:i') : $mutasi->created_at->format('d M H:i') }}
                    </span>
                </div>
                <p class="text-gray-500">
                    Dari <span class="font-semibold text-gray-700">{{ $mutasi->dariKaryawan->name ?? 'Sistem' }}</span> ({{ ucfirst($mutasi->dari_tahapan ?? 'awal') }})
                    ke <span class="font-semibold text-gray-700">{{ $mutasi->keKaryawan->name ?? '-' }}</span> ({{ ucfirst($mutasi->ke_tahapan) }})
                </p>
            </div>
        </div>
    @empty
        <p class="text-xs text-gray-400 py-4 text-center">Belum ada aktivitas serah terima.</p>
    @endforelse
</div>

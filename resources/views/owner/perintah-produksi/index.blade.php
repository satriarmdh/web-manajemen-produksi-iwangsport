<x-layouts.admin>
    <x-slot name="title">Approval Perintah Produksi | Iwangsport</x-slot>
    <x-slot name="header">Approval Perintah Produksi</x-slot>

    {{-- Card Grid --}}
    @if($perintahProduksi->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($perintahProduksi as $wo)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-[#0F034D] text-sm">{{ $wo->nomor_wo }}</h3>
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                            Pending
                        </span>
                    </div>

                    {{-- Info --}}
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Mulai: {{ \Carbon\Carbon::parse($wo->tgl_mulai)->format('d M Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span>Oleh: {{ $wo->user->name ?? '-' }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            <span>{{ $wo->details->count() }} produk</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 pt-3 border-t border-gray-50">
                        {{-- Approve --}}
                        <form action="{{ route('owner.perintah-produksi.approve', $wo) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" onclick="return confirm('Setujui perintah produksi ini?')"
                                class="w-full py-2 text-xs font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                                Setujui
                            </button>
                        </form>
                        {{-- Reject --}}
                        <form action="{{ route('owner.perintah-produksi.reject', $wo) }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="alasan_penolakan" value="Ditolak oleh owner">
                            <button type="submit" onclick="return confirm('Tolak perintah produksi ini?')"
                                class="w-full py-2 text-xs font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                Tolak
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $perintahProduksi->links('pagination::tailwind') }}
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-gray-500 text-sm">Tidak ada perintah produksi yang menunggu persetujuan</p>
        </div>
    @endif
</x-layouts.admin>

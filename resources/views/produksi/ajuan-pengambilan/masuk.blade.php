<x-layouts.produksi>
    <x-slot:header>Ajuan Masuk</x-slot:header>

    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm mb-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-[#0F034D]">Ajuan Masuk</h3>
                <p class="text-sm text-gray-500 mt-1">Konfirmasi ajuan pengambilan barang dari karyawan produksi lain.</p>
            </div>
            <div class="sm:w-auto">
                <div class="rounded-xl bg-amber-50 border border-amber-100 px-3 py-2">
                    <p class="text-[10px] uppercase tracking-wide text-amber-700/70">Perlu konfirmasi</p>
                    <p class="text-sm font-bold text-amber-700">{{ $ajuanMasuk->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <section class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
        <div class="mb-4 rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-800">
            Konfirmasi dari Perintah Produksi paling lama terlebih dahulu jika memungkinkan.
        </div>
        <h3 class="font-bold text-[#0F034D] mb-3">Daftar Ajuan Masuk</h3>
        <div class="space-y-4">
            @forelse($ajuanMasuk->groupBy('id_perintah') as $idPerintah => $ajuanDalamPerintah)
                @php
                    $fifoIndex = $loop->iteration;
                    $perintah = $ajuanDalamPerintah->first()->perintahProduksi;
                    $totalQty = $ajuanDalamPerintah->sum('qty_ajuan');
                @endphp
                <details class="group rounded-2xl border border-gray-100 overflow-hidden shadow-sm" open>
                    <summary class="list-none cursor-pointer bg-[#0F034D] p-4 text-white">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-sm font-bold text-white">{{ $perintah->nomor_wo ?? 'Perintah Produksi' }}</p>
                                    <span class="rounded-full bg-white/15 px-2.5 py-1 text-xs font-bold text-white">Prioritas #{{ $fifoIndex }}</span>
                                </div>
                                <p class="text-xs text-blue-200 mt-1">Periode: {{ $perintah?->tgl_mulai ? \Carbon\Carbon::parse($perintah->tgl_mulai)->format('d M Y') : '-' }} - {{ $perintah?->tgl_selesai ? \Carbon\Carbon::parse($perintah->tgl_selesai)->format('d M Y') : '-' }}</p>
                                <p class="text-xs text-blue-200 mt-1">{{ $ajuanDalamPerintah->count() }} ajuan  -  <span class="text-amber-300 font-bold bg-white/10 px-2 py-0.5 rounded-lg">{{ number_format($totalQty, 0, ',', '.') }} pcs</span> diajukan</p>
                            </div>
                            <svg class="w-5 h-5 text-blue-200 transition-transform group-open:rotate-180 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"></path></svg>
                        </div>
                    </summary>

                    <div class="divide-y divide-gray-100 bg-white">
                        @foreach($ajuanDalamPerintah as $ajuan)
                            <div class="p-4">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex items-start gap-4">
                                        <!-- Large Qty Badge -->
                                        <div class="flex h-12 w-20 shrink-0 flex-col items-center justify-center rounded-xl bg-blue-50 border border-blue-100 text-[#0F034D]">
                                            <span class="text-[9px] uppercase font-semibold text-blue-500/80">Jumlah</span>
                                            <span class="text-sm font-black mt-0.5">{{ number_format($ajuan->qty_ajuan, 0, ',', '.') }} <span class="text-[9px] font-normal text-blue-600/80">pcs</span></span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $ajuan->produk->nama_produk ?? '-' }} - {{ ucfirst($ajuan->produk->warna ?? '-') }}</p>
                                            <p class="text-xs text-gray-500 mt-1">Dari {{ $ajuan->keKaryawan->name ?? '-' }} - <span class="font-semibold text-gray-700">{{ ucfirst($ajuan->ke_tahapan) }}</span></p>
                                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                                <span class="inline-flex items-center rounded-full bg-gray-50 px-2.5 py-0.5 text-[10px] font-medium text-gray-400">Diajukan {{ $ajuan->created_at?->format('d M Y H:i') }}</span>
                                                <span class="inline-flex rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700">Menunggu konfirmasi</span>
                                            </div>
                                            @if($ajuan->catatan_pengaju)
                                                <p class="mt-2.5 rounded-lg bg-gray-50 px-3 py-2 text-xs text-gray-600 border border-gray-100">Catatan: {{ $ajuan->catatan_pengaju }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 sm:w-56">
                                        <form action="{{ route('produksi.ajuan-pengambilan.approve', $ajuan) }}" method="POST"
                                            data-swal-confirm
                                            data-confirm-title="Setujui Ajuan Ini?"
                                            data-confirm-message="Barang akan dialihkan ke karyawan yang mengajukan. Pastikan stok ready mencukupi."
                                            data-confirm-button="Ya, Setujui">
                                            @csrf
                                            <button class="w-full rounded-xl bg-green-600 py-2.5 text-xs font-bold text-white hover:bg-green-700 transition-colors shadow-sm shadow-green-600/10 cursor-pointer">Setujui</button>
                                        </form>
                                        <button type="button" data-open-reject-modal data-reject-action="{{ route('produksi.ajuan-pengambilan.reject', $ajuan) }}" class="w-full rounded-xl bg-red-50 py-2.5 text-xs font-bold text-red-600 hover:bg-red-100 transition-colors cursor-pointer">Tolak</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </details>
            @empty
                <div class="rounded-xl bg-gray-50 border border-gray-100 p-6 text-center">
                    <p class="text-sm font-semibold text-gray-600">Belum ada ajuan masuk.</p>
                    <p class="text-xs text-gray-400 mt-1">Ajuan dari karyawan lain akan tampil di sini.</p>
                </div>
            @endforelse
        </div>
    </section>
    @include('produksi.ajuan-pengambilan.partials._reject-modal')
    @vite('resources/js/produksi/ajuan-pengambilan/masuk.js')
</x-layouts.produksi>

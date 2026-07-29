<x-layouts.admin>
    <x-slot:breadcrumb>
        <li class="flex items-center gap-1.5 text-gray-400">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"></path></svg>
            <span class="select-none">Transaksi Stok</span>
        </li>
        <li class="flex items-center">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <a href="{{ route('admin.pergerakan-stok.index', ['tab' => $pergerakanStok->jenis_pergerakan]) }}" class="text-gray-400 hover:text-[#0F034D] transition-colors">Pergerakan Stok</a>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            Detail {{ $pergerakanStok->nomor_transaksi }}
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Detail Transaksi Stok
    </x-slot:header>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 w-full overflow-hidden">
        <!-- Header Panel -->
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.pergerakan-stok.index', ['tab' => $pergerakanStok->jenis_pergerakan]) }}" 
                   class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-xl transition-colors cursor-pointer" title="Kembali">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg font-bold text-gray-900">{{ $pergerakanStok->nomor_transaksi }}</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pergerakanStok->jenis_pergerakan === 'masuk' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                            Stok {{ ucfirst($pergerakanStok->jenis_pergerakan) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">Tanggal: {{ $pergerakanStok->tanggal->format('d M Y') }}</p>
                </div>
            </div>
            
            <form method="POST" action="{{ route('admin.pergerakan-stok.destroy', $pergerakanStok) }}" data-confirm-delete>
                @csrf @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-xl transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Hapus
                </button>
            </form>
        </div>

        <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Info Header (1/3) -->
            <div class="space-y-6 lg:border-r lg:border-gray-100 lg:pr-8">
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Informasi Umum</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <dt class="text-gray-500">Dibuat Oleh</dt>
                            <dd class="font-medium text-gray-900">{{ $pergerakanStok->user?->name ?? '-' }}</dd>
                        </div>
                        @if($pergerakanStok->jenis_pergerakan === 'masuk')
                            <div class="flex justify-between py-1.5 border-b border-gray-50">
                                <dt class="text-gray-500">Supplier</dt>
                                <dd class="font-medium text-gray-900">{{ $pergerakanStok->supplier?->nama_supplier ?? '-' }}</dd>
                            </div>
                        @else
                            <div class="flex justify-between py-1.5 border-b border-gray-50">
                                <dt class="text-gray-500">Penerima</dt>
                                <dd class="font-medium text-gray-900">{{ $pergerakanStok->penerima ?? '-' }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <dt class="text-gray-500">Keterangan / Catatan</dt>
                            <dd class="font-medium text-gray-900 text-right max-w-[200px] break-words">{{ $pergerakanStok->catatan ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Bukti Lampiran -->
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Bukti Lampiran</h3>
                    @if($pergerakanStok->bukti)
                        <div class="relative rounded-xl border border-gray-100 overflow-hidden group bg-gray-50">
                            <img src="{{ asset('storage/' . $pergerakanStok->bukti) }}" alt="Bukti Lampiran" class="w-full h-auto max-h-48 object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <a href="{{ asset('storage/' . $pergerakanStok->bukti) }}" target="_blank" class="px-4 py-2 bg-white text-gray-800 text-xs font-bold rounded-lg shadow-md hover:bg-gray-100 transition-colors">Buka Gambar Asli</a>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic">Tidak ada lampiran bukti.</p>
                    @endif
                </div>
            </div>

            <!-- List Item (2/3) -->
            <div class="lg:col-span-2 space-y-4">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Detail Bahan Baku</h3>
                <div class="overflow-x-auto border border-gray-100 rounded-xl">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-500">Kode</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-500">Nama Bahan Baku</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-500">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($pergerakanStok->detailPergerakanStok as $detail)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3 text-gray-500 font-mono">{{ $detail->bahanBaku?->kode_bahan }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $detail->bahanBaku?->nama_bahan }} - {{ $detail->bahanBaku?->warna ? ucfirst($detail->bahanBaku->warna) : '-' }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900">
                                        {{ $pergerakanStok->jenis_pergerakan === 'masuk' ? '+' : '-' }}{{ $detail->jumlah }} {{ $detail->bahanBaku?->satuan }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>

<x-layouts.owner>
    <x-slot:breadcrumb>
        <li class="flex items-center gap-1.5 text-gray-400">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="select-none">Laporan & Riwayat</span>
        </li>
        <li class="flex items-center">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <a href="{{ route('owner.mutasi-bahan-baku.index', ['tab' => 'keluar']) }}" class="text-gray-400 hover:text-[#0F034D] transition-colors">Mutasi Bahan Baku</a>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            Detail {{ $perintahProduksi->nomor_wo }}
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Detail Mutasi Bahan Baku (Kain)
    </x-slot:header>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 w-full overflow-hidden">
        <!-- Header Panel -->
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('owner.mutasi-bahan-baku.index', ['tab' => 'keluar']) }}" 
                   class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-xl transition-colors cursor-pointer" title="Kembali">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg font-bold text-gray-900">{{ $perintahProduksi->nomor_wo }}</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                            Bahan Keluar (Penggunaan Kain)
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">Disetujui pada: {{ $perintahProduksi->approved_at ? $perintahProduksi->approved_at->format('d M Y H:i') : '-' }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Info Header (1/3) -->
            <div class="space-y-6 lg:border-r lg:border-gray-100 lg:pr-8">
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Informasi Umum</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <dt class="text-gray-500">Dibuat Oleh (Admin)</dt>
                            <dd class="font-medium text-gray-900">{{ $perintahProduksi->user?->name ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <dt class="text-gray-500">Disetujui Oleh</dt>
                            <dd class="font-medium text-gray-900">{{ $perintahProduksi->approver?->name ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <dt class="text-gray-500">Keperluan</dt>
                            <dd class="font-medium text-gray-900">Perintah Produksi / WO</dd>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50 gap-4">
                            <dt class="text-gray-500">Keterangan / Catatan</dt>
                            <dd class="font-medium text-gray-900 text-right max-w-[200px] break-words">Penggunaan kain utama untuk pelaksanaan proses pemotongan perintah produksi.</dd>
                        </div>
                    </dl>
                </div>

                <!-- Informasi Persetujuan & Link Pantau WO -->
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Informasi Persetujuan WO</h3>
                    <div class="rounded-xl border border-gray-100 p-4 bg-gray-50/50 space-y-3.5">
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tanggal Mulai:</span>
                                <span class="font-semibold text-gray-800">{{ $perintahProduksi->tgl_mulai->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Target Selesai:</span>
                                <span class="font-semibold text-gray-800">{{ $perintahProduksi->tgl_selesai ? $perintahProduksi->tgl_selesai->format('d M Y') : '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Status Produksi:</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-700 uppercase">{{ $perintahProduksi->status_production ?? $perintahProduksi->status_produksi }}</span>
                            </div>
                        </div>

                        <a href="{{ route('owner.pantau-progres.show', $perintahProduksi) }}" class="block w-full text-center py-2 bg-[#0F034D] hover:bg-[#0a0235] text-white text-xs font-bold rounded-lg shadow-sm transition-colors cursor-pointer">
                            Pantau Progres WO
                        </a>
                    </div>
                </div>
            </div>

            <!-- List Item (2/3) -->
            <div class="lg:col-span-2 space-y-4">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Detail Bahan Baku (Kain Utama)</h3>
                <div class="overflow-x-auto border border-gray-100 rounded-xl">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-500">Kode</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-500">Nama Bahan Baku</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-500">Jumlah Pakai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($perintahProduksi->riwayatPenggunaanKain as $detail)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3 text-gray-500 font-mono">{{ $detail->bahanBaku?->kode_bahan }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $detail->bahanBaku?->nama_bahan }} - {{ $detail->bahanBaku?->warna ? ucfirst($detail->bahanBaku->warna) : '-' }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-red-600">
                                        -{{ (int) $detail->jumlah_pakai }} Roll
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.owner>

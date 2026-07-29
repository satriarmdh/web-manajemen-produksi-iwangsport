<x-layouts.owner>
    <x-slot:breadcrumb>
        <li class="flex items-center gap-1.5 text-gray-400">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="select-none">Laporan & Riwayat</span>
        </li>
        <li class="flex items-center">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <a href="{{ route('owner.riwayat-penjualan.index') }}" class="text-gray-400 hover:text-[#0F034D] transition-colors">Riwayat Penjualan</a>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            Detail {{ $penjualan->nomor_invoice }}
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Detail Transaksi Penjualan
    </x-slot:header>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 w-full overflow-hidden">
        <!-- Header Panel -->
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('owner.riwayat-penjualan.index') }}" 
                   class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-xl transition-colors cursor-pointer" title="Kembali ke Daftar Penjualan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h1 class="text-lg font-bold text-gray-900">{{ $penjualan->nomor_invoice }}</h1>
                    <p class="text-xs text-gray-400 mt-0.5">Tanggal Transaksi: {{ $penjualan->tanggal->format('d M Y') }}</p>
                </div>
            </div>
            
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-green-50 border border-green-200 text-green-700 text-xs font-bold shadow-sm">
                <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                Selesai
            </span>
        </div>

        <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Info Header (1/3) -->
            <div class="space-y-6 lg:border-r lg:border-gray-100 lg:pr-8">
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Informasi Umum</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between py-1.5 border-b border-gray-50 gap-4">
                            <dt class="text-gray-500">Pelanggan</dt>
                            <dd class="font-medium text-gray-950 text-right">
                                <div class="font-bold text-[#0F034D]">{{ $penjualan->pelanggan->nama_pelanggan }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $penjualan->pelanggan->kode_pelanggan }} · {{ $penjualan->pelanggan->no_telp }}</div>
                            </dd>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <dt class="text-gray-500">Dibuat Oleh (Admin)</dt>
                            <dd class="font-medium text-gray-900 text-right">{{ $penjualan->user->name }}</dd>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <dt class="text-gray-500">Total Kuantitas</dt>
                            <dd class="font-medium text-gray-900 text-right">{{ $penjualan->total_item }} pcs</dd>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <dt class="text-gray-500">Total Nilai Transaksi</dt>
                            <dd class="font-bold text-[#0F034D] text-right">Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50 gap-4">
                            <dt class="text-gray-500">Keterangan / Catatan</dt>
                            <dd class="font-medium text-gray-900 text-right max-w-[200px] break-words">{{ $penjualan->catatan ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- List Item (2/3) -->
            <div class="lg:col-span-2 space-y-4">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Detail Produk Terjual</h3>
                <div class="overflow-x-auto border border-gray-100 rounded-xl">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-500">Kode Produk</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-500">Nama Produk</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-500">Harga Satuan</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-500">Qty</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-500">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($penjualan->detailPenjualan as $detail)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3 text-gray-500 font-mono">{{ $detail->produk?->kode_produk ?? '-' }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $detail->produk?->nama_produk }} - {{ $detail->produk?->warna ? ucfirst($detail->produk->warna) : '-' }}</td>
                                    <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center font-semibold text-gray-700">{{ $detail->qty }} pcs</td>
                                    <td class="px-4 py-3 text-right font-bold text-[#0F034D]">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50/50 border-t border-gray-100 font-bold">
                                <td colspan="4" class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Grand Total:</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-[#0F034D]">Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.owner>

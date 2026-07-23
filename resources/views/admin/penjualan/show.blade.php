<x-layouts.admin>
    <x-slot:breadcrumb>
        <li class="flex items-center">
            <a href="{{ route('admin.penjualan.index') }}" class="text-gray-400 hover:text-[#0F034D] transition-colors">Penjualan Produk</a>
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </li>
        <li class="flex items-center text-[#0F034D] font-semibold">{{ $penjualan->nomor_invoice }}</li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Detail Penjualan
    </x-slot:header>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 w-full">
        <!-- Header -->
        <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-t-xl">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.penjualan.index') }}" class="inline-flex items-center justify-center w-9 h-9 hover:bg-gray-50 rounded-xl text-gray-500 hover:text-[#0F034D] transition-colors border border-gray-200" title="Kembali ke Daftar Penjualan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h3 class="text-lg font-bold text-[#0F034D]">{{ $penjualan->nomor_invoice }}</h3>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $penjualan->tanggal->format('d M Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.penjualan.edit', $penjualan) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-600 text-sm font-medium rounded-xl transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
                <form method="POST" action="{{ route('admin.penjualan.destroy', $penjualan) }}" onsubmit="return confirm('Hapus transaksi {{ $penjualan->nomor_invoice }}? Stok produk akan dikembalikan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-xl transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus
                    </button>
                </form>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="px-6 py-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 border-b border-gray-100">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Pelanggan</p>
                <p class="text-sm font-semibold text-[#0F034D]">{{ $penjualan->pelanggan->nama_pelanggan }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $penjualan->pelanggan->kode_pelanggan }} · {{ $penjualan->pelanggan->no_telp }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Dibuat Oleh</p>
                <p class="text-sm font-semibold text-[#0F034D]">{{ $penjualan->user->name }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $penjualan->created_at->format('d M Y H:i') }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Total Item</p>
                <p class="text-sm font-semibold text-[#0F034D]">{{ $penjualan->total_item }} pcs</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Total Harga</p>
                <p class="text-sm font-bold text-[#0F034D]">Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</p>
            </div>
            @if($penjualan->catatan)
                <div class="sm:col-span-2 md:col-span-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Catatan</p>
                    <p class="text-sm text-gray-600">{{ $penjualan->catatan }}</p>
                </div>
            @endif
        </div>

        <!-- Items Table -->
        <div class="px-6 py-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Items Produk</h4>
            <div class="overflow-x-auto border border-gray-100 rounded-xl">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Produk</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Harga Satuan</th>
                            <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">Qty</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($penjualan->detailPenjualan as $detail)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $detail->produk->nama_produk }} - {{ $detail->produk->warna }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 text-right">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $detail->qty }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-[#0F034D] text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50/50 border-t border-gray-100">
                            <td colspan="3" class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Grand Total:</td>
                            <td class="px-4 py-3 text-right text-sm font-bold text-[#0F034D]">Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>

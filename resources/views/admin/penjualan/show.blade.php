<x-layouts.admin>
    <x-slot:breadcrumb>
        <li class="flex items-center gap-1.5 text-gray-400">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"></path></svg>
            <span class="select-none">Transaksi Stok</span>
        </li>
        <li class="flex items-center">
            <svg class="w-3 h-3 text-gray-300 mx-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <a href="{{ route('admin.penjualan.index') }}" class="text-gray-400 hover:text-[#0F034D] transition-colors">Penjualan Produk</a>
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
                <a href="{{ (str_contains(url()->previous(), route('admin.penjualan.index')) ? url()->previous() : route('admin.penjualan.index')) }}" 
                   class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-xl transition-colors cursor-pointer" title="Kembali ke Daftar Penjualan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg font-bold text-gray-900">{{ $penjualan->nomor_invoice }}</h1>
                        @php $st = $penjualan->status_pembayaran; @endphp
                        @if($st === 'lunas')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">Lunas</span>
                        @elseif($st === 'sebagian')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">Sebagian / DP</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">Belum Bayar</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">Tanggal: {{ $penjualan->tanggal->format('d M Y') }}</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.penjualan.cetak-pdf', $penjualan) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-sm font-semibold rounded-xl transition-all shadow-md shadow-[#0F034D]/20 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Nota (PDF)
                </a>
                <a href="{{ route('admin.penjualan.edit', $penjualan) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-600 text-sm font-medium rounded-xl transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
                <button type="button" data-swal-delete data-url="{{ route('admin.penjualan.destroy', $penjualan) }}" data-method="DELETE" data-message="Transaksi '{{ $penjualan->nomor_invoice }}' akan dihapus. Stok produk akan dikembalikan." class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-xl transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Hapus
                </button>
            </div>
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
                                <div class="text-xs text-gray-400 mt-0.5">{{ $penjualan->pelanggan->no_telp }}</div>
                            </dd>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <dt class="text-gray-500">Dibuat Oleh</dt>
                            <dd class="font-medium text-gray-900 text-right">{{ $penjualan->user->name }}</dd>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <dt class="text-gray-500">Total Item</dt>
                            <dd class="font-medium text-gray-900 text-right">{{ $penjualan->total_item }} pcs</dd>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <dt class="text-gray-500">Total Harga</dt>
                            <dd class="font-bold text-[#0F034D] text-right">Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <dt class="text-gray-500">Telah Dibayar</dt>
                            <dd class="font-bold text-green-700 text-right">Rp {{ number_format($penjualan->total_dibayar, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <dt class="text-gray-500">Sisa Tagihan</dt>
                            <dd class="font-bold {{ $penjualan->sisa_pembayaran > 0 ? 'text-amber-600' : 'text-gray-500' }} text-right">
                                Rp {{ number_format($penjualan->sisa_pembayaran, 0, ',', '.') }}
                            </dd>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50 gap-4">
                            <dt class="text-gray-500">Keterangan / Catatan</dt>
                            <dd class="font-medium text-gray-900 text-right max-w-[200px] break-words">{{ $penjualan->catatan ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- List Item + Payment History (2/3) -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Detail Produk -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Detail Produk</h3>
                    <div class="overflow-x-auto border border-gray-100 rounded-xl">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-500">Kode</th>
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
                                    <td colspan="3" class="px-4 py-3 text-right text-gray-700">Total:</td>
                                    <td class="px-4 py-3 text-center text-gray-900">{{ $penjualan->total_item }} pcs</td>
                                    <td class="px-4 py-3 text-right text-[#0F034D]">Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Riwayat Pembayaran & Galeri Resi -->
                <div class="space-y-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Riwayat Pembayaran & Bukti Transfer</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Daftar pembayaran dan berkas resi transfer yang telah dilampirkan admin</p>
                        </div>
                        <button type="button" onclick="openTambahPembayaranModal()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#0F034D] hover:bg-[#0a0235] text-white text-xs font-semibold rounded-xl transition-all cursor-pointer shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Tambah Pembayaran
                        </button>
                    </div>

                    <div class="overflow-x-auto border border-gray-100 rounded-xl">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-500">Tanggal</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-500">Metode</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-500">Nominal</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-500">Petugas</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-500">Bukti Resi Transfer</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($penjualan->pembayaranPenjualan as $bayar)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-4 py-3 text-gray-700 font-medium">
                                            {{ $bayar->tanggal_bayar->format('d M Y, H:i') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $bayar->metode_pembayaran === 'transfer' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-green-50 text-green-700 border border-green-200' }}">
                                                {{ ucfirst($bayar->metode_pembayaran) }}
                                            </span>
                                            @if($bayar->catatan)
                                                <p class="text-[11px] text-gray-400 mt-0.5">{{ $bayar->catatan }}</p>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right font-bold text-[#0F034D]">
                                            Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 text-xs">
                                            {{ $bayar->user?->name ?? 'System' }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($bayar->bukti_pembayaran)
                                                <button type="button" onclick="previewImage('{{ asset('storage/' . $bayar->bukti_pembayaran) }}', 'Bukti Resi Transfer - {{ $bayar->tanggal_bayar->format('d M Y') }}')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold transition-colors cursor-pointer border border-blue-200">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    Lihat Resi
                                                </button>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Tanpa Berkas</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button type="button" data-swal-delete data-url="{{ route('admin.penjualan.pembayaran.destroy', $bayar) }}" data-method="DELETE" data-message="Hapus entri pembayaran Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}?" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Hapus Entri Pembayaran">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-sm text-gray-400">
                                            Belum ada aktivitas pembayaran dicatat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.penjualan.partials._tambah-pembayaran-modal')
    @include('admin.penjualan.partials._preview-image-modal')

    @vite([
        'resources/css/global-modal.css',
        'resources/js/admin/custom-forms.js',
        'resources/js/admin/penjualan/show.js'
    ])
</x-layouts.admin>

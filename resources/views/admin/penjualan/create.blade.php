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
            Tambah Transaksi Penjualan
        </li>
    </x-slot:breadcrumb>

    <x-slot:header>
        Tambah Transaksi Penjualan
    </x-slot:header>

    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 pt-6 pb-4 border-b border-gray-100 rounded-t-xl">
            <h3 class="text-lg font-bold text-[#0F034D]">Form Penjualan</h3>
        </div>

        <form method="POST" action="{{ route('admin.penjualan.store') }}" id="penjualan-form" class="px-6 py-6 space-y-6">
            @csrf
            <!-- Invoice Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-700 focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors @error('tanggal') border-red-300 @enderror">
                    @error('tanggal') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pelanggan <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="hidden" name="pelanggan_id" id="pelanggan_value" required>
                        <input type="text" id="pelanggan_input" placeholder="Cari / pilih pelanggan..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500 @error('pelanggan_id') border-red-300 @enderror">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="dropdown-arrow w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                        <div id="pelanggan_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-52 overflow-y-auto hidden">
                            <div class="p-2">
                                @foreach($pelanggan as $p)
                                <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="{{ $p->id }}" data-text="{{ $p->kode_pelanggan }} - {{ $p->nama_pelanggan }}">
                                    <span class="text-sm font-medium text-gray-700">{{ $p->nama_pelanggan }} <span class="text-gray-400">({{ $p->kode_pelanggan }})</span></span>
                                    <svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                @endforeach
                            </div>
                            <div id="pelanggan_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                        </div>
                    </div>
                    @error('pelanggan_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Detail Produk -->
            <div>
                <h2 class="text-lg font-bold text-[#0F034D] mb-4">Detail Produk</h2>

                <!-- Form Input Detail -->
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Produk <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="hidden" id="input_produk_value">
                                <input type="text" id="input_produk_input" placeholder="Ketik untuk mencari produk..." autocomplete="off" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-2.5 pr-10 text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none transition-colors text-gray-500">
                                <svg class="dropdown-arrow absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                <div id="input_produk_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                    <div class="p-2">
                                        @foreach($produk as $p)
                                            <div class="dropdown-option flex items-center justify-between gap-2 px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors" data-value="{{ $p->id }}" data-text="{{ $p->nama_produk }} - {{ ucfirst($p->warna) }}">
                                                <div class="min-w-0"><div class="text-sm font-medium text-gray-900 truncate">{{ $p->nama_produk }}</div><div class="text-xs text-gray-500">{{ ucfirst($p->warna) }} · Stok {{ $p->stok }} pcs</div></div>
                                                <svg class="check-icon w-4 h-4 text-[#0F034D] hidden shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div id="input_produk_no_results" class="hidden p-4 text-center text-sm text-gray-500">Produk tidak ditemukan</div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jumlah PCS <span class="text-red-500">*</span> <span id="input-stock-hint" class="text-[11px] text-gray-400 font-normal ml-1">(Pilih produk)</span></label>
                            <input type="number" id="input-qty" min="1" placeholder="Contoh: 10" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] outline-none text-gray-700">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Harga Satuan</label>
                            <input type="text" id="input-harga" readonly value="-" class="w-full bg-gray-100 border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Subtotal</label>
                            <div class="flex gap-2">
                                <input type="text" id="input-subtotal" readonly value="-" class="min-w-0 flex-1 bg-gray-100 border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-600 cursor-not-allowed">
                                <button type="button" id="btn-tambah-item" disabled class="px-5 py-2.5 text-sm font-semibold text-white bg-[#0F034D] rounded-xl hover:bg-[#0a0235] transition-all disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed cursor-pointer shrink-0">Tambah</button>
                            </div>
                        </div>
                    </div>
                    <p id="item-form-error" class="hidden mt-3 text-xs font-medium text-red-500"></p>
                </div>

                <!-- Tabel Detail -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="border-b-2 border-gray-200">
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600">No</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600">Produk</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold text-gray-600">Harga</th>
                            <th class="text-center py-3 px-4 text-xs font-semibold text-gray-600">Qty</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold text-gray-600">Subtotal</th>
                            <th class="text-center py-3 px-4 text-xs font-semibold text-gray-600">Aksi</th>
                        </tr></thead>
                        <tbody id="items-body"><tr id="empty-item-row"><td colspan="6" class="py-8 text-center text-sm text-gray-400">Belum ada produk ditambahkan.</td></tr></tbody>
                        <tfoot><tr class="border-t-2 border-gray-200 bg-gray-50">
                            <td colspan="4" class="py-3 px-4 text-right font-semibold text-[#0F034D]">Total:</td>
                            <td class="py-3 px-4 text-right font-bold text-[#0F034D]" id="grand-total">Rp 0</td><td></td>
                        </tr></tfoot>
                    </table>
                </div>
                @error('items') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <!-- Catatan -->
            <div>
                <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-1.5">Catatan</label>
                <textarea name="catatan" id="catatan" rows="2" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-700 focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors @error('catatan') border-red-300 @enderror" placeholder="Catatan tambahan (opsional)">{{ old('catatan') }}</textarea>
                @error('catatan') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.penjualan.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-xl transition-colors cursor-pointer">Batal</a>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#0F034D] hover:bg-[#0a0235] text-white text-sm font-medium rounded-xl transition-all shadow-md shadow-[#0F034D]/20 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>

    @php
        $produkJson = $produk->map(fn($p) => ['id' => $p->id, 'nama' => $p->nama_produk, 'warna' => $p->warna, 'stok' => $p->stok, 'harga' => $p->harga_satuan]);
    @endphp

    <!-- Hidden produk data for JS -->
    <script type="application/json" id="produk-data">
        {!! json_encode($produkJson) !!}
    </script>

    @vite([
        'resources/js/admin/custom-forms.js',
        'resources/js/admin/penjualan/create.js'
    ])
</x-layouts.admin>

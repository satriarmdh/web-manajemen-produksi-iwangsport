    {{-- MODAL DETAIL TRANSAKSI --}}
    {{-- ========================================= --}}
    <div id="detail-modal" class="slide-panel">
        <!-- Backdrop -->
        <div class="slide-panel-backdrop" data-panel-close="detail-modal"></div>

        <!-- Panel Body -->
        <div class="slide-panel-body">
            <!-- Header -->
            <div class="slide-panel-header">
                <div class="slide-panel-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="slide-panel-header-title" id="detail-title">Detail Transaksi</h3>
                    <p class="text-xs text-gray-500 mt-0.5" id="detail-date"></p>
                </div>
                <button class="slide-panel-close" data-panel-close="detail-modal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="slide-panel-content">
                <div id="detail-body">
                    <!-- Filled by JS -->
                </div>
            </div>

            <!-- Footer -->
            <div class="slide-panel-footer">
                <button type="button" onclick="closePanel('detail-modal')" class="btn-panel-cancel">Tutup</button>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- SLIDE PANEL: TAMBAH STOK MASUK --}}
    {{-- ========================================= --}}
    <div id="add-modal-masuk" class="slide-panel">
        <div class="slide-panel-backdrop" data-panel-close="add-modal-masuk"></div>
        <div class="slide-panel-body">
            <div class="slide-panel-header">
                <div class="slide-panel-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m7-7-7 7-7-7"/>
                    </svg>
                </div>
                <h3 class="slide-panel-header-title">Tambah Stok Masuk</h3>
                <button class="slide-panel-close" data-panel-close="add-modal-masuk">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.pemasukan-bahan.store') }}" enctype="multipart/form-data" id="addFormMasuk" class="slide-panel-content">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Bahan Baku <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="hidden" name="bahan_baku_id" id="masuk_bahan_baku_value" required>
                            <input type="text" id="masuk_bahan_baku_input" placeholder="Pilih Bahan Baku..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="dropdown-arrow w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            <div id="masuk_bahan_baku_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                <div class="p-2">
                                    @foreach($bahanBakuAll as $b)
                                    <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="{{ $b->id }}" data-text="{{ ucwords($b->nama_bahan.' - '.$b->warna) }}" data-satuan="{{ $b->satuan }}">
                                        <span class="text-sm font-medium text-gray-700">{{ ucwords($b->nama_bahan.' - '.$b->warna) }} <span class="text-gray-400">({{ $b->kode_bahan }})</span></span>
                                        <svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    @endforeach
                                </div>
                                <div id="masuk_bahan_baku_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah <span id="masuk_satuan_label" class="text-gray-400 font-normal"></span><span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="quantity" min="1" required class="w-full px-4 py-2.5 pr-20 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] text-sm transition-colors" placeholder="Masukkan jumlah">
                            <span id="masuk_satuan_badge" class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-1 rounded-md">-</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Supplier</label>
                        <div class="relative">
                            <input type="hidden" name="supplier_id" id="masuk_supplier_value">
                            <input type="text" id="masuk_supplier_input" placeholder="Pilih Supplier (opsional)..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="dropdown-arrow w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            <div id="masuk_supplier_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                <div class="p-2">
                                    <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="" data-text="Tidak Ada">
                                        <span class="text-sm font-medium text-gray-700">Tidak Ada (opsional)</span>
                                        <svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    @foreach($suppliers as $s)
                                    <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="{{ $s->id }}" data-text="{{ $s->nama_supplier }}">
                                        <span class="text-sm font-medium text-gray-700">{{ $s->nama_supplier }}</span>
                                        <svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    @endforeach
                                </div>
                                <div id="masuk_supplier_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] text-sm transition-colors" placeholder="Catatan tambahan..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Bukti Pembelian (Invoice/Foto)</label>
                        <input type="file" name="bukti_pembelian" accept="image/*,.pdf" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:bg-gray-100 file:text-gray-700 transition-colors">
                        <p class="text-xs text-gray-500 mt-1">Pilih file atau ambil foto langsung dari perangkat</p>
                    </div>
                </div>
            </form>

            <div class="slide-panel-footer">
                <button type="button" onclick="closePanel('add-modal-masuk')" class="btn-panel-cancel">Batal</button>
                <button type="submit" form="addFormMasuk" class="btn-panel-submit">Simpan</button>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- SLIDE PANEL: TAMBAH STOK KELUAR --}}
    {{-- ========================================= --}}
    <div id="add-modal-keluar" class="slide-panel">
        <div class="slide-panel-backdrop" data-panel-close="add-modal-keluar"></div>
        <div class="slide-panel-body">
            <div class="slide-panel-header">
                <div class="slide-panel-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19V5m-7 7 7-7 7 7"/>
                    </svg>
                </div>
                <h3 class="slide-panel-header-title">Tambah Stok Keluar</h3>
                <button class="slide-panel-close" data-panel-close="add-modal-keluar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.pengeluaran-bahan.store') }}" enctype="multipart/form-data" id="addFormKeluar" class="slide-panel-content">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Bahan Baku <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="hidden" name="bahan_baku_id" id="keluar_bahan_baku_value" required>
                            <input type="text" id="keluar_bahan_baku_input" placeholder="Pilih Bahan Baku (Non-Kain)..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="dropdown-arrow w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            <div id="keluar_bahan_baku_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                <div class="p-2">
                                    @foreach($bahanBakuNonKain as $b)
                                    <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="{{ $b->id }}" data-text="{{ ucwords($b->nama_bahan.' - '.$b->warna) }}" data-satuan="{{ $b->satuan }}">
                                        <span class="text-sm font-medium text-gray-700">{{ ucwords($b->nama_bahan.' - '.$b->warna) }} <span class="text-gray-400">({{ $b->kode_bahan }})</span> <span class="text-xs text-gray-400">- Stok: {{ $b->stok }}</span></span>
                                        <svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    @endforeach
                                </div>
                                <div id="keluar_bahan_baku_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah <span id="keluar_satuan_label" class="text-gray-400 font-normal"></span><span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="quantity" min="1" required class="w-full px-4 py-2.5 pr-20 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] text-sm transition-colors" placeholder="Masukkan jumlah">
                            <span id="keluar_satuan_badge" class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-1 rounded-md">-</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Penerima <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="hidden" name="penerima" id="keluar_penerima_value" required>
                            <input type="text" id="keluar_penerima_input" placeholder="Pilih Penerima..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="dropdown-arrow w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            <div id="keluar_penerima_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                <div class="p-2">
                                    @foreach($karyawan as $k)
                                    <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="{{ $k->name }}" data-text="{{ $k->name }}">
                                        <span class="text-sm font-medium text-gray-700">{{ $k->name }} <span class="text-xs text-gray-400">({{ ucfirst($k->role) }})</span></span>
                                        <svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    @endforeach
                                </div>
                                <div id="keluar_penerima_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] text-sm transition-colors" placeholder="Catatan tambahan..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Bukti Pengeluaran (Foto)</label>
                        <input type="file" name="bukti_pengeluaran" accept="image/*,.pdf" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:bg-gray-100 file:text-gray-700 transition-colors">
                        <p class="text-xs text-gray-500 mt-1">Pilih file atau ambil foto langsung dari perangkat</p>
                    </div>
                </div>
            </form>

            <div class="slide-panel-footer">
                <button type="button" onclick="closePanel('add-modal-keluar')" class="btn-panel-cancel">Batal</button>
                <button type="submit" form="addFormKeluar" class="btn-panel-submit">Simpan</button>
            </div>
        </div>
    </div>

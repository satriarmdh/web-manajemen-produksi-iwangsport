    {{-- MODAL TAMBAH BAHAN BAKU --}}
    {{-- ========================================= --}}
    <div id="add-modal" class="slide-panel">
        <div class="slide-panel-backdrop" data-panel-close></div>
        <div class="slide-panel-body">
            <div class="slide-panel-header">
                <div class="slide-panel-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <h2 class="slide-panel-header-title">Tambah Bahan Baku</h2>
                <button class="slide-panel-close" data-panel-close><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form action="{{ route('admin.bahan-baku.store') }}" method="POST" id="addForm" class="slide-panel-content">
                @csrf
                <div class="space-y-4">
                    <input type="hidden" id="add_kode_bahan">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Bahan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_bahan" required placeholder="Contoh: Kain Cotton Combed 30s Hitam" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Warna Dasar <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="hidden" name="warna" id="add_warna_value" required>
                            <input type="text" id="add_warna_input" placeholder="Pilih Warna..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="dropdown-arrow w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            <div id="add_warna_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                <div class="p-2">
                                    <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="hitam" data-text="Hitam"><span class="text-sm font-medium text-gray-700">Hitam</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                    <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="putih" data-text="Putih"><span class="text-sm font-medium text-gray-700">Putih</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                    <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="abu-abu" data-text="Abu-Abu"><span class="text-sm font-medium text-gray-700">Abu-Abu</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                    <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="navy" data-text="Navy"><span class="text-sm font-medium text-gray-700">Navy</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                    <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="silver" data-text="Silver"><span class="text-sm font-medium text-gray-700">Silver</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                    <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="biru" data-text="Biru"><span class="text-sm font-medium text-gray-700">Biru</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                </div>
                                <div id="add_warna_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="hidden" name="kategori" id="add_kategori_value" data-prefixes="{{ json_encode($nextNumbers) }}" required>
                                <input type="text" id="add_kategori_input" placeholder="Pilih..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-500">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="dropdown-arrow w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                                <div id="add_kategori_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                    <div class="p-2">
                                        <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="kain" data-text="Kain"><span class="text-sm font-medium text-gray-700">Kain</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                        <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="bahan_pendukung" data-text="Bahan Pendukung"><span class="text-sm font-medium text-gray-700">Bahan Pendukung</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                    </div>
                                    <div id="add_kategori_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                            <div class="relative pointer-events-none">
                                <input type="hidden" name="satuan" id="add_satuan_value" required>
                                <input type="text" id="add_satuan_input" readonly placeholder="Otomatis mengikuti kategori..." autocomplete="off" class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-xl bg-gray-50 transition-colors text-sm text-gray-600 cursor-not-allowed">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="dropdown-arrow w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                                <div id="add_satuan_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto hidden">
                                    <div class="p-2">
                                        <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="roll" data-text="Roll"><span class="text-sm font-medium text-gray-700">Roll</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                        <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="pcs" data-text="Pieces (Pcs)"><span class="text-sm font-medium text-gray-700">Pieces (Pcs)</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                    </div>
                                    <div id="add_satuan_no_results" class="hidden p-4 text-center text-sm text-gray-500">Tidak ditemukan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Stok</label>
                            <input type="number" name="stok" min="0" placeholder="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Stok Minimal</label>
                            <input type="number" name="stok_minimal" min="0" placeholder="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                            <p class="text-xs text-gray-400 mt-1">Kosongkan/0 jika tidak ingin monitoring stok menipis</p>
                        </div>
                    </div>
                </div>
            </form>
            <div class="slide-panel-footer">
                <button type="button" class="btn-panel-cancel" data-panel-close>Batal</button>
                <button type="submit" form="addForm" class="btn-panel-submit">Simpan Data</button>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}

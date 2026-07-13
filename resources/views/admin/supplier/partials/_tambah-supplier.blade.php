    {{-- MODAL TAMBAH SUPPLIER --}}
    {{-- ========================================= --}}
    <div id="add-modal" class="slide-panel">
        <div class="slide-panel-backdrop" data-panel-close></div>
        <div class="slide-panel-body">
            <div class="slide-panel-header">
                <div class="slide-panel-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <h2 class="slide-panel-header-title">Tambah Supplier</h2>
                <button class="slide-panel-close" data-panel-close><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form action="{{ route('admin.supplier.store') }}" method="POST" id="addForm" class="slide-panel-content">
                @csrf
                <div class="space-y-4">
                    <input type="hidden" id="add_kode_supplier" data-next-number="{{ $nextNumber }}">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Supplier <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_supplier" required placeholder="Contoh: PT Tekstil Jaya Abadi" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori Bahan <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <input type="checkbox" name="kategori[]" value="kain" id="add_kategori_kain" class="hidden" onchange="updateKategoriCheckbox(this, 'add_kategori_kain')">
                                <div id="add_kategori_kain_wrapper" onclick="document.getElementById('add_kategori_kain').click()" class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                    <div id="add_kategori_kain_box" class="relative flex shrink-0 items-center justify-center w-5 h-5 rounded border-2 border-gray-300 transition-all">
                                        <svg id="add_kategori_kain_icon" class="w-3 h-3 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span id="add_kategori_kain_text" class="text-sm font-medium text-gray-700">Kain</span>
                                </div>
                            </div>
                            <div>
                                <input type="checkbox" name="kategori[]" value="benang" id="add_kategori_benang" class="hidden" onchange="updateKategoriCheckbox(this, 'add_kategori_benang')">
                                <div id="add_kategori_benang_wrapper" onclick="document.getElementById('add_kategori_benang').click()" class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                    <div id="add_kategori_benang_box" class="relative flex shrink-0 items-center justify-center w-5 h-5 rounded border-2 border-gray-300 transition-all">
                                        <svg id="add_kategori_benang_icon" class="w-3 h-3 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span id="add_kategori_benang_text" class="text-sm font-medium text-gray-700">Benang</span>
                                </div>
                            </div>
                            <div>
                                <input type="checkbox" name="kategori[]" value="kancing" id="add_kategori_kancing" class="hidden" onchange="updateKategoriCheckbox(this, 'add_kategori_kancing')">
                                <div id="add_kategori_kancing_wrapper" onclick="document.getElementById('add_kategori_kancing').click()" class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                    <div id="add_kategori_kancing_box" class="relative flex shrink-0 items-center justify-center w-5 h-5 rounded border-2 border-gray-300 transition-all">
                                        <svg id="add_kategori_kancing_icon" class="w-3 h-3 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span id="add_kategori_kancing_text" class="text-sm font-medium text-gray-700">Kancing</span>
                                </div>
                            </div>
                            <div>
                                <input type="checkbox" name="kategori[]" value="resleting" id="add_kategori_resleting" class="hidden" onchange="updateKategoriCheckbox(this, 'add_kategori_resleting')">
                                <div id="add_kategori_resleting_wrapper" onclick="document.getElementById('add_kategori_resleting').click()" class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                    <div id="add_kategori_resleting_box" class="relative flex shrink-0 items-center justify-center w-5 h-5 rounded border-2 border-gray-300 transition-all">
                                        <svg id="add_kategori_resleting_icon" class="w-3 h-3 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span id="add_kategori_resleting_text" class="text-sm font-medium text-gray-700">Resleting</span>
                                </div>
                            </div>
                            <div>
                                <input type="checkbox" name="kategori[]" value="aksesoris" id="add_kategori_aksesoris" class="hidden" onchange="updateKategoriCheckbox(this, 'add_kategori_aksesoris')">
                                <div id="add_kategori_aksesoris_wrapper" onclick="document.getElementById('add_kategori_aksesoris').click()" class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                    <div id="add_kategori_aksesoris_box" class="relative flex shrink-0 items-center justify-center w-5 h-5 rounded border-2 border-gray-300 transition-all">
                                        <svg id="add_kategori_aksesoris_icon" class="w-3 h-3 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span id="add_kategori_aksesoris_text" class="text-sm font-medium text-gray-700">Aksesoris</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Pilih satu atau lebih kategori bahan yang disuplai.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Kontak <span class="text-red-500">*</span></label>
                            <input type="text" name="kontak" required placeholder="08xxxxxxxxxx" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required placeholder="supplier@email.com" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat <span class="text-red-500">*</span></label>
                        <textarea name="alamat" required rows="3" placeholder="Alamat lengkap supplier" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan</label>
                        <textarea name="catatan" rows="2" placeholder="Catatan tambahan (opsional)" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm"></textarea>
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

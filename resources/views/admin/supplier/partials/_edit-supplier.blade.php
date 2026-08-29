    {{-- MODAL EDIT SUPPLIER --}}
    {{-- ========================================= --}}
    <div id="edit-modal" class="slide-panel">
        <div class="slide-panel-backdrop" data-panel-close></div>
        <div class="slide-panel-body">
            <div class="slide-panel-header">
                <div class="slide-panel-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <h2 class="slide-panel-header-title">Edit Supplier</h2>
                <button class="slide-panel-close" data-panel-close><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form action="" method="POST" id="editForm" class="slide-panel-content">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <input type="hidden" id="edit_kode">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Supplier <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_supplier" id="edit_nama" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori Bahan <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-2" id="edit_kategori_container">
                            <div>
                                <input type="checkbox" name="kategori[]" value="kain" id="edit_kategori_kain" class="edit_kategori hidden" onchange="updateKategoriCheckbox(this, 'edit_kategori_kain')">
                                <div id="edit_kategori_kain_wrapper" onclick="document.getElementById('edit_kategori_kain').click()" class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                    <div id="edit_kategori_kain_box" class="relative flex shrink-0 items-center justify-center w-5 h-5 rounded border-2 border-gray-300 transition-all">
                                        <svg id="edit_kategori_kain_icon" class="w-3 h-3 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span id="edit_kategori_kain_text" class="text-sm font-medium text-gray-700">Kain</span>
                                </div>
                            </div>
                            <div>
                                <input type="checkbox" name="kategori[]" value="bahan_pendukung" id="edit_kategori_bahan_pendukung" class="edit_kategori hidden" onchange="updateKategoriCheckbox(this, 'edit_kategori_bahan_pendukung')">
                                <div id="edit_kategori_bahan_pendukung_wrapper" onclick="document.getElementById('edit_kategori_bahan_pendukung').click()" class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                    <div id="edit_kategori_bahan_pendukung_box" class="relative flex shrink-0 items-center justify-center w-5 h-5 rounded border-2 border-gray-300 transition-all">
                                        <svg id="edit_kategori_bahan_pendukung_icon" class="w-3 h-3 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span id="edit_kategori_bahan_pendukung_text" class="text-sm font-medium text-gray-700">Bahan Pendukung</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Kontak <span class="text-red-500">*</span></label>
                            <input type="text" name="kontak" id="edit_kontak" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="edit_email" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat <span class="text-red-500">*</span></label>
                        <textarea name="alamat" id="edit_alamat" required rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan</label>
                        <textarea name="catatan" id="edit_catatan" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm"></textarea>
                    </div>

                    <!-- Checkbox is_aktif -->
                    <div>
                        <input type="hidden" name="is_aktif" value="0">
                        <input type="checkbox" name="is_aktif" id="edit_is_aktif" value="1" class="hidden" onchange="updateCheckbox(this, 'edit_cb')">
                        <div id="edit_cb_wrapper" onclick="document.getElementById('edit_is_aktif').click()" class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-all">
                            <div id="edit_cb_box" class="relative flex shrink-0 items-center justify-center w-5 h-5 rounded border-2 border-gray-300 transition-all">
                                <svg id="edit_cb_icon" class="w-3 h-3 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <span id="edit_cb_text" class="text-sm font-semibold text-gray-700">Aktif</span>
                                <p class="text-xs text-gray-500">Supplier ini dapat digunakan dalam proses produksi.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="slide-panel-footer">
                <button type="button" class="btn-panel-cancel" data-panel-close>Batal</button>
                <button type="submit" form="editForm" class="btn-panel-submit">Simpan Perubahan</button>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}

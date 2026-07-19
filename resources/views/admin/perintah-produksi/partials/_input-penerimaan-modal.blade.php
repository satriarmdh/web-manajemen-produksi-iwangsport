{{-- Modal Input Penerimaan Hasil Produksi --}}
<div id="inputPenerimaanModal" class="slide-panel">
    <div class="slide-panel-backdrop" data-close-penerimaan-modal></div>
    <div class="slide-panel-body">
        {{-- Header --}}
        <div class="slide-panel-header">
            <div class="slide-panel-header-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <h2 class="slide-panel-header-title">Input Penerimaan Hasil Produksi</h2>
                <p class="text-xs text-gray-500 mt-0.5" id="input_produk_nama">-</p>
            </div>
            <button type="button" class="slide-panel-close" data-close-penerimaan-modal>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form Content --}}
        <form action="{{ route('admin.penerimaan-hasil-produksi.store') }}" method="POST" enctype="multipart/form-data" id="formInputPenerimaan" class="slide-panel-content">
            @csrf
            <input type="hidden" name="perintah_produksi_detail_id" id="input_detail_id">

            <div class="space-y-5">
                {{-- Summary Info Produk --}}
                <div class="grid grid-cols-3 gap-2 p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Estimasi</p>
                        <p class="text-sm font-bold text-gray-700"><span id="input_estimasi">0</span> pcs</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Sudah Diterima</p>
                        <p class="text-sm font-bold text-green-600"><span id="input_diterima">0</span> pcs</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Sisa</p>
                        <p class="text-sm font-bold text-amber-600"><span id="input_sisa">0</span> pcs</p>
                    </div>
                </div>

                {{-- Pilih Karyawan (PALING ATAS) --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Pilih Karyawan Finishing <span class="text-red-500">*</span>
                    </label>
                    <select name="dari_karyawan_id" id="input_dari_karyawan" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-[#0F034D] focus:ring-1 focus:ring-[#0F034D]/20 outline-none transition-all text-sm">
                        <option value="">-- Pilih Karyawan --</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1.5">Hanya karyawan dengan stok ready yang ditampilkan</p>
                </div>

                {{-- Card Stat Karyawan yang Dipilih --}}
                <div id="karyawan_stat_card" class="hidden">
                    <div class="bg-gradient-to-br from-[#0F034D]/5 to-purple-50 rounded-xl border border-[#0F034D]/10 p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-8 h-8 rounded-lg bg-[#0F034D] flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Data Karyawan Terpilih</p>
                                <p class="text-sm font-bold text-[#0F034D]" id="stat_karyawan_nama">-</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="bg-white/60 rounded-lg p-2.5 border border-white">
                                <p class="text-xs text-gray-500 mb-0.5">Barang Ready</p>
                                <p class="text-lg font-bold text-blue-600" id="stat_qty_ready">0</p>
                            </div>
                            <div class="bg-white/60 rounded-lg p-2.5 border border-white">
                                <p class="text-xs text-gray-500 mb-0.5">Diserahkan</p>
                                <p class="text-lg font-bold text-green-600" id="stat_qty_diserahkan">0</p>
                            </div>
                            <div class="bg-white/60 rounded-lg p-2.5 border border-white">
                                <p class="text-xs text-gray-500 mb-0.5">Sisa</p>
                                <p class="text-lg font-bold text-amber-600" id="stat_qty_sisa">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Qty Diterima --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Qty Diterima <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="qty_diterima" id="input_qty_diterima" min="1" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-[#0F034D] focus:ring-1 focus:ring-[#0F034D]/20 outline-none transition-all text-sm"
                            placeholder="Masukkan jumlah yang diterima">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">pcs</span>
                    </div>
                    <p class="text-xs text-red-500 mt-1.5 hidden" id="error_qty">Qty melebihi stok ready karyawan</p>
                </div>

                {{-- Tanggal Terima --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tanggal Terima <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_terima" id="input_tanggal_terima" required max="{{ date('Y-m-d') }}"
                        value="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-[#0F034D] focus:ring-1 focus:ring-[#0F034D]/20 outline-none transition-all text-sm">
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Catatan
                    </label>
                    <textarea name="catatan" id="input_catatan" rows="3" maxlength="500"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-[#0F034D] focus:ring-1 focus:ring-[#0F034D]/20 outline-none transition-all resize-none text-sm"
                        placeholder="Catatan tambahan (opsional)"></textarea>
                    <p class="text-xs text-gray-500 mt-1.5">Maksimal 500 karakter</p>
                </div>

                {{-- Bukti Foto --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Bukti Foto <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 hover:border-[#0F034D] transition-colors">
                        <input type="file" name="bukti_foto" id="input_bukti_foto" accept="image/jpeg,image/jpg,image/png" required
                            class="hidden">
                        <label for="input_bukti_foto" class="cursor-pointer flex flex-col items-center">
                            <div id="preview_container" class="hidden">
                                <img id="preview_image" class="max-h-32 rounded-lg mb-2 border border-gray-200">
                            </div>
                            <div id="upload_placeholder" class="flex flex-col items-center">
                                <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                </svg>
                                <p class="text-sm font-medium text-gray-700 mb-0.5">Klik untuk upload foto</p>
                                <p class="text-xs text-gray-400">JPG, JPEG, PNG (Max 2MB)</p>
                            </div>
                        </label>
                    </div>
                    <p class="text-xs text-red-500 mt-1.5">Bukti foto WAJIB untuk dokumentasi penerimaan</p>
                </div>
            </div>
        </form>

        {{-- Footer --}}
        <div class="slide-panel-footer">
            <button type="button" class="btn-panel-cancel" data-close-penerimaan-modal>Batal</button>
            <button type="submit" form="formInputPenerimaan" class="btn-panel-submit">Simpan Penerimaan</button>
        </div>
    </div>
</div>

{{-- Photo Preview Modal --}}
<div id="photoPreviewModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[60] hidden opacity-0 transition-opacity duration-200">
    <div class="flex items-center justify-center min-h-screen p-4">
        <button type="button" data-close-photo-preview class="absolute top-4 right-4 w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <img id="photo_preview_image" src="" alt="Bukti Photo" class="max-w-full max-h-[90vh] rounded-xl shadow-2xl">
    </div>
</div>

    {{-- MODAL EDIT BASELINE --}}
    {{-- ========================================= --}}
    <div id="edit-modal" class="slide-panel">
        <!-- Backdrop -->
        <div class="slide-panel-backdrop" data-panel-close="edit-modal"></div>

        <!-- Panel Body -->
        <div class="slide-panel-body">
            <!-- Header -->
            <div class="slide-panel-header">
                <div class="slide-panel-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <h3 class="slide-panel-header-title">Edit Baseline Produksi</h3>
                <button class="slide-panel-close" data-panel-close="edit-modal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <form action="" method="POST" id="editForm" class="slide-panel-content">
                @csrf
                @method('PUT')
                <div class="space-y-4">

                            <!-- Pilih Produk -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Produk <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="hidden" name="produk_id" id="edit_produk_id" required>
                                    <input type="text" id="edit_produk_search" placeholder="Ketik untuk mencari produk..." autocomplete="off" class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="dropdown-arrow w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                    <div id="edit_produk_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                        <div class="p-2">
                                            @foreach($produks as $produk)
                                                <div class="dropdown-option flex items-center justify-between gap-2 px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="{{ $produk->id }}" data-text="{{ $produk->nama_produk }} ({{ $produk->kode_produk }}) - {{ ucfirst($produk->ukuran) }}, {{ ucfirst($produk->warna) }}" data-warna="{{ $produk->warna }}">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="font-medium text-gray-900">{{ $produk->nama_produk }}</div>
                                                        <div class="text-xs text-gray-500">{{ $produk->kode_produk }} - {{ ucfirst($produk->ukuran) }}, {{ ucfirst($produk->warna) }}</div>
                                                    </div>
                                                    <svg class="check-icon w-4 h-4 text-[#0F034D] hidden flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div id="edit_produk_no_results" class="hidden p-4 text-center text-sm text-gray-500">Produk tidak ditemukan</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pilih Bahan Baku -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Bahan Baku <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="hidden" name="bahan_baku_id" id="edit_bahan_baku_id" required>
                                    <input type="text" id="edit_bahan_baku_search" placeholder="Ketik untuk mencari bahan baku..." autocomplete="off" class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="dropdown-arrow w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                    <div id="edit_bahan_baku_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                        <div class="p-2">
                                            @foreach($bahanBaku as $bahan)
                                                <div class="dropdown-option flex items-center justify-between gap-2 px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="{{ $bahan->id }}" data-text="{{ $bahan->nama_bahan }} ({{ $bahan->kode_bahan }}) - {{ ucfirst($bahan->warna) }}, {{ ucfirst($bahan->kategori) }}" data-warna="{{ $bahan->warna }}" data-kategori="{{ $bahan->kategori }}">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="font-medium text-gray-900">{{ $bahan->nama_bahan }}</div>
                                                        <div class="text-xs text-gray-500">{{ $bahan->kode_bahan }} - {{ ucfirst($bahan->warna) }}, {{ ucfirst($bahan->kategori) }}</div>
                                                    </div>
                                                    <svg class="check-icon w-4 h-4 text-[#0F034D] hidden flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div id="edit_bahan_baku_no_results" class="hidden p-4 text-center text-sm text-gray-500">Bahan baku tidak ditemukan</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Error Message Warna -->
                            <div id="edit_warna_error" class="hidden p-3 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm text-red-600"></p>
                            </div>
                            
                            <!-- Grid: Pcs per Roll & Toleransi -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Pcs per Roll <span class="text-red-500">*</span></label>
                                    <input type="number" name="pcs_per_roll" id="edit_pcs_per_roll" required min="1" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Toleransi Minus</label>
                                    <input type="number" name="toleransi_minus" id="edit_toleransi_minus" min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                                </div>
                            </div>

                            <!-- Keterangan -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                                <textarea name="keterangan" id="edit_keterangan" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm"></textarea>
                            </div>

                            <!-- Toggle Aktif -->
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
                                        <p class="text-xs text-gray-500">Baseline ini akan digunakan dalam perhitungan produksi.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                </form>

            <!-- Footer -->
            <div class="slide-panel-footer">
                <button type="button" onclick="closePanel('edit-modal')" class="btn-panel-cancel">Batal</button>
                <button type="submit" form="editForm" class="btn-panel-submit">Simpan Perubahan</button>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}

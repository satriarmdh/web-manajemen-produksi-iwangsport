    {{-- MODAL TAMBAH BASELINE --}}
    {{-- ========================================= --}}
    <div id="add-modal" class="slide-panel">
        <!-- Backdrop -->
        <div class="slide-panel-backdrop" data-panel-close="add-modal"></div>

        <!-- Panel Body -->
        <div class="slide-panel-body">
            <!-- Header -->
            <div class="slide-panel-header">
                <div class="slide-panel-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="slide-panel-header-title">Tambah Baseline Produksi</h3>
                <button class="slide-panel-close" data-panel-close="add-modal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <form action="{{ route('admin.standard-baseline-produksi.store') }}" method="POST" id="addForm" class="slide-panel-content">
                @csrf
                <div class="space-y-4">
                            <!-- Pilih Produk -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Produk <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="hidden" name="produk_id" id="add_produk_id" required>
                                    <input type="text" id="add_produk_search" placeholder="Ketik untuk mencari produk..." autocomplete="off" class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="dropdown-arrow w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                    <div id="add_produk_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                        <div class="p-2">
                                            @foreach($produks as $produk)
                                                <div class="dropdown-option hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm px-3 py-2" style="display:flex; align-items:center; justify-content:space-between;" data-value="{{ $produk->id }}" data-text="{{ $produk->nama_produk }} ({{ $produk->kode_produk }}) â€” {{ ucfirst($produk->ukuran) }}, {{ ucfirst($produk->warna) }}" data-warna="{{ $produk->warna }}">
                                                    <div style="flex:1; min-width:0; overflow:hidden;">
                                                        <div class="font-medium text-gray-900 truncate">{{ $produk->nama_produk }}</div>
                                                        <div class="text-xs text-gray-500 truncate">{{ $produk->kode_produk }} â€¢ {{ ucfirst($produk->ukuran) }}, {{ ucfirst($produk->warna) }}</div>
                                                    </div>
                                                    <svg class="check-icon hidden" style="width:16px; height:16px; color:#0F034D; flex-shrink:0; margin-left:8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div id="add_produk_no_results" class="hidden p-4 text-center text-sm text-gray-500">Produk tidak ditemukan</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pilih Bahan Baku -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Bahan Baku <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="hidden" name="bahan_baku_id" id="add_bahan_baku_id" required>
                                    <input type="text" id="add_bahan_baku_search" placeholder="Ketik untuk mencari bahan baku..." autocomplete="off" class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="dropdown-arrow w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                    <div id="add_bahan_baku_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                        <div class="p-2">
                                            @foreach($bahanBaku as $bahan)
                                                <div class="dropdown-option hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm px-3 py-2" style="display:flex; align-items:center; justify-content:space-between;" data-value="{{ $bahan->id }}" data-text="{{ $bahan->nama_bahan }} ({{ $bahan->kode_bahan }}) â€” {{ ucfirst($bahan->warna) }}, {{ ucfirst($bahan->kategori) }}" data-warna="{{ $bahan->warna }}" data-kategori="{{ $bahan->kategori }}">
                                                    <div style="flex:1; min-width:0; overflow:hidden;">
                                                        <div class="font-medium text-gray-900 truncate">{{ $bahan->nama_bahan }}</div>
                                                        <div class="text-xs text-gray-500 truncate">{{ $bahan->kode_bahan }} â€¢ {{ ucfirst($bahan->warna) }}, {{ ucfirst($bahan->kategori) }}</div>
                                                    </div>
                                                    <svg class="check-icon hidden" style="width:16px; height:16px; color:#0F034D; flex-shrink:0; margin-left:8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div id="add_bahan_baku_no_results" class="hidden p-4 text-center text-sm text-gray-500">Bahan baku tidak ditemukan</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Error Message Warna -->
                            <div id="add_warna_error" class="hidden p-3 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm text-red-600"></p>
                            </div>
                            
                            <!-- Grid: Pcs per Roll & Toleransi -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Pcs per Roll <span class="text-red-500">*</span></label>
                                    <input type="number" name="pcs_per_roll" required min="1" placeholder="Contoh: 138" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                                    <p class="text-xs text-gray-500 mt-1">Estimasi pcs yang dihasilkan 1 roll.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Toleransi Minus</label>
                                    <input type="number" name="toleransi_minus" min="0" value="0" placeholder="Contoh: 5" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                                    <p class="text-xs text-gray-500 mt-1">Batas bawah yang masih wajar.</p>
                                </div>
                            </div>

                            <!-- Keterangan -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                                <textarea name="keterangan" rows="2" placeholder="Catatan tambahan (opsional)" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm"></textarea>
                            </div>

                        </div>

                </form>

            <!-- Footer -->
            <div class="slide-panel-footer">
                <button type="button" onclick="closePanel('add-modal')" class="btn-panel-cancel">Batal</button>
                <button type="submit" form="addForm" class="btn-panel-submit">Simpan Baseline</button>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}

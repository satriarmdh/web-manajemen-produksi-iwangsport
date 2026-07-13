    {{-- MODAL DETAIL BASELINE --}}
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="slide-panel-header-title">Detail Baseline Produksi</h3>
                <button class="slide-panel-close" data-panel-close="detail-modal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="slide-panel-content">
                <div class="space-y-5">
                        {{-- Produk --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Produk</label>
                            <p id="detail_produk" class="text-sm font-bold text-gray-900">-</p>
                            <div class="inline-flex items-center gap-1.5 text-xs text-gray-400 mt-0.5">
                                <span id="detail_produk_sub">-</span>
                                <span id="detail_produk_dot" class="inline-block w-2.5 h-2.5 rounded-full shrink-0"></span>
                            </div>
                        </div>

                        {{-- Bahan Baku --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Bahan Baku</label>
                            <p id="detail_bahan" class="text-sm font-bold text-gray-900">-</p>
                            <div class="inline-flex items-center gap-1.5 text-xs text-gray-400 mt-0.5">
                                <span id="detail_bahan_sub">-</span>
                                <span id="detail_bahan_dot" class="inline-block w-2.5 h-2.5 rounded-full shrink-0"></span>
                            </div>
                        </div>

                        {{-- Estimasi Grid --}}
                        <div class="grid grid-cols-3 gap-3">
                            <div class="bg-[#0F034D]/5 rounded-xl p-3 text-center">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Pcs per Roll</p>
                                <p id="detail_pcs" class="text-xl font-bold text-[#0F034D]">-</p>
                            </div>
                            <div class="bg-amber-50 rounded-xl p-3 text-center">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Toleransi (-)</p>
                                <p id="detail_toleransi" class="text-xl font-bold text-amber-600">-</p>
                            </div>
                            <div class="bg-blue-50 rounded-xl p-3 text-center">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Range</p>
                                <p id="detail_range" class="text-lg font-bold text-blue-700">-</p>
                            </div>
                        </div>

                        {{-- Keterangan --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Keterangan</label>
                            <p id="detail_keterangan" class="text-sm text-gray-700">-</p>
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Status</label>
                            <div id="detail_status"></div>
                        </div>

                        {{-- Tanggal Dibuat --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Dibuat</label>
                            <p id="detail_created" class="text-sm text-gray-700">-</p>
                        </div>
                </div>
            </div>

            <!-- Footer -->
            <!-- <div class="slide-panel-footer">
                <button type="button" onclick="closePanel('detail-modal')" class="btn-panel-cancel">Tutup</button>
            </div> -->
        </div>
    </div>

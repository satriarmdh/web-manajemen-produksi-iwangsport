    {{-- MODAL DETAIL PELANGGAN --}}
    {{-- ========================================= --}}
    <div id="detail-modal" class="slide-panel">
        <div class="slide-panel-backdrop" data-panel-close></div>
        <div class="slide-panel-body">
            <div class="slide-panel-header">
                <div class="slide-panel-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <h2 class="slide-panel-header-title">Detail Pelanggan</h2>
                <button class="slide-panel-close" data-panel-close><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="slide-panel-content">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Kode Pelanggan</label>
                        <p id="detail_kode" class="text-sm text-gray-900 font-bold">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Nama Pelanggan</label>
                        <p id="detail_nama" class="text-sm text-gray-900 font-bold">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">No. Telepon</label>
                        <p id="detail_no_telp" class="text-sm text-gray-900 font-bold">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Email</label>
                        <p id="detail_email" class="text-sm text-gray-900 font-bold">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Alamat</label>
                        <p id="detail_alamat" class="text-sm text-gray-900">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Keterangan</label>
                        <p id="detail_keterangan" class="text-sm text-gray-900">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Status</label>
                        <div id="detail_status"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Dibuat</label>
                        <p id="detail_created" class="text-sm text-gray-900">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH PELANGGAN --}}
    {{-- ========================================= --}}
    <div id="add-modal" class="slide-panel">
        <div class="slide-panel-backdrop" data-panel-close></div>
        <div class="slide-panel-body">
            <div class="slide-panel-header">
                <div class="slide-panel-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <h2 class="slide-panel-header-title">Tambah Pelanggan</h2>
                <button class="slide-panel-close" data-panel-close><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form action="{{ route('admin.pelanggan.store') }}" method="POST" id="addForm" class="slide-panel-content">
                @csrf
                <div class="space-y-4">
                    <!-- Kode Pelanggan (Auto Generate Preview) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Pelanggan</label>
                        <input type="text" id="add_kode_pelanggan" readonly data-next-number="{{ $nextNumber }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm bg-gray-50 text-gray-500 italic cursor-not-allowed">
                        <p class="text-xs text-gray-400 mt-1">Otomatis digenerate oleh sistem.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Pelanggan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_pelanggan" required placeholder="Contoh: PT Mitra Jaya Abadi" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">No. Telepon <span class="text-red-500">*</span></label>
                            <input type="text" name="no_telp" required placeholder="08xxxxxxxxxx" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required placeholder="pelanggan@email.com" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat <span class="text-red-500">*</span></label>
                        <textarea name="alamat" required rows="3" placeholder="Alamat lengkap pelanggan" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="2" placeholder="Keterangan tambahan (opsional)" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm"></textarea>
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

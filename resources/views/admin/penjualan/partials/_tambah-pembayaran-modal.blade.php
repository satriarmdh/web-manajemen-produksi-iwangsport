{{-- Slide-Over Right Panel Tambah Pembayaran --}}
<div id="modal-tambah-pembayaran" class="slide-panel">
    <div class="slide-panel-backdrop" onclick="closeTambahPembayaranModal()"></div>
    <div class="slide-panel-body">
        <div class="slide-panel-header">
            <div class="slide-panel-header-icon">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="slide-panel-header-title text-base font-bold text-[#0F034D]">Tambah Pembayaran</h2>
                <p class="text-xs text-gray-500">Catat cicilan pelunasan & upload resi transfer</p>
            </div>
            <button type="button" class="slide-panel-close" onclick="closeTambahPembayaranModal()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('admin.penjualan.pembayaran.store', $penjualan) }}" method="POST" id="form-tambah-pembayaran" enctype="multipart/form-data" class="slide-panel-content space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tanggal Pembayaran <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="tanggal_bayar" value="{{ now()->format('Y-m-d\TH:i') }}" max="{{ now()->format('Y-m-d\TH:i') }}" required class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nominal Pembayaran (Rp) <span class="text-red-500">*</span></label>
                <input type="hidden" name="jumlah_bayar" id="modal_jumlah_bayar_value" value="">
                <input type="text" id="modal_jumlah_bayar_display" required placeholder="0" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm font-bold text-gray-900 focus:ring-1 focus:ring-[#0F034D]">
                @if($penjualan->sisa_pembayaran > 0)
                    <p class="text-[11px] text-amber-600 font-medium mt-1">Sisa tagihan saat ini: Rp {{ number_format($penjualan->sisa_pembayaran, 0, ',', '.') }}</p>
                @endif
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Metode Pembayaran <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="hidden" name="metode_pembayaran" id="modal_metode_value" value="tunai" required>
                    <input type="text" id="modal_metode_input" placeholder="Pilih metode..." readonly autocomplete="off" class="w-full px-3 py-2.5 pr-10 border border-gray-300 rounded-xl focus:ring-1 focus:ring-[#0F034D]/20 focus:border-[#0F034D] transition-colors text-sm text-gray-800 bg-white cursor-pointer">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="dropdown-arrow w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                    <div id="modal_metode_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-52 overflow-y-auto hidden">
                        <div class="p-2 space-y-1">
                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="tunai" data-text="Tunai / Cash">
                                <span class="text-sm font-medium text-gray-700">Tunai / Cash</span>
                                <svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div class="dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm" data-value="transfer" data-text="Transfer">
                                <span class="text-sm font-medium text-gray-700">Transfer</span>
                                <svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Upload Resi Bukti Transfer <span class="text-gray-400 font-normal">(Opsional)</span></label>
                <input type="file" name="bukti_pembayaran" id="modal_bukti_pembayaran" accept="image/jpeg,image/png,image/webp" class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#0F034D]">
                <div id="modal_preview_bukti_container" class="hidden mt-2">
                    <p class="text-[11px] font-semibold text-gray-500 mb-1">Preview Resi Upload:</p>
                    <img id="modal_preview_bukti_img" src="" class="h-28 w-auto rounded-lg border border-gray-200 object-cover shadow-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Catatan / Keterangan <span class="text-gray-400 font-normal">(Opsional)</span></label>
                <input type="text" name="catatan" placeholder="Misal: Pelunasan sisa tagihan" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-1 focus:ring-[#0F034D]">
            </div>
        </form>

        <div class="slide-panel-footer">
            <button type="button" class="btn-panel-cancel" onclick="closeTambahPembayaranModal()">Batal</button>
            <button type="submit" form="form-tambah-pembayaran" class="btn-panel-submit">Simpan Pembayaran</button>
        </div>
    </div>
</div>

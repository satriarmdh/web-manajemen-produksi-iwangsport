function openTambahPembayaranModal() {
    const modal = document.getElementById('modal-tambah-pembayaran');
    if (modal) modal.classList.add('is-open');
}

function closeTambahPembayaranModal() {
    const modal = document.getElementById('modal-tambah-pembayaran');
    if (modal) modal.classList.remove('is-open');
}

function previewImage(src, title) {
    const target = document.getElementById('preview-img-target');
    const titleEl = document.getElementById('preview-title');
    const modal = document.getElementById('modal-preview-image');
    if (target && modal) {
        target.src = src;
        if (titleEl) titleEl.textContent = title || 'Bukti Transfer';
        modal.classList.remove('hidden');
    }
}

// Expose to window scope for inline onclick triggers
window.openTambahPembayaranModal = openTambahPembayaranModal;
window.closeTambahPembayaranModal = closeTambahPembayaranModal;
window.previewImage = previewImage;

document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.initCustomDropdown === 'function') {
        window.initCustomDropdown('modal_metode');
        window.setCustomDropdownValue('modal_metode', 'tunai');
    }
    if (typeof window.initCurrencyInput === 'function') {
        window.initCurrencyInput('modal_jumlah_bayar_display', 'modal_jumlah_bayar_value');
    }

    const modalBuktiInput = document.getElementById('modal_bukti_pembayaran');
    if (modalBuktiInput) {
        modalBuktiInput.addEventListener('change', function() {
            const file = this.files[0];
            const container = document.getElementById('modal_preview_bukti_container');
            const img = document.getElementById('modal_preview_bukti_img');
            if (file && container && img) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    container.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else if (container) {
                container.classList.add('hidden');
            }
        });
    }
});

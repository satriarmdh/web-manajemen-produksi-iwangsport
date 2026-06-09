/**
 * Generate Kode Produk - Preview Otomatis
 * Menampilkan preview kode produk (CLN-001, CLN-002, dst) pada modal tambah.
 */
document.addEventListener('DOMContentLoaded', () => {
    const kodePreview = document.getElementById('add_kode_produk');

    if (kodePreview && kodePreview.dataset.nextNumber) {
        // Set preview kode saat modal dibuka
        kodePreview.value = kodePreview.dataset.nextNumber;
    }
});

/**
 * Generate Kode Supplier - Preview Otomatis
 * Menampilkan preview kode supplier (SUP-001, SUP-002, dst) pada modal tambah.
 */
document.addEventListener('DOMContentLoaded', () => {
    const kodePreview = document.getElementById('add_kode_supplier');

    if (kodePreview && kodePreview.dataset.nextNumber) {
        // Set preview kode saat halaman dimuat
        kodePreview.value = kodePreview.dataset.nextNumber;
        kodePreview.classList.remove('text-gray-500', 'italic');
        kodePreview.classList.add('text-[#0F034D]', 'font-bold', 'bg-blue-50/50', 'border-blue-200');
    }
});

/**
 * Toggle Modal - Manajemen Produk
 * Menggunakan Slide Panel API dari global-modal.js
 */

/**
 * Buka modal edit dan isi data dari atribut data-* pada tombol yang diklik.
 */
function openEditModal(button) {
    const id = button.dataset.id;
    const kode = button.dataset.kode;
    const nama = button.dataset.nama;
    const ukuran = button.dataset.ukuran;
    const warna = button.dataset.warna;
    const harga = button.dataset.harga;
    const satuan = button.dataset.satuan;
    const stok = button.dataset.stok;
    const stokMinimal = button.dataset.stokMinimal || '0';
    const isAktif = button.dataset.isAktif;

    // Set action URL form edit
    const editForm = document.getElementById('editForm');
    editForm.action = `/admin/produk/${id}`;

    // Isi field
    document.getElementById('edit_kode').value = kode;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_harga').value = harga;
    document.getElementById('edit_satuan').value = satuan;
    document.getElementById('edit_stok').value = stok;
    document.getElementById('edit_stok_minimal').value = stokMinimal;

    // Set custom dropdown values
    if (typeof setCustomDropdownValue === 'function') {
        setCustomDropdownValue('edit_ukuran', ukuran);
        setCustomDropdownValue('edit_warna', warna);
    }

    // Set checkbox is_aktif
    const checkboxAktif = document.getElementById('edit_is_aktif');
    if (checkboxAktif) {
        checkboxAktif.checked = isAktif === '1';
        if (typeof updateCheckbox === 'function') {
            updateCheckbox(checkboxAktif, 'edit_cb');
        }
    }

    window.openPanel('edit-modal');
}

/**
 * Toggle dropdown filter/sorting (tutup dropdown lain saat buka yang baru)
 */
function toggleFilterMenu(dropdownId) {
    const allDropdowns = document.querySelectorAll('[id$="Dropdown"]');
    allDropdowns.forEach(dd => {
        if (dd.id !== dropdownId) {
            dd.classList.add('hidden', 'opacity-0', 'scale-95', 'pointer-events-none');
            dd.classList.remove('opacity-100', 'scale-100');
        }
    });

    const dropdown = document.getElementById(dropdownId);
    const isHidden = dropdown.classList.contains('hidden');

    if (isHidden) {
        dropdown.classList.remove('hidden');
        requestAnimationFrame(() => {
            dropdown.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
            dropdown.classList.add('opacity-100', 'scale-100');
        });
    } else {
        dropdown.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
        dropdown.classList.remove('opacity-100', 'scale-100');
        setTimeout(() => dropdown.classList.add('hidden'), 200);
    }
}

// Tutup dropdown saat klik di luar
document.addEventListener('click', (e) => {
    if (!e.target.closest('[id$="Dropdown"]') && !e.target.closest('[onclick*="toggleFilterMenu"]')) {
        document.querySelectorAll('[id$="Dropdown"]').forEach(dd => {
            dd.classList.add('hidden', 'opacity-0', 'scale-95', 'pointer-events-none');
        });
    }
});

// Expose ke global scope
window.openEditModal = openEditModal;
window.toggleFilterMenu = toggleFilterMenu;

// Initialize custom dropdowns on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    if (typeof initCustomDropdown === 'function') {
        // Modal Tambah
        initCustomDropdown('add_ukuran');
        initCustomDropdown('add_warna');
        // Modal Edit
        initCustomDropdown('edit_ukuran');
        initCustomDropdown('edit_warna');
    }
});

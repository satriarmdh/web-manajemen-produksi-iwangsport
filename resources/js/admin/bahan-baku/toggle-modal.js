/**
 * Toggle Modal - Manajemen Bahan Baku
 * Mengatur buka/tutup modal dengan animasi smooth.
 */

function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    const content = modal.querySelector('.relative.w-full');
    const isHidden = modal.classList.contains('hidden');

    if (isHidden) {
        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
            modal.classList.add('opacity-100');
            if (content) {
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }
        });
    } else {
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        if (content) {
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
        }
        setTimeout(() => {
            modal.classList.add('hidden');
            const form = modal.querySelector('form');
            if (form) form.reset();
            // Reset custom dropdowns
            if (typeof resetCustomDropdown === 'function') {
                if (modalId === 'add-modal') {
                    resetCustomDropdown('add_warna');
                    resetCustomDropdown('add_kategori');
                    resetCustomDropdown('add_satuan');
                } else if (modalId === 'edit-modal') {
                    resetCustomDropdown('edit_warna');
                    resetCustomDropdown('edit_kategori');
                    resetCustomDropdown('edit_satuan');
                }
            }
        }, 300);
    }
}

/**
 * Buka modal edit dan isi data dari atribut data-* pada tombol yang diklik.
 */
function openEditModal(button) {
    const id = button.dataset.id;
    const kode = button.dataset.kode;
    const nama = button.dataset.nama;
    const warna = button.dataset.warna;
    const kategori = button.dataset.kategori;
    const satuan = button.dataset.satuan;
    const stok = button.dataset.stok;

    const form = document.getElementById('editForm');
    form.action = `/admin/bahan-baku/${id}`;

    document.getElementById('edit_kode').value = kode;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_stok').value = stok;

    // Set custom dropdown values
    if (typeof setCustomDropdownValue === 'function') {
        setCustomDropdownValue('edit_warna', warna);
        setCustomDropdownValue('edit_kategori', kategori);
        setCustomDropdownValue('edit_satuan', satuan);
    }

    toggleModal('edit-modal');
}

/**
 * Toggle dropdown filter/sorting
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
window.toggleModal = toggleModal;
window.openEditModal = openEditModal;
window.toggleFilterMenu = toggleFilterMenu;

// Initialize custom dropdowns on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    if (typeof initCustomDropdown === 'function') {
        // Modal Tambah
        initCustomDropdown('add_warna');
        initCustomDropdown('add_kategori');
        initCustomDropdown('add_satuan');
        // Modal Edit
        initCustomDropdown('edit_warna');
        initCustomDropdown('edit_kategori');
        initCustomDropdown('edit_satuan');
    }
});

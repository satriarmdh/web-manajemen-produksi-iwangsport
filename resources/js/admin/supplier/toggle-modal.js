/**
 * Toggle Modal - Manajemen Supplier
 * Menggunakan Slide Panel API dari global-modal.js
 */

/**
 * Buka modal detail dan tampilkan data lengkap supplier
 */
function openDetailModal(button) {
    const kode     = button.dataset.kode;
    const nama     = button.dataset.nama;
    const kategori = button.dataset.kategori;
    const kontak   = button.dataset.kontak;
    const email    = button.dataset.email;
    const alamat   = button.dataset.alamat;
    const catatan  = button.dataset.catatan;
    const status   = button.dataset.status;
    const created  = button.dataset.created;

    // Isi field detail
    document.getElementById('detail_kode').textContent    = kode;
    document.getElementById('detail_nama').textContent    = nama;
    document.getElementById('detail_kontak').textContent  = kontak;
    document.getElementById('detail_email').textContent   = email;
    document.getElementById('detail_alamat').textContent  = alamat;
    document.getElementById('detail_catatan').textContent = catatan || '-';
    document.getElementById('detail_created').textContent = created;

    // Parse dan render kategori (multi-select)
    let kategoriArray = [];
    try {
        kategoriArray = JSON.parse(kategori);
    } catch (e) {
        kategoriArray = [];
    }
    
    const kategoriContainer = document.getElementById('detail_kategori');
    kategoriContainer.innerHTML = '';
    if (kategoriArray.length > 0) {
        kategoriArray.forEach(kat => {
            const badge = document.createElement('span');
            badge.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border bg-gray-100 text-gray-700 border-gray-200';
            badge.textContent = kat.charAt(0).toUpperCase() + kat.slice(1);
            kategoriContainer.appendChild(badge);
        });
    } else {
        kategoriContainer.innerHTML = '<span class="text-sm text-gray-400">-</span>';
    }

    // Render status badge
    const statusContainer = document.getElementById('detail_status');
    if (status === 'aktif') {
        statusContainer.innerHTML = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600 border border-green-100"><div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>Aktif</span>';
    } else {
        statusContainer.innerHTML = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600 border border-red-100"><div class="w-1.5 h-1.5 rounded-full bg-red-500"></div>Nonaktif</span>';
    }

    window.openPanel('detail-modal');
}

/**
 * Toggle dropdown aksi (Edit & Hapus) pada setiap baris supplier
 * Menggunakan position: fixed agar dropdown tidak terpotong oleh overflow parent.
 */
function toggleActionDropdown(button) {
    const dropdown = button.nextElementSibling;
    const isHidden = dropdown.classList.contains('hidden');

    // Tutup semua dropdown aksi lain yang sedang terbuka
    document.querySelectorAll('.action-dropdown').forEach(dd => {
        if (dd !== dropdown) {
            dd.classList.add('hidden', 'opacity-0', 'scale-95', 'pointer-events-none');
            dd.style.position = '';
            dd.style.top = '';
            dd.style.right = '';
        }
    });

    // Toggle dropdown yang diklik
    if (isHidden) {
        // Hitung posisi berdasarkan viewport agar tidak terpengaruh overflow parent
        const rect = button.getBoundingClientRect();
        dropdown.style.position = 'fixed';
        dropdown.style.top = `${rect.bottom + 4}px`;
        dropdown.style.right = `${window.innerWidth - rect.right}px`;

        dropdown.classList.remove('hidden');
        setTimeout(() => {
            dropdown.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
        }, 10);
    } else {
        dropdown.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
        setTimeout(() => {
            dropdown.classList.add('hidden');
            dropdown.style.position = '';
            dropdown.style.top = '';
            dropdown.style.right = '';
        }, 300);
    }
}

/**
 * Buka modal edit dan isi data dari atribut data-* pada tombol yang diklik.
 * Termasuk multi-kategori checkbox.
 */
function openEditModal(button) {
    const id       = button.dataset.id;
    const kode     = button.dataset.kode;
    const nama     = button.dataset.nama;
    const kategori = button.dataset.kategori; // JSON string, misal '["kain","benang"]'
    const kontak   = button.dataset.kontak;
    const email    = button.dataset.email;
    const alamat   = button.dataset.alamat;
    const catatan  = button.dataset.catatan;
    const isAktif  = button.dataset.isAktif;

    // Set action URL form edit
    const editForm = document.getElementById('editForm');
    editForm.action = `/admin/supplier/${id}`;

    // Isi field text
    document.getElementById('edit_kode').value    = kode;
    document.getElementById('edit_nama').value    = nama;
    document.getElementById('edit_kontak').value  = kontak;
    document.getElementById('edit_email').value   = email;
    document.getElementById('edit_alamat').value  = alamat;
    document.getElementById('edit_catatan').value = catatan;

    // Set checkbox is_aktif
    const checkboxAktif = document.getElementById('edit_is_aktif');
    if (checkboxAktif) {
        checkboxAktif.checked = isAktif === '1';
        if (typeof updateCheckbox === 'function') {
            updateCheckbox(checkboxAktif, 'edit_cb');
        }
    }

    // Isi checkbox kategori (multi-select) dengan custom styling
    let kategoriArray = [];
    try {
        kategoriArray = JSON.parse(kategori);
    } catch (e) {
        kategoriArray = [];
    }

    // Uncheck semua checkbox edit_kategori terlebih dahulu
    document.querySelectorAll('.edit_kategori').forEach(cb => {
        cb.checked = false;
        if (typeof updateKategoriCheckbox === 'function') {
            updateKategoriCheckbox(cb, cb.id);
        }
    });

    // Check yang sesuai dengan data kategori
    document.querySelectorAll('.edit_kategori').forEach(cb => {
        if (kategoriArray.includes(cb.value)) {
            cb.checked = true;
            if (typeof updateKategoriCheckbox === 'function') {
                updateKategoriCheckbox(cb, cb.id);
            }
        }
    });

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
    // Tutup filter/sort dropdown
    if (!e.target.closest('[id$="Dropdown"]') && !e.target.closest('[onclick*="toggleFilterMenu"]')) {
        document.querySelectorAll('[id$="Dropdown"]').forEach(dd => {
            dd.classList.add('hidden', 'opacity-0', 'scale-95', 'pointer-events-none');
        });
    }
    
    // Tutup action dropdown
    if (!e.target.closest('.action-dropdown') && !e.target.closest('[onclick*="toggleActionDropdown"]')) {
        document.querySelectorAll('.action-dropdown').forEach(dd => {
            dd.classList.add('hidden', 'opacity-0', 'scale-95', 'pointer-events-none');
            dd.style.position = '';
            dd.style.top = '';
            dd.style.right = '';
        });
    }
});

// Tutup & reset action dropdown saat scroll atau resize (karena posisi fixed bisa bergeser)
window.addEventListener('scroll', () => {
    document.querySelectorAll('.action-dropdown').forEach(dd => {
        if (!dd.classList.contains('hidden')) {
            dd.classList.add('hidden', 'opacity-0', 'scale-95', 'pointer-events-none');
            dd.style.position = '';
            dd.style.top = '';
            dd.style.right = '';
        }
    });
}, true);

window.addEventListener('resize', () => {
    document.querySelectorAll('.action-dropdown').forEach(dd => {
        if (!dd.classList.contains('hidden')) {
            dd.classList.add('hidden', 'opacity-0', 'scale-95', 'pointer-events-none');
            dd.style.position = '';
            dd.style.top = '';
            dd.style.right = '';
        }
    });
});

// Expose ke global scope
window.openDetailModal = openDetailModal;
window.openEditModal = openEditModal;
window.toggleFilterMenu = toggleFilterMenu;
window.toggleActionDropdown = toggleActionDropdown;

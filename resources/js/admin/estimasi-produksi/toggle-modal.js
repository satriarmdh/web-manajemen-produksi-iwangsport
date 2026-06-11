/**
 * Update checkbox styling saat status berubah
 */
function updateCheckbox(checkbox, prefix) {
    const wrapper = document.getElementById(`${prefix}_wrapper`);
    const box = document.getElementById(`${prefix}_box`);
    const icon = document.getElementById(`${prefix}_icon`);
    const text = document.getElementById(`${prefix}_text`);

    if (checkbox.checked) {
        wrapper.className = 'flex items-center gap-3 p-4 border border-[#0F034D] bg-[#0F034D]/5 ring-1 ring-[#0F034D] rounded-xl cursor-pointer hover:bg-gray-50 transition-all';
        box.className = 'relative flex shrink-0 items-center justify-center w-5 h-5 rounded border-2 border-[#0F034D] transition-all';
        icon.style.display = 'block';
        text.className = 'text-sm font-semibold text-[#0F034D]';
    } else {
        wrapper.className = 'flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-all';
        box.className = 'relative flex shrink-0 items-center justify-center w-5 h-5 rounded border-2 border-gray-300 transition-all';
        icon.style.display = 'none';
        text.className = 'text-sm font-semibold text-gray-700';
    }
}

window.updateCheckbox = updateCheckbox;

/**
 * Toggle Modal - Standard Baseline Produksi
 * Mengatur buka/tutup modal dengan animasi smooth.
 */

function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    const content = modal.querySelector('.relative.w-full');
    const isHidden = modal.classList.contains('hidden');

    if (isHidden) {
        // BUKA
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
        // TUTUP
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        if (content) {
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
        }
        setTimeout(() => {
            modal.classList.add('hidden');
            // Reset form setelah modal ditutup
            const form = modal.querySelector('form');
            if (form) form.reset();
            // Reset searchable dropdowns
            resetSearchableDropdowns(modalId);
        }, 300);
    }
}

/**
 * Reset searchable dropdowns setelah modal ditutup
 */
function resetSearchableDropdowns(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    // Reset semua search input
    const searchInputs = modal.querySelectorAll('input[type="text"][id$="_search"]');
    searchInputs.forEach(input => {
        input.value = '';
    });

    // Reset semua hidden input
    const hiddenInputs = modal.querySelectorAll('input[type="hidden"][id$="_id"]');
    hiddenInputs.forEach(input => {
        input.value = '';
    });

    // Hide semua dropdown
    const dropdowns = modal.querySelectorAll('[id$="_dropdown"]');
    dropdowns.forEach(dropdown => {
        dropdown.classList.add('hidden');
    });
}

/**
 * Initialize searchable dropdown
 */
function initSearchableDropdown(prefix) {
    const searchInput = document.getElementById(`${prefix}_search`);
    const hiddenInput = document.getElementById(`${prefix}_id`);
    const dropdown = document.getElementById(`${prefix}_dropdown`);
    const noResults = document.getElementById(`${prefix}_no_results`);
    const options = dropdown.querySelectorAll('.dropdown-option');

    if (!searchInput || !hiddenInput || !dropdown) return;

    // Show dropdown on focus
    searchInput.addEventListener('focus', () => {
        dropdown.classList.remove('hidden');
        filterOptions();
    });

    // Filter options on input
    searchInput.addEventListener('input', filterOptions);

    function filterOptions() {
        const searchTerm = searchInput.value.toLowerCase();
        let visibleCount = 0;

        options.forEach(option => {
            const text = option.getAttribute('data-text').toLowerCase();
            if (text.includes(searchTerm)) {
                option.style.display = 'block';
                visibleCount++;
            } else {
                option.style.display = 'none';
            }
        });

        // Show/hide no results message
        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    }

    // Select option
    options.forEach(option => {
        option.addEventListener('click', () => {
            const value = option.getAttribute('data-value');
            const text = option.getAttribute('data-text');
            
            hiddenInput.value = value;
            searchInput.value = text;
            dropdown.classList.add('hidden');

            // Update checkmark - hide all, show selected
            options.forEach(opt => {
                const checkIcon = opt.querySelector('.check-icon');
                if (checkIcon) {
                    checkIcon.classList.add('hidden');
                }
            });
            const selectedCheckIcon = option.querySelector('.check-icon');
            if (selectedCheckIcon) {
                selectedCheckIcon.classList.remove('hidden');
            }
        });
    });

    // Hide dropdown on click outside
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
}

/**
 * Buka modal detail dan tampilkan data lengkap baseline
 */
function openDetailModal(button) {
    const produkNama = button.dataset.produkNama;
    const produkKode = button.dataset.produkKode;
    const produkUkuran = button.dataset.produkUkuran;
    const produkWarna = button.dataset.produkWarna;
    const bahanNama = button.dataset.bahanNama;
    const bahanKode = button.dataset.bahanKode;
    const bahanWarna = button.dataset.bahanWarna;
    const bahanKategori = button.dataset.bahanKategori;
    const pcs = button.dataset.pcs;
    const toleransi = button.dataset.toleransi;
    const rangeBawah = button.dataset.rangeBawah;
    const keterangan = button.dataset.keterangan;
    const status = button.dataset.status;
    const created = button.dataset.created;

    // Isi field detail
    document.getElementById('detail_produk').textContent = produkNama;
    document.getElementById('detail_produk_sub').textContent = `${produkKode} · ${produkUkuran.charAt(0).toUpperCase() + produkUkuran.slice(1)} · ${produkWarna}`;
    
    document.getElementById('detail_bahan').textContent = bahanNama;
    document.getElementById('detail_bahan_sub').textContent = `${bahanKode} · ${bahanWarna} · ${bahanKategori.charAt(0).toUpperCase() + bahanKategori.slice(1)}`;
    
    document.getElementById('detail_pcs').textContent = pcs;
    document.getElementById('detail_toleransi').textContent = toleransi > 0 ? `−${toleransi}` : '0';
    document.getElementById('detail_range').textContent = `${rangeBawah} - ${pcs}`;
    
    document.getElementById('detail_keterangan').textContent = keterangan || '—';
    
    // Render status badge
    const statusContainer = document.getElementById('detail_status');
    if (status === 'aktif') {
        statusContainer.innerHTML = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600 border border-green-100"><div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>Aktif</span>';
    } else {
        statusContainer.innerHTML = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600 border border-red-100"><div class="w-1.5 h-1.5 rounded-full bg-red-500"></div>Nonaktif</span>';
    }

    document.getElementById('detail_created').textContent = created;

    toggleModal('detail-modal');
}

/**
 * Toggle dropdown aksi (Edit & Hapus) pada setiap baris baseline
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
 */
function openEditModal(button) {
    const id = button.dataset.id;
    const produkId = button.dataset.produkId;
    const bahanId = button.dataset.bahanId;
    const pcs = button.dataset.pcs;
    const toleransi = button.dataset.toleransi;
    const keterangan = button.dataset.keterangan;
    const status = button.dataset.status;

    // Set action URL form edit
    const editForm = document.getElementById('editForm');
    editForm.action = `/admin/estimasi-produksi/${id}`;

    // Isi field
    document.getElementById('edit_produk_id').value = produkId;
    document.getElementById('edit_bahan_baku_id').value = bahanId;
    
    // Set search input text
    const produkOption = document.querySelector(`#edit_produk_dropdown .dropdown-option[data-value="${produkId}"]`);
    const bahanOption = document.querySelector(`#edit_bahan_baku_dropdown .dropdown-option[data-value="${bahanId}"]`);
    
    if (produkOption) {
        document.getElementById('edit_produk_search').value = produkOption.getAttribute('data-text');
    }
    if (bahanOption) {
        document.getElementById('edit_bahan_baku_search').value = bahanOption.getAttribute('data-text');
    }
    
    document.getElementById('edit_pcs_per_roll').value = pcs;
    document.getElementById('edit_toleransi_minus').value = toleransi;
    document.getElementById('edit_keterangan').value = keterangan;
    
    // Set checkbox aktif
    const checkboxAktif = document.getElementById('edit_is_aktif');
    checkboxAktif.checked = status === '1';

    toggleModal('edit-modal');
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

// Initialize searchable dropdowns on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    // Modal Tambah
    initSearchableDropdown('add_produk');
    initSearchableDropdown('add_bahan_baku');
    
    // Modal Edit
    initSearchableDropdown('edit_produk');
    initSearchableDropdown('edit_bahan_baku');
});

// Expose ke global scope
window.toggleModal = toggleModal;
window.openDetailModal = openDetailModal;
window.openEditModal = openEditModal;
window.toggleFilterMenu = toggleFilterMenu;
window.toggleActionDropdown = toggleActionDropdown;
window.initSearchableDropdown = initSearchableDropdown;

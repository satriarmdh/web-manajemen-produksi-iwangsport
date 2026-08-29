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
 * Validasi kompatibilitas warna antara produk dan bahan baku
 * @param {string} prefix - Prefix modal ('add' atau 'edit')
 * @returns {boolean} - true jika valid, false jika tidak valid
 */
function validateWarnaCompatibility(prefix) {
    const produkId = document.getElementById(`${prefix}_produk_id`).value;
    const bahanId = document.getElementById(`${prefix}_bahan_baku_id`).value;

    if (!produkId || !bahanId) {
        return true; // Belum pilih keduanya, biarkan validasi required field yang handle
    }

    // Cari option produk yang dipilih
    const produkOption = document.querySelector(`#${prefix}_produk_dropdown .dropdown-option[data-value="${produkId}"]`);
    const bahanOption = document.querySelector(`#${prefix}_bahan_baku_dropdown .dropdown-option[data-value="${bahanId}"]`);

    if (!produkOption || !bahanOption) {
        return true; // Option tidak ditemukan, biarkan backend yang handle
    }

    const produkWarna = produkOption.getAttribute('data-warna');
    const bahanWarna = bahanOption.getAttribute('data-warna');

    if (produkWarna !== bahanWarna) {
        const produkNama = produkOption.getAttribute('data-text').split('(')[0].trim();
        const bahanNama = bahanOption.getAttribute('data-text').split('(')[0].trim();

        const errorElement = document.getElementById(`${prefix}_warna_error`);
        if (errorElement) {
            const errorText = errorElement.querySelector('p');
            errorText.textContent = `Warna produk (${produkWarna}) dan bahan baku (${bahanWarna}) tidak cocok. Silakan pilih dengan warna yang sama.`;
            errorElement.classList.remove('hidden');
        }
        return false;
    }

    // Jika valid, sembunyikan error
    const errorElement = document.getElementById(`${prefix}_warna_error`);
    if (errorElement) {
        errorElement.classList.add('hidden');
    }

    return true;
}

window.validateWarnaCompatibility = validateWarnaCompatibility;

/**
 * Toggle Modal - Standard Baseline Produksi
 * Menggunakan Slide Panel API dari global-modal.js
 */

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

    // Reset tampilan option bahan baku ke semua kategori kain (agar saat modal dibuka lagi tidak ter-filter)
    const bahanDropdowns = modal.querySelectorAll('[id*="bahan_baku"][id$="_dropdown"]');
    bahanDropdowns.forEach(dd => {
        const noResultsId = dd.id.replace('_dropdown', '_no_results');
        const noResults = document.getElementById(noResultsId);
        showAllKainOptions(dd, noResults);
    });
}

/**
 * Initialize searchable dropdown
 * @param {string} prefix - Prefix ID
 * @param {Function} onSelectCallback - Optional callback when option is selected (value, selectedOption)
 */
function initSearchableDropdown(prefix, onSelectCallback) {
    const searchInput = document.getElementById(`${prefix}_search`);
    const hiddenInput = document.getElementById(`${prefix}_id`);
    const dropdown = document.getElementById(`${prefix}_dropdown`);
    const noResults = document.getElementById(`${prefix}_no_results`);
    const options = dropdown.querySelectorAll('.dropdown-option');

    if (!searchInput || !hiddenInput || !dropdown) return;

    // Simpan teks asli item yang selected (untuk restore saat blur tanpa pilih baru)
    let originalSelectedText = '';

    function updateOriginalText() {
        const currentId = hiddenInput.value;
        if (currentId) {
            const selectedOpt = dropdown.querySelector(`.dropdown-option[data-value="${currentId}"]`);
            originalSelectedText = selectedOpt ? selectedOpt.getAttribute('data-text') : '';
        } else {
            originalSelectedText = '';
        }
    }

    // Initialize original text on load
    updateOriginalText();

    // Show dropdown on focus — kosongkan search agar semua opsi muncul
    searchInput.addEventListener('focus', () => {
        updateOriginalText();
        searchInput.value = '';
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
            const isWarnaFiltered = option.classList.contains('warna-filtered-out');
            if (text.includes(searchTerm) && !isWarnaFiltered) {
                option.style.display = 'flex';
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
                opt.classList.remove('bg-gray-100');
            });
            const selectedCheckIcon = option.querySelector('.check-icon');
            if (selectedCheckIcon) {
                selectedCheckIcon.classList.remove('hidden');
            }
            option.classList.add('bg-gray-100');

            // Update original text agar saat blur, teks yang benar yang dikembalikan
            originalSelectedText = text;

            // Call onSelect callback if provided
            if (onSelectCallback) {
                onSelectCallback(value, option);
            }
        });
    });

    // Restore original text on blur (jika user tidak pilih item baru)
    searchInput.addEventListener('blur', () => {
        // Beri delay kecil agar click event pada option sempat diproses
        setTimeout(() => {
            if (!dropdown.classList.contains('hidden')) return;
            // Jika search input kosong dan ada item yang selected, kembalikan teks asli
            if (searchInput.value === '' && originalSelectedText) {
                searchInput.value = originalSelectedText;
            }
        }, 150);
    });

    // Hide dropdown on click outside
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
}

/**
 * Filter dropdown bahan baku berdasarkan warna produk yang dipilih.
 * Hanya menampilkan bahan baku dengan warna yang sama dan kategori kain.
 * @param {string} produkPrefix - Prefix ID dropdown produk (e.g., 'add_produk')
 * @param {string} bahanPrefix - Prefix ID dropdown bahan baku (e.g., 'add_bahan_baku')
 */
function filterBahanBakuByWarna(produkPrefix, bahanPrefix) {
    const produkSearchInput = document.getElementById(`${produkPrefix}_search`);
    const bahanDropdown = document.getElementById(`${bahanPrefix}_dropdown`);
    const bahanNoResults = document.getElementById(`${bahanPrefix}_no_results`);

    if (!produkSearchInput || !bahanDropdown) return;

    // Get selected produk option to extract warna
    const selectedProdukOption = document.querySelector(`#${produkPrefix}_dropdown .dropdown-option[data-value="${document.getElementById(`${produkPrefix}_id`).value}"]`);

    if (!selectedProdukOption) {
        // Jika tidak ada produk yang dipilih, tampilkan semua bahan baku kategori kain
        showAllKainOptions(bahanDropdown, bahanNoResults);
        return;
    }

    const produkWarna = selectedProdukOption.getAttribute('data-warna');
    const bahanOptions = bahanDropdown.querySelectorAll('.dropdown-option');
    let visibleCount = 0;

    bahanOptions.forEach(option => {
        const bahanWarna = option.getAttribute('data-warna');
        const bahanKategori = option.getAttribute('data-kategori');

        // Tampilkan hanya jika kategori kain DAN warna cocok dengan produk
        if (bahanKategori === 'kain' && bahanWarna === produkWarna) {
            option.classList.remove('warna-filtered-out');
            option.style.display = 'flex';
            visibleCount++;
        } else {
            option.classList.add('warna-filtered-out');
            option.style.display = 'none';
        }
    });

    // Show/hide no results message
    if (bahanNoResults) {
        if (visibleCount === 0) {
            bahanNoResults.classList.remove('hidden');
            bahanNoResults.textContent = `Tidak ada bahan baku kain warna ${produkWarna}`;
        } else {
            bahanNoResults.classList.add('hidden');
        }
    }
}

/**
 * Tampilkan semua option bahan baku kategori kain (saat produk belum dipilih)
 */
function showAllKainOptions(dropdown, noResults) {
    const options = dropdown.querySelectorAll('.dropdown-option');
    let visibleCount = 0;

    options.forEach(option => {
        const kategori = option.getAttribute('data-kategori');
        // Hapus class warna-filtered-out agar bisa tampil kembali
        option.classList.remove('warna-filtered-out');
        if (kategori === 'kain') {
            option.style.display = 'flex';
            visibleCount++;
        } else {
            option.style.display = 'none';
        }
    });

    if (noResults) {
        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
            noResults.textContent = 'Tidak ada bahan baku kategori kain';
        } else {
            noResults.classList.add('hidden');
        }
    }
}

function renderColorDot(elementId, warna) {
    const dot = document.getElementById(elementId);
    if (!dot) return;

    const normalized = (warna || '-').toLowerCase().trim();
    const colorMap = {
        'hitam': '#000000',
        'putih': '#FFFFFF',
        'abu-abu': '#9CA3AF',
        'abu': '#9CA3AF',
        'navy': '#061952',
        'silver': '#C0C0C0',
        'biru': '#2563EB',
    };

    let hex = colorMap[normalized];
    if (!hex) {
        if (normalized.includes('navy')) hex = '#061952';
        else if (normalized.includes('biru')) hex = '#2563EB';
        else if (normalized.includes('hitam')) hex = '#000000';
        else if (normalized.includes('putih')) hex = '#FFFFFF';
        else if (normalized.includes('silver')) hex = '#C0C0C0';
        else if (normalized.includes('abu')) hex = '#9CA3AF';
        else hex = '#CBD5E1';
    }

    const needsStroke = ['abu-abu', 'abu', 'putih', 'silver'].includes(normalized) || normalized.includes('putih');

    dot.style.backgroundColor = hex;
    dot.title = `Warna ${warna || '-'}`;
    dot.classList.toggle('ring-1', needsStroke);
    dot.classList.toggle('ring-gray-300', needsStroke);
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
    renderColorDot('detail_produk_dot', produkWarna);

    document.getElementById('detail_bahan').textContent = bahanNama;
    document.getElementById('detail_bahan_sub').textContent = `${bahanKode} · ${bahanWarna} · ${bahanKategori.charAt(0).toUpperCase() + bahanKategori.slice(1)}`;
    renderColorDot('detail_bahan_dot', bahanWarna);

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

    window.openPanel('detail-modal');
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
    editForm.action = `/admin/standard-baseline-produksi/${id}`;

    // Isi field
    document.getElementById('edit_produk_id').value = produkId;
    document.getElementById('edit_bahan_baku_id').value = bahanId;

    // Set search input text dan highlight visual untuk produk
    const produkOption = document.querySelector(`#edit_produk_dropdown .dropdown-option[data-value="${produkId}"]`);
    if (produkOption) {
        document.getElementById('edit_produk_search').value = produkOption.getAttribute('data-text');

        // Clear semua highlight di dropdown produk
        document.querySelectorAll('#edit_produk_dropdown .dropdown-option').forEach(opt => {
            opt.classList.remove('bg-gray-100');
            const checkIcon = opt.querySelector('.check-icon');
            if (checkIcon) checkIcon.classList.add('hidden');
        });

        // Highlight item yang selected
        produkOption.classList.add('bg-gray-100');
        const produkCheckIcon = produkOption.querySelector('.check-icon');
        if (produkCheckIcon) produkCheckIcon.classList.remove('hidden');
    }

    // Set search input text dan highlight visual untuk bahan baku
    const bahanOption = document.querySelector(`#edit_bahan_baku_dropdown .dropdown-option[data-value="${bahanId}"]`);
    if (bahanOption) {
        document.getElementById('edit_bahan_baku_search').value = bahanOption.getAttribute('data-text');

        // Clear semua highlight di dropdown bahan baku
        document.querySelectorAll('#edit_bahan_baku_dropdown .dropdown-option').forEach(opt => {
            opt.classList.remove('bg-gray-100');
            const checkIcon = opt.querySelector('.check-icon');
            if (checkIcon) checkIcon.classList.add('hidden');
        });

        // Highlight item yang selected
        bahanOption.classList.add('bg-gray-100');
        const bahanCheckIcon = bahanOption.querySelector('.check-icon');
        if (bahanCheckIcon) bahanCheckIcon.classList.remove('hidden');
    }

    document.getElementById('edit_pcs_per_roll').value = pcs;
    document.getElementById('edit_toleransi_minus').value = toleransi;
    document.getElementById('edit_keterangan').value = keterangan;

    // Set checkbox is_aktif
    const checkboxAktif = document.getElementById('edit_is_aktif');
    if (checkboxAktif) {
        checkboxAktif.checked = status === '1';
        if (typeof updateCheckbox === 'function') {
            updateCheckbox(checkboxAktif, 'edit_cb');
        }
    }

    // Filter dropdown bahan baku berdasarkan warna produk yang sudah tersimpan
    filterBahanBakuByWarna('edit_produk', 'edit_bahan_baku');

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

// Initialize searchable dropdowns on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    // Modal Tambah - dengan callback filter bahan baku saat produk dipilih
    initSearchableDropdown('add_produk', (value, option) => {
        // Saat produk dipilih, filter dropdown bahan baku berdasarkan warna
        filterBahanBakuByWarna('add_produk', 'add_bahan_baku');
    });
    initSearchableDropdown('add_bahan_baku');

    // Modal Edit - dengan callback filter bahan baku saat produk dipilih
    initSearchableDropdown('edit_produk', (value, option) => {
        // Saat produk dipilih, filter dropdown bahan baku berdasarkan warna
        filterBahanBakuByWarna('edit_produk', 'edit_bahan_baku');
    });
    initSearchableDropdown('edit_bahan_baku');

    // Add form validation for color compatibility
    const addForm = document.getElementById('addForm');
    if (addForm) {
        addForm.addEventListener('submit', (e) => {
            if (!validateWarnaCompatibility('add')) {
                e.preventDefault();
            }
        });
    }

    // Edit form validation for color compatibility
    const editForm = document.getElementById('editForm');
    if (editForm) {
        editForm.addEventListener('submit', (e) => {
            if (!validateWarnaCompatibility('edit')) {
                e.preventDefault();
            }
        });
    }
});

// Expose ke global scope
window.openDetailModal = openDetailModal;
window.openEditModal = openEditModal;
window.toggleFilterMenu = toggleFilterMenu;
window.toggleActionDropdown = toggleActionDropdown;
window.initSearchableDropdown = initSearchableDropdown;
window.filterBahanBakuByWarna = filterBahanBakuByWarna;
window.showAllKainOptions = showAllKainOptions;

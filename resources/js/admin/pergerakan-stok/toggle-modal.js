/**
 * Toggle Modal - Pergerakan Stok Bahan Baku
 * Mengatur buka/tutup modal dan detail transaksi.
 */

// Reset form saat panel ditutup
document.addEventListener('DOMContentLoaded', () => {
    // Initialize custom dropdowns
    initCustomDropdown('masuk_bahan_baku');
    initCustomDropdown('masuk_supplier');
    initCustomDropdown('keluar_bahan_baku');
    initCustomDropdown('keluar_penerima');
    
    ['add-modal-masuk', 'add-modal-keluar'].forEach(modalId => {
        const panel = document.getElementById(modalId);
        if (panel) {
            panel.addEventListener('panel:close', () => {
                const form = panel.querySelector('form');
                if (form) form.reset();
                
                // Reset custom dropdowns
                if (modalId === 'add-modal-masuk') {
                    resetCustomDropdown('masuk_bahan_baku');
                    resetCustomDropdown('masuk_supplier');
                    updateSatuanDisplay('masuk', '');
                } else if (modalId === 'add-modal-keluar') {
                    resetCustomDropdown('keluar_bahan_baku');
                    resetCustomDropdown('keluar_penerima');
                    updateSatuanDisplay('keluar', '');
                }
            });
        }
    });
});

// ============================================
// UPDATE SATUAN DINAMIS SAAT BAHAN BAKU DIPILIH
// ============================================
function updateSatuanDisplay(type, satuan) {
    const badge = document.getElementById(`${type}_satuan_badge`);
    const label = document.getElementById(`${type}_satuan_label`);
    
    if (satuan) {
        if (badge) {
            badge.textContent = satuan;
            badge.className = 'absolute right-4 top-1/2 -translate-y-1/2 text-xs font-semibold text-[#0F034D] bg-[#0F034D]/10 px-2 py-1 rounded-md';
        }
        if (label) label.textContent = `(${satuan})`;
    } else {
        if (badge) {
            badge.textContent = '—';
            badge.className = 'absolute right-4 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-1 rounded-md';
        }
        if (label) label.textContent = '';
    }
}

// Listen for bahan baku selection changes
document.addEventListener('DOMContentLoaded', () => {
    // Stok Masuk
    const masukHidden = document.getElementById('masuk_bahan_baku_value');
    if (masukHidden) {
        masukHidden.addEventListener('change', function () {
            const dropdown = document.getElementById('masuk_bahan_baku_dropdown');
            if (!dropdown) return;
            const selected = dropdown.querySelector(`.dropdown-option[data-value="${this.value}"]`);
            const satuan = selected ? selected.dataset.satuan : '';
            updateSatuanDisplay('masuk', satuan);
        });
    }

    // Stok Keluar
    const keluarHidden = document.getElementById('keluar_bahan_baku_value');
    if (keluarHidden) {
        keluarHidden.addEventListener('change', function () {
            const dropdown = document.getElementById('keluar_bahan_baku_dropdown');
            if (!dropdown) return;
            const selected = dropdown.querySelector(`.dropdown-option[data-value="${this.value}"]`);
            const satuan = selected ? selected.dataset.satuan : '';
            updateSatuanDisplay('keluar', satuan);
        });
    }
});

// ============================================
// DROPDOWN FILTER (kategori, supplier, tanggal)
// ============================================
function toggleDropdown(name) {
    const dropdown = document.getElementById('dropdown-' + name);
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
    
    allDropdowns.forEach(d => {
        if (d.id !== 'dropdown-' + name) {
            d.classList.add('opacity-0', 'scale-95', 'pointer-events-none', 'hidden');
            // Reset arrow for closed dropdowns
            const dropdownName = d.id.replace('dropdown-', '');
            const arrow = document.querySelector(`[data-dropdown="${dropdownName}"] .dropdown-arrow`);
            if (arrow) arrow.classList.remove('rotate-180');
        }
    });

    if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden', 'opacity-0', 'scale-95', 'pointer-events-none');
        // Rotate arrow
        const arrow = document.querySelector(`[data-dropdown="${name}"] .dropdown-arrow`);
        if (arrow) arrow.classList.add('rotate-180');
    } else {
        dropdown.classList.add('opacity-0', 'scale-95', 'pointer-events-none', 'hidden');
        // Reset arrow
        const arrow = document.querySelector(`[data-dropdown="${name}"] .dropdown-arrow`);
        if (arrow) arrow.classList.remove('rotate-180');
    }
}

// Close semua dropdown saat klik di luar
document.addEventListener('click', function(e) {
    // Close dropdown lama (desktop kategori/supplier/tanggal)
    if (!e.target.closest('[data-dropdown]') && !e.target.closest('[id^="dropdown-"]')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
            d.classList.add('opacity-0', 'scale-95', 'pointer-events-none', 'hidden');
            // Reset arrow
            const dropdownName = d.id.replace('dropdown-', '');
            const arrow = document.querySelector(`[data-dropdown="${dropdownName}"] .dropdown-arrow`);
            if (arrow) arrow.classList.remove('rotate-180');
        });
    }

    // Close filterDropdownMobile saat klik di luar
    if (!e.target.closest('#filterDropdownMobile') && !e.target.closest('[data-toggle-filter-menu]')) {
        const mobileDd = document.getElementById('filterDropdownMobile');
        if (mobileDd && !mobileDd.classList.contains('hidden')) {
            mobileDd.classList.add('opacity-0', 'scale-95', 'pointer-events-none', 'hidden');
        }
    }
});

// TOGGLE FILTER MENU (Mobile nested filter)
// ============================================
function toggleFilterMenu(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const allDropdowns = document.querySelectorAll('[id$="Dropdown"], [id$="DropdownMobile"]');
    
    allDropdowns.forEach(d => {
        if (d.id !== dropdownId) {
            d.classList.add('opacity-0', 'scale-95', 'pointer-events-none', 'hidden');
        }
    });

    if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden', 'opacity-0', 'scale-95', 'pointer-events-none');
    } else {
        dropdown.classList.add('opacity-0', 'scale-95', 'pointer-events-none', 'hidden');
    }
}

// ============================================
// MODAL DETAIL TRANSAKSI
// ============================================
function showDetail(type, data) {
    const modal = document.getElementById('detail-modal');
    const title = document.getElementById('detail-title');
    const date = document.getElementById('detail-date');
    const body = document.getElementById('detail-body');

    title.textContent = type === 'masuk' ? 'Detail Stok Masuk' : 'Detail Stok Keluar';
    date.textContent = data.tanggal;

    let html = '<div class="space-y-3">';

    // Bahan Baku
    html += `<div class="flex justify-between items-center py-2 border-b border-gray-100">
        <span class="text-sm text-gray-500">Bahan Baku</span>
        <span class="text-sm font-medium text-gray-900">${data.bahan_baku} <span class="text-gray-400">(${data.kode_bahan})</span></span>
    </div>`;

    // Jumlah
    const jumlahColor = type === 'masuk' ? 'text-green-600' : 'text-red-600';
    const jumlahPrefix = type === 'masuk' ? '+' : '-';
    html += `<div class="flex justify-between items-center py-2 border-b border-gray-100">
        <span class="text-sm text-gray-500">Jumlah</span>
        <span class="text-sm font-bold ${jumlahColor}">${jumlahPrefix}${data.jumlah} ${data.satuan}</span>
    </div>`;

    // Supplier / Penerima
    if (type === 'masuk') {
        html += `<div class="flex justify-between items-center py-2 border-b border-gray-100">
            <span class="text-sm text-gray-500">Supplier</span>
            <span class="text-sm font-medium text-gray-900">${data.supplier || '-'}</span>
        </div>`;
    } else {
        html += `<div class="flex justify-between items-center py-2 border-b border-gray-100">
            <span class="text-sm text-gray-500">Penerima</span>
            <span class="text-sm font-medium text-gray-900">${data.penerima || '-'}</span>
        </div>`;
    }

    // Admin
    html += `<div class="flex justify-between items-center py-2 border-b border-gray-100">
        <span class="text-sm text-gray-500">Admin</span>
        <span class="text-sm font-medium text-gray-900">${data.admin || '-'}</span>
    </div>`;

    // Keterangan/Catatan
    const note = type === 'masuk' ? (data.catatan || '-') : (data.keterangan || '-');
    html += `<div class="py-2 border-b border-gray-100">
        <span class="text-sm text-gray-500 block mb-1">Keterangan</span>
        <p class="text-sm text-gray-700">${note}</p>
    </div>`;

    // Bukti
    if (data.bukti) {
        html += `<div class="py-2">
            <span class="text-sm text-gray-500 block mb-2">Bukti ${type === 'masuk' ? 'Pembelian' : 'Pengeluaran'}</span>
            <img src="${data.bukti}" alt="Bukti" class="w-full max-h-64 object-cover rounded-xl border border-gray-200 cursor-pointer" onclick="window.open('${data.bukti}', '_blank')">
        </div>`;
    } else {
        html += `<div class="py-2">
            <span class="text-sm text-gray-500 block mb-1">Bukti ${type === 'masuk' ? 'Pembelian' : 'Pengeluaran'}</span>
            <p class="text-sm text-gray-400 italic">Tidak ada bukti dilampirkan</p>
        </div>`;
    }

    html += '</div>';
    body.innerHTML = html;

    togglePanel('detail-modal');
}

window.toggleDropdown = toggleDropdown;
window.toggleFilterMenu = toggleFilterMenu;
window.showDetail = showDetail;
window.updateSatuanDisplay = updateSatuanDisplay;


document.querySelectorAll('[data-stock-dropdown]').forEach((button) => {
    button.addEventListener('click', (event) => {
        event.stopPropagation();
        toggleDropdown(button.dataset.stockDropdown);
    });
});

document.querySelectorAll('[data-open-panel]').forEach((button) => {
    button.addEventListener('click', () => togglePanel(button.dataset.openPanel));
});

document.querySelectorAll('[data-close-panel]').forEach((button) => {
    button.addEventListener('click', () => closePanel(button.dataset.closePanel));
});

document.querySelectorAll('[data-confirm-delete]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        event.preventDefault();

        Swal.fire({
            title: 'Hapus Transaksi?',
            text: 'Data yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-xl font-sans',
                confirmButton: 'px-5 py-2.5 text-sm font-semibold rounded-lg',
                cancelButton: 'px-5 py-2.5 text-sm font-semibold rounded-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});

document.querySelectorAll('[data-show-detail]').forEach((button) => {
    button.addEventListener('click', () => {
        const type = button.dataset.showDetail;
        const data = JSON.parse(button.dataset.detailJson);
        showDetail(type, data);
    });
});

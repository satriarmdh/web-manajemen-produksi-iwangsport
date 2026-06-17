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
                } else if (modalId === 'add-modal-keluar') {
                    resetCustomDropdown('keluar_bahan_baku');
                    resetCustomDropdown('keluar_penerima');
                }
            });
        }
    });
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
        }
    });

    if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden', 'opacity-0', 'scale-95', 'pointer-events-none');
    } else {
        dropdown.classList.add('opacity-0', 'scale-95', 'pointer-events-none', 'hidden');
    }
}

// Close semua dropdown saat klik di luar
document.addEventListener('click', function(e) {
    // Close dropdown lama (desktop kategori/supplier/tanggal)
    if (!e.target.closest('[data-dropdown]') && !e.target.closest('[id^="dropdown-"]')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
            d.classList.add('opacity-0', 'scale-95', 'pointer-events-none', 'hidden');
        });
    }

    // Close filterDropdownMobile saat klik di luar
    if (!e.target.closest('#filterDropdownMobile') && !e.target.closest('[onclick*="toggleFilterMenu"]')) {
        const mobileDd = document.getElementById('filterDropdownMobile');
        if (mobileDd && !mobileDd.classList.contains('hidden')) {
            mobileDd.classList.add('opacity-0', 'scale-95', 'pointer-events-none', 'hidden');
        }
    }
});

// ============================================
// NESTED SUBMENU TOGGLE (Mobile - Kategori & Supplier)
// ============================================
document.addEventListener('click', function(e) {
    const groupDiv = e.target.closest('#filterDropdownMobile .relative.group');
    if (groupDiv) {
        const button = groupDiv.querySelector('button');
        if (!button || !button.contains(e.target)) return;

        const nestedSubmenu = groupDiv.querySelector('.nested-submenu');
        if (nestedSubmenu) {
            e.preventDefault();
            e.stopPropagation();

            // Tutup nested-submenu lain yang sedang terbuka
            document.querySelectorAll('#filterDropdownMobile .nested-submenu:not(.hidden)').forEach(el => {
                if (el !== nestedSubmenu) el.classList.add('hidden');
            });

            // Toggle submenu yang diklik
            nestedSubmenu.classList.toggle('hidden');
        }
        return;
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

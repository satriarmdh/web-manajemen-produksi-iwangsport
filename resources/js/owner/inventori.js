// State filter client-side untuk Bahan Baku
let currentBahanCat = 'semua';
let currentBahanStatus = window.inventoriConfig ? window.inventoriConfig.initialBahanStatus : 'semua';

// State filter client-side untuk Produk Jadi
let currentProdukSize = 'semua';
let currentProdukStatus = window.inventoriConfig ? window.inventoriConfig.initialProdukStatus : 'semua';

function toggleCustomDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    if (!dropdown) return;

    const isHidden = dropdown.classList.contains('hidden');
    
    // Close other dropdowns first
    document.querySelectorAll('[id^="dropdown-filter-"]').forEach(d => {
        if (d.id !== dropdownId) {
            d.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
            setTimeout(() => { d.classList.add('hidden'); }, 150);
        }
    });

    if (isHidden) {
        dropdown.classList.remove('hidden');
        setTimeout(() => {
            dropdown.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
            dropdown.classList.add('opacity-100', 'scale-100');
        }, 10);
    } else {
        dropdown.classList.remove('opacity-100', 'scale-100');
        dropdown.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
        setTimeout(() => {
            dropdown.classList.add('hidden');
        }, 150);
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    const isClickInside = e.target.closest('[id^="dropdown-filter-"]') || e.target.closest('button[onclick^="toggleCustomDropdown"]');
    if (!isClickInside) {
        closeAllCustomDropdowns();
    }
});

function closeAllCustomDropdowns() {
    document.querySelectorAll('[id^="dropdown-filter-"]').forEach(d => {
        d.classList.remove('opacity-100', 'scale-100');
        d.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
        setTimeout(() => { d.classList.add('hidden'); }, 150);
    });
}

function switchTab(tabId) {
    // Hide all contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
        content.classList.remove('block');
    });
    // Reset all tab button styles
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.className = "tab-btn flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition-all bg-white text-gray-500 hover:text-gray-800 hover:shadow-sm cursor-pointer";
    });

    // Show selected content
    const activeContent = document.getElementById('content-' + tabId);
    if (activeContent) {
        activeContent.classList.remove('hidden');
        activeContent.classList.add('block');
    }
    // Active selected button
    const activeBtn = document.getElementById('tab-' + tabId);
    if (activeBtn) {
        activeBtn.className = "tab-btn flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition-all bg-[#0F034D] text-white shadow-md shadow-[#0F034D]/20 cursor-pointer";
    }

    // Simpan tab aktif ke URL agar refresh / pagination tetap di tab ini.
    const tabUrl = new URL(window.location.href);
    if (tabId === 'stok-bahan') {
        // Tab default: bersihkan parameter agar URL tetap rapi.
        tabUrl.searchParams.delete('tab');
    } else {
        tabUrl.searchParams.set('tab', tabId);
    }
    history.replaceState(null, '', tabUrl.toString());
}

// --- FILTER MUTASI (SERVER-SIDE REDIRECT) ---
function applyMutasiFilter(paramName, paramValue) {
    const url = new URL(window.location.href);
    if (paramValue && paramValue !== 'semua') {
        url.searchParams.set(paramName, paramValue);
    } else {
        url.searchParams.delete(paramName);
    }
    url.searchParams.set('tab', 'mutasi-gudang');
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

function resetMutasiFilters() {
    const url = new URL(window.location.href);
    url.searchParams.delete('jenis_item');
    url.searchParams.delete('jenis_pergerakan');
    url.searchParams.set('tab', 'mutasi-gudang');
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

// --- FILTER BAHAN ---
function selectBahanCategory(cat) {
    currentBahanCat = cat;
    
    // Update active style di menu list
    document.querySelectorAll('.bahan-cat-btn').forEach(btn => {
        btn.className = "bahan-cat-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900";
    });
    event.target.className = "bahan-cat-btn block w-full text-left px-3 py-2 text-xs font-bold rounded-lg transition-colors bg-[#0F034D]/5 text-[#0F034D]";
    
    updateBahanActiveFilterBadge();
    filterBahanBaku();
    closeAllCustomDropdowns();
}

function selectBahanStatus(status) {
    currentBahanStatus = status;
    
    document.querySelectorAll('.bahan-status-btn').forEach(btn => {
        btn.className = "bahan-status-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900";
    });
    
    document.querySelectorAll('.bahan-status-btn').forEach(btn => {
        if (btn.textContent.trim().toLowerCase() === status) {
            btn.className = "bahan-status-btn block w-full text-left px-3 py-2 text-xs font-bold rounded-lg transition-colors bg-[#0F034D]/5 text-[#0F034D]";
        }
    });
    
    updateBahanActiveFilterBadge();
    filterBahanBaku();
    closeAllCustomDropdowns();
}

function updateBahanActiveFilterBadge() {
    const badge = document.getElementById('badge-active-filter-bahan');
    const btnReset = document.getElementById('btn-reset-bahan');
    const hasFilter = currentBahanCat !== 'semua' || currentBahanStatus !== 'semua' || document.getElementById('search-bahan').value.trim() !== '';
    
    if (hasFilter) {
        badge.classList.remove('hidden');
        btnReset.className = "flex items-center justify-center w-10 h-10 text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-colors cursor-pointer shrink-0 shadow-sm";
    } else {
        badge.classList.add('hidden');
        btnReset.className = "hidden";
    }
}

function resetBahanFilters() {
    // Kosongkan search bar
    document.getElementById('search-bahan').value = '';
    
    // Reset state
    currentBahanCat = 'semua';
    currentBahanStatus = 'semua';

    // Reset menu buttons style
    document.querySelectorAll('.bahan-cat-btn').forEach(btn => {
        if (btn.textContent.trim().toLowerCase() === 'semua kategori') {
            btn.className = "bahan-cat-btn block w-full text-left px-3 py-2 text-xs font-bold rounded-lg transition-colors bg-[#0F034D]/5 text-[#0F034D]";
        } else {
            btn.className = "bahan-cat-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900";
        }
    });

    document.querySelectorAll('.bahan-status-btn').forEach(btn => {
        if (btn.textContent.trim().toLowerCase() === 'semua status') {
            btn.className = "bahan-status-btn block w-full text-left px-3 py-2 text-xs font-bold rounded-lg transition-colors bg-[#0F034D]/5 text-[#0F034D]";
        } else {
            btn.className = "bahan-status-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900";
        }
    });

    updateBahanActiveFilterBadge();
    filterBahanBaku();
}

// Client-side filtering untuk Bahan Baku (dengan kategori)
function filterBahanBaku() {
    const query = document.getElementById('search-bahan').value.toLowerCase().trim();
    const rows = document.querySelectorAll('.bahan-row');

    rows.forEach(row => {
        const nama = row.getAttribute('data-nama');
        const kode = row.getAttribute('data-kode');
        const kategori = row.getAttribute('data-kategori');
        const status = row.getAttribute('data-status');

        const matchesQuery = nama.includes(query) || kode.includes(query);
        const matchesCategory = (currentBahanCat === 'semua') || (kategori === currentBahanCat);
        const matchesStatus = (currentBahanStatus === 'semua') || (status === currentBahanStatus);

        if (matchesQuery && matchesCategory && matchesStatus) {
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    });
    updateBahanActiveFilterBadge();
}


// --- FILTER PRODUK ---
function selectProdukSize(size) {
    currentProdukSize = size;
    
    // Update active style di menu list
    document.querySelectorAll('.produk-size-btn').forEach(btn => {
        btn.className = "produk-size-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900";
    });
    event.target.className = "produk-size-btn block w-full text-left px-3 py-2 text-xs font-bold rounded-lg transition-colors bg-[#0F034D]/5 text-[#0F034D]";
    
    updateProdukActiveFilterBadge();
    filterProduk();
    closeAllCustomDropdowns();
}

function selectProdukStatus(status) {
    currentProdukStatus = status;
    
    document.querySelectorAll('.produk-status-btn').forEach(btn => {
        btn.className = "produk-status-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900";
    });
    
    document.querySelectorAll('.produk-status-btn').forEach(btn => {
        if (btn.textContent.trim().toLowerCase() === status) {
            btn.className = "produk-status-btn block w-full text-left px-3 py-2 text-xs font-bold rounded-lg transition-colors bg-[#0F034D]/5 text-[#0F034D]";
        }
    });
    
    updateProdukActiveFilterBadge();
    filterProduk();
    closeAllCustomDropdowns();
}

function updateProdukActiveFilterBadge() {
    const badge = document.getElementById('badge-active-filter-produk');
    const btnReset = document.getElementById('btn-reset-produk');
    const hasFilter = currentProdukSize !== 'semua' || currentProdukStatus !== 'semua' || document.getElementById('search-produk').value.trim() !== '';
    
    if (hasFilter) {
        badge.classList.remove('hidden');
        btnReset.className = "flex items-center justify-center w-10 h-10 text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-colors cursor-pointer shrink-0 shadow-sm";
    } else {
        badge.classList.add('hidden');
        btnReset.className = "hidden";
    }
}

function resetProdukFilters() {
    // Kosongkan search bar
    document.getElementById('search-produk').value = '';
    
    // Reset state
    currentProdukSize = 'semua';
    currentProdukStatus = 'semua';

    // Reset menu style
    document.querySelectorAll('.produk-size-btn').forEach(btn => {
        if (btn.textContent.trim().toLowerCase() === 'semua ukuran') {
            btn.className = "produk-size-btn block w-full text-left px-3 py-2 text-xs font-bold rounded-lg transition-colors bg-[#0F034D]/5 text-[#0F034D]";
        } else {
            btn.className = "produk-size-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900";
        }
    });

    document.querySelectorAll('.produk-status-btn').forEach(btn => {
        if (btn.textContent.trim().toLowerCase() === 'semua status') {
            btn.className = "produk-status-btn block w-full text-left px-3 py-2 text-xs font-bold rounded-lg transition-colors bg-[#0F034D]/5 text-[#0F034D]";
        } else {
            btn.className = "produk-status-btn block w-full text-left px-3 py-2 text-xs font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900";
        }
    });

    updateProdukActiveFilterBadge();
    filterProduk();
}

// Client-side filtering untuk Produk (dengan ukuran)
function filterProduk() {
    const query = document.getElementById('search-produk').value.toLowerCase().trim();
    const rows = document.querySelectorAll('.produk-row');

    rows.forEach(row => {
        const nama = row.getAttribute('data-nama');
        const kode = row.getAttribute('data-kode');
        const ukuran = row.getAttribute('data-ukuran');
        const status = row.getAttribute('data-status');

        const matchesQuery = nama.includes(query) || kode.includes(query);
        const matchesSize = (currentProdukSize === 'semua') || (ukuran === currentProdukSize);
        const matchesStatus = (currentProdukStatus === 'semua') || (status === currentProdukStatus);

        if (matchesQuery && matchesSize && matchesStatus) {
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    });
    updateProdukActiveFilterBadge();
}

// --- INTERAKTIF FILTER DARI STAT CARD ---
function filterBahanFromCard(status) {
    switchTab('stok-bahan');
    selectBahanStatus(status);
    showCardFilterAlert('bahan', status);
}

function filterProdukFromCard(status) {
    switchTab('stok-produk');
    selectProdukStatus(status);
    showCardFilterAlert('produk', status);
}

function showCardFilterAlert(type, status) {
    const alertDiv = document.getElementById('alert-card-filter');
    const alertText = document.getElementById('alert-card-filter-text');
    const alertBtn = alertDiv.querySelector('button');
    
    if (status === 'menipis') {
        alertDiv.className = "mb-6 px-4 py-3.5 bg-amber-50 border border-amber-100 text-amber-800 rounded-xl text-sm flex items-center justify-between gap-3 shadow-sm";
        alertDiv.querySelector('svg').setAttribute('class', "w-5 h-5 text-amber-500 shrink-0");
        alertBtn.className = "text-xs font-bold bg-amber-100 hover:bg-amber-200 text-amber-900 px-3 py-1.5 rounded-lg transition-colors cursor-pointer shrink-0";
    } else {
        alertDiv.className = "mb-6 px-4 py-3.5 bg-rose-50 border border-rose-100 text-rose-800 rounded-xl text-sm flex items-center justify-between gap-3 shadow-sm";
        alertDiv.querySelector('svg').setAttribute('class', "w-5 h-5 text-rose-500 shrink-0");
        alertBtn.className = "text-xs font-bold bg-rose-100 hover:bg-rose-200 text-rose-900 px-3 py-1.5 rounded-lg transition-colors cursor-pointer shrink-0";
    }
    
    const displayName = type === 'bahan' ? 'Bahan Baku' : 'Produk Jadi';
    alertText.innerHTML = `Filter aktif: Menampilkan <strong>${displayName}</strong> dengan status <strong>${status.toUpperCase()}</strong>.`;
    alertDiv.classList.remove('hidden');
}

function clearCardFilter() {
    const alertDiv = document.getElementById('alert-card-filter');
    alertDiv.classList.add('hidden');
    
    resetBahanFilters();
    resetProdukFilters();
}

// Expose functions globally for onclick inline attributes
window.toggleCustomDropdown = toggleCustomDropdown;
window.switchTab = switchTab;
window.applyMutasiFilter = applyMutasiFilter;
window.resetMutasiFilters = resetMutasiFilters;
window.selectBahanCategory = selectBahanCategory;
window.selectBahanStatus = selectBahanStatus;
window.resetBahanFilters = resetBahanFilters;
window.selectProdukSize = selectProdukSize;
window.selectProdukStatus = selectProdukStatus;
window.resetProdukFilters = resetProdukFilters;
window.filterBahanFromCard = filterBahanFromCard;
window.filterProdukFromCard = filterProdukFromCard;
window.clearCardFilter = clearCardFilter;

// Initial state trigger from URL params on load
window.addEventListener('DOMContentLoaded', () => {
    document.getElementById('search-bahan').addEventListener('input', filterBahanBaku);
    document.getElementById('search-produk').addEventListener('input', filterProduk);

    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');
    if (activeTab) {
        switchTab(activeTab);
    } else if (window.inventoriConfig && window.inventoriConfig.requestStok === 'menipis') {
        filterBahanFromCard('menipis');
    }

    const mutasiContent = document.getElementById('content-mutasi-gudang');
    if (mutasiContent) {
        mutasiContent.querySelectorAll('a[href]').forEach(link => {
            try {
                const href = new URL(link.href, window.location.origin);
                if (href.searchParams.has('page')) {
                    href.searchParams.set('tab', 'mutasi-gudang');
                    link.href = href.toString();
                }
            } catch (e) { /* ignore */ }
        });
    }

    // ========== MUTASI DETAIL SLIDE PANEL ==========
    const mutasiModal = document.getElementById('mutasiDetailModal');

    document.querySelectorAll('[data-open-mutasi-detail]').forEach(btn => {
        btn.addEventListener('click', function () {
            const d = this.dataset;
            document.getElementById('detail_nama_item').textContent = d.nama || '-';
            document.getElementById('detail_nama').textContent = d.nama || '-';
            document.getElementById('detail_kode').textContent = d.kode || '-';
            document.getElementById('detail_tipe').textContent = d.jenisItem === 'bahan_baku' ? 'Bahan Baku' : 'Produk Jadi';
            document.getElementById('detail_pergerakan').textContent = (d.pergerakan || '-').charAt(0).toUpperCase() + (d.pergerakan || '').slice(1);
            document.getElementById('detail_jumlah').textContent = parseInt(d.jumlah || 0).toLocaleString('id-ID');
            document.getElementById('detail_stok_sebelum').textContent = parseInt(d.stokSebelum || 0).toLocaleString('id-ID');
            document.getElementById('detail_stok_sesudah').textContent = parseInt(d.stokSesudah || 0).toLocaleString('id-ID');
            document.getElementById('detail_waktu').textContent = d.waktu || '-';
            document.getElementById('detail_pic').textContent = d.pic || '-';
            document.getElementById('detail_keterangan').textContent = d.keterangan || '-';

            mutasiModal.classList.remove('hidden');
            mutasiModal.classList.add('is-open');
            setTimeout(() => mutasiModal.classList.remove('opacity-0'), 10);
        });
    });

    document.querySelectorAll('[data-close-mutasi-detail]').forEach(btn => {
        btn.addEventListener('click', () => {
            mutasiModal.classList.add('opacity-0');
            mutasiModal.classList.remove('is-open');
            setTimeout(() => mutasiModal.classList.add('hidden'), 300);
        });
    });
});

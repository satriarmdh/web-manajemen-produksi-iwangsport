// Distribusi tab switcher (Stok | Log | Riwayat)
document.querySelectorAll('[data-distribusi-tab]').forEach(btn => {
    btn.addEventListener('click', () => {
        const targetKey = btn.dataset.distribusiTab;
        const card = btn.closest('.rounded-xl.border');

        if (!card) return;

        // Update tab button styles
        card.querySelectorAll('[data-distribusi-tab]').forEach(t => {
            if (t === btn) {
                t.classList.add('bg-[#0F034D]', 'text-white', 'shadow-sm');
                t.classList.remove('text-gray-500', 'hover:text-[#0F034D]');
            } else {
                t.classList.remove('bg-[#0F034D]', 'text-white', 'shadow-sm');
                t.classList.add('text-gray-500', 'hover:text-[#0F034D]');
            }
        });

        // Show/hide content panels
        card.querySelectorAll('[data-distribusi-content]').forEach(c => {
            if (c.dataset.distribusiContent === targetKey) {
                c.classList.remove('hidden');
            } else {
                c.classList.add('hidden');
            }
        });
    });
});

// Product selector switcher (sidebar list → detail panel)
function selectProduct(productId) {
    // Update sidebar active states
    document.querySelectorAll('[data-product-selector]').forEach(btn => {
        if (btn.dataset.productSelector === String(productId)) {
            btn.classList.add('bg-[#0F034D]/5', 'border-[#0F034D]', 'shadow-sm');
            btn.classList.remove('bg-white', 'border-gray-300', 'hover:bg-gray-50', 'hover:border-gray-400', 'hover:-translate-y-0.5', 'hover:shadow-md');
        } else {
            btn.classList.remove('bg-[#0F034D]/5', 'border-[#0F034D]', 'shadow-sm');
            btn.classList.add('bg-white', 'border-gray-300', 'hover:bg-gray-50', 'hover:border-gray-400', 'hover:-translate-y-0.5', 'hover:shadow-md');
        }
    });

    // Show/hide detail panels
    document.querySelectorAll('[data-product-panel]').forEach(panel => {
        if (panel.dataset.productPanel === String(productId)) {
            panel.classList.remove('hidden');
        } else {
            panel.classList.add('hidden');
        }
    });

    // Sync URL hash for shareability
    if (window.location.hash !== `#produk-${productId}`) {
        history.replaceState(null, '', `#produk-${productId}`);
    }
}

document.querySelectorAll('[data-product-selector]').forEach(btn => {
    btn.addEventListener('click', () => {
        const productId = btn.dataset.productSelector;
        selectProduct(productId);
    });
});

// Product search filter (sidebar list)
const productSearch = document.querySelector('[data-product-search]');
if (productSearch) {
    productSearch.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        document.querySelectorAll('.product-list-item').forEach(item => {
            const name = item.dataset.productName || '';
            if (name.includes(query)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    });
}

// On load: read hash, select product (default: first)
document.addEventListener('DOMContentLoaded', () => {
    const hash = window.location.hash;
    let targetId = null;
    if (hash.startsWith('#produk-')) {
        const hashId = hash.replace('#produk-', '');
        if (document.querySelector(`[data-product-selector="${hashId}"]`)) {
            targetId = hashId;
        }
    }
    if (!targetId) {
        const first = document.querySelector('[data-product-selector]');
        if (first) {
            targetId = first.dataset.productSelector;
        }
    }
    if (targetId) {
        selectProduct(targetId);
    }
});

// Photo viewer modal (Lihat Foto bukti penerimaan)
document.addEventListener('click', function(e) {
    const btn = e.target.closest('[data-view-photo]');
    if (btn) {
        const photoUrl = btn.dataset.viewPhoto;
        const modal = document.getElementById('photoViewerModal');
        const img = document.getElementById('photoViewerImg');

        if (modal && img && photoUrl) {
            img.src = photoUrl;
            modal.classList.remove('hidden');
            requestAnimationFrame(() => {
                modal.classList.remove('opacity-0');
            });
        }
    }
});

// Close photo viewer
const photoModal = document.getElementById('photoViewerModal');
if (photoModal) {
    photoModal.addEventListener('click', function(e) {
        if (e.target === this || e.target.closest('[data-close-photo]')) {
            this.classList.add('opacity-0');
            setTimeout(() => this.classList.add('hidden'), 200);
        }
    });
}

// Stok Detail Modal (Data-view-stok-detail)
document.addEventListener('click', function(e) {
    const btn = e.target.closest('[data-view-stok-detail]');
    if (btn) {
        const data = btn.dataset;
        const modal = document.getElementById('stokDetailModal');
        const modalContent = document.getElementById('stokDetailModalContent');
        if (!modal || !modalContent) return;

        // Populate modal
        document.getElementById('modal-karyawan-name').textContent = data.karyawanName;
        document.getElementById('modal-peran').textContent = data.peran.toUpperCase();
        document.getElementById('modal-qty-hold').textContent = parseInt(data.qtyHold).toLocaleString('id-ID');
        document.getElementById('modal-total-selesai').textContent = parseInt(data.totalSelesai).toLocaleString('id-ID');
        document.getElementById('modal-total-dikeluarkan').textContent = parseInt(data.totalDikeluarkan).toLocaleString('id-ID');
        document.getElementById('modal-total-reject').textContent = parseInt(data.totalReject).toLocaleString('id-ID');
        document.getElementById('modal-ready-qty').textContent = parseInt(data.readyQty).toLocaleString('id-ID');

        // Status barang badge
        const statusBadge = document.getElementById('modal-status-barang-badge');
        if (statusBadge) {
            if (data.statusBarang === 'Ready') {
                statusBadge.textContent = 'Ready';
                statusBadge.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200';
            } else {
                statusBadge.textContent = 'Dalam Proses';
                statusBadge.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200';
            }
        }

        // Status pengerjaan
        const pengerjaanEl = document.getElementById('modal-status-pengerjaan');
        if (pengerjaanEl) {
            pengerjaanEl.textContent = data.isSelesai === '1' ? 'Sudah Selesai' : 'Dalam Pengerjaan';
        }

        // Selisih warning (qty_hold > 0 && is_selesai = true)
        const selisihWarning = document.getElementById('modal-selisih-warning');
        if (selisihWarning) {
            if (parseInt(data.qtyHold) > 0 && data.isSelesai === '1') {
                selisihWarning.classList.remove('hidden');
                const warnQty = document.getElementById('modal-qty-hold-warning');
                if (warnQty) warnQty.textContent = parseInt(data.qtyHold).toLocaleString('id-ID');
            } else {
                selisihWarning.classList.add('hidden');
            }
        }

        // Show modal
        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        });
    }
});

// Close stok detail modal
const stokModal = document.getElementById('stokDetailModal');
if (stokModal) {
    const closeModal = () => {
        stokModal.classList.add('opacity-0');
        const modalContent = document.getElementById('stokDetailModalContent');
        if (modalContent) {
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
        }
        setTimeout(() => stokModal.classList.add('hidden'), 200);
    };

    stokModal.addEventListener('click', function(e) {
        if (e.target === this || e.target.closest('[data-close-stok-modal]')) {
            closeModal();
        }
    });
}

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

// Stok Detail Slide Panel (data-view-stok-detail)
const stokPanel = document.getElementById('stokDetailPanel');
let stokRecordsCurrentPage = 1;
let currentStokKey = null;
let currentStokRegistryId = null;
let currentStokRecordType = 'cacat';
const STOK_RECORDS_PER_PAGE = 5;

function getStokRegistry(registryId = currentStokRegistryId) {
    const el = registryId ? document.getElementById(registryId) : null;
    if (!el) return {};
    try {
        return JSON.parse(el.textContent || '{}');
    } catch (e) {
        console.error('Failed parse stok registry:', e);
        return {};
    }
}

function getCurrentStokRecords() {
    const entry = getStokRegistry()[currentStokKey] || { cacat: [], selisih: null };
    if (currentStokRecordType === 'selisih') return entry.selisih ? [entry.selisih] : [];
    return Array.isArray(entry.cacat) ? entry.cacat : [];
}

function escapeHtml(value) {
    const node = document.createElement('div');
    node.textContent = value ?? '';
    return node.innerHTML;
}

function renderStokRecordCard(rec) {
    const isCacat = rec.jenis === 'cacat';
    const wrap = isCacat ? 'bg-amber-50 border-amber-200' : 'bg-red-50 border-red-200';
    const qtyColor = isCacat ? 'text-amber-700' : 'text-red-700';

    return `
        <article class="border rounded-lg px-3 py-2.5 ${wrap}">
            <div class="flex items-center justify-between gap-3">
                <span class="text-xs font-bold ${qtyColor}">${(rec.qty || 0).toLocaleString('id-ID')} pcs</span>
                <time class="text-[10px] text-gray-500">${escapeHtml(rec.tgl)}</time>
            </div>
            <p class="text-xs text-gray-700 leading-relaxed mt-1.5">${escapeHtml(rec.keterangan)}</p>
        </article>
    `;
}

function renderStokRecordsPage(records, page) {
    const list = document.getElementById('records-list');
    const empty = document.getElementById('records-empty');
    const pagination = document.getElementById('records-pagination');
    const pageInfo = document.getElementById('records-page-info');
    const prevBtn = document.getElementById('records-prev');
    const nextBtn = document.getElementById('records-next');
    const countLabel = document.getElementById('records-count-label');

    if (!list) return;
    const total = records.length;
    countLabel.textContent = `${total} catatan`;

    if (total === 0) {
        list.innerHTML = '';
        empty.classList.remove('hidden');
        pagination.classList.add('hidden');
        return;
    }
    empty.classList.add('hidden');

    const totalPages = Math.ceil(total / STOK_RECORDS_PER_PAGE);
    if (page > totalPages) page = totalPages;
    if (page < 1) page = 1;
    stokRecordsCurrentPage = page;

    const start = (page - 1) * STOK_RECORDS_PER_PAGE;
    const pageItems = records.slice(start, start + STOK_RECORDS_PER_PAGE);
    list.innerHTML = pageItems.map(renderStokRecordCard).join('');

    if (totalPages > 1) {
        pagination.classList.remove('hidden');
        pageInfo.textContent = `${page} / ${totalPages}`;
        prevBtn.disabled = page === 1;
        nextBtn.disabled = page === totalPages;
    } else {
        pagination.classList.add('hidden');
    }
}

function openStokPanel(data) {
    // Populate header + stats
    document.getElementById('modal-karyawan-name').textContent = data.karyawanName;
    document.getElementById('modal-peran').textContent = data.peran.toUpperCase();
    document.getElementById('modal-qty-hold').textContent = parseInt(data.qtyHold || 0).toLocaleString('id-ID');
    document.getElementById('modal-total-selesai').textContent = parseInt(data.totalSelesai || 0).toLocaleString('id-ID');
    document.getElementById('modal-total-dikeluarkan').textContent = parseInt(data.totalDikeluarkan || 0).toLocaleString('id-ID');
    document.getElementById('modal-ready-qty').textContent = parseInt(data.readyQty || 0).toLocaleString('id-ID');

    // Status hasil dan pengerjaan memakai dua konsep berbeda:
    // hasil yang menunggu serah terima vs pekerjaan yang ditandai selesai.
    const statusBadge = document.getElementById('modal-status-barang-badge');
    const readyQty = parseInt(data.readyQty || 0);
    const isSelesai = data.isSelesai === '1';
    if (statusBadge) {
        if (readyQty > 0) {
            statusBadge.textContent = 'Siap Diserahkan';
            statusBadge.className = 'px-2.5 py-1 rounded-full text-[11px] font-semibold shrink-0 bg-emerald-100 text-emerald-700 border border-emerald-200';
        } else if (isSelesai) {
            statusBadge.textContent = 'Selesai';
            statusBadge.className = 'px-2.5 py-1 rounded-full text-[11px] font-semibold shrink-0 bg-gray-100 text-gray-700 border border-gray-200';
        } else {
            statusBadge.textContent = 'Dalam Proses';
            statusBadge.className = 'px-2.5 py-1 rounded-full text-[11px] font-semibold shrink-0 bg-amber-100 text-amber-700 border border-amber-200';
        }
    }

    // Status pengerjaan
    const pengerjaanEl = document.getElementById('modal-status-pengerjaan');
    if (pengerjaanEl) {
        pengerjaanEl.textContent = isSelesai ? 'Sudah Selesai' : 'Dalam Pengerjaan';
        pengerjaanEl.className = isSelesai
            ? 'text-xs font-semibold text-emerald-700 shrink-0'
            : 'text-xs font-semibold text-gray-700 shrink-0';
    }

    currentStokKey = data.stokKey;
    currentStokRegistryId = data.registryId;
    currentStokRecordType = 'cacat';
    const registry = getStokRegistry();
    const entry = registry[currentStokKey] || { cacat: [], selisih: null };
    const cacatRecords = Array.isArray(entry.cacat) ? entry.cacat : [];
    const allRecords = entry.selisih ? [entry.selisih, ...cacatRecords] : [...cacatRecords];

    // Summary section
    const summarySection = document.getElementById('recordsSummarySection');
    const listSection = document.getElementById('recordsListSection');
    const totalCacat = cacatRecords.reduce((s, r) => s + (r.qty || 0), 0);
    const totalSelisih = entry.selisih ? (entry.selisih.qty || 0) : 0;

    if (allRecords.length > 0) {
        summarySection.classList.remove('hidden');
        listSection.classList.remove('hidden');
        document.getElementById('summary-total-cacat').textContent = totalCacat.toLocaleString('id-ID');
        document.getElementById('summary-count-cacat').textContent = cacatRecords.length;
        document.getElementById('summary-total-selisih').textContent = totalSelisih.toLocaleString('id-ID');
        document.getElementById('summary-count-selisih').textContent = entry.selisih ? 1 : 0;
        setStokRecordTab('cacat');
    } else {
        summarySection.classList.add('hidden');
        listSection.classList.remove('hidden');
        setStokRecordTab('cacat');
    }

    // Show panel
    stokPanel.classList.add('is-open');
}

function setStokRecordTab(type) {
    currentStokRecordType = type;
    document.querySelectorAll('[data-record-type]').forEach((tab) => {
        const active = tab.dataset.recordType === type;
        tab.setAttribute('aria-selected', String(active));
        tab.className = active
            ? 'px-2 py-1.5 text-[11px] font-semibold rounded-md bg-[#0F034D] text-white shadow-sm transition-colors'
            : 'px-2 py-1.5 text-[11px] font-semibold rounded-md text-gray-500 hover:text-[#0F034D] transition-colors';
    });
    renderStokRecordsPage(getCurrentStokRecords(), 1);
}

document.addEventListener('click', function(e) {
    const tab = e.target.closest('[data-record-type]');
    if (tab) setStokRecordTab(tab.dataset.recordType);

    const btn = e.target.closest('[data-view-stok-detail]');
    if (btn) {
        const data = btn.dataset;
        if (!stokPanel) return;
        openStokPanel(data);
    }
});

// Pagination handlers
 document.getElementById('records-prev')?.addEventListener('click', () => {
    if (stokRecordsCurrentPage > 1) renderStokRecordsPage(getCurrentStokRecords(), stokRecordsCurrentPage - 1);
});
document.getElementById('records-next')?.addEventListener('click', () => {
    renderStokRecordsPage(getCurrentStokRecords(), stokRecordsCurrentPage + 1);
});

// Close stok panel
if (stokPanel) {
    stokPanel.addEventListener('click', function(e) {
        if (e.target.closest('[data-close-stok-modal]') || e.target.classList.contains('slide-panel-backdrop')) {
            stokPanel.classList.remove('is-open');
        }
    });
}

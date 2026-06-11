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
        }, 300);
    }
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

// Expose ke global scope
window.toggleModal = toggleModal;
window.openDetailModal = openDetailModal;
window.openEditModal = openEditModal;
window.toggleFilterMenu = toggleFilterMenu;
window.toggleActionDropdown = toggleActionDropdown;

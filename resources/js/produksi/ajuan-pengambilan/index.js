const tabButtons = document.querySelectorAll('[data-ajuan-tab-button]');
const tabPanels = document.querySelectorAll('[data-ajuan-tab-panel]');
const filterForm = document.getElementById('filter-ajuan-form');

const closeDropdowns = (except = null) => {
    document.querySelectorAll('[data-custom-dropdown-menu]').forEach((menu) => {
        if (menu.dataset.customDropdownMenu !== except) menu.classList.add('hidden');
    });
    document.querySelectorAll('[data-custom-dropdown-arrow]').forEach((arrow) => {
        if (arrow.dataset.customDropdownArrow !== except) arrow.classList.remove('rotate-180');
    });
};

tabButtons.forEach((button) => {
    button.addEventListener('click', () => {
        const target = button.dataset.ajuanTabButton;
        tabButtons.forEach((item) => {
            const active = item.dataset.ajuanTabButton === target;
            item.classList.toggle('bg-[#0F034D]', active);
            item.classList.toggle('text-white', active);
            item.classList.toggle('shadow-md', active);
            item.classList.toggle('shadow-[#0F034D]/20', active);
            item.classList.toggle('text-gray-500', !active);
            item.classList.toggle('hover:bg-gray-50', !active);
        });
        tabPanels.forEach((panel) => panel.classList.toggle('hidden', panel.dataset.ajuanTabPanel !== target));
    });
});

let searchTimer;
const searchInput = document.getElementById('ajuan-search');
if (searchInput) {
    searchInput.closest('form')?.querySelector('input[name="search"]');
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => searchInput.closest('form')?.submit(), 450);
    });
}
document.getElementById('ajuan-date-filter')?.addEventListener('change', () => {
    document.getElementById('filter-ajuan-form')?.submit();
});

document.querySelectorAll('[data-custom-dropdown-button]').forEach((button) => {
    button.addEventListener('click', (event) => {
        event.stopPropagation();
        const key = button.dataset.customDropdownButton;
        const menu = document.querySelector(`[data-custom-dropdown-menu="${key}"]`);
        const arrow = document.querySelector(`[data-custom-dropdown-arrow="${key}"]`);
        closeDropdowns(key);
        menu?.classList.toggle('hidden');
        arrow?.classList.toggle('rotate-180');
    });
});

document.querySelectorAll('[data-custom-dropdown-option]').forEach((option) => {
    option.addEventListener('click', () => {
        const key = option.dataset.customDropdownOption;
        const value = option.dataset.value ?? '';
        // Update hidden input in filter form
        if (filterForm) {
            const hiddenInput = filterForm.querySelector(`input[name="${key}"]`);
            if (hiddenInput) hiddenInput.value = value;
        }
        // Also update standalone hidden inputs (backwards compat)
        const standaloneInput = document.querySelector(`[data-custom-dropdown-input="${key}"]`);
        if (standaloneInput) standaloneInput.value = value;
        filterForm?.submit();
    });
});

document.addEventListener('click', () => closeDropdowns());

// --- Riwayat Ajuan Status Filter (client-side) ---
const riwayatFilterBtns = document.querySelectorAll('[data-riwayat-status]');
const riwayatList = document.querySelector('[data-riwayat-list]');

if (riwayatFilterBtns.length && riwayatList) {
    riwayatFilterBtns.forEach((btn) => {
        btn.addEventListener('click', () => {
            const status = btn.dataset.riwayatStatus;

            // Toggle active styles
            riwayatFilterBtns.forEach((b) => {
                const isActive = b.dataset.riwayatStatus === status;
                b.classList.toggle('bg-[#0F034D]', isActive);
                b.classList.toggle('text-white', isActive);
                b.classList.toggle('border-[#0F034D]', isActive);
                b.classList.toggle('bg-white', !isActive);
                b.classList.toggle('text-gray-600', !isActive);
                b.classList.toggle('border-gray-200', !isActive);
                b.classList.toggle('hover:bg-gray-50', !isActive);
            });

            // Filter items
            const items = riwayatList.querySelectorAll('[data-riwayat-ajuan-status]');
            const groups = riwayatList.querySelectorAll('details');
            groups.forEach((group) => {
                const childItems = group.querySelectorAll('[data-riwayat-ajuan-status]');
                let hasVisible = false;
                childItems.forEach((item) => {
                    const match = status === '' || item.dataset.riwayatAjuanStatus === status;
                    item.style.display = match ? '' : 'none';
                    if (match) hasVisible = true;
                });
                group.style.display = hasVisible ? '' : 'none';
            });

            // Show/hide empty state
            let emptyMsg = riwayatList.querySelector('[data-riwayat-empty]');
            const anyVisible = riwayatList.querySelectorAll('details[style=""], details:not([style])');
            let visibleCount = 0;
            groups.forEach((g) => { if (g.style.display !== 'none') visibleCount++; });

            if (visibleCount === 0 && !emptyMsg) {
                emptyMsg = document.createElement('div');
                emptyMsg.dataset.riwayatEmpty = '';
                emptyMsg.className = 'rounded-xl bg-gray-50 border border-gray-100 p-6 text-center';
                emptyMsg.innerHTML = '<p class="text-sm font-semibold text-gray-600">Tidak ada ajuan dengan status ini.</p>';
                riwayatList.appendChild(emptyMsg);
            } else if (visibleCount > 0 && emptyMsg) {
                emptyMsg.remove();
            }
        });
    });
}
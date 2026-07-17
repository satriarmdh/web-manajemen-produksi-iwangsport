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
document.getElementById('ajuan-search')?.addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => filterForm?.submit(), 450);
});
document.getElementById('ajuan-date-filter')?.addEventListener('change', () => filterForm?.submit());

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
        const input = document.querySelector(`[data-custom-dropdown-input="${option.dataset.customDropdownOption}"]`);
        if (!input) return;
        input.value = option.dataset.value ?? '';
        filterForm?.submit();
    });
});

document.addEventListener('click', () => closeDropdowns());
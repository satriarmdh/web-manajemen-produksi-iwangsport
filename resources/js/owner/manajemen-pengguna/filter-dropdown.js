const closeDropdowns = (exceptId = null) => {
    document.querySelectorAll('[id$="Dropdown"]').forEach((dropdown) => {
        if (dropdown.id === exceptId) return;

        dropdown.classList.add('hidden', 'opacity-0', 'scale-95', 'pointer-events-none');
        dropdown.classList.remove('opacity-100', 'scale-100');
    });
};

const toggleFilterMenu = (dropdownId) => {
    closeDropdowns(dropdownId);

    const dropdown = document.getElementById(dropdownId);
    if (!dropdown) return;

    const isHidden = dropdown.classList.contains('hidden');

    if (isHidden) {
        dropdown.classList.remove('hidden');
        requestAnimationFrame(() => {
            dropdown.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
            dropdown.classList.add('opacity-100', 'scale-100');
        });
        return;
    }

    dropdown.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
    dropdown.classList.remove('opacity-100', 'scale-100');
    setTimeout(() => dropdown.classList.add('hidden'), 200);
};

document.querySelectorAll('[data-toggle-filter-menu]').forEach((button) => {
    button.addEventListener('click', (event) => {
        event.stopPropagation();
        toggleFilterMenu(button.dataset.toggleFilterMenu);
    });
});

document.addEventListener('click', (event) => {
    if (event.target.closest('[id$="Dropdown"]')) return;
    closeDropdowns();
});

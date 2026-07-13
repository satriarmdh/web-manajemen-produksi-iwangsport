document.querySelectorAll('[data-toggle-filter-menu]').forEach((button) => {
    button.addEventListener('click', (event) => {
        event.stopPropagation();

        const dropdownId = button.dataset.toggleFilterMenu;
        if (typeof window.toggleFilterMenu === 'function') {
            window.toggleFilterMenu(dropdownId);
        }
    });
});

/**
 * Toggle dropdown filter/sorting untuk halaman Perintah Produksi.
 * Pola ini mengikuti halaman Bahan Baku/Produk.
 */
function toggleFilterMenu(dropdownId) {
    const allDropdowns = document.querySelectorAll('[id$="Dropdown"]');
    allDropdowns.forEach((dropdown) => {
        if (dropdown.id !== dropdownId) {
            dropdown.classList.add('hidden', 'opacity-0', 'scale-95', 'pointer-events-none');
            dropdown.classList.remove('opacity-100', 'scale-100');
        }
    });

    const dropdown = document.getElementById(dropdownId);
    if (!dropdown) {
        return;
    }

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

document.addEventListener('click', (event) => {
    if (!event.target.closest('[id$="Dropdown"]') && !event.target.closest('[onclick*="toggleFilterMenu"]')) {
        document.querySelectorAll('[id$="Dropdown"]').forEach((dropdown) => {
            dropdown.classList.add('hidden', 'opacity-0', 'scale-95', 'pointer-events-none');
            dropdown.classList.remove('opacity-100', 'scale-100');
        });
    }
});

window.toggleFilterMenu = toggleFilterMenu;

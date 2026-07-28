document.addEventListener('DOMContentLoaded', () => {
    function toggleDropdown(dropdown) {
        const isHidden = dropdown.classList.contains('hidden');
        closeAllDropdowns(dropdown);
        if (isHidden) {
            dropdown.classList.remove('hidden');
            setTimeout(() => {
                dropdown.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
                dropdown.classList.add('opacity-100', 'scale-100');
            }, 10);
        } else {
            closeDropdown(dropdown);
        }
    }

    function closeDropdown(dropdown) {
        if (dropdown && !dropdown.classList.contains('hidden')) {
            dropdown.classList.remove('opacity-100', 'scale-100');
            dropdown.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
            setTimeout(() => {
                dropdown.classList.add('hidden');
            }, 150);
        }
    }

    function closeAllDropdowns(exceptDropdown = null) {
        const selectors = [
            '#dropdown-kategori',
            '#dropdown-tanggal',
            '#dropdown-tanggal-mulai',
            '#filterDropdownMobile',
            '#filterDropdown',
            '#sortDropdown'
        ];
        selectors.forEach(sel => {
            const dropdown = document.querySelector(sel);
            if (dropdown && dropdown !== exceptDropdown) {
                closeDropdown(dropdown);
            }
        });
    }

    // Bind event listener untuk button dengan data-toggle-filter-menu
    document.querySelectorAll('[data-toggle-filter-menu]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const id = btn.getAttribute('data-toggle-filter-menu');
            const dropdown = document.getElementById(id);
            if (dropdown) {
                toggleDropdown(dropdown);
            }
        });
    });

    // Bind event listener untuk button dengan data-stock-dropdown
    document.querySelectorAll('[data-stock-dropdown]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const id = btn.getAttribute('data-stock-dropdown');
            const dropdown = document.getElementById(`dropdown-${id}`);
            if (dropdown) {
                toggleDropdown(dropdown);
            }
        });
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        const clickedInside = e.target.closest('#dropdown-kategori, #dropdown-tanggal, #dropdown-tanggal-mulai, #filterDropdownMobile, #filterDropdown, #sortDropdown');
        const clickedTrigger = e.target.closest('[data-toggle-filter-menu], [data-stock-dropdown]');
        if (!clickedInside && !clickedTrigger) {
            closeAllDropdowns();
        }
    });
});

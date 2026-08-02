(() => {
    // Custom dropdown toggle with transitions (same pattern as pantau-progres)
    function openDropdown(menu, button) {
        menu.classList.remove('hidden');
        requestAnimationFrame(() => {
            menu.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
        });
        const arrow = button.querySelector('.dropdown-arrow');
        if (arrow) arrow.classList.add('rotate-180');
    }

    function closeDropdown(menu) {
        if (menu.classList.contains('hidden')) return;
        menu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
        const wrapper = menu.closest('[data-dropdown]');
        const arrow = wrapper?.querySelector('.dropdown-arrow');
        if (arrow) arrow.classList.remove('rotate-180');
        setTimeout(() => {
            if (menu.classList.contains('opacity-0')) {
                menu.classList.add('hidden');
            }
        }, 200);
    }

    document.querySelectorAll('[data-dropdown]').forEach((wrapper) => {
        const button = wrapper.querySelector('button');
        const menu = wrapper.querySelector('[id^="dropdown-"]');
        if (button && menu) {
            button.addEventListener('click', (e) => {
                e.stopPropagation();

                // Close others
                document.querySelectorAll('[id^="dropdown-"]').forEach((m) => {
                    if (m !== menu) closeDropdown(m);
                });

                // Toggle current
                if (menu.classList.contains('hidden')) {
                    openDropdown(menu, button);
                } else {
                    closeDropdown(menu);
                }
            });
        }
    });

    // Close on click outside
    document.addEventListener('click', () => {
        document.querySelectorAll('[id^="dropdown-"]').forEach((m) => {
            closeDropdown(m);
        });
    });
})();
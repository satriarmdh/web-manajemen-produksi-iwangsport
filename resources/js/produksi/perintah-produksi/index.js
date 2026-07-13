document.querySelectorAll('[data-custom-dropdown]').forEach((dropdown) => {
            const button = dropdown.querySelector('[data-dropdown-button]');
            const menu = dropdown.querySelector('[data-dropdown-menu]');
            const input = dropdown.querySelector('[data-dropdown-input]');
            const label = dropdown.querySelector('[data-dropdown-label]');
            const arrow = dropdown.querySelector('[data-dropdown-arrow]');

            button?.addEventListener('click', (event) => {
                event.stopPropagation();
                const isOpen = menu.classList.contains('opacity-100');

                document.querySelectorAll('[data-dropdown-menu]').forEach((otherMenu) => {
                    otherMenu.classList.remove('opacity-100', 'scale-100');
                    otherMenu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                });
                document.querySelectorAll('[data-dropdown-arrow]').forEach((otherArrow) => otherArrow.classList.remove('rotate-180'));

                if (!isOpen) {
                    menu.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
                    menu.classList.add('opacity-100', 'scale-100');
                    arrow?.classList.add('rotate-180');
                }
            });

            dropdown.querySelectorAll('[data-dropdown-option]').forEach((option) => {
                option.addEventListener('click', () => {
                    input.value = option.dataset.value ?? '';
                    label.textContent = option.dataset.label ?? option.textContent.trim();
                    menu.classList.remove('opacity-100', 'scale-100');
                    menu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                    arrow?.classList.remove('rotate-180');
                    dropdown.closest('form')?.submit();
                });
            });
        });

        let searchSubmitTimer;
        document.querySelectorAll('[data-search-input]').forEach((input) => {
            input.addEventListener('input', () => {
                clearTimeout(searchSubmitTimer);
                searchSubmitTimer = setTimeout(() => {
                    input.closest('form')?.submit();
                }, 600);
            });
        });

        document.querySelectorAll('[data-date-input]').forEach((input) => {
            input.addEventListener('change', () => {
                input.closest('form')?.submit();
            });
        });

        document.addEventListener('click', () => {
            document.querySelectorAll('[data-dropdown-menu]').forEach((menu) => {
                menu.classList.remove('opacity-100', 'scale-100');
                menu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
            });
            document.querySelectorAll('[data-dropdown-arrow]').forEach((arrow) => arrow.classList.remove('rotate-180'));
        });
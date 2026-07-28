// Custom dropdown toggle for filter with transitions
function openDropdown(menu, button) {
    menu.classList.remove('hidden');
    requestAnimationFrame(() => {
        menu.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
    });
    const arrow = button.querySelector('.dropdown-arrow');
    if (arrow) arrow.classList.add('rotate-180');
}

function closeDropdown(menu, button = null) {
    if (menu.classList.contains('hidden')) return;
    menu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
    
    const activeBtn = button || document.querySelector(`[data-penjualan-dropdown="${menu.id.replace('dropdown-', '')}"]`);
    const arrow = activeBtn?.querySelector('.dropdown-arrow');
    if (arrow) arrow.classList.remove('rotate-180');
    
    setTimeout(() => {
        if (menu.classList.contains('opacity-0')) {
            menu.classList.add('hidden');
        }
    }, 200);
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-penjualan-dropdown]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.stopPropagation();
            const name = button.dataset.penjualanDropdown;
            const dropdown = document.getElementById('dropdown-' + name);

            // Close all other dropdowns
            document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
                if (d.id !== 'dropdown-' + name) {
                    closeDropdown(d);
                }
            });

            // Toggle current
            if (dropdown.classList.contains('hidden')) {
                openDropdown(dropdown, button);
            } else {
                closeDropdown(dropdown, button);
            }
        });
    });

    // Close dropdown on outside click
    document.addEventListener('click', (e) => {
        document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
            const button = document.querySelector(`[data-penjualan-dropdown="${d.id.replace('dropdown-', '')}"]`);
            if (button && !button.contains(e.target) && !d.contains(e.target)) {
                closeDropdown(d, button);
            }
        });
    });

    // Confirm delete
    document.querySelectorAll('[data-confirm-delete]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Penjualan?',
                    text: 'Stok produk akan dikembalikan. Data tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-xl font-sans',
                        confirmButton: 'px-5 py-2.5 text-sm font-semibold rounded-lg',
                        cancelButton: 'px-5 py-2.5 text-sm font-semibold rounded-lg'
                    }
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            } else {
                if (confirm('Hapus penjualan ini? Stok akan dikembalikan.')) form.submit();
            }
        });
    });
});

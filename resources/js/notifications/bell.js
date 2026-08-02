/**
 * Notification Bell Component
 * Fetches dropdown HTML from server, manages badge count.
 * Requires: notification-bell, notification-dropdown, notification-badge elements in layout.
 */
(() => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const bellBtn = document.getElementById('notification-bell');
    const dropdown = document.getElementById('notification-dropdown');
    const badge = document.getElementById('notification-badge');

    if (!bellBtn || !dropdown) return;

    async function loadBadge() {
        try {
            const res = await fetch('/api/notifications', { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();
            if (data.unread_count > 0) {
                badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        } catch (e) { /* silent */ }
    }

    async function loadDropdown() {
        try {
            dropdown.innerHTML = '<div class="p-4 text-center text-sm text-gray-400">Memuat...</div>';
            const res = await fetch('/api/notifications/dropdown', { headers: { 'Accept': 'text/html' } });
            if (!res.ok) return;
            dropdown.innerHTML = await res.text();
            bindDropdownEvents();
        } catch (e) {
            dropdown.innerHTML = '<div class="p-4 text-center text-sm text-red-400">Gagal memuat notifikasi</div>';
        }
    }

    function bindDropdownEvents() {
        // Mark individual notification as read on click, then redirect to url
        dropdown.querySelectorAll('.notif-item[data-id]').forEach(item => {
            item.addEventListener('click', async (e) => {
                e.preventDefault();
                const id = item.dataset.id;
                const url = item.dataset.url || '#';
                await fetch('/api/notifications/' + id + '/read', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                });
                loadBadge();
                if (url && url !== '#') {
                    window.location.href = url;
                } else {
                    loadDropdown();
                }
            });
        });

        // Mark all as read
        const markAllBtn = dropdown.querySelector('#notification-mark-all');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                e.stopPropagation();
                await fetch('/api/notifications/read-all', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                });
                loadBadge();
                loadDropdown();
            });
        }
    }

    // Toggle dropdown
    bellBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = !dropdown.classList.contains('hidden');
        if (isOpen) {
            dropdown.classList.add('hidden');
        } else {
            dropdown.classList.remove('hidden');
            loadDropdown();
        }
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target) && !bellBtn.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Load badge count on page load
    loadBadge();
})();

// resources/js/owner/layout/toggle-navbar-menu.js

// --- 1. Logika Sidebar Desktop ---
window.toggleSidebar = function() {
    if(window.innerWidth < 768) return; 

    const sidebar = document.getElementById('sidebar');
    const header = document.getElementById('sidebar-header');
    const toggleIcon = document.getElementById('sidebar-toggle-icon');
    const texts = document.querySelectorAll('.sidebar-text');

    if (sidebar.classList.contains('w-72')) {
        sidebar.classList.replace('w-72', 'w-24');
        header.classList.remove('flex-row', 'justify-between', 'h-20', 'px-6');
        header.classList.add('flex-col', 'justify-center', 'py-6', 'gap-4');
        toggleIcon.classList.add('rotate-180');
        texts.forEach(text => text.classList.add('hidden'));

        const openMenus = document.querySelectorAll('.max-h-96');
        openMenus.forEach(menu => {
            menu.classList.replace('max-h-96', 'max-h-0');
            const btnIcon = menu.previousElementSibling.querySelector('svg:last-child');
            if(btnIcon) btnIcon.classList.remove('rotate-180');
        });
    } else {
        sidebar.classList.replace('w-24', 'w-72');
        header.classList.remove('flex-col', 'justify-center', 'py-6', 'gap-4');
        header.classList.add('flex-row', 'justify-between', 'h-20', 'px-6');
        toggleIcon.classList.remove('rotate-180');
        setTimeout(() => { texts.forEach(text => text.classList.remove('hidden')); }, 150);
    }
};

// --- 2. Logika Sidebar Mobile ---
window.toggleMobileSidebar = function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobile-overlay');

    if (sidebar.classList.contains('-translate-x-full')) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        setTimeout(() => overlay.classList.remove('opacity-0'), 10);
    } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('opacity-0');
        setTimeout(() => overlay.classList.add('hidden'), 300);
    }
};

// --- 3. Logika Buka Tutup Menu Tree (Dropdown) ---
window.toggleMenu = function(menuId, iconId) {
    const sidebar = document.getElementById('sidebar');
    if (sidebar.classList.contains('w-24') && window.innerWidth >= 768) {
        window.toggleSidebar();
        setTimeout(() => window.executeToggleMenu(menuId, iconId), 200);
        return;
    }
    window.executeToggleMenu(menuId, iconId);
};

window.executeToggleMenu = function(menuId, iconId) {
    const menu = document.getElementById(menuId);
    const icon = document.getElementById(iconId);
    if (menu.classList.contains('max-h-0')) {
        menu.classList.replace('max-h-0', 'max-h-96');
        icon.classList.add('rotate-180');
    } else {
        menu.classList.replace('max-h-96', 'max-h-0');
        icon.classList.remove('rotate-180');
    }
};

// --- 4. Logika Profile Dropdown ---
window.toggleProfileDropdown = function() {
    const dropdown = document.getElementById('profile-dropdown');
    if (dropdown.classList.contains('opacity-0')) {
        dropdown.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
        dropdown.classList.add('opacity-100', 'scale-100', 'pointer-events-auto');
    } else {
        dropdown.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
        dropdown.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
    }
};

// Klik di luar untuk menutup dropdown profile
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('profile-dropdown');
    const profileBtn = document.getElementById('profile-btn');
    if (profileBtn && dropdown) {
        if (!profileBtn.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
            dropdown.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
        }
    }
});
/**
 * Toggle profile dropdown in mobile nav layouts.
 * Usage: onclick="toggleProfileDropdown()"
 */
window.toggleProfileDropdown = function () {
    const dropdown = document.getElementById('profile-dropdown');
    const arrow = document.getElementById('profile-arrow');
    if (!dropdown) return;
    const isOpen = dropdown.classList.contains('opacity-100');

    if (isOpen) {
        dropdown.classList.remove('opacity-100', 'scale-100');
        dropdown.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
        arrow?.classList.remove('rotate-180');
    } else {
        dropdown.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
        dropdown.classList.add('opacity-100', 'scale-100');
        arrow?.classList.add('rotate-180');
    }
};

document.addEventListener('click', function (event) {
    const button = document.getElementById('profile-btn');
    const dropdown = document.getElementById('profile-dropdown');
    const arrow = document.getElementById('profile-arrow');

    if (!button || !dropdown) return;

    if (!button.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.classList.remove('opacity-100', 'scale-100');
        dropdown.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
        arrow?.classList.remove('rotate-180');
    }
});

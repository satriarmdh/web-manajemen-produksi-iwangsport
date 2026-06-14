/**
 * Toggle checkbox "Ingat Saya" di halaman login.
 * Hanya toggle icon check dan border kotak, tanpa styling seperti tombol.
 */

document.addEventListener('DOMContentLoaded', function () {
    const rememberWrapper = document.querySelector('[data-remember-wrapper]');
    const checkbox = document.getElementById('remember');
    const box = document.getElementById('remember_box');
    const icon = document.getElementById('remember_icon');

    if (!rememberWrapper || !checkbox || !box || !icon) return;

    rememberWrapper.addEventListener('click', function () {
        checkbox.checked = !checkbox.checked;

        if (checkbox.checked) {
            box.className = 'flex shrink-0 items-center justify-center w-5 h-5 rounded border-2 border-[#0F034D] transition-all';
            icon.classList.remove('hidden');
        } else {
            box.className = 'flex shrink-0 items-center justify-center w-5 h-5 rounded border-2 border-gray-300 transition-all';
            icon.classList.add('hidden');
        }
    });
});

/**
 * Toggle password field visibility between text/password.
 * Usage: onclick="togglePasswordVisibility('input-id', this)"
 */
window.togglePasswordVisibility = function (inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;

    const isPassword = input.getAttribute('type') === 'password';
    input.setAttribute('type', isPassword ? 'text' : 'password');

    const eyeOpen = btn.querySelector('.eye-open');
    const eyeClosed = btn.querySelector('.eye-closed');

    if (isPassword) {
        eyeOpen?.classList.add('hidden');
        eyeClosed?.classList.remove('hidden');
    } else {
        eyeOpen?.classList.remove('hidden');
        eyeClosed?.classList.add('hidden');
    }
};

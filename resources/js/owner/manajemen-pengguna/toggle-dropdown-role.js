// Logika dropdown peran (role) dan lihat/sembunyikan password pada manajemen pengguna Owner
document.addEventListener('DOMContentLoaded', function () {
    // 1. Logika Dropdown Peran
    const btn = document.getElementById('role_dropdown_btn');
    const menu = document.getElementById('role_dropdown_menu');
    const input = document.getElementById('role_input');
    const chevron = document.getElementById('role_chevron');
    const selectedText = document.getElementById('role_selected_text');
    const selectedIcon = document.getElementById('role_selected_icon');
    const options = document.querySelectorAll('.role-option');

    if (btn && menu && input) {
        function closeMenu() {
            menu.classList.add('hidden');
            menu.classList.add('opacity-0');
            menu.classList.add('scale-95');
            if (chevron) chevron.classList.remove('rotate-180');
        }

        function openMenu() {
            menu.classList.remove('hidden');
            setTimeout(() => {
                menu.classList.remove('opacity-0');
                menu.classList.remove('scale-95');
            }, 10);
            if (chevron) chevron.classList.add('rotate-180');
        }

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (menu.classList.contains('hidden')) {
                openMenu();
            } else {
                closeMenu();
            }
        });

        document.addEventListener('click', function () {
            closeMenu();
        });

        function selectRole(val) {
            options.forEach(opt => {
                const check = opt.querySelector('.check-icon');
                if (opt.getAttribute('data-value') === val) {
                    opt.classList.add('is-active');
                    if (check) check.classList.remove('hidden');
                    
                    const textNode = opt.querySelector('.option-text');
                    if (textNode) {
                        selectedText.innerText = textNode.textContent.trim();
                        if (selectedText.classList.contains('text-gray-500')) {
                            selectedText.classList.remove('text-gray-500');
                        }
                        selectedText.classList.add('text-gray-900', 'font-medium');
                    }

                    const svgNode = opt.querySelector('svg');
                    if (svgNode && selectedIcon) {
                        selectedIcon.innerHTML = svgNode.outerHTML;
                        selectedIcon.classList.remove('hidden');
                    }
                } else {
                    opt.classList.remove('is-active');
                    if (check) check.classList.add('hidden');
                }
            });
        }

        options.forEach(opt => {
            opt.addEventListener('click', function (e) {
                e.stopPropagation();
                const val = this.getAttribute('data-value');
                input.value = val;
                selectRole(val);
                closeMenu();
            });
        });

        if (input.value) {
            selectRole(input.value);
        }
    }

    // 2. Logika Lihat/Sembunyikan Password
    window.togglePasswordVisibility = function (inputId, btnEl) {
        const passwordField = document.getElementById(inputId);
        if (!passwordField) return;

        const isPassword = passwordField.getAttribute('type') === 'password';
        passwordField.setAttribute('type', isPassword ? 'text' : 'password');

        const eyeOpen = btnEl.querySelector('.eye-open');
        const eyeClosed = btnEl.querySelector('.eye-closed');

        if (isPassword) {
            if (eyeOpen) eyeOpen.classList.add('hidden');
            if (eyeClosed) eyeClosed.classList.remove('hidden');
        } else {
            if (eyeOpen) eyeOpen.classList.remove('hidden');
            if (eyeClosed) eyeClosed.classList.add('hidden');
        }
    };
});

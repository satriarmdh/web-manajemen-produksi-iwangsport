/**
 * Custom Dropdown & Checkbox Utilities
 * Konsisten di seluruh halaman admin (dropdown + checkbox custom styling).
 */

/**
 * Initialize a custom searchable dropdown.
 * @param {string} prefix - Prefix ID (e.g. 'add_ukuran' maps to add_ukuran_input, add_ukuran_value, etc.)
 */
function initCustomDropdown(prefix) {
    const searchInput = document.getElementById(`${prefix}_input`);
    const hiddenInput = document.getElementById(`${prefix}_value`);
    const dropdown = document.getElementById(`${prefix}_dropdown`);
    const noResults = document.getElementById(`${prefix}_no_results`);
    if (!searchInput || !hiddenInput || !dropdown) return;

    const options = dropdown.querySelectorAll('.dropdown-option');
    const placeholder = searchInput.placeholder;

    // Simpan teks asli item yang selected (untuk restore saat blur tanpa pilih baru)
    let originalSelectedText = '';

    function updateOriginalText() {
        const currentValue = hiddenInput.value;
        if (currentValue) {
            const selectedOpt = dropdown.querySelector(`.dropdown-option[data-value="${currentValue}"]`);
            originalSelectedText = selectedOpt ? selectedOpt.getAttribute('data-text') : '';
        } else {
            originalSelectedText = '';
        }
    }

    // Initialize original text on load
    updateOriginalText();

    function filterOptions() {
        const term = searchInput.value.toLowerCase();
        let count = 0;
        options.forEach(opt => {
            const text = opt.dataset.text.toLowerCase();
            if (text.includes(term)) { opt.style.display = ''; count++; }
            else { opt.style.display = 'none'; }
        });
        if (noResults) {
            if (count === 0) noResults.classList.remove('hidden');
            else noResults.classList.add('hidden');
        }
    }

    function selectOption(opt) {
        // Remove selected from previous (termasuk yang di-set oleh setCustomDropdownValue)
        options.forEach(o => {
            o.classList.remove('bg-gray-100');
            const ci = o.querySelector('.check-icon');
            if (ci) ci.classList.add('hidden');
        });
        // Set new selected
        opt.classList.add('bg-gray-100');
        const ci = opt.querySelector('.check-icon');
        if (ci) ci.classList.remove('hidden');

        hiddenInput.value = opt.dataset.value;
        hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        searchInput.value = opt.dataset.text;
        searchInput.classList.remove('text-gray-500');
        searchInput.classList.add('text-gray-900', 'font-medium');
        dropdown.classList.add('hidden');

        // Update original text agar saat blur, teks yang benar yang dikembalikan
        originalSelectedText = opt.dataset.text;
    }

    // Show dropdown on focus — kosongkan search agar semua opsi muncul
    searchInput.addEventListener('focus', () => {
        updateOriginalText();
        dropdown.classList.remove('hidden');
        searchInput.value = '';
        filterOptions();
    });

    // Filter on input
    searchInput.addEventListener('input', filterOptions);

    // Select option on click
    options.forEach(opt => {
        opt.addEventListener('click', () => selectOption(opt));
    });

    // Restore original text on blur (jika user tidak pilih item baru)
    searchInput.addEventListener('blur', () => {
        // Beri delay kecil agar click event pada option sempat diproses
        setTimeout(() => {
            if (!dropdown.classList.contains('hidden')) return;
            // Jika search input kosong dan ada item yang selected, kembalikan teks asli
            if (searchInput.value === '' && originalSelectedText) {
                searchInput.value = originalSelectedText;
            }
        }, 150);
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
}

/**
 * Set custom dropdown value programmatically (used when opening edit modal).
 * @param {string} prefix - Prefix ID
 * @param {string} value - Option value to select
 */
function setCustomDropdownValue(prefix, value) {
    const searchInput = document.getElementById(`${prefix}_input`);
    const hiddenInput = document.getElementById(`${prefix}_value`);
    const dropdown = document.getElementById(`${prefix}_dropdown`);
    if (!searchInput || !hiddenInput || !dropdown) return;

    // Reset all options styling
    dropdown.querySelectorAll('.dropdown-option').forEach(opt => {
        opt.classList.remove('bg-gray-100');
        const ci = opt.querySelector('.check-icon');
        if (ci) ci.classList.add('hidden');
    });

    // Find and select matching option
    const option = dropdown.querySelector(`.dropdown-option[data-value="${value}"]`);
    if (option) {
        option.classList.add('bg-gray-100');
        const ci = option.querySelector('.check-icon');
        if (ci) ci.classList.remove('hidden');

        hiddenInput.value = value;
        hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        searchInput.value = option.dataset.text;
        searchInput.classList.remove('text-gray-500');
        searchInput.classList.add('text-gray-900', 'font-medium');
    } else {
        // Reset if value not found
        hiddenInput.value = '';
        searchInput.value = '';
        searchInput.classList.remove('text-gray-900', 'font-medium');
        searchInput.classList.add('text-gray-500');
    }
}

/**
 * Reset custom dropdown to placeholder state (used when closing modal).
 * @param {string} prefix - Prefix ID
 */
function resetCustomDropdown(prefix) {
    const searchInput = document.getElementById(`${prefix}_input`);
    const hiddenInput = document.getElementById(`${prefix}_value`);
    const dropdown = document.getElementById(`${prefix}_dropdown`);
    if (!searchInput || !hiddenInput || !dropdown) return;

    // Reset all options styling
    dropdown.querySelectorAll('.dropdown-option').forEach(opt => {
        opt.classList.remove('bg-gray-100');
        const ci = opt.querySelector('.check-icon');
        if (ci) ci.classList.add('hidden');
    });

    hiddenInput.value = '';
    searchInput.value = '';
    searchInput.classList.remove('text-gray-900', 'font-medium');
    searchInput.classList.add('text-gray-500');
    dropdown.classList.add('hidden');
}

/**
 * Update custom checkbox visual styling.
 * @param {HTMLElement} checkbox - The hidden checkbox input
 * @param {string} prefix - Prefix for wrapper, box, icon, text element IDs
 */
function updateCheckbox(checkbox, prefix) {
    const wrapper = document.getElementById(`${prefix}_wrapper`);
    const box = document.getElementById(`${prefix}_box`);
    const icon = document.getElementById(`${prefix}_icon`);
    const text = document.getElementById(`${prefix}_text`);
    if (!wrapper || !box) return;

    if (checkbox.checked) {
        wrapper.className = 'flex items-center gap-2 p-2 border border-[#0F034D] bg-[#0F034D]/5 ring-1 ring-[#0F034D] rounded-xl cursor-pointer hover:bg-gray-50 transition-all';
        box.className = 'relative flex shrink-0 items-center justify-center w-5 h-5 rounded border-2 border-[#0F034D] transition-all';
        if (icon) icon.style.display = 'block';
        if (text) text.className = 'text-sm font-semibold text-[#0F034D]';
    } else {
        wrapper.className = 'flex items-center gap-2 p-2 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-all';
        box.className = 'relative flex shrink-0 items-center justify-center w-5 h-5 rounded border-2 border-gray-300 transition-all';
        if (icon) icon.style.display = 'none';
        if (text) text.className = 'text-sm font-medium text-gray-700';
    }
}

// Expose to global scope
window.initCustomDropdown = initCustomDropdown;
window.setCustomDropdownValue = setCustomDropdownValue;
window.resetCustomDropdown = resetCustomDropdown;
window.updateCheckbox = updateCheckbox;

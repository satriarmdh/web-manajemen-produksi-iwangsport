document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('textarea[name="alasan"]').forEach(textarea => {
        const form = textarea.closest('form');
        if (!form) return;
        const checkbox = form.querySelector('input[name="tandai_selesai"]');
        if (!checkbox) return;

        textarea.addEventListener('input', () => {
            if (textarea.value.trim().length > 0) {
                if (!checkbox.checked) {
                    checkbox.checked = true;
                    checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        });
    });
});

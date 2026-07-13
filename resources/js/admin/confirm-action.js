document.querySelectorAll('[data-confirm-action]').forEach((button) => {
    button.addEventListener('click', (event) => {
        const message = button.dataset.confirmAction;
        if (!confirm(message)) {
            event.preventDefault();
        }
    });
});

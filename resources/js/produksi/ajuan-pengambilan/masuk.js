const modal = document.querySelector('[data-reject-modal]');
const form = modal?.querySelector('form');
const textarea = modal?.querySelector('textarea');

const closeModal = () => {
    modal?.classList.add('hidden');
    modal?.classList.remove('flex');
    form?.reset();
};

document.querySelectorAll('[data-open-reject-modal]').forEach((button) => {
    button.addEventListener('click', () => {
        if (!modal || !form) return;
        form.action = button.dataset.rejectAction;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        textarea?.focus();
    });
});

document.querySelectorAll('[data-close-reject-modal]').forEach((button) => {
    button.addEventListener('click', closeModal);
});

modal?.addEventListener('click', (event) => {
    if (event.target === modal) closeModal();
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeModal();
});
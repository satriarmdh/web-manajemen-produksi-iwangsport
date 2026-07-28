const modal = document.querySelector('[data-reject-modal]');
const form = modal?.querySelector('form');
const textarea = modal?.querySelector('textarea');

const closeModal = () => {
    if (!modal) return;
    modal.classList.add('opacity-0');
    const body = modal.querySelector('div');
    if (body) {
        body.classList.remove('scale-100');
        body.classList.add('scale-95');
    }
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        form?.reset();
    }, 300);
};

document.querySelectorAll('[data-open-reject-modal]').forEach((button) => {
    button.addEventListener('click', () => {
        if (!modal || !form) return;
        form.action = button.dataset.rejectAction;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
            const body = modal.querySelector('div');
            if (body) {
                body.classList.remove('scale-95');
                body.classList.add('scale-100');
            }
        });
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
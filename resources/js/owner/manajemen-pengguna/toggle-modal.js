// Fungsi global untuk buka/tutup modal apa saja
window.toggleModal = function (modalId) {
    const modal = document.getElementById(modalId);
    const modalBox = modal.querySelector('.transform');
    
    if (modal.classList.contains('hidden')) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalBox.classList.remove('scale-95');
            modalBox.classList.add('scale-100');
        }, 10);
    } else {
        modal.classList.add('opacity-0');
        modalBox.classList.remove('scale-100');
        modalBox.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
};

// Fungsi khusus untuk menangkap data dan membuka Modal Edit
window.openEditModal = function (button) {
    // 1. Ambil data yang dititipkan di tombol
    const id = button.getAttribute('data-id');
    const name = button.getAttribute('data-name');
    const email = button.getAttribute('data-email');
    const role = button.getAttribute('data-role');

    // 2. Ubah URL action form agar menunjuk ke ID yang benar
    const form = document.getElementById('editForm');
    form.action = `/owner/users/${id}`;

    // 3. Isi nilai inputan di dalam modal
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_role').value = role;

    // 4. Munculkan modalnya!
    toggleModal('edit-modal');
};
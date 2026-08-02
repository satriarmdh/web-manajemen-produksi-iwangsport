/**
 * SweetAlert Confirmation — Shared utility for delete and critical action confirmations.
 * Usage: 
 *   - Delete: <button data-swal-delete data-url="/admin/path/123">Delete</button>
 *   - Critical: <form data-swal-confirm data-confirm-message="Apakah Anda yakin?">
 */
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swal === 'undefined') return;

    // ===== DELETE CONFIRMATION =====
    document.addEventListener('click', function (e) {
        const deleteBtn = e.target.closest('[data-swal-delete]');
        if (!deleteBtn) return;
        
        e.preventDefault();
        const url = deleteBtn.dataset.url || deleteBtn.href;
        const method = deleteBtn.dataset.method || 'DELETE';
        const message = deleteBtn.dataset.message || 'Data yang dihapus tidak dapat dikembalikan.';
        
        Swal.fire({
            title: 'Hapus Data?',
            html: `<span class="swal-confirm-text">${message}</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'swal-custom-popup',
                title: 'swal-custom-title',
                htmlContainer: 'swal-custom-html',
                icon: 'swal-custom-icon',
                confirmButton: 'swal-confirm-btn',
                cancelButton: 'swal-cancel-btn',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                    <input type="hidden" name="_method" value="${method}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // ===== CRITICAL ACTION CONFIRMATION =====
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form.hasAttribute('data-swal-confirm')) return;
        
        e.preventDefault();
        const message = form.dataset.confirmMessage || 'Apakah Anda yakin ingin menyimpan data ini?';
        const title = form.dataset.confirmTitle || 'Konfirmasi';
        const btnText = form.dataset.confirmButton || 'Ya, Simpan';
        
        Swal.fire({
            title: title,
            html: `<span class="swal-confirm-text">${message}</span>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: btnText,
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'swal-custom-popup',
                title: 'swal-custom-title',
                htmlContainer: 'swal-custom-html',
                icon: 'swal-custom-icon',
                confirmButton: 'swal-confirm-btn',
                cancelButton: 'swal-cancel-btn',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                form.removeAttribute('data-swal-confirm');
                form.submit();
            }
        });
    });
});

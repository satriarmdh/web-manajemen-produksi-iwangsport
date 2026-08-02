/**
 * SweetAlert textarea prompt for rejecting WO.
 * Populates hidden input then submits form.
 */
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swal === 'undefined') return;

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-swal-reject]');
        if (!btn) return;

        e.preventDefault();
        const woId = btn.dataset.swalReject;
        const nomorWo = btn.dataset.rejectNomor;
        const formId = btn.dataset.rejectForm;

        Swal.fire({
            title: 'Tolak Perintah Produksi?',
            html: `<span class="swal-confirm-text">WO ${nomorWo} akan ditolak. Berikan alasan penolakan di bawah ini.</span>`,
            icon: 'warning',
            input: 'textarea',
            inputLabel: 'Alasan Penolakan',
            inputPlaceholder: 'Tuliskan alasan penolakan...',
            inputAttributes: { 'aria-label': 'Alasan penolakan', rows: 4 },
            inputValidator: (value) => {
                if (!value || !value.trim()) return 'Alasan penolakan wajib diisi!';
            },
            showCancelButton: true,
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'swal-custom-popup',
                title: 'swal-custom-title',
                htmlContainer: 'swal-custom-html',
                icon: 'swal-custom-icon',
                confirmButton: 'swal-confirm-btn swal-reject-btn',
                cancelButton: 'swal-cancel-btn',
                input: 'swal-custom-input',
            },
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                document.getElementById('alasan-' + woId).value = result.value;
                document.getElementById(formId).submit();
            }
        });
    });
});

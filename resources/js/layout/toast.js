// resources/js/layout/toast.js
// SweetAlert success/error/warning — toast top-end

document.addEventListener('DOMContentLoaded', function () {
    if (!window.flashMessages) return;

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        showCloseButton: true,
        timer: 3500,
        timerProgressBar: true,
        customClass: {
            popup: 'swal-custom-popup',
            title: 'swal-custom-title',
            htmlContainer: 'swal-custom-html',
            icon: 'swal-custom-icon',
            closeButton: 'swal-custom-close',
            timerProgressBar: 'swal-custom-progress',
        },
        didOpen: (t) => {
            t.addEventListener('mouseenter', Swal.stopTimer);
            t.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    if (window.flashMessages.success) {
        Toast.fire({ icon: 'success', title: 'Berhasil!', html: window.flashMessages.success });
    }

    if (window.flashMessages.error) {
        Toast.fire({ icon: 'error', title: 'Gagal!', html: window.flashMessages.error, timer: 5000 });
    }

    if (window.flashMessages.warning) {
        Toast.fire({ icon: 'warning', title: 'Perhatian!', html: window.flashMessages.warning, timer: 5000 });
    }
});
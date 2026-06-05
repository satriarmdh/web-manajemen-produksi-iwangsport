// resources/js/owner/layout/toast.js

document.addEventListener('DOMContentLoaded', function () {
    // Pastikan objek flashMessages ada (dikirim dari Blade)
    if (!window.flashMessages) return;

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        customClass: {
            popup: 'rounded-xl shadow-lg border border-gray-50 font-sans px-4 py-3',
            title: 'text-[#0F034D] text-sm font-bold ml-2',
            htmlContainer: 'text-gray-500 text-xs font-medium ml-2',
            icon: 'border-none w-auto h-auto mt-2 ml-2'
        },
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // Cek masing-masing pesan
    if (window.flashMessages.success) {
        Toast.fire({
            icon: 'success',
            // title: 'Berhasil!',
            html: window.flashMessages.success
        });
    }

    if (window.flashMessages.error) {
        Toast.fire({
            icon: 'error',
            // title: 'Gagal!',
            html: window.flashMessages.error
        });
    }

    if (window.flashMessages.warning) {
        Toast.fire({
            icon: 'warning',
            // title: 'Peringatan!',
            html: window.flashMessages.warning
        });
    }
});
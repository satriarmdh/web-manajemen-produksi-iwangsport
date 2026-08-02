import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/owner/manajemen-pengguna/toggle-dropdown-role.js',
                'resources/js/utils/toggle-password.js',
                'resources/js/utils/swal-confirm.js',
                'resources/js/notifications/bell.js',
                'resources/js/owner/reject-wo.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
<<<<<<< HEAD
import tailwindcss from '@tailwindcss/vite';
=======
>>>>>>> base_v3

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
<<<<<<< HEAD
        tailwindcss(),
=======
>>>>>>> base_v3
    ],
});

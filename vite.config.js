import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/auth.css',
                'resources/css/home.css',
                'resources/css/module-level.css',
                'resources/js/app.js',
                'resources/js/text-editor.js',
                'resources/js/auth.js',
                'resources/js/custom-question.js',
                'resources/js/results-graphs.js',
                'resources/js/module-information-fields.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});

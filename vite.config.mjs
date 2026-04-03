import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/vite-app.scss',
                'resources/js/vite-app.js',
            ],
            refresh: [
                'resources/views/**/*.blade.php',
                'routes/**',
            ],
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                includePaths: [
                    path.resolve(__dirname, 'node_modules'),
                    path.resolve(__dirname, 'resources/sass'),
                ],
                silenceDeprecations: ['legacy-js-api', 'import'],
            },
        },
    },
    server: {
        host: '127.0.0.1',
        port: 5173,
        strictPort: true,
    },
});

import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import { defineConfig } from 'vite';

export default defineConfig({
    build: {
        emptyOutDir: false,
        sourcemap: true,
    },
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        })
    ],
    ssr: {
        noExternal: ['laravel-vite-plugin', '@inertiajs/vue3/server'],
    },
    resolve: {
        alias: {
            '@shopen': path.resolve(__dirname, 'src/resources/js'),
        },
        dedupe: ['vue', 'pinia', '@inertiajs/vue3'],
    },
});

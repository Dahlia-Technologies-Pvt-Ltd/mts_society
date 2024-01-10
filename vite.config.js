import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel(['resources/src/index.tsx']),
    ],
    resolve: {
        alias: {
            '@src': '/resources/src',
        },
    },
});
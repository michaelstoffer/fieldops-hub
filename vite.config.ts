import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        VitePWA({
            registerType: 'autoUpdate',
            injectRegister: 'auto',
            // Use injectManifest so we can write a custom SW with BackgroundSync
            strategies: 'injectManifest',
            srcDir: 'resources/js',
            filename: 'sw.ts',
            injectManifest: {
                globPatterns: ['**/*.{js,css,ico,png,svg,woff2}'],
            },
            manifest: {
                name: 'FieldOps Hub – Technician',
                short_name: 'FieldOps',
                description: 'Technician field job management',
                theme_color: '#0f172a',
                background_color: '#ffffff',
                display: 'standalone',
                orientation: 'portrait',
                start_url: '/technician/dashboard',
                scope: '/',
                icons: [
                    {
                        src: '/favicon.ico',
                        sizes: '48x48',
                        type: 'image/x-icon',
                    },
                ],
            },
            devOptions: {
                enabled: false,
            },
        }),
    ],
});

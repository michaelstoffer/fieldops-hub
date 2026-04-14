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

    build: {
        // Raise the warning threshold — chunks over 1MB get flagged
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    // FullCalendar — only used on /owner/calendar, ~150KB gzipped
                    if (id.includes('@fullcalendar')) {
                        return 'fullcalendar';
                    }
                    // Sentry — error monitoring, not needed on initial paint
                    if (id.includes('@sentry')) {
                        return 'sentry';
                    }
                    // Vue ecosystem — stable, long-cache TTL
                    if (id.includes('node_modules/vue') ||
                        id.includes('node_modules/@vue') ||
                        id.includes('node_modules/@inertiajs')) {
                        return 'vue-vendor';
                    }
                    // UI primitives — reka-ui, lucide, clsx, cva
                    if (id.includes('node_modules/reka-ui') ||
                        id.includes('node_modules/lucide-vue-next') ||
                        id.includes('node_modules/class-variance-authority') ||
                        id.includes('node_modules/clsx') ||
                        id.includes('node_modules/tailwind-merge')) {
                        return 'ui-vendor';
                    }
                },
            },
        },
    },
});

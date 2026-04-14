import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    async setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        // Sentry is large (~50KB) and only needed in production.
        // Load it asynchronously so it never blocks the initial paint.
        if (import.meta.env.VITE_SENTRY_DSN && import.meta.env.VITE_APP_ENV === 'production') {
            import('@sentry/vue').then((Sentry) => {
                Sentry.init({
                    app,
                    dsn: import.meta.env.VITE_SENTRY_DSN as string,
                    environment: import.meta.env.VITE_APP_ENV as string,
                    integrations: [Sentry.browserTracingIntegration()],
                    tracesSampleRate: 0.1,
                });
            });
        }

        app.use(plugin).mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

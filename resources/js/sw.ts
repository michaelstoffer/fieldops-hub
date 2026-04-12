/// <reference lib="webworker" />
import { BackgroundSyncPlugin } from 'workbox-background-sync';
import { ExpirationPlugin } from 'workbox-expiration';
import { cleanupOutdatedCaches, precacheAndRoute } from 'workbox-precaching';
import { registerRoute } from 'workbox-routing';
import { NetworkFirst, NetworkOnly, StaleWhileRevalidate } from 'workbox-strategies';

declare let self: ServiceWorkerGlobalScope;

// Injected by vite-plugin-pwa at build time
precacheAndRoute(self.__WB_MANIFEST);
cleanupOutdatedCaches();

// ── Read: serve cached job data when offline ──────────────────────────────────
registerRoute(
    ({ url }) => url.pathname.startsWith('/api/technician/jobs') && !url.pathname.includes('/photos') && !url.pathname.includes('/line-items') && !url.pathname.includes('/checklist') && !url.pathname.includes('/notes') && !url.pathname.includes('/status'),
    new StaleWhileRevalidate({
        cacheName: 'technician-jobs-api',
        plugins: [
            new ExpirationPlugin({ maxAgeSeconds: 60 * 60 * 8 }),
        ],
    }),
);

registerRoute(
    ({ url }) => url.pathname === '/api/technician/catalog',
    new StaleWhileRevalidate({
        cacheName: 'technician-catalog-api',
        plugins: [
            new ExpirationPlugin({ maxAgeSeconds: 60 * 60 * 24 }),
        ],
    }),
);

// ── Background sync queue for mutation requests ───────────────────────────────
// Queues writes when offline and replays them on reconnect.
const jobWritesQueue = new BackgroundSyncPlugin('job-writes-queue', {
    maxRetentionTime: 24 * 60, // keep queued requests for up to 24 hours
});

// Status updates
registerRoute(
    ({ url, request }) => url.pathname.match(/^\/api\/technician\/jobs\/\d+\/status$/) && request.method === 'PATCH',
    new NetworkOnly({ plugins: [jobWritesQueue] }),
    'PATCH',
);

// Technician notes
registerRoute(
    ({ url, request }) => url.pathname.match(/^\/api\/technician\/jobs\/\d+\/notes$/) && request.method === 'PATCH',
    new NetworkOnly({ plugins: [jobWritesQueue] }),
    'PATCH',
);

// Customer notes
registerRoute(
    ({ url, request }) => url.pathname.match(/^\/api\/technician\/jobs\/\d+\/customer-notes$/) && request.method === 'PATCH',
    new NetworkOnly({ plugins: [jobWritesQueue] }),
    'PATCH',
);

// Checklist toggles
registerRoute(
    ({ url, request }) => url.pathname.match(/^\/api\/technician\/jobs\/\d+\/checklist\/\d+$/) && request.method === 'PATCH',
    new NetworkOnly({ plugins: [jobWritesQueue] }),
    'PATCH',
);

// Line item create
registerRoute(
    ({ url, request }) => url.pathname.match(/^\/api\/technician\/jobs\/\d+\/line-items$/) && request.method === 'POST',
    new NetworkOnly({ plugins: [jobWritesQueue] }),
    'POST',
);

// Line item update
registerRoute(
    ({ url, request }) => url.pathname.match(/^\/api\/technician\/jobs\/\d+\/line-items\/\d+$/) && request.method === 'PATCH',
    new NetworkOnly({ plugins: [jobWritesQueue] }),
    'PATCH',
);

// Line item delete
registerRoute(
    ({ url, request }) => url.pathname.match(/^\/api\/technician\/jobs\/\d+\/line-items\/\d+$/) && request.method === 'DELETE',
    new NetworkOnly({ plugins: [jobWritesQueue] }),
    'DELETE',
);

// Photo upload — excluded from sync queue (binary data doesn't survive serialisation well;
// photos require connectivity at upload time)
registerRoute(
    ({ url, request }) => url.pathname.match(/^\/api\/technician\/jobs\/\d+\/photos$/) && request.method === 'POST',
    new NetworkFirst({ cacheName: 'photo-uploads' }),
    'POST',
);

// Photo delete
registerRoute(
    ({ url, request }) => url.pathname.match(/^\/api\/technician\/jobs\/\d+\/photos\/\d+$/) && request.method === 'DELETE',
    new NetworkOnly({ plugins: [jobWritesQueue] }),
    'DELETE',
);

// ── Notify clients when the SW takes over ────────────────────────────────────
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

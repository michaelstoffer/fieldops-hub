<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface Customer { id: number; first_name: string; last_name: string }
interface Estimate {
    id: number;
    estimate_number: string | null;
    title: string;
    status: string;
    expires_at: string | null;
    created_at: string;
    customer: Customer | null;
}

interface Paginated {
    data: Estimate[];
    links: { url: string | null; label: string; active: boolean }[];
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    estimates: Paginated;
    filters: { search?: string; status?: string };
    statuses: Record<string, string>;
}>();

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');

const STATUS_CLASSES: Record<string, string> = {
    draft:    'bg-muted text-muted-foreground',
    sent:     'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
    accepted: 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400',
    declined: 'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-400',
    expired:  'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400',
};

let searchTimeout: ReturnType<typeof setTimeout>;

function applyFilters() {
    router.get(
        '/owner/estimates',
        { search: search.value || undefined, status: status.value || undefined },
        { preserveState: true, replace: true },
    );
}

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});
watch(status, applyFilters);

function formatDate(dt: string | null): string {
    if (!dt) return '—';
    return new Date(dt).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}
</script>

<template>
    <OwnerLayout title="Estimates">
        <Head title="Estimates" />

        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-foreground">Estimates</h2>
                <p class="mt-0.5 text-sm text-muted-foreground">{{ estimates.total }} total</p>
            </div>
            <Link
                href="/owner/estimates/create"
                class="inline-flex items-center gap-2 rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700"
            >
                + New Estimate
            </Link>
        </div>

        <!-- Filters -->
        <div class="mb-4 flex flex-wrap gap-3">
            <label for="estimate-search" class="sr-only">Search estimates</label>
            <input
                id="estimate-search"
                v-model="search"
                type="search"
                placeholder="Search estimates…"
                class="rounded-lg border border-input bg-background px-3 py-1.5 text-sm text-foreground focus:border-slate-500 focus:outline-none"
            />
            <label for="estimate-status" class="sr-only">Filter by status</label>
            <select
                id="estimate-status"
                v-model="status"
                class="rounded-lg border border-input bg-background px-3 py-1.5 text-sm text-foreground focus:border-slate-500 focus:outline-none dark:[color-scheme:dark]"
            >
                <option value="">All statuses</option>
                <option v-for="(label, key) in statuses" :key="key" :value="key">{{ label }}</option>
            </select>
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-xl bg-card shadow-sm ring-1 ring-border">
            <table class="min-w-full divide-y divide-border text-sm">
                <thead class="bg-background text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                    <tr>
                        <th class="px-4 py-3 text-left">Number</th>
                        <th class="px-4 py-3 text-left">Title</th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Expires</th>
                        <th class="px-4 py-3 text-left">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr v-if="estimates.data.length === 0">
                        <td colspan="6" class="px-4 py-16 text-center">
                            <svg class="mx-auto h-10 w-10 text-muted-foreground/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                            <p class="mt-3 text-sm font-semibold text-foreground">No estimates yet</p>
                            <p class="mt-1 text-sm text-muted-foreground">Create your first estimate to get started.</p>
                            <Link href="/owner/estimates/create" class="mt-4 inline-flex items-center rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">+ New Estimate</Link>
                        </td>
                    </tr>
                    <tr
                        v-for="estimate in estimates.data"
                        :key="estimate.id"
                        class="cursor-pointer hover:bg-accent"
                        @click="router.visit(`/owner/estimates/${estimate.id}`)"
                    >
                        <td class="px-4 py-3 font-mono text-xs text-muted-foreground">{{ estimate.estimate_number ?? '—' }}</td>
                        <td class="px-4 py-3 font-medium text-foreground">{{ estimate.title }}</td>
                        <td class="px-4 py-3 text-muted-foreground">
                            <template v-if="estimate.customer">
                                {{ estimate.customer.first_name }} {{ estimate.customer.last_name }}
                            </template>
                            <span v-else class="text-muted-foreground">—</span>
                        </td>
                        <td class="px-4 py-3">
                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="STATUS_CLASSES[estimate.status] ?? 'bg-muted text-muted-foreground'"
                            >
                                {{ statuses[estimate.status] ?? estimate.status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-muted-foreground">{{ formatDate(estimate.expires_at) }}</td>
                        <td class="px-4 py-3 text-muted-foreground">{{ formatDate(estimate.created_at) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="estimates.links.length > 3" class="mt-4 flex justify-center gap-1">
            <template v-for="link in estimates.links" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="rounded px-3 py-1 text-sm"
                    :class="link.active ? 'bg-slate-800 text-white' : 'text-muted-foreground hover:bg-accent'"
                    v-html="link.label"
                />
                <span v-else class="rounded px-3 py-1 text-sm text-muted-foreground" v-html="link.label" />
            </template>
        </div>
    </OwnerLayout>
</template>

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
    draft:    'bg-slate-100 text-slate-600',
    sent:     'bg-blue-100 text-blue-700',
    accepted: 'bg-green-100 text-green-700',
    declined: 'bg-red-100 text-red-600',
    expired:  'bg-amber-100 text-amber-700',
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
                <h2 class="text-xl font-semibold text-slate-800">Estimates</h2>
                <p class="mt-0.5 text-sm text-slate-500">{{ estimates.total }} total</p>
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
            <input
                v-model="search"
                type="search"
                placeholder="Search estimates…"
                class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-slate-500 focus:outline-none"
            />
            <select
                v-model="status"
                class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-slate-500 focus:outline-none"
            >
                <option value="">All statuses</option>
                <option v-for="(label, key) in statuses" :key="key" :value="key">{{ label }}</option>
            </select>
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Number</th>
                        <th class="px-4 py-3 text-left">Title</th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Expires</th>
                        <th class="px-4 py-3 text-left">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-if="estimates.data.length === 0">
                        <td colspan="6" class="px-4 py-8 text-center text-slate-400">No estimates found.</td>
                    </tr>
                    <tr
                        v-for="estimate in estimates.data"
                        :key="estimate.id"
                        class="cursor-pointer hover:bg-slate-50"
                        @click="router.visit(`/owner/estimates/${estimate.id}`)"
                    >
                        <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ estimate.estimate_number ?? '—' }}</td>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ estimate.title }}</td>
                        <td class="px-4 py-3 text-slate-600">
                            <template v-if="estimate.customer">
                                {{ estimate.customer.first_name }} {{ estimate.customer.last_name }}
                            </template>
                            <span v-else class="text-slate-400">—</span>
                        </td>
                        <td class="px-4 py-3">
                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="STATUS_CLASSES[estimate.status] ?? 'bg-slate-100 text-slate-600'"
                            >
                                {{ statuses[estimate.status] ?? estimate.status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ formatDate(estimate.expires_at) }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ formatDate(estimate.created_at) }}</td>
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
                    :class="link.active ? 'bg-slate-800 text-white' : 'text-slate-600 hover:bg-slate-100'"
                    v-html="link.label"
                />
                <span v-else class="rounded px-3 py-1 text-sm text-slate-400" v-html="link.label" />
            </template>
        </div>
    </OwnerLayout>
</template>

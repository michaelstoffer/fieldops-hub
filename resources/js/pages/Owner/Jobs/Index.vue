<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface Customer { id: number; first_name: string; last_name: string }
interface JobType  { id: number; name: string; color: string }
interface Job {
    id: number;
    title: string;
    status: string;
    scheduled_at: string | null;
    customer: Customer | null;
    job_type: JobType | null;
}

interface PaginatedJobs {
    data: Job[];
    links: { url: string | null; label: string; active: boolean }[];
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    jobs: PaginatedJobs;
    filters: { search?: string; status?: string };
    statuses: Record<string, string>;
}>();

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');

const STATUS_CLASSES: Record<string, string> = {
    scheduled:   'bg-blue-100 text-blue-700',
    en_route:    'bg-purple-100 text-purple-700',
    in_progress: 'bg-amber-100 text-amber-700',
    completed:   'bg-green-100 text-green-700',
    cancelled:   'bg-slate-100 text-slate-500',
    on_hold:     'bg-orange-100 text-orange-700',
};

let searchTimeout: ReturnType<typeof setTimeout>;

function applyFilters() {
    router.get(
        '/owner/jobs',
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
    <OwnerLayout title="Jobs">
        <Head title="Jobs" />

        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Jobs</h2>
                <p class="mt-0.5 text-sm text-slate-500">{{ jobs.total }} total</p>
            </div>
            <Link
                href="/owner/jobs/create"
                class="inline-flex items-center gap-2 rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700"
            >
                + New Job
            </Link>
        </div>

        <!-- Filters -->
        <div class="mb-4 flex flex-wrap gap-3">
            <input
                v-model="search"
                type="search"
                placeholder="Search jobs or customers…"
                class="w-full max-w-xs rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none"
            />
            <select
                v-model="status"
                class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none"
            >
                <option value="">All statuses</option>
                <option v-for="(label, key) in statuses" :key="key" :value="key">{{ label }}</option>
            </select>
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-xl bg-white shadow">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Title</th>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Customer</th>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Scheduled</th>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Status</th>
                        <th class="relative px-5 py-3"><span class="sr-only">View</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-if="jobs.data.length === 0">
                        <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">No jobs found.</td>
                    </tr>
                    <tr v-for="job in jobs.data" :key="job.id" class="hover:bg-slate-50">
                        <td class="px-5 py-3 text-sm font-medium text-slate-800">
                            <Link :href="`/owner/jobs/${job.id}`" class="hover:underline">{{ job.title }}</Link>
                        </td>
                        <td class="px-5 py-3 text-sm text-slate-600">
                            <Link v-if="job.customer" :href="`/owner/customers/${job.customer.id}`" class="hover:underline">
                                {{ job.customer.last_name }}, {{ job.customer.first_name }}
                            </Link>
                            <span v-else class="text-slate-400">—</span>
                        </td>
                        <td class="px-5 py-3 text-sm text-slate-600">{{ formatDate(job.scheduled_at) }}</td>
                        <td class="px-5 py-3 text-sm">
                            <span
                                class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="STATUS_CLASSES[job.status] ?? 'bg-slate-100 text-slate-600'"
                            >
                                {{ statuses[job.status] ?? job.status }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right text-sm">
                            <Link :href="`/owner/jobs/${job.id}`" class="font-medium text-slate-500 hover:text-slate-800">View →</Link>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="jobs.total > 25" class="flex items-center justify-between border-t border-slate-100 px-5 py-3">
                <p class="text-xs text-slate-500">Showing {{ jobs.from }}–{{ jobs.to }} of {{ jobs.total }}</p>
                <div class="flex gap-1">
                    <template v-for="link in jobs.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            preserve-state
                            class="rounded px-2 py-1 text-xs"
                            :class="link.active ? 'bg-slate-800 text-white' : 'text-slate-600 hover:bg-slate-100'"
                            v-html="link.label"
                        />
                        <span v-else class="rounded px-2 py-1 text-xs text-slate-300" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>
    </OwnerLayout>
</template>

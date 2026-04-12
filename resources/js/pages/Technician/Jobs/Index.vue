<script setup lang="ts">
import TechnicianLayout from '@/layouts/TechnicianLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Job {
    id: number;
    title: string;
    status: string;
    scheduled_at: string | null;
    customer: { id: number; first_name: string; last_name: string } | null;
    property: { id: number; address_line1: string; city: string; state: string } | null;
    job_type: { id: number; name: string; color: string } | null;
}

defineProps<{
    jobs: Job[];
    statuses: Record<string, string>;
}>();

const STATUS_CLASSES: Record<string, string> = {
    scheduled:   'bg-blue-100 text-blue-700',
    en_route:    'bg-purple-100 text-purple-700',
    in_progress: 'bg-amber-100 text-amber-700',
    completed:   'bg-green-100 text-green-700',
    cancelled:   'bg-slate-100 text-slate-500',
    on_hold:     'bg-orange-100 text-orange-700',
};

function formatTime(dt: string | null): string {
    if (!dt) return '—';
    return new Date(dt).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
}
</script>

<template>
    <TechnicianLayout title="Today's Jobs">
        <Head title="Today's Jobs" />

        <div class="p-4">
            <div v-if="jobs.length === 0" class="rounded-xl bg-white py-12 text-center shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">No jobs scheduled for today.</p>
            </div>

            <ul v-else class="space-y-3">
                <li v-for="job in jobs" :key="job.id">
                    <Link
                        :href="`/technician/jobs/${job.id}`"
                        class="block rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200 active:ring-slate-400"
                    >
                        <div class="mb-2 flex items-start justify-between gap-2">
                            <p class="font-semibold text-slate-900">{{ job.title }}</p>
                            <span
                                class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="STATUS_CLASSES[job.status] ?? 'bg-slate-100 text-slate-600'"
                            >
                                {{ statuses[job.status] ?? job.status }}
                            </span>
                        </div>

                        <div class="space-y-1 text-sm text-slate-500">
                            <p v-if="job.customer">
                                {{ job.customer.first_name }} {{ job.customer.last_name }}
                            </p>
                            <p v-if="job.property">
                                {{ job.property.address_line1 }}, {{ job.property.city }}, {{ job.property.state }}
                            </p>
                            <div class="flex items-center justify-between pt-1">
                                <span v-if="job.job_type" class="inline-flex items-center gap-1.5">
                                    <span class="h-2 w-2 rounded-full" :style="{ background: job.job_type.color }" />
                                    {{ job.job_type.name }}
                                </span>
                                <span class="ml-auto text-xs">{{ formatTime(job.scheduled_at) }}</span>
                            </div>
                        </div>
                    </Link>
                </li>
            </ul>
        </div>
    </TechnicianLayout>
</template>

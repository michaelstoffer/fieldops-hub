<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

interface Job {
    id: number;
    title: string;
    description: string | null;
    status: string;
    scheduled_at: string | null;
    started_at: string | null;
    completed_at: string | null;
    cancelled_at: string | null;
    office_notes: string | null;
    technician_notes: string | null;
    customer: { id: number; first_name: string; last_name: string } | null;
    property: { id: number; address_line1: string; city: string; state: string; postal_code: string } | null;
    job_type: { id: number; name: string; color: string } | null;
    assigned_technician: { id: number; name: string } | null;
}

const props = defineProps<{
    job: Job;
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

const statusForm = useForm({ status: props.job.status });

function changeStatus(newStatus: string) {
    statusForm.status = newStatus;
    statusForm.patch(`/owner/jobs/${props.job.id}/status`);
}

function formatDate(dt: string | null): string {
    if (!dt) return '—';
    return new Date(dt).toLocaleString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric',
        hour: 'numeric', minute: '2-digit',
    });
}

function cancelJob() {
    if (confirm('Cancel this job?')) {
        router.delete(`/owner/jobs/${props.job.id}`);
    }
}
</script>

<template>
    <OwnerLayout :title="job.title">
        <Head :title="job.title" />

        <!-- Breadcrumb -->
        <nav class="mb-4 text-sm text-slate-500">
            <Link href="/owner/jobs" class="hover:underline">Jobs</Link>
            <span class="mx-1">›</span>
            <span class="text-slate-800">{{ job.title }}</span>
        </nav>

        <!-- Header -->
        <div class="mb-6 flex items-start justify-between">
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-semibold text-slate-800">{{ job.title }}</h2>
                <span
                    class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium"
                    :class="STATUS_CLASSES[job.status] ?? 'bg-slate-100 text-slate-600'"
                >
                    {{ statuses[job.status] ?? job.status }}
                </span>
            </div>
            <div class="flex gap-2">
                <Link
                    :href="`/owner/jobs/${job.id}/edit`"
                    class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                >
                    Edit
                </Link>
                <button
                    type="button"
                    class="inline-flex items-center rounded-lg border border-red-200 bg-white px-4 py-2 text-sm font-medium text-red-600 shadow-sm hover:bg-red-50"
                    @click="cancelJob"
                >
                    Cancel Job
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left: details -->
            <div class="space-y-6 lg:col-span-1">
                <!-- Status actions -->
                <div class="rounded-xl bg-white shadow">
                    <div class="border-b border-slate-100 px-5 py-3">
                        <h3 class="text-sm font-semibold text-slate-700">Update Status</h3>
                    </div>
                    <div class="flex flex-wrap gap-2 px-5 py-4">
                        <button
                            v-for="(label, key) in statuses"
                            :key="key"
                            type="button"
                            class="rounded-lg border px-3 py-1.5 text-xs font-medium transition"
                            :class="job.status === key
                                ? 'border-slate-800 bg-slate-800 text-white'
                                : 'border-slate-200 text-slate-600 hover:border-slate-400'"
                            :disabled="job.status === key"
                            @click="changeStatus(key)"
                        >
                            {{ label }}
                        </button>
                    </div>
                </div>

                <!-- Details -->
                <div class="rounded-xl bg-white shadow">
                    <div class="border-b border-slate-100 px-5 py-3">
                        <h3 class="text-sm font-semibold text-slate-700">Details</h3>
                    </div>
                    <dl class="divide-y divide-slate-100">
                        <div class="flex justify-between px-5 py-3 text-sm">
                            <dt class="text-slate-500">Customer</dt>
                            <dd class="font-medium text-slate-800">
                                <Link v-if="job.customer" :href="`/owner/customers/${job.customer.id}`" class="hover:underline">
                                    {{ job.customer.first_name }} {{ job.customer.last_name }}
                                </Link>
                                <span v-else class="text-slate-400">—</span>
                            </dd>
                        </div>
                        <div class="flex justify-between px-5 py-3 text-sm">
                            <dt class="text-slate-500">Property</dt>
                            <dd class="text-right font-medium text-slate-800">
                                <span v-if="job.property">
                                    {{ job.property.address_line1 }},
                                    {{ job.property.city }}, {{ job.property.state }}
                                </span>
                                <span v-else class="text-slate-400">—</span>
                            </dd>
                        </div>
                        <div class="flex justify-between px-5 py-3 text-sm">
                            <dt class="text-slate-500">Type</dt>
                            <dd class="font-medium text-slate-800">
                                <span v-if="job.job_type" class="inline-flex items-center gap-1.5">
                                    <span class="h-2.5 w-2.5 rounded-full" :style="{ background: job.job_type.color }" />
                                    {{ job.job_type.name }}
                                </span>
                                <span v-else class="text-slate-400">—</span>
                            </dd>
                        </div>
                        <div class="flex justify-between px-5 py-3 text-sm">
                            <dt class="text-slate-500">Assigned to</dt>
                            <dd class="font-medium text-slate-800">
                                {{ job.assigned_technician?.name ?? '—' }}
                            </dd>
                        </div>
                        <div class="flex justify-between px-5 py-3 text-sm">
                            <dt class="text-slate-500">Scheduled</dt>
                            <dd class="font-medium text-slate-800">{{ formatDate(job.scheduled_at) }}</dd>
                        </div>
                        <div v-if="job.started_at" class="flex justify-between px-5 py-3 text-sm">
                            <dt class="text-slate-500">Started</dt>
                            <dd class="font-medium text-slate-800">{{ formatDate(job.started_at) }}</dd>
                        </div>
                        <div v-if="job.completed_at" class="flex justify-between px-5 py-3 text-sm">
                            <dt class="text-slate-500">Completed</dt>
                            <dd class="font-medium text-green-700">{{ formatDate(job.completed_at) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Right: notes -->
            <div class="space-y-6 lg:col-span-2">
                <div v-if="job.description" class="rounded-xl bg-white shadow">
                    <div class="border-b border-slate-100 px-5 py-3">
                        <h3 class="text-sm font-semibold text-slate-700">Description</h3>
                    </div>
                    <p class="px-5 py-4 text-sm text-slate-600 whitespace-pre-wrap">{{ job.description }}</p>
                </div>

                <div class="rounded-xl bg-white shadow">
                    <div class="border-b border-slate-100 px-5 py-3">
                        <h3 class="text-sm font-semibold text-slate-700">Office Notes</h3>
                    </div>
                    <p v-if="job.office_notes" class="px-5 py-4 text-sm text-slate-600 whitespace-pre-wrap">{{ job.office_notes }}</p>
                    <p v-else class="px-5 py-4 text-sm text-slate-400">No office notes.</p>
                </div>

                <div v-if="job.technician_notes" class="rounded-xl bg-white shadow">
                    <div class="border-b border-slate-100 px-5 py-3">
                        <h3 class="text-sm font-semibold text-slate-700">Technician Notes</h3>
                    </div>
                    <p class="px-5 py-4 text-sm text-slate-600 whitespace-pre-wrap">{{ job.technician_notes }}</p>
                </div>
            </div>
        </div>
    </OwnerLayout>
</template>

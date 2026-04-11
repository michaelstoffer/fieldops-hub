<script setup lang="ts">
import TechnicianLayout from '@/layouts/TechnicianLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Job {
    id: number;
    title: string;
    description: string | null;
    status: string;
    scheduled_at: string | null;
    started_at: string | null;
    completed_at: string | null;
    office_notes: string | null;
    technician_notes: string | null;
    customer: { id: number; first_name: string; last_name: string; phone?: string } | null;
    property: { id: number; address_line1: string; city: string; state: string; postal_code: string } | null;
    job_type: { id: number; name: string; color: string } | null;
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

// Technicians cannot set cancelled status
const TECHNICIAN_STATUSES = Object.fromEntries(
    Object.entries(props.statuses).filter(([key]) => key !== 'cancelled')
);

const statusForm = useForm({ status: props.job.status });
const notesForm = useForm({ technician_notes: props.job.technician_notes ?? '' });
const editingNotes = ref(false);

function changeStatus(newStatus: string) {
    statusForm.status = newStatus;
    statusForm.patch(`/api/technician/jobs/${props.job.id}/status`, {
        onSuccess: () => statusForm.reset(),
    });
}

function saveNotes() {
    notesForm.patch(`/api/technician/jobs/${props.job.id}/notes`, {
        onSuccess: () => { editingNotes.value = false; },
    });
}

function formatDate(dt: string | null): string {
    if (!dt) return '—';
    return new Date(dt).toLocaleString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric',
        hour: 'numeric', minute: '2-digit',
    });
}
</script>

<template>
    <TechnicianLayout :title="job.title">
        <Head :title="job.title" />

        <div class="p-4 space-y-4">
            <!-- Breadcrumb -->
            <nav class="text-sm text-slate-500">
                <Link href="/technician/jobs" class="hover:underline">Jobs</Link>
                <span class="mx-1">›</span>
                <span class="text-slate-800">{{ job.title }}</span>
            </nav>

            <!-- Title + status -->
            <div class="flex items-start justify-between gap-2">
                <h2 class="text-xl font-bold text-slate-900">{{ job.title }}</h2>
                <span
                    class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-medium"
                    :class="STATUS_CLASSES[job.status] ?? 'bg-slate-100 text-slate-600'"
                >
                    {{ statuses[job.status] ?? job.status }}
                </span>
            </div>

            <!-- Update status -->
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="border-b border-slate-100 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-700">Update Status</h3>
                </div>
                <div class="flex flex-wrap gap-2 p-4">
                    <button
                        v-for="(label, key) in TECHNICIAN_STATUSES"
                        :key="key"
                        type="button"
                        class="rounded-lg border px-3 py-1.5 text-xs font-medium transition"
                        :class="job.status === key
                            ? 'border-slate-800 bg-slate-800 text-white'
                            : 'border-slate-200 text-slate-600 active:bg-slate-50'"
                        :disabled="job.status === key || statusForm.processing"
                        @click="changeStatus(key)"
                    >
                        {{ label }}
                    </button>
                </div>
            </div>

            <!-- Job details -->
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="border-b border-slate-100 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-700">Details</h3>
                </div>
                <dl class="divide-y divide-slate-100 text-sm">
                    <div v-if="job.customer" class="flex justify-between px-4 py-3">
                        <dt class="text-slate-500">Customer</dt>
                        <dd class="font-medium text-slate-800">
                            {{ job.customer.first_name }} {{ job.customer.last_name }}
                        </dd>
                    </div>
                    <div v-if="job.customer?.phone" class="flex justify-between px-4 py-3">
                        <dt class="text-slate-500">Phone</dt>
                        <dd class="font-medium text-slate-800">
                            <a :href="`tel:${job.customer.phone}`" class="text-blue-600">
                                {{ job.customer.phone }}
                            </a>
                        </dd>
                    </div>
                    <div v-if="job.property" class="flex justify-between gap-4 px-4 py-3">
                        <dt class="text-slate-500">Address</dt>
                        <dd class="text-right font-medium text-slate-800">
                            {{ job.property.address_line1 }},
                            {{ job.property.city }}, {{ job.property.state }}
                            {{ job.property.postal_code }}
                        </dd>
                    </div>
                    <div v-if="job.job_type" class="flex justify-between px-4 py-3">
                        <dt class="text-slate-500">Type</dt>
                        <dd class="flex items-center gap-1.5 font-medium text-slate-800">
                            <span class="h-2 w-2 rounded-full" :style="{ background: job.job_type.color }" />
                            {{ job.job_type.name }}
                        </dd>
                    </div>
                    <div class="flex justify-between px-4 py-3">
                        <dt class="text-slate-500">Scheduled</dt>
                        <dd class="font-medium text-slate-800">{{ formatDate(job.scheduled_at) }}</dd>
                    </div>
                    <div v-if="job.started_at" class="flex justify-between px-4 py-3">
                        <dt class="text-slate-500">Started</dt>
                        <dd class="font-medium text-slate-800">{{ formatDate(job.started_at) }}</dd>
                    </div>
                    <div v-if="job.completed_at" class="flex justify-between px-4 py-3">
                        <dt class="text-slate-500">Completed</dt>
                        <dd class="font-medium text-green-700">{{ formatDate(job.completed_at) }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Description -->
            <div v-if="job.description" class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="border-b border-slate-100 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-700">Description</h3>
                </div>
                <p class="whitespace-pre-wrap px-4 py-3 text-sm text-slate-600">{{ job.description }}</p>
            </div>

            <!-- Office notes (read-only) -->
            <div v-if="job.office_notes" class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="border-b border-slate-100 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-700">Office Notes</h3>
                </div>
                <p class="whitespace-pre-wrap px-4 py-3 text-sm text-slate-600">{{ job.office_notes }}</p>
            </div>

            <!-- Technician notes (editable) -->
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-700">My Notes</h3>
                    <button
                        v-if="!editingNotes"
                        type="button"
                        class="text-xs font-medium text-blue-600"
                        @click="editingNotes = true"
                    >
                        Edit
                    </button>
                </div>

                <div v-if="editingNotes" class="p-4">
                    <textarea
                        v-model="notesForm.technician_notes"
                        rows="4"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 focus:border-slate-500 focus:outline-none"
                        placeholder="Add your notes here…"
                    />
                    <div class="mt-2 flex justify-end gap-2">
                        <button
                            type="button"
                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600"
                            @click="editingNotes = false"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-medium text-white"
                            :disabled="notesForm.processing"
                            @click="saveNotes"
                        >
                            Save
                        </button>
                    </div>
                </div>
                <p v-else-if="notesForm.technician_notes" class="whitespace-pre-wrap px-4 py-3 text-sm text-slate-600">
                    {{ notesForm.technician_notes }}
                </p>
                <p v-else class="px-4 py-3 text-sm text-slate-400">No notes yet. Tap Edit to add.</p>
            </div>
        </div>
    </TechnicianLayout>
</template>

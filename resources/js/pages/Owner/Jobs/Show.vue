<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Invoice {
    id: number;
    invoice_number: string | null;
    status: string;
    total: string;
    balance_due: string;
}

interface JobMessage {
    id: number;
    channel: string;
    event: string;
    recipient: string;
    body: string;
    status: string;
    error: string | null;
    created_at: string;
}

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
    invoice: Invoice | null;
    messages: JobMessage[];
}

const props = defineProps<{
    job: Job;
    statuses: Record<string, string>;
}>();

const EVENT_LABELS: Record<string, string> = {
    job_scheduled: 'Job Scheduled',
    job_reminder:  'Job Reminder',
    en_route:      'Technician En Route',
    job_completed: 'Job Completed',
};


const STATUS_CLASSES: Record<string, string> = {
    scheduled:   'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
    en_route:    'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400',
    in_progress: 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400',
    completed:   'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400',
    cancelled:   'bg-muted text-muted-foreground',
    on_hold:     'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-400',
};

const statusForm = useForm({ status: props.job.status });
const generateInvoiceForm = useForm({});

function generateInvoice() {
    if (!confirm('Generate an invoice from this job\'s line items?')) return;
    generateInvoiceForm.post(`/owner/jobs/${props.job.id}/invoice`);
}

function formatCurrency(val: string | number): string {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(val));
}

function changeStatus(newStatus: string) {
    statusForm.status = newStatus;
    statusForm.patch(`/owner/jobs/${props.job.id}/status`);
}

function formatDate(dt: string | null): string {
    if (!dt) return '—';
    // The server returns wall-clock time without a TZ suffix (e.g. "2026-05-27 14:00:00").
    // Appending a space and replacing with 'T' keeps it as a local-time literal so the
    // browser doesn't shift it by the UTC offset.
    const localStr = dt.replace(' ', 'T').replace(/(\.\d+)?$/, '');
    const d = new Date(localStr);
    return d.toLocaleString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric',
        hour: 'numeric', minute: '2-digit',
    });
}

function cancelJob() {
    if (confirm('Cancel this job?')) {
        router.delete(`/owner/jobs/${props.job.id}`);
    }
}

const NEXT_STEP: Record<string, { status: string; label: string }> = {
    scheduled:   { status: 'en_route',    label: 'Mark En Route' },
    en_route:    { status: 'in_progress', label: 'Mark In Progress' },
    in_progress: { status: 'completed',   label: 'Mark Completed' },
    on_hold:     { status: 'scheduled',   label: 'Resume Job' },
};

const nextStep = computed(() => NEXT_STEP[props.job.status] ?? null);

function advanceStatus() {
    if (!nextStep.value) return;
    statusForm.status = nextStep.value.status;
    statusForm.patch(`/owner/jobs/${props.job.id}/status`);
}
</script>

<template>
    <OwnerLayout :title="job.title">
        <Head :title="job.title" />

        <!-- Breadcrumb -->
        <nav class="mb-4 text-sm text-muted-foreground">
            <Link href="/owner/jobs" class="hover:underline">Jobs</Link>
            <span class="mx-1">›</span>
            <span class="text-foreground">{{ job.title }}</span>
        </nav>

        <!-- Header -->
        <div class="mb-6 flex items-start justify-between">
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-semibold text-foreground">{{ job.title }}</h2>
                <span
                    class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium"
                    :class="STATUS_CLASSES[job.status] ?? 'bg-muted text-muted-foreground'"
                >
                    {{ statuses[job.status] ?? job.status }}
                </span>
            </div>
            <div class="flex gap-2">
                <button
                    v-if="nextStep"
                    type="button"
                    class="inline-flex items-center rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                    :disabled="statusForm.processing"
                    @click="advanceStatus"
                >
                    {{ nextStep.label }}
                </button>
                <Link
                    :href="`/owner/jobs/${job.id}/edit`"
                    class="inline-flex items-center rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium text-foreground shadow-sm hover:bg-accent"
                >
                    Edit
                </Link>
                <button
                    type="button"
                    class="inline-flex items-center rounded-lg border border-red-200 bg-card px-4 py-2 text-sm font-medium text-red-600 shadow-sm hover:bg-red-50"
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
                <div class="rounded-xl bg-card shadow ring-1 ring-border">
                    <div class="border-b border-border px-5 py-3">
                        <h3 class="text-sm font-semibold text-foreground">Update Status</h3>
                    </div>
                    <div class="flex flex-wrap gap-2 px-5 py-4">
                        <button
                            v-for="(label, key) in statuses"
                            :key="key"
                            type="button"
                            class="rounded-lg border px-3 py-1.5 text-xs font-medium transition"
                            :class="job.status === key
                                ? 'border-slate-800 bg-slate-800 text-white'
                                : 'border-border text-muted-foreground hover:border-slate-400'"
                            :disabled="job.status === key"
                            @click="changeStatus(key)"
                        >
                            {{ label }}
                        </button>
                    </div>
                </div>

                <!-- Details -->
                <div class="rounded-xl bg-card shadow ring-1 ring-border">
                    <div class="border-b border-border px-5 py-3">
                        <h3 class="text-sm font-semibold text-foreground">Details</h3>
                    </div>
                    <dl class="divide-y divide-border">
                        <div class="flex justify-between px-5 py-3 text-sm">
                            <dt class="text-muted-foreground">Customer</dt>
                            <dd class="font-medium text-foreground">
                                <Link v-if="job.customer" :href="`/owner/customers/${job.customer.id}`" class="hover:underline">
                                    {{ job.customer.first_name }} {{ job.customer.last_name }}
                                </Link>
                                <span v-else class="text-muted-foreground">—</span>
                            </dd>
                        </div>
                        <div class="flex justify-between px-5 py-3 text-sm">
                            <dt class="text-muted-foreground">Property</dt>
                            <dd class="text-right font-medium text-foreground">
                                <span v-if="job.property">
                                    {{ job.property.address_line1 }},
                                    {{ job.property.city }}, {{ job.property.state }}
                                </span>
                                <span v-else class="text-muted-foreground">—</span>
                            </dd>
                        </div>
                        <div class="flex justify-between px-5 py-3 text-sm">
                            <dt class="text-muted-foreground">Type</dt>
                            <dd class="font-medium text-foreground">
                                <span v-if="job.job_type" class="inline-flex items-center gap-1.5">
                                    <span class="h-2.5 w-2.5 rounded-full" :style="{ background: job.job_type.color }" />
                                    {{ job.job_type.name }}
                                </span>
                                <span v-else class="text-muted-foreground">—</span>
                            </dd>
                        </div>
                        <div class="flex justify-between px-5 py-3 text-sm">
                            <dt class="text-muted-foreground">Assigned to</dt>
                            <dd class="font-medium text-foreground">
                                {{ job.assigned_technician?.name ?? '—' }}
                            </dd>
                        </div>
                        <div class="flex justify-between px-5 py-3 text-sm">
                            <dt class="text-muted-foreground">Scheduled</dt>
                            <dd class="font-medium text-foreground">{{ formatDate(job.scheduled_at) }}</dd>
                        </div>
                        <div v-if="job.started_at" class="flex justify-between px-5 py-3 text-sm">
                            <dt class="text-muted-foreground">Started</dt>
                            <dd class="font-medium text-foreground">{{ formatDate(job.started_at) }}</dd>
                        </div>
                        <div v-if="job.completed_at" class="flex justify-between px-5 py-3 text-sm">
                            <dt class="text-muted-foreground">Completed</dt>
                            <dd class="font-medium text-green-600 dark:text-green-400">{{ formatDate(job.completed_at) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Right: notes -->
            <div class="space-y-6 lg:col-span-2">
                <div v-if="job.description" class="rounded-xl bg-card shadow ring-1 ring-border">
                    <div class="border-b border-border px-5 py-3">
                        <h3 class="text-sm font-semibold text-foreground">Description</h3>
                    </div>
                    <p class="px-5 py-4 text-sm text-muted-foreground whitespace-pre-wrap">{{ job.description }}</p>
                </div>

                <div class="rounded-xl bg-card shadow ring-1 ring-border">
                    <div class="border-b border-border px-5 py-3">
                        <h3 class="text-sm font-semibold text-foreground">Office Notes</h3>
                    </div>
                    <p v-if="job.office_notes" class="px-5 py-4 text-sm text-muted-foreground whitespace-pre-wrap">{{ job.office_notes }}</p>
                    <p v-else class="px-5 py-4 text-sm text-muted-foreground">No office notes.</p>
                </div>

                <div v-if="job.technician_notes" class="rounded-xl bg-card shadow ring-1 ring-border">
                    <div class="border-b border-border px-5 py-3">
                        <h3 class="text-sm font-semibold text-foreground">Technician Notes</h3>
                    </div>
                    <p class="px-5 py-4 text-sm text-muted-foreground whitespace-pre-wrap">{{ job.technician_notes }}</p>
                </div>

                <!-- Message log (#93) -->
                <div class="rounded-xl bg-card shadow ring-1 ring-border">
                    <div class="border-b border-border px-5 py-3">
                        <h3 class="text-sm font-semibold text-foreground">Message Log</h3>
                    </div>
                    <div v-if="job.messages && job.messages.length > 0" class="divide-y divide-border/50">
                        <div v-for="msg in job.messages" :key="msg.id" class="px-5 py-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="shrink-0 rounded-full px-2 py-0.5 text-xs font-medium"
                                            :class="msg.channel === 'email'
                                                ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400'
                                                : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400'"
                                        >
                                            {{ msg.channel === 'email' ? 'Email' : 'SMS' }}
                                        </span>
                                        <span class="text-xs font-medium text-foreground">
                                            {{ EVENT_LABELS[msg.event] ?? msg.event }}
                                        </span>
                                        <span
                                            v-if="msg.status === 'failed'"
                                            class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-600 dark:bg-red-900/40 dark:text-red-400"
                                        >
                                            Failed
                                        </span>
                                    </div>
                                    <p class="mt-1 truncate text-xs text-muted-foreground">To: {{ msg.recipient }}</p>
                                    <p v-if="msg.error" class="mt-0.5 text-xs text-destructive">{{ msg.error }}</p>
                                </div>
                                <span class="shrink-0 text-xs text-muted-foreground">{{ formatDate(msg.created_at) }}</span>
                            </div>
                        </div>
                    </div>
                    <p v-else class="px-5 py-4 text-sm text-muted-foreground">No messages sent yet.</p>
                </div>

                <!-- Invoice -->
                <div class="rounded-xl bg-card shadow ring-1 ring-border">
                    <div class="border-b border-border px-5 py-3">
                        <h3 class="text-sm font-semibold text-foreground">Invoice</h3>
                    </div>
                    <div class="px-5 py-4">
                        <div v-if="job.invoice" class="flex items-center justify-between">
                            <div>
                                <p class="font-mono text-sm font-medium text-foreground">{{ job.invoice.invoice_number ?? '—' }}</p>
                                <p class="mt-0.5 text-xs text-muted-foreground">
                                    {{ job.invoice.status.charAt(0).toUpperCase() + job.invoice.status.slice(1) }}
                                    · Total {{ formatCurrency(job.invoice.total) }}
                                    · Due {{ formatCurrency(job.invoice.balance_due) }}
                                </p>
                            </div>
                            <Link
                                :href="`/owner/invoices/${job.invoice.id}`"
                                class="rounded-lg border border-border px-3 py-1.5 text-sm font-medium text-foreground hover:bg-accent"
                            >
                                View Invoice
                            </Link>
                        </div>
                        <div v-else-if="job.status === 'completed'" class="flex items-center justify-between">
                            <p class="text-sm text-muted-foreground">No invoice generated yet.</p>
                            <button
                                type="button"
                                class="rounded-lg bg-slate-800 px-3 py-1.5 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                                :disabled="generateInvoiceForm.processing"
                                @click="generateInvoice"
                            >
                                Generate Invoice
                            </button>
                        </div>
                        <p v-else class="text-sm text-muted-foreground">Available once job is completed.</p>
                    </div>
                </div>
            </div>
        </div>
    </OwnerLayout>
</template>

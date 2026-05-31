<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface LineItem {
    id: number;
    name: string;
    description: string | null;
    unit_price: string;
    quantity: string;
    total: string;
    is_taxable: boolean;
}

interface Package {
    id: number;
    tier: string;
    label: string;
    description: string | null;
    subtotal: string;
    tax_amount: string;
    total: string;
    is_recommended: boolean;
    line_items: LineItem[];
}

interface Estimate {
    id: number;
    estimate_number: string | null;
    title: string;
    intro: string | null;
    footer: string | null;
    status: string;
    token: string;
    expires_at: string | null;
    sent_at: string | null;
    accepted_at: string | null;
    accepted_package: string | null;
    declined_at: string | null;
    tax_rate: string;
    customer: { id: number; first_name: string; last_name: string; email?: string } | null;
    packages: Package[];
    converted_job: { id: number; title: string } | null;
}

const props = defineProps<{
    estimate: Estimate;
    statuses: Record<string, string>;
}>();

const STATUS_CLASSES: Record<string, string> = {
    draft:    'bg-muted text-muted-foreground',
    sent:     'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
    accepted: 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400',
    declined: 'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-400',
    expired:  'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400',
};

const TIER_LABELS: Record<string, string> = { good: 'Good', better: 'Better', best: 'Best' };

const sendForm = useForm({});
const convertForm = useForm({});
const showSendModal = ref(false);

function confirmSend() {
    sendForm.post(`/owner/estimates/${props.estimate.id}/send`, {
        onSuccess: () => { showSendModal.value = false; },
    });
}

function convertToJob() {
    if (!confirm('Convert this estimate to a job?')) return;
    convertForm.post(`/owner/estimates/${props.estimate.id}/convert`);
}

function deleteEstimate() {
    if (!confirm('Delete this estimate? This cannot be undone.')) return;
    router.delete(`/owner/estimates/${props.estimate.id}`);
}

const publicUrl = computed(() => {
    const origin = typeof window !== 'undefined' ? window.location.origin : '';
    return `${origin}/estimates/${props.estimate.token}`;
});

function copyLink() {
    navigator.clipboard.writeText(publicUrl.value);
}

function formatDate(dt: string | null): string {
    if (!dt) return '—';
    return new Date(dt).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function formatCurrency(val: string | number): string {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(val));
}
</script>

<template>
    <OwnerLayout :title="estimate.title">
        <Head :title="estimate.title" />

        <!-- Breadcrumb -->
        <nav class="mb-4 text-sm text-muted-foreground">
            <Link href="/owner/estimates" class="hover:underline">Estimates</Link>
            <span class="mx-1">›</span>
            <span class="text-foreground">{{ estimate.estimate_number ?? estimate.title }}</span>
        </nav>

        <!-- Header -->
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-semibold text-foreground">{{ estimate.title }}</h2>
                    <span
                        class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                        :class="STATUS_CLASSES[estimate.status] ?? 'bg-muted text-muted-foreground'"
                    >
                        {{ statuses[estimate.status] ?? estimate.status }}
                    </span>
                </div>
                <p v-if="estimate.customer" class="mt-1 text-sm text-muted-foreground">
                    {{ estimate.customer.first_name }} {{ estimate.customer.last_name }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <Link
                    v-if="estimate.status === 'draft'"
                    :href="`/owner/estimates/${estimate.id}/edit`"
                    class="rounded-lg border border-border px-3 py-1.5 text-sm font-medium text-foreground hover:bg-accent"
                >
                    Edit
                </Link>
                <button
                    v-if="estimate.status === 'draft'"
                    type="button"
                    class="rounded-lg bg-slate-800 px-3 py-1.5 text-sm font-medium text-white hover:bg-slate-700"
                    @click="showSendModal = true"
                >
                    Send to Customer
                </button>
                <button
                    v-if="estimate.status === 'accepted' && !estimate.converted_job"
                    type="button"
                    class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 disabled:opacity-50"
                    :disabled="convertForm.processing"
                    @click="convertToJob"
                >
                    Convert to Job
                </button>
                <Link
                    v-if="estimate.status === 'accepted' && estimate.converted_job"
                    :href="`/owner/jobs/${estimate.converted_job.id}`"
                    class="rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-sm font-medium text-green-700 hover:bg-green-100"
                >
                    View Job →
                </Link>
                <button
                    type="button"
                    class="rounded-lg border border-border px-3 py-1.5 text-sm font-medium text-muted-foreground hover:bg-red-50 hover:text-red-600"
                    @click="deleteEstimate"
                >
                    Delete
                </button>
            </div>
        </div>

        <!-- Public link -->
        <div v-if="['sent','accepted','declined'].includes(estimate.status)" class="mb-6 flex items-center gap-3 rounded-xl bg-blue-50 px-4 py-3 ring-1 ring-blue-100">
            <span class="text-sm text-blue-700">Public link:</span>
            <a :href="publicUrl" target="_blank" class="flex-1 truncate text-sm font-medium text-blue-600 hover:underline">{{ publicUrl }}</a>
            <button type="button" class="text-xs font-medium text-blue-600 hover:underline" @click="copyLink">Copy</button>
        </div>

        <!-- Meta -->
        <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="rounded-xl bg-card p-4 shadow-sm ring-1 ring-border">
                <p class="text-xs text-muted-foreground">Number</p>
                <p class="mt-1 font-mono text-sm font-medium text-foreground">{{ estimate.estimate_number ?? '—' }}</p>
            </div>
            <div class="rounded-xl bg-card p-4 shadow-sm ring-1 ring-border">
                <p class="text-xs text-muted-foreground">Expires</p>
                <p class="mt-1 text-sm font-medium text-foreground">{{ formatDate(estimate.expires_at) }}</p>
            </div>
            <div class="rounded-xl bg-card p-4 shadow-sm ring-1 ring-border">
                <p class="text-xs text-muted-foreground">Sent</p>
                <p class="mt-1 text-sm font-medium text-foreground">{{ formatDate(estimate.sent_at) }}</p>
            </div>
            <div v-if="estimate.accepted_at" class="rounded-xl bg-card p-4 shadow-sm ring-1 ring-green-200">
                <p class="text-xs text-green-500">Accepted</p>
                <p class="mt-1 text-sm font-medium text-green-700">
                    {{ formatDate(estimate.accepted_at) }} · {{ TIER_LABELS[estimate.accepted_package ?? ''] ?? estimate.accepted_package }}
                </p>
            </div>
        </div>

        <!-- Intro -->
        <div v-if="estimate.intro" class="mb-4 rounded-xl bg-card p-4 shadow-sm ring-1 ring-border">
            <p class="whitespace-pre-wrap text-sm text-muted-foreground">{{ estimate.intro }}</p>
        </div>

        <!-- Packages -->
        <div class="space-y-4">
            <div
                v-for="pkg in estimate.packages"
                :key="pkg.id"
                class="rounded-xl bg-card shadow-sm ring-1"
                :class="pkg.is_recommended ? 'ring-blue-400' : 'ring-border'"
            >
                <!-- Package header -->
                <div
                    class="flex items-center justify-between rounded-t-xl px-4 py-3"
                    :class="pkg.is_recommended ? 'bg-blue-50' : 'bg-background'"
                >
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">{{ TIER_LABELS[pkg.tier] ?? pkg.tier }}</span>
                        <span class="font-semibold text-foreground">{{ pkg.label }}</span>
                        <span v-if="pkg.is_recommended" class="rounded-full bg-blue-600 px-2 py-0.5 text-xs font-medium text-white">Recommended</span>
                        <span v-if="estimate.accepted_package === pkg.tier" class="rounded-full bg-green-600 px-2 py-0.5 text-xs font-medium text-white">Accepted</span>
                    </div>
                    <span class="text-lg font-bold text-foreground">{{ formatCurrency(pkg.total) }}</span>
                </div>

                <!-- Description -->
                <p v-if="pkg.description" class="border-b border-border px-4 py-2 text-sm text-muted-foreground">{{ pkg.description }}</p>

                <!-- Line items -->
                <table class="min-w-full divide-y divide-border text-sm">
                    <thead>
                        <tr class="text-xs text-muted-foreground">
                            <th class="px-4 py-2 text-left font-medium">Item</th>
                            <th class="px-4 py-2 text-right font-medium">Qty</th>
                            <th class="px-4 py-2 text-right font-medium">Unit Price</th>
                            <th class="px-4 py-2 text-right font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        <tr v-for="li in pkg.line_items" :key="li.id">
                            <td class="px-4 py-2.5 text-foreground">
                                {{ li.name }}
                                <span v-if="!li.is_taxable" class="ml-1 text-xs text-muted-foreground">(non-taxable)</span>
                            </td>
                            <td class="px-4 py-2.5 text-right text-muted-foreground">{{ li.quantity }}</td>
                            <td class="px-4 py-2.5 text-right text-muted-foreground">{{ formatCurrency(li.unit_price) }}</td>
                            <td class="px-4 py-2.5 text-right font-medium text-foreground">{{ formatCurrency(li.total) }}</td>
                        </tr>
                        <tr v-if="pkg.line_items.length === 0">
                            <td colspan="4" class="px-4 py-3 text-center text-xs text-muted-foreground">No line items.</td>
                        </tr>
                    </tbody>
                    <tfoot class="border-t border-border bg-background text-xs text-muted-foreground">
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right">Subtotal</td>
                            <td class="px-4 py-2 text-right font-medium">{{ formatCurrency(pkg.subtotal) }}</td>
                        </tr>
                        <tr v-if="Number(pkg.tax_amount) > 0">
                            <td colspan="3" class="px-4 py-2 text-right">Tax ({{ (Number(estimate.tax_rate) * 100).toFixed(2) }}%)</td>
                            <td class="px-4 py-2 text-right font-medium">{{ formatCurrency(pkg.tax_amount) }}</td>
                        </tr>
                        <tr class="text-foreground">
                            <td colspan="3" class="px-4 py-2 text-right font-semibold">Total</td>
                            <td class="px-4 py-2 text-right font-bold">{{ formatCurrency(pkg.total) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div v-if="estimate.footer" class="mt-4 rounded-xl bg-card p-4 shadow-sm ring-1 ring-border">
            <p class="whitespace-pre-wrap text-sm text-muted-foreground">{{ estimate.footer }}</p>
        </div>

        <!-- Send confirmation modal -->
        <div v-if="showSendModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
            <div class="w-full max-w-md rounded-xl bg-card p-6 shadow-xl">
                <h3 class="text-base font-semibold text-foreground">Send Estimate to Customer?</h3>
                <p class="mt-1 text-sm text-muted-foreground">This will send the estimate link to the customer by email.</p>
                <dl class="mt-4 space-y-2 rounded-lg bg-background px-4 py-3 text-sm">
                    <div class="flex gap-2">
                        <dt class="w-16 shrink-0 font-medium text-muted-foreground">To:</dt>
                        <dd class="text-foreground">
                            {{ estimate.customer ? `${estimate.customer.first_name} ${estimate.customer.last_name}` : '—' }}
                            <span v-if="estimate.customer?.email" class="text-muted-foreground">({{ estimate.customer.email }})</span>
                        </dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="w-16 shrink-0 font-medium text-muted-foreground">Link:</dt>
                        <dd class="truncate text-blue-600">{{ publicUrl }}</dd>
                    </div>
                </dl>
                <div class="mt-5 flex justify-end gap-2">
                    <button
                        type="button"
                        class="rounded-lg border border-border px-4 py-2 text-sm font-medium text-muted-foreground hover:bg-accent"
                        @click="showSendModal = false"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                        :disabled="sendForm.processing"
                        @click="confirmSend"
                    >
                        {{ sendForm.processing ? 'Sending…' : 'Confirm Send' }}
                    </button>
                </div>
            </div>
        </div>
    </OwnerLayout>
</template>

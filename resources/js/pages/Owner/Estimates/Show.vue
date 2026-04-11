<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

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
    draft:    'bg-slate-100 text-slate-600',
    sent:     'bg-blue-100 text-blue-700',
    accepted: 'bg-green-100 text-green-700',
    declined: 'bg-red-100 text-red-600',
    expired:  'bg-amber-100 text-amber-700',
};

const TIER_LABELS: Record<string, string> = { good: 'Good', better: 'Better', best: 'Best' };

const sendForm = useForm({});
const convertForm = useForm({});

function sendEstimate() {
    sendForm.post(`/owner/estimates/${props.estimate.id}/send`);
}

function convertToJob() {
    if (!confirm('Convert this estimate to a job?')) return;
    convertForm.post(`/owner/estimates/${props.estimate.id}/convert`);
}

function deleteEstimate() {
    if (!confirm('Delete this estimate? This cannot be undone.')) return;
    router.delete(`/owner/estimates/${props.estimate.id}`);
}

const publicUrl = computed(() => `${window.location.origin}/estimates/${props.estimate.token}`);

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
        <nav class="mb-4 text-sm text-slate-500">
            <Link href="/owner/estimates" class="hover:underline">Estimates</Link>
            <span class="mx-1">›</span>
            <span class="text-slate-800">{{ estimate.estimate_number ?? estimate.title }}</span>
        </nav>

        <!-- Header -->
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-semibold text-slate-800">{{ estimate.title }}</h2>
                    <span
                        class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                        :class="STATUS_CLASSES[estimate.status] ?? 'bg-slate-100 text-slate-600'"
                    >
                        {{ statuses[estimate.status] ?? estimate.status }}
                    </span>
                </div>
                <p v-if="estimate.customer" class="mt-1 text-sm text-slate-500">
                    {{ estimate.customer.first_name }} {{ estimate.customer.last_name }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <Link
                    v-if="estimate.status === 'draft'"
                    :href="`/owner/estimates/${estimate.id}/edit`"
                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Edit
                </Link>
                <button
                    v-if="estimate.status === 'draft'"
                    type="button"
                    class="rounded-lg bg-slate-800 px-3 py-1.5 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                    :disabled="sendForm.processing"
                    @click="sendEstimate"
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
                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-medium text-slate-500 hover:bg-red-50 hover:text-red-600"
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
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs text-slate-400">Number</p>
                <p class="mt-1 font-mono text-sm font-medium text-slate-700">{{ estimate.estimate_number ?? '—' }}</p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs text-slate-400">Expires</p>
                <p class="mt-1 text-sm font-medium text-slate-700">{{ formatDate(estimate.expires_at) }}</p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs text-slate-400">Sent</p>
                <p class="mt-1 text-sm font-medium text-slate-700">{{ formatDate(estimate.sent_at) }}</p>
            </div>
            <div v-if="estimate.accepted_at" class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-green-200">
                <p class="text-xs text-green-500">Accepted</p>
                <p class="mt-1 text-sm font-medium text-green-700">
                    {{ formatDate(estimate.accepted_at) }} · {{ TIER_LABELS[estimate.accepted_package ?? ''] ?? estimate.accepted_package }}
                </p>
            </div>
        </div>

        <!-- Intro -->
        <div v-if="estimate.intro" class="mb-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <p class="whitespace-pre-wrap text-sm text-slate-600">{{ estimate.intro }}</p>
        </div>

        <!-- Packages -->
        <div class="space-y-4">
            <div
                v-for="pkg in estimate.packages"
                :key="pkg.id"
                class="rounded-xl bg-white shadow-sm ring-1"
                :class="pkg.is_recommended ? 'ring-blue-400' : 'ring-slate-200'"
            >
                <!-- Package header -->
                <div
                    class="flex items-center justify-between rounded-t-xl px-4 py-3"
                    :class="pkg.is_recommended ? 'bg-blue-50' : 'bg-slate-50'"
                >
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ TIER_LABELS[pkg.tier] ?? pkg.tier }}</span>
                        <span class="font-semibold text-slate-800">{{ pkg.label }}</span>
                        <span v-if="pkg.is_recommended" class="rounded-full bg-blue-600 px-2 py-0.5 text-xs font-medium text-white">Recommended</span>
                        <span v-if="estimate.accepted_package === pkg.tier" class="rounded-full bg-green-600 px-2 py-0.5 text-xs font-medium text-white">Accepted</span>
                    </div>
                    <span class="text-lg font-bold text-slate-800">{{ formatCurrency(pkg.total) }}</span>
                </div>

                <!-- Description -->
                <p v-if="pkg.description" class="border-b border-slate-100 px-4 py-2 text-sm text-slate-500">{{ pkg.description }}</p>

                <!-- Line items -->
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead>
                        <tr class="text-xs text-slate-400">
                            <th class="px-4 py-2 text-left font-medium">Item</th>
                            <th class="px-4 py-2 text-right font-medium">Qty</th>
                            <th class="px-4 py-2 text-right font-medium">Unit Price</th>
                            <th class="px-4 py-2 text-right font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-for="li in pkg.line_items" :key="li.id">
                            <td class="px-4 py-2.5 text-slate-700">
                                {{ li.name }}
                                <span v-if="!li.is_taxable" class="ml-1 text-xs text-slate-400">(non-taxable)</span>
                            </td>
                            <td class="px-4 py-2.5 text-right text-slate-500">{{ li.quantity }}</td>
                            <td class="px-4 py-2.5 text-right text-slate-500">{{ formatCurrency(li.unit_price) }}</td>
                            <td class="px-4 py-2.5 text-right font-medium text-slate-700">{{ formatCurrency(li.total) }}</td>
                        </tr>
                        <tr v-if="pkg.line_items.length === 0">
                            <td colspan="4" class="px-4 py-3 text-center text-xs text-slate-400">No line items.</td>
                        </tr>
                    </tbody>
                    <tfoot class="border-t border-slate-200 bg-slate-50 text-xs text-slate-500">
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right">Subtotal</td>
                            <td class="px-4 py-2 text-right font-medium">{{ formatCurrency(pkg.subtotal) }}</td>
                        </tr>
                        <tr v-if="Number(pkg.tax_amount) > 0">
                            <td colspan="3" class="px-4 py-2 text-right">Tax ({{ (Number(estimate.tax_rate) * 100).toFixed(2) }}%)</td>
                            <td class="px-4 py-2 text-right font-medium">{{ formatCurrency(pkg.tax_amount) }}</td>
                        </tr>
                        <tr class="text-slate-700">
                            <td colspan="3" class="px-4 py-2 text-right font-semibold">Total</td>
                            <td class="px-4 py-2 text-right font-bold">{{ formatCurrency(pkg.total) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div v-if="estimate.footer" class="mt-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <p class="whitespace-pre-wrap text-sm text-slate-500">{{ estimate.footer }}</p>
        </div>
    </OwnerLayout>
</template>

<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface Stats {
    total_count: number;
    total_invoiced: string;
    outstanding_balance: string;
    overdue_balance: string;
    paid_this_month: string;
    open_count: number;
    overdue_count: number;
}

interface Invoice {
    id: number;
    invoice_number: string | null;
    status: string;
    total: string;
    balance_due: string;
    due_at: string | null;
    issued_at: string | null;
    customer: { id: number; first_name: string; last_name: string } | null;
    job: { id: number; title: string } | null;
}

interface Paginator {
    data: Invoice[];
    links: { url: string | null; label: string; active: boolean }[];
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    stats: Stats;
    invoices: Paginator;
    filters: { search?: string; status?: string };
    statuses: Record<string, string>;
}>();

const STATUS_CLASSES: Record<string, string> = {
    draft:   'bg-slate-100 text-slate-600',
    sent:    'bg-blue-100 text-blue-700',
    paid:    'bg-green-100 text-green-700',
    partial: 'bg-amber-100 text-amber-700',
    overdue: 'bg-red-100 text-red-600',
    void:    'bg-slate-100 text-slate-400',
};

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');

let searchTimeout: ReturnType<typeof setTimeout>;

watch(search, (val) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/owner/billing', { search: val, status: status.value }, { preserveState: true, replace: true });
    }, 300);
});

watch(status, (val) => {
    router.get('/owner/billing', { search: search.value, status: val }, { preserveState: true, replace: true });
});

function formatDate(dt: string | null): string {
    if (!dt) return '—';
    return new Date(dt).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function formatCurrency(val: string | number): string {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(val));
}
</script>

<template>
    <OwnerLayout title="Billing">
        <Head title="Billing" />

        <!-- Summary tiles -->
        <section class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
            <div class="rounded-xl bg-white p-4 shadow ring-1 ring-slate-200">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Outstanding</p>
                <p class="mt-2 text-2xl font-semibold text-blue-600">
                    {{ formatCurrency(stats.outstanding_balance) }}
                </p>
                <p class="mt-0.5 text-xs text-slate-400">{{ stats.open_count }} open invoice{{ stats.open_count !== 1 ? 's' : '' }}</p>
            </div>

            <div class="rounded-xl bg-white p-4 shadow ring-1 ring-slate-200">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Overdue</p>
                <p class="mt-2 text-2xl font-semibold" :class="Number(stats.overdue_balance) > 0 ? 'text-red-600' : 'text-slate-400'">
                    {{ formatCurrency(stats.overdue_balance) }}
                </p>
                <p class="mt-0.5 text-xs text-slate-400">{{ stats.overdue_count }} overdue invoice{{ stats.overdue_count !== 1 ? 's' : '' }}</p>
            </div>

            <div class="rounded-xl bg-white p-4 shadow ring-1 ring-slate-200">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Paid This Month</p>
                <p class="mt-2 text-2xl font-semibold text-green-600">
                    {{ formatCurrency(stats.paid_this_month) }}
                </p>
            </div>

            <div class="rounded-xl bg-white p-4 shadow ring-1 ring-slate-200">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Total Invoiced</p>
                <p class="mt-2 text-2xl font-semibold text-slate-800">
                    {{ formatCurrency(stats.total_invoiced) }}
                </p>
            </div>

            <div class="rounded-xl bg-white p-4 shadow ring-1 ring-slate-200">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">All Invoices</p>
                <p class="mt-2 text-2xl font-semibold text-slate-800">
                    {{ stats.total_count }}
                </p>
                <p class="mt-0.5 text-xs text-slate-400">lifetime</p>
            </div>
        </section>

        <!-- Quick filters -->
        <div class="mb-4 flex flex-wrap items-center gap-2">
            <button
                v-for="(label, key) in { '': 'All', sent: 'Sent', partial: 'Partial', overdue: 'Overdue', paid: 'Paid', draft: 'Draft' }"
                :key="key"
                @click="status = key"
                class="rounded-full px-3 py-1 text-xs font-medium transition"
                :class="status === key
                    ? 'bg-slate-800 text-white'
                    : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50'"
            >
                {{ label }}
            </button>

            <input
                v-model="search"
                type="search"
                placeholder="Search number or customer…"
                class="ml-auto w-56 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-slate-400"
            />
        </div>

        <!-- Invoice table -->
        <div class="overflow-hidden rounded-xl bg-white shadow ring-1 ring-slate-200">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-xs text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Number</th>
                        <th class="px-4 py-3 text-left font-medium">Customer</th>
                        <th class="px-4 py-3 text-left font-medium">Job</th>
                        <th class="px-4 py-3 text-left font-medium">Status</th>
                        <th class="px-4 py-3 text-right font-medium">Total</th>
                        <th class="px-4 py-3 text-right font-medium">Balance Due</th>
                        <th class="px-4 py-3 text-left font-medium">Due</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-for="inv in invoices.data" :key="inv.id" class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono font-medium text-slate-700">
                            {{ inv.invoice_number ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-slate-700">
                            <span v-if="inv.customer">{{ inv.customer.first_name }} {{ inv.customer.last_name }}</span>
                            <span v-else class="text-slate-400">—</span>
                        </td>
                        <td class="px-4 py-3 text-slate-500">
                            <Link v-if="inv.job" :href="`/owner/jobs/${inv.job.id}`" class="hover:underline">
                                {{ inv.job.title }}
                            </Link>
                            <span v-else class="text-slate-400">—</span>
                        </td>
                        <td class="px-4 py-3">
                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="STATUS_CLASSES[inv.status] ?? 'bg-slate-100 text-slate-600'"
                            >
                                {{ statuses[inv.status] ?? inv.status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-slate-700">{{ formatCurrency(inv.total) }}</td>
                        <td
                            class="px-4 py-3 text-right font-medium"
                            :class="Number(inv.balance_due) > 0 ? 'text-red-600' : 'text-green-700'"
                        >
                            {{ formatCurrency(inv.balance_due) }}
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ formatDate(inv.due_at) }}</td>
                        <td class="px-4 py-3 text-right">
                            <Link
                                :href="`/owner/invoices/${inv.id}`"
                                class="text-xs font-medium text-blue-600 hover:underline"
                            >
                                View
                            </Link>
                        </td>
                    </tr>
                    <tr v-if="invoices.data.length === 0">
                        <td colspan="8" class="px-4 py-8 text-center text-sm text-slate-400">No invoices found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="invoices.total > 25" class="mt-4 flex flex-wrap gap-1">
            <template v-for="link in invoices.links" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="rounded px-2.5 py-1 text-xs font-medium"
                    :class="link.active ? 'bg-slate-800 text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-50'"
                    v-html="link.label"
                />
                <span
                    v-else
                    class="cursor-default rounded border border-slate-100 px-2.5 py-1 text-xs text-slate-300"
                    v-html="link.label"
                />
            </template>
        </div>
    </OwnerLayout>
</template>

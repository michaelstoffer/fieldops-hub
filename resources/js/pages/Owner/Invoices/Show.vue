<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
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

interface Payment {
    id: number;
    amount: string;
    method: string;
    reference: string | null;
    paid_at: string;
    recorded_by: { id: number; name: string } | null;
    notes: string | null;
}

interface Invoice {
    id: number;
    invoice_number: string | null;
    status: string;
    subtotal: string;
    tax_rate: string;
    tax_amount: string;
    discount_amount: string;
    total: string;
    amount_paid: string;
    balance_due: string;
    issued_at: string | null;
    due_at: string | null;
    sent_at: string | null;
    paid_at: string | null;
    notes: string | null;
    customer: { id: number; first_name: string; last_name: string } | null;
    job: { id: number; title: string } | null;
    line_items: LineItem[];
    payments: Payment[];
}

const props = defineProps<{
    invoice: Invoice;
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

const PAYMENT_METHOD_LABELS: Record<string, string> = {
    cash: 'Cash', check: 'Check', card: 'Card',
    bank_transfer: 'Bank Transfer', stripe: 'Stripe',
};

const sendForm = useForm({});
const voidForm = useForm({});
const deleteForm = useForm({});
const checkoutForm = useForm({});

const showPaymentForm = ref(false);
const paymentForm = useForm({
    amount:    '',
    method:    'cash',
    reference: '',
    notes:     '',
    paid_at:   new Date().toISOString().slice(0, 10),
});

const PAYMENT_METHODS = [
    { value: 'cash',          label: 'Cash' },
    { value: 'check',         label: 'Check' },
    { value: 'card',          label: 'Card' },
    { value: 'bank_transfer', label: 'Bank Transfer' },
];

function submitPayment() {
    paymentForm.post(`/owner/invoices/${props.invoice.id}/payments`, {
        onSuccess: () => {
            showPaymentForm.value = false;
            paymentForm.reset();
        },
    });
}

function startCheckout() {
    checkoutForm.post(`/owner/invoices/${props.invoice.id}/checkout`);
}

const paymentResult = computed(() => {
    const params = new URLSearchParams(window.location.search);
    return params.get('payment');
});

function sendInvoice() {
    sendForm.post(`/owner/invoices/${props.invoice.id}/send`);
}

function voidInvoice() {
    if (!confirm('Void this invoice? This cannot be undone.')) return;
    voidForm.post(`/owner/invoices/${props.invoice.id}/void`);
}

function deleteInvoice() {
    if (!confirm('Delete this draft invoice?')) return;
    deleteForm.delete(`/owner/invoices/${props.invoice.id}`);
}

function formatDate(dt: string | null): string {
    if (!dt) return '—';
    return new Date(dt).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function formatDateTime(dt: string | null): string {
    if (!dt) return '—';
    return new Date(dt).toLocaleString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric',
        hour: 'numeric', minute: '2-digit',
    });
}

function formatCurrency(val: string | number): string {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(val));
}
</script>

<template>
    <OwnerLayout :title="invoice.invoice_number ?? 'Invoice'">
        <Head :title="invoice.invoice_number ?? 'Invoice'" />

        <!-- Breadcrumb -->
        <nav class="mb-4 text-sm text-slate-500">
            <Link href="/owner/invoices" class="hover:underline">Invoices</Link>
            <span class="mx-1">›</span>
            <span class="text-slate-800">{{ invoice.invoice_number ?? 'Invoice' }}</span>
        </nav>

        <!-- Payment result banners -->
        <div v-if="paymentResult === 'success'" class="mb-4 rounded-xl bg-green-50 px-4 py-3 text-sm text-green-700 ring-1 ring-green-200">
            Payment received — invoice updated.
        </div>
        <div v-if="paymentResult === 'cancelled'" class="mb-4 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-700 ring-1 ring-amber-200">
            Payment was cancelled. No charge was made.
        </div>

        <!-- Header -->
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-semibold text-slate-800">{{ invoice.invoice_number ?? 'Invoice' }}</h2>
                    <span
                        class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                        :class="STATUS_CLASSES[invoice.status] ?? 'bg-slate-100 text-slate-600'"
                    >
                        {{ statuses[invoice.status] ?? invoice.status }}
                    </span>
                </div>
                <p v-if="invoice.customer" class="mt-1 text-sm text-slate-500">
                    {{ invoice.customer.first_name }} {{ invoice.customer.last_name }}
                    <template v-if="invoice.job">
                        · <Link :href="`/owner/jobs/${invoice.job.id}`" class="hover:underline">{{ invoice.job.title }}</Link>
                    </template>
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button
                    v-if="['sent', 'partial', 'overdue'].includes(invoice.status) && Number(invoice.balance_due) > 0"
                    type="button"
                    class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                    :disabled="checkoutForm.processing"
                    @click="startCheckout"
                >
                    Pay Online (Stripe)
                </button>
                <button
                    v-if="['draft', 'overdue'].includes(invoice.status)"
                    type="button"
                    class="rounded-lg bg-slate-800 px-3 py-1.5 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                    :disabled="sendForm.processing"
                    @click="sendInvoice"
                >
                    Send to Customer
                </button>
                <button
                    v-if="!['paid', 'void'].includes(invoice.status)"
                    type="button"
                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-medium text-slate-500 hover:bg-amber-50 hover:text-amber-700 disabled:opacity-50"
                    :disabled="voidForm.processing"
                    @click="voidInvoice"
                >
                    Void
                </button>
                <button
                    v-if="invoice.status === 'draft'"
                    type="button"
                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-medium text-slate-500 hover:bg-red-50 hover:text-red-600"
                    @click="deleteInvoice"
                >
                    Delete
                </button>
            </div>
        </div>

        <!-- Meta -->
        <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs text-slate-400">Issued</p>
                <p class="mt-1 text-sm font-medium text-slate-700">{{ formatDate(invoice.issued_at) }}</p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs text-slate-400">Due</p>
                <p class="mt-1 text-sm font-medium" :class="invoice.status === 'overdue' ? 'text-red-600' : 'text-slate-700'">
                    {{ formatDate(invoice.due_at) }}
                </p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs text-slate-400">Total</p>
                <p class="mt-1 text-sm font-bold text-slate-800">{{ formatCurrency(invoice.total) }}</p>
            </div>
            <div
                class="rounded-xl p-4 shadow-sm ring-1"
                :class="Number(invoice.balance_due) > 0 ? 'bg-red-50 ring-red-200' : 'bg-green-50 ring-green-200'"
            >
                <p class="text-xs" :class="Number(invoice.balance_due) > 0 ? 'text-red-400' : 'text-green-500'">Balance Due</p>
                <p class="mt-1 text-sm font-bold" :class="Number(invoice.balance_due) > 0 ? 'text-red-700' : 'text-green-700'">
                    {{ formatCurrency(invoice.balance_due) }}
                </p>
            </div>
        </div>

        <!-- Line items -->
        <div class="mb-6 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-100 px-4 py-3">
                <h3 class="text-sm font-semibold text-slate-700">Line Items</h3>
            </div>
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-xs text-slate-400">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium">Item</th>
                        <th class="px-4 py-2 text-right font-medium">Qty</th>
                        <th class="px-4 py-2 text-right font-medium">Unit Price</th>
                        <th class="px-4 py-2 text-right font-medium">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-for="li in invoice.line_items" :key="li.id">
                        <td class="px-4 py-2.5 text-slate-700">
                            {{ li.name }}
                            <span v-if="!li.is_taxable" class="ml-1 text-xs text-slate-400">(non-taxable)</span>
                            <p v-if="li.description" class="text-xs text-slate-400">{{ li.description }}</p>
                        </td>
                        <td class="px-4 py-2.5 text-right text-slate-500">{{ li.quantity }}</td>
                        <td class="px-4 py-2.5 text-right text-slate-500">{{ formatCurrency(li.unit_price) }}</td>
                        <td class="px-4 py-2.5 text-right font-medium text-slate-700">{{ formatCurrency(li.total) }}</td>
                    </tr>
                    <tr v-if="invoice.line_items.length === 0">
                        <td colspan="4" class="px-4 py-4 text-center text-xs text-slate-400">No line items.</td>
                    </tr>
                </tbody>
                <tfoot class="border-t border-slate-200 bg-slate-50 text-xs text-slate-500">
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-right">Subtotal</td>
                        <td class="px-4 py-2 text-right font-medium">{{ formatCurrency(invoice.subtotal) }}</td>
                    </tr>
                    <tr v-if="Number(invoice.discount_amount) > 0">
                        <td colspan="3" class="px-4 py-2 text-right">Discount</td>
                        <td class="px-4 py-2 text-right font-medium text-green-600">-{{ formatCurrency(invoice.discount_amount) }}</td>
                    </tr>
                    <tr v-if="Number(invoice.tax_amount) > 0">
                        <td colspan="3" class="px-4 py-2 text-right">
                            Tax ({{ (Number(invoice.tax_rate) * 100).toFixed(2) }}%)
                        </td>
                        <td class="px-4 py-2 text-right font-medium">{{ formatCurrency(invoice.tax_amount) }}</td>
                    </tr>
                    <tr class="text-slate-700">
                        <td colspan="3" class="px-4 py-2 text-right font-semibold">Total</td>
                        <td class="px-4 py-2 text-right font-bold">{{ formatCurrency(invoice.total) }}</td>
                    </tr>
                    <tr v-if="Number(invoice.amount_paid) > 0" class="text-green-700">
                        <td colspan="3" class="px-4 py-2 text-right font-semibold">Amount Paid</td>
                        <td class="px-4 py-2 text-right font-medium">{{ formatCurrency(invoice.amount_paid) }}</td>
                    </tr>
                    <tr v-if="Number(invoice.amount_paid) > 0" class="font-semibold text-slate-700">
                        <td colspan="3" class="px-4 py-2 text-right">Balance Due</td>
                        <td class="px-4 py-2 text-right font-bold" :class="Number(invoice.balance_due) > 0 ? 'text-red-600' : 'text-green-700'">
                            {{ formatCurrency(invoice.balance_due) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Notes -->
        <div v-if="invoice.notes" class="mb-6 rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <p class="mb-1 text-xs font-medium text-slate-400">Notes</p>
            <p class="whitespace-pre-wrap text-sm text-slate-600">{{ invoice.notes }}</p>
        </div>

        <!-- Record payment -->
        <div
            v-if="!['paid', 'void', 'draft'].includes(invoice.status) && Number(invoice.balance_due) > 0"
            class="mb-6 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200"
        >
            <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                <h3 class="text-sm font-semibold text-slate-700">Record Payment</h3>
                <button
                    v-if="!showPaymentForm"
                    type="button"
                    class="rounded-lg bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-700"
                    @click="showPaymentForm = true"
                >
                    + Record Payment
                </button>
            </div>
            <form v-if="showPaymentForm" class="space-y-4 px-4 py-4" @submit.prevent="submitPayment">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Amount</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-slate-400">$</span>
                            <input
                                v-model="paymentForm.amount"
                                type="number"
                                step="0.01"
                                min="0.01"
                                :max="invoice.balance_due"
                                :placeholder="invoice.balance_due"
                                class="w-full rounded-lg border border-slate-200 py-2 pl-7 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400"
                                :class="{ 'border-red-400': paymentForm.errors.amount }"
                                required
                            />
                        </div>
                        <p v-if="paymentForm.errors.amount" class="mt-1 text-xs text-red-500">{{ paymentForm.errors.amount }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Method</label>
                        <select
                            v-model="paymentForm.method"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400"
                        >
                            <option v-for="m in PAYMENT_METHODS" :key="m.value" :value="m.value">{{ m.label }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Date</label>
                        <input
                            v-model="paymentForm.paid_at"
                            type="date"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400"
                            :class="{ 'border-red-400': paymentForm.errors.paid_at }"
                            required
                        />
                        <p v-if="paymentForm.errors.paid_at" class="mt-1 text-xs text-red-500">{{ paymentForm.errors.paid_at }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Reference <span class="text-slate-400">(cheque #, receipt, etc.)</span></label>
                        <input
                            v-model="paymentForm.reference"
                            type="text"
                            placeholder="Optional"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400"
                        />
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Notes <span class="text-slate-400">(optional)</span></label>
                    <textarea
                        v-model="paymentForm.notes"
                        rows="2"
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400"
                    />
                </div>
                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-600 hover:bg-slate-50"
                        @click="showPaymentForm = false; paymentForm.reset()"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="rounded-lg bg-green-600 px-4 py-1.5 text-sm font-medium text-white hover:bg-green-700 disabled:opacity-50"
                        :disabled="paymentForm.processing"
                    >
                        Save Payment
                    </button>
                </div>
            </form>
            <p v-else class="px-4 py-3 text-sm text-slate-400">
                Balance due: <span class="font-semibold text-slate-700">{{ formatCurrency(invoice.balance_due) }}</span>
            </p>
        </div>

        <!-- Payment history -->
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-100 px-4 py-3">
                <h3 class="text-sm font-semibold text-slate-700">Payment History</h3>
            </div>
            <div v-if="invoice.payments.length === 0" class="px-4 py-4 text-sm text-slate-400">
                No payments recorded.
            </div>
            <table v-else class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-xs text-slate-400">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium">Date</th>
                        <th class="px-4 py-2 text-left font-medium">Method</th>
                        <th class="px-4 py-2 text-left font-medium">Reference</th>
                        <th class="px-4 py-2 text-left font-medium">Recorded by</th>
                        <th class="px-4 py-2 text-right font-medium">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-for="pmt in invoice.payments" :key="pmt.id">
                        <td class="px-4 py-2.5 text-slate-500">{{ formatDateTime(pmt.paid_at) }}</td>
                        <td class="px-4 py-2.5 text-slate-700 capitalize">{{ PAYMENT_METHOD_LABELS[pmt.method] ?? pmt.method }}</td>
                        <td class="px-4 py-2.5 font-mono text-xs text-slate-500">{{ pmt.reference ?? '—' }}</td>
                        <td class="px-4 py-2.5 text-slate-500">{{ pmt.recorded_by?.name ?? '—' }}</td>
                        <td class="px-4 py-2.5 text-right font-medium text-green-700">{{ formatCurrency(pmt.amount) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </OwnerLayout>
</template>

<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Customer { id: number; first_name: string; last_name: string }
interface CatalogItem { id: number; name: string; unit_price: string; unit: string; is_taxable: boolean }

interface LineItemForm {
    item_id: number | null;
    name: string;
    description: string;
    unit_price: string;
    quantity: string;
    is_taxable: boolean;
}

const props = defineProps<{
    customers: Customer[];
    catalogItems: CatalogItem[];
}>();

function today(): string {
    return new Date().toISOString().slice(0, 10);
}
function thirtyDaysOut(): string {
    const d = new Date();
    d.setDate(d.getDate() + 30);
    return d.toISOString().slice(0, 10);
}

const form = useForm({
    customer_id:     null as number | null,
    issued_at:       today(),
    due_at:          thirtyDaysOut(),
    tax_rate:        '0',
    discount_amount: '0.00',
    notes:           '',
    line_items:      [blankLine()] as LineItemForm[],
});

function blankLine(): LineItemForm {
    return { item_id: null, name: '', description: '', unit_price: '0.00', quantity: '1', is_taxable: false };
}

function addLine() {
    form.line_items.push(blankLine());
}

function removeLine(idx: number) {
    form.line_items.splice(idx, 1);
}

function selectCatalog(idx: number, itemId: string) {
    if (!itemId) return;
    const cat = props.catalogItems.find((c) => c.id === Number(itemId));
    if (!cat) return;
    const li = form.line_items[idx];
    li.item_id    = cat.id;
    li.name       = cat.name;
    li.unit_price = parseFloat(cat.unit_price).toFixed(2);
    li.is_taxable = cat.is_taxable;
}

const subtotal = computed(() =>
    form.line_items.reduce((sum, li) =>
        sum + parseFloat(li.unit_price || '0') * parseFloat(li.quantity || '0'), 0),
);

const taxAmount = computed(() => {
    const taxableSubtotal = form.line_items
        .filter((li) => li.is_taxable)
        .reduce((sum, li) => sum + parseFloat(li.unit_price || '0') * parseFloat(li.quantity || '0'), 0);
    return taxableSubtotal * (parseFloat(form.tax_rate || '0') / 100);
});

const total = computed(() =>
    subtotal.value + taxAmount.value - parseFloat(form.discount_amount || '0'),
);

function fmt(n: number) {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(n);
}

function submit() {
    form.transform((data) => ({
        ...data,
        tax_rate:        parseFloat(data.tax_rate || '0') / 100,
        discount_amount: parseFloat(data.discount_amount || '0'),
        line_items:      data.line_items.map((li) => ({
            ...li,
            unit_price: parseFloat(li.unit_price || '0'),
            quantity:   parseFloat(li.quantity || '1'),
        })),
    })).post('/owner/invoices');
}
</script>

<template>
    <OwnerLayout title="New Invoice">
        <Head title="New Invoice" />

        <nav class="mb-4 text-sm text-muted-foreground">
            <Link href="/owner/invoices" class="hover:underline">Invoices</Link>
            <span class="mx-1">›</span>
            <span class="text-foreground">New Invoice</span>
        </nav>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Header fields -->
            <div class="rounded-xl bg-card p-6 shadow-sm ring-1 ring-border">
                <h3 class="mb-4 text-sm font-semibold text-foreground">Details</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <!-- Customer -->
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-muted-foreground">Customer <span class="text-red-500">*</span></label>
                        <select
                            v-model="form.customer_id"
                            class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                            :class="{ 'border-destructive': form.errors.customer_id }"
                        >
                            <option :value="null">Select customer…</option>
                            <option v-for="c in customers" :key="c.id" :value="c.id">
                                {{ c.last_name }}, {{ c.first_name }}
                            </option>
                        </select>
                        <p v-if="form.errors.customer_id" class="mt-1 text-xs text-destructive">{{ form.errors.customer_id }}</p>
                    </div>

                    <!-- Issued at -->
                    <div>
                        <label class="mb-1 block text-xs font-medium text-muted-foreground">Issue Date <span class="text-red-500">*</span></label>
                        <input
                            v-model="form.issued_at"
                            type="date"
                            class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                            :class="{ 'border-destructive': form.errors.issued_at }"
                        />
                        <p v-if="form.errors.issued_at" class="mt-1 text-xs text-destructive">{{ form.errors.issued_at }}</p>
                    </div>

                    <!-- Due at -->
                    <div>
                        <label class="mb-1 block text-xs font-medium text-muted-foreground">Due Date <span class="text-red-500">*</span></label>
                        <input
                            v-model="form.due_at"
                            type="date"
                            class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                            :class="{ 'border-destructive': form.errors.due_at }"
                        />
                        <p v-if="form.errors.due_at" class="mt-1 text-xs text-destructive">{{ form.errors.due_at }}</p>
                    </div>

                    <!-- Notes -->
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-muted-foreground">Notes</label>
                        <textarea
                            v-model="form.notes"
                            rows="2"
                            class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                            placeholder="Optional notes shown on the invoice…"
                        />
                    </div>
                </div>
            </div>

            <!-- Line items -->
            <div class="rounded-xl bg-card shadow-sm ring-1 ring-border">
                <div class="border-b border-border px-4 py-3">
                    <h3 class="text-sm font-semibold text-foreground">Line Items</h3>
                </div>

                <div class="divide-y divide-border/50">
                    <div
                        v-for="(li, idx) in form.line_items"
                        :key="idx"
                        class="grid grid-cols-12 gap-2 px-4 py-3 items-start"
                    >
                        <!-- Catalog picker + name -->
                        <div class="col-span-12 sm:col-span-4">
                            <select
                                class="w-full rounded border border-border bg-background px-2 py-1.5 text-xs text-foreground focus:border-slate-400 focus:outline-none"
                                @change="selectCatalog(idx, ($event.target as HTMLSelectElement).value)"
                            >
                                <option value="">From catalog…</option>
                                <option v-for="cat in catalogItems" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                            <input
                                v-model="li.name"
                                type="text"
                                placeholder="Item name *"
                                class="mt-1 w-full rounded border border-border bg-background px-2 py-1.5 text-sm text-foreground focus:border-slate-400 focus:outline-none"
                            />
                        </div>
                        <!-- Description -->
                        <div class="col-span-12 sm:col-span-3">
                            <input
                                v-model="li.description"
                                type="text"
                                placeholder="Description"
                                class="w-full rounded border border-border bg-background px-2 py-1.5 text-sm text-muted-foreground focus:border-slate-400 focus:outline-none"
                            />
                        </div>
                        <!-- Qty -->
                        <div class="col-span-3 sm:col-span-1">
                            <input
                                v-model="li.quantity"
                                type="number"
                                min="0.001"
                                step="any"
                                placeholder="Qty"
                                class="w-full rounded border border-border bg-background px-2 py-1.5 text-sm text-right focus:border-slate-400 focus:outline-none"
                            />
                        </div>
                        <!-- Unit price -->
                        <div class="col-span-4 sm:col-span-2">
                            <input
                                v-model="li.unit_price"
                                type="number"
                                min="0"
                                step="0.01"
                                placeholder="Price"
                                class="w-full rounded border border-border bg-background px-2 py-1.5 text-sm text-right focus:border-slate-400 focus:outline-none"
                            />
                        </div>
                        <!-- Line total -->
                        <div class="col-span-3 sm:col-span-1 text-right text-sm font-medium text-foreground pt-2">
                            {{ fmt(parseFloat(li.unit_price || '0') * parseFloat(li.quantity || '0')) }}
                        </div>
                        <!-- Taxable + remove -->
                        <div class="col-span-2 sm:col-span-1 flex flex-col items-center gap-1 pt-1">
                            <label class="text-xs text-muted-foreground cursor-pointer" title="Taxable">
                                <input v-model="li.is_taxable" type="checkbox" class="rounded" /> Tax
                            </label>
                            <button
                                type="button"
                                class="text-xs text-red-400 hover:text-red-600"
                                @click="removeLine(idx)"
                            >
                                ✕
                            </button>
                        </div>
                    </div>
                </div>

                <div class="border-t border-border px-4 py-2">
                    <button
                        type="button"
                        class="text-xs font-medium text-blue-600 hover:underline"
                        @click="addLine"
                    >
                        + Add line item
                    </button>
                </div>
            </div>

            <!-- Totals -->
            <div class="rounded-xl bg-card p-6 shadow-sm ring-1 ring-border">
                <div class="ml-auto max-w-xs space-y-2 text-sm">
                    <div class="flex justify-between text-muted-foreground">
                        <span>Subtotal</span>
                        <span>{{ fmt(subtotal) }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 text-muted-foreground">
                        <label class="flex items-center gap-2">
                            Tax rate
                            <input
                                v-model="form.tax_rate"
                                type="number"
                                min="0"
                                max="100"
                                step="0.01"
                                class="w-20 rounded border border-border bg-background px-2 py-1 text-right text-xs focus:border-slate-400 focus:outline-none"
                            />
                            <span class="text-xs text-muted-foreground">%</span>
                        </label>
                        <span>{{ fmt(taxAmount) }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 text-muted-foreground">
                        <label class="flex items-center gap-2">
                            Discount
                            <input
                                v-model="form.discount_amount"
                                type="number"
                                min="0"
                                step="0.01"
                                class="w-24 rounded border border-border bg-background px-2 py-1 text-right text-xs focus:border-slate-400 focus:outline-none"
                            />
                        </label>
                        <span>- {{ fmt(parseFloat(form.discount_amount || '0')) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-border pt-2 text-base font-semibold text-foreground">
                        <span>Total</span>
                        <span>{{ fmt(total) }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3">
                <Link
                    href="/owner/invoices"
                    class="rounded-lg border border-border px-4 py-2 text-sm font-medium text-muted-foreground hover:bg-accent"
                >
                    Cancel
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                >
                    Create Invoice
                </button>
            </div>
        </form>
    </OwnerLayout>
</template>

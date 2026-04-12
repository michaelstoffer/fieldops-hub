<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';

interface Customer { id: number; first_name: string; last_name: string }
interface Job { id: number; title: string; scheduled_at: string | null; customer_id: number }
interface CatalogItem { id: number; name: string; unit_price: string; unit: string; is_taxable: boolean }

interface LineItemForm {
    item_id: number | null;
    name: string;
    description: string;
    unit_price: string;
    quantity: string;
    is_taxable: boolean;
}

interface PackageForm {
    tier: string;
    label: string;
    description: string;
    is_recommended: boolean;
    line_items: LineItemForm[];
}

const props = defineProps<{
    customers: Customer[];
    jobs: Job[];
    catalogItems: CatalogItem[];
    tiers: string[];
    statuses: Record<string, string>;
    // only present on edit
    estimate?: {
        id: number;
        customer_id: number;
        job_id: number | null;
        title: string;
        intro: string | null;
        footer: string | null;
        expires_at: string | null;
        tax_rate: string;
        packages: Array<{
            tier: string;
            label: string;
            description: string | null;
            is_recommended: boolean;
            line_items: Array<{
                item_id: number | null;
                name: string;
                description: string | null;
                unit_price: string;
                quantity: string;
                is_taxable: boolean;
            }>;
        }>;
    };
}>();

const TIER_LABELS: Record<string, string> = { good: 'Good', better: 'Better', best: 'Best' };
const isEdit = computed(() => !!props.estimate);

// Build default packages (one per tier)
function defaultPackages(): PackageForm[] {
    return props.tiers.map((tier) => {
        const existing = props.estimate?.packages.find((p) => p.tier === tier);
        return {
            tier,
            label: existing?.label ?? TIER_LABELS[tier] ?? tier,
            description: existing?.description ?? '',
            is_recommended: existing?.is_recommended ?? (tier === 'better'),
            line_items: existing?.line_items.map((li) => ({
                item_id: li.item_id,
                name: li.name,
                description: li.description ?? '',
                unit_price: li.unit_price,
                quantity: li.quantity,
                is_taxable: li.is_taxable,
            })) ?? [],
        };
    });
}

const form = useForm({
    customer_id: props.estimate?.customer_id ?? (null as number | null),
    job_id:      props.estimate?.job_id ?? (null as number | null),
    title:       props.estimate?.title ?? '',
    intro:       props.estimate?.intro ?? '',
    footer:      props.estimate?.footer ?? '',
    expires_at:  props.estimate?.expires_at ?? '',
    tax_rate:    props.estimate?.tax_rate ? String(Number(props.estimate.tax_rate) * 100) : '0',
    packages:    defaultPackages(),
});

// Which tiers are included
const activeTiers = reactive(new Set(
    props.estimate ? props.estimate.packages.map((p) => p.tier) : ['good', 'better', 'best'],
));

function toggleTier(tier: string) {
    if (activeTiers.has(tier)) {
        activeTiers.delete(tier);
    } else {
        activeTiers.add(tier);
    }
}

const activePackages = computed(() =>
    form.packages.filter((p) => activeTiers.has(p.tier)),
);

function addLineItem(pkg: PackageForm) {
    pkg.line_items.push({
        item_id: null, name: '', description: '', unit_price: '0.00', quantity: '1', is_taxable: true,
    });
}

function removeLineItem(pkg: PackageForm, idx: number) {
    pkg.line_items.splice(idx, 1);
}

function selectCatalog(pkg: PackageForm, idx: number, itemId: number | null) {
    if (!itemId) return;
    const cat = props.catalogItems.find((c) => c.id === Number(itemId));
    if (!cat) return;
    const li = pkg.line_items[idx];
    li.item_id    = cat.id;
    li.name       = cat.name;
    li.unit_price = parseFloat(cat.unit_price).toFixed(2);
    li.is_taxable = cat.is_taxable;
}

function packageTotal(pkg: PackageForm): number {
    return pkg.line_items.reduce((sum, li) => sum + parseFloat(li.unit_price || '0') * parseFloat(li.quantity || '0'), 0);
}

function submit() {
    const taxRateFraction = parseFloat(form.tax_rate || '0') / 100;
    const payload = {
        ...form,
        tax_rate: taxRateFraction,
        packages: activePackages.value.map((p) => ({
            ...p,
            line_items: p.line_items.map((li) => ({
                ...li,
                unit_price: parseFloat(li.unit_price || '0'),
                quantity:   parseFloat(li.quantity || '1'),
            })),
        })),
    };

    if (isEdit.value) {
        form.transform(() => payload).patch(`/owner/estimates/${props.estimate!.id}`);
    } else {
        form.transform(() => payload).post('/owner/estimates');
    }
}

function formatCurrency(val: number): string {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val);
}
</script>

<template>
    <OwnerLayout :title="isEdit ? 'Edit Estimate' : 'New Estimate'">
        <Head :title="isEdit ? 'Edit Estimate' : 'New Estimate'" />

        <!-- Breadcrumb -->
        <nav class="mb-4 text-sm text-slate-500">
            <Link href="/owner/estimates" class="hover:underline">Estimates</Link>
            <span class="mx-1">›</span>
            <span class="text-slate-800">{{ isEdit ? 'Edit' : 'New' }}</span>
        </nav>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Basic details -->
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="mb-4 text-sm font-semibold text-slate-700">Details</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <!-- Customer -->
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Customer <span class="text-red-500">*</span></label>
                        <select
                            v-model="form.customer_id"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                            :class="{ 'border-red-400': form.errors.customer_id }"
                        >
                            <option :value="null">Select customer…</option>
                            <option v-for="c in customers" :key="c.id" :value="c.id">
                                {{ c.first_name }} {{ c.last_name }}
                            </option>
                        </select>
                        <p v-if="form.errors.customer_id" class="mt-1 text-xs text-red-500">{{ form.errors.customer_id }}</p>
                    </div>

                    <!-- Job (optional) -->
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Related Job</label>
                        <select
                            v-model="form.job_id"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                        >
                            <option :value="null">None</option>
                            <option v-for="j in jobs" :key="j.id" :value="j.id">{{ j.title }}</option>
                        </select>
                    </div>

                    <!-- Title -->
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-slate-600">Title <span class="text-red-500">*</span></label>
                        <input
                            v-model="form.title"
                            type="text"
                            placeholder="e.g. HVAC Service Estimate"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                            :class="{ 'border-red-400': form.errors.title }"
                        />
                        <p v-if="form.errors.title" class="mt-1 text-xs text-red-500">{{ form.errors.title }}</p>
                    </div>

                    <!-- Expires at -->
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Expires</label>
                        <input
                            v-model="form.expires_at"
                            type="date"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                        />
                    </div>

                    <!-- Tax rate -->
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Tax Rate (%)</label>
                        <input
                            v-model="form.tax_rate"
                            type="number"
                            min="0"
                            max="100"
                            step="0.01"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                        />
                    </div>

                    <!-- Intro -->
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-slate-600">Intro (shown above packages)</label>
                        <textarea
                            v-model="form.intro"
                            rows="2"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                            placeholder="Thank you for the opportunity…"
                        />
                    </div>

                    <!-- Footer -->
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-slate-600">Footer (shown below packages)</label>
                        <textarea
                            v-model="form.footer"
                            rows="2"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                            placeholder="Prices valid for 30 days…"
                        />
                    </div>
                </div>
            </div>

            <!-- Tier toggles -->
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="tier in tiers"
                    :key="tier"
                    type="button"
                    class="rounded-lg border px-3 py-1.5 text-sm font-medium transition"
                    :class="activeTiers.has(tier)
                        ? 'border-slate-800 bg-slate-800 text-white'
                        : 'border-slate-200 text-slate-500'"
                    @click="toggleTier(tier)"
                >
                    {{ TIER_LABELS[tier] ?? tier }}
                </button>
                <p class="ml-2 self-center text-xs text-slate-400">Toggle tiers to include in estimate</p>
            </div>

            <!-- Package builders -->
            <div
                v-for="pkg in form.packages"
                v-show="activeTiers.has(pkg.tier)"
                :key="pkg.tier"
                class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200"
            >
                <!-- Package header -->
                <div class="flex flex-wrap items-center gap-4 border-b border-slate-100 px-4 py-3">
                    <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ TIER_LABELS[pkg.tier] ?? pkg.tier }}</span>
                    <input
                        v-model="pkg.label"
                        type="text"
                        placeholder="Package label"
                        class="flex-1 rounded border-0 bg-transparent text-sm font-semibold text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-300 px-1 py-0.5"
                    />
                    <label class="flex items-center gap-1.5 text-xs text-slate-500 cursor-pointer">
                        <input v-model="pkg.is_recommended" type="checkbox" class="rounded" />
                        Recommended
                    </label>
                    <span class="ml-auto text-sm font-bold text-slate-700">{{ formatCurrency(packageTotal(pkg)) }}</span>
                </div>

                <!-- Description -->
                <div class="px-4 py-2 border-b border-slate-50">
                    <input
                        v-model="pkg.description"
                        type="text"
                        placeholder="Package description (optional)…"
                        class="w-full rounded border-0 bg-transparent text-sm text-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-200 px-1 py-0.5"
                    />
                </div>

                <!-- Line items -->
                <div class="divide-y divide-slate-50">
                    <div
                        v-for="(li, idx) in pkg.line_items"
                        :key="idx"
                        class="grid grid-cols-12 gap-2 px-4 py-3 items-start"
                    >
                        <!-- Catalog picker -->
                        <div class="col-span-12 sm:col-span-4">
                            <select
                                class="w-full rounded border border-slate-200 px-2 py-1.5 text-xs text-slate-600 focus:border-slate-400 focus:outline-none"
                                @change="selectCatalog(pkg, idx, ($event.target as HTMLSelectElement).value ? Number(($event.target as HTMLSelectElement).value) : null)"
                            >
                                <option value="">From catalog…</option>
                                <option v-for="cat in catalogItems" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                            <input
                                v-model="li.name"
                                type="text"
                                placeholder="Item name *"
                                class="mt-1 w-full rounded border border-slate-200 px-2 py-1.5 text-sm text-slate-800 focus:border-slate-400 focus:outline-none"
                            />
                        </div>
                        <!-- Description -->
                        <div class="col-span-12 sm:col-span-3">
                            <input
                                v-model="li.description"
                                type="text"
                                placeholder="Description"
                                class="w-full rounded border border-slate-200 px-2 py-1.5 text-sm text-slate-600 focus:border-slate-400 focus:outline-none"
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
                                class="w-full rounded border border-slate-200 px-2 py-1.5 text-sm text-right focus:border-slate-400 focus:outline-none"
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
                                class="w-full rounded border border-slate-200 px-2 py-1.5 text-sm text-right focus:border-slate-400 focus:outline-none"
                            />
                        </div>
                        <!-- Total -->
                        <div class="col-span-3 sm:col-span-1 text-right text-sm font-medium text-slate-700 pt-2">
                            {{ formatCurrency(parseFloat(li.unit_price || '0') * parseFloat(li.quantity || '0')) }}
                        </div>
                        <!-- Taxable + remove -->
                        <div class="col-span-2 sm:col-span-1 flex flex-col items-center gap-1 pt-1">
                            <label class="text-xs text-slate-400 cursor-pointer" title="Taxable">
                                <input v-model="li.is_taxable" type="checkbox" class="rounded" /> Tax
                            </label>
                            <button
                                type="button"
                                class="text-xs text-red-400 hover:text-red-600"
                                @click="removeLineItem(pkg, idx)"
                            >
                                ✕
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Add line item -->
                <div class="border-t border-slate-100 px-4 py-2">
                    <button
                        type="button"
                        class="text-xs font-medium text-blue-600 hover:underline"
                        @click="addLineItem(pkg)"
                    >
                        + Add line item
                    </button>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3">
                <Link
                    :href="isEdit ? `/owner/estimates/${estimate!.id}` : '/owner/estimates'"
                    class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50"
                >
                    Cancel
                </Link>
                <button
                    type="submit"
                    class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                    :disabled="form.processing"
                >
                    {{ isEdit ? 'Update Estimate' : 'Create Estimate' }}
                </button>
            </div>
        </form>
    </OwnerLayout>
</template>

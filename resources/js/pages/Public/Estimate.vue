<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
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

interface Organization {
    id: number;
    name: string;
}

interface Estimate {
    id: number;
    title: string;
    intro: string | null;
    footer: string | null;
    status: string;
    token: string;
    expires_at: string | null;
    accepted_at: string | null;
    accepted_package: string | null;
    declined_at: string | null;
    tax_rate: string;
    customer: { id: number; first_name: string; last_name: string } | null;
    organization: Organization | null;
    packages: Package[];
}

const props = defineProps<{ estimate: Estimate }>();

const page = usePage();
const flash = computed(() => (page.props as any).flash as { success?: string } | undefined);

const TIER_LABELS: Record<string, string> = { good: 'Good', better: 'Better', best: 'Best' };

const selectedTier = ref<string | null>(null);
const submitting = ref(false);

const isAccepted = computed(() => props.estimate.status === 'accepted');
const isDeclined = computed(() => props.estimate.status === 'declined');
const isEditable = computed(() => props.estimate.status === 'sent');

function accept() {
    if (!selectedTier.value) return;
    submitting.value = true;
    router.post(`/estimates/${props.estimate.token}/accept`, { tier: selectedTier.value }, {
        onFinish: () => { submitting.value = false; },
    });
}

function decline() {
    if (!confirm('Are you sure you want to decline this estimate?')) return;
    submitting.value = true;
    router.post(`/estimates/${props.estimate.token}/decline`, {}, {
        onFinish: () => { submitting.value = false; },
    });
}

function formatCurrency(val: string | number): string {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(val));
}

function formatDate(dt: string | null): string {
    if (!dt) return '';
    return new Date(dt).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
}
</script>

<template>
    <Head :title="`Estimate – ${estimate.title}`" />

    <div class="min-h-screen bg-slate-50">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="mx-auto max-w-4xl px-4 py-5 sm:px-6">
                <p v-if="estimate.organization" class="text-sm font-semibold text-slate-500">
                    {{ estimate.organization.name }}
                </p>
                <h1 class="text-2xl font-bold text-slate-900">{{ estimate.title }}</h1>
                <p v-if="estimate.customer" class="mt-1 text-sm text-slate-500">
                    Prepared for {{ estimate.customer.first_name }} {{ estimate.customer.last_name }}
                </p>
                <p v-if="estimate.expires_at" class="mt-1 text-xs text-slate-400">
                    Valid through {{ formatDate(estimate.expires_at) }}
                </p>
            </div>
        </header>

        <main class="mx-auto max-w-4xl px-4 py-8 sm:px-6">

            <!-- Flash message -->
            <div v-if="flash?.success" class="mb-6 rounded-xl bg-green-50 p-4 text-sm font-medium text-green-700 ring-1 ring-green-200">
                {{ flash.success }}
            </div>

            <!-- Accepted banner -->
            <div v-if="isAccepted" class="mb-6 rounded-xl bg-green-50 p-4 ring-1 ring-green-200">
                <p class="font-semibold text-green-700">You've accepted this estimate.</p>
                <p class="mt-0.5 text-sm text-green-600">
                    Selected package: <strong>{{ TIER_LABELS[estimate.accepted_package ?? ''] ?? estimate.accepted_package }}</strong> · {{ formatDate(estimate.accepted_at) }}
                </p>
            </div>

            <!-- Declined banner -->
            <div v-if="isDeclined" class="mb-6 rounded-xl bg-slate-100 p-4 ring-1 ring-slate-200">
                <p class="font-medium text-slate-600">You've declined this estimate.</p>
                <p class="mt-0.5 text-sm text-slate-400">Please contact us if you'd like to discuss further.</p>
            </div>

            <!-- Intro -->
            <p v-if="estimate.intro" class="mb-6 whitespace-pre-wrap text-slate-600">{{ estimate.intro }}</p>

            <!-- Packages — Good / Better / Best cards -->
            <div class="grid gap-4" :class="estimate.packages.length >= 3 ? 'md:grid-cols-3' : estimate.packages.length === 2 ? 'md:grid-cols-2' : 'max-w-sm'">
                <div
                    v-for="pkg in estimate.packages"
                    :key="pkg.id"
                    class="relative flex cursor-pointer flex-col rounded-2xl bg-white shadow-sm ring-2 transition-all"
                    :class="[
                        pkg.is_recommended && !selectedTier ? 'ring-blue-500 shadow-blue-100' : '',
                        selectedTier === pkg.tier ? 'ring-blue-600 shadow-lg shadow-blue-100' : (pkg.is_recommended ? '' : 'ring-slate-200'),
                    ]"
                    @click="isEditable && (selectedTier = pkg.tier)"
                >
                    <!-- Recommended badge -->
                    <div v-if="pkg.is_recommended" class="absolute -top-3 left-1/2 -translate-x-1/2">
                        <span class="rounded-full bg-blue-600 px-3 py-0.5 text-xs font-bold text-white shadow">Most Popular</span>
                    </div>

                    <!-- Tier label -->
                    <div
                        class="rounded-t-2xl px-5 pt-6 pb-4 text-center"
                        :class="pkg.is_recommended ? 'bg-blue-600' : 'bg-slate-800'"
                    >
                        <p class="text-xs font-semibold uppercase tracking-widest text-white/70">{{ TIER_LABELS[pkg.tier] ?? pkg.tier }}</p>
                        <p class="mt-0.5 text-xl font-bold text-white">{{ pkg.label }}</p>
                        <p class="mt-2 text-3xl font-extrabold text-white">{{ formatCurrency(pkg.total) }}</p>
                        <p v-if="Number(pkg.tax_amount) > 0" class="text-xs text-white/60">incl. {{ formatCurrency(pkg.tax_amount) }} tax</p>
                    </div>

                    <!-- Description -->
                    <p v-if="pkg.description" class="border-b border-slate-100 px-5 py-3 text-sm text-slate-500">{{ pkg.description }}</p>

                    <!-- Line items -->
                    <ul class="flex-1 divide-y divide-slate-50 px-5 py-3">
                        <li
                            v-for="li in pkg.line_items"
                            :key="li.id"
                            class="flex items-start justify-between gap-2 py-2 text-sm"
                        >
                            <div>
                                <p class="font-medium text-slate-700">{{ li.name }}</p>
                                <p v-if="li.description" class="text-xs text-slate-400">{{ li.description }}</p>
                            </div>
                            <span class="shrink-0 font-medium text-slate-700">{{ formatCurrency(li.total) }}</span>
                        </li>
                    </ul>

                    <!-- Select radio -->
                    <div v-if="isEditable" class="border-t border-slate-100 px-5 py-4 text-center">
                        <div
                            class="mx-auto flex h-6 w-6 items-center justify-center rounded-full border-2 transition"
                            :class="selectedTier === pkg.tier ? 'border-blue-600 bg-blue-600' : 'border-slate-300'"
                        >
                            <svg v-if="selectedTier === pkg.tier" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 text-white">
                                <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.42 0l-3.5-3.5a1 1 0 111.42-1.42L8.5 12.08l6.79-6.79a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

                    <!-- Accepted indicator -->
                    <div v-if="isAccepted && estimate.accepted_package === pkg.tier" class="border-t border-green-100 bg-green-50 px-5 py-3 text-center text-sm font-semibold text-green-700">
                        ✓ Accepted
                    </div>
                </div>
            </div>

            <!-- Footer text -->
            <p v-if="estimate.footer" class="mt-6 whitespace-pre-wrap text-sm text-slate-500">{{ estimate.footer }}</p>

            <!-- Accept / Decline CTAs -->
            <div v-if="isEditable" class="mt-8 flex flex-wrap justify-center gap-3">
                <button
                    type="button"
                    class="rounded-xl bg-blue-600 px-8 py-3 text-base font-semibold text-white shadow hover:bg-blue-700 disabled:opacity-50"
                    :disabled="!selectedTier || submitting"
                    @click="accept"
                >
                    Accept {{ selectedTier ? TIER_LABELS[selectedTier] + ' Package' : 'Selected Package' }}
                </button>
                <button
                    type="button"
                    class="rounded-xl border border-slate-200 px-8 py-3 text-base font-medium text-slate-500 hover:bg-slate-100 disabled:opacity-50"
                    :disabled="submitting"
                    @click="decline"
                >
                    Decline
                </button>
            </div>

            <!-- Footer branding -->
            <p class="mt-12 text-center text-xs text-slate-300">
                Powered by FieldOps Hub
            </p>
        </main>
    </div>
</template>

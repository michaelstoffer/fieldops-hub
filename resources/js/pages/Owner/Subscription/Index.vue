<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Subscription', href: '/owner/subscription' },
];

const props = defineProps<{
    subscription: {
        plan: string;
        status: string;
        billing_interval: string;
        trial_ends_at: string | null;
        current_period_end: string | null;
        days_remaining: number;
        is_trialing: boolean;
    } | null;
    current_plan: string;
    active_plan: string;
    plans: Array<{
        key: string;
        label: string;
        monthly: number;
        annual: number;
        tech_limit: number | null;
    }>;
}>();

const billingAnnual = ref(false);

const form = useForm({
    plan: props.current_plan,
    interval: 'monthly',
});

function subscribe(planKey: string) {
    form.plan = planKey;
    form.interval = billingAnnual.value ? 'annual' : 'monthly';
    form.post(route('owner.subscription.checkout'));
}

const trialBadgeColor = computed(() => {
    if (!props.subscription?.is_trialing) return '';
    const days = props.subscription.days_remaining;
    if (days <= 2) return 'bg-red-100 text-red-700 border-red-200';
    if (days <= 5) return 'bg-amber-100 text-amber-700 border-amber-200';
    return 'bg-blue-100 text-blue-700 border-blue-200';
});

function formatDate(iso: string | null): string {
    if (!iso) return '—';
    return new Date(iso).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Subscription" />

        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-10 space-y-8">

            <!-- Current status card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">Current plan</h2>

                <div v-if="subscription" class="flex flex-col sm:flex-row sm:items-center gap-6">
                    <div class="flex-1 space-y-2">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl font-bold text-slate-900 capitalize">{{ current_plan }}</span>
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold capitalize"
                                  :class="trialBadgeColor">
                                {{ subscription.is_trialing ? `Trial — ${subscription.days_remaining} day${subscription.days_remaining === 1 ? '' : 's'} left` : subscription.status }}
                            </span>
                        </div>

                        <p v-if="subscription.is_trialing" class="text-sm text-slate-500">
                            Trial ends {{ formatDate(subscription.trial_ends_at) }}.
                            Add a payment method to continue after your trial.
                        </p>
                        <p v-else class="text-sm text-slate-500">
                            Next billing date: {{ formatDate(subscription.current_period_end) }}.
                        </p>

                        <div v-if="subscription.is_trialing && current_plan === 'starter'" class="text-xs text-blue-600 font-medium">
                            Your trial includes Growth plan features. After the trial, you'll use Starter features.
                        </div>
                    </div>
                </div>

                <div v-else class="text-slate-500 text-sm">No active subscription found.</div>
            </div>

            <!-- Billing toggle + plans -->
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-slate-900">
                        {{ subscription?.is_trialing ? 'Subscribe to keep your access' : 'Change plan' }}
                    </h2>
                    <div class="flex items-center gap-3">
                        <span class="text-sm" :class="!billingAnnual ? 'text-slate-900 font-medium' : 'text-slate-400'">Monthly</span>
                        <button
                            type="button"
                            @click="billingAnnual = !billingAnnual"
                            class="relative inline-flex h-6 w-11 rounded-full transition-colors"
                            :class="billingAnnual ? 'bg-blue-600' : 'bg-slate-200'"
                        >
                            <span class="inline-block h-4 w-4 rounded-full bg-white shadow translate-y-1 transition-transform"
                                  :class="billingAnnual ? 'translate-x-6' : 'translate-x-1'"></span>
                        </button>
                        <span class="text-sm" :class="billingAnnual ? 'text-slate-900 font-medium' : 'text-slate-400'">
                            Annual <span class="text-teal-600 font-semibold">(save 20%)</span>
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div
                        v-for="plan in plans"
                        :key="plan.key"
                        class="relative rounded-2xl border-2 p-6 flex flex-col"
                        :class="plan.key === 'growth'
                            ? 'border-blue-500 bg-slate-900'
                            : 'border-slate-200 bg-white'"
                    >
                        <div v-if="plan.key === 'growth'" class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <span class="inline-flex rounded-full bg-blue-600 px-3 py-1 text-xs font-bold text-white shadow">Most Popular</span>
                        </div>

                        <!-- Current badge -->
                        <div v-if="plan.key === current_plan" class="absolute top-4 right-4">
                            <span class="inline-flex items-center rounded-full bg-teal-100 px-2 py-0.5 text-xs font-semibold text-teal-700">Current</span>
                        </div>

                        <h3 class="text-base font-bold mb-1" :class="plan.key === 'growth' ? 'text-white' : 'text-slate-900'">{{ plan.label }}</h3>
                        <p class="text-xs mb-4" :class="plan.key === 'growth' ? 'text-slate-400' : 'text-slate-500'">
                            {{ plan.tech_limit === null ? 'Unlimited technicians' : `Up to ${plan.tech_limit} technicians` }}
                        </p>

                        <div class="mb-5">
                            <span class="text-3xl font-black" :class="plan.key === 'growth' ? 'text-white' : 'text-slate-900'">
                                ${{ billingAnnual ? plan.annual : plan.monthly }}
                            </span>
                            <span class="text-sm" :class="plan.key === 'growth' ? 'text-slate-400' : 'text-slate-500'">/mo</span>
                        </div>

                        <button
                            type="button"
                            :disabled="form.processing"
                            @click="subscribe(plan.key)"
                            class="w-full rounded-xl py-2.5 text-sm font-semibold transition-colors disabled:opacity-50"
                            :class="plan.key === 'growth'
                                ? 'bg-blue-600 hover:bg-blue-500 text-white'
                                : 'bg-slate-100 hover:bg-slate-200 text-slate-900'"
                        >
                            {{ plan.key === current_plan ? 'Renew' : 'Subscribe' }}
                        </button>
                    </div>
                </div>

                <p class="mt-4 text-xs text-slate-400 text-center">
                    You'll be taken to Stripe to complete your payment securely.
                </p>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    current_plan: string;
    plans: Array<{
        key: string;
        label: string;
        monthly: number;
        annual: number;
        tech_limit: number | null;
    }>;
}>();

const billingAnnual = ref(false);

const form = useForm({ plan: props.current_plan, interval: 'monthly' });

function subscribe(planKey: string) {
    form.plan = planKey;
    form.interval = billingAnnual.value ? 'annual' : 'monthly';
    form.post(route('owner.subscription.checkout'));
}
</script>

<template>
    <Head title="Trial Ended — FieldOps Hub" />

    <div class="min-h-screen bg-slate-900 flex flex-col items-center justify-center px-4 py-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_#1e40af30_0%,_transparent_60%)]"></div>
        <div class="absolute inset-0 opacity-[0.03]"
             style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 40px 40px;"></div>

        <div class="relative z-10 w-full max-w-2xl">

            <!-- Logo -->
            <div class="flex items-center justify-center gap-2.5 mb-10">
                <div class="h-9 w-9 rounded-xl bg-blue-500 flex items-center justify-center shadow-lg shadow-blue-500/30">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="text-white font-semibold text-lg tracking-tight">FieldOps Hub</span>
            </div>

            <!-- Message -->
            <div class="text-center mb-10">
                <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">Your trial has ended</h1>
                <p class="text-slate-400 text-lg max-w-lg mx-auto">
                    Your 14-day trial is up. Subscribe to restore access to your data, jobs, and team — nothing has been deleted.
                </p>
            </div>

            <!-- Billing toggle -->
            <div class="flex items-center justify-center gap-3 mb-8">
                <span class="text-sm font-medium" :class="!billingAnnual ? 'text-white' : 'text-slate-400'">Monthly</span>
                <button type="button" @click="billingAnnual = !billingAnnual"
                        class="relative inline-flex h-6 w-11 rounded-full transition-colors"
                        :class="billingAnnual ? 'bg-blue-600' : 'bg-slate-600'">
                    <span class="inline-block h-4 w-4 rounded-full bg-white shadow translate-y-1 transition-transform"
                          :class="billingAnnual ? 'translate-x-6' : 'translate-x-1'"></span>
                </button>
                <span class="text-sm font-medium" :class="billingAnnual ? 'text-white' : 'text-slate-400'">
                    Annual <span class="text-teal-400 font-semibold">(save 20%)</span>
                </span>
            </div>

            <!-- Plan cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div v-for="plan in plans" :key="plan.key"
                     class="relative rounded-2xl p-6 flex flex-col"
                     :class="plan.key === 'growth'
                         ? 'bg-blue-600 ring-2 ring-blue-400'
                         : 'bg-white/5 border border-white/10'">

                    <div v-if="plan.key === 'growth'" class="absolute -top-3 left-1/2 -translate-x-1/2">
                        <span class="inline-flex rounded-full bg-blue-400 px-3 py-1 text-xs font-bold text-white shadow">Most Popular</span>
                    </div>

                    <div v-if="plan.key === current_plan" class="absolute top-3 right-3">
                        <span class="inline-flex rounded-full bg-white/20 px-2 py-0.5 text-xs font-semibold text-white">Your plan</span>
                    </div>

                    <h3 class="text-base font-bold text-white mb-1">{{ plan.label }}</h3>
                    <p class="text-xs text-white/60 mb-4">{{ plan.tech_limit === null ? 'Unlimited technicians' : `Up to ${plan.tech_limit} technicians` }}</p>

                    <div class="mb-5">
                        <span class="text-3xl font-black text-white">${{ billingAnnual ? plan.annual : plan.monthly }}</span>
                        <span class="text-sm text-white/60">/mo</span>
                    </div>

                    <button type="button" :disabled="form.processing" @click="subscribe(plan.key)"
                            class="w-full rounded-xl py-2.5 text-sm font-semibold transition-colors disabled:opacity-50"
                            :class="plan.key === 'growth'
                                ? 'bg-white text-blue-700 hover:bg-blue-50'
                                : 'bg-white/10 hover:bg-white/20 text-white'">
                        Subscribe
                    </button>
                </div>
            </div>

            <p class="text-center text-xs text-slate-500">Secure checkout via Stripe. Cancel any time.</p>
        </div>
    </div>
</template>

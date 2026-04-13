<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { login, register } from '@/routes';
import { ref, computed } from 'vue';

const PLANS = [
    {
        key: 'starter',
        name: 'Starter',
        price: 79,
        seats: 'Up to 3 technicians',
        description: 'Small crew getting organized.',
    },
    {
        key: 'growth',
        name: 'Growth',
        price: 149,
        seats: 'Up to 10 technicians',
        description: 'For growing field operations.',
        popular: true,
    },
    {
        key: 'pro',
        name: 'Pro',
        price: 249,
        seats: 'Unlimited technicians',
        description: 'Established, unlimited scale.',
    },
];

// Read ?plan= from URL query string
const validPlans = ['starter', 'growth', 'pro'];
const queryPlan = new URLSearchParams(window.location.search).get('plan') ?? '';

// Step 1 = plan selection, Step 2 = account details
// Skip to step 2 if a valid plan was pre-selected from the pricing page
const step = ref<1 | 2>(validPlans.includes(queryPlan) ? 2 : 1);
const selectedPlan = ref<string>(validPlans.includes(queryPlan) ? queryPlan : 'growth');

const selectedPlanLabel = computed(() =>
    PLANS.find(p => p.key === selectedPlan.value)?.name ?? 'Growth'
);

function choosePlan(key: string) {
    selectedPlan.value = key;
}

function goToDetails() {
    step.value = 2;
}

const form = useForm({
    plan: selectedPlan,
    company_name: '',
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.plan = selectedPlan.value;
    form.post(register.url(), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Create your account — FieldOps Hub" />

    <div class="min-h-screen flex">

        <!-- ── Left panel ────────────────────────────────────────────────────── -->
        <div class="hidden lg:flex lg:w-1/2 relative flex-col justify-between p-12 bg-slate-900 overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_#1e40af55_0%,_transparent_60%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_right,_#0f766e44_0%,_transparent_60%)]"></div>
            <div class="absolute inset-0 opacity-[0.04]"
                 style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 40px 40px;"></div>

            <!-- Logo -->
            <div class="relative z-10 flex items-center gap-3">
                <div class="h-9 w-9 rounded-xl bg-blue-500 flex items-center justify-center shadow-lg shadow-blue-500/30">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="text-white font-semibold text-lg tracking-tight">FieldOps Hub</span>
            </div>

            <!-- Branding copy -->
            <div class="relative z-10 space-y-6">
                <h1 class="text-4xl xl:text-5xl font-bold text-white leading-tight">
                    Your ops.<br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-teal-400">
                        Under control.
                    </span>
                </h1>
                <p class="text-slate-400 text-lg leading-relaxed max-w-sm">
                    14-day free trial. No credit card required. Your organization is created automatically.
                </p>
                <div class="flex flex-wrap gap-2 pt-2">
                    <span v-for="feat in ['Live dispatch', 'Job tracking', 'Invoicing', 'Estimates', 'Reporting']"
                          :key="feat"
                          class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-slate-300 text-sm">
                        <span class="h-1.5 w-1.5 rounded-full bg-teal-400"></span>
                        {{ feat }}
                    </span>
                </div>
            </div>

            <div class="relative z-10">
                <p class="text-slate-500 text-sm">&copy; {{ new Date().getFullYear() }} FieldOps Hub</p>
            </div>
        </div>

        <!-- ── Right panel ───────────────────────────────────────────────────── -->
        <div class="flex-1 flex flex-col items-center justify-center px-6 py-12 bg-slate-50">

            <!-- Mobile logo -->
            <div class="lg:hidden flex items-center gap-2 mb-10">
                <div class="h-8 w-8 rounded-lg bg-blue-600 flex items-center justify-center">
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="font-semibold text-slate-800 text-base tracking-tight">FieldOps Hub</span>
            </div>

            <!-- ── Step 1: Plan selection ───────────────────────────────────── -->
            <div v-if="step === 1" class="w-full max-w-lg">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-slate-900">Choose your plan</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        14-day free trial on all plans. Starter and Growth trials both include full Growth features.
                    </p>
                </div>

                <div class="space-y-3">
                    <button
                        v-for="p in PLANS"
                        :key="p.key"
                        type="button"
                        @click="choosePlan(p.key)"
                        class="w-full flex items-center gap-4 rounded-xl border-2 p-4 text-left transition-all"
                        :class="selectedPlan === p.key
                            ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500'
                            : 'border-slate-200 bg-white hover:border-slate-300'"
                    >
                        <!-- Radio indicator -->
                        <div class="shrink-0 h-5 w-5 rounded-full border-2 flex items-center justify-center"
                             :class="selectedPlan === p.key ? 'border-blue-600' : 'border-slate-300'">
                            <div v-if="selectedPlan === p.key" class="h-2.5 w-2.5 rounded-full bg-blue-600"></div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-slate-900">{{ p.name }}</span>
                                <span v-if="p.popular"
                                      class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">
                                    Most Popular
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">{{ p.seats }} &bull; {{ p.description }}</p>
                        </div>

                        <div class="shrink-0 text-right">
                            <span class="font-bold text-slate-900">${{ p.price }}</span>
                            <span class="text-xs text-slate-400">/mo</span>
                        </div>
                    </button>
                </div>

                <button
                    type="button"
                    @click="goToDetails"
                    class="mt-6 w-full flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition-colors"
                >
                    Continue with {{ selectedPlanLabel }}
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </button>

                <p class="mt-4 text-center text-sm text-slate-500">
                    Already have an account?
                    <Link :href="login()" class="text-blue-600 hover:text-blue-700 font-medium">Sign in</Link>
                </p>
            </div>

            <!-- ── Step 2: Account details ─────────────────────────────────── -->
            <div v-else class="w-full max-w-sm">
                <div class="mb-8">
                    <!-- Back + plan indicator -->
                    <div class="flex items-center gap-3 mb-5">
                        <button type="button" @click="step = 1"
                                class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </button>
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                            {{ selectedPlanLabel }} plan — 14-day free trial
                        </span>
                    </div>

                    <h2 class="text-2xl font-bold text-slate-900">Create your account</h2>
                    <p class="mt-1 text-sm text-slate-500">Get your team up and running today.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-5">

                    <div>
                        <label for="company_name" class="block text-sm font-medium text-slate-700 mb-1.5">Company name</label>
                        <input
                            id="company_name"
                            v-model="form.company_name"
                            type="text"
                            autocomplete="organization"
                            required
                            autofocus
                            placeholder="Acme Field Services"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
                            :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400/20': form.errors.company_name }"
                        />
                        <p v-if="form.errors.company_name" class="mt-1.5 text-xs text-red-600">{{ form.errors.company_name }}</p>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Your name</label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            autocomplete="name"
                            required
                            placeholder="Jane Smith"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
                            :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400/20': form.errors.name }"
                        />
                        <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Work email</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            autocomplete="username"
                            required
                            placeholder="you@company.com"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
                            :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400/20': form.errors.email }"
                        />
                        <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            autocomplete="new-password"
                            required
                            placeholder="••••••••"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
                            :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400/20': form.errors.password }"
                        />
                        <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-600">{{ form.errors.password }}</p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Confirm password</label>
                        <input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            required
                            placeholder="••••••••"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
                            :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400/20': form.errors.password_confirmation }"
                        />
                        <p v-if="form.errors.password_confirmation" class="mt-1.5 text-xs text-red-600">{{ form.errors.password_confirmation }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <svg v-if="form.processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ form.processing ? 'Creating account…' : 'Start free trial' }}
                    </button>

                    <p class="text-center text-xs text-slate-400">
                        No credit card required. Cancel any time.
                    </p>

                    <p class="text-center text-sm text-slate-500">
                        Already have an account?
                        <Link :href="login()" class="text-blue-600 hover:text-blue-700 font-medium">Sign in</Link>
                    </p>
                </form>
            </div>
        </div>
    </div>
</template>

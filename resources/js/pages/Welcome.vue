<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { login, register } from '@/routes';
import { computed } from 'vue';

function registerUrl(plan?: string): string {
    return plan ? `${register().url}?plan=${plan}` : register().url;
}

const props = defineProps<{
    foundingOffer: {
        discount_percent: number;
        remaining: number;
        max_uses: number;
    } | null;
}>();

const spotsLeft = computed(() => props.foundingOffer?.remaining ?? 0);
const spotsTotal = computed(() => props.foundingOffer?.max_uses ?? 10);
const spotsUsed = computed(() => spotsTotal.value - spotsLeft.value);
const spotsBarWidth = computed(() => `${(spotsUsed.value / spotsTotal.value) * 100}%`);

// Pricing tiers — Growth in the middle; shown first in hero copy to anchor perception
const tiers = [
    {
        name: 'Starter',
        highlight: false,
        badge: null,
        monthly: 79,
        annual: 63,
        description: 'Perfect for small crews just getting organized.',
        seats: 'Up to 3 technicians',
        features: [
            'Up to 3 technician seats',
            'Customers & properties',
            'Job scheduling & tracking',
            'Estimates with online acceptance',
            'Invoicing & payment recording',
            'Technician mobile PWA',
            'Standard reporting',
            'Email support',
        ],
    },
    {
        name: 'Growth',
        highlight: true,
        badge: 'Most Popular',
        monthly: 149,
        annual: 119,
        description: 'For growing operations managing a real crew.',
        seats: 'Up to 10 technicians',
        features: [
            'Everything in Starter',
            'Up to 10 technician seats',
            'Automated SMS & email notifications',
            'Live dispatch map with GPS tracking',
            'Multi-tier estimate packages',
            'Stripe online payments',
            'Job profitability & technician reports',
            'Priority support',
        ],
    },
    {
        name: 'Pro',
        highlight: false,
        badge: null,
        monthly: 249,
        annual: 199,
        description: 'Unlimited scale for established operations.',
        seats: 'Unlimited technicians',
        features: [
            'Everything in Growth',
            'Unlimited technician seats',
            'Dedicated onboarding call',
            'Custom integrations on request',
            'SLA-backed uptime',
            'White-glove migration support',
            'Early access to new features',
            'Dedicated Slack channel',
        ],
    },
];

const features = [
    {
        icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        title: 'Smart Job Scheduling',
        description: 'Create and assign jobs in seconds. Track status in real time from scheduled through completion — every step visible to your whole team.',
    },
    {
        icon: 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z',
        title: 'Live Dispatch & GPS Tracking',
        description: 'See exactly where every technician is on a live map. Assign jobs to the nearest available tech and watch their trail update in real time.',
    },
    {
        icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        title: 'Estimates Customers Accept Online',
        description: 'Send Good/Better/Best estimate packages with a shareable link. Customers accept or decline online — no phone tag required.',
    },
    {
        icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
        title: 'Invoicing & Online Payments',
        description: 'Generate invoices from completed jobs. Customers pay online via Stripe. Record cash, check, or card payments with a full audit trail.',
    },
    {
        icon: 'M12 18h.01M8 21h8a2 2 0 002-2v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2a2 2 0 002 2zM8 3h8a2 2 0 012 2v8a2 2 0 01-2 2H8a2 2 0 01-2-2V5a2 2 0 012-2z',
        title: 'Technician Mobile App',
        description: 'Your field team gets a mobile-optimized PWA that works offline. Check job details, update status, and complete checklists from the field.',
    },
    {
        icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        title: 'Reports That Drive Decisions',
        description: 'Owner dashboard with live KPIs. Drill into job profitability by type, revenue per technician, and trends over time.',
    },
];

const faqs = [
    {
        q: 'Can I switch plans later?',
        a: 'Yes. Upgrade or downgrade any time from your billing settings. Changes take effect immediately and are prorated.',
    },
    {
        q: 'How does annual billing work?',
        a: 'Annual plans are billed once a year at roughly 20% off the monthly rate. You can switch to annual from your billing settings at any time.',
    },
    {
        q: 'What happens when I hit my technician seat limit?',
        a: "You'll be prompted to upgrade before adding a new technician. Your existing team and data are never affected.",
    },
    {
        q: 'Is there a free trial?',
        a: 'We offer a 14-day trial on all plans — no credit card required. Just sign up and your organization is ready to go.',
    },
    {
        q: 'What integrations are included?',
        a: 'Stripe for online payments, Twilio for SMS notifications, SendGrid for email, and Google Maps for live dispatch. Each is configurable per organization.',
    },
    {
        q: 'How is data isolated between organizations?',
        a: 'Every record — customers, jobs, invoices, technicians — is scoped to your organization. There is no cross-tenant data access by design.',
    },
];

import { ref } from 'vue';
const openFaq = ref<number | null>(null);
const billingAnnual = ref(false);

function toggleFaq(i: number) {
    openFaq.value = openFaq.value === i ? null : i;
}

function getPrice(tier: typeof tiers[0]) {
    return billingAnnual.value ? tier.annual : tier.monthly;
}
</script>

<template>
    <Head title="FieldOps Hub — Field Service Management for Growing Teams" />

    <div class="min-h-screen bg-slate-50 text-slate-900 antialiased">

        <!-- ── Nav ──────────────────────────────────────────────────────────── -->
        <header class="sticky top-0 z-50 bg-slate-900/95 backdrop-blur-sm border-b border-white/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center gap-2.5">
                        <div class="h-8 w-8 rounded-lg bg-blue-500 flex items-center justify-center shadow-md shadow-blue-500/30">
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-white font-semibold tracking-tight">FieldOps Hub</span>
                    </div>

                    <!-- Nav links -->
                    <nav class="hidden md:flex items-center gap-6">
                        <a href="#features" class="text-sm text-slate-400 hover:text-white transition-colors">Features</a>
                        <a href="#pricing" class="text-sm text-slate-400 hover:text-white transition-colors">Pricing</a>
                        <a href="#faq" class="text-sm text-slate-400 hover:text-white transition-colors">FAQ</a>
                    </nav>

                    <!-- Auth links -->
                    <div class="flex items-center gap-3">
                        <Link :href="login().url" class="text-sm text-slate-400 hover:text-white transition-colors font-medium">
                            Sign in
                        </Link>
                        <Link
                            :href="registerUrl()"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 px-4 py-2 text-sm font-semibold text-white transition-colors shadow-sm"
                        >
                            Start free trial
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- ── Hero ─────────────────────────────────────────────────────────── -->
        <section class="relative bg-slate-900 overflow-hidden">
            <!-- Background glows -->
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_#1e40af40_0%,_transparent_60%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_right,_#0f766e30_0%,_transparent_60%)]"></div>
            <!-- Grid overlay -->
            <div class="absolute inset-0 opacity-[0.03]"
                 style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 40px 40px;">
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-32 text-center">
                <!-- Founding member pill — shown only when offer is active -->
                <div v-if="foundingOffer" class="inline-flex items-center gap-2 rounded-full bg-amber-400/10 border border-amber-400/30 px-4 py-1.5 mb-8">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    <span class="text-amber-300 text-sm font-medium">
                        Founding Member spots open — {{ foundingOffer.remaining }} of {{ foundingOffer.max_uses }} remaining
                    </span>
                </div>

                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold text-white leading-[1.08] tracking-tight">
                    Field service<br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-teal-400">
                        under control.
                    </span>
                </h1>

                <p class="mt-6 text-lg sm:text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed">
                    Dispatch technicians, track jobs in real time, send estimates, collect payments — all from one fast, modern platform built for field service teams.
                </p>

                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <Link
                        :href="registerUrl()"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-500 px-8 py-3.5 text-base font-semibold text-white transition-colors shadow-lg shadow-blue-600/25"
                    >
                        Start your free 14-day trial
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </Link>
                    <a
                        href="#pricing"
                        class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl border border-white/10 hover:border-white/20 px-8 py-3.5 text-base font-medium text-slate-300 hover:text-white transition-colors"
                    >
                        See pricing
                    </a>
                </div>

                <p class="mt-4 text-sm text-slate-500">No credit card required. Cancel any time.</p>

                <!-- Feature pill row -->
                <div class="mt-14 flex flex-wrap items-center justify-center gap-2">
                    <span v-for="feat in ['Live dispatch', 'GPS tracking', 'Job scheduling', 'Estimates', 'Invoicing', 'Online payments', 'Technician PWA', 'Reporting']"
                          :key="feat"
                          class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-slate-400 text-sm">
                        <span class="h-1.5 w-1.5 rounded-full bg-teal-400"></span>
                        {{ feat }}
                    </span>
                </div>
            </div>
        </section>

        <!-- ── Social proof strip ───────────────────────────────────────────── -->
        <div class="bg-slate-800 border-y border-white/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex flex-col sm:flex-row items-center justify-center gap-6 sm:gap-12 text-center">
                    <div>
                        <div class="text-2xl font-bold text-white">HVAC</div>
                        <div class="text-xs text-slate-500 mt-0.5">Heating & Cooling</div>
                    </div>
                    <div class="hidden sm:block h-8 w-px bg-white/10"></div>
                    <div>
                        <div class="text-2xl font-bold text-white">Plumbing</div>
                        <div class="text-xs text-slate-500 mt-0.5">Residential & Commercial</div>
                    </div>
                    <div class="hidden sm:block h-8 w-px bg-white/10"></div>
                    <div>
                        <div class="text-2xl font-bold text-white">Electrical</div>
                        <div class="text-xs text-slate-500 mt-0.5">Licensed Contractors</div>
                    </div>
                    <div class="hidden sm:block h-8 w-px bg-white/10"></div>
                    <div>
                        <div class="text-2xl font-bold text-white">Landscaping</div>
                        <div class="text-xs text-slate-500 mt-0.5">Maintenance & Install</div>
                    </div>
                    <div class="hidden sm:block h-8 w-px bg-white/10"></div>
                    <div>
                        <div class="text-2xl font-bold text-white">& More</div>
                        <div class="text-xs text-slate-500 mt-0.5">Any field trade</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Features ─────────────────────────────────────────────────────── -->
        <section id="features" class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 border border-blue-100 px-4 py-1.5 mb-4">
                        <span class="text-blue-700 text-sm font-medium">Built for the field</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-slate-900">
                        Everything your team needs.<br class="hidden sm:block"/> Nothing they don't.
                    </h2>
                    <p class="mt-4 text-lg text-slate-500 max-w-2xl mx-auto">
                        FieldOps Hub is purpose-built for field service businesses — not adapted from generic project management software.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div
                        v-for="feature in features"
                        :key="feature.title"
                        class="group relative rounded-2xl border border-slate-100 bg-slate-50 p-8 hover:border-blue-100 hover:bg-blue-50/30 transition-colors"
                    >
                        <div class="h-11 w-11 rounded-xl bg-blue-100 flex items-center justify-center mb-5 group-hover:bg-blue-600 transition-colors">
                            <svg class="h-5 w-5 text-blue-600 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="feature.icon" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ feature.title }}</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── How it works ─────────────────────────────────────────────────── -->
        <section class="py-24 bg-slate-900 relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_#1e3a5f20_0%,_transparent_70%)]"></div>
            <div class="absolute inset-0 opacity-[0.025]"
                 style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 40px 40px;">
            </div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white">From request to invoice in one flow</h2>
                    <p class="mt-4 text-lg text-slate-400 max-w-2xl mx-auto">
                        Every step of the job lifecycle is connected. No copy-paste between systems.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="(step, i) in [
                        { number: '01', title: 'Send an estimate', body: 'Build a tiered Good/Better/Best estimate and send the customer a link to accept online.' },
                        { number: '02', title: 'Schedule the job', body: 'Convert the accepted estimate to a job. Assign it to a technician and it appears on their mobile app instantly.' },
                        { number: '03', title: 'Dispatch & track', body: 'Watch your technicians on a live map. Automated SMS and email keep the customer informed every step.' },
                        { number: '04', title: 'Invoice & collect', body: 'Generate an invoice from the completed job. Customers pay online via Stripe or you record payment your way.' },
                    ]" :key="i" class="relative">
                        <!-- Connector line -->
                        <div v-if="i < 3" class="hidden lg:block absolute top-8 left-full w-6 h-px bg-gradient-to-r from-blue-500/40 to-transparent z-10"></div>

                        <div class="rounded-2xl bg-white/5 border border-white/10 p-7 h-full">
                            <div class="text-4xl font-black text-blue-500/30 mb-4 leading-none">{{ step.number }}</div>
                            <h3 class="text-base font-semibold text-white mb-2">{{ step.title }}</h3>
                            <p class="text-slate-400 text-sm leading-relaxed">{{ step.body }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Pricing ──────────────────────────────────────────────────────── -->
        <section id="pricing" class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-4">
                    <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 border border-blue-100 px-4 py-1.5 mb-4">
                        <span class="text-blue-700 text-sm font-medium">Simple, transparent pricing</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-slate-900">
                        Pay for your team size.<br class="hidden sm:block"/> Nothing more.
                    </h2>
                    <p class="mt-4 text-lg text-slate-500 max-w-xl mx-auto">
                        One price per organization. Unlimited customers, jobs, invoices, and estimates on every plan.
                    </p>
                </div>

                <!-- Billing toggle -->
                <div class="flex items-center justify-center gap-3 mt-8 mb-12">
                    <span class="text-sm font-medium" :class="!billingAnnual ? 'text-slate-900' : 'text-slate-400'">Monthly</span>
                    <button
                        type="button"
                        @click="billingAnnual = !billingAnnual"
                        class="relative inline-flex h-6 w-11 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        :class="billingAnnual ? 'bg-blue-600' : 'bg-slate-200'"
                    >
                        <span
                            class="inline-block h-4 w-4 rounded-full bg-white shadow translate-y-1 transition-transform"
                            :class="billingAnnual ? 'translate-x-6' : 'translate-x-1'"
                        ></span>
                    </button>
                    <span class="text-sm font-medium" :class="billingAnnual ? 'text-slate-900' : 'text-slate-400'">
                        Annual
                        <span class="ml-1.5 inline-flex items-center rounded-full bg-teal-50 border border-teal-100 px-2 py-0.5 text-xs font-semibold text-teal-700">Save 20%</span>
                    </span>
                </div>

                <!-- Founding member banner — removable block -->
                <div v-if="foundingOffer" class="mb-10 rounded-2xl bg-gradient-to-r from-amber-500/10 via-amber-400/10 to-orange-400/10 border border-amber-400/30 p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex items-center rounded-full bg-amber-400/20 border border-amber-400/40 px-2.5 py-0.5 text-xs font-semibold text-amber-700">
                                    Founding Member Offer
                                </span>
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900">
                                {{ foundingOffer.discount_percent }}% off for life — in exchange for your feedback.
                            </h3>
                            <p class="mt-1 text-sm text-slate-600">
                                We're looking for our first {{ foundingOffer.max_uses }} customers to shape the product. Lock in a permanent {{ foundingOffer.discount_percent }}% discount. No code needed — discount applied automatically at signup.
                            </p>
                            <!-- Progress bar -->
                            <div class="mt-3 flex items-center gap-3">
                                <div class="flex-1 h-2 rounded-full bg-amber-100 overflow-hidden">
                                    <div
                                        class="h-full rounded-full bg-amber-400 transition-all duration-500"
                                        :style="{ width: spotsBarWidth }"
                                    ></div>
                                </div>
                                <span class="text-xs font-semibold text-amber-700 whitespace-nowrap">
                                    {{ spotsLeft }} spot{{ spotsLeft === 1 ? '' : 's' }} left
                                </span>
                            </div>
                        </div>
                        <Link
                            :href="registerUrl()"
                            class="shrink-0 inline-flex items-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-400 px-6 py-3 text-sm font-bold text-white transition-colors shadow-lg shadow-amber-500/25"
                        >
                            Claim my spot
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </Link>
                    </div>
                </div>
                <!-- /Founding member banner -->

                <!-- Pricing cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                    <div
                        v-for="tier in tiers"
                        :key="tier.name"
                        class="relative rounded-2xl border p-8 flex flex-col"
                        :class="tier.highlight
                            ? 'border-blue-500 bg-slate-900 shadow-xl shadow-blue-500/10 ring-1 ring-blue-500'
                            : 'border-slate-200 bg-white'"
                    >
                        <!-- Badge -->
                        <div v-if="tier.badge" class="absolute -top-3.5 left-1/2 -translate-x-1/2">
                            <span class="inline-flex items-center rounded-full bg-blue-600 px-3 py-1 text-xs font-bold text-white shadow">
                                {{ tier.badge }}
                            </span>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-bold mb-1" :class="tier.highlight ? 'text-white' : 'text-slate-900'">
                                {{ tier.name }}
                            </h3>
                            <p class="text-sm" :class="tier.highlight ? 'text-slate-400' : 'text-slate-500'">
                                {{ tier.description }}
                            </p>
                        </div>

                        <!-- Price -->
                        <div class="mb-2">
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-black" :class="tier.highlight ? 'text-white' : 'text-slate-900'">
                                    ${{ getPrice(tier) }}
                                </span>
                                <span class="text-sm" :class="tier.highlight ? 'text-slate-400' : 'text-slate-500'">/mo</span>
                            </div>
                            <p class="text-xs mt-1" :class="tier.highlight ? 'text-slate-500' : 'text-slate-400'">
                                {{ billingAnnual ? 'Billed annually' : 'Billed monthly' }}
                            </p>
                        </div>

                        <!-- Seat label -->
                        <div class="mb-6">
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold"
                                :class="tier.highlight ? 'bg-blue-500/20 text-blue-300' : 'bg-slate-100 text-slate-600'"
                            >
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ tier.seats }}
                            </span>
                        </div>

                        <!-- CTA -->
                        <Link
                            :href="registerUrl(tier.name.toLowerCase())"
                            class="block w-full text-center rounded-xl px-4 py-2.5 text-sm font-semibold transition-colors mb-8"
                            :class="tier.highlight
                                ? 'bg-blue-600 hover:bg-blue-500 text-white shadow-sm'
                                : 'bg-slate-100 hover:bg-slate-200 text-slate-900'"
                        >
                            Start free trial
                        </Link>

                        <!-- Feature list -->
                        <ul class="space-y-3 flex-1">
                            <li
                                v-for="feat in tier.features"
                                :key="feat"
                                class="flex items-start gap-2.5 text-sm"
                                :class="tier.highlight ? 'text-slate-300' : 'text-slate-600'"
                            >
                                <svg class="h-4 w-4 mt-0.5 shrink-0" :class="tier.highlight ? 'text-teal-400' : 'text-teal-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ feat }}
                            </li>
                        </ul>
                    </div>
                </div>

                <p class="mt-8 text-center text-sm text-slate-400">
                    All plans include a 14-day free trial. No credit card required.
                    <a href="#faq" class="text-blue-600 hover:text-blue-700 font-medium">Questions? See the FAQ.</a>
                </p>
            </div>
        </section>

        <!-- ── FAQ ──────────────────────────────────────────────────────────── -->
        <section id="faq" class="py-24 bg-slate-50">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14">
                    <h2 class="text-3xl sm:text-4xl font-bold text-slate-900">Frequently asked questions</h2>
                    <p class="mt-4 text-lg text-slate-500">Can't find what you're looking for? <a :href="'mailto:hello@fieldopshub.com'" class="text-blue-600 hover:text-blue-700 font-medium">Reach out.</a></p>
                </div>

                <div class="space-y-3">
                    <div
                        v-for="(faq, i) in faqs"
                        :key="i"
                        class="rounded-xl border border-slate-200 bg-white overflow-hidden"
                    >
                        <button
                            type="button"
                            class="w-full flex items-center justify-between px-6 py-5 text-left gap-4 hover:bg-slate-50 transition-colors"
                            @click="toggleFaq(i)"
                        >
                            <span class="font-semibold text-slate-900 text-sm sm:text-base">{{ faq.q }}</span>
                            <svg
                                class="h-5 w-5 text-slate-400 shrink-0 transition-transform duration-200"
                                :class="{ 'rotate-180': openFaq === i }"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div v-show="openFaq === i" class="px-6 pt-0 pb-5">
                            <div class="border-t border-slate-100 pt-4">
                                <p class="text-slate-500 text-sm leading-relaxed">{{ faq.a }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Final CTA ─────────────────────────────────────────────────────── -->
        <section class="bg-slate-900 relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_#1e40af50_0%,_transparent_60%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,_#0f766e35_0%,_transparent_60%)]"></div>
            <div class="absolute inset-0 opacity-[0.03]"
                 style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 40px 40px;">
            </div>

            <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
                <h2 class="text-4xl sm:text-5xl font-bold text-white leading-tight">
                    Ready to stop<br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-teal-400">losing jobs to chaos?</span>
                </h2>
                <p class="mt-6 text-lg text-slate-400 max-w-xl mx-auto">
                    Set up your organization in under two minutes. No credit card. No commitment.
                </p>

                <!-- Founding member CTA variant -->
                <div v-if="foundingOffer" class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <Link
                        :href="registerUrl()"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-400 px-8 py-3.5 text-base font-bold text-white transition-colors shadow-lg shadow-amber-500/25"
                    >
                        Claim your Founding Member spot
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </Link>
                    <Link
                        :href="registerUrl()"
                        class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl border border-white/10 hover:border-white/20 px-8 py-3.5 text-base font-medium text-slate-300 hover:text-white transition-colors"
                    >
                        Start regular free trial
                    </Link>
                </div>
                <div v-else class="mt-10">
                    <Link
                        :href="registerUrl()"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-500 px-10 py-4 text-base font-semibold text-white transition-colors shadow-lg shadow-blue-600/25"
                    >
                        Start your free 14-day trial
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </Link>
                </div>

                <p class="mt-4 text-sm text-slate-500">No credit card required &bull; 14-day free trial &bull; Cancel any time</p>
            </div>
        </section>

        <!-- ── Footer ────────────────────────────────────────────────────────── -->
        <footer class="bg-slate-950 border-t border-white/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                    <!-- Logo -->
                    <div class="flex items-center gap-2.5">
                        <div class="h-7 w-7 rounded-lg bg-blue-500 flex items-center justify-center">
                            <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-white font-semibold text-sm tracking-tight">FieldOps Hub</span>
                    </div>

                    <nav class="flex items-center gap-6">
                        <a href="#features" class="text-sm text-slate-500 hover:text-slate-300 transition-colors">Features</a>
                        <a href="#pricing" class="text-sm text-slate-500 hover:text-slate-300 transition-colors">Pricing</a>
                        <a href="#faq" class="text-sm text-slate-500 hover:text-slate-300 transition-colors">FAQ</a>
                        <Link :href="login().url" class="text-sm text-slate-500 hover:text-slate-300 transition-colors">Sign in</Link>
                    </nav>

                    <p class="text-sm text-slate-600">&copy; {{ new Date().getFullYear() }} FieldOps Hub. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

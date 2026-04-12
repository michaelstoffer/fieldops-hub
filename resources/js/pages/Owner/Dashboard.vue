<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Stats {
    jobs_today: number;
    revenue_this_week: number;
    accounts_receivable: number;
    overdue_invoices: number;
    open_jobs: number;
    unassigned_jobs: number;
}

const props = defineProps<{ stats: Stats }>();

function formatCurrency(val: number): string {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val);
}
</script>

<template>
    <OwnerLayout title="Owner Dashboard">
        <Head title="Owner Dashboard" />

        <!-- KPI cards -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Jobs Today</p>
                <p class="mt-2 text-3xl font-bold text-slate-800">{{ stats.jobs_today }}</p>
                <Link href="/owner/jobs" class="mt-2 inline-block text-xs text-slate-400 hover:text-slate-700 transition-colors">View jobs →</Link>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Revenue This Week</p>
                <p class="mt-2 text-3xl font-bold text-green-600">{{ formatCurrency(stats.revenue_this_week) }}</p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Accounts Receivable</p>
                <p class="mt-2 text-3xl font-bold text-blue-600">{{ formatCurrency(stats.accounts_receivable) }}</p>
                <Link href="/owner/invoices" class="mt-2 inline-block text-xs text-slate-400 hover:text-slate-700 transition-colors">View invoices →</Link>
            </div>

            <div class="bg-white rounded-xl border border-rose-100 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Overdue Invoices</p>
                <p class="mt-2 text-3xl font-bold text-rose-600">{{ stats.overdue_invoices }}</p>
                <Link href="/owner/billing" class="mt-2 inline-block text-xs text-slate-400 hover:text-slate-700 transition-colors">View billing →</Link>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Open Jobs</p>
                <p class="mt-2 text-3xl font-bold text-slate-800">{{ stats.open_jobs }}</p>
            </div>

            <div class="bg-white rounded-xl border border-amber-100 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Unassigned Jobs</p>
                <p class="mt-2 text-3xl font-bold text-amber-600">{{ stats.unassigned_jobs }}</p>
                <Link href="/owner/dispatch" class="mt-2 inline-block text-xs text-slate-400 hover:text-slate-700 transition-colors">Open dispatch →</Link>
            </div>
        </section>

        <!-- Quick links to reports -->
        <section>
            <h2 class="text-sm font-semibold text-slate-600 mb-3 uppercase tracking-wide">Reports</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <Link
                    href="/owner/reports/jobs-by-type"
                    class="block bg-white rounded-xl border border-slate-200 p-4 shadow-sm hover:border-slate-300 hover:shadow transition-all"
                >
                    <p class="font-semibold text-slate-800">Jobs by Type</p>
                    <p class="text-xs text-slate-500 mt-1">Breakdown of jobs grouped by service type</p>
                </Link>
                <Link
                    href="/owner/reports/job-profitability"
                    class="block bg-white rounded-xl border border-slate-200 p-4 shadow-sm hover:border-slate-300 hover:shadow transition-all"
                >
                    <p class="font-semibold text-slate-800">Job Profitability</p>
                    <p class="text-xs text-slate-500 mt-1">Revenue vs. parts cost per job</p>
                </Link>
                <Link
                    href="/owner/reports/technician-performance"
                    class="block bg-white rounded-xl border border-slate-200 p-4 shadow-sm hover:border-slate-300 hover:shadow transition-all"
                >
                    <p class="font-semibold text-slate-800">Technician Performance</p>
                    <p class="text-xs text-slate-500 mt-1">Jobs completed, revenue, and avg duration</p>
                </Link>
            </div>
        </section>
    </OwnerLayout>
</template>

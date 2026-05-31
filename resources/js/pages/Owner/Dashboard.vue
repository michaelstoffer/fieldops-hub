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

        <!-- Quick-create actions -->
        <div class="mb-6 flex flex-wrap gap-3">
            <Link
                href="/owner/jobs/create"
                class="inline-flex items-center gap-2 rounded-lg bg-foreground px-4 py-2 text-sm font-medium text-background hover:opacity-90 transition-opacity"
            >
                + New Job
            </Link>
            <Link
                href="/owner/customers/create"
                class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium text-foreground hover:bg-accent transition-colors"
            >
                + New Customer
            </Link>
            <Link
                href="/owner/invoices/create"
                class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium text-foreground hover:bg-accent transition-colors"
            >
                + New Invoice
            </Link>
        </div>

        <!-- KPI cards -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <div class="bg-card rounded-xl border border-border p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Jobs Today</p>
                <p class="mt-2 text-3xl font-bold text-foreground">{{ stats.jobs_today }}</p>
                <Link href="/owner/jobs" class="mt-2 inline-block text-xs text-muted-foreground hover:text-foreground transition-colors">View jobs →</Link>
            </div>

            <div class="bg-card rounded-xl border border-border p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Revenue This Week</p>
                <p class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">{{ formatCurrency(stats.revenue_this_week) }}</p>
            </div>

            <div class="bg-card rounded-xl border border-border p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Accounts Receivable</p>
                <p class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-400">{{ formatCurrency(stats.accounts_receivable) }}</p>
                <Link href="/owner/invoices" class="mt-2 inline-block text-xs text-muted-foreground hover:text-foreground transition-colors">View invoices →</Link>
            </div>

            <div class="bg-card rounded-xl border border-rose-200 dark:border-rose-800/60 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Overdue Invoices</p>
                <p class="mt-2 text-3xl font-bold text-rose-600 dark:text-rose-400">{{ stats.overdue_invoices }}</p>
                <Link href="/owner/billing" class="mt-2 inline-block text-xs text-muted-foreground hover:text-foreground transition-colors">View billing →</Link>
            </div>

            <div class="bg-card rounded-xl border border-border p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Open Jobs</p>
                <p class="mt-2 text-3xl font-bold text-foreground">{{ stats.open_jobs }}</p>
            </div>

            <div class="bg-card rounded-xl border border-amber-200 dark:border-amber-800/60 p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Unassigned Jobs</p>
                <p class="mt-2 text-3xl font-bold text-amber-600 dark:text-amber-400">{{ stats.unassigned_jobs }}</p>
                <Link href="/owner/dispatch" class="mt-2 inline-block text-xs text-muted-foreground hover:text-foreground transition-colors">Open dispatch →</Link>
            </div>
        </section>

        <!-- Quick links to reports -->
        <section>
            <h2 class="text-sm font-semibold text-muted-foreground mb-3 uppercase tracking-wide">Reports</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <Link
                    href="/owner/reports/jobs-by-type"
                    class="block bg-card rounded-xl border border-border p-4 shadow-sm hover:border-border/80 hover:shadow transition-all"
                >
                    <p class="font-semibold text-foreground">Jobs by Type</p>
                    <p class="text-xs text-muted-foreground mt-1">Breakdown of jobs grouped by service type</p>
                </Link>
                <Link
                    href="/owner/reports/job-profitability"
                    class="block bg-card rounded-xl border border-border p-4 shadow-sm hover:border-border/80 hover:shadow transition-all"
                >
                    <p class="font-semibold text-foreground">Job Profitability</p>
                    <p class="text-xs text-muted-foreground mt-1">Revenue vs. parts cost per job</p>
                </Link>
                <Link
                    href="/owner/reports/technician-performance"
                    class="block bg-card rounded-xl border border-border p-4 shadow-sm hover:border-border/80 hover:shadow transition-all"
                >
                    <p class="font-semibold text-foreground">Technician Performance</p>
                    <p class="text-xs text-muted-foreground mt-1">Jobs completed, revenue, and avg duration</p>
                </Link>
            </div>
        </section>
    </OwnerLayout>
</template>

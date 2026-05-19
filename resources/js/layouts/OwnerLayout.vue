<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    LayoutDashboard, Users, Briefcase, CalendarDays, FileText, Receipt,
    MapPin, BarChart2, TrendingUp, UserCheck, Building2, Plug, UsersRound,
    Tags, Package, LogOut,
} from 'lucide-vue-next';

defineProps<{
    title?: string;
}>();

const page = usePage();
const currentUrl = computed(() => page.url);
const sidebarOpen = ref(false);

function navClass(href: string): string {
    return currentUrl.value.startsWith(href)
        ? 'block px-3 py-2 rounded-md bg-slate-700 text-white font-medium'
        : 'block px-3 py-2 rounded-md text-slate-300 hover:bg-slate-800 hover:text-white transition-colors';
}
</script>

<template>
    <div class="min-h-screen bg-slate-100 flex">
        <!-- Mobile sidebar backdrop -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-20 bg-black/50 lg:hidden"
            @click="sidebarOpen = false"
        />

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-900 text-slate-100 flex flex-col transform transition-transform duration-200 ease-in-out lg:static lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="px-6 py-4 text-xl font-bold border-b border-slate-800 tracking-tight">
                FieldOps Hub
            </div>

            <nav class="flex-1 px-3 py-4 space-y-0.5 text-sm overflow-y-auto">
                <Link href="/owner/dashboard" :class="navClass('/owner/dashboard')">Dashboard</Link>
                <Link href="/owner/customers" :class="navClass('/owner/customers')">Customers</Link>
                <Link href="/owner/jobs" :class="navClass('/owner/jobs')">Jobs</Link>
                <Link href="/owner/calendar" :class="navClass('/owner/calendar')">Calendar</Link>
                <Link href="/owner/estimates" :class="navClass('/owner/estimates')">Estimates</Link>
                <Link href="/owner/invoices" :class="navClass('/owner/invoices')">Invoices</Link>
                <Link href="/owner/dispatch" :class="navClass('/owner/dispatch')">Dispatch</Link>

                <div class="pt-4 pb-1 px-3 text-xs uppercase tracking-wider text-slate-500 font-semibold">Reports</div>
                <Link href="/owner/reports/jobs-by-type" :class="navClass('/owner/reports/jobs-by-type')">Jobs by Type</Link>
                <Link href="/owner/reports/job-profitability" :class="navClass('/owner/reports/job-profitability')">Job Profitability</Link>
                <Link href="/owner/reports/technician-performance" :class="navClass('/owner/reports/technician-performance')">Technician Performance</Link>

                <div class="pt-4 pb-1 px-3 text-xs uppercase tracking-wider text-slate-500 font-semibold">Settings</div>
                <Link href="/owner/settings/company" :class="navClass('/owner/settings/company')">Company</Link>
                <Link href="/owner/settings/integrations" :class="navClass('/owner/settings/integrations')">Integrations</Link>
                <Link href="/owner/team" :class="navClass('/owner/team')">Team</Link>

                <div class="pt-4 pb-1 px-3 text-xs uppercase tracking-wider text-slate-500 font-semibold">Configuration</div>
                <Link href="/owner/job-types" :class="navClass('/owner/job-types')">Job Types</Link>
                <Link href="/owner/items" :class="navClass('/owner/items')">Catalog Items</Link>
            </nav>

            <div class="border-t border-slate-800 px-4 py-3">
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="w-full text-left text-sm text-slate-400 hover:text-white transition-colors"
                >
                    Sign out
                </Link>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top bar -->
            <header class="sticky top-0 z-10 h-14 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 shadow-sm">
                <div class="flex items-center gap-3">
                    <!-- Mobile hamburger -->
                    <button
                        type="button"
                        class="lg:hidden -ml-1 flex h-9 w-9 items-center justify-center rounded-md text-slate-500 hover:text-slate-900"
                        aria-label="Open navigation"
                        @click="sidebarOpen = !sidebarOpen"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-base font-semibold text-slate-800 truncate">
                        {{ title ?? 'FieldOps Hub' }}
                    </h1>
                </div>
                <div class="text-sm text-slate-600 flex items-center gap-3">
                    <slot name="header-actions" />
                </div>
            </header>

            <!-- Page body -->
            <main class="flex-1 p-4 sm:p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

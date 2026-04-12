<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface TechRow {
    id: number;
    name: string;
    jobs_completed: number;
    revenue: number;
    avg_duration_minutes: number | null;
}

const props = defineProps<{
    technicians: TechRow[];
    filters: { from: string; to: string };
}>();

const from = ref(props.filters.from);
const to   = ref(props.filters.to);

function apply() {
    router.get('/owner/reports/technician-performance', { from: from.value, to: to.value }, { preserveState: true, replace: true });
}

function formatCurrency(val: number): string {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val);
}

function formatDuration(minutes: number | null): string {
    if (minutes === null) return '—';
    if (minutes < 60) return `${minutes}m`;
    const h = Math.floor(minutes / 60);
    const m = minutes % 60;
    return m > 0 ? `${h}h ${m}m` : `${h}h`;
}

const totals = computed(() => ({
    jobs:    props.technicians.reduce((s, t) => s + t.jobs_completed, 0),
    revenue: props.technicians.reduce((s, t) => s + t.revenue, 0),
}));
</script>

<template>
    <OwnerLayout title="Technician Performance">
        <Head title="Technician Performance" />

        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-800">Technician Performance</h2>
        </div>

        <!-- Date filter -->
        <div class="mb-6 flex flex-wrap items-end gap-3 bg-white rounded-xl shadow p-4">
            <div>
                <label class="block text-xs text-slate-500 mb-1">From</label>
                <input v-model="from" type="date" class="border border-slate-300 rounded px-3 py-1.5 text-sm" />
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">To</label>
                <input v-model="to" type="date" class="border border-slate-300 rounded px-3 py-1.5 text-sm" />
            </div>
            <button @click="apply"
                class="px-4 py-1.5 bg-slate-800 text-white text-sm rounded hover:bg-slate-700">
                Apply
            </button>
        </div>

        <!-- Summary cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4">
                <p class="text-xs uppercase text-slate-500">Total Jobs Completed</p>
                <p class="mt-1 text-2xl font-semibold text-slate-800">{{ totals.jobs }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4">
                <p class="text-xs uppercase text-slate-500">Total Revenue</p>
                <p class="mt-1 text-2xl font-semibold text-green-600">{{ formatCurrency(totals.revenue) }}</p>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-600">Technician</th>
                        <th class="px-4 py-3 text-center font-medium text-slate-600">Jobs Completed</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-600">Revenue Generated</th>
                        <th class="px-4 py-3 text-center font-medium text-slate-600">Avg Job Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="technicians.length === 0">
                        <td colspan="4" class="px-4 py-8 text-center text-slate-400">No technician data for this date range.</td>
                    </tr>
                    <tr v-for="tech in technicians" :key="tech.id" class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ tech.name }}</td>
                        <td class="px-4 py-3 text-center text-slate-700">{{ tech.jobs_completed }}</td>
                        <td class="px-4 py-3 text-right text-green-700">{{ formatCurrency(tech.revenue) }}</td>
                        <td class="px-4 py-3 text-center text-slate-600">{{ formatDuration(tech.avg_duration_minutes) }}</td>
                    </tr>
                </tbody>
                <tfoot v-if="technicians.length > 0" class="bg-slate-50 border-t border-slate-200">
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-700">Total</td>
                        <td class="px-4 py-3 text-center font-semibold text-slate-800">{{ totals.jobs }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-green-700">{{ formatCurrency(totals.revenue) }}</td>
                        <td class="px-4 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </OwnerLayout>
</template>

<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface JobRow {
    id: number;
    title: string;
    completed_at: string | null;
    job_type: { id: number; name: string; color: string } | null;
    technician: { id: number; name: string } | null;
    revenue: number;
    parts_cost: number;
    margin: number;
    margin_pct: number | null;
}

interface SelectOption { id: number; name: string }

const props = defineProps<{
    jobs: JobRow[];
    job_types: SelectOption[];
    technicians: SelectOption[];
    filters: { from: string; to: string; job_type_id?: string | null; technician_id?: string | null };
}>();

const from       = ref(props.filters.from);
const to         = ref(props.filters.to);
const jobTypeId  = ref(props.filters.job_type_id ?? '');
const techId     = ref(props.filters.technician_id ?? '');

function apply() {
    router.get('/owner/reports/job-profitability', {
        from: from.value,
        to: to.value,
        job_type_id: jobTypeId.value || undefined,
        technician_id: techId.value || undefined,
    }, { preserveState: true, replace: true });
}

function formatCurrency(val: number): string {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val);
}

function formatDate(d: string | null): string {
    return d ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
}

const totals = computed(() => ({
    revenue: props.jobs.reduce((s, j) => s + j.revenue, 0),
    parts:   props.jobs.reduce((s, j) => s + j.parts_cost, 0),
    margin:  props.jobs.reduce((s, j) => s + j.margin, 0),
}));
</script>

<template>
    <OwnerLayout title="Job Profitability">
        <Head title="Job Profitability" />

        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-foreground">Job Profitability</h2>
        </div>

        <!-- Filters -->
        <div class="mb-6 flex flex-wrap items-end gap-3 bg-card rounded-xl shadow ring-1 ring-border p-4">
            <div>
                <label class="block text-xs text-muted-foreground mb-1">From</label>
                <input v-model="from" type="date" class="border border-input bg-background rounded px-3 py-1.5 text-sm" />
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1">To</label>
                <input v-model="to" type="date" class="border border-input bg-background rounded px-3 py-1.5 text-sm" />
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1">Job Type</label>
                <select v-model="jobTypeId" class="border border-input bg-background rounded px-3 py-1.5 text-sm">
                    <option value="">All types</option>
                    <option v-for="jt in job_types" :key="jt.id" :value="jt.id">{{ jt.name }}</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1">Technician</label>
                <select v-model="techId" class="border border-input bg-background rounded px-3 py-1.5 text-sm">
                    <option value="">All technicians</option>
                    <option v-for="t in technicians" :key="t.id" :value="t.id">{{ t.name }}</option>
                </select>
            </div>
            <button @click="apply"
                class="px-4 py-1.5 bg-slate-800 text-white text-sm rounded hover:bg-slate-700">
                Apply
            </button>
        </div>

        <!-- Summary cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-card rounded-xl shadow ring-1 ring-border p-4">
                <p class="text-xs uppercase text-muted-foreground">Total Revenue</p>
                <p class="mt-1 text-2xl font-semibold text-green-600 dark:text-green-400">{{ formatCurrency(totals.revenue) }}</p>
            </div>
            <div class="bg-card rounded-xl shadow ring-1 ring-border p-4">
                <p class="text-xs uppercase text-muted-foreground">Total Parts Cost</p>
                <p class="mt-1 text-2xl font-semibold text-foreground">{{ formatCurrency(totals.parts) }}</p>
            </div>
            <div class="bg-card rounded-xl shadow ring-1 ring-border p-4">
                <p class="text-xs uppercase text-muted-foreground">Total Margin</p>
                <p class="mt-1 text-2xl font-semibold"
                    :class="totals.margin >= 0 ? 'text-green-600 dark:text-green-400' : 'text-rose-600 dark:text-rose-400'">
                    {{ formatCurrency(totals.margin) }}
                </p>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-card rounded-xl shadow ring-1 ring-border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-background border-b border-border">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Job</th>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Type</th>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Technician</th>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Completed</th>
                        <th class="px-4 py-3 text-right font-medium text-muted-foreground">Revenue</th>
                        <th class="px-4 py-3 text-right font-medium text-muted-foreground">Parts</th>
                        <th class="px-4 py-3 text-right font-medium text-muted-foreground">Margin</th>
                        <th class="px-4 py-3 text-right font-medium text-muted-foreground">Margin %</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="jobs.length === 0">
                        <td colspan="8" class="px-4 py-8 text-center text-muted-foreground">No completed jobs in this range.</td>
                    </tr>
                    <tr v-for="job in jobs" :key="job.id" class="border-b border-border hover:bg-accent">
                        <td class="px-4 py-3 font-medium text-foreground">{{ job.title }}</td>
                        <td class="px-4 py-3">
                            <span v-if="job.job_type" class="inline-flex items-center gap-1.5">
                                <span :style="{ background: job.job_type.color }" class="w-2 h-2 rounded-full"></span>
                                {{ job.job_type.name }}
                            </span>
                            <span v-else class="text-muted-foreground">—</span>
                        </td>
                        <td class="px-4 py-3 text-muted-foreground">{{ job.technician?.name ?? '—' }}</td>
                        <td class="px-4 py-3 text-muted-foreground">{{ formatDate(job.completed_at) }}</td>
                        <td class="px-4 py-3 text-right text-green-600 dark:text-green-400">{{ formatCurrency(job.revenue) }}</td>
                        <td class="px-4 py-3 text-right text-muted-foreground">{{ formatCurrency(job.parts_cost) }}</td>
                        <td class="px-4 py-3 text-right font-medium"
                            :class="job.margin >= 0 ? 'text-green-600 dark:text-green-400' : 'text-rose-600 dark:text-rose-400'">
                            {{ formatCurrency(job.margin) }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span v-if="job.margin_pct !== null"
                                :class="job.margin_pct >= 0 ? 'text-green-600 dark:text-green-400' : 'text-rose-600 dark:text-rose-400'">
                                {{ job.margin_pct }}%
                            </span>
                            <span v-else class="text-muted-foreground">—</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </OwnerLayout>
</template>

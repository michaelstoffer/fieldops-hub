<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface JobTypeRow {
    type: { id: number; name: string; color: string };
    statuses: Record<string, number>;
    total: number;
}

const props = defineProps<{
    rows: JobTypeRow[];
    filters: { from: string; to: string };
    statuses: Record<string, string>;
}>();

const from = ref(props.filters.from);
const to   = ref(props.filters.to);

function apply() {
    router.get('/owner/reports/jobs-by-type', { from: from.value, to: to.value }, { preserveState: true, replace: true });
}

const STATUS_CLASSES: Record<string, string> = {
    scheduled:   'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
    en_route:    'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400',
    in_progress: 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400',
    completed:   'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400',
    cancelled:   'bg-muted text-muted-foreground',
    on_hold:     'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-400',
};
</script>

<template>
    <OwnerLayout title="Jobs by Type">
        <Head title="Jobs by Type" />

        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-foreground">Jobs by Type</h2>
        </div>

        <!-- Date filter -->
        <div class="mb-6 flex flex-wrap items-end gap-3 bg-card rounded-xl shadow ring-1 ring-border p-4">
            <div>
                <label class="block text-xs text-muted-foreground mb-1">From</label>
                <input v-model="from" type="date" class="border border-input bg-background rounded px-3 py-1.5 text-sm" />
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1">To</label>
                <input v-model="to" type="date" class="border border-input bg-background rounded px-3 py-1.5 text-sm" />
            </div>
            <button @click="apply"
                class="px-4 py-1.5 bg-slate-800 text-white text-sm rounded hover:bg-slate-700">
                Apply
            </button>
        </div>

        <!-- Table -->
        <div class="bg-card rounded-xl shadow ring-1 ring-border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-background border-b border-border">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Job Type</th>
                        <th v-for="(label, key) in statuses" :key="key"
                            class="px-4 py-3 text-center font-medium text-muted-foreground">
                            {{ label }}
                        </th>
                        <th class="px-4 py-3 text-center font-medium text-muted-foreground">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="rows.length === 0">
                        <td :colspan="Object.keys(statuses).length + 2" class="px-4 py-8 text-center text-muted-foreground">
                            No jobs found for this date range.
                        </td>
                    </tr>
                    <tr v-for="row in rows" :key="row.type.id" class="border-b border-border hover:bg-accent">
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-2">
                                <span :style="{ background: row.type.color }"
                                    class="w-2.5 h-2.5 rounded-full inline-block"></span>
                                {{ row.type.name }}
                            </span>
                        </td>
                        <td v-for="(label, key) in statuses" :key="key" class="px-4 py-3 text-center">
                            <span v-if="row.statuses[key]"
                                :class="STATUS_CLASSES[key] ?? 'bg-muted text-muted-foreground'"
                                class="inline-block px-2 py-0.5 rounded-full text-xs font-medium">
                                {{ row.statuses[key] }}
                            </span>
                            <span v-else class="text-muted-foreground/40">—</span>
                        </td>
                        <td class="px-4 py-3 text-center font-semibold text-foreground">{{ row.total }}</td>
                    </tr>
                </tbody>
                <tfoot v-if="rows.length > 0" class="bg-background border-t border-border">
                    <tr>
                        <td class="px-4 py-3 font-semibold text-foreground">Total</td>
                        <td v-for="(label, key) in statuses" :key="key" class="px-4 py-3 text-center font-semibold text-foreground">
                            {{ rows.reduce((sum, r) => sum + (r.statuses[key] ?? 0), 0) || '—' }}
                        </td>
                        <td class="px-4 py-3 text-center font-semibold text-foreground">
                            {{ rows.reduce((sum, r) => sum + r.total, 0) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </OwnerLayout>
</template>

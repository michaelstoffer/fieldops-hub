<script setup lang="ts">
import TechnicianLayout from '@/layouts/TechnicianLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    stats: {
        today_jobs: number;
        in_progress: number;
    };
}>();
</script>

<template>
    <TechnicianLayout title="Dashboard">
        <Head title="Dashboard" />

        <div class="p-4">
            <h2 class="mb-4 text-lg font-semibold text-foreground">Today's Overview</h2>

            <div class="mb-6 grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-card p-4 shadow-sm ring-1 ring-border">
                    <p class="text-sm text-muted-foreground">Today's Jobs</p>
                    <p class="mt-1 text-3xl font-bold text-foreground">{{ stats.today_jobs }}</p>
                </div>
                <div class="rounded-xl bg-card p-4 shadow-sm ring-1 ring-border">
                    <p class="text-sm text-muted-foreground">In Progress</p>
                    <p class="mt-1 text-3xl font-bold text-amber-600 dark:text-amber-400">{{ stats.in_progress }}</p>
                </div>
            </div>

            <Link
                href="/technician/jobs"
                class="flex w-full items-center justify-center rounded-xl bg-slate-900 py-3.5 text-sm font-semibold text-white shadow-sm active:bg-slate-700"
            >
                View Today's Jobs
            </Link>
        </div>
    </TechnicianLayout>
</template>

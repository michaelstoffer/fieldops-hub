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
            <h2 class="mb-4 text-lg font-semibold text-slate-800">Today's Overview</h2>

            <div class="mb-6 grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm text-slate-500">Today's Jobs</p>
                    <p class="mt-1 text-3xl font-bold text-slate-900">{{ stats.today_jobs }}</p>
                </div>
                <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm text-slate-500">In Progress</p>
                    <p class="mt-1 text-3xl font-bold text-amber-600">{{ stats.in_progress }}</p>
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

<script setup>
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

const page = usePage();
const props = defineProps({
    stats: Object,
});
</script>

<template>
    <OwnerLayout title="Owner Dashboard">
        <Head title="Owner Dashboard" />

        <!-- Top quick stats -->
        <section class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4">
                <p class="text-xs uppercase text-slate-500">Open Jobs</p>
                <p class="mt-2 text-2xl font-semibold text-slate-800">
                    {{ stats.open_jobs }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow p-4">
                <p class="text-xs uppercase text-slate-500">Today’s Visits</p>
                <p class="mt-2 text-2xl font-semibold text-slate-800">
                    {{ stats.today_visits }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow p-4">
                <p class="text-xs uppercase text-slate-500">Overdue Jobs</p>
                <p class="mt-2 text-2xl font-semibold text-rose-600">
                    {{ stats.overdue_jobs }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow p-4">
                <p class="text-xs uppercase text-slate-500">Unassigned</p>
                <p class="mt-2 text-2xl font-semibold text-amber-600">
                    {{ stats.unassigned_jobs }}
                </p>
            </div>
        </section>

        <!-- Lower layout stub: schedule + jobs list -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Upcoming jobs / schedule -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow p-4">
                <h2 class="text-sm font-semibold text-slate-800 mb-3">
                    Today’s Schedule
                </h2>
                <div class="text-slate-500 text-sm">
                    <!-- TODO: Replace with real schedule component -->
                    No schedule data yet. Once we wire jobs & visits,
                    this will show a timeline of the day.
                </div>
            </div>

            <!-- Alerts / priorities -->
            <div class="bg-white rounded-xl shadow p-4">
                <h2 class="text-sm font-semibold text-slate-800 mb-3">
                    Alerts & Priorities
                </h2>
                <ul class="text-sm text-slate-600 space-y-2">
                    <li>• 3 overdue jobs need review</li>
                    <li>• 5 jobs unassigned to a tech</li>
                    <li>• 1 customer waiting on follow-up</li>
                </ul>
            </div>
        </section>
    </OwnerLayout>
</template>

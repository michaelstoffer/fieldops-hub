<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { router } from '@inertiajs/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import timeGridPlugin from '@fullcalendar/timegrid';
import FullCalendar from '@fullcalendar/vue3';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const calendarOptions = ref({
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'timeGridWeek',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay',
    },
    height: 'auto',
    nowIndicator: true,
    businessHours: {
        daysOfWeek: [1, 2, 3, 4, 5],
        startTime: '07:00',
        endTime: '18:00',
    },
    slotMinTime: '06:00:00',
    slotMaxTime: '20:00:00',
    eventSources: [
        {
            url: '/owner/calendar/events',
            method: 'GET',
            extraParams: {},
            failure: () => {
                console.error('Failed to load calendar events');
            },
        },
    ],
    eventClick: (info: { event: { url: string }; jsEvent: Event }) => {
        info.jsEvent.preventDefault();
        if (info.event.url) {
            router.visit(info.event.url);
        }
    },
    eventTimeFormat: {
        hour: 'numeric',
        minute: '2-digit',
        meridiem: 'short',
    },
    dayMaxEvents: true,
});
</script>

<template>
    <OwnerLayout title="Calendar">
        <Head title="Calendar" />

        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-800">Calendar</h2>
            <Link
                href="/owner/jobs/create"
                class="inline-flex items-center gap-2 rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700"
            >
                + New Job
            </Link>
        </div>

        <div class="rounded-xl bg-white p-4 shadow">
            <FullCalendar :options="calendarOptions" />
        </div>
    </OwnerLayout>
</template>

<style>
/* Ensure FullCalendar renders cleanly inside Tailwind's reset */
.fc .fc-button {
    @apply rounded border border-slate-200 bg-white text-sm text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none;
}
.fc .fc-button-primary {
    @apply border-slate-800 bg-slate-800 text-white hover:bg-slate-700;
}
.fc .fc-button-primary:not(:disabled):active,
.fc .fc-button-primary.fc-button-active {
    @apply border-slate-900 bg-slate-900;
}
.fc .fc-toolbar-title {
    @apply text-lg font-semibold text-slate-800;
}
.fc-theme-standard td,
.fc-theme-standard th {
    @apply border-slate-100;
}
.fc .fc-daygrid-day.fc-day-today,
.fc .fc-timegrid-col.fc-day-today {
    @apply bg-blue-50;
}
</style>

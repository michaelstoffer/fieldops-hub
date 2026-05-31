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
    dateClick: (info: { dateStr: string }) => {
        router.visit(`/owner/jobs/create?scheduled_at=${encodeURIComponent(info.dateStr)}`);
    },
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
            <div>
                <h2 class="text-xl font-semibold text-foreground">Calendar</h2>
                <p class="mt-0.5 text-sm text-muted-foreground">Click any time slot to schedule a new job</p>
            </div>
            <Link
                href="/owner/jobs/create"
                class="inline-flex items-center gap-2 rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                New Job
            </Link>
        </div>

        <div class="rounded-xl bg-card shadow ring-1 ring-border p-5">
            <FullCalendar :options="calendarOptions" />
        </div>
    </OwnerLayout>
</template>

<style>
/* FullCalendar base chrome */
.fc .fc-toolbar-title {
    @apply text-base font-semibold text-foreground;
}

/* Nav / view-switcher buttons */
.fc .fc-button {
    @apply rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-medium text-foreground shadow-sm transition-colors hover:bg-accent focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-1;
    text-transform: capitalize;
}
.fc .fc-button-primary {
    @apply border-slate-700 bg-slate-800 text-white hover:bg-slate-700 dark:border-slate-600 dark:bg-slate-700 dark:hover:bg-slate-600;
}
.fc .fc-button-primary:not(:disabled):active,
.fc .fc-button-primary.fc-button-active {
    @apply border-slate-900 bg-slate-900 dark:border-slate-500 dark:bg-slate-600;
}
.fc .fc-button:disabled {
    @apply opacity-40 cursor-not-allowed;
}

/* Grid borders */
.fc-theme-standard td,
.fc-theme-standard th {
    border-color: hsl(var(--border));
}
.fc-theme-standard .fc-scrollgrid {
    border-color: hsl(var(--border));
}

/* Column headers (Mon, Tue…) */
.fc .fc-col-header-cell-cushion {
    @apply text-xs font-semibold uppercase tracking-wide text-muted-foreground py-2;
}

/* Day numbers in month view */
.fc .fc-daygrid-day-number {
    @apply text-sm text-muted-foreground px-2 py-1;
}
.fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
    @apply text-foreground font-semibold;
}

/* Today highlight */
.fc .fc-daygrid-day.fc-day-today,
.fc .fc-timegrid-col.fc-day-today {
    background-color: hsl(var(--primary) / 0.06) !important;
}

/* Time slots */
.fc .fc-timegrid-slot-label-cushion {
    @apply text-xs text-muted-foreground;
}
.fc .fc-timegrid-slot {
    height: 2.5rem;
}

/* Now indicator */
.fc .fc-timegrid-now-indicator-line {
    @apply border-blue-500;
}
.fc .fc-timegrid-now-indicator-arrow {
    border-color: hsl(var(--primary));
}

/* Business hours shading */
.fc .fc-non-business {
    background-color: hsl(var(--muted) / 0.4);
}

/* Event tiles */
.fc .fc-event {
    @apply rounded-md border-0 px-1.5 py-0.5 text-xs font-medium shadow-sm cursor-pointer;
}
.fc .fc-event:hover {
    filter: brightness(1.1);
}
.fc .fc-event-title {
    @apply font-medium;
}
.fc .fc-event-time {
    @apply opacity-80;
}

/* "more" popover */
.fc .fc-more-popover {
    @apply rounded-xl border border-border bg-card shadow-lg;
}
.fc .fc-more-popover .fc-popover-header {
    @apply rounded-t-xl bg-muted px-3 py-2 text-xs font-semibold text-foreground;
}

/* Week/day time-grid background */
.fc .fc-timegrid-body {
    background-color: hsl(var(--card));
}
</style>

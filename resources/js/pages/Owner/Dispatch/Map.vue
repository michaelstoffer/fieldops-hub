<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

interface JobSummary {
    id: number;
    title: string;
    status: string;
    scheduled_at: string | null;
    customer: string | null;
    address: string | null;
}

interface TechLocation {
    latitude: number;
    longitude: number;
    heading: number | null;
    recorded_at: string;
}

interface Technician {
    id: number;
    name: string;
    location: TechLocation | null;
    current_job: JobSummary | null;
    upcoming_jobs: JobSummary[];
}

defineProps<{ technicians: { id: number; name: string }[] }>();

const POLL_INTERVAL = 15_000; // 15 seconds
const hasMapsKey = !!import.meta.env.VITE_GOOGLE_MAPS_API_KEY;

const mapRef = ref<HTMLElement | null>(null);
const focused = ref<Technician | null>(null);
const showTrails = ref(false);
const techs = ref<Technician[]>([]);

// Google Maps objects
let map: google.maps.Map | null = null;
const markers: Map<number, google.maps.Marker> = new Map();
const trails: Map<number, google.maps.Polyline> = new Map();
let pollTimer: ReturnType<typeof setInterval> | null = null;

const STATUS_COLORS: Record<string, string> = {
    en_route:    '#7c3aed',
    in_progress: '#d97706',
    scheduled:   '#2563eb',
    completed:   '#16a34a',
    on_hold:     '#ea580c',
    cancelled:   '#94a3b8',
};

function statusLabel(status: string): string {
    return { en_route: 'En Route', in_progress: 'In Progress', scheduled: 'Scheduled', completed: 'Completed', on_hold: 'On Hold', cancelled: 'Cancelled' }[status] ?? status;
}

function techStatus(tech: Technician): string {
    return tech.current_job?.status ?? (tech.location ? 'available' : 'offline');
}

function techStatusColor(tech: Technician): string {
    const s = tech.current_job?.status;
    return s ? (STATUS_COLORS[s] ?? '#94a3b8') : (tech.location ? '#16a34a' : '#94a3b8');
}

async function fetchLocations() {
    const res = await fetch('/owner/dispatch/technicians', {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    });
    if (!res.ok) return;
    const json = await res.json();
    techs.value = json.data;
    updateMarkers();
}

async function fetchTrail(techId: number): Promise<{ lat: number; lng: number }[]> {
    const res = await fetch(`/owner/dispatch/technicians/${techId}/trail`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    });
    if (!res.ok) return [];
    return (await res.json()).data;
}

function updateMarkers() {
    if (!map) return;

    techs.value.forEach((tech) => {
        if (!tech.location) {
            markers.get(tech.id)?.setMap(null);
            markers.delete(tech.id);
            return;
        }

        const pos = { lat: tech.location.latitude, lng: tech.location.longitude };
        const color = techStatusColor(tech);
        const label = tech.name.charAt(0).toUpperCase();

        if (markers.has(tech.id)) {
            const m = markers.get(tech.id)!;
            m.setPosition(pos);
            m.setIcon(makeIcon(color, label));
        } else {
            const m = new google.maps.Marker({
                position: pos,
                map,
                title: tech.name,
                icon: makeIcon(color, label),
            });
            m.addListener('click', () => { focused.value = tech; });
            markers.set(tech.id, m);
        }
    });

    // Update trails if enabled
    if (showTrails.value) {
        refreshTrails();
    }
}

function makeIcon(color: string, label: string): google.maps.Symbol & { labelOrigin: google.maps.Point } {
    return {
        path: google.maps.SymbolPath.CIRCLE,
        scale: 18,
        fillColor: color,
        fillOpacity: 1,
        strokeColor: '#fff',
        strokeWeight: 2,
        labelOrigin: new google.maps.Point(0, 0),
    } as any;
}

async function refreshTrails() {
    if (!map) return;

    // Clear existing trails
    trails.forEach((p) => p.setMap(null));
    trails.clear();

    if (!showTrails.value) return;

    for (const tech of techs.value) {
        if (!tech.location) continue;
        const points = await fetchTrail(tech.id);
        if (points.length < 2) continue;

        const poly = new google.maps.Polyline({
            path: points,
            geodesic: true,
            strokeColor: techStatusColor(tech),
            strokeOpacity: 0.6,
            strokeWeight: 3,
            map,
        });
        trails.set(tech.id, poly);
    }
}

function toggleTrails() {
    showTrails.value = !showTrails.value;
    refreshTrails();
}

function initMap() {
    if (!mapRef.value) return;

    map = new google.maps.Map(mapRef.value, {
        center: { lat: 39.5, lng: -98.35 }, // US center
        zoom: 5,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: false,
    });

    fetchLocations();
    pollTimer = setInterval(fetchLocations, POLL_INTERVAL);
}

function loadGoogleMaps() {
    const key = import.meta.env.VITE_GOOGLE_MAPS_API_KEY;
    if (!key) {
        // No API key — show placeholder
        return;
    }

    if (window.google?.maps) {
        initMap();
        return;
    }

    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${key}&callback=__gmapsInit`;
    script.async = true;
    (window as any).__gmapsInit = () => { initMap(); };
    document.head.appendChild(script);
}

function formatTime(iso: string | null): string {
    if (!iso) return '—';
    return new Date(iso).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
}

onMounted(() => {
    loadGoogleMaps();
});

onUnmounted(() => {
    if (pollTimer) clearInterval(pollTimer);
    delete (window as any).__gmapsInit;
});
</script>

<template>
    <OwnerLayout title="Dispatch Map">
        <Head title="Dispatch Map" />

        <div class="flex h-[calc(100vh-3.5rem-1.5rem)] gap-4">
            <!-- Sidebar: technician list -->
            <aside class="flex w-64 shrink-0 flex-col gap-3">
                <!-- Trails toggle -->
                <div class="flex items-center justify-between rounded-xl bg-white px-4 py-3 shadow ring-1 ring-slate-200">
                    <span class="text-sm font-medium text-slate-700">Show Trails</span>
                    <button
                        type="button"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition"
                        :class="showTrails ? 'bg-slate-800' : 'bg-slate-200'"
                        @click="toggleTrails"
                    >
                        <span
                            class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition"
                            :class="showTrails ? 'translate-x-6' : 'translate-x-1'"
                        />
                    </button>
                </div>

                <!-- Technician cards -->
                <div class="flex-1 space-y-2 overflow-y-auto">
                    <button
                        v-for="tech in techs"
                        :key="tech.id"
                        type="button"
                        class="w-full rounded-xl bg-white px-4 py-3 text-left shadow ring-1 transition"
                        :class="focused?.id === tech.id ? 'ring-slate-800' : 'ring-slate-200 hover:ring-slate-300'"
                        @click="focused = tech"
                    >
                        <div class="flex items-center gap-2">
                            <span
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold text-white"
                                :style="{ background: techStatusColor(tech) }"
                            >
                                {{ tech.name.charAt(0) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">{{ tech.name }}</p>
                                <p class="text-xs capitalize text-slate-500">
                                    {{ tech.current_job ? statusLabel(tech.current_job.status) : (tech.location ? 'Available' : 'Offline') }}
                                </p>
                            </div>
                        </div>
                    </button>

                    <p v-if="techs.length === 0" class="py-4 text-center text-sm text-slate-400">
                        No technicians found.
                    </p>
                </div>
            </aside>

            <!-- Map area -->
            <div class="relative flex-1 overflow-hidden rounded-xl shadow ring-1 ring-slate-200">
                <div ref="mapRef" class="h-full w-full bg-slate-100">
                    <div
                        v-if="!hasMapsKey"
                        class="flex h-full items-center justify-center text-slate-400"
                    >
                        <p class="text-sm">Set <code class="rounded bg-slate-200 px-1">GOOGLE_MAPS_API_KEY</code> to enable the map.</p>
                    </div>
                </div>

                <!-- Focus panel (#82) -->
                <transition
                    enter-active-class="transition duration-200"
                    enter-from-class="translate-x-full opacity-0"
                    enter-to-class="translate-x-0 opacity-100"
                    leave-active-class="transition duration-150"
                    leave-from-class="translate-x-0 opacity-100"
                    leave-to-class="translate-x-full opacity-0"
                >
                    <div
                        v-if="focused"
                        class="absolute right-0 top-0 h-full w-72 overflow-y-auto bg-white shadow-xl ring-1 ring-slate-200"
                    >
                        <!-- Header -->
                        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span
                                    class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold text-white"
                                    :style="{ background: techStatusColor(focused) }"
                                >
                                    {{ focused.name.charAt(0) }}
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">{{ focused.name }}</p>
                                    <p class="text-xs capitalize text-slate-500">
                                        {{ focused.current_job ? statusLabel(focused.current_job.status) : (focused.location ? 'Available' : 'Offline') }}
                                    </p>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="text-slate-400 hover:text-slate-600"
                                @click="focused = null"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                </svg>
                            </button>
                        </div>

                        <!-- Location info -->
                        <div v-if="focused.location" class="border-b border-slate-100 px-4 py-3">
                            <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-400">Location</p>
                            <p class="text-xs text-slate-600">
                                {{ focused.location.latitude.toFixed(4) }}, {{ focused.location.longitude.toFixed(4) }}
                            </p>
                            <p class="text-xs text-slate-400">
                                Updated {{ formatTime(focused.location.recorded_at) }}
                            </p>
                        </div>

                        <!-- Current job -->
                        <div v-if="focused.current_job" class="border-b border-slate-100 px-4 py-3">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Current Job</p>
                            <p class="text-sm font-medium text-slate-800">{{ focused.current_job.title }}</p>
                            <p v-if="focused.current_job.customer" class="mt-0.5 text-xs text-slate-500">{{ focused.current_job.customer }}</p>
                            <p v-if="focused.current_job.address" class="mt-0.5 text-xs text-slate-500">{{ focused.current_job.address }}</p>
                            <span
                                class="mt-1.5 inline-block rounded-full px-2 py-0.5 text-xs font-medium"
                                :style="{ background: (STATUS_COLORS[focused.current_job.status] ?? '#94a3b8') + '22', color: STATUS_COLORS[focused.current_job.status] ?? '#94a3b8' }"
                            >
                                {{ statusLabel(focused.current_job.status) }}
                            </span>
                        </div>

                        <!-- Upcoming jobs -->
                        <div v-if="focused.upcoming_jobs.length > 0" class="px-4 py-3">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Upcoming Today</p>
                            <div class="space-y-3">
                                <div
                                    v-for="job in focused.upcoming_jobs"
                                    :key="job.id"
                                    class="rounded-lg bg-slate-50 p-3"
                                >
                                    <p class="text-sm font-medium text-slate-800">{{ job.title }}</p>
                                    <p v-if="job.customer" class="mt-0.5 text-xs text-slate-500">{{ job.customer }}</p>
                                    <p class="mt-0.5 text-xs text-slate-400">{{ formatTime(job.scheduled_at) }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-if="!focused.current_job && focused.upcoming_jobs.length === 0" class="px-4 py-6 text-center">
                            <p class="text-sm text-slate-400">No active or upcoming jobs today.</p>
                        </div>
                    </div>
                </transition>
            </div>
        </div>
    </OwnerLayout>
</template>

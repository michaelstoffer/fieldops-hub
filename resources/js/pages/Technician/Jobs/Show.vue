<script setup lang="ts">
import TechnicianLayout from '@/layouts/TechnicianLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';

// Compress an image File to a JPEG Blob under `maxKB` kilobytes.
async function compressImage(file: File, maxKB = 800): Promise<Blob> {
    return new Promise((resolve) => {
        const img = new Image();
        const url = URL.createObjectURL(file);
        img.onload = () => {
            URL.revokeObjectURL(url);
            const canvas = document.createElement('canvas');
            const MAX_DIM = 1920;
            let { width, height } = img;
            if (width > MAX_DIM || height > MAX_DIM) {
                if (width > height) { height = Math.round((height * MAX_DIM) / width); width = MAX_DIM; }
                else { width = Math.round((width * MAX_DIM) / height); height = MAX_DIM; }
            }
            canvas.width = width;
            canvas.height = height;
            canvas.getContext('2d')!.drawImage(img, 0, 0, width, height);
            const tryQuality = (q: number) => {
                canvas.toBlob((blob) => {
                    if (!blob) { resolve(new Blob()); return; }
                    if (blob.size <= maxKB * 1024 || q <= 0.3) { resolve(blob); return; }
                    tryQuality(Math.round((q - 0.1) * 10) / 10);
                }, 'image/jpeg', q);
            };
            tryQuality(0.85);
        };
        img.src = url;
    });
}

interface ChecklistItem {
    id: number;
    label: string;
    sort_order: number;
    is_required: boolean;
    completed_at: string | null;
}

interface Attachment {
    id: number;
    filename: string;
    url: string;
    tag: 'before' | 'after' | null;
    mime_type: string | null;
}

interface Property {
    id: number;
    address_line1: string;
    city: string;
    state: string;
    postal_code: string;
}

interface JobMessage {
    id: number;
    channel: string;
    event: string;
    recipient: string;
    status: string;
    created_at: string;
}

interface Job {
    id: number;
    title: string;
    description: string | null;
    status: string;
    scheduled_at: string | null;
    started_at: string | null;
    arrived_at: string | null;
    completed_at: string | null;
    office_notes: string | null;
    technician_notes: string | null;
    customer_notes: string | null;
    customer: { id: number; first_name: string; last_name: string; phone?: string } | null;
    property: Property | null;
    job_type: { id: number; name: string; color: string } | null;
    checklist_items: ChecklistItem[];
    attachments: Attachment[];
    line_items: LineItem[];
    messages: JobMessage[];
}

interface LineItem {
    id: number;
    item_id: number | null;
    name: string;
    unit_price: string;
    quantity: string;
    total: string;
    sort_order: number;
}

interface CatalogItem {
    id: number;
    name: string;
    unit_price: string;
    unit: string;
    sku: string | null;
}

const props = defineProps<{
    job: Job;
    statuses: Record<string, string>;
}>();

const STATUS_CLASSES: Record<string, string> = {
    scheduled:   'bg-blue-100 text-blue-700',
    en_route:    'bg-purple-100 text-purple-700',
    in_progress: 'bg-amber-100 text-amber-700',
    completed:   'bg-green-100 text-green-700',
    cancelled:   'bg-slate-100 text-slate-500',
    on_hold:     'bg-orange-100 text-orange-700',
};

// Three-step technician workflow. Labels are PWA-specific and intentionally
// differ from the backend `statuses` labels used on the owner side.
const TECHNICIAN_ACTIONS: { key: string; label: string }[] = [
    { key: 'en_route',    label: 'On my Way' },
    { key: 'in_progress', label: 'Arrived' },
    { key: 'completed',   label: 'Complete' },
];

const statusForm = useForm({ status: props.job.status });
const notesForm = useForm({ technician_notes: props.job.technician_notes ?? '' });
const editingNotes = ref(false);
const customerNotesForm = useForm({ customer_notes: props.job.customer_notes ?? '' });
const editingCustomerNotes = ref(false);

const checklistState = reactive(
    Object.fromEntries(
        (props.job.checklist_items ?? []).map((item) => [item.id, item.completed_at !== null]),
    ) as Record<number, boolean>,
);
const togglingItem = ref<number | null>(null);

// Photos
const photos = reactive<Attachment[]>([...(props.job.attachments ?? [])]);
const uploadingTag = ref<'before' | 'after' | null>(null);
const uploadProgress = ref(0);
const deletingPhotoId = ref<number | null>(null);
const beforeInputRef = ref<HTMLInputElement | null>(null);
const afterInputRef = ref<HTMLInputElement | null>(null);

const beforePhotos = computed(() => photos.filter((p) => p.tag === 'before'));
const afterPhotos = computed(() => photos.filter((p) => p.tag === 'after'));

async function handlePhotoCapture(e: Event, tag: 'before' | 'after') {
    const input = e.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;
    input.value = '';

    uploadingTag.value = tag;
    uploadProgress.value = 0;

    const blob = await compressImage(file);
    const compressed = new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), { type: 'image/jpeg' });

    const formData = new FormData();
    formData.append('photo', compressed);
    formData.append('tag', tag);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', `/api/technician/jobs/${props.job.id}/photos`);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Accept', 'application/json');

    const csrfMeta = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null;
    if (csrfMeta) xhr.setRequestHeader('X-CSRF-TOKEN', csrfMeta.content);

    xhr.upload.onprogress = (ev) => {
        if (ev.lengthComputable) uploadProgress.value = Math.round((ev.loaded / ev.total) * 100);
    };

    xhr.onload = () => {
        uploadingTag.value = null;
        uploadProgress.value = 0;
        if (xhr.status === 201) {
            const resp = JSON.parse(xhr.responseText);
            photos.push(resp.data);
        }
    };

    xhr.onerror = () => {
        uploadingTag.value = null;
        uploadProgress.value = 0;
    };

    xhr.send(formData);
}

function deletePhoto(photo: Attachment) {
    deletingPhotoId.value = photo.id;
    router.delete(`/api/technician/jobs/${props.job.id}/photos/${photo.id}`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            const idx = photos.findIndex((p) => p.id === photo.id);
            if (idx !== -1) photos.splice(idx, 1);
        },
        onFinish: () => { deletingPhotoId.value = null; },
    });
}

// Line items
const lineItems = reactive<LineItem[]>([...(props.job.line_items ?? [])]);
const lineItemTotal = computed(() =>
    lineItems.reduce((sum, li) => sum + parseFloat(li.unit_price) * parseFloat(li.quantity), 0),
);

// Add form state
const showAddLineItem = ref(false);
const addForm = reactive({ name: '', unit_price: '', quantity: '1', item_id: null as number | null });
const addingLineItem = ref(false);

// Catalog search
const catalogQuery = ref('');
const catalogResults = ref<CatalogItem[]>([]);
const catalogLoading = ref(false);
let catalogDebounce: ReturnType<typeof setTimeout> | null = null;

function searchCatalog() {
    if (catalogDebounce) clearTimeout(catalogDebounce);
    if (!catalogQuery.value.trim()) { catalogResults.value = []; return; }
    catalogDebounce = setTimeout(async () => {
        catalogLoading.value = true;
        try {
            const res = await fetch(`/api/technician/catalog?q=${encodeURIComponent(catalogQuery.value)}`, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const json = await res.json();
            catalogResults.value = json.data ?? [];
        } finally {
            catalogLoading.value = false;
        }
    }, 300);
}

function selectCatalogItem(item: CatalogItem) {
    addForm.item_id = item.id;
    addForm.name = item.name;
    addForm.unit_price = parseFloat(item.unit_price).toFixed(2);
    catalogQuery.value = item.name;
    catalogResults.value = [];
}

async function submitAddLineItem() {
    if (!addForm.name || !addForm.unit_price || !addForm.quantity) return;
    addingLineItem.value = true;
    try {
        const csrfMeta = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null;
        const res = await fetch(`/api/technician/jobs/${props.job.id}/line-items`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(csrfMeta ? { 'X-CSRF-TOKEN': csrfMeta.content } : {}),
            },
            body: JSON.stringify({
                item_id: addForm.item_id,
                name: addForm.name,
                unit_price: parseFloat(addForm.unit_price),
                quantity: parseFloat(addForm.quantity),
            }),
        });
        if (res.ok) {
            const json = await res.json();
            lineItems.push(json.data);
            addForm.name = ''; addForm.unit_price = ''; addForm.quantity = '1'; addForm.item_id = null;
            catalogQuery.value = ''; catalogResults.value = [];
            showAddLineItem.value = false;
        }
    } finally {
        addingLineItem.value = false;
    }
}

// Inline editing
const editingLineItemId = ref<number | null>(null);
const editForm = reactive({ name: '', unit_price: '', quantity: '' });

function startEditLineItem(item: LineItem) {
    editingLineItemId.value = item.id;
    editForm.name = item.name;
    editForm.unit_price = parseFloat(item.unit_price).toFixed(2);
    editForm.quantity = parseFloat(item.quantity).toString();
}

async function saveLineItem(item: LineItem) {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null;
    const res = await fetch(`/api/technician/jobs/${props.job.id}/line-items/${item.id}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(csrfMeta ? { 'X-CSRF-TOKEN': csrfMeta.content } : {}),
        },
        body: JSON.stringify({
            name: editForm.name,
            unit_price: parseFloat(editForm.unit_price),
            quantity: parseFloat(editForm.quantity),
        }),
    });
    if (res.ok) {
        const json = await res.json();
        const idx = lineItems.findIndex((li) => li.id === item.id);
        if (idx !== -1) lineItems.splice(idx, 1, json.data);
        editingLineItemId.value = null;
    }
}

function cancelEditLineItem() { editingLineItemId.value = null; }

async function removeLineItem(item: LineItem) {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null;
    const res = await fetch(`/api/technician/jobs/${props.job.id}/line-items/${item.id}`, {
        method: 'DELETE',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(csrfMeta ? { 'X-CSRF-TOKEN': csrfMeta.content } : {}),
        },
    });
    if (res.ok) {
        const idx = lineItems.findIndex((li) => li.id === item.id);
        if (idx !== -1) lineItems.splice(idx, 1);
    }
}

const directionsUrl = computed(() => {
    const p = props.job.property;
    if (!p) return null;
    const q = encodeURIComponent(`${p.address_line1}, ${p.city}, ${p.state} ${p.postal_code}`);
    return `https://www.google.com/maps/dir/?api=1&destination=${q}`;
});

function toggleChecklistItem(item: ChecklistItem) {
    const next = !checklistState[item.id];
    checklistState[item.id] = next;
    togglingItem.value = item.id;

    router.patch(
        `/api/technician/jobs/${props.job.id}/checklist/${item.id}`,
        { completed: next },
        {
            preserveScroll: true,
            preserveState: true,
            onError: () => {
                checklistState[item.id] = !next;
            },
            onFinish: () => {
                togglingItem.value = null;
            },
        },
    );
}

function changeStatus(newStatus: string) {
    statusForm.status = newStatus;
    statusForm.patch(`/api/technician/jobs/${props.job.id}/status`, {
        onSuccess: () => statusForm.reset(),
    });
}

function saveNotes() {
    notesForm.patch(`/api/technician/jobs/${props.job.id}/notes`, {
        onSuccess: () => { editingNotes.value = false; },
    });
}

function saveCustomerNotes() {
    customerNotesForm.patch(`/api/technician/jobs/${props.job.id}/customer-notes`, {
        onSuccess: () => { editingCustomerNotes.value = false; },
    });
}

function formatDate(dt: string | null): string {
    if (!dt) return '—';
    return new Date(dt).toLocaleString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric',
        hour: 'numeric', minute: '2-digit',
    });
}

function formatTime(dt: string | null): string {
    if (!dt) return '—';
    return new Date(dt).toLocaleString('en-US', {
        month: 'short', day: 'numeric',
        hour: 'numeric', minute: '2-digit',
    });
}

const MSG_EVENT_LABELS: Record<string, string> = {
    job_scheduled: 'Confirmation sent',
    job_reminder:  'Reminder sent',
    en_route:      'En route notification sent',
    job_completed: 'Completion message sent',
};

// Combine job timestamps and outbound messages into a single sorted timeline
const timeline = computed(() => {
    const entries: { ts: number; label: string; sub?: string; type: 'status' | 'message' }[] = [];

    const add = (dt: string | null, label: string, type: 'status' | 'message' = 'status', sub?: string) => {
        if (dt) entries.push({ ts: new Date(dt).getTime(), label, sub, type });
    };

    add(props.job.scheduled_at, 'Scheduled');
    add(props.job.arrived_at,   'Arrived on site');
    add(props.job.started_at,   'Work started');
    add(props.job.completed_at, 'Job completed');

    for (const msg of (props.job.messages ?? [])) {
        if (msg.status === 'sent') {
            add(msg.created_at, MSG_EVENT_LABELS[msg.event] ?? msg.event, 'message',
                msg.channel === 'email' ? 'Email' : 'SMS');
        }
    }

    return entries.sort((a, b) => a.ts - b.ts);
});
</script>

<template>
    <TechnicianLayout :title="job.title">
        <Head :title="job.title" />

        <div class="p-4 space-y-4">
            <!-- Breadcrumb -->
            <nav class="text-sm text-slate-500">
                <Link href="/technician/jobs" class="hover:underline">Jobs</Link>
                <span class="mx-1">›</span>
                <span class="text-slate-800">{{ job.title }}</span>
            </nav>

            <!-- Title + status -->
            <div class="flex items-start justify-between gap-2">
                <h2 class="text-xl font-bold text-slate-900">{{ job.title }}</h2>
                <span
                    class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-medium"
                    :class="STATUS_CLASSES[job.status] ?? 'bg-slate-100 text-slate-600'"
                >
                    {{ statuses[job.status] ?? job.status }}
                </span>
            </div>

            <!-- Update status -->
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="border-b border-slate-100 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-700">Update Status</h3>
                </div>
                <div class="flex flex-wrap gap-2 p-4">
                    <button
                        v-for="action in TECHNICIAN_ACTIONS"
                        :key="action.key"
                        type="button"
                        class="rounded-lg border px-3 py-1.5 text-xs font-medium transition"
                        :class="job.status === action.key
                            ? 'border-slate-800 bg-slate-800 text-white'
                            : 'border-slate-200 text-slate-600 active:bg-slate-50'"
                        :disabled="job.status === action.key || statusForm.processing"
                        @click="changeStatus(action.key)"
                    >
                        {{ action.label }}
                    </button>
                </div>
            </div>

            <!-- Job details -->
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="border-b border-slate-100 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-700">Details</h3>
                </div>
                <dl class="divide-y divide-slate-100 text-sm">
                    <div v-if="job.customer" class="flex justify-between px-4 py-3">
                        <dt class="text-slate-500">Customer</dt>
                        <dd class="font-medium text-slate-800">
                            {{ job.customer.first_name }} {{ job.customer.last_name }}
                        </dd>
                    </div>
                    <div v-if="job.customer?.phone" class="flex justify-between px-4 py-3">
                        <dt class="text-slate-500">Phone</dt>
                        <dd class="font-medium text-slate-800">
                            <a :href="`tel:${job.customer.phone}`" class="text-blue-600">
                                {{ job.customer.phone }}
                            </a>
                        </dd>
                    </div>
                    <div v-if="job.property" class="flex justify-between gap-4 px-4 py-3">
                        <dt class="text-slate-500">Address</dt>
                        <dd class="text-right font-medium text-slate-800">
                            <p>
                                {{ job.property.address_line1 }},
                                {{ job.property.city }}, {{ job.property.state }}
                                {{ job.property.postal_code }}
                            </p>
                            <a
                                v-if="directionsUrl"
                                :href="directionsUrl"
                                target="_blank"
                                rel="noopener"
                                class="mt-1 inline-block text-xs font-medium text-blue-600"
                            >
                                Get directions →
                            </a>
                        </dd>
                    </div>
                    <div v-if="job.job_type" class="flex justify-between px-4 py-3">
                        <dt class="text-slate-500">Type</dt>
                        <dd class="flex items-center gap-1.5 font-medium text-slate-800">
                            <span class="h-2 w-2 rounded-full" :style="{ background: job.job_type.color }" />
                            {{ job.job_type.name }}
                        </dd>
                    </div>
                    <div class="flex justify-between px-4 py-3">
                        <dt class="text-slate-500">Scheduled</dt>
                        <dd class="font-medium text-slate-800">{{ formatDate(job.scheduled_at) }}</dd>
                    </div>
                    <div v-if="job.arrived_at" class="flex justify-between px-4 py-3">
                        <dt class="text-slate-500">Arrived</dt>
                        <dd class="font-medium text-slate-800">{{ formatDate(job.arrived_at) }}</dd>
                    </div>
                    <div v-if="job.started_at" class="flex justify-between px-4 py-3">
                        <dt class="text-slate-500">Started</dt>
                        <dd class="font-medium text-slate-800">{{ formatDate(job.started_at) }}</dd>
                    </div>
                    <div v-if="job.completed_at" class="flex justify-between px-4 py-3">
                        <dt class="text-slate-500">Completed</dt>
                        <dd class="font-medium text-green-700">{{ formatDate(job.completed_at) }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Checklist -->
            <div
                v-if="job.checklist_items && job.checklist_items.length > 0"
                class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200"
            >
                <div class="border-b border-slate-100 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-700">Checklist</h3>
                </div>
                <ul class="divide-y divide-slate-100">
                    <li
                        v-for="item in job.checklist_items"
                        :key="item.id"
                        class="flex items-center gap-3 px-4 py-2"
                    >
                        <button
                            type="button"
                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded border transition"
                            :class="checklistState[item.id]
                                ? 'border-green-600 bg-green-600 text-white'
                                : 'border-slate-300 bg-white'"
                            :disabled="togglingItem === item.id"
                            :aria-pressed="checklistState[item.id]"
                            :aria-label="`Toggle ${item.label}`"
                            @click="toggleChecklistItem(item)"
                        >
                            <svg
                                v-if="checklistState[item.id]"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                class="h-3.5 w-3.5"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.42 0l-3.5-3.5a1 1 0 111.42-1.42L8.5 12.08l6.79-6.79a1 1 0 011.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                        <div class="flex-1">
                            <p
                                class="text-sm"
                                :class="checklistState[item.id]
                                    ? 'text-slate-400 line-through'
                                    : 'text-slate-800'"
                            >
                                {{ item.label }}
                                <span v-if="item.is_required" class="ml-1 text-red-500">*</span>
                            </p>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Photos -->
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="border-b border-slate-100 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-700">Photos</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    <!-- Before -->
                    <div class="p-4">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Before</span>
                            <button
                                type="button"
                                class="rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-medium text-slate-600 active:bg-slate-50 disabled:opacity-50"
                                :disabled="uploadingTag !== null"
                                @click="beforeInputRef?.click()"
                            >
                                <span v-if="uploadingTag === 'before'">Uploading {{ uploadProgress }}%…</span>
                                <span v-else>+ Add photo</span>
                            </button>
                            <input
                                ref="beforeInputRef"
                                type="file"
                                accept="image/*"
                                capture="environment"
                                class="sr-only"
                                @change="handlePhotoCapture($event, 'before')"
                            />
                        </div>
                        <div v-if="beforePhotos.length" class="grid grid-cols-3 gap-2">
                            <div
                                v-for="photo in beforePhotos"
                                :key="photo.id"
                                class="group relative aspect-square overflow-hidden rounded-lg bg-slate-100"
                            >
                                <img :src="photo.url" :alt="photo.filename" class="h-full w-full object-cover" />
                                <button
                                    type="button"
                                    class="absolute right-1 top-1 flex h-5 w-5 items-center justify-center rounded-full bg-black/60 text-white opacity-0 transition group-hover:opacity-100 active:opacity-100 disabled:opacity-50"
                                    :disabled="deletingPhotoId === photo.id"
                                    aria-label="Delete photo"
                                    @click="deletePhoto(photo)"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3 w-3">
                                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p v-else class="text-xs text-slate-400">No before photos yet.</p>
                    </div>

                    <!-- After -->
                    <div class="p-4">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">After</span>
                            <button
                                type="button"
                                class="rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-medium text-slate-600 active:bg-slate-50 disabled:opacity-50"
                                :disabled="uploadingTag !== null"
                                @click="afterInputRef?.click()"
                            >
                                <span v-if="uploadingTag === 'after'">Uploading {{ uploadProgress }}%…</span>
                                <span v-else>+ Add photo</span>
                            </button>
                            <input
                                ref="afterInputRef"
                                type="file"
                                accept="image/*"
                                capture="environment"
                                class="sr-only"
                                @change="handlePhotoCapture($event, 'after')"
                            />
                        </div>
                        <div v-if="afterPhotos.length" class="grid grid-cols-3 gap-2">
                            <div
                                v-for="photo in afterPhotos"
                                :key="photo.id"
                                class="group relative aspect-square overflow-hidden rounded-lg bg-slate-100"
                            >
                                <img :src="photo.url" :alt="photo.filename" class="h-full w-full object-cover" />
                                <button
                                    type="button"
                                    class="absolute right-1 top-1 flex h-5 w-5 items-center justify-center rounded-full bg-black/60 text-white opacity-0 transition group-hover:opacity-100 active:opacity-100 disabled:opacity-50"
                                    :disabled="deletingPhotoId === photo.id"
                                    aria-label="Delete photo"
                                    @click="deletePhoto(photo)"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3 w-3">
                                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p v-else class="text-xs text-slate-400">No after photos yet.</p>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div v-if="job.description" class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="border-b border-slate-100 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-700">Description</h3>
                </div>
                <p class="whitespace-pre-wrap px-4 py-3 text-sm text-slate-600">{{ job.description }}</p>
            </div>

            <!-- Office notes (read-only) -->
            <div v-if="job.office_notes" class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="border-b border-slate-100 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-700">Office Notes</h3>
                </div>
                <p class="whitespace-pre-wrap px-4 py-3 text-sm text-slate-600">{{ job.office_notes }}</p>
            </div>

            <!-- Technician notes (internal, editable) -->
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-700">My Notes</h3>
                        <p class="text-xs text-slate-400">Internal — not shown to the customer</p>
                    </div>
                    <button
                        v-if="!editingNotes"
                        type="button"
                        class="text-xs font-medium text-blue-600"
                        @click="editingNotes = true"
                    >
                        Edit
                    </button>
                </div>

                <div v-if="editingNotes" class="p-4">
                    <textarea
                        v-model="notesForm.technician_notes"
                        rows="4"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 focus:border-slate-500 focus:outline-none"
                        placeholder="Add your notes here…"
                    />
                    <div class="mt-2 flex justify-end gap-2">
                        <button
                            type="button"
                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600"
                            @click="editingNotes = false"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-medium text-white"
                            :disabled="notesForm.processing"
                            @click="saveNotes"
                        >
                            Save
                        </button>
                    </div>
                </div>
                <p v-else-if="notesForm.technician_notes" class="whitespace-pre-wrap px-4 py-3 text-sm text-slate-600">
                    {{ notesForm.technician_notes }}
                </p>
                <p v-else class="px-4 py-3 text-sm text-slate-400">No notes yet. Tap Edit to add.</p>
            </div>

            <!-- Customer notes (customer-facing, editable) -->
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-700">Customer Notes</h3>
                        <p class="text-xs text-slate-400">Visible to the customer on their summary</p>
                    </div>
                    <button
                        v-if="!editingCustomerNotes"
                        type="button"
                        class="text-xs font-medium text-blue-600"
                        @click="editingCustomerNotes = true"
                    >
                        Edit
                    </button>
                </div>

                <div v-if="editingCustomerNotes" class="p-4">
                    <textarea
                        v-model="customerNotesForm.customer_notes"
                        rows="4"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 focus:border-slate-500 focus:outline-none"
                        placeholder="Notes for the customer (e.g. what was done, recommendations)…"
                    />
                    <div class="mt-2 flex justify-end gap-2">
                        <button
                            type="button"
                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600"
                            @click="editingCustomerNotes = false"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-medium text-white"
                            :disabled="customerNotesForm.processing"
                            @click="saveCustomerNotes"
                        >
                            Save
                        </button>
                    </div>
                </div>
                <p v-else-if="customerNotesForm.customer_notes" class="whitespace-pre-wrap px-4 py-3 text-sm text-slate-600">
                    {{ customerNotesForm.customer_notes }}
                </p>
                <p v-else class="px-4 py-3 text-sm text-slate-400">No customer notes yet. Tap Edit to add.</p>
            </div>

            <!-- Activity timeline (#94) -->
            <div v-if="timeline.length > 0" class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="border-b border-slate-100 px-4 py-3">
                    <h3 class="text-sm font-semibold text-slate-700">Activity</h3>
                </div>
                <ol class="px-4 py-3">
                    <li
                        v-for="(entry, i) in timeline"
                        :key="i"
                        class="relative flex gap-3 pb-4 last:pb-0"
                    >
                        <!-- Connector line -->
                        <div class="flex flex-col items-center">
                            <span
                                class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full"
                                :class="entry.type === 'message'
                                    ? 'bg-blue-100 text-blue-600'
                                    : 'bg-slate-200 text-slate-500'"
                            >
                                <!-- envelope for messages, dot for status -->
                                <svg v-if="entry.type === 'message'" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" />
                                    <path d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" />
                                </svg>
                                <span v-else class="h-1.5 w-1.5 rounded-full bg-slate-500" />
                            </span>
                            <div v-if="i < timeline.length - 1" class="mt-1 w-px flex-1 bg-slate-100" />
                        </div>
                        <!-- Content -->
                        <div class="min-w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium text-slate-800">{{ entry.label }}</p>
                            <div class="mt-0.5 flex items-center gap-2">
                                <span v-if="entry.sub" class="rounded bg-slate-100 px-1 py-0.5 text-xs text-slate-500">{{ entry.sub }}</span>
                                <span class="text-xs text-slate-400">{{ formatTime(new Date(entry.ts).toISOString()) }}</span>
                            </div>
                        </div>
                    </li>
                </ol>
            </div>
        </div>
    </TechnicianLayout>
</template>

<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Customer {
    id: number;
    first_name: string;
    last_name: string;
    properties: { id: number; address_line1: string; city: string; state: string }[];
}

interface JobType { id: number; name: string; color: string }
interface Technician { id: number; name: string }

interface JobFormData {
    customer_id: number | null;
    property_id: number | null;
    job_type_id: number | null;
    assigned_to: number | null;
    title: string;
    description: string;
    scheduled_at: string;
    office_notes: string;
}

const props = defineProps<{
    form: InertiaForm<JobFormData>;
    customers: Customer[];
    jobTypes: JobType[];
    technicians: Technician[];
}>();

const selectedCustomerProperties = computed(() => {
    if (!props.form.customer_id) return [];
    return props.customers.find(c => c.id === Number(props.form.customer_id))?.properties ?? [];
});

function onCustomerChange() {
    props.form.property_id = null;
}
</script>

<template>
    <div class="space-y-5">
        <!-- Title -->
        <div>
            <label for="title" class="block text-sm font-medium text-slate-700">Title <span class="text-red-500">*</span></label>
            <input
                id="title"
                v-model="form.title"
                type="text"
                class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none"
                :class="{ 'border-red-400': form.errors.title }"
            />
            <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p>
        </div>

        <!-- Customer + Property row -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="customer_id" class="block text-sm font-medium text-slate-700">Customer <span class="text-red-500">*</span></label>
                <select
                    id="customer_id"
                    v-model="form.customer_id"
                    class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none"
                    :class="{ 'border-red-400': form.errors.customer_id }"
                    @change="onCustomerChange"
                >
                    <option :value="null">— Select customer —</option>
                    <option v-for="c in customers" :key="c.id" :value="c.id">
                        {{ c.last_name }}, {{ c.first_name }}
                    </option>
                </select>
                <p v-if="form.errors.customer_id" class="mt-1 text-xs text-red-600">{{ form.errors.customer_id }}</p>
            </div>
            <div>
                <label for="property_id" class="block text-sm font-medium text-slate-700">Property</label>
                <select
                    id="property_id"
                    v-model="form.property_id"
                    :disabled="!form.customer_id || selectedCustomerProperties.length === 0"
                    class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none disabled:bg-slate-50 disabled:text-slate-400"
                >
                    <option :value="null">— Select property —</option>
                    <option v-for="p in selectedCustomerProperties" :key="p.id" :value="p.id">
                        {{ p.address_line1 }}, {{ p.city }}, {{ p.state }}
                    </option>
                </select>
            </div>
        </div>

        <!-- Job type + Technician row -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="job_type_id" class="block text-sm font-medium text-slate-700">Job Type</label>
                <select
                    id="job_type_id"
                    v-model="form.job_type_id"
                    class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none"
                >
                    <option :value="null">— Select type —</option>
                    <option v-for="t in jobTypes" :key="t.id" :value="t.id">{{ t.name }}</option>
                </select>
            </div>
            <div>
                <label for="assigned_to" class="block text-sm font-medium text-slate-700">Assign To</label>
                <select
                    id="assigned_to"
                    v-model="form.assigned_to"
                    class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none"
                >
                    <option :value="null">— Unassigned —</option>
                    <option v-for="t in technicians" :key="t.id" :value="t.id">{{ t.name }}</option>
                </select>
            </div>
        </div>

        <!-- Scheduled at -->
        <div>
            <label for="scheduled_at" class="block text-sm font-medium text-slate-700">Scheduled Date & Time</label>
            <input
                id="scheduled_at"
                v-model="form.scheduled_at"
                type="datetime-local"
                class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none"
            />
            <p v-if="form.errors.scheduled_at" class="mt-1 text-xs text-red-600">{{ form.errors.scheduled_at }}</p>
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
            <textarea
                id="description"
                v-model="form.description"
                rows="3"
                class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none"
            />
        </div>

        <!-- Office notes -->
        <div>
            <label for="office_notes" class="block text-sm font-medium text-slate-700">Office Notes</label>
            <textarea
                id="office_notes"
                v-model="form.office_notes"
                rows="3"
                class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none"
            />
        </div>
    </div>
</template>

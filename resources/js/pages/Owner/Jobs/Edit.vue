<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import JobForm from './partials/JobForm.vue';

interface Customer {
    id: number; first_name: string; last_name: string;
    properties: { id: number; address_line1: string; city: string; state: string }[];
}
interface JobType    { id: number; name: string; color: string }
interface Technician { id: number; name: string }
interface Job {
    id: number; title: string; description: string | null;
    customer_id: number | null; property_id: number | null;
    job_type_id: number | null; assigned_to: number | null;
    scheduled_at: string | null; office_notes: string | null;
}

const props = defineProps<{
    job: Job;
    customers: Customer[];
    jobTypes: JobType[];
    technicians: Technician[];
}>();

// datetime-local input expects "YYYY-MM-DDTHH:mm"
function toDatetimeLocal(dt: string | null): string {
    if (!dt) return '';
    return new Date(dt).toISOString().slice(0, 16);
}

const form = useForm({
    customer_id:  props.job.customer_id,
    property_id:  props.job.property_id,
    job_type_id:  props.job.job_type_id,
    assigned_to:  props.job.assigned_to,
    title:        props.job.title,
    description:  props.job.description ?? '',
    scheduled_at: toDatetimeLocal(props.job.scheduled_at),
    office_notes: props.job.office_notes ?? '',
});

function submit() {
    form.patch(`/owner/jobs/${props.job.id}`);
}
</script>

<template>
    <OwnerLayout :title="`Edit — ${job.title}`">
        <Head :title="`Edit Job`" />

        <nav class="mb-4 text-sm text-slate-500">
            <Link href="/owner/jobs" class="hover:underline">Jobs</Link>
            <span class="mx-1">›</span>
            <Link :href="`/owner/jobs/${job.id}`" class="hover:underline">{{ job.title }}</Link>
            <span class="mx-1">›</span>
            <span class="text-slate-800">Edit</span>
        </nav>

        <div class="max-w-2xl">
            <div class="rounded-xl bg-white shadow">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-slate-800">Edit Job</h2>
                </div>
                <form @submit.prevent="submit" class="px-6 py-5">
                    <JobForm
                        :form="form"
                        :customers="customers"
                        :job-types="jobTypes"
                        :technicians="technicians"
                    />

                    <div class="mt-6 flex items-center gap-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center rounded-lg bg-slate-800 px-5 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                        >
                            Save Changes
                        </button>
                        <Link :href="`/owner/jobs/${job.id}`" class="text-sm text-slate-500 hover:text-slate-700">Cancel</Link>
                    </div>
                </form>
            </div>
        </div>
    </OwnerLayout>
</template>

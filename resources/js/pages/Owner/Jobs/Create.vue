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

const props = defineProps<{
    customers: Customer[];
    jobTypes: JobType[];
    technicians: Technician[];
    preselect: { customer_id?: string | number; property_id?: string | number };
}>();

const form = useForm({
    customer_id:  props.preselect.customer_id ? Number(props.preselect.customer_id) : null as number | null,
    property_id:  props.preselect.property_id ? Number(props.preselect.property_id) : null as number | null,
    job_type_id:  null as number | null,
    assigned_to:  null as number | null,
    title:        '',
    description:  '',
    scheduled_at: '',
    office_notes: '',
});

function submit() {
    form.post('/owner/jobs');
}
</script>

<template>
    <OwnerLayout title="New Job">
        <Head title="New Job" />

        <nav class="mb-4 text-sm text-slate-500">
            <Link href="/owner/jobs" class="hover:underline">Jobs</Link>
            <span class="mx-1">›</span>
            <span class="text-slate-800">New Job</span>
        </nav>

        <div class="max-w-2xl">
            <div class="rounded-xl bg-white shadow">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-slate-800">Create Job</h2>
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
                            Create Job
                        </button>
                        <Link href="/owner/jobs" class="text-sm text-slate-500 hover:text-slate-700">Cancel</Link>
                    </div>
                </form>
            </div>
        </div>
    </OwnerLayout>
</template>

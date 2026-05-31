<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
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
    preselect: { customer_id?: string | number; property_id?: string | number; scheduled_at?: string };
}>();

const customers = ref<Customer[]>([...props.customers]);
const jobTypes  = ref<JobType[]>([...props.jobTypes]);

const form = useForm({
    customer_id:  props.preselect.customer_id ? Number(props.preselect.customer_id) : null as number | null,
    property_id:  props.preselect.property_id ? Number(props.preselect.property_id) : null as number | null,
    job_type_id:  null as number | null,
    assigned_to:  null as number | null,
    title:        '',
    description:  '',
    scheduled_at: props.preselect.scheduled_at ? props.preselect.scheduled_at.slice(0, 16) : '',
    office_notes: '',
});

function onCustomerAdded(customer: Customer) {
    customers.value.push(customer);
}

function onPropertyAdded(property: Customer['properties'][number]) {
    const customer = customers.value.find(c => c.id === form.customer_id);
    if (customer) customer.properties.push(property);
}

function onJobTypeAdded(jobType: JobType) {
    jobTypes.value.push(jobType);
}

function submit() {
    form.post('/owner/jobs');
}
</script>

<template>
    <OwnerLayout title="New Job">
        <Head title="New Job" />

        <nav class="mb-4 text-sm text-muted-foreground">
            <Link href="/owner/jobs" class="hover:underline">Jobs</Link>
            <span class="mx-1">›</span>
            <span class="text-foreground">New Job</span>
        </nav>

        <div class="max-w-2xl">
            <div class="rounded-xl bg-card shadow ring-1 ring-border">
                <div class="border-b border-border px-6 py-4">
                    <h2 class="text-base font-semibold text-foreground">Create Job</h2>
                </div>
                <form @submit.prevent="submit" class="px-6 py-5">
                    <JobForm
                        :form="form"
                        :customers="customers"
                        :job-types="jobTypes"
                        :technicians="technicians"
                        @customer-added="onCustomerAdded"
                        @property-added="onPropertyAdded"
                        @job-type-added="onJobTypeAdded"
                    />

                    <div class="mt-6 flex items-center gap-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center rounded-lg bg-slate-800 px-5 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                        >
                            Create Job
                        </button>
                        <Link href="/owner/jobs" class="text-sm text-muted-foreground hover:text-foreground">Cancel</Link>
                    </div>
                </form>
            </div>
        </div>
    </OwnerLayout>
</template>

<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PropertyForm from './partials/PropertyForm.vue';

interface Customer {
    id: number;
    first_name: string;
    last_name: string;
}

interface Property {
    id: number;
    customer_id: number;
    name: string | null;
    address_line1: string;
    address_line2: string | null;
    city: string;
    state: string;
    postal_code: string;
    country: string;
    notes: string | null;
}

const props = defineProps<{ property: Property; customer: Customer }>();

const form = useForm({
    name: props.property.name ?? '',
    address_line1: props.property.address_line1,
    address_line2: props.property.address_line2 ?? '',
    city: props.property.city,
    state: props.property.state,
    postal_code: props.property.postal_code,
    country: props.property.country,
    notes: props.property.notes ?? '',
});

function submit() {
    form.patch(`/owner/properties/${props.property.id}`);
}
</script>

<template>
    <OwnerLayout title="Edit Property">
        <Head title="Edit Property" />

        <nav class="mb-4 text-sm text-slate-500">
            <Link href="/owner/customers" class="hover:underline">Customers</Link>
            <span class="mx-1">›</span>
            <Link :href="`/owner/customers/${customer.id}`" class="hover:underline">
                {{ customer.first_name }} {{ customer.last_name }}
            </Link>
            <span class="mx-1">›</span>
            <span class="text-slate-800">Edit Property</span>
        </nav>

        <div class="max-w-2xl">
            <div class="rounded-xl bg-white shadow">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-slate-800">Edit Property</h2>
                </div>
                <form @submit.prevent="submit" class="px-6 py-5">
                    <PropertyForm :form="form" />

                    <div class="mt-6 flex items-center gap-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center rounded-lg bg-slate-800 px-5 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                        >
                            Save Changes
                        </button>
                        <Link
                            :href="`/owner/customers/${customer.id}`"
                            class="text-sm text-slate-500 hover:text-slate-700"
                        >
                            Cancel
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </OwnerLayout>
</template>

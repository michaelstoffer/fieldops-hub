<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import CustomerForm from './partials/CustomerForm.vue';

interface Customer {
    id: number;
    first_name: string;
    last_name: string;
    email: string | null;
    phone: string | null;
    mobile: string | null;
    notes: string | null;
}

const props = defineProps<{ customer: Customer }>();

const form = useForm({
    first_name: props.customer.first_name,
    last_name: props.customer.last_name,
    email: props.customer.email ?? '',
    phone: props.customer.phone ?? '',
    mobile: props.customer.mobile ?? '',
    notes: props.customer.notes ?? '',
});

function submit() {
    form.patch(`/owner/customers/${props.customer.id}`);
}
</script>

<template>
    <OwnerLayout :title="`Edit — ${customer.first_name} ${customer.last_name}`">
        <Head :title="`Edit ${customer.first_name} ${customer.last_name}`" />

        <!-- Breadcrumb -->
        <nav class="mb-4 text-sm text-slate-500">
            <Link href="/owner/customers" class="hover:underline">Customers</Link>
            <span class="mx-1">›</span>
            <Link :href="`/owner/customers/${customer.id}`" class="hover:underline">
                {{ customer.first_name }} {{ customer.last_name }}
            </Link>
            <span class="mx-1">›</span>
            <span class="text-slate-800">Edit</span>
        </nav>

        <div class="max-w-2xl">
            <div class="rounded-xl bg-white shadow">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-slate-800">Edit Customer</h2>
                </div>
                <form @submit.prevent="submit" class="px-6 py-5">
                    <CustomerForm :form="form" />

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

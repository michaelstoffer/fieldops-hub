<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import CustomerForm from './partials/CustomerForm.vue';

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    mobile: '',
    notes: '',
});

function submit() {
    form.post('/owner/customers');
}
</script>

<template>
    <OwnerLayout title="Add Customer">
        <Head title="Add Customer" />

        <!-- Breadcrumb -->
        <nav class="mb-4 text-sm text-slate-500">
            <Link href="/owner/customers" class="hover:underline">Customers</Link>
            <span class="mx-1">›</span>
            <span class="text-slate-800">Add Customer</span>
        </nav>

        <div class="max-w-2xl">
            <div class="rounded-xl bg-white shadow">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-slate-800">New Customer</h2>
                </div>
                <form @submit.prevent="submit" class="px-6 py-5">
                    <CustomerForm :form="form" />

                    <div class="mt-6 flex items-center gap-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center rounded-lg bg-slate-800 px-5 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                        >
                            Save Customer
                        </button>
                        <Link
                            href="/owner/customers"
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

<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PropertyForm from './partials/PropertyForm.vue';

interface Customer {
    id: number;
    first_name: string;
    last_name: string;
}

const props = defineProps<{ customer: Customer }>();

const form = useForm({
    name: '',
    address_line1: '',
    address_line2: '',
    city: '',
    state: '',
    postal_code: '',
    country: 'US',
    notes: '',
});

function submit() {
    form.post(`/owner/customers/${props.customer.id}/properties`);
}
</script>

<template>
    <OwnerLayout title="Add Property">
        <Head title="Add Property" />

        <nav class="mb-4 text-sm text-muted-foreground">
            <Link href="/owner/customers" class="hover:underline">Customers</Link>
            <span class="mx-1">›</span>
            <Link :href="`/owner/customers/${customer.id}`" class="hover:underline">
                {{ customer.first_name }} {{ customer.last_name }}
            </Link>
            <span class="mx-1">›</span>
            <span class="text-foreground">Add Property</span>
        </nav>

        <div class="max-w-2xl">
            <div class="rounded-xl bg-card shadow ring-1 ring-border">
                <div class="border-b border-border px-6 py-4">
                    <h2 class="text-base font-semibold text-foreground">New Property</h2>
                </div>
                <form @submit.prevent="submit" class="px-6 py-5">
                    <PropertyForm :form="form" />

                    <div class="mt-6 flex items-center gap-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center rounded-lg bg-slate-800 px-5 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50 dark:bg-slate-700 dark:hover:bg-slate-600"
                        >
                            Save Property
                        </button>
                        <Link
                            :href="`/owner/customers/${customer.id}`"
                            class="text-sm text-muted-foreground hover:text-foreground"
                        >
                            Cancel
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </OwnerLayout>
</template>

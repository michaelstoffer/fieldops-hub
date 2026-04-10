<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

interface Customer {
    id: number;
    first_name: string;
    last_name: string;
    email: string | null;
    phone: string | null;
    mobile: string | null;
    notes: string | null;
    created_at: string;
}

const props = defineProps<{ customer: Customer }>();

function archiveCustomer() {
    if (confirm(`Archive ${props.customer.first_name} ${props.customer.last_name}? They can be restored later.`)) {
        router.delete(`/owner/customers/${props.customer.id}`);
    }
}
</script>

<template>
    <OwnerLayout :title="`${customer.first_name} ${customer.last_name}`">
        <Head :title="`${customer.first_name} ${customer.last_name}`" />

        <!-- Breadcrumb -->
        <nav class="mb-4 text-sm text-slate-500">
            <Link href="/owner/customers" class="hover:underline">Customers</Link>
            <span class="mx-1">›</span>
            <span class="text-slate-800">{{ customer.first_name }} {{ customer.last_name }}</span>
        </nav>

        <!-- Header -->
        <div class="mb-6 flex items-start justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-800">
                    {{ customer.first_name }} {{ customer.last_name }}
                </h2>
            </div>
            <div class="flex gap-2">
                <Link
                    :href="`/owner/customers/${customer.id}/edit`"
                    class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                >
                    Edit
                </Link>
                <button
                    type="button"
                    class="inline-flex items-center rounded-lg border border-red-200 bg-white px-4 py-2 text-sm font-medium text-red-600 shadow-sm hover:bg-red-50"
                    @click="archiveCustomer"
                >
                    Archive
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Contact details -->
            <div class="lg:col-span-1">
                <div class="rounded-xl bg-white shadow">
                    <div class="border-b border-slate-100 px-5 py-3">
                        <h3 class="text-sm font-semibold text-slate-700">Contact Details</h3>
                    </div>
                    <dl class="divide-y divide-slate-100">
                        <div class="flex justify-between px-5 py-3 text-sm">
                            <dt class="text-slate-500">Email</dt>
                            <dd class="font-medium text-slate-800">
                                <a v-if="customer.email" :href="`mailto:${customer.email}`" class="hover:underline">
                                    {{ customer.email }}
                                </a>
                                <span v-else class="text-slate-400">—</span>
                            </dd>
                        </div>
                        <div class="flex justify-between px-5 py-3 text-sm">
                            <dt class="text-slate-500">Phone</dt>
                            <dd class="font-medium text-slate-800">
                                <a v-if="customer.phone" :href="`tel:${customer.phone}`" class="hover:underline">
                                    {{ customer.phone }}
                                </a>
                                <span v-else class="text-slate-400">—</span>
                            </dd>
                        </div>
                        <div class="flex justify-between px-5 py-3 text-sm">
                            <dt class="text-slate-500">Mobile</dt>
                            <dd class="font-medium text-slate-800">
                                <a v-if="customer.mobile" :href="`tel:${customer.mobile}`" class="hover:underline">
                                    {{ customer.mobile }}
                                </a>
                                <span v-else class="text-slate-400">—</span>
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Notes -->
                <div v-if="customer.notes" class="mt-6 rounded-xl bg-white shadow">
                    <div class="border-b border-slate-100 px-5 py-3">
                        <h3 class="text-sm font-semibold text-slate-700">Notes</h3>
                    </div>
                    <p class="px-5 py-4 text-sm text-slate-600 whitespace-pre-wrap">{{ customer.notes }}</p>
                </div>
            </div>

            <!-- Right column: Properties + Jobs (stubs) -->
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-xl bg-white shadow">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-3">
                        <h3 class="text-sm font-semibold text-slate-700">Properties</h3>
                    </div>
                    <div class="px-5 py-8 text-center text-sm text-slate-400">
                        No properties yet.
                    </div>
                </div>

                <div class="rounded-xl bg-white shadow">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-3">
                        <h3 class="text-sm font-semibold text-slate-700">Jobs</h3>
                    </div>
                    <div class="px-5 py-8 text-center text-sm text-slate-400">
                        No jobs yet.
                    </div>
                </div>
            </div>
        </div>
    </OwnerLayout>
</template>

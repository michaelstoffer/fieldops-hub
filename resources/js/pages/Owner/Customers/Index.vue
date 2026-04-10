<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface Customer {
    id: number;
    first_name: string;
    last_name: string;
    email: string | null;
    phone: string | null;
    mobile: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedCustomers {
    data: Customer[];
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    customers: PaginatedCustomers;
    filters: { search?: string };
}>();

const search = ref(props.filters.search ?? '');

let searchTimeout: ReturnType<typeof setTimeout>;

watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            '/owner/customers',
            { search: value || undefined },
            { preserveState: true, replace: true },
        );
    }, 300);
});
</script>

<template>
    <OwnerLayout title="Customers">
        <Head title="Customers" />

        <!-- Header row -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Customers</h2>
                <p class="text-sm text-slate-500 mt-0.5">
                    {{ customers.total }} total
                </p>
            </div>
            <Link
                href="/owner/customers/create"
                class="inline-flex items-center gap-2 rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700"
            >
                + Add Customer
            </Link>
        </div>

        <!-- Search -->
        <div class="mb-4">
            <input
                v-model="search"
                type="search"
                placeholder="Search by name, email, or phone…"
                class="w-full max-w-sm rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm text-slate-800 placeholder-slate-400 shadow-sm focus:border-slate-400 focus:outline-none"
            />
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-xl bg-white shadow">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Email</th>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Phone</th>
                        <th class="relative px-5 py-3"><span class="sr-only">View</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-if="customers.data.length === 0">
                        <td colspan="4" class="px-5 py-10 text-center text-sm text-slate-400">
                            No customers found.
                        </td>
                    </tr>
                    <tr
                        v-for="customer in customers.data"
                        :key="customer.id"
                        class="hover:bg-slate-50"
                    >
                        <td class="px-5 py-3 text-sm font-medium text-slate-800">
                            <Link
                                :href="`/owner/customers/${customer.id}`"
                                class="hover:underline"
                            >
                                {{ customer.last_name }}, {{ customer.first_name }}
                            </Link>
                        </td>
                        <td class="px-5 py-3 text-sm text-slate-600">
                            {{ customer.email ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-sm text-slate-600">
                            {{ customer.phone ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-right text-sm">
                            <Link
                                :href="`/owner/customers/${customer.id}`"
                                class="font-medium text-slate-500 hover:text-slate-800"
                            >
                                View →
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div
                v-if="customers.total > 25"
                class="flex items-center justify-between border-t border-slate-100 px-5 py-3"
            >
                <p class="text-xs text-slate-500">
                    Showing {{ customers.from }}–{{ customers.to }} of {{ customers.total }}
                </p>
                <div class="flex gap-1">
                    <template v-for="link in customers.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            preserve-state
                            class="rounded px-2 py-1 text-xs"
                            :class="link.active ? 'bg-slate-800 text-white' : 'text-slate-600 hover:bg-slate-100'"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            class="rounded px-2 py-1 text-xs text-slate-300"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </OwnerLayout>
</template>

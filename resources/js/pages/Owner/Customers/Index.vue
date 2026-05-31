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
                <h2 class="text-xl font-semibold text-foreground">Customers</h2>
                <p class="text-sm text-muted-foreground mt-0.5">
                    {{ customers.total }} total
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Link
                    href="/owner/customers/import"
                    class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium text-foreground hover:bg-accent"
                >
                    Import CSV
                </Link>
                <Link
                    href="/owner/customers/create"
                    class="inline-flex items-center gap-2 rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700"
                >
                    + Add Customer
                </Link>
            </div>
        </div>

        <!-- Search -->
        <div class="mb-4">
            <label for="customer-search" class="sr-only">Search customers</label>
            <input
                id="customer-search"
                v-model="search"
                type="search"
                placeholder="Search by name, email, or phone…"
                class="w-full max-w-sm rounded-lg border border-border bg-background px-4 py-2 text-sm text-foreground placeholder:text-muted-foreground shadow-sm focus:border-slate-400 focus:outline-none"
            />
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-xl bg-card shadow ring-1 ring-border">
            <table class="min-w-full divide-y divide-border" aria-label="Customers">
                <thead class="bg-background">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-muted-foreground">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-muted-foreground">Email</th>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-muted-foreground">Phone</th>
                        <th class="relative px-5 py-3"><span class="sr-only">View</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr v-if="customers.data.length === 0">
                        <td colspan="4" class="px-5 py-16 text-center">
                            <svg class="mx-auto h-10 w-10 text-muted-foreground/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                            <p class="mt-3 text-sm font-semibold text-foreground">No customers yet</p>
                            <p class="mt-1 text-sm text-muted-foreground">Add your first customer to get started.</p>
                            <Link href="/owner/customers/create" class="mt-4 inline-flex items-center rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">+ Add Customer</Link>
                        </td>
                    </tr>
                    <tr
                        v-for="customer in customers.data"
                        :key="customer.id"
                        class="cursor-pointer hover:bg-accent"
                        @click="router.visit(`/owner/customers/${customer.id}`)"
                    >
                        <td class="px-5 py-3 text-sm font-medium text-foreground">
                            {{ customer.last_name }}, {{ customer.first_name }}
                        </td>
                        <td class="px-5 py-3 text-sm text-muted-foreground">{{ customer.email ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-muted-foreground">{{ customer.phone ?? '—' }}</td>
                        <td class="px-5 py-3 text-right text-sm text-muted-foreground">View →</td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div
                v-if="customers.total > 25"
                class="flex items-center justify-between border-t border-border px-5 py-3"
            >
                <p class="text-xs text-muted-foreground">
                    Showing {{ customers.from }}–{{ customers.to }} of {{ customers.total }}
                </p>
                <div class="flex gap-1">
                    <template v-for="link in customers.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            preserve-state
                            class="rounded px-2 py-1 text-xs"
                            :class="link.active ? 'bg-slate-800 text-white' : 'text-muted-foreground hover:bg-accent'"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            class="rounded px-2 py-1 text-xs text-muted-foreground/40"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </OwnerLayout>
</template>

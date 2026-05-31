<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

interface Item {
    id: number;
    name: string;
    sku: string | null;
    description: string | null;
    unit_price: string;
    unit: string;
    is_taxable: boolean;
    is_active: boolean;
}

defineProps<{ items: Item[] }>();

function deactivate(id: number) {
    if (confirm('Deactivate this item? It will no longer appear in the catalog.')) {
        router.delete(`/owner/items/${id}`);
    }
}

function activate(id: number) {
    router.patch(`/owner/items/${id}`, { is_active: true } as any, { preserveScroll: true });
}

function formatPrice(price: string): string {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(price));
}
</script>

<template>
    <OwnerLayout title="Catalog Items">
        <Head title="Catalog Items" />

        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-xl font-semibold text-foreground">Catalog Items</h1>
            <Link
                href="/owner/items/create"
                class="inline-flex items-center rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700"
            >
                + Add Item
            </Link>
        </div>

        <div class="rounded-xl bg-card shadow-sm ring-1 ring-border overflow-hidden">
            <div v-if="items.length === 0" class="px-6 py-16 text-center">
                <svg class="mx-auto h-10 w-10 text-muted-foreground/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>
                <p class="mt-3 text-sm font-semibold text-foreground">No catalog items yet</p>
                <p class="mt-1 text-sm text-muted-foreground">Add items to use them on invoices and estimates.</p>
                <Link href="/owner/items/create" class="mt-4 inline-flex items-center rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">+ Add Item</Link>
            </div>

            <table v-else class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-background">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide">Name</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide hidden sm:table-cell">SKU</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide">Price</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide hidden md:table-cell">Unit</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide hidden md:table-cell">Tax</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr
                        v-for="item in items"
                        :key="item.id"
                        class="hover:bg-accent transition-colors"
                        :class="{ 'opacity-50': !item.is_active }"
                    >
                        <td class="px-6 py-4">
                            <p class="font-medium text-foreground">
                                {{ item.name }}
                                <span v-if="!item.is_active" class="ml-2 text-xs font-normal text-muted-foreground">(inactive)</span>
                            </p>
                            <p v-if="item.description" class="mt-0.5 text-xs text-muted-foreground truncate max-w-xs">{{ item.description }}</p>
                        </td>
                        <td class="px-6 py-4 text-muted-foreground hidden sm:table-cell">{{ item.sku ?? '—' }}</td>
                        <td class="px-6 py-4 text-right font-medium text-foreground">{{ formatPrice(item.unit_price) }}</td>
                        <td class="px-6 py-4 text-muted-foreground hidden md:table-cell">{{ item.unit }}</td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <span
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="item.is_taxable ? 'bg-green-50 text-green-700 dark:bg-green-900/40 dark:text-green-400' : 'bg-muted text-muted-foreground'"
                            >
                                {{ item.is_taxable ? 'Taxable' : 'Non-taxable' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <Link
                                    :href="`/owner/items/${item.id}/edit`"
                                    class="text-xs font-medium text-muted-foreground hover:text-foreground"
                                >
                                    Edit
                                </Link>
                                <button
                                    v-if="item.is_active"
                                    type="button"
                                    class="text-xs font-medium text-red-400 hover:text-red-600"
                                    @click="deactivate(item.id)"
                                >
                                    Deactivate
                                </button>
                                <button
                                    v-else
                                    type="button"
                                    class="text-xs font-medium text-green-600 hover:text-green-800"
                                    @click="activate(item.id)"
                                >
                                    Activate
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </OwnerLayout>
</template>

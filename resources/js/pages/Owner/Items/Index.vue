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
            <h1 class="text-xl font-semibold text-slate-800">Catalog Items</h1>
            <Link
                href="/owner/items/create"
                class="inline-flex items-center rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700"
            >
                + Add Item
            </Link>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">
            <div v-if="items.length === 0" class="px-6 py-12 text-center text-sm text-slate-400">
                No catalog items yet. Add one to get started.
            </div>

            <table v-else class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Name</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden sm:table-cell">SKU</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Price</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">Unit</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">Tax</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr
                        v-for="item in items"
                        :key="item.id"
                        class="hover:bg-slate-50 transition-colors"
                        :class="{ 'opacity-50': !item.is_active }"
                    >
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-900">
                                {{ item.name }}
                                <span v-if="!item.is_active" class="ml-2 text-xs font-normal text-slate-400">(inactive)</span>
                            </p>
                            <p v-if="item.description" class="mt-0.5 text-xs text-slate-500 truncate max-w-xs">{{ item.description }}</p>
                        </td>
                        <td class="px-6 py-4 text-slate-500 hidden sm:table-cell">{{ item.sku ?? '—' }}</td>
                        <td class="px-6 py-4 text-right font-medium text-slate-800">{{ formatPrice(item.unit_price) }}</td>
                        <td class="px-6 py-4 text-slate-500 hidden md:table-cell">{{ item.unit }}</td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <span
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="item.is_taxable ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-500'"
                            >
                                {{ item.is_taxable ? 'Taxable' : 'Non-taxable' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <Link
                                    :href="`/owner/items/${item.id}/edit`"
                                    class="text-xs font-medium text-slate-600 hover:text-slate-800"
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

<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import ItemForm from './partials/ItemForm.vue';

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

const props = defineProps<{ item: Item }>();

const form = useForm({
    name:        props.item.name,
    sku:         props.item.sku ?? '',
    description: props.item.description ?? '',
    unit_price:  props.item.unit_price,
    unit:        props.item.unit,
    is_taxable:  props.item.is_taxable,
    is_active:   props.item.is_active,
});

function submit() {
    form.patch(`/owner/items/${props.item.id}`);
}
</script>

<template>
    <OwnerLayout :title="`Edit — ${item.name}`">
        <Head title="Edit Catalog Item" />

        <nav class="mb-4 text-sm text-muted-foreground">
            <Link href="/owner/items" class="hover:underline">Catalog Items</Link>
            <span class="mx-1">›</span>
            <span class="text-foreground">{{ item.name }}</span>
        </nav>

        <div class="max-w-2xl">
            <div class="rounded-xl bg-card shadow ring-1 ring-border">
                <div class="border-b border-border px-6 py-4">
                    <h2 class="text-base font-semibold text-foreground">Edit Catalog Item</h2>
                </div>
                <form @submit.prevent="submit" class="px-6 py-5">
                    <ItemForm :form="form" />

                    <div class="mt-6 flex items-center gap-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center rounded-lg bg-slate-800 px-5 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                        >
                            Save Changes
                        </button>
                        <Link href="/owner/items" class="text-sm text-muted-foreground hover:text-foreground">Cancel</Link>
                    </div>
                </form>
            </div>
        </div>
    </OwnerLayout>
</template>

<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import ItemForm from './partials/ItemForm.vue';

const form = useForm({
    name:        '',
    sku:         '',
    description: '',
    unit_price:  '0.00',
    unit:        'each',
    is_taxable:  true,
    is_active:   true,
});

function submit() {
    form.post('/owner/items');
}
</script>

<template>
    <OwnerLayout title="New Catalog Item">
        <Head title="New Catalog Item" />

        <nav class="mb-4 text-sm text-muted-foreground">
            <Link href="/owner/items" class="hover:underline">Catalog Items</Link>
            <span class="mx-1">›</span>
            <span class="text-foreground">New Item</span>
        </nav>

        <div class="max-w-2xl">
            <div class="rounded-xl bg-card shadow ring-1 ring-border">
                <div class="border-b border-border px-6 py-4">
                    <h2 class="text-base font-semibold text-foreground">Create Catalog Item</h2>
                </div>
                <form @submit.prevent="submit" class="px-6 py-5">
                    <ItemForm :form="form" />

                    <div class="mt-6 flex items-center gap-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center rounded-lg bg-slate-800 px-5 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                        >
                            Create Item
                        </button>
                        <Link href="/owner/items" class="text-sm text-muted-foreground hover:text-foreground">Cancel</Link>
                    </div>
                </form>
            </div>
        </div>
    </OwnerLayout>
</template>

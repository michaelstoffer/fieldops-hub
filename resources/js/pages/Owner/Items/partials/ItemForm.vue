<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';

defineProps<{
    form: InertiaForm<{
        name: string;
        sku: string;
        description: string;
        unit_price: string;
        unit: string;
        is_taxable: boolean;
        is_active: boolean;
    }>;
}>();

const UNIT_OPTIONS = [
    { value: 'each', label: 'Each' },
    { value: 'hr',   label: 'Hour' },
    { value: 'ft',   label: 'Foot' },
    { value: 'sqft', label: 'Sq Ft' },
    { value: 'lb',   label: 'Pound' },
    { value: 'gal',  label: 'Gallon' },
];
</script>

<template>
    <div class="space-y-4">
        <!-- Name -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
                Name <span class="text-red-500">*</span>
            </label>
            <input
                v-model="form.name"
                type="text"
                placeholder="e.g. HVAC Filter Replacement"
                class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-500/20"
                :class="{ 'border-red-400': form.errors.name }"
            />
            <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
        </div>

        <!-- SKU -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">SKU</label>
            <input
                v-model="form.sku"
                type="text"
                placeholder="e.g. SKU-001"
                class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-500/20"
                :class="{ 'border-red-400': form.errors.sku }"
            />
            <p v-if="form.errors.sku" class="mt-1 text-xs text-red-600">{{ form.errors.sku }}</p>
        </div>

        <!-- Description -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
            <textarea
                v-model="form.description"
                rows="2"
                placeholder="Optional description shown on estimates and invoices"
                class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-500/20"
                :class="{ 'border-red-400': form.errors.description }"
            />
            <p v-if="form.errors.description" class="mt-1 text-xs text-red-600">{{ form.errors.description }}</p>
        </div>

        <!-- Unit price + Unit -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Unit Price <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500 text-sm">$</span>
                    <input
                        v-model="form.unit_price"
                        type="number"
                        min="0"
                        step="0.01"
                        placeholder="0.00"
                        class="w-full rounded-lg border border-slate-300 pl-7 pr-3.5 py-2.5 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-500/20"
                        :class="{ 'border-red-400': form.errors.unit_price }"
                    />
                </div>
                <p v-if="form.errors.unit_price" class="mt-1 text-xs text-red-600">{{ form.errors.unit_price }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Unit <span class="text-red-500">*</span>
                </label>
                <select
                    v-model="form.unit"
                    class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-500/20"
                    :class="{ 'border-red-400': form.errors.unit }"
                >
                    <option v-for="opt in UNIT_OPTIONS" :key="opt.value" :value="opt.value">
                        {{ opt.label }}
                    </option>
                </select>
                <p v-if="form.errors.unit" class="mt-1 text-xs text-red-600">{{ form.errors.unit }}</p>
            </div>
        </div>

        <!-- Taxable + Active -->
        <div class="flex items-center gap-6">
            <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                <input
                    v-model="form.is_taxable"
                    type="checkbox"
                    class="rounded border-slate-300 text-slate-800 focus:ring-slate-500"
                />
                Taxable
            </label>
            <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                <input
                    v-model="form.is_active"
                    type="checkbox"
                    class="rounded border-slate-300 text-slate-800 focus:ring-slate-500"
                />
                Active
            </label>
        </div>
    </div>
</template>

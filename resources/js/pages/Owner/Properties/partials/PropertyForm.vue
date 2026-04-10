<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';

interface PropertyFormData {
    name: string;
    address_line1: string;
    address_line2: string;
    city: string;
    state: string;
    postal_code: string;
    country: string;
    notes: string;
}

defineProps<{
    form: InertiaForm<PropertyFormData>;
}>();

const US_STATES = [
    'AL','AK','AZ','AR','CA','CO','CT','DE','FL','GA',
    'HI','ID','IL','IN','IA','KS','KY','LA','ME','MD',
    'MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ',
    'NM','NY','NC','ND','OH','OK','OR','PA','RI','SC',
    'SD','TN','TX','UT','VT','VA','WA','WV','WI','WY',
    'DC',
];
</script>

<template>
    <div class="space-y-5">
        <!-- Property name (optional label) -->
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700">Property Label <span class="text-slate-400 font-normal">(optional)</span></label>
            <input
                id="name"
                v-model="form.name"
                type="text"
                placeholder="e.g. Main Residence, Office"
                class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-slate-400 focus:outline-none"
                :class="{ 'border-red-400': form.errors.name }"
            />
            <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
        </div>

        <!-- Address line 1 -->
        <div>
            <label for="address_line1" class="block text-sm font-medium text-slate-700">Street Address <span class="text-red-500">*</span></label>
            <input
                id="address_line1"
                v-model="form.address_line1"
                type="text"
                autocomplete="address-line1"
                class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-slate-400 focus:outline-none"
                :class="{ 'border-red-400': form.errors.address_line1 }"
            />
            <p v-if="form.errors.address_line1" class="mt-1 text-xs text-red-600">{{ form.errors.address_line1 }}</p>
        </div>

        <!-- Address line 2 -->
        <div>
            <label for="address_line2" class="block text-sm font-medium text-slate-700">Apt / Suite <span class="text-slate-400 font-normal">(optional)</span></label>
            <input
                id="address_line2"
                v-model="form.address_line2"
                type="text"
                autocomplete="address-line2"
                class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-slate-400 focus:outline-none"
            />
        </div>

        <!-- City / State / ZIP row -->
        <div class="grid grid-cols-2 gap-5 sm:grid-cols-4">
            <div class="col-span-2 sm:col-span-2">
                <label for="city" class="block text-sm font-medium text-slate-700">City <span class="text-red-500">*</span></label>
                <input
                    id="city"
                    v-model="form.city"
                    type="text"
                    autocomplete="address-level2"
                    class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-slate-400 focus:outline-none"
                    :class="{ 'border-red-400': form.errors.city }"
                />
                <p v-if="form.errors.city" class="mt-1 text-xs text-red-600">{{ form.errors.city }}</p>
            </div>
            <div>
                <label for="state" class="block text-sm font-medium text-slate-700">State <span class="text-red-500">*</span></label>
                <select
                    id="state"
                    v-model="form.state"
                    class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-slate-400 focus:outline-none"
                    :class="{ 'border-red-400': form.errors.state }"
                >
                    <option value="">—</option>
                    <option v-for="s in US_STATES" :key="s" :value="s">{{ s }}</option>
                </select>
                <p v-if="form.errors.state" class="mt-1 text-xs text-red-600">{{ form.errors.state }}</p>
            </div>
            <div>
                <label for="postal_code" class="block text-sm font-medium text-slate-700">ZIP <span class="text-red-500">*</span></label>
                <input
                    id="postal_code"
                    v-model="form.postal_code"
                    type="text"
                    autocomplete="postal-code"
                    class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-slate-400 focus:outline-none"
                    :class="{ 'border-red-400': form.errors.postal_code }"
                />
                <p v-if="form.errors.postal_code" class="mt-1 text-xs text-red-600">{{ form.errors.postal_code }}</p>
            </div>
        </div>

        <!-- Notes -->
        <div>
            <label for="notes" class="block text-sm font-medium text-slate-700">Notes</label>
            <textarea
                id="notes"
                v-model="form.notes"
                rows="3"
                class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-slate-400 focus:outline-none"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';

interface CustomerFormData {
    first_name: string;
    last_name: string;
    email: string;
    phone: string;
    mobile: string;
    notes: string;
}

defineProps<{
    form: InertiaForm<CustomerFormData>;
}>();
</script>

<template>
    <div class="space-y-5">
        <!-- Name row -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="first_name" class="block text-sm font-medium text-slate-700">First Name <span class="text-red-500">*</span></label>
                <input
                    id="first_name"
                    v-model="form.first_name"
                    type="text"
                    class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-slate-400 focus:outline-none"
                    :class="{ 'border-red-400': form.errors.first_name }"
                    autocomplete="given-name"
                />
                <p v-if="form.errors.first_name" class="mt-1 text-xs text-red-600">{{ form.errors.first_name }}</p>
            </div>
            <div>
                <label for="last_name" class="block text-sm font-medium text-slate-700">Last Name <span class="text-red-500">*</span></label>
                <input
                    id="last_name"
                    v-model="form.last_name"
                    type="text"
                    class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-slate-400 focus:outline-none"
                    :class="{ 'border-red-400': form.errors.last_name }"
                    autocomplete="family-name"
                />
                <p v-if="form.errors.last_name" class="mt-1 text-xs text-red-600">{{ form.errors.last_name }}</p>
            </div>
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
            <input
                id="email"
                v-model="form.email"
                type="email"
                class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-slate-400 focus:outline-none"
                :class="{ 'border-red-400': form.errors.email }"
                autocomplete="email"
            />
            <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
        </div>

        <!-- Phone / Mobile row -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="phone" class="block text-sm font-medium text-slate-700">Phone</label>
                <input
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-slate-400 focus:outline-none"
                    :class="{ 'border-red-400': form.errors.phone }"
                    autocomplete="tel"
                />
                <p v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}</p>
            </div>
            <div>
                <label for="mobile" class="block text-sm font-medium text-slate-700">Mobile</label>
                <input
                    id="mobile"
                    v-model="form.mobile"
                    type="tel"
                    class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-slate-400 focus:outline-none"
                    :class="{ 'border-red-400': form.errors.mobile }"
                    autocomplete="tel"
                />
                <p v-if="form.errors.mobile" class="mt-1 text-xs text-red-600">{{ form.errors.mobile }}</p>
            </div>
        </div>

        <!-- Notes -->
        <div>
            <label for="notes" class="block text-sm font-medium text-slate-700">Notes</label>
            <textarea
                id="notes"
                v-model="form.notes"
                rows="4"
                class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-slate-400 focus:outline-none"
                :class="{ 'border-red-400': form.errors.notes }"
            />
            <p v-if="form.errors.notes" class="mt-1 text-xs text-red-600">{{ form.errors.notes }}</p>
        </div>
    </div>
</template>

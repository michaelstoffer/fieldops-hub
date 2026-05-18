<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';

interface JobTypeFormData {
    name: string;
    color: string;
    description: string;
    is_active: boolean;
}

defineProps<{ form: InertiaForm<JobTypeFormData> }>();

const PRESET_COLORS = [
    '#3b82f6', '#10b981', '#f59e0b', '#ef4444',
    '#8b5cf6', '#ec4899', '#14b8a6', '#f97316',
    '#6366f1', '#84cc16', '#0ea5e9', '#a78bfa',
];
</script>

<template>
    <div class="space-y-5">
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700">Name <span class="text-red-500">*</span></label>
            <input
                id="name"
                v-model="form.name"
                type="text"
                placeholder="e.g. HVAC Maintenance"
                class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none"
                :class="{ 'border-red-400': form.errors.name }"
            />
            <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Color <span class="text-red-500">*</span></label>
            <div class="mt-2 flex flex-wrap gap-2">
                <button
                    v-for="c in PRESET_COLORS"
                    :key="c"
                    type="button"
                    class="h-7 w-7 rounded-full ring-2 ring-offset-2 transition"
                    :style="{ backgroundColor: c }"
                    :class="form.color === c ? 'ring-slate-700' : 'ring-transparent hover:ring-slate-300'"
                    @click="form.color = c"
                />
                <input
                    v-model="form.color"
                    type="color"
                    class="h-7 w-7 cursor-pointer rounded-full border border-slate-200"
                    title="Custom color"
                />
            </div>
            <p v-if="form.errors.color" class="mt-1 text-xs text-red-600">{{ form.errors.color }}</p>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
            <textarea
                id="description"
                v-model="form.description"
                rows="3"
                class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none"
            />
        </div>

        <div class="flex items-center gap-3">
            <input
                id="is_active"
                v-model="form.is_active"
                type="checkbox"
                class="rounded border-slate-300"
            />
            <label for="is_active" class="text-sm text-slate-700">Active (appears on job forms)</label>
        </div>
    </div>
</template>

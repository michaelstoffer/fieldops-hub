<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

interface JobType {
    id: number;
    name: string;
    color: string;
    description: string | null;
    is_active: boolean;
}

defineProps<{ jobTypes: JobType[] }>();

function deactivate(id: number) {
    if (confirm('Deactivate this job type? It will no longer appear on new jobs.')) {
        router.delete(`/owner/job-types/${id}`);
    }
}

function activate(id: number) {
    router.patch(`/owner/job-types/${id}/activate`, {}, { preserveScroll: true });
}
</script>

<template>
    <OwnerLayout title="Job Types">
        <Head title="Job Types" />

        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-xl font-semibold text-slate-800">Job Types</h1>
            <Link
                href="/owner/job-types/create"
                class="inline-flex items-center rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700"
            >
                + Add Job Type
            </Link>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
            <div v-if="jobTypes.length === 0" class="px-6 py-16 text-center">
                <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L9.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>
                <p class="mt-3 text-sm font-semibold text-slate-700">No job types yet</p>
                <p class="mt-1 text-sm text-slate-400">Add a job type to categorize your work.</p>
                <Link href="/owner/job-types/create" class="mt-4 inline-flex items-center rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">+ Add Job Type</Link>
            </div>

            <ul v-else class="divide-y divide-slate-100">
                <li
                    v-for="jt in jobTypes"
                    :key="jt.id"
                    class="flex items-center gap-4 px-6 py-4"
                >
                    <span
                        class="h-3 w-3 flex-shrink-0 rounded-full"
                        :style="{ backgroundColor: jt.color }"
                    />
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-slate-800" :class="{ 'opacity-50': !jt.is_active }">
                            {{ jt.name }}
                            <span v-if="!jt.is_active" class="ml-2 text-xs font-normal text-slate-400">(inactive)</span>
                        </p>
                        <p v-if="jt.description" class="mt-0.5 truncate text-xs text-slate-500">{{ jt.description }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <Link
                            :href="`/owner/job-types/${jt.id}/edit`"
                            class="text-xs font-medium text-slate-600 hover:text-slate-800"
                        >
                            Edit
                        </Link>
                        <button
                            v-if="jt.is_active"
                            type="button"
                            class="text-xs font-medium text-red-400 hover:text-red-600"
                            @click="deactivate(jt.id)"
                        >
                            Deactivate
                        </button>
                        <button
                            v-else
                            type="button"
                            class="text-xs font-medium text-green-600 hover:text-green-800"
                            @click="activate(jt.id)"
                        >
                            Activate
                        </button>
                    </div>
                </li>
            </ul>
        </div>
    </OwnerLayout>
</template>

<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import JobTypeForm from './partials/JobTypeForm.vue';

const form = useForm({
    name:        '',
    color:       '#3b82f6',
    description: '',
    is_active:   true,
});

function submit() {
    form.post('/owner/job-types');
}
</script>

<template>
    <OwnerLayout title="Add Job Type">
        <Head title="Add Job Type" />

        <nav class="mb-4 text-sm text-muted-foreground">
            <Link href="/owner/job-types" class="hover:underline">Job Types</Link>
            <span class="mx-1">›</span>
            <span class="text-foreground">Add Job Type</span>
        </nav>

        <div class="max-w-xl">
            <div class="rounded-xl bg-card shadow ring-1 ring-border">
                <div class="border-b border-border px-6 py-4">
                    <h2 class="text-base font-semibold text-foreground">New Job Type</h2>
                </div>
                <form @submit.prevent="submit" class="px-6 py-5">
                    <JobTypeForm :form="form" />
                    <div class="mt-6 flex items-center gap-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center rounded-lg bg-slate-800 px-5 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                        >
                            Save Job Type
                        </button>
                        <Link href="/owner/job-types" class="text-sm text-muted-foreground hover:text-foreground">Cancel</Link>
                    </div>
                </form>
            </div>
        </div>
    </OwnerLayout>
</template>

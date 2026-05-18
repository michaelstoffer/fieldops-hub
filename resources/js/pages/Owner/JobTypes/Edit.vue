<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import JobTypeForm from './partials/JobTypeForm.vue';

const props = defineProps<{
    jobType: {
        id: number;
        name: string;
        color: string;
        description: string | null;
        is_active: boolean;
    };
}>();

const form = useForm({
    name:        props.jobType.name,
    color:       props.jobType.color,
    description: props.jobType.description ?? '',
    is_active:   props.jobType.is_active,
});

function submit() {
    form.patch(`/owner/job-types/${props.jobType.id}`);
}
</script>

<template>
    <OwnerLayout title="Edit Job Type">
        <Head title="Edit Job Type" />

        <nav class="mb-4 text-sm text-slate-500">
            <Link href="/owner/job-types" class="hover:underline">Job Types</Link>
            <span class="mx-1">›</span>
            <span class="text-slate-800">Edit</span>
        </nav>

        <div class="max-w-xl">
            <div class="rounded-xl bg-white shadow">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-slate-800">Edit Job Type</h2>
                </div>
                <form @submit.prevent="submit" class="px-6 py-5">
                    <JobTypeForm :form="form" />
                    <div class="mt-6 flex items-center gap-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center rounded-lg bg-slate-800 px-5 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                        >
                            Update Job Type
                        </button>
                        <Link href="/owner/job-types" class="text-sm text-slate-500 hover:text-slate-700">Cancel</Link>
                    </div>
                </form>
            </div>
        </div>
    </OwnerLayout>
</template>

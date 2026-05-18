<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({ file: null as File | null });
const preview = ref<string[][]>([]);
const previewHeaders = ref<string[]>([]);
const parseError = ref('');

const EXPECTED = ['first_name', 'last_name', 'email', 'phone', 'mobile', 'notes'];

function onFile(e: Event) {
    const input = e.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;
    form.file = file;
    parseError.value = '';
    preview.value = [];
    previewHeaders.value = [];

    const reader = new FileReader();
    reader.onload = (ev) => {
        const text = ev.target?.result as string;
        const rows = text.trim().split(/\r?\n/).slice(0, 6).map((r) => r.split(',').map((c) => c.trim().replace(/^"|"$/g, '')));
        if (rows.length === 0) return;
        previewHeaders.value = rows[0].map((h) => h.toLowerCase());
        preview.value = rows.slice(1);
    };
    reader.readAsText(file);
}

function submit() {
    if (!form.file) return;
    form.post('/owner/customers/import', {
        forceFormData: true,
    });
}
</script>

<template>
    <OwnerLayout title="Import Customers">
        <Head title="Import Customers" />

        <nav class="mb-4 text-sm text-slate-500">
            <Link href="/owner/customers" class="hover:underline">Customers</Link>
            <span class="mx-1">›</span>
            <span class="text-slate-800">Import</span>
        </nav>

        <div class="max-w-3xl space-y-6">
            <!-- Instructions -->
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h2 class="mb-3 text-base font-semibold text-slate-800">Import Customers from CSV</h2>
                <p class="mb-4 text-sm text-slate-600">
                    Upload a <code class="rounded bg-slate-100 px-1 py-0.5 text-xs">.csv</code> file with the following columns.
                    The first row must be a header row. Columns can be in any order.
                </p>
                <div class="overflow-hidden rounded-lg border border-slate-200">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Column</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Required</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="col in [
                                { name: 'first_name', req: true,  note: 'Customer first name' },
                                { name: 'last_name',  req: true,  note: 'Customer last name' },
                                { name: 'email',      req: false, note: 'Email address' },
                                { name: 'phone',      req: false, note: 'Office/main phone' },
                                { name: 'mobile',     req: false, note: 'Mobile phone' },
                                { name: 'notes',      req: false, note: 'Internal notes' },
                            ]" :key="col.name">
                                <td class="px-4 py-2 font-mono text-xs text-slate-700">{{ col.name }}</td>
                                <td class="px-4 py-2 text-xs">
                                    <span v-if="col.req" class="font-medium text-red-500">Required</span>
                                    <span v-else class="text-slate-400">Optional</span>
                                </td>
                                <td class="px-4 py-2 text-xs text-slate-500">{{ col.note }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p class="mt-3 text-xs text-slate-400">
                    Rows missing both <code class="rounded bg-slate-100 px-1 py-0.5">first_name</code> and
                    <code class="rounded bg-slate-100 px-1 py-0.5">last_name</code> will be skipped.
                    Maximum file size: 2 MB.
                </p>
            </div>

            <!-- Upload form -->
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">CSV File <span class="text-red-500">*</span></label>
                        <input
                            type="file"
                            accept=".csv,text/csv"
                            class="mt-1 block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-800 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-slate-700"
                            @change="onFile"
                        />
                        <p v-if="form.errors.file" class="mt-1 text-xs text-red-600">{{ form.errors.file }}</p>
                        <p v-if="parseError" class="mt-1 text-xs text-red-600">{{ parseError }}</p>
                    </div>

                    <!-- Preview table -->
                    <div v-if="preview.length > 0" class="overflow-x-auto rounded-lg border border-slate-200">
                        <p class="border-b border-slate-100 bg-slate-50 px-4 py-2 text-xs font-medium text-slate-500">
                            Preview (first {{ preview.length }} rows)
                        </p>
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th
                                        v-for="h in previewHeaders"
                                        :key="h"
                                        class="px-3 py-2 text-left font-semibold text-slate-600"
                                        :class="{ 'text-red-500': h !== '' && !EXPECTED.includes(h) }"
                                    >
                                        {{ h }}
                                        <span v-if="h !== '' && !EXPECTED.includes(h)" class="ml-1 font-normal text-red-400">(unknown)</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr v-for="(row, ri) in preview" :key="ri" class="hover:bg-slate-50">
                                    <td v-for="(cell, ci) in row" :key="ci" class="px-3 py-1.5 text-slate-700">{{ cell }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center gap-3">
                        <button
                            type="submit"
                            :disabled="!form.file || form.processing"
                            class="inline-flex items-center rounded-lg bg-slate-800 px-5 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Importing…' : 'Import Customers' }}
                        </button>
                        <Link href="/owner/customers" class="text-sm text-slate-500 hover:text-slate-700">Cancel</Link>
                    </div>
                </form>
            </div>
        </div>
    </OwnerLayout>
</template>

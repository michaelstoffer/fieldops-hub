<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { update as companyUpdate } from '@/routes/owner/settings/company/index.ts';
import { destroy as logoDestroy } from '@/routes/owner/settings/company/logo/index.ts';
import { ref, watch } from 'vue';

interface CompanySettings {
    company_name: string | null;
    company_email: string | null;
    company_phone: string | null;
    company_address: string | null;
    company_city: string | null;
    company_state: string | null;
    company_zip: string | null;
    company_website: string | null;
    logo_path: string | null;
    default_tax_rate: string | null;
}

const props = defineProps<{ settings: CompanySettings }>();

const form = useForm({
    company_name:     props.settings.company_name ?? '',
    company_email:    props.settings.company_email ?? '',
    company_phone:    props.settings.company_phone ?? '',
    company_address:  props.settings.company_address ?? '',
    company_city:     props.settings.company_city ?? '',
    company_state:    props.settings.company_state ?? '',
    company_zip:      props.settings.company_zip ?? '',
    company_website:  props.settings.company_website ?? '',
    default_tax_rate: props.settings.default_tax_rate
        ? String(parseFloat(String(props.settings.default_tax_rate)) * 100)
        : '',
    logo:             null as File | null,
});

const logoFile   = ref<File | null>(null);
const logoPreview = ref<string | null>(props.settings.logo_path);
const page       = usePage();

watch(() => props.settings, (newSettings) => {
    form.company_name = newSettings.company_name ?? '';
    form.company_email = newSettings.company_email ?? '';
    form.company_phone = newSettings.company_phone ?? '';
    form.company_address = newSettings.company_address ?? '';
    form.company_city = newSettings.company_city ?? '';
    form.company_state = newSettings.company_state ?? '';
    form.company_zip = newSettings.company_zip ?? '';
    form.company_website = newSettings.company_website ?? '';
    form.default_tax_rate = newSettings.default_tax_rate
        ? String(parseFloat(String(newSettings.default_tax_rate)) * 100)
        : '';
    logoPreview.value = newSettings.logo_path;
    logoFile.value = null;
    form.logo = null;
});

function onLogoChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0] ?? null;
    logoFile.value = file;
    form.logo = file;
    if (file) {
        logoPreview.value = URL.createObjectURL(file);
    }
}

function removeLogo() {
    if (confirm('Remove the company logo?')) {
        router.delete(logoDestroy().url, {
            onSuccess: () => { logoPreview.value = null; },
        });
    }
}

function submit() {
    form.post(companyUpdate().url, {
        forceFormData: true,
        onSuccess: () => {
            logoFile.value = null;
            form.logo = null;
        },
    });
}
</script>

<template>
    <OwnerLayout title="Company Settings">
        <Head title="Company Settings" />

        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-800">Company Settings</h2>
        </div>

        <div v-if="(page.props as any).flash?.success"
            class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
            {{ (page.props as any).flash.success }}
        </div>

        <form @submit.prevent="submit" enctype="multipart/form-data">
            <div class="space-y-6">

                <!-- Logo -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4">Branding</h3>
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <img v-if="logoPreview" :src="logoPreview"
                                alt="Company logo" class="w-20 h-20 object-contain rounded border border-slate-200" />
                            <div v-else class="w-20 h-20 rounded border border-slate-200 bg-slate-50 flex items-center justify-center text-slate-400 text-xs">
                                No logo
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Upload Logo</label>
                                <input type="file" accept="image/*" @change="onLogoChange"
                                    class="text-sm text-slate-600" />
                                <p class="text-xs text-slate-400 mt-1">PNG, JPG, SVG up to 2MB</p>
                            </div>
                            <button
                                v-if="logoPreview && !logoFile"
                                type="button"
                                class="text-xs text-red-500 hover:text-red-700"
                                @click="removeLogo"
                            >
                                Remove logo
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Company info -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4">Company Info</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Company Name</label>
                            <input v-model="form.company_name" type="text"
                                class="w-full border border-slate-300 rounded px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Email</label>
                            <input v-model="form.company_email" type="email"
                                class="w-full border border-slate-300 rounded px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Phone</label>
                            <input v-model="form.company_phone" type="text"
                                class="w-full border border-slate-300 rounded px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Website</label>
                            <input v-model="form.company_website" type="url"
                                class="w-full border border-slate-300 rounded px-3 py-2 text-sm" placeholder="https://" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs text-slate-500 mb-1">Address</label>
                            <input v-model="form.company_address" type="text"
                                class="w-full border border-slate-300 rounded px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">City</label>
                            <input v-model="form.company_city" type="text"
                                class="w-full border border-slate-300 rounded px-3 py-2 text-sm" />
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">State</label>
                                <input v-model="form.company_state" type="text" maxlength="10"
                                    class="w-full border border-slate-300 rounded px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">ZIP</label>
                                <input v-model="form.company_zip" type="text" maxlength="20"
                                    class="w-full border border-slate-300 rounded px-3 py-2 text-sm" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tax -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4">Billing Defaults</h3>
                    <div class="max-w-xs">
                        <label class="block text-xs text-slate-500 mb-1">Default Tax Rate (%)</label>
                        <input v-model="form.default_tax_rate" type="number" step="0.01" min="0" max="100"
                            class="w-full border border-slate-300 rounded px-3 py-2 text-sm"
                            placeholder="e.g. 8.25" />
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" :disabled="form.processing"
                        class="px-5 py-2 bg-slate-800 text-white text-sm rounded hover:bg-slate-700 disabled:opacity-60">
                        {{ form.processing ? 'Saving…' : 'Save Settings' }}
                    </button>
                </div>
            </div>
        </form>
    </OwnerLayout>
</template>

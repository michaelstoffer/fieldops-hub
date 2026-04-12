<script setup lang="ts">
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

interface IntegrationSettings {
    stripe_secret_key: string | null;
    stripe_publishable_key: string | null;
    stripe_webhook_secret: string | null;
    twilio_account_sid: string | null;
    twilio_auth_token: string | null;
    twilio_from_number: string | null;
    sendgrid_api_key: string | null;
    sendgrid_from_email: string | null;
    google_maps_api_key: string | null;
}

const props = defineProps<{ settings: IntegrationSettings }>();

const form = ref({ ...props.settings });
const saving = ref(false);
const page   = usePage();

function submit() {
    saving.value = true;
    router.post('/owner/settings/integrations', form.value, {
        onFinish: () => { saving.value = false; },
    });
}
</script>

<template>
    <OwnerLayout title="Integrations">
        <Head title="Integrations" />

        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-800">Integration Settings</h2>
        </div>

        <div v-if="(page.props as any).flash?.success"
            class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
            {{ (page.props as any).flash.success }}
        </div>

        <p class="mb-6 text-sm text-slate-500">
            API keys are encrypted at rest. Existing keys are shown masked — leave a field unchanged to keep the current value.
        </p>

        <form @submit.prevent="submit">
            <div class="space-y-6">

                <!-- Stripe -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4">Stripe</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Secret Key</label>
                            <input v-model="form.stripe_secret_key" type="text"
                                class="w-full border border-slate-300 rounded px-3 py-2 text-sm font-mono"
                                placeholder="sk_live_…" />
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Publishable Key</label>
                            <input v-model="form.stripe_publishable_key" type="text"
                                class="w-full border border-slate-300 rounded px-3 py-2 text-sm font-mono"
                                placeholder="pk_live_…" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs text-slate-500 mb-1">Webhook Secret</label>
                            <input v-model="form.stripe_webhook_secret" type="text"
                                class="w-full border border-slate-300 rounded px-3 py-2 text-sm font-mono"
                                placeholder="whsec_…" />
                        </div>
                    </div>
                </div>

                <!-- Twilio -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4">Twilio (SMS)</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Account SID</label>
                            <input v-model="form.twilio_account_sid" type="text"
                                class="w-full border border-slate-300 rounded px-3 py-2 text-sm font-mono"
                                placeholder="AC…" />
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Auth Token</label>
                            <input v-model="form.twilio_auth_token" type="text"
                                class="w-full border border-slate-300 rounded px-3 py-2 text-sm font-mono" />
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">From Number</label>
                            <input v-model="form.twilio_from_number" type="text"
                                class="w-full border border-slate-300 rounded px-3 py-2 text-sm"
                                placeholder="+1…" />
                        </div>
                    </div>
                </div>

                <!-- SendGrid -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4">SendGrid (Email)</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">API Key</label>
                            <input v-model="form.sendgrid_api_key" type="text"
                                class="w-full border border-slate-300 rounded px-3 py-2 text-sm font-mono"
                                placeholder="SG.…" />
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">From Email</label>
                            <input v-model="form.sendgrid_from_email" type="email"
                                class="w-full border border-slate-300 rounded px-3 py-2 text-sm"
                                placeholder="noreply@example.com" />
                        </div>
                    </div>
                </div>

                <!-- Google Maps -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-sm font-semibold text-slate-700 mb-4">Google Maps</h3>
                    <div class="max-w-lg">
                        <label class="block text-xs text-slate-500 mb-1">API Key</label>
                        <input v-model="form.google_maps_api_key" type="text"
                            class="w-full border border-slate-300 rounded px-3 py-2 text-sm font-mono" />
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" :disabled="saving"
                        class="px-5 py-2 bg-slate-800 text-white text-sm rounded hover:bg-slate-700 disabled:opacity-60">
                        {{ saving ? 'Saving…' : 'Save Integrations' }}
                    </button>
                </div>
            </div>
        </form>
    </OwnerLayout>
</template>

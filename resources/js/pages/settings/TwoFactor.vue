<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import TwoFactorRecoveryCodes from '@/components/TwoFactorRecoveryCodes.vue';
import TwoFactorSetupModal from '@/components/TwoFactorSetupModal.vue';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ShieldBan, ShieldCheck } from 'lucide-vue-next';
import { onUnmounted, ref } from 'vue';

interface Props {
    requiresConfirmation?: boolean;
    twoFactorEnabled?: boolean;
}

withDefaults(defineProps<Props>(), {
    requiresConfirmation: false,
    twoFactorEnabled: false,
});

const { hasSetupData, clearTwoFactorAuthData } = useTwoFactorAuth();
const showSetupModal = ref<boolean>(false);
const enabling = ref(false);
const disabling = ref(false);

function enableTwoFactor() {
    enabling.value = true;
    router.post('/user/two-factor-authentication', {}, {
        preserveScroll: true,
        onSuccess: () => { showSetupModal.value = true; },
        onFinish: () => { enabling.value = false; },
    });
}

function disableTwoFactor() {
    disabling.value = true;
    router.delete('/user/two-factor-authentication', {
        preserveScroll: true,
        onFinish: () => { disabling.value = false; },
    });
}

onUnmounted(() => {
    clearTwoFactorAuthData();
});
</script>

<template>
    <OwnerLayout title="Settings">
        <Head title="Two-Factor Authentication" />
        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    title="Two-Factor Authentication"
                    description="Manage your two-factor authentication settings"
                />

                <div v-if="!twoFactorEnabled" class="flex flex-col items-start space-y-4">
                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700">Disabled</span>

                    <p class="text-sm text-slate-600">
                        When you enable two-factor authentication, you will be
                        prompted for a secure pin during login. This pin can be
                        retrieved from a TOTP-supported application on your phone.
                    </p>

                    <button v-if="hasSetupData" @click="showSetupModal = true"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
                        <ShieldCheck class="w-4 h-4" />Continue Setup
                    </button>
                    <button v-else @click="enableTwoFactor" :disabled="enabling"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50 transition-colors">
                        <ShieldCheck class="w-4 h-4" />{{ enabling ? 'Enabling…' : 'Enable 2FA' }}
                    </button>
                </div>

                <div v-else class="flex flex-col items-start space-y-4">
                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700">Enabled</span>

                    <p class="text-sm text-slate-600">
                        With two-factor authentication enabled, you will be
                        prompted for a secure, random pin during login, which
                        you can retrieve from the TOTP-supported application on
                        your phone.
                    </p>

                    <TwoFactorRecoveryCodes />

                    <button @click="disableTwoFactor" :disabled="disabling"
                        class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50 transition-colors">
                        <ShieldBan class="w-4 h-4" />{{ disabling ? 'Disabling…' : 'Disable 2FA' }}
                    </button>
                </div>

                <TwoFactorSetupModal
                    v-model:isOpen="showSetupModal"
                    :requiresConfirmation="requiresConfirmation"
                    :twoFactorEnabled="twoFactorEnabled"
                />
            </div>
        </SettingsLayout>
    </OwnerLayout>
</template>

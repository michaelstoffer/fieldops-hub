<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import TwoFactorRecoveryCodes from '@/components/TwoFactorRecoveryCodes.vue';
import TwoFactorSetupModal from '@/components/TwoFactorSetupModal.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
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
                    <Badge variant="destructive">Disabled</Badge>

                    <p class="text-sm text-slate-600">
                        When you enable two-factor authentication, you will be
                        prompted for a secure pin during login. This pin can be
                        retrieved from a TOTP-supported application on your phone.
                    </p>

                    <Button v-if="hasSetupData" @click="showSetupModal = true">
                        <ShieldCheck />Continue Setup
                    </Button>
                    <Button v-else @click="enableTwoFactor" :disabled="enabling">
                        <ShieldCheck />{{ enabling ? 'Enabling…' : 'Enable 2FA' }}
                    </Button>
                </div>

                <div v-else class="flex flex-col items-start space-y-4">
                    <Badge variant="default">Enabled</Badge>

                    <p class="text-sm text-slate-600">
                        With two-factor authentication enabled, you will be
                        prompted for a secure, random pin during login, which
                        you can retrieve from the TOTP-supported application on
                        your phone.
                    </p>

                    <TwoFactorRecoveryCodes />

                    <Button variant="destructive" @click="disableTwoFactor" :disabled="disabling">
                        <ShieldBan />{{ disabling ? 'Disabling…' : 'Disable 2FA' }}
                    </Button>
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

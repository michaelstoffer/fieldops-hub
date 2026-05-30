<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
</script>

<template>
    <OwnerLayout title="Settings">
        <Head title="Appearance settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    title="Appearance settings"
                    description="Update your account's appearance settings"
                />
                <AppearanceTabs />
            </div>
        </SettingsLayout>
    </OwnerLayout>
</template>

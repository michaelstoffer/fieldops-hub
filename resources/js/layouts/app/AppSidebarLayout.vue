<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const flash = computed(() => (usePage().props as any).flash as { success?: string; warning?: string; error?: string } | undefined);
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-x-hidden">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <div v-if="flash?.warning" class="mx-4 mt-4 rounded-md border border-yellow-300 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                {{ flash.warning }}
            </div>
            <div v-if="flash?.success" class="mx-4 mt-4 rounded-md border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ flash.success }}
            </div>
            <div v-if="flash?.error" class="mx-4 mt-4 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ flash.error }}
            </div>
            <slot />
        </AppContent>
    </AppShell>
</template>

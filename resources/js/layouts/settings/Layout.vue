<script setup lang="ts">
import Heading from '@/components/Heading.vue';
</script>

<template>
    <div class="px-4 py-6">
        <Heading
            title="Settings"
            description="Manage your profile and account settings"
        />
        <div class="max-w-2xl space-y-12">
            <slot />
        </div>
    </div>
</template>

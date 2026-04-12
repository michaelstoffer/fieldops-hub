<script setup lang="ts">
import EstimateForm from './EstimateForm.vue';

defineProps<{
    customers: object[];
    jobs: object[];
    catalogItems: object[];
    tiers: string[];
    statuses: Record<string, string>;
}>();
</script>

<template>
    <EstimateForm
        :customers="customers"
        :jobs="jobs"
        :catalog-items="catalogItems"
        :tiers="tiers"
        :statuses="statuses"
    />
</template>

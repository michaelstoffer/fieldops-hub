<script setup lang="ts">
import EstimateForm from './EstimateForm.vue';

defineProps<{
    estimate: object;
    customers: object[];
    jobs: object[];
    catalogItems: object[];
    tiers: string[];
    statuses: Record<string, string>;
}>();
</script>

<template>
    <EstimateForm
        :estimate="estimate as any"
        :customers="customers"
        :jobs="jobs"
        :catalog-items="catalogItems"
        :tiers="tiers"
        :statuses="statuses"
    />
</template>

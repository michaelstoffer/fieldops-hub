<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-show="message" role="alert" aria-live="polite">
        <p class="text-sm text-red-600">
            {{ message }}
        </p>
    </div>
</template>

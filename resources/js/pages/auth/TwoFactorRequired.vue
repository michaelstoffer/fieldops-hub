<script setup lang="ts">
import { show } from '@/routes/two-factor/index.ts';
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ShieldAlert } from 'lucide-vue-next';
</script>

<template>
    <OwnerLayout title="Two-Factor Authentication Required">
        <Head title="Two-Factor Authentication Required" />

        <div class="flex min-h-[60vh] flex-col items-center justify-center text-center">
            <ShieldAlert class="mb-4 h-12 w-12 text-yellow-500" />
            <h1 class="mb-2 text-2xl font-semibold text-foreground">Two-Factor Authentication Required</h1>
            <p class="mb-6 max-w-md text-muted-foreground">
                Your account role requires two-factor authentication before you can access this area.
                Set it up now to continue.
            </p>
            <Link
                :href="show.url()"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition-colors"
            >
                Set Up Two-Factor Authentication
            </Link>
        </div>
    </OwnerLayout>
</template>

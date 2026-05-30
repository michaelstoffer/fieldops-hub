<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { show } from '@/routes/two-factor/index.ts';
import { Head, Link } from '@inertiajs/vue3';
import { ShieldAlert } from 'lucide-vue-next';
</script>

<template>
    <AppLayout>
        <Head title="Two-Factor Authentication Required" />
        <div class="flex min-h-[60vh] flex-col items-center justify-center text-center">
            <ShieldAlert class="mb-4 h-12 w-12 text-yellow-500" />
            <h1 class="mb-2 text-2xl font-semibold text-gray-900">Two-Factor Authentication Required</h1>
            <p class="mb-6 max-w-md text-gray-600">
                Your account role requires two-factor authentication to be enabled before you can access this area.
                Set it up now to continue.
            </p>
            <Button as-child>
                <Link :href="show.url()">Set Up Two-Factor Authentication</Link>
            </Button>
        </div>
    </AppLayout>
</template>

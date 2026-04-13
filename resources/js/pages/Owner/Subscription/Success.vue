<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Subscribed — FieldOps Hub" />

    <div class="min-h-screen bg-slate-900 flex flex-col items-center justify-center px-4 text-center">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_#0f766e25_0%,_transparent_60%)]"></div>

        <div class="relative z-10 max-w-md">
            <div class="h-16 w-16 rounded-full bg-teal-500/20 border border-teal-500/30 flex items-center justify-center mx-auto mb-6">
                <svg class="h-8 w-8 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-white mb-3">You're subscribed!</h1>
            <p class="text-slate-400 text-lg mb-8">
                Your account is fully active. Welcome aboard — let's get back to work.
            </p>

            <Link :href="route('owner.dashboard')"
                  class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-500 px-8 py-3 text-sm font-semibold text-white transition-colors">
                Go to dashboard
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </Link>
        </div>
    </div>
</template>

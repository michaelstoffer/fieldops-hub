<script setup lang="ts">
import { logout } from '@/routes';
import { send } from '@/routes/verification';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{ status?: string }>();

const form = useForm({});
const submit = () => form.post(send.url());
const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <Head title="Verify Email — FieldOps Hub" />

    <div class="min-h-screen flex">

        <!-- Left branding panel -->
        <div class="hidden lg:flex lg:w-1/2 relative flex-col justify-between p-12 bg-slate-900 overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_#1e40af55_0%,_transparent_60%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_right,_#0f766e44_0%,_transparent_60%)]"></div>
            <div class="absolute inset-0 opacity-[0.04]"
                 style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 40px 40px;"></div>

            <Link href="/" class="relative z-10 flex items-center gap-3 group">
                <div class="h-9 w-9 rounded-xl bg-blue-500 flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:bg-blue-400 transition-colors">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="text-white font-semibold text-lg tracking-tight group-hover:text-blue-200 transition-colors">FieldOps Hub</span>
            </Link>

            <div class="relative z-10 space-y-4">
                <h1 class="text-4xl font-bold text-white leading-tight">
                    One quick<br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-teal-400">step.</span>
                </h1>
                <p class="text-slate-400 text-lg leading-relaxed max-w-sm">
                    Verify your email address to activate your account and get started.
                </p>
            </div>

            <p class="relative z-10 text-slate-500 text-sm">&copy; {{ new Date().getFullYear() }} FieldOps Hub</p>
        </div>

        <!-- Right form panel -->
        <div class="flex-1 flex flex-col items-center justify-center px-6 py-12 bg-slate-50">

            <Link href="/" class="lg:hidden flex items-center gap-2 mb-10 group">
                <div class="h-8 w-8 rounded-lg bg-blue-600 flex items-center justify-center group-hover:bg-blue-500 transition-colors">
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="font-semibold text-slate-800 text-base tracking-tight">FieldOps Hub</span>
            </Link>

            <div class="w-full max-w-sm">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-slate-900">Check your inbox</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Thanks for signing up. Click the verification link we sent to your email to activate your account.
                    </p>
                </div>

                <div v-if="verificationLinkSent" class="mb-5 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                    A new verification link has been sent to your email address.
                </div>

                <form @submit.prevent="submit">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <svg v-if="form.processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ form.processing ? 'Sending…' : 'Resend verification email' }}
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-slate-500">
                    Wrong account?
                    <Link :href="logout()" method="post" as="button" class="text-blue-600 hover:text-blue-700 font-medium">Sign out</Link>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { login } from '@/routes';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(login.url(), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Sign in — FieldOps Hub" />

    <div class="min-h-screen flex">

        <!-- ── Left panel: branding ─────────────────────────────────────────── -->
        <div class="hidden lg:flex lg:w-1/2 relative flex-col justify-between p-12 bg-slate-900 overflow-hidden">

            <!-- Background radial glow -->
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_#1e40af55_0%,_transparent_60%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_right,_#0f766e44_0%,_transparent_60%)]"></div>

            <!-- Grid overlay -->
            <div class="absolute inset-0 opacity-[0.04]"
                 style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 40px 40px;">
            </div>

            <!-- Logo -->
            <div class="relative z-10 flex items-center gap-3">
                <div class="h-9 w-9 rounded-xl bg-blue-500 flex items-center justify-center shadow-lg shadow-blue-500/30">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="text-white font-semibold text-lg tracking-tight">FieldOps Hub</span>
            </div>

            <!-- Hero text -->
            <div class="relative z-10 space-y-6">
                <h1 class="text-4xl xl:text-5xl font-bold text-white leading-tight">
                    Field service<br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-teal-400">
                        under control.
                    </span>
                </h1>
                <p class="text-slate-400 text-lg leading-relaxed max-w-sm">
                    Dispatch technicians, track jobs, invoice customers — all in one place.
                </p>

                <!-- Feature pills -->
                <div class="flex flex-wrap gap-2 pt-2">
                    <span v-for="feat in ['Live dispatch', 'Job tracking', 'Invoicing', 'Estimates', 'Reporting']"
                          :key="feat"
                          class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-slate-300 text-sm">
                        <span class="h-1.5 w-1.5 rounded-full bg-teal-400"></span>
                        {{ feat }}
                    </span>
                </div>
            </div>

            <!-- Footer quote -->
            <div class="relative z-10">
                <p class="text-slate-500 text-sm">&copy; {{ new Date().getFullYear() }} FieldOps Hub</p>
            </div>
        </div>

        <!-- ── Right panel: form ─────────────────────────────────────────────── -->
        <div class="flex-1 flex flex-col items-center justify-center px-6 py-12 bg-slate-50">

            <!-- Mobile logo -->
            <div class="lg:hidden flex items-center gap-2 mb-10">
                <div class="h-8 w-8 rounded-lg bg-blue-600 flex items-center justify-center">
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="font-semibold text-slate-800 text-base tracking-tight">FieldOps Hub</span>
            </div>

            <div class="w-full max-w-sm">

                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-slate-900">Welcome back</h2>
                    <p class="mt-1 text-sm text-slate-500">Sign in to your account to continue.</p>
                </div>

                <!-- Status message (e.g. password reset success) -->
                <div v-if="status" class="mb-5 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Email address
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            autocomplete="username"
                            required
                            autofocus
                            placeholder="you@company.com"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm
                                   focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20
                                   transition"
                            :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400/20': form.errors.email }"
                        />
                        <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                            <Link
                                v-if="canResetPassword"
                                href="/forgot-password"
                                class="text-xs text-blue-600 hover:text-blue-700 font-medium"
                            >
                                Forgot password?
                            </Link>
                        </div>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            autocomplete="current-password"
                            required
                            placeholder="••••••••"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm
                                   focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20
                                   transition"
                            :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400/20': form.errors.password }"
                        />
                        <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-600">{{ form.errors.password }}</p>
                    </div>

                    <!-- Remember me -->
                    <div class="flex items-center gap-2">
                        <input
                            id="remember"
                            v-model="form.remember"
                            type="checkbox"
                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/30 cursor-pointer"
                        />
                        <label for="remember" class="text-sm text-slate-600 cursor-pointer select-none">
                            Keep me signed in
                        </label>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                               hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                               disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <svg v-if="form.processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ form.processing ? 'Signing in…' : 'Sign in' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

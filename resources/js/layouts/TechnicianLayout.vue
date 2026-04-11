<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    title?: string;
}>();

const page = usePage();
const user = (page.props.auth as { user: { name: string } }).user;
</script>

<template>
    <div class="flex min-h-screen flex-col bg-slate-50">
        <!-- Top bar -->
        <header class="sticky top-0 z-10 flex h-14 items-center justify-between border-b border-slate-200 bg-white px-4 shadow-sm">
            <span class="text-base font-semibold text-slate-800">{{ title ?? 'FieldOps Hub' }}</span>
            <div class="flex items-center gap-3 text-sm text-slate-500">
                <span class="hidden sm:inline">{{ user?.name }}</span>
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="rounded-md px-2 py-1 text-xs font-medium text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                >
                    Sign out
                </Link>
            </div>
        </header>

        <!-- Page content -->
        <main class="flex-1 pb-20">
            <slot />
        </main>

        <!-- Bottom navigation -->
        <nav class="fixed bottom-0 left-0 right-0 z-10 flex border-t border-slate-200 bg-white">
            <Link
                href="/technician/dashboard"
                class="flex flex-1 flex-col items-center gap-1 py-3 text-xs font-medium text-slate-500 hover:text-slate-900"
            >
                <!-- home icon -->
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z" />
                    <path d="M9 21V12h6v9" />
                </svg>
                Dashboard
            </Link>
            <Link
                href="/technician/jobs"
                class="flex flex-1 flex-col items-center gap-1 py-3 text-xs font-medium text-slate-500 hover:text-slate-900"
            >
                <!-- briefcase icon -->
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="7" width="20" height="14" rx="2" />
                    <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" />
                </svg>
                Jobs
            </Link>
        </nav>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        default: 'FieldOps Hub',
    },
});
</script>

<template>
    <div class="min-h-screen bg-slate-100 flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-slate-100 flex flex-col">
            <div class="px-6 py-4 text-xl font-semibold border-b border-slate-800">
                FieldOps Hub
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
                <Link href="/owner/dashboard" class="block px-3 py-2 rounded-md hover:bg-slate-800">
                    Dashboard
                </Link>
                <Link href="/owner/customers" class="block px-3 py-2 rounded-md hover:bg-slate-800">
                    Customers
                </Link>
                <Link href="/owner/jobs" class="block px-3 py-2 rounded-md hover:bg-slate-800">
                    Jobs / Work Orders
                </Link>
                <Link href="/owner/schedule" class="block px-3 py-2 rounded-md hover:bg-slate-800">
                    Schedule
                </Link>
            </nav>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col">
            <!-- Top bar -->
            <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6">
                <h1 class="text-lg font-semibold text-slate-800">
                    {{ title }}
                </h1>
                <div class="text-sm text-slate-600 flex items-center gap-3">
                    <slot name="header-actions" />
                </div>
            </header>

            <!-- Page body -->
            <main class="flex-1 p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

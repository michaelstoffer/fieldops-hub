<script setup lang="ts">
import { useLocationSharing } from '@/composables/useLocationSharing';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    title?: string;
}>();

const page = usePage();
const user = (page.props.auth as { user: { name: string } }).user;

const { enabled: locationEnabled, permissionDenied, toggle: toggleLocation } = useLocationSharing();
</script>

<template>
    <div class="flex min-h-screen flex-col bg-background">
        <!-- Top bar — pt accounts for iOS status bar notch -->
        <header class="sticky top-0 z-10 flex h-14 items-center justify-between border-b border-border bg-card px-4 shadow-sm pt-[env(safe-area-inset-top,0px)]" style="height: calc(3.5rem + env(safe-area-inset-top, 0px))">
            <span class="text-base font-semibold text-foreground">{{ title ?? 'FieldOps Hub' }}</span>
            <div class="flex items-center gap-3 text-sm text-muted-foreground">
                <!-- Location sharing toggle -->
                <button
                    type="button"
                    class="flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium transition"
                    :class="locationEnabled
                        ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400'
                        : 'bg-muted text-muted-foreground'"
                    :title="permissionDenied ? 'Location permission denied' : (locationEnabled ? 'Sharing location' : 'Share location')"
                    @click="toggleLocation"
                >
                    <!-- location pin icon -->
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-2.083 3.952-5.125 3.952-8.577 0-4.85-3.922-8.773-8.75-8.773S3.75 7.65 3.75 12.5c0 3.452 2.008 6.494 3.952 8.577a19.58 19.58 0 002.683 2.282 16.975 16.975 0 001.144.742zM12.5 15a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" clip-rule="evenodd" />
                    </svg>
                    <span v-if="locationEnabled">On</span>
                    <span v-else>Off</span>
                </button>

                <span class="hidden sm:inline">{{ user?.name }}</span>
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="rounded-md px-2 py-1 text-xs font-medium text-muted-foreground hover:bg-accent hover:text-foreground transition-colors"
                >
                    Sign out
                </Link>
            </div>
        </header>

        <!-- Page content — pb accounts for bottom nav height + iOS home bar -->
        <main class="flex-1 pb-[calc(5rem+env(safe-area-inset-bottom,0px))]">
            <slot />
        </main>

        <!-- Bottom navigation — extends into iOS home bar safe area -->
        <nav class="fixed bottom-0 left-0 right-0 z-10 flex border-t border-border bg-card pb-[env(safe-area-inset-bottom,0px)]">
            <Link
                href="/technician/dashboard"
                class="flex min-h-[48px] flex-1 flex-col items-center justify-center gap-1 py-2 text-xs font-medium text-muted-foreground hover:text-foreground active:text-foreground transition-colors"
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
                class="flex min-h-[48px] flex-1 flex-col items-center justify-center gap-1 py-2 text-xs font-medium text-muted-foreground hover:text-foreground active:text-foreground transition-colors"
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

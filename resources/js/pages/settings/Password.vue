<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submit = () => form.put('/settings/password', {
    preserveScroll: true,
    onSuccess: () => form.reset(),
    onError: () => {
        if (form.errors.password) form.reset('password', 'password_confirmation');
        if (form.errors.current_password) form.reset('current_password');
    },
});
</script>

<template>
    <OwnerLayout title="Settings">
        <Head title="Password settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <div>
                    <h3 class="text-base font-medium text-foreground">Update password</h3>
                    <p class="text-sm text-muted-foreground">Ensure your account uses a long, random password.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-5 max-w-md">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-foreground mb-1.5">Current password</label>
                        <input
                            id="current_password"
                            v-model="form.current_password"
                            type="password"
                            autocomplete="current-password"
                            required
                            placeholder="••••••••"
                            class="w-full rounded-lg border border-input bg-background px-3.5 py-2.5 text-sm text-foreground shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
                            :class="{ 'border-red-400': form.errors.current_password }"
                        />
                        <InputError :message="form.errors.current_password" class="mt-1.5" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-foreground mb-1.5">New password</label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            autocomplete="new-password"
                            required
                            placeholder="••••••••"
                            class="w-full rounded-lg border border-input bg-background px-3.5 py-2.5 text-sm text-foreground shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
                            :class="{ 'border-red-400': form.errors.password }"
                        />
                        <InputError :message="form.errors.password" class="mt-1.5" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-foreground mb-1.5">Confirm new password</label>
                        <input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            required
                            placeholder="••••••••"
                            class="w-full rounded-lg border border-input bg-background px-3.5 py-2.5 text-sm text-foreground shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
                            :class="{ 'border-red-400': form.errors.password_confirmation }"
                        />
                        <InputError :message="form.errors.password_confirmation" class="mt-1.5" />
                    </div>

                    <div class="flex items-center gap-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50 transition-colors"
                        >Save password</button>
                        <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" enter-active-class="transition" leave-active-class="transition">
                            <span v-show="form.recentlySuccessful" class="text-sm text-muted-foreground">Saved.</span>
                        </Transition>
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </OwnerLayout>
</template>

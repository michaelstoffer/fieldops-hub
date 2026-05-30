<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

defineProps<{ mustVerifyEmail: boolean; status?: string }>();

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});

const submit = () => form.patch('/settings/profile', { preserveScroll: true });

const deleteForm = useForm({ password: '' });
const submitDelete = () => deleteForm.delete('/settings/profile', { preserveScroll: true });
</script>

<template>
    <OwnerLayout title="Settings">
        <Head title="Profile settings" />

        <SettingsLayout>
            <!-- Profile information -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-base font-medium text-slate-900">Profile information</h3>
                    <p class="text-sm text-slate-500">Update your name and email address.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-5 max-w-md">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Name</label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            autocomplete="name"
                            required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
                            :class="{ 'border-red-400': form.errors.name }"
                        />
                        <InputError :message="form.errors.name" class="mt-1.5" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            autocomplete="username"
                            required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
                            :class="{ 'border-red-400': form.errors.email }"
                        />
                        <InputError :message="form.errors.email" class="mt-1.5" />

                        <div v-if="mustVerifyEmail && !user.email_verified_at" class="mt-2 text-sm text-slate-500">
                            Your email address is unverified.
                            <button
                                type="button"
                                class="text-blue-600 underline hover:text-blue-700"
                                @click="$inertia.post('/email/verification-notification')"
                            >Resend verification email.</button>
                        </div>
                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50 transition-colors"
                        >Save</button>
                        <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" enter-active-class="transition" leave-active-class="transition">
                            <span v-show="form.recentlySuccessful" class="text-sm text-slate-500">Saved.</span>
                        </Transition>
                    </div>
                </form>
            </div>

            <!-- Delete account -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-base font-medium text-slate-900">Delete account</h3>
                    <p class="text-sm text-slate-500">Permanently delete your account and all data.</p>
                </div>

                <div class="rounded-lg border border-red-200 bg-red-50 p-4 space-y-4">
                    <p class="text-sm font-medium text-red-700">Warning — this cannot be undone.</p>
                    <details class="group">
                        <summary class="cursor-pointer text-sm font-semibold text-red-700 list-none flex items-center gap-2">
                            <span>Delete my account</span>
                        </summary>
                        <form @submit.prevent="submitDelete" class="mt-4 space-y-4">
                            <div>
                                <label for="delete-password" class="block text-sm font-medium text-slate-700 mb-1.5">Confirm your password</label>
                                <input
                                    id="delete-password"
                                    v-model="deleteForm.password"
                                    type="password"
                                    autocomplete="current-password"
                                    required
                                    placeholder="••••••••"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 transition"
                                    :class="{ 'border-red-400': deleteForm.errors.password }"
                                />
                                <InputError :message="deleteForm.errors.password" class="mt-1.5" />
                            </div>
                            <button
                                type="submit"
                                :disabled="deleteForm.processing"
                                class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50 transition-colors"
                            >Permanently delete account</button>
                        </form>
                    </details>
                </div>
            </div>
        </SettingsLayout>
    </OwnerLayout>
</template>

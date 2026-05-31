<script setup lang="ts">
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import OwnerLayout from '@/layouts/OwnerLayout.vue';
import { store as teamStore, update as teamUpdate, destroy as teamDestroy, passwordReset as teamPasswordReset } from '@/routes/owner/team/index.ts';
import { index as subscriptionIndex } from '@/routes/owner/subscription/index.ts';

const props = defineProps<{
    team_members: Array<{
        id: number;
        name: string;
        email: string;
        roles: string[];
    }>;
    roles: string[];
    technician_limit: number | null;
    technician_count: number;
    at_limit: boolean;
    active_plan: string;
}>();

const page = usePage();
const showAddForm = ref(false);

const addForm = useForm({
    name: '',
    email: '',
    password: '',
    role: 'technician',
});

function submitAdd() {
    addForm.post(teamStore().url, {
        preserveScroll: true,
        onSuccess: () => {
            addForm.reset();
            showAddForm.value = false;
        },
    });
}

function updateRole(userId: number, role: string) {
    router.patch(teamUpdate(userId).url, { role }, { preserveScroll: true });
}

function removeMember(userId: number, name: string) {
    if (confirm(`Remove ${name} from your team? This cannot be undone.`)) {
        router.delete(teamDestroy(userId).url, { preserveScroll: true });
    }
}

function sendPasswordReset(userId: number, name: string) {
    if (confirm(`Send a password reset email to ${name}?`)) {
        router.post(teamPasswordReset(userId).url, {}, { preserveScroll: true });
    }
}

const ROLE_LABELS: Record<string, string> = {
    owner: 'Owner',
    admin: 'Admin',
    dispatcher: 'Dispatcher',
    bookkeeper: 'Bookkeeper',
    technician: 'Technician',
};

const ROLE_COLORS: Record<string, string> = {
    owner: 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400',
    admin: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
    dispatcher: 'bg-teal-100 text-teal-700 dark:bg-teal-900/40 dark:text-teal-400',
    bookkeeper: 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400',
    technician: 'bg-muted text-foreground',
};
</script>

<template>
    <OwnerLayout title="Team Members">
        <Head title="Team Members" />

        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-10 space-y-6">

            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-foreground">Team Members</h1>
                    <p class="text-sm text-muted-foreground mt-0.5">
                        Manage your organization's users and their roles.
                    </p>
                </div>
                <button
                    type="button"
                    @click="showAddForm = !showAddForm"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition-colors"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add member
                </button>
            </div>

            <!-- Success message -->
            <div v-if="(page.props as any).flash?.success"
                class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ (page.props as any).flash.success }}
            </div>

            <!-- Technician seat usage -->
            <div v-if="technician_limit !== null"
                 class="rounded-xl border p-4 flex items-center gap-4"
                 :class="at_limit ? 'border-amber-200 bg-amber-50' : 'border-border bg-background'">
                <div class="flex-1">
                    <p class="text-sm font-medium" :class="at_limit ? 'text-amber-800' : 'text-foreground'">
                        Technician seats — {{ technician_count }} / {{ technician_limit }} used
                    </p>
                    <div class="mt-1.5 h-2 rounded-full bg-muted overflow-hidden">
                        <div
                            class="h-full rounded-full transition-all"
                            :class="at_limit ? 'bg-amber-400' : 'bg-blue-500'"
                            :style="{ width: `${Math.min(100, (technician_count / technician_limit) * 100)}%` }"
                        ></div>
                    </div>
                </div>
                <div v-if="at_limit" class="shrink-0">
                    <a :href="subscriptionIndex().url"
                       class="text-xs font-semibold text-amber-700 hover:text-amber-800 underline">
                        Upgrade plan
                    </a>
                </div>
            </div>

            <!-- Add member form -->
            <div v-if="showAddForm" class="rounded-2xl border border-border bg-card p-6">
                <h2 class="text-base font-semibold text-foreground mb-4">Add a team member</h2>

                <form @submit.prevent="submitAdd" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">Name</label>
                        <input v-model="addForm.name" type="text" required placeholder="Jane Smith"
                               class="w-full rounded-lg border border-input bg-background px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                               :class="{ 'border-destructive': addForm.errors.name }" />
                        <p v-if="addForm.errors.name" class="mt-1 text-xs text-destructive">{{ addForm.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">Email</label>
                        <input v-model="addForm.email" type="email" required placeholder="jane@company.com"
                               class="w-full rounded-lg border border-input bg-background px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                               :class="{ 'border-destructive': addForm.errors.email }" />
                        <p v-if="addForm.errors.email" class="mt-1 text-xs text-destructive">{{ addForm.errors.email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">Temporary password</label>
                        <input v-model="addForm.password" type="password" required placeholder="••••••••"
                               class="w-full rounded-lg border border-input bg-background px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                               :class="{ 'border-destructive': addForm.errors.password }" />
                        <p v-if="addForm.errors.password" class="mt-1 text-xs text-destructive">{{ addForm.errors.password }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">Role</label>
                        <select v-model="addForm.role" required
                                class="w-full rounded-lg border border-input bg-background px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                :class="{ 'border-destructive': addForm.errors.role }">
                            <option v-for="r in roles" :key="r" :value="r">{{ ROLE_LABELS[r] ?? r }}</option>
                        </select>
                        <p v-if="addForm.errors.role" class="mt-1 text-xs text-destructive">{{ addForm.errors.role }}</p>
                    </div>

                    <div class="sm:col-span-2 flex items-center gap-3">
                        <button type="submit" :disabled="addForm.processing"
                                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white disabled:opacity-50 transition-colors">
                            <svg v-if="addForm.processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            {{ addForm.processing ? 'Adding…' : 'Add member' }}
                        </button>
                        <button type="button" @click="showAddForm = false"
                                class="text-sm text-muted-foreground hover:text-foreground">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Team table -->
            <div class="rounded-2xl border border-border bg-card overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border bg-background">
                            <th class="text-left px-6 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide">Name</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide">Email</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide">Role</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr v-for="member in team_members" :key="member.id" class="hover:bg-accent transition-colors">
                            <td class="px-6 py-4 font-medium text-foreground">{{ member.name }}</td>
                            <td class="px-6 py-4 text-muted-foreground">{{ member.email }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <span v-for="role in member.roles" :key="role"
                                          class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                          :class="ROLE_COLORS[role] ?? 'bg-muted text-muted-foreground'">
                                        {{ ROLE_LABELS[role] ?? role }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <select
                                        :value="member.roles[0]"
                                        @change="updateRole(member.id, ($event.target as HTMLSelectElement).value)"
                                        class="w-36 rounded-lg border border-border bg-background text-foreground px-2.5 py-1.5 text-xs focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:[color-scheme:dark]"
                                    >
                                        <option v-for="r in roles" :key="r" :value="r">{{ ROLE_LABELS[r] ?? r }}</option>
                                    </select>
                                    <button
                                        v-if="member.roles[0] !== 'owner'"
                                        type="button"
                                        @click="sendPasswordReset(member.id, member.name)"
                                        class="rounded-lg p-1.5 text-muted-foreground hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                        title="Send password reset email"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        @click="removeMember(member.id, member.name)"
                                        class="rounded-lg p-1.5 text-muted-foreground hover:text-red-600 hover:bg-red-50 transition-colors"
                                        title="Remove member"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!team_members.length">
                            <td colspan="4" class="px-6 py-12 text-center text-muted-foreground text-sm">
                                No team members yet. Add your first member above.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </OwnerLayout>
</template>

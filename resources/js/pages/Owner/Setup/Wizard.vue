<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface Company {
    name: string | null;
    email: string | null;
    phone: string | null;
    address: string | null;
    city: string | null;
    state: string | null;
    zip: string | null;
}

interface JobType {
    id: number;
    name: string;
    color: string;
}

interface Technician {
    id: number;
    name: string;
    email: string;
}

const props = defineProps<{
    company: Company;
    job_types: JobType[];
    technicians: Technician[];
}>();

// ── Wizard step state ─────────────────────────────────────────────────────────

const step = ref<1 | 2 | 3>(1);
const steps = [
    { id: 1, label: 'Company Info' },
    { id: 2, label: 'Job Types' },
    { id: 3, label: 'Technicians' },
] as const;

const canFinish = computed(() => {
    return !!props.company.name && props.job_types.length > 0 && props.technicians.length > 0;
});

// ── Step 1: Company form ──────────────────────────────────────────────────────

const companyForm = useForm({
    name:    props.company.name ?? '',
    email:   props.company.email ?? '',
    phone:   props.company.phone ?? '',
    address: props.company.address ?? '',
    city:    props.company.city ?? '',
    state:   props.company.state ?? '',
    zip:     props.company.zip ?? '',
});

function saveCompany() {
    companyForm.post('/owner/setup/company', {
        preserveScroll: true,
        onSuccess: () => { step.value = 2; },
    });
}

// ── Step 2: Job types ─────────────────────────────────────────────────────────

const jobTypeForm = useForm({
    name:  '',
    color: '#3b82f6',
});

const PRESET_COLORS = [
    '#3b82f6', '#10b981', '#f59e0b', '#6366f1',
    '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6',
];

function addJobType() {
    jobTypeForm.post('/owner/setup/job-types', {
        preserveScroll: true,
        onSuccess: () => {
            jobTypeForm.reset();
            jobTypeForm.color = '#3b82f6';
        },
    });
}

function removeJobType(id: number) {
    router.delete(`/owner/setup/job-types/${id}`, { preserveScroll: true });
}

// ── Step 3: Technicians ───────────────────────────────────────────────────────

const techForm = useForm({
    name:     '',
    email:    '',
    password: '',
});

function addTechnician() {
    techForm.post('/owner/setup/technicians', {
        preserveScroll: true,
        onSuccess: () => techForm.reset(),
    });
}

// ── Finish ────────────────────────────────────────────────────────────────────

const finishing = ref(false);

function finish() {
    finishing.value = true;
    router.post('/owner/setup/complete', {}, {
        onFinish: () => { finishing.value = false; },
    });
}
</script>

<template>
    <Head title="Setup — FieldOps Hub" />

    <div class="min-h-screen bg-slate-50 flex flex-col items-center justify-start py-12 px-4">
        <!-- Header -->
        <div class="w-full max-w-2xl mb-8 text-center">
            <h1 class="text-2xl font-bold text-slate-900">Welcome to FieldOps Hub</h1>
            <p class="mt-2 text-slate-500 text-sm">Let's get your account set up in a few quick steps.</p>
        </div>

        <!-- Step progress -->
        <div class="w-full max-w-2xl mb-8">
            <ol class="flex items-center gap-0">
                <li
                    v-for="(s, idx) in steps"
                    :key="s.id"
                    class="flex flex-1 items-center"
                >
                    <div class="flex items-center gap-2">
                        <div
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-semibold border-2 transition-colors"
                            :class="step > s.id
                                ? 'bg-green-600 border-green-600 text-white'
                                : step === s.id
                                    ? 'bg-slate-900 border-slate-900 text-white'
                                    : 'border-slate-300 text-slate-400'"
                        >
                            <svg v-if="step > s.id" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.42 0l-3.5-3.5a1 1 0 111.42-1.42L8.5 12.08l6.79-6.79a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span v-else>{{ s.id }}</span>
                        </div>
                        <span
                            class="hidden sm:inline text-sm font-medium"
                            :class="step === s.id ? 'text-slate-900' : 'text-slate-400'"
                        >{{ s.label }}</span>
                    </div>
                    <div v-if="idx < steps.length - 1" class="flex-1 h-0.5 mx-3" :class="step > s.id ? 'bg-green-500' : 'bg-slate-200'" />
                </li>
            </ol>
        </div>

        <!-- Card -->
        <div class="w-full max-w-2xl bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">

            <!-- ── Step 1: Company Info ──────────────────────────────────────── -->
            <div v-if="step === 1">
                <h2 class="text-lg font-semibold text-slate-800 mb-1">Company Information</h2>
                <p class="text-sm text-slate-500 mb-6">This appears on invoices and customer communications.</p>

                <form @submit.prevent="saveCompany" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Company name <span class="text-rose-500">*</span></label>
                        <input
                            v-model="companyForm.name"
                            type="text"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                            placeholder="Acme HVAC Services"
                            required
                        />
                        <p v-if="companyForm.errors.name" class="mt-1 text-xs text-rose-600">{{ companyForm.errors.name }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                            <input v-model="companyForm.email" type="email" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500" placeholder="info@company.com" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                            <input v-model="companyForm.phone" type="tel" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500" placeholder="(555) 000-1234" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Street address</label>
                        <input v-model="companyForm.address" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500" placeholder="123 Main St" />
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-slate-700 mb-1">City</label>
                            <input v-model="companyForm.city" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500" placeholder="Springfield" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">State</label>
                            <input v-model="companyForm.state" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500" placeholder="IL" maxlength="2" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">ZIP</label>
                            <input v-model="companyForm.zip" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500" placeholder="62701" />
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button
                            type="submit"
                            :disabled="companyForm.processing"
                            class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 disabled:opacity-50 transition-colors"
                        >
                            Save &amp; Continue →
                        </button>
                    </div>
                </form>
            </div>

            <!-- ── Step 2: Job Types ─────────────────────────────────────────── -->
            <div v-if="step === 2">
                <h2 class="text-lg font-semibold text-slate-800 mb-1">Service / Job Types</h2>
                <p class="text-sm text-slate-500 mb-6">Add the types of work your team performs (e.g. HVAC, Plumbing, Electrical).</p>

                <!-- Existing job types -->
                <ul v-if="job_types.length > 0" class="mb-5 space-y-2">
                    <li
                        v-for="jt in job_types"
                        :key="jt.id"
                        class="flex items-center justify-between rounded-lg border border-slate-200 px-3 py-2.5"
                    >
                        <div class="flex items-center gap-2.5">
                            <span class="h-3 w-3 rounded-full" :style="{ background: jt.color }" />
                            <span class="text-sm font-medium text-slate-800">{{ jt.name }}</span>
                        </div>
                        <button
                            type="button"
                            class="text-xs text-slate-400 hover:text-rose-600 transition-colors"
                            @click="removeJobType(jt.id)"
                        >Remove</button>
                    </li>
                </ul>
                <p v-else class="mb-5 rounded-lg border border-dashed border-slate-200 px-4 py-6 text-center text-sm text-slate-400">
                    No job types added yet. Add at least one below.
                </p>

                <!-- Add job type form -->
                <form @submit.prevent="addJobType" class="flex items-end gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Name</label>
                        <input
                            v-model="jobTypeForm.name"
                            type="text"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                            placeholder="HVAC Service"
                            required
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Color</label>
                        <div class="flex items-center gap-1.5">
                            <span
                                v-for="c in PRESET_COLORS"
                                :key="c"
                                class="h-6 w-6 rounded-full cursor-pointer border-2 transition-all"
                                :class="jobTypeForm.color === c ? 'border-slate-900 scale-110' : 'border-transparent'"
                                :style="{ background: c }"
                                @click="jobTypeForm.color = c"
                            />
                        </div>
                    </div>
                    <button
                        type="submit"
                        :disabled="jobTypeForm.processing"
                        class="shrink-0 rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50 transition-colors"
                    >
                        + Add
                    </button>
                </form>
                <p v-if="jobTypeForm.errors.name" class="mt-1 text-xs text-rose-600">{{ jobTypeForm.errors.name }}</p>

                <div class="flex justify-between pt-6">
                    <button type="button" class="text-sm text-slate-500 hover:text-slate-700" @click="step = 1">← Back</button>
                    <button
                        type="button"
                        :disabled="job_types.length === 0"
                        class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 disabled:opacity-50 transition-colors"
                        @click="step = 3"
                    >
                        Continue →
                    </button>
                </div>
            </div>

            <!-- ── Step 3: Technicians ───────────────────────────────────────── -->
            <div v-if="step === 3">
                <h2 class="text-lg font-semibold text-slate-800 mb-1">Add Technicians</h2>
                <p class="text-sm text-slate-500 mb-6">Create accounts for your field technicians. They'll use the mobile app to manage jobs.</p>

                <!-- Existing technicians -->
                <ul v-if="technicians.length > 0" class="mb-5 space-y-2">
                    <li
                        v-for="tech in technicians"
                        :key="tech.id"
                        class="flex items-center gap-3 rounded-lg border border-slate-200 px-3 py-2.5"
                    >
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-600">
                            {{ tech.name.charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ tech.name }}</p>
                            <p class="text-xs text-slate-400">{{ tech.email }}</p>
                        </div>
                    </li>
                </ul>
                <p v-else class="mb-5 rounded-lg border border-dashed border-slate-200 px-4 py-6 text-center text-sm text-slate-400">
                    No technicians added yet. Add at least one below.
                </p>

                <!-- Add technician form -->
                <form @submit.prevent="addTechnician" class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Full name</label>
                            <input
                                v-model="techForm.name"
                                type="text"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                                placeholder="Jane Doe"
                                required
                            />
                            <p v-if="techForm.errors.name" class="mt-0.5 text-xs text-rose-600">{{ techForm.errors.name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Email</label>
                            <input
                                v-model="techForm.email"
                                type="email"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                                placeholder="jane@company.com"
                                required
                            />
                            <p v-if="techForm.errors.email" class="mt-0.5 text-xs text-rose-600">{{ techForm.errors.email }}</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Password</label>
                        <input
                            v-model="techForm.password"
                            type="password"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                            placeholder="Minimum 8 characters"
                            required
                        />
                        <p v-if="techForm.errors.password" class="mt-0.5 text-xs text-rose-600">{{ techForm.errors.password }}</p>
                    </div>
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="techForm.processing"
                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50 transition-colors"
                        >
                            + Add Technician
                        </button>
                    </div>
                </form>

                <div class="flex justify-between pt-6">
                    <button type="button" class="text-sm text-slate-500 hover:text-slate-700" @click="step = 2">← Back</button>
                    <button
                        type="button"
                        :disabled="!canFinish || finishing"
                        class="rounded-lg bg-green-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-600 disabled:opacity-50 transition-colors"
                        @click="finish"
                    >
                        {{ finishing ? 'Finishing…' : 'Finish Setup ✓' }}
                    </button>
                </div>
            </div>

        </div>

        <p class="mt-6 text-xs text-slate-400">
            You can update all of this later in <strong>Settings</strong>.
        </p>
    </div>
</template>

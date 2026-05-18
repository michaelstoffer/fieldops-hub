<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import axios from 'axios';

interface Customer {
    id: number;
    first_name: string;
    last_name: string;
    properties: { id: number; address_line1: string; city: string; state: string }[];
}

interface JobType { id: number; name: string; color: string }
interface Technician { id: number; name: string }

interface JobFormData {
    customer_id: number | null;
    property_id: number | null;
    job_type_id: number | null;
    assigned_to: number | null;
    title: string;
    description: string;
    scheduled_at: string;
    office_notes: string;
}

const props = defineProps<{
    form: InertiaForm<JobFormData>;
    customers: Customer[];
    jobTypes: JobType[];
    technicians: Technician[];
}>();

const emit = defineEmits<{
    (e: 'customer-added', customer: Customer): void;
    (e: 'property-added', property: Customer['properties'][number]): void;
    (e: 'job-type-added', jobType: JobType): void;
}>();

const selectedCustomerProperties = computed(() => {
    if (!props.form.customer_id) return [];
    return props.customers.find(c => c.id === Number(props.form.customer_id))?.properties ?? [];
});

function onCustomerChange() {
    props.form.property_id = null;
}

// ── Quick-create customer dialog ───────────────────────────────────────────

const showCustomerDialog = ref(false);
const savingCustomer = ref(false);
const customerDialogError = ref('');

const newCustomer = ref({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    mobile: '',
    notes: '',
});

function openCustomerDialog() {
    newCustomer.value = { first_name: '', last_name: '', email: '', phone: '', mobile: '', notes: '' };
    customerDialogError.value = '';
    showCustomerDialog.value = true;
}

function closeCustomerDialog() {
    showCustomerDialog.value = false;
}

async function saveNewCustomer() {
    if (!newCustomer.value.first_name.trim() || !newCustomer.value.last_name.trim()) {
        customerDialogError.value = 'First and last name are required.';
        return;
    }
    savingCustomer.value = true;
    customerDialogError.value = '';
    try {
        const { data } = await axios.post('/owner/customers/quick-create', newCustomer.value);
        emit('customer-added', data);
        props.form.customer_id = data.id;
        props.form.property_id = null;
        closeCustomerDialog();
    } catch {
        customerDialogError.value = 'Could not save customer. Please try again.';
    } finally {
        savingCustomer.value = false;
    }
}

// ── Quick-create property dialog ───────────────────────────────────────────

const showPropertyDialog = ref(false);
const savingProperty = ref(false);
const propertyDialogError = ref('');

const newProperty = ref({
    name: '',
    address_line1: '',
    address_line2: '',
    city: '',
    state: '',
    postal_code: '',
});

function openPropertyDialog() {
    newProperty.value = { name: '', address_line1: '', address_line2: '', city: '', state: '', postal_code: '' };
    propertyDialogError.value = '';
    showPropertyDialog.value = true;
}

function closePropertyDialog() {
    showPropertyDialog.value = false;
}

// ── Quick-create job type dialog ───────────────────────────────────────────

const showJobTypeDialog = ref(false);
const savingJobType = ref(false);
const jobTypeDialogError = ref('');

const PRESET_COLORS = [
    '#3b82f6', '#10b981', '#f59e0b', '#ef4444',
    '#8b5cf6', '#ec4899', '#14b8a6', '#f97316',
];

const newJobType = ref({ name: '', color: '#3b82f6' });

function openJobTypeDialog() {
    newJobType.value = { name: '', color: '#3b82f6' };
    jobTypeDialogError.value = '';
    showJobTypeDialog.value = true;
}

function closeJobTypeDialog() {
    showJobTypeDialog.value = false;
}

async function saveNewJobType() {
    if (!newJobType.value.name.trim()) {
        jobTypeDialogError.value = 'Name is required.';
        return;
    }
    savingJobType.value = true;
    jobTypeDialogError.value = '';
    try {
        const { data } = await axios.post('/owner/job-types/quick-create', newJobType.value);
        emit('job-type-added', data);
        props.form.job_type_id = data.id;
        closeJobTypeDialog();
    } catch {
        jobTypeDialogError.value = 'Could not save job type. Please try again.';
    } finally {
        savingJobType.value = false;
    }
}

async function saveNewProperty() {
    if (!newProperty.value.address_line1.trim() || !newProperty.value.city.trim() || !newProperty.value.state.trim() || !newProperty.value.postal_code.trim()) {
        propertyDialogError.value = 'Address, city, state, and postal code are required.';
        return;
    }
    savingProperty.value = true;
    propertyDialogError.value = '';
    try {
        const { data } = await axios.post(
            `/owner/customers/${props.form.customer_id}/properties/quick-create`,
            newProperty.value,
        );
        emit('property-added', data);
        props.form.property_id = data.id;
        closePropertyDialog();
    } catch (err: any) {
        const errors = err?.response?.data?.errors;
        if (errors) {
            propertyDialogError.value = Object.values(errors).flat().join(' ');
        } else {
            propertyDialogError.value = 'Could not save property. Please try again.';
        }
    } finally {
        savingProperty.value = false;
    }
}
</script>

<template>
    <div class="space-y-5">
        <!-- Title -->
        <div>
            <label for="title" class="block text-sm font-medium text-slate-700">Title <span class="text-red-500">*</span></label>
            <input
                id="title"
                v-model="form.title"
                type="text"
                class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none"
                :class="{ 'border-red-400': form.errors.title }"
            />
            <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p>
        </div>

        <!-- Customer + Property row -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <div class="flex items-center justify-between">
                    <label for="customer_id" class="block text-sm font-medium text-slate-700">Customer <span class="text-red-500">*</span></label>
                    <button
                        type="button"
                        class="text-xs font-medium text-blue-600 hover:text-blue-800"
                        @click="openCustomerDialog"
                    >
                        + New customer
                    </button>
                </div>
                <select
                    id="customer_id"
                    v-model="form.customer_id"
                    class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none"
                    :class="{ 'border-red-400': form.errors.customer_id }"
                    @change="onCustomerChange"
                >
                    <option :value="null">— Select customer —</option>
                    <option v-for="c in customers" :key="c.id" :value="c.id">
                        {{ c.last_name }}, {{ c.first_name }}
                    </option>
                </select>
                <p v-if="form.errors.customer_id" class="mt-1 text-xs text-red-600">{{ form.errors.customer_id }}</p>
            </div>
            <div>
                <div class="flex items-center justify-between">
                    <label for="property_id" class="block text-sm font-medium text-slate-700">Property</label>
                    <button
                        v-if="form.customer_id"
                        type="button"
                        class="text-xs font-medium text-blue-600 hover:text-blue-800"
                        @click="openPropertyDialog"
                    >
                        + New property
                    </button>
                </div>
                <select
                    id="property_id"
                    v-model="form.property_id"
                    :disabled="!form.customer_id || selectedCustomerProperties.length === 0"
                    class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none disabled:bg-slate-50 disabled:text-slate-400"
                >
                    <option :value="null">— Select property —</option>
                    <option v-for="p in selectedCustomerProperties" :key="p.id" :value="p.id">
                        {{ p.address_line1 }}, {{ p.city }}, {{ p.state }}
                    </option>
                </select>
            </div>
        </div>

        <!-- Job type + Technician row -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <div class="flex items-center justify-between">
                    <label for="job_type_id" class="block text-sm font-medium text-slate-700">Job Type</label>
                    <button
                        type="button"
                        class="text-xs font-medium text-blue-600 hover:text-blue-800"
                        @click="openJobTypeDialog"
                    >
                        + New type
                    </button>
                </div>
                <select
                    id="job_type_id"
                    v-model="form.job_type_id"
                    class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none"
                >
                    <option :value="null">— Select type —</option>
                    <option v-for="t in jobTypes" :key="t.id" :value="t.id">{{ t.name }}</option>
                </select>
            </div>
            <div>
                <label for="assigned_to" class="block text-sm font-medium text-slate-700">Assign To</label>
                <select
                    id="assigned_to"
                    v-model="form.assigned_to"
                    class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none"
                >
                    <option :value="null">— Unassigned —</option>
                    <option v-for="t in technicians" :key="t.id" :value="t.id">{{ t.name }}</option>
                </select>
            </div>
        </div>

        <!-- Scheduled at -->
        <div>
            <label for="scheduled_at" class="block text-sm font-medium text-slate-700">Scheduled Date & Time</label>
            <input
                id="scheduled_at"
                v-model="form.scheduled_at"
                type="datetime-local"
                class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none"
            />
            <p v-if="form.errors.scheduled_at" class="mt-1 text-xs text-red-600">{{ form.errors.scheduled_at }}</p>
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
            <textarea
                id="description"
                v-model="form.description"
                rows="3"
                class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none"
            />
        </div>

        <!-- Office notes -->
        <div>
            <label for="office_notes" class="block text-sm font-medium text-slate-700">Office Notes</label>
            <textarea
                id="office_notes"
                v-model="form.office_notes"
                rows="3"
                class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-slate-400 focus:outline-none"
            />
        </div>
    </div>

    <!-- Quick-create customer dialog -->
    <Teleport to="body">
        <div
            v-if="showCustomerDialog"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
            @click.self="closeCustomerDialog"
        >
            <div class="w-full max-w-md rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h3 class="text-base font-semibold text-slate-800">New Customer</h3>
                    <button type="button" class="text-slate-400 hover:text-slate-600" @click="closeCustomerDialog">✕</button>
                </div>
                <div class="space-y-4 px-6 py-5">
                    <p v-if="customerDialogError" class="rounded bg-red-50 px-3 py-2 text-xs text-red-600">{{ customerDialogError }}</p>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-600">First Name <span class="text-red-500">*</span></label>
                            <input
                                v-model="newCustomer.first_name"
                                type="text"
                                class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600">Last Name <span class="text-red-500">*</span></label>
                            <input
                                v-model="newCustomer.last_name"
                                type="text"
                                class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600">Email</label>
                        <input
                            v-model="newCustomer.email"
                            type="email"
                            class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-600">Phone</label>
                            <input
                                v-model="newCustomer.phone"
                                type="tel"
                                class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600">Mobile</label>
                            <input
                                v-model="newCustomer.mobile"
                                type="tel"
                                class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                            />
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
                    <button
                        type="button"
                        class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50"
                        @click="closeCustomerDialog"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        :disabled="savingCustomer"
                        class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                        @click="saveNewCustomer"
                    >
                        {{ savingCustomer ? 'Saving…' : 'Save Customer' }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>

    <!-- Quick-create job type dialog -->
    <Teleport to="body">
        <div
            v-if="showJobTypeDialog"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
            @click.self="closeJobTypeDialog"
        >
            <div class="w-full max-w-sm rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h3 class="text-base font-semibold text-slate-800">New Job Type</h3>
                    <button type="button" class="text-slate-400 hover:text-slate-600" @click="closeJobTypeDialog">✕</button>
                </div>
                <div class="space-y-4 px-6 py-5">
                    <p v-if="jobTypeDialogError" class="rounded bg-red-50 px-3 py-2 text-xs text-red-600">{{ jobTypeDialogError }}</p>

                    <div>
                        <label class="block text-xs font-medium text-slate-600">Name <span class="text-red-500">*</span></label>
                        <input
                            v-model="newJobType.name"
                            type="text"
                            placeholder="e.g. HVAC Maintenance"
                            class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600">Color</label>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <button
                                v-for="c in PRESET_COLORS"
                                :key="c"
                                type="button"
                                class="h-7 w-7 rounded-full ring-2 ring-offset-2 transition"
                                :style="{ backgroundColor: c }"
                                :class="newJobType.color === c ? 'ring-slate-700' : 'ring-transparent hover:ring-slate-300'"
                                @click="newJobType.color = c"
                            />
                            <input
                                v-model="newJobType.color"
                                type="color"
                                class="h-7 w-7 cursor-pointer rounded-full border border-slate-200"
                                title="Custom color"
                            />
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
                    <button
                        type="button"
                        class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50"
                        @click="closeJobTypeDialog"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        :disabled="savingJobType"
                        class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                        @click="saveNewJobType"
                    >
                        {{ savingJobType ? 'Saving…' : 'Save Job Type' }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>

    <!-- Quick-create property dialog -->
    <Teleport to="body">
        <div
            v-if="showPropertyDialog"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
            @click.self="closePropertyDialog"
        >
            <div class="w-full max-w-md rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h3 class="text-base font-semibold text-slate-800">New Property</h3>
                    <button type="button" class="text-slate-400 hover:text-slate-600" @click="closePropertyDialog">✕</button>
                </div>
                <div class="space-y-4 px-6 py-5">
                    <p v-if="propertyDialogError" class="rounded bg-red-50 px-3 py-2 text-xs text-red-600">{{ propertyDialogError }}</p>

                    <div>
                        <label class="block text-xs font-medium text-slate-600">Property Name <span class="text-slate-400">(optional)</span></label>
                        <input
                            v-model="newProperty.name"
                            type="text"
                            placeholder="e.g. Main Office, Warehouse"
                            class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600">Address <span class="text-red-500">*</span></label>
                        <input
                            v-model="newProperty.address_line1"
                            type="text"
                            placeholder="Street address"
                            class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600">Address Line 2</label>
                        <input
                            v-model="newProperty.address_line2"
                            type="text"
                            placeholder="Apt, suite, unit…"
                            class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        />
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-slate-600">City <span class="text-red-500">*</span></label>
                            <input
                                v-model="newProperty.city"
                                type="text"
                                class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600">State <span class="text-red-500">*</span></label>
                            <input
                                v-model="newProperty.state"
                                type="text"
                                maxlength="2"
                                placeholder="TX"
                                class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm uppercase focus:border-slate-400 focus:outline-none"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600">Postal Code <span class="text-red-500">*</span></label>
                        <input
                            v-model="newProperty.postal_code"
                            type="text"
                            placeholder="12345"
                            class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        />
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
                    <button
                        type="button"
                        class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50"
                        @click="closePropertyDialog"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        :disabled="savingProperty"
                        class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
                        @click="saveNewProperty"
                    >
                        {{ savingProperty ? 'Saving…' : 'Save Property' }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>

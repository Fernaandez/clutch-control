<template>
    <AdminLayout>
        <template #header>
            Report #{{ reportRecord.id }}
        </template>

        <div class="mb-6">
            <Link :href="route('admin.reports.index')" class="text-gray-500 hover:text-gray-700 transition">Reports</Link>
            <span class="mx-2 text-gray-400">/</span>
            <span class="text-gray-800 font-medium">Review</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider font-bold">{{ reportRecord.type }}</p>
                                <h2 class="text-2xl font-bold text-gray-900 mt-1">{{ reportRecord.subject }}</h2>
                            </div>
                            <span :class="statusClass(reportRecord.status)" class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                                {{ reportRecord.status }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold">Reason</p>
                            <p class="text-gray-900 capitalize">{{ reportRecord.reason }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold">Created</p>
                            <p class="text-gray-900">{{ formatDate(reportRecord.created_at) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold">Reporter</p>
                            <p class="text-gray-900">{{ reporterLabel }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold">IP</p>
                            <p class="text-gray-900">{{ reportRecord.ip_address || '-' }}</p>
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-100">
                        <p class="text-xs text-gray-500 uppercase font-bold mb-2">Details</p>
                        <div class="bg-gray-50 border border-gray-100 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-line">
                            {{ reportRecord.details || 'No details provided.' }}
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-100">
                        <p class="text-xs text-gray-500 uppercase font-bold mb-2">Reported Content Snapshot</p>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs overflow-auto max-h-96">{{ JSON.stringify(reportRecord.reportable, null, 2) }}</pre>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Review</h3>
                    <form class="space-y-4" @submit.prevent="submit">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select v-model="form.status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="pending">Pending</option>
                                <option value="reviewing">Reviewing</option>
                                <option value="resolved">Resolved</option>
                                <option value="dismissed">Dismissed</option>
                            </select>
                            <p v-if="form.errors.status" class="mt-2 text-sm text-red-600">{{ form.errors.status }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Admin notes</label>
                            <textarea v-model="form.admin_notes" rows="6" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Internal notes..."></textarea>
                            <p v-if="form.errors.admin_notes" class="mt-2 text-sm text-red-600">{{ form.errors.admin_notes }}</p>
                        </div>

                        <button type="submit" :disabled="form.processing" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-md font-medium transition disabled:opacity-60">
                            {{ form.processing ? 'Saving...' : 'Save review' }}
                        </button>
                    </form>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-sm text-gray-600">
                    <p class="text-xs text-gray-500 uppercase font-bold mb-2">Last review</p>
                    <p>{{ reportRecord.reviewer?.email || 'Not reviewed yet' }}</p>
                    <p v-if="reportRecord.reviewed_at" class="text-gray-500 mt-1">{{ formatDate(reportRecord.reviewed_at) }}</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    reportRecord: Object,
});

const form = useForm({
    status: props.reportRecord.status,
    admin_notes: props.reportRecord.admin_notes || '',
});

const reporterLabel = computed(() => {
    if (props.reportRecord.reporter) {
        return `${props.reportRecord.reporter.name} (${props.reportRecord.reporter.email})`;
    }

    return props.reportRecord.contact_email || 'Anonymous';
});

const statusClass = (status) => ({
    'bg-yellow-100 text-yellow-800': status === 'pending',
    'bg-blue-100 text-blue-800': status === 'reviewing',
    'bg-green-100 text-green-800': status === 'resolved',
    'bg-gray-100 text-gray-800': status === 'dismissed',
});

const formatDate = (date) => date ? new Date(date).toLocaleString() : '-';

const submit = () => {
    form.patch(route('admin.reports.update', props.reportRecord.id), {
        preserveScroll: true,
    });
};
</script>

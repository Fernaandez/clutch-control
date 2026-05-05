<template>
    <AdminLayout>
        <template #header>
            Reports
        </template>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-100 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input v-model="filters.search" type="text" placeholder="Search details or email..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <select v-model="filters.status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="all">All statuses</option>
                        <option value="pending">Pending</option>
                        <option value="reviewing">Reviewing</option>
                        <option value="resolved">Resolved</option>
                        <option value="dismissed">Dismissed</option>
                    </select>
                    <select v-model="filters.type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="all">All types</option>
                        <option value="user">User</option>
                        <option value="message">Message</option>
                        <option value="route">Route</option>
                        <option value="event">Event</option>
                        <option value="sale">Sale</option>
                    </select>
                    <select v-model="filters.reason" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="all">All reasons</option>
                        <option value="spam">Spam</option>
                        <option value="harassment">Harassment</option>
                        <option value="scam">Scam</option>
                        <option value="inappropriate">Inappropriate</option>
                        <option value="dangerous">Dangerous</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">All Reports</h2>
                <div class="text-sm text-gray-500">
                    Pending: <span class="font-bold text-red-600">{{ pendingCount }}</span> · Total: {{ reports.total }}
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-500">
                    <thead class="bg-gray-50 text-xs text-gray-700 uppercase">
                        <tr>
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3">Subject</th>
                            <th class="px-6 py-3">Reason</th>
                            <th class="px-6 py-3">Reporter</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="report in reports.data" :key="report.id" class="border-b hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">{{ report.id }}</td>
                            <td class="px-6 py-4">{{ report.type }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 max-w-xs truncate">{{ report.subject }}</td>
                            <td class="px-6 py-4 capitalize">{{ report.reason }}</td>
                            <td class="px-6 py-4">
                                <span v-if="report.reporter">{{ report.reporter.email }}</span>
                                <span v-else>{{ report.contact_email || 'Anonymous' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="statusClass(report.status)" class="px-2 py-1 rounded text-xs font-semibold uppercase tracking-wider">
                                    {{ report.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ formatDate(report.created_at) }}</td>
                            <td class="px-6 py-4 text-right">
                                <Link :href="route('admin.reports.show', report.id)" class="text-blue-600 hover:text-blue-900 font-medium bg-blue-50 px-3 py-1.5 rounded transition">Review</Link>
                            </td>
                        </tr>
                        <tr v-if="reports.data.length === 0">
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">No reports found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100 flex justify-between items-center" v-if="reports.links && reports.links.length > 3">
                <div class="flex space-x-1">
                    <template v-for="(link, i) in reports.links" :key="i">
                        <Link v-if="link.url" :href="link.url" v-html="link.label" class="px-3 py-1 border rounded text-sm" :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 hover:bg-gray-50'"></Link>
                        <span v-else v-html="link.label" class="px-3 py-1 border rounded text-sm text-gray-400 bg-gray-50"></span>
                    </template>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    reports: Object,
    filters: Object,
    pendingCount: Number,
});

const filters = ref({
    search: props.filters?.search || '',
    status: props.filters?.status || 'all',
    type: props.filters?.type || 'all',
    reason: props.filters?.reason || 'all',
});

watch(filters, debounce((value) => {
    router.get(route('admin.reports.index'), value, {
        preserveState: true,
        replace: true,
    });
}, 300), { deep: true });

const statusClass = (status) => ({
    'bg-yellow-100 text-yellow-800': status === 'pending',
    'bg-blue-100 text-blue-800': status === 'reviewing',
    'bg-green-100 text-green-800': status === 'resolved',
    'bg-gray-100 text-gray-800': status === 'dismissed',
});

const formatDate = (date) => date ? new Date(date).toLocaleString() : '-';
</script>

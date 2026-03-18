<template>
    <AdminLayout>
        <template #header>
            Events
        </template>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex flex-col sm:flex-row gap-4 justify-between items-center">
                <div class="flex-1 flex gap-4 w-full">
                    <input 
                        v-model="filters.search" 
                        type="text" 
                        placeholder="Search by title or location..." 
                        class="w-full max-w-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                    <div class="flex items-center gap-2">
                        <label class="text-xs text-gray-500">From:</label>
                        <input 
                            v-model="filters.date_start" 
                            type="date" 
                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-xs text-gray-500">To:</label>
                        <input 
                            v-model="filters.date_end" 
                            type="date" 
                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">All Events</h2>
                <div class="text-sm text-gray-500">Total: {{ events.total }}</div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-500">
                    <thead class="bg-gray-50 text-xs text-gray-700 uppercase">
                        <tr>
                            <th scope="col" class="px-6 py-3">ID</th>
                            <th scope="col" class="px-6 py-3">Title</th>
                            <th scope="col" class="px-6 py-3">Organizer</th>
                            <th scope="col" class="px-6 py-3">Location</th>
                            <th scope="col" class="px-6 py-3">Participants</th>
                            <th scope="col" class="px-6 py-3">Start Time</th>
                            <th scope="col" class="px-6 py-3">Visibility</th>
                            <th scope="col" class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in events.data" :key="item.id" class="border-b hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">{{ item.id }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ item.title }}</td>
                            <td class="px-6 py-4">{{ item.organizer ? item.organizer.name : 'Unknown' }}</td>
                            <td class="px-6 py-4">{{ item.location }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800">
                                    {{ item.participants_count || 0 }} / {{ item.max_participants || '∞' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ new Date(item.start_time).toLocaleString() }}</td>
                            <td class="px-6 py-4">
                                <span :class="{'bg-green-100 text-green-800': item.is_public, 'bg-gray-100 text-gray-800': !item.is_public}" class="px-2 py-1 rounded text-xs font-semibold uppercase tracking-wider">
                                    {{ item.is_public ? 'Public' : 'Private' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <Link :href="route('admin.events.edit', item.id)" class="text-blue-600 hover:text-blue-900 font-medium bg-blue-50 px-3 py-1.5 rounded transition">Edit</Link>
                                <button @click="confirmDelete(item.id)" class="text-red-600 hover:text-red-900 font-medium bg-red-50 px-3 py-1.5 rounded transition">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="events.data.length === 0">
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No events found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination (Simple Example) -->
            <div class="p-4 border-t border-gray-100 flex justify-between items-center" v-if="events.links && events.links.length > 3">
                <div class="flex space-x-1">
                    <template v-for="(link, i) in events.links" :key="i">
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
import { router, Link } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    events: Object,
    filters: Object,
});

const filters = ref({
    search: props.filters?.search || '',
    date_start: props.filters?.date_start || '',
    date_end: props.filters?.date_end || '',
});

watch(filters, debounce(function (value) {
    router.get(route('admin.events.index'), value, {
        preserveState: true,
        replace: true,
    });
}, 300), { deep: true });

const confirmDelete = (id) => {
    if (confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
        router.delete(route('admin.events.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

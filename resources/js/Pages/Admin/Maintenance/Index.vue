<template>
    <AdminLayout>
        <template #header>
            <span>Maintenance Tasks</span>
        </template>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motorcycle / Owner</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title / Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Freq. / Last KM</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="task in tasks.data" :key="task.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">#{{ task.id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div v-if="task.motorcycle">
                                    <div class="font-bold text-gray-900">{{ task.motorcycle.brand }} {{ task.motorcycle.model }}</div>
                                    <div class="text-xs">{{ task.motorcycle.user?.name || 'Unknown User' }}</div>
                                </div>
                                <span v-else class="text-gray-400 italic">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="font-semibold">{{ task.title }} <span v-if="task.is_recurring" class="text-xs bg-blue-100 text-blue-800 px-1 rounded ml-1">Recurring</span></div>
                                <div class="text-xs text-gray-500">{{ task.type || '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ task.frequency_km ? task.frequency_km + ' km' : '-' }} / {{ task.last_km_done ? task.last_km_done + ' km' : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <Link :href="route('admin.maintenance.edit', task.id)" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded transition-colors">Edit</Link>
                                <button @click="deleteTask(task.id)" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded transition-colors">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="tasks.data.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                No maintenance tasks found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="tasks.links && tasks.links.length > 3" class="px-6 py-4 border-t border-gray-100 flex items-center justify-center">
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <Link v-for="(link, i) in tasks.links" :key="i"
                          :href="link.url || '#'"
                          v-html="link.label"
                          class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
                          :class="{'z-10 bg-indigo-50 border-indigo-500 text-indigo-600': link.active, 'opacity-50 cursor-not-allowed': !link.url}"
                          :disabled="!link.url"
                    />
                </nav>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    tasks: Object,
});

const deleteTask = (id) => {
    if (confirm('Are you sure you want to delete this maintenance task? This action cannot be undone.')) {
        router.delete(route('admin.maintenance.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AdminLayout>
        <template #header>
            <span>Motorcycles</span>
        </template>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex flex-col sm:flex-row gap-4 justify-between items-center">
                <div class="flex-1 flex gap-4 w-full">
                    <input 
                        v-model="filters.search" 
                        type="text" 
                        placeholder="Search by brand, model, plate, or owner..." 
                        class="w-full max-w-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                    <input 
                        v-model="filters.year_min" 
                        type="number" 
                        placeholder="Year Min" 
                        class="w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                    <input 
                        v-model="filters.km_max" 
                        type="number" 
                        placeholder="Max KM" 
                        class="w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motorcycle</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year / Plate</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">KMs</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="motorcycle in motorcycles.data" :key="motorcycle.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">#{{ motorcycle.id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span v-if="motorcycle.user">{{ motorcycle.user.name }}</span>
                                <span v-else class="text-gray-400 italic">Unknown</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                {{ motorcycle.brand }} {{ motorcycle.model }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ motorcycle.year }} <span v-if="motorcycle.plate" class="bg-gray-100 border border-gray-300 rounded px-1">{{ motorcycle.plate }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ motorcycle.current_km }} km
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <Link :href="route('admin.motorcycles.edit', motorcycle.id)" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded transition-colors">Edit</Link>
                                <button @click="deleteMotorcycle(motorcycle.id)" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded transition-colors">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="motorcycles.data.length === 0">
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                No motorcycles found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="motorcycles.links && motorcycles.links.length > 3" class="px-6 py-4 border-t border-gray-100 flex items-center justify-center">
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <Link v-for="(link, i) in motorcycles.links" :key="i"
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
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    motorcycles: Object,
    filters: Object,
});

const filters = ref({
    search: props.filters?.search || '',
    year_min: props.filters?.year_min || '',
    km_max: props.filters?.km_max || '',
});

watch(filters, debounce(function (value) {
    router.get(route('admin.motorcycles.index'), value, {
        preserveState: true,
        replace: true,
    });
}, 300), { deep: true });

const deleteMotorcycle = (id) => {
    if (confirm('Are you sure you want to delete this motorcycle? This action cannot be undone.')) {
        router.delete(route('admin.motorcycles.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

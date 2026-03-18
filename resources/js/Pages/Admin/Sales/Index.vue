<template>
    <AdminLayout>
        <template #header>
            Sales (Marketplace)
        </template>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex flex-col sm:flex-row gap-4 justify-between items-center">
                <div class="flex-1 flex gap-4 w-full">
                    <input 
                        v-model="filters.search" 
                        type="text" 
                        placeholder="Search by title or seller..." 
                        class="w-full max-w-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                    <select 
                        v-model="filters.state" 
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                        <option value="all">All States</option>
                        <option value="actiu">Active</option>
                        <option value="actiu (reservat) (nou)">Reserved</option>
                        <option value="venuda">Sold</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">All Sales Listings</h2>
                <div class="text-sm text-gray-500">Total: {{ sales.total }}</div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-500">
                    <thead class="bg-gray-50 text-xs text-gray-700 uppercase">
                        <tr>
                            <th scope="col" class="px-6 py-3">ID</th>
                            <th scope="col" class="px-6 py-3">Title</th>
                            <th scope="col" class="px-6 py-3">Seller</th>
                            <th scope="col" class="px-6 py-3">Price</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in sales.data" :key="item.id" class="border-b hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">{{ item.id }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ item.title }}</td>
                            <td class="px-6 py-4">{{ item.motorcycle?.user?.name || 'Unknown' }}</td>
                            <td class="px-6 py-4 font-semibold">{{ item.price }} €</td>
                            <td class="px-6 py-4">
                                <span v-if="item.state === 'venuda'" class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold uppercase tracking-wider mr-2">Sold</span>
                                <span v-if="item.state === 'actiu'" class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold uppercase tracking-wider">Active</span>
                                <span v-if="item.state === 'actiu (reservat) (nou)'" class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-semibold uppercase tracking-wider">Reserved</span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <Link :href="route('admin.sales.edit', item.id)" class="text-blue-600 hover:text-blue-900 font-medium bg-blue-50 px-3 py-1.5 rounded transition">Edit</Link>
                                <button @click="confirmDelete(item.id)" class="text-red-600 hover:text-red-900 font-medium bg-red-50 px-3 py-1.5 rounded transition">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="sales.data.length === 0">
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No sale listings found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination (Simple Example) -->
            <div class="p-4 border-t border-gray-100 flex justify-between items-center" v-if="sales.links && sales.links.length > 3">
                <div class="flex space-x-1">
                    <template v-for="(link, i) in sales.links" :key="i">
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
    sales: Object,
    filters: Object,
});

const filters = ref({
    search: props.filters?.search || '',
    state: props.filters?.state || 'all',
});

watch(filters, debounce(function (value) {
    router.get(route('admin.sales.index'), value, {
        preserveState: true,
        replace: true,
    });
}, 300), { deep: true });

const confirmDelete = (id) => {
    if (confirm('Are you sure you want to delete this sale listing? This action cannot be undone.')) {
        router.delete(route('admin.sales.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

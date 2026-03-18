<template>
    <AdminLayout>
        <template #header>
            Routes
        </template>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex flex-col sm:flex-row gap-4 justify-between items-center">
                <div class="flex-1 flex gap-4 w-full">
                    <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-10 gap-3 w-full">
                        <input 
                            v-model="filters.search_title" 
                            type="text" 
                            placeholder="Search title..." 
                            class="col-span-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                        <input 
                            v-model="filters.search_user" 
                            type="text" 
                            placeholder="Search user..." 
                            class="col-span-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                        <div class="col-span-2 flex gap-1">
                             <input v-model="filters.distance_min" type="number" placeholder="Min KM" class="w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                             <input v-model="filters.distance_max" type="number" placeholder="Max KM" class="w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div class="col-span-2 flex gap-1">
                             <input v-model="filters.duration_min" type="number" placeholder="Min min" class="w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                             <input v-model="filters.duration_max" type="number" placeholder="Max min" class="w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        
                        <select 
                            v-model="filters.difficulty" 
                            class="col-span-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option value="all">Any Diff.</option>
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                        <select 
                            v-model="filters.visibility" 
                            class="col-span-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option value="all">Any Vis.</option>
                            <option value="public">Public</option>
                            <option value="private">Private</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">All Routes</h2>
                <div class="text-sm text-gray-500">Total: {{ routes.total }}</div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-500">
                    <thead class="bg-gray-50 text-xs text-gray-700 uppercase">
                        <tr>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 select-none" @click="sortBy('id')">
                                ID <span v-if="filters.sort_field === 'id'">{{ filters.sort_dir === 'asc' ? '↑' : '↓' }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 select-none" @click="sortBy('title')">
                                Title <span v-if="filters.sort_field === 'title'">{{ filters.sort_dir === 'asc' ? '↑' : '↓' }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 select-none" @click="sortBy('user')">
                                User <span v-if="filters.sort_field === 'user'">{{ filters.sort_dir === 'asc' ? '↑' : '↓' }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 select-none" @click="sortBy('planned_distance_km')">
                                Distance <span v-if="filters.sort_field === 'planned_distance_km'">{{ filters.sort_dir === 'asc' ? '↑' : '↓' }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 select-none" @click="sortBy('duration_seconds')">
                                Time <span v-if="filters.sort_field === 'duration_seconds'">{{ filters.sort_dir === 'asc' ? '↑' : '↓' }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 select-none" @click="sortBy('difficulty')">
                                Difficulty <span v-if="filters.sort_field === 'difficulty'">{{ filters.sort_dir === 'asc' ? '↑' : '↓' }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 select-none" @click="sortBy('is_public')">
                                Visibility <span v-if="filters.sort_field === 'is_public'">{{ filters.sort_dir === 'asc' ? '↑' : '↓' }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in routes.data" :key="item.id" class="border-b hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">{{ item.id }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ item.title }}</td>
                            <td class="px-6 py-4">{{ item.user ? item.user.name : 'Unknown' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ item.planned_distance_km ? item.planned_distance_km + ' km' : (item.distance_km ? item.distance_km + ' km' : '-') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ formatDuration(item.duration_seconds) }}</td>
                            <td class="px-6 py-4 capitalize">{{ item.difficulty || '-' }}</td>
                            <td class="px-6 py-4">
                                <span :class="{'bg-green-100 text-green-800': item.is_public, 'bg-gray-100 text-gray-800': !item.is_public}" class="px-2 py-1 rounded text-xs font-semibold uppercase tracking-wider">
                                    {{ item.is_public ? 'Public' : 'Private' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <Link :href="route('admin.routes.edit', item.id)" class="text-blue-600 hover:text-blue-900 font-medium bg-blue-50 px-3 py-1.5 rounded transition">Edit</Link>
                                <button @click="confirmDelete(item.id)" class="text-red-600 hover:text-red-900 font-medium bg-red-50 px-3 py-1.5 rounded transition">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="routes.data.length === 0">
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">No routes found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination (Simple Example) -->
            <div class="p-4 border-t border-gray-100 flex justify-between items-center" v-if="routes.links && routes.links.length > 3">
                <div class="flex space-x-1">
                    <template v-for="(link, i) in routes.links" :key="i">
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
    routes: Object,
    filters: Object,
});

const filters = ref({
    search_title: props.filters?.search_title || '',
    search_user: props.filters?.search_user || '',
    distance_min: props.filters?.distance_min || '',
    distance_max: props.filters?.distance_max || '',
    duration_min: props.filters?.duration_min || '',
    duration_max: props.filters?.duration_max || '',
    difficulty: props.filters?.difficulty || 'all',
    visibility: props.filters?.visibility || 'all',
    sort_field: props.filters?.sort_field || 'id',
    sort_dir: props.filters?.sort_dir || 'desc',
});

const sortBy = (field) => {
    if (filters.value.sort_field === field) {
        filters.value.sort_dir = filters.value.sort_dir === 'asc' ? 'desc' : 'asc';
    } else {
        filters.value.sort_field = field;
        filters.value.sort_dir = 'asc';
    }
};

const formatDuration = (seconds) => {
    if (!seconds) return '-';
    const hrs = Math.floor(seconds / 3600);
    const mins = Math.floor((seconds % 3600) / 60);
    if (hrs > 0) return `${hrs}h ${mins}m`;
    return `${mins}m`;
};

watch(filters, debounce(function (value) {
    router.get(route('admin.routes.index'), value, {
        preserveState: true,
        replace: true,
    });
}, 300), { deep: true });

const confirmDelete = (id) => {
    if (confirm('Are you sure you want to delete this route? This action cannot be undone.')) {
        router.delete(route('admin.routes.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

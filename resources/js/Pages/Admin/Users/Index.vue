<template>
    <AdminLayout>
        <template #header>
            Users
        </template>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex flex-col sm:flex-row gap-4 justify-between items-center">
                <div class="flex-1 flex gap-4 w-full">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 w-full">
                        <input 
                            v-model="filters.search_name" 
                            type="text" 
                            placeholder="Search by name..." 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                        <input 
                            v-model="filters.search_email" 
                            type="text" 
                            placeholder="Search by email..." 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                        <input 
                            v-model="filters.search_phone" 
                            type="text" 
                            placeholder="Search by phone..." 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                        <select 
                            v-model="filters.role" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option value="all">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">All Users</h2>
                <div class="text-sm text-gray-500">Total: {{ users.total }}</div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-500">
                    <thead class="bg-gray-50 text-xs text-gray-700 uppercase">
                        <tr>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 select-none" @click="sortBy('id')">
                                ID <span v-if="filters.sort_field === 'id'">{{ filters.sort_dir === 'asc' ? '↑' : '↓' }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 select-none" @click="sortBy('name')">
                                Name <span v-if="filters.sort_field === 'name'">{{ filters.sort_dir === 'asc' ? '↑' : '↓' }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 select-none" @click="sortBy('email')">
                                Email <span v-if="filters.sort_field === 'email'">{{ filters.sort_dir === 'asc' ? '↑' : '↓' }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 select-none" @click="sortBy('phone_number')">
                                Phone <span v-if="filters.sort_field === 'phone_number'">{{ filters.sort_dir === 'asc' ? '↑' : '↓' }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer hover:bg-gray-100 select-none" @click="sortBy('role')">
                                Role <span v-if="filters.sort_field === 'role'">{{ filters.sort_dir === 'asc' ? '↑' : '↓' }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users.data" :key="user.id" class="border-b hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">{{ user.id }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ user.name }}</td>
                            <td class="px-6 py-4">{{ user.email }}</td>
                            <td class="px-6 py-4">{{ user.phone_number || '-' }}</td>
                            <td class="px-6 py-4">
                                <span :class="{'bg-purple-100 text-purple-800': user.role === 'admin', 'bg-gray-100 text-gray-800': user.role !== 'admin'}" class="px-2 py-1 rounded text-xs font-semibold uppercase tracking-wider">
                                    {{ user.role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <Link :href="route('admin.users.edit', user.id)" class="text-blue-600 hover:text-blue-900 font-medium bg-blue-50 px-3 py-1.5 rounded transition">Edit</Link>
                                <button v-if="$page.props.auth.user.id !== user.id" @click="confirmDelete(user.id)" class="text-red-600 hover:text-red-900 font-medium bg-red-50 px-3 py-1.5 rounded transition">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No users found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination (Simple Example) -->
            <div class="p-4 border-t border-gray-100 flex justify-between items-center" v-if="users.links && users.links.length > 3">
                <div class="flex space-x-1">
                    <template v-for="(link, i) in users.links" :key="i">
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
    users: Object,
    filters: Object,
});

const filters = ref({
    search_name: props.filters?.search_name || '',
    search_email: props.filters?.search_email || '',
    search_phone: props.filters?.search_phone || '',
    role: props.filters?.role || 'all',
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

watch(filters, debounce(function (value) {
    router.get(route('admin.users.index'), value, {
        preserveState: true,
        replace: true,
    });
}, 300), { deep: true });

const confirmDelete = (id) => {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        router.delete(route('admin.users.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

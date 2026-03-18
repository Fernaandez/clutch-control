<template>
    <AdminLayout>
        <template #header>
            <div class="flex items-center space-x-2">
                <Link :href="route('admin.sales.index')" class="text-gray-500 hover:text-gray-700 transition">Sales</Link>
                <span class="text-gray-400">/</span>
                <span>Edit Sale Listing</span>
            </div>
        </template>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
            <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2">Listing Details (ID: {{ saleRecord.id }})</h2>
            <p class="text-sm text-gray-500 mb-2">Seller: {{ saleRecord.motorcycle?.user?.name }}</p>
            <p class="text-sm text-gray-500 mb-6">Motorcycle: {{ saleRecord.motorcycle?.brand }} {{ saleRecord.motorcycle?.model }}</p>
            
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input id="title" v-model="form.title" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    <p v-if="form.errors.title" class="mt-2 text-sm text-red-600">{{ form.errors.title }}</p>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" v-model="form.description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required></textarea>
                    <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{ form.errors.description }}</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Price (€)</label>
                        <input id="price" v-model="form.price" type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <p v-if="form.errors.price" class="mt-2 text-sm text-red-600">{{ form.errors.price }}</p>
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                        <input id="location" v-model="form.location" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <p v-if="form.errors.location" class="mt-2 text-sm text-red-600">{{ form.errors.location }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 border-t pt-4">
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">Listing State</label>
                        <select id="state" v-model="form.state" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="actiu">Active</option>
                            <option value="actiu (reservat) (nou)">Reserved</option>
                            <option value="venuda">Sold</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end space-x-3">
                    <Link :href="route('admin.sales.index')" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </Link>
                    <button type="submit" :disabled="form.processing" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    saleRecord: Object,
});

const form = useForm({
    title: props.saleRecord.title,
    description: props.saleRecord.description,
    price: props.saleRecord.price,
    location: props.saleRecord.location,
    state: props.saleRecord.state || 'actiu',
});

const submit = () => {
    form.put(route('admin.sales.update', props.saleRecord.id));
};
</script>

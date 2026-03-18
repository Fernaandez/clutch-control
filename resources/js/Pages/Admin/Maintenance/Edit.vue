<template>
    <AdminLayout>
        <template #header>
            <div class="flex items-center space-x-2">
                <Link :href="route('admin.maintenance.index')" class="text-gray-500 hover:text-gray-700 transition">Maintenance Tasks</Link>
                <span class="text-gray-400">/</span>
                <span>Edit Task</span>
            </div>
        </template>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
            <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2">Maintenance Details (ID: {{ maintenanceRecord.id }})</h2>
            
            <div v-if="maintenanceRecord.motorcycle" class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-100">
                <p class="text-sm text-gray-500">Motorcycle: <span class="font-bold text-gray-800">{{ maintenanceRecord.motorcycle.brand }} {{ maintenanceRecord.motorcycle.model }}</span> ({{ maintenanceRecord.motorcycle.plate }})</p>
                <p class="text-sm text-gray-500">Owner: {{ maintenanceRecord.motorcycle.user?.name }} ({{ maintenanceRecord.motorcycle.user?.email }})</p>
            </div>
            
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input id="title" v-model="form.title" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    <p v-if="form.errors.title" class="mt-2 text-sm text-red-600">{{ form.errors.title }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type (Workshop, Garage, etc.)</label>
                        <input id="type" v-model="form.type" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p v-if="form.errors.type" class="mt-2 text-sm text-red-600">{{ form.errors.type }}</p>
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">Location (City or Shop Name)</label>
                        <input id="location" v-model="form.location" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p v-if="form.errors.location" class="mt-2 text-sm text-red-600">{{ form.errors.location }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="frequency_km" class="block text-sm font-medium text-gray-700">Frequency (KMs)</label>
                        <input id="frequency_km" v-model="form.frequency_km" type="number" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p v-if="form.errors.frequency_km" class="mt-2 text-sm text-red-600">{{ form.errors.frequency_km }}</p>
                    </div>
                    
                    <div>
                        <label for="is_recurring" class="block text-sm font-medium text-gray-700">Recurring Task?</label>
                        <div class="mt-2 inline-flex items-center">
                            <input id="is_recurring" type="checkbox" v-model="form.is_recurring" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <label for="is_recurring" class="ml-2 text-sm text-gray-600">Yes, it repeats</label>
                        </div>
                        <p v-if="form.errors.is_recurring" class="mt-2 text-sm text-red-600">{{ form.errors.is_recurring }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="last_km_done" class="block text-sm font-medium text-gray-700">Last KMs Done</label>
                        <input id="last_km_done" v-model="form.last_km_done" type="number" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p v-if="form.errors.last_km_done" class="mt-2 text-sm text-red-600">{{ form.errors.last_km_done }}</p>
                    </div>
                    <div>
                        <label for="last_date_done" class="block text-sm font-medium text-gray-700">Last Date Done</label>
                        <input id="last_date_done" v-model="form.last_date_done" type="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p v-if="form.errors.last_date_done" class="mt-2 text-sm text-red-600">{{ form.errors.last_date_done }}</p>
                    </div>
                </div>

                <div>
                    <label for="affiliate_link" class="block text-sm font-medium text-gray-700">Affiliate / Part Link</label>
                    <input id="affiliate_link" v-model="form.affiliate_link" type="url" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <p v-if="form.errors.affiliate_link" class="mt-2 text-sm text-red-600">{{ form.errors.affiliate_link }}</p>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end space-x-3">
                    <Link :href="route('admin.maintenance.index')" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
    maintenanceRecord: Object,
});

const form = useForm({
    title: props.maintenanceRecord.title,
    type: props.maintenanceRecord.type,
    location: props.maintenanceRecord.location,
    frequency_km: props.maintenanceRecord.frequency_km,
    last_km_done: props.maintenanceRecord.last_km_done,
    last_date_done: props.maintenanceRecord.last_date_done,
    is_recurring: props.maintenanceRecord.is_recurring == 1,
    affiliate_link: props.maintenanceRecord.affiliate_link,
});

const submit = () => {
    form.put(route('admin.maintenance.update', props.maintenanceRecord.id));
};
</script>

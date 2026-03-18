<template>
    <AdminLayout>
        <template #header>
            <div class="flex items-center space-x-2">
                <Link :href="route('admin.motorcycles.index')" class="text-gray-500 hover:text-gray-700 transition">Motorcycles</Link>
                <span class="text-gray-400">/</span>
                <span>Edit Motorcycle</span>
            </div>
        </template>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
            <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2">Motorcycle Details (ID: {{ motorcycleRecord.id }})</h2>
            <p class="text-sm text-gray-500 mb-6">Owner: {{ motorcycleRecord.user?.name }} ({{ motorcycleRecord.user?.email }})</p>
            
            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="brand" class="block text-sm font-medium text-gray-700">Brand</label>
                        <input id="brand" v-model="form.brand" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <p v-if="form.errors.brand" class="mt-2 text-sm text-red-600">{{ form.errors.brand }}</p>
                    </div>

                    <div>
                        <label for="model" class="block text-sm font-medium text-gray-700">Model</label>
                        <input id="model" v-model="form.model" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <p v-if="form.errors.model" class="mt-2 text-sm text-red-600">{{ form.errors.model }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                        <input id="year" v-model="form.year" type="number" min="1900" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <p v-if="form.errors.year" class="mt-2 text-sm text-red-600">{{ form.errors.year }}</p>
                    </div>

                    <div>
                        <label for="plate" class="block text-sm font-medium text-gray-700">Plate (License)</label>
                        <input id="plate" v-model="form.plate" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm uppercase">
                        <p v-if="form.errors.plate" class="mt-2 text-sm text-red-600">{{ form.errors.plate }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="current_km" class="block text-sm font-medium text-gray-700">Current KMs</label>
                        <input id="current_km" v-model="form.current_km" type="number" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <p v-if="form.errors.current_km" class="mt-2 text-sm text-red-600">{{ form.errors.current_km }}</p>
                    </div>
                    <div>
                        <label for="cc" class="block text-sm font-medium text-gray-700">CC</label>
                        <input id="cc" v-model="form.cc" type="number" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p v-if="form.errors.cc" class="mt-2 text-sm text-red-600">{{ form.errors.cc }}</p>
                    </div>
                    <div>
                        <label for="power_cv" class="block text-sm font-medium text-gray-700">Power (CV)</label>
                        <input id="power_cv" v-model="form.power_cv" type="number" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p v-if="form.errors.power_cv" class="mt-2 text-sm text-red-600">{{ form.errors.power_cv }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type (Sport, Naked, Trail...)</label>
                        <input id="type" v-model="form.type" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p v-if="form.errors.type" class="mt-2 text-sm text-red-600">{{ form.errors.type }}</p>
                    </div>

                    <div>
                        <label for="license_type" class="block text-sm font-medium text-gray-700">License Required (A, A2, etc.)</label>
                        <input id="license_type" v-model="form.license_type" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p v-if="form.errors.license_type" class="mt-2 text-sm text-red-600">{{ form.errors.license_type }}</p>
                    </div>
                </div>

                <div>
                    <label for="extras" class="block text-sm font-medium text-gray-700">Extras / Notes</label>
                    <textarea id="extras" v-model="form.extras" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                    <p v-if="form.errors.extras" class="mt-2 text-sm text-red-600">{{ form.errors.extras }}</p>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end space-x-3">
                    <Link :href="route('admin.motorcycles.index')" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
    motorcycleRecord: Object,
});

const form = useForm({
    brand: props.motorcycleRecord.brand,
    model: props.motorcycleRecord.model,
    year: props.motorcycleRecord.year,
    plate: props.motorcycleRecord.plate,
    current_km: props.motorcycleRecord.current_km,
    cc: props.motorcycleRecord.cc,
    power_cv: props.motorcycleRecord.power_cv,
    license_type: props.motorcycleRecord.license_type,
    type: props.motorcycleRecord.type,
    extras: props.motorcycleRecord.extras,
});

const submit = () => {
    form.put(route('admin.motorcycles.update', props.motorcycleRecord.id));
};
</script>

<template>
    <AdminLayout>
        <template #header>
            <div class="flex items-center space-x-2">
                <Link :href="route('admin.routes.index')" class="text-gray-500 hover:text-gray-700 transition">Routes</Link>
                <span class="text-gray-400">/</span>
                <span>Edit Route</span>
            </div>
        </template>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-4xl">
            <div class="flex justify-between items-center mb-6 border-b pb-2">
                <h2 class="text-xl font-bold text-gray-800">Route Details (ID: {{ routeRecord.id }})</h2>
                <div class="flex gap-2">
                    <a v-if="routeRecord.start_lat && routeRecord.start_lng" :href="`https://www.google.com/maps/search/?api=1&query=${routeRecord.start_lat},${routeRecord.start_lng}`" target="_blank" class="px-3 py-1 bg-green-100 text-green-700 text-sm font-semibold rounded hover:bg-green-200 transition">
                        🌍 Open in Google Maps
                    </a>
                </div>
            </div>
            <p class="text-sm text-gray-500 mb-6">Owner: {{ routeRecord.user?.name }} ({{ routeRecord.user?.email }})</p>
            
            <!-- Map Preview -->
            <div v-if="routeRecord.geo_json" class="w-full h-96 rounded-xl overflow-hidden mb-8 border border-gray-200 shadow relative">
                <div id="interactive-route-map" class="w-full h-full bg-gray-900 z-0"></div>
                <div class="absolute bottom-2 right-2 bg-white px-2 py-1 rounded text-xs font-bold text-gray-700 shadow border border-gray-200 z-[400] pointer-events-none">
                    Interactive Route Map
                </div>
            </div>

            <!-- Image Preview -->
            <div v-if="routeRecord.photo" class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">Route Image</label>
                <img :src="routeRecord.photo" alt="Route Image" class="h-48 rounded shadow object-cover">
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input id="title" v-model="form.title" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <p v-if="form.errors.title" class="mt-2 text-sm text-red-600">{{ form.errors.title }}</p>
                    </div>

                    <div>
                        <label for="motorcycle_id" class="block text-sm font-medium text-gray-700">Motorcycle Used</label>
                        <select id="motorcycle_id" v-model="form.motorcycle_id" class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">None</option>
                            <option v-for="moto in userMotorcycles" :key="moto.id" :value="moto.id">
                                {{ moto.brand }} {{ moto.model }} ({{ moto.plate }})
                            </option>
                        </select>
                        <p v-if="form.errors.motorcycle_id" class="mt-2 text-sm text-red-600">{{ form.errors.motorcycle_id }}</p>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" v-model="form.description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                    <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{ form.errors.description }}</p>
                </div>
                
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="difficulty" class="block text-sm font-medium text-gray-700">Difficulty</label>
                        <select id="difficulty" v-model="form.difficulty" class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">None</option>
                            <option value="easy">🟢 Fàcil</option>
                            <option value="medium">🟡 Mitjana</option>
                            <option value="hard">🔴 Difícil</option>
                        </select>
                        <p v-if="form.errors.difficulty" class="mt-2 text-sm text-red-600">{{ form.errors.difficulty }}</p>
                    </div>

                    <div>
                        <label for="distance_km" class="block text-sm font-medium text-gray-700">Real Distance (km)</label>
                        <input id="distance_km" v-model="form.distance_km" type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p v-if="form.errors.distance_km" class="mt-2 text-sm text-red-600">{{ form.errors.distance_km }}</p>
                    </div>

                    <div>
                        <label for="planned_distance_km" class="block text-sm font-medium text-gray-700">Planned Distance (km)</label>
                        <input id="planned_distance_km" v-model="form.planned_distance_km" type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p v-if="form.errors.planned_distance_km" class="mt-2 text-sm text-red-600">{{ form.errors.planned_distance_km }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="duration_seconds" class="block text-sm font-medium text-gray-700">Duration (seconds)</label>
                        <input id="duration_seconds" v-model="form.duration_seconds" type="number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p v-if="form.errors.duration_seconds" class="mt-2 text-sm text-red-600">{{ form.errors.duration_seconds }}</p>
                    </div>

                    <div>
                        <label for="share_token" class="block text-sm font-medium text-gray-700">Share Token</label>
                        <input id="share_token" v-model="form.share_token" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p v-if="form.errors.share_token" class="mt-2 text-sm text-red-600">{{ form.errors.share_token }}</p>
                    </div>

                    <div>
                        <label for="is_public" class="block text-sm font-medium text-gray-700">Visibility</label>
                        <div class="mt-2 inline-flex items-center">
                            <input id="is_public" type="checkbox" v-model="form.is_public" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <label for="is_public" class="ml-2 text-sm text-gray-600">Is Public</label>
                        </div>
                        <p v-if="form.errors.is_public" class="mt-2 text-sm text-red-600">{{ form.errors.is_public }}</p>
                    </div>
                </div>

                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700">Photo URL</label>
                    <input id="photo" v-model="form.photo" type="url" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <p v-if="form.errors.photo" class="mt-2 text-sm text-red-600">{{ form.errors.photo }}</p>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end space-x-3">
                    <Link :href="route('admin.routes.index')" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
import { onMounted } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({
    routeRecord: Object,
    userMotorcycles: Array,
});

onMounted(() => {
    if (props.routeRecord.geo_json && document.getElementById('interactive-route-map')) {
        const map = L.map('interactive-route-map', {
            zoomControl: true,
            dragging: true,
            scrollWheelZoom: true,
        });

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        let points = props.routeRecord.geo_json;
        if (typeof points === 'string') {
            try {
                points = JSON.parse(points);
                if (typeof points === 'string') points = JSON.parse(points);
            } catch (e) { return; }
        }

        if (Array.isArray(points)) {
            const validPoints = points.map(p => {
                if (Array.isArray(p) && p.length >= 2) return [p[1], p[0]]; 
                if (p && (p.lat !== undefined || p.latitude !== undefined)) return [p.lat ?? p.latitude, p.lng ?? p.longitude];
                return null;
            }).filter(Boolean);

            if (validPoints.length > 0) {
                const polyline = L.polyline(validPoints, {
                    color: '#0CE1B5',
                    weight: 3,
                    opacity: 1
                }).addTo(map);
                map.fitBounds(polyline.getBounds(), { padding: [20, 20] });
            }
        }
    }
});

const form = useForm({
    title: props.routeRecord.title,
    description: props.routeRecord.description,
    difficulty: props.routeRecord.difficulty || '',
    is_public: props.routeRecord.is_public == 1,
    distance_km: props.routeRecord.distance_km,
    planned_distance_km: props.routeRecord.planned_distance_km,
    duration_seconds: props.routeRecord.duration_seconds,
    share_token: props.routeRecord.share_token,
    motorcycle_id: props.routeRecord.motorcycle_id || '',
    photo: props.routeRecord.photo,
});

const submit = () => {
    form.put(route('admin.routes.update', props.routeRecord.id));
};
</script>

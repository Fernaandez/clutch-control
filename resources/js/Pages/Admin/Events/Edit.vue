<template>
    <AdminLayout>
        <template #header>
            <div class="flex items-center space-x-2">
                <Link :href="route('admin.events.index')" class="text-gray-500 hover:text-gray-700 transition">Events</Link>
                <span class="text-gray-400">/</span>
                <span>Edit Event</span>
            </div>
        </template>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-4xl">
            <div class="flex justify-between items-center mb-6 border-b pb-2">
                <h2 class="text-xl font-bold text-gray-800">Event Details (ID: {{ eventRecord.id }})</h2>
                <div class="flex gap-2">
                    <a v-if="eventRecord.latitude && eventRecord.longitude" :href="`https://www.google.com/maps/search/?api=1&query=${eventRecord.latitude},${eventRecord.longitude}`" target="_blank" class="px-3 py-1 bg-green-100 text-green-700 text-sm font-semibold rounded hover:bg-green-200 transition">
                        🌍 Open in Google Maps
                    </a>
                </div>
            </div>
            <p class="text-sm text-gray-500 mb-6">Organizer: {{ eventRecord.organizer?.name }} ({{ eventRecord.organizer?.email }}) | Joined Participants: {{ eventRecord.participants?.length || 0 }}</p>

            <!-- Image Preview -->
            <div v-if="eventRecord.photo" class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">Event Image</label>
                <img :src="eventRecord.photo.startsWith('http') ? eventRecord.photo : $page.props.storageUrl + '/' + eventRecord.photo" alt="Event Image" class="h-48 rounded shadow object-cover">
            </div>

            <!-- Map Preview -->
            <div v-if="eventRecord.routes && eventRecord.routes.length > 0" class="w-full h-64 rounded-xl overflow-hidden mb-8 border border-gray-200 shadow relative">
                <div id="event-map" class="w-full h-full bg-gray-900 z-0"></div>
                <div class="absolute bottom-2 right-2 bg-white px-2 py-1 rounded text-xs font-bold text-gray-700 shadow border border-gray-200 z-[400] pointer-events-none">
                    Event Global Map ({{ eventRecord.routes.length }} routes)
                </div>
            </div>

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
                
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">Location Name</label>
                        <input id="location" v-model="form.location" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <p v-if="form.errors.location" class="mt-2 text-sm text-red-600">{{ form.errors.location }}</p>
                    </div>
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                        <input id="latitude" v-model="form.latitude" type="number" step="0.00000001" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p v-if="form.errors.latitude" class="mt-2 text-sm text-red-600">{{ form.errors.latitude }}</p>
                    </div>
                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                        <input id="longitude" v-model="form.longitude" type="number" step="0.00000001" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p v-if="form.errors.longitude" class="mt-2 text-sm text-red-600">{{ form.errors.longitude }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                        <input id="start_time" v-model="form.start_time" type="datetime-local" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <p v-if="form.errors.start_time" class="mt-2 text-sm text-red-600">{{ form.errors.start_time }}</p>
                    </div>

                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700">End Time (Optional)</label>
                        <input id="end_time" v-model="form.end_time" type="datetime-local" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p v-if="form.errors.end_time" class="mt-2 text-sm text-red-600">{{ form.errors.end_time }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="max_participants" class="block text-sm font-medium text-gray-700">Max Participants</label>
                        <input id="max_participants" v-model="form.max_participants" type="number" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p v-if="form.errors.max_participants" class="mt-2 text-sm text-red-600">{{ form.errors.max_participants }}</p>
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
                    <label for="photo" class="block text-sm font-medium text-gray-700">Photo URL / Path</label>
                    <input id="photo" v-model="form.photo" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <p v-if="form.errors.photo" class="mt-2 text-sm text-red-600">{{ form.errors.photo }}</p>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end space-x-3">
                    <Link :href="route('admin.events.index')" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
    eventRecord: Object,
});

onMounted(() => {
    if (props.eventRecord.routes && props.eventRecord.routes.length > 0) {
        const map = L.map('event-map', {
            zoomControl: true,
            dragging: true,
            scrollWheelZoom: true,
            doubleClickZoom: true,
            boxZoom: true,
            attributionControl: false
        });

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        const bounds = L.latLngBounds();
        const colors = ['#0CE1B5', '#f43f5e', '#a855f7', '#eab308', '#3b82f6'];

        props.eventRecord.routes.forEach((route, index) => {
            if (!route.geo_json) return;
            let points = route.geo_json;
            if (typeof points === 'string') {
                try {
                    points = JSON.parse(points);
                    if (typeof points === 'string') points = JSON.parse(points);
                } catch (e) { return; }
            }

            if (!Array.isArray(points)) return;

            const validPoints = points.map(p => {
                if (Array.isArray(p) && p.length >= 2) return [p[1], p[0]]; 
                if (p && (p.lat !== undefined || p.latitude !== undefined)) return [p.lat ?? p.latitude, p.lng ?? p.longitude];
                return null;
            }).filter(Boolean);

            if (validPoints.length > 0) {
                const color = colors[index % colors.length];
                const polyline = L.polyline(validPoints, {
                    color: color,
                    weight: 3,
                    opacity: 1
                }).addTo(map);
                bounds.extend(polyline.getBounds());
            }
        });

        if (bounds.isValid()) {
            map.fitBounds(bounds, { padding: [20, 20] });
        }
    }
});

// Helper to format datetime for input[type="datetime-local"]
const formatDatetime = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    const tzoffset = date.getTimezoneOffset() * 60000; // offset in milliseconds
    const localISOTime = (new Date(date - tzoffset)).toISOString().slice(0, 16);
    return localISOTime;
};

const form = useForm({
    title: props.eventRecord.title,
    description: props.eventRecord.description,
    location: props.eventRecord.location,
    latitude: props.eventRecord.latitude,
    longitude: props.eventRecord.longitude,
    start_time: formatDatetime(props.eventRecord.start_time),
    end_time: formatDatetime(props.eventRecord.end_time),
    max_participants: props.eventRecord.max_participants || '',
    share_token: props.eventRecord.share_token,
    photo: props.eventRecord.photo,
    is_public: props.eventRecord.is_public == 1,
});

const submit = () => {
    form.put(route('admin.events.update', props.eventRecord.id));
};
</script>

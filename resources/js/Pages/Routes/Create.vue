<template>
    <AppLayout :title="$t('routes.new_route')">
        <div class="max-w-3xl mx-auto px-4 py-6 pb-24 cc-fade-in">

            <div v-show="!isMapOpen">

                <header class="flex items-center gap-3 mb-6">
                    <button
                        type="button"
                        @click="goBack"
                        class="cc-icon-btn"
                        :aria-label="$t('common.back')"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>
                    </button>
                    <h1 class="cc-title flex-1 truncate">{{ $t('routes.new_route') }}</h1>
                </header>

                <form @submit.prevent="submit" class="space-y-5">

                    <div v-if="Object.keys(form.errors).length > 0" class="cc-card p-4 border-red-500/20 bg-red-500/[0.06] mb-6">
                        <ul class="space-y-1">
                            <li v-for="(error, field) in form.errors" :key="field" class="text-red-400 text-sm">{{ error }}</li>
                        </ul>
                    </div>

                    <div class="cc-card p-5 space-y-5">
                        <p class="cc-section-label">{{ $t('routes.route_name') }}</p>
                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">{{ $t('routes.route_name') }}</label>
                                <input v-model="form.title" type="text" class="w-full text-sm">
                                <div v-if="form.errors.title" class="text-red-400 text-xs mt-1">{{ form.errors.title }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">{{ $t('nav.moto') }}</label>
                                <select v-model="form.motorcycle_id" class="w-full text-sm">
                                    <option :value="null">{{ $t('routes.select_motorcycle') }}</option>
                                    <option v-for="moto in motorcycles" :key="moto.id" :value="moto.id">{{ moto.alias || moto.model }}</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-400 mb-2">{{ $t('routes.route_type') }}</label>
                                <select v-model="form.category_id" class="w-full text-sm">
                                    <option :value="null">{{ $t('routes.select_category') }}</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">{{ $t('routes.description_label') }}</label>
                            <textarea v-model="form.description" rows="3" class="w-full text-sm"></textarea>
                            <div v-if="form.errors.description" class="text-red-400 text-xs mt-1">{{ form.errors.description }}</div>
                        </div>
                    </div>

                    <div class="cc-card p-5 space-y-5">
                        <p class="cc-section-label">{{ $t('routes.difficulty_label') }}</p>
                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">{{ $t('routes.difficulty_label') }}</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button type="button" @click="form.difficulty = 'easy'" :class="form.difficulty === 'easy' ? 'bg-green-500/20 border-green-500 text-green-400' : 'bg-white/[0.04] border-white/[0.08] text-gray-500'" class="rounded-xl border py-2 px-1 text-xs font-medium transition hover:border-white/[0.15]">{{ $t('routes.difficulty_easy') }}</button>
                                    <button type="button" @click="form.difficulty = 'medium'" :class="form.difficulty === 'medium' ? 'bg-yellow-500/20 border-yellow-500 text-yellow-400' : 'bg-white/[0.04] border-white/[0.08] text-gray-500'" class="rounded-xl border py-2 px-1 text-xs font-medium transition hover:border-white/[0.15]">{{ $t('routes.difficulty_medium') }}</button>
                                    <button type="button" @click="form.difficulty = 'hard'" :class="form.difficulty === 'hard' ? 'bg-red-500/20 border-red-500 text-red-400' : 'bg-white/[0.04] border-white/[0.08] text-gray-500'" class="rounded-xl border py-2 px-1 text-xs font-medium transition hover:border-white/[0.15]">{{ $t('routes.difficulty_hard') }}</button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">{{ $t('routes.visibility_label') }}</label>
                                <div class="flex items-center gap-2 bg-white/[0.04] p-1.5 rounded-xl border border-white/[0.08] h-[42px]">
                                    <button type="button" @click="form.is_public = true" class="flex-1 py-1.5 rounded-lg text-xs font-medium transition flex items-center justify-center gap-1.5" :class="form.is_public ? 'bg-brand-neon text-brand-black' : 'text-gray-500 hover:text-white'">
                                        <AppIcon name="globe" size="xs" />
                                        {{ $t('routes.public_badge') }}
                                    </button>
                                    <button type="button" @click="form.is_public = false" class="flex-1 py-1.5 rounded-lg text-xs font-medium transition flex items-center justify-center gap-1.5" :class="!form.is_public ? 'bg-white/[0.1] text-white' : 'text-gray-500 hover:text-white'">
                                        <AppIcon name="lock" size="xs" />
                                        {{ $t('routes.private_badge') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-if="form.errors.motorcycle_id || form.errors.difficulty || form.errors.is_public" class="text-red-400 text-xs">
                            {{ $t('routes.invalid_selections') }}
                        </div>
                        <div v-if="form.errors.planned_distance_km || form.errors.duration_seconds || form.errors.geo_json || form.errors.waypoints" class="text-red-400 text-xs">
                            {{ $t('routes.invalid_route_draw') }}
                        </div>
                    </div>

                    <div class="cc-card p-5 space-y-4">
                        <p class="cc-section-label">{{ $t('routes.route_photo') }}</p>
                        <input @change="e => form.photo = e.target.files[0]" type="file" accept="image/*" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-white/[0.06] file:text-white hover:file:bg-white/[0.1] transition cursor-pointer">
                        <div v-if="form.errors.photo" class="text-red-400 text-xs">{{ form.errors.photo }}</div>
                    </div>

                    <div class="cc-card p-5 space-y-5">
                        <div class="flex items-center justify-between">
                            <p class="cc-section-label">{{ $t('routes.route_trace') }}</p>
                            <span v-if="uiWaypoints.length > 0" class="cc-chip-neutral">{{ $t('routes.points_defined', { n: uiWaypoints.length }) }}</span>
                        </div>
                        <div v-if="form.planned_distance_km > 0" class="grid grid-cols-2 gap-4">
                            <div class="bg-white/[0.04] p-3 rounded-xl border border-white/[0.08] text-center">
                                <span class="block text-2xl font-mono font-semibold text-white tracking-tight">{{ form.planned_distance_km }}</span>
                                <span class="text-xs text-gray-500">{{ $t('routes.km_label') }}</span>
                            </div>
                            <div class="bg-white/[0.04] p-3 rounded-xl border border-white/[0.08] text-center">
                                <span class="block text-2xl font-mono font-semibold text-white tracking-tight">{{ formattedDuration }}</span>
                                <span class="text-xs text-gray-500">{{ $t('routes.estimated_time') }}</span>
                            </div>
                        </div>
                        <button type="button" @click="openMap" class="w-full group relative overflow-hidden rounded-xl bg-white/[0.04] border border-white/[0.08] hover:border-white/[0.15] transition-all duration-200 h-32 flex flex-col items-center justify-center gap-2">
                            <div class="relative z-10 bg-white/[0.08] text-white p-3 rounded-full group-hover:bg-white/[0.12] transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                            </div>
                            <span class="relative z-10 text-gray-300 font-medium text-sm group-hover:text-white transition-colors">
                                {{ uiWaypoints.length > 0 ? $t('routes.edit_map') : $t('routes.edit_map') }}
                            </span>
                        </button>
                    </div>

                    <button type="submit" :disabled="form.processing || uiWaypoints.length < 2" class="cc-btn-primary w-full">
                        {{ $t('routes.save_route') }}
                    </button>
                </form>
            </div>

            <div v-show="isMapOpen" class="fixed inset-0 z-[5000] bg-gray-900 flex flex-col">

                <div id="map" class="absolute inset-0 w-full h-full z-0"></div>

                <div class="absolute top-3 left-0 w-full z-[5010] p-4 pt-safe-top pointer-events-none">
                    <div class="pointer-events-auto bg-black/80 backdrop-blur-xl border border-white/[0.08] rounded-2xl p-2 flex flex-col gap-2 max-w-2xl mx-auto">

                        <div class="flex items-center gap-2">
                            <button type="button" @click="closeMap" class="cc-icon-btn bg-black/50 backdrop-blur-md border-white/20 text-white flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                            </button>
                            <div class="h-8 w-px bg-white/10 mx-1"></div>

                            <div class="flex-1 relative group">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg v-if="isSearching" class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <svg v-else class="w-4 h-4 text-gray-500 group-focus-within:text-white transition" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/></svg>
                                </div>
                                <input v-model="searchQuery" @input="handleSearchInput" type="text" class="block w-full py-2.5 pl-10 pr-3 text-sm text-white bg-transparent border-none focus:ring-0 placeholder-gray-600" :placeholder="$t('routes.add_stop')" autocomplete="off">
                            </div>
                        </div>

                        <div v-if="searchResults.length > 0" class="border-t border-white/[0.08] pt-2">
                            <ul class="max-h-40 overflow-y-auto">
                                <li v-for="(result, index) in searchResults" :key="index" @click="selectSearchResult(result)" class="p-2 hover:bg-white/[0.06] rounded-lg cursor-pointer flex items-center gap-3 transition">
                                    <div class="bg-white/[0.08] p-1.5 rounded-full text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-white font-medium truncate">{{ result.display_name.split(',')[0] }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ result.display_name }}</p>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div v-if="uiWaypoints.length > 0 && searchResults.length === 0" class="border-t border-white/[0.08] pt-2 mt-1">
                            <div class="text-xs text-gray-500 font-medium mb-2 pl-1">{{ $t('routes.route_order') }}</div>

                            <draggable
                                v-model="uiWaypoints"
                                item-key="id"
                                @end="onDragEnd"
                                handle=".drag-handle"
                                class="flex flex-col gap-2 max-h-40 overflow-y-auto"
                            >
                                <template #item="{element, index}">
                                    <div class="flex items-center gap-2 bg-white/[0.04] p-2 rounded-lg border border-white/[0.08] text-sm group">
                                        <div class="drag-handle text-gray-500 cursor-grab hover:text-white p-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                                        </div>

                                        <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-semibold flex-shrink-0" :class="index === 0 ? 'bg-green-500 text-black' : (index === uiWaypoints.length -1 ? 'bg-red-500 text-white' : 'bg-white/[0.15] text-white')">
                                            {{ index + 1 }}
                                        </div>

                                        <div class="flex-1 truncate text-gray-300">
                                            {{ element.name || `Punt ${index + 1}` }}
                                        </div>

                                        <button type="button" @click="removeWaypoint(index)" class="text-gray-600 hover:text-red-400 p-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                </template>
                            </draggable>
                        </div>

                    </div>
                </div>

                <div class="absolute bottom-3 left-0 w-full z-[5010] px-4 flex items-end justify-between pointer-events-none pb-safe-bottom">
                    <div class="pointer-events-auto bg-black/80 backdrop-blur-md border border-white/[0.08] rounded-xl p-3">
                        <div class="text-xs text-gray-400 font-medium mb-1">{{ $t('routes.total_route') }}</div>
                        <div class="flex items-baseline gap-1">
                            <span class="text-2xl font-mono font-semibold text-white tracking-tight">{{ form.planned_distance_km }}</span>
                            <span class="text-xs text-gray-500">km</span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3 pointer-events-auto">
                        <button type="button" @click="locateUser" class="cc-icon-btn bg-black/50 backdrop-blur-md border-white/20 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 transform rotate-45"><path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" /></svg>
                        </button>
                        <button type="button" @click="closeMap" class="cc-icon-btn-accent">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 01.208 1.04l-9 13.5a.75.75 0 01-1.154.114l-6-6a.75.75 0 011.06-1.06l5.353 5.353 8.493-12.739a.75.75 0 011.04-.208z" clip-rule="evenodd" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed, nextTick } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import { Geolocation } from '@capacitor/geolocation';
import AppLayout from '@/Layouts/AppLayout.vue';
import AppIcon from '@/Components/AppIcon.vue';
import 'leaflet/dist/leaflet.css';
import 'leaflet-routing-machine/dist/leaflet-routing-machine.css';
import L from 'leaflet';
import 'leaflet-routing-machine';
import draggable from 'vuedraggable';
import { smartBack } from '@/Composables/navigationStack.js';
import { addMapTileLayer } from '@/config/mapTiles.js';
import { parseLatLngPath } from '@/services/routeGeometry.js';

const props = defineProps({
    motorcycles: Array,
    categories: Array
});

const goBack = () => smartBack(route('routes.index'));

const form = useForm({
    title: '',
    description: '',
    difficulty: 'medium',
    motorcycle_id: null,
    category_id: null,
    planned_distance_km: 0,
    duration_seconds: 0,
    geo_json: null,
    waypoints: [],
    is_public: false,
    photo: null
});

const map = ref(null);
const routingControl = ref(null);
const duration = ref(0);
const isMapOpen = ref(false);
const userLocationMarker = ref(null);
const uiWaypoints = ref([]);
const isPlannedRoute = ref(false);
const plannedRouteLayer = ref(null);
const plannedRouteMarkers = ref([]); 

const searchQuery = ref('');
const searchResults = ref([]);
const isSearching = ref(false);
let searchTimeout = null;

const formattedDuration = computed(() => {
    if (!duration.value) return '0h 0m';
    const h = Math.floor(duration.value / 3600);
    const m = Math.floor((duration.value % 3600) / 60);
    return `${h}h ${m}m`;
});

const addPointToMap = (latlng, name = '') => {
    if (isPlannedRoute.value) {
        clearPlannedRouteDisplay();
        isPlannedRoute.value = false;
    }
    uiWaypoints.value.push({
        id: Date.now() + Math.random(),
        lat: latlng.lat,
        lng: latlng.lng,
        name: name || `Punt`
    });
    syncWaypointsToMap();
};

const removeWaypoint = (index) => {
    if (isPlannedRoute.value) {
        clearPlannedRouteDisplay();
        isPlannedRoute.value = false;
    }
    uiWaypoints.value.splice(index, 1);
    syncWaypointsToMap();
};

const onDragEnd = () => {
    if (isPlannedRoute.value) {
        clearPlannedRouteDisplay();
        isPlannedRoute.value = false;
    }
    syncWaypointsToMap();
};

const clearPlannedRouteDisplay = () => {
    if (plannedRouteLayer.value && map.value) {
        map.value.removeLayer(plannedRouteLayer.value);
        plannedRouteLayer.value = null;
    }
    plannedRouteMarkers.value.forEach((marker) => map.value?.removeLayer(marker));
    plannedRouteMarkers.value = [];
};

const syncFormWaypoints = () => {
    form.waypoints = uiWaypoints.value.map((wp) => ({
        lat: wp.lat,
        lng: wp.lng,
        name: wp.name,
    }));
};

const drawPlannedRoute = () => {
    if (!map.value || !form.geo_json) return;

    clearPlannedRouteDisplay();
    routingControl.value?.setWaypoints([]);

    const latLngs = parseLatLngPath(form.geo_json);
    if (!latLngs.length) return;

    const line = latLngs.map((point) => [point.lat, point.lng]);
    plannedRouteLayer.value = L.polyline(line, {
        color: '#fafafa',
        weight: 6,
        opacity: 0.9,
    }).addTo(map.value);

    uiWaypoints.value.forEach((wp, index) => {
        const marker = L.circleMarker([wp.lat, wp.lng], {
            radius: index === 0 ? 7 : 5,
            color: '#0a0a0a',
            fillColor: '#fafafa',
            weight: index === 0 ? 3 : 2,
            fillOpacity: 1,
        }).addTo(map.value);
        plannedRouteMarkers.value.push(marker);
    });

    try {
        map.value.fitBounds(plannedRouteLayer.value.getBounds(), { padding: [48, 48] });
    } catch {
        // ignore invalid bounds
    }

    syncFormWaypoints();
};

const syncWaypointsToMap = () => {
    if (!routingControl.value) return;
    const leafletPoints = uiWaypoints.value.map(wp => L.latLng(wp.lat, wp.lng));
    routingControl.value.setWaypoints(leafletPoints);
};

const handleSearchInput = () => {
    if (searchTimeout) clearTimeout(searchTimeout);
    if (searchQuery.value.length < 3) {
        searchResults.value = [];
        return;
    }
    isSearching.value = true;
    searchTimeout = setTimeout(async () => {
        try {
            let viewboxParams = '';
            if (map.value) {
                const bounds = map.value.getBounds();
                viewboxParams = `&viewbox=${bounds.getWest()},${bounds.getNorth()},${bounds.getEast()},${bounds.getSouth()}`;
            }
            const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchQuery.value)}&addressdetails=1&limit=5${viewboxParams}&bounded=0`;
            const response = await fetch(url);
            searchResults.value = await response.json();
        } catch (error) {
            console.error("Error buscant:", error);
        } finally {
            isSearching.value = false;
        }
    }, 500);
};

const selectSearchResult = (result) => {
    const lat = parseFloat(result.lat);
    const lon = parseFloat(result.lon);
    const latlng = L.latLng(lat, lon);
    map.value.flyTo(latlng, 16);
    addPointToMap(latlng, result.display_name.split(',')[0]);
    searchQuery.value = '';
    searchResults.value = [];
};

const openMap = async () => {
    isMapOpen.value = true;
    await nextTick();
    if (map.value) {
        map.value.invalidateSize();
        if (isPlannedRoute.value && form.geo_json) {
            drawPlannedRoute();
        }
    }
};

const closeMap = () => {
    isMapOpen.value = false;
};

const locateUser = async () => {
    try {
        const permStatus = await Geolocation.checkPermissions();
        if (permStatus.location !== 'granted') {
            await Geolocation.requestPermissions();
        }

        const pos = await Geolocation.getCurrentPosition({ enableHighAccuracy: true });
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        map.value.flyTo([lat, lng], 15);
        if (uiWaypoints.value.length === 0) {
             addPointToMap(L.latLng(lat, lng), "La meva ubicació");
        }
        if (userLocationMarker.value) map.value.removeLayer(userLocationMarker.value);
        userLocationMarker.value = L.circleMarker([lat, lng], {
            radius: 8, fillColor: '#3b82f6', color: '#ffffff', weight: 2, opacity: 1, fillOpacity: 1
        }).addTo(map.value);
    } catch (err) {
        console.warn("No s'ha pogut localitzar:", err);
    }
};

const undoPoint = () => {
    if (!routingControl.value) return;
    const currentWps = routingControl.value.getWaypoints();
    const validWps = currentWps.filter(wp => wp.latLng);
    if (validWps.length > 0) {
        validWps.pop();
        routingControl.value.setWaypoints(validWps);
    }
};

const loadPlannedDraft = () => {
    const raw = sessionStorage.getItem('clutch_planned_route');
    if (!raw) return;

    try {
        const draft = JSON.parse(raw);
        sessionStorage.removeItem('clutch_planned_route');

        if (draft.title) form.title = draft.title;
        if (draft.planned_distance_km) form.planned_distance_km = draft.planned_distance_km;
        if (draft.duration_seconds) {
            form.duration_seconds = draft.duration_seconds;
            duration.value = draft.duration_seconds;
        }
        if (draft.geo_json) form.geo_json = draft.geo_json;
        if (draft.is_planned_route) isPlannedRoute.value = true;
        if (Array.isArray(draft.waypoints) && draft.waypoints.length) {
            uiWaypoints.value = draft.waypoints.map((wp, index) => ({
                id: Date.now() + index,
                lat: wp.lat,
                lng: wp.lng,
                name: wp.name || `Punt ${index + 1}`,
            }));
            syncFormWaypoints();
        }
    } catch (e) {
        console.error('Error carregant esborrany de ruta planificada:', e);
    }
};

onMounted(() => {
    loadPlannedDraft();

    map.value = L.map('map', { zoomControl: false, attributionControl: false }).setView([41.3851, 2.1734], 13);
    
    addMapTileLayer(map.value, L, { keepBuffer: 100, updateWhenIdle: false });

    routingControl.value = L.Routing.control({
        waypoints: [],
        routeWhileDragging: true,
        show: false,
        addWaypoints: false,
        draggableWaypoints: true,
        fitSelectedRoutes: true,
        lineOptions: { styles: [{ color: '#fafafa', opacity: 0.9, weight: 6 }] },
        createMarker: function(i, wp, nWps) {
            return L.marker(wp.latLng, {
                draggable: true,
                icon: L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div style="background-color: ${i === 0 ? '#fafafa' : '#0a0a0a'}; width: 12px; height: 12px; border-radius: 50%; border: 2px solid #fafafa;"></div>`,
                    iconSize: [12, 12]
                })
            });
        }
    }).addTo(map.value);

    const container = document.querySelector('.leaflet-routing-container');
    if (container) container.style.display = 'none';

    map.value.on('click', (e) => {
        addPointToMap(e.latlng);
    });

    routingControl.value.on('routesfound', function(e) {
        if (isPlannedRoute.value) return;

        const routes = e.routes;
        const summary = routes[0].summary;
        form.planned_distance_km = (summary.totalDistance / 1000).toFixed(1);
        duration.value = summary.totalTime;
        form.duration_seconds = Math.round(summary.totalTime);
        form.geo_json = JSON.stringify(routes[0].coordinates);
        const wps = routingControl.value.getWaypoints().filter(wp => wp.latLng);
        form.waypoints = wps.map(wp => ({ lat: wp.latLng.lat, lng: wp.latLng.lng }));
    });
    
    // Check if recorded route exists in localstorage
    const recordedRouteData = localStorage.getItem('clutch_recorded_route');
    if (recordedRouteData) {
        try {
            const recordedPoints = JSON.parse(recordedRouteData);
            if (Array.isArray(recordedPoints) && recordedPoints.length > 0) {
                uiWaypoints.value = recordedPoints;
                syncWaypointsToMap();
            }
        } catch (e) {
            console.error("Error parsing stored route:", e);
        }
        localStorage.removeItem('clutch_recorded_route');
    } else if (isPlannedRoute.value && form.geo_json) {
        drawPlannedRoute();
    } else if (uiWaypoints.value.length === 0) {
        locateUser();
    } else {
        syncWaypointsToMap();
    }
});

const submit = () => {
    if (isPlannedRoute.value) {
        syncFormWaypoints();
    }

    form.transform(data => ({
        ...data,
        geo_json: typeof data.geo_json === 'string' ? data.geo_json : JSON.stringify(data.geo_json)
    })).post(route('routes.store'), { forceFormData: true });
};
</script>

<style>
.pt-safe-top { padding-top: env(safe-area-inset-top, 40px); }
.pb-safe-bottom { padding-bottom: env(safe-area-inset-bottom, 20px); }
.leaflet-routing-container { display: none !important; }
</style>

<template>
    <AppLayout title="Editar Ruta">
        <div class="max-w-4xl mx-auto px-4 py-6 pb-24">
            
            <div v-show="!isMapOpen">
                
                <div class="flex items-center justify-between mb-8">
                    <Link :href="route('routes.index')" class="text-gray-500 hover:text-white flex items-center gap-1 text-sm">
                        &larr; Cancel·lar
                    </Link>
                    <h1 class="text-xl font-black text-white uppercase tracking-tighter">Editar <span class="text-brand-neon">Ruta</span></h1>
                </div>

                <form @submit.prevent="submit" class="space-y-8">
                    
                    <div class="bg-brand-surface p-6 rounded-xl border border-brand-dark shadow-lg space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Nom de la Ruta</label>
                                <input v-model="form.title" type="text" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0">
                                <div v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Moto</label>
                                <select v-model="form.motorcycle_id" class="w-full bg-brand-black border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon">
                                    <option :value="null">-- Selecciona Moto --</option>
                                    <option v-for="moto in motorcycles" :key="moto.id" :value="moto.id">{{ moto.alias || moto.model }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Tipus de Ruta</label>
                                <select v-model="form.category_id" class="w-full bg-brand-black border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon">
                                    <option :value="null">-- Selecciona Categoria --</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Descripció</label>
                            <textarea v-model="form.description" rows="3" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0"></textarea>
                            <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                        </div>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Dificultat</label>
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-2 h-auto">
                                    <button type="button" @click="form.difficulty = 'easy'" :class="form.difficulty === 'easy' ? 'bg-green-500/20 border-green-500 text-green-400' : 'bg-brand-black border-brand-dark text-gray-500'" class="rounded-lg border py-2 px-1 text-[10px] sm:text-xs font-bold transition hover:border-gray-500">🟢 Fàcil</button>
                                    <button type="button" @click="form.difficulty = 'medium'" :class="form.difficulty === 'medium' ? 'bg-yellow-500/20 border-yellow-500 text-yellow-400' : 'bg-brand-black border-brand-dark text-gray-500'" class="rounded-lg border py-2 px-1 text-[10px] sm:text-xs font-bold transition hover:border-gray-500">🟡 Mitjana</button>
                                    <button type="button" @click="form.difficulty = 'hard'" :class="form.difficulty === 'hard' ? 'bg-red-500/20 border-red-500 text-red-400' : 'bg-brand-black border-brand-dark text-gray-500'" class="rounded-lg border py-2 px-1 text-[10px] sm:text-xs font-bold transition hover:border-gray-500">🔴 Difícil</button>
                                    <button type="button" @click="form.difficulty = 'expert'" :class="form.difficulty === 'expert' ? 'bg-purple-500/20 border-purple-500 text-purple-400' : 'bg-brand-black border-brand-dark text-gray-500'" class="rounded-lg border py-2 px-1 text-[10px] sm:text-xs font-bold transition hover:border-gray-500">🟣 Experta</button>
                                    <button type="button" @click="form.difficulty = 'extreme'" :class="form.difficulty === 'extreme' ? 'bg-gray-800 border-gray-500 text-white' : 'bg-brand-black border-brand-dark text-gray-500'" class="rounded-lg border py-2 px-1 text-[10px] sm:text-xs font-bold transition hover:border-gray-500">☠️ Extrema</button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Visibilitat</label>
                                <div class="flex items-center gap-3 bg-brand-black p-2 rounded-lg border border-brand-dark h-[42px]">
                                    <button type="button" @click="form.is_public = true" class="flex-1 py-1 rounded text-[10px] sm:text-xs font-bold uppercase transition" :class="form.is_public ? 'bg-brand-neon text-black' : 'text-gray-500 hover:text-white'">
                                        🌍 Pública
                                    </button>
                                    <button type="button" @click="form.is_public = false" class="flex-1 py-1 rounded text-[10px] sm:text-xs font-bold uppercase transition" :class="!form.is_public ? 'bg-gray-600 text-white' : 'text-gray-500 hover:text-white'">
                                        🔒 Privada
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-if="form.errors.motorcycle_id || form.errors.difficulty || form.errors.is_public" class="text-red-500 text-xs mt-1">
                            Valors de selecció incorrectes.
                        </div>
                        <div v-if="form.errors.planned_distance_km || form.errors.duration_seconds || form.errors.geo_json || form.errors.waypoints" class="text-red-500 text-xs mt-1">
                            Falta dibuixar la ruta correctament al mapa. (KMs o waypoints invàlids)
                        </div>
                    </div>

                    <div class="bg-brand-surface p-4 rounded-xl border border-brand-dark">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Foto de la Ruta</label>
                        <div v-if="mapRoute.photo" class="mb-3">
                            <img :src="$page.props.storageUrl + '/' + mapRoute.photo" alt="Foto Ruta" class="h-32 w-full object-cover rounded-xl border border-brand-dark">
                        </div>
                        <input @change="e => form.photo = e.target.files[0]" type="file" accept="image/*" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-brand-base/20 file:text-brand-neon hover:file:bg-brand-base/30 transition cursor-pointer">
                        <div v-if="form.errors.photo" class="text-red-500 text-xs mt-1">{{ form.errors.photo }}</div>
                    </div>

                    <div class="bg-brand-surface p-6 rounded-xl border border-brand-dark shadow-lg">
                        <div class="flex items-center justify-between mb-4">
                            <label class="text-xs font-bold text-gray-400 uppercase">Traçat de la Ruta</label>
                            <span v-if="uiWaypoints.length > 0" class="text-xs text-brand-neon font-bold">✅ {{ uiWaypoints.length }} punts definits</span>
                        </div>
                        <div v-if="form.planned_distance_km > 0" class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-brand-black p-3 rounded-lg border border-brand-dark text-center">
                                <span class="block text-2xl font-mono font-bold text-brand-neon">{{ form.planned_distance_km }}</span>
                                <span class="text-[10px] text-gray-500 uppercase">Quilòmetres</span>
                            </div>
                            <div class="bg-brand-black p-3 rounded-lg border border-brand-dark text-center">
                                <span class="block text-2xl font-mono font-bold text-white">{{ formattedDuration }}</span>
                                <span class="text-[10px] text-gray-500 uppercase">Temps Estimat</span>
                            </div>
                        </div>
                        <button type="button" @click="openMap" class="w-full group relative overflow-hidden rounded-xl bg-brand-black border border-brand-dark hover:border-brand-neon transition-all duration-300 h-32 flex flex-col items-center justify-center gap-2">
                            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                            <div class="relative z-10 bg-brand-neon text-brand-black p-3 rounded-full shadow-[0_0_20px_rgba(12,225,181,0.4)] group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                            </div>
                            <span class="relative z-10 text-brand-neon font-bold uppercase tracking-widest text-sm group-hover:text-white transition-colors">
                                Editar Mapa
                            </span>
                        </button>
                    </div>

                    <button type="submit" :disabled="form.processing || uiWaypoints.length < 2" class="w-full bg-white text-black font-black py-4 rounded-xl uppercase tracking-widest hover:bg-gray-200 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-xl">
                        Guardar Canvis
                    </button>
                </form>
            </div>

            <div v-show="isMapOpen" class="fixed inset-0 z-[5000] bg-gray-900 flex flex-col">
                <div id="map" class="absolute inset-0 w-full h-full z-0"></div>

                <div class="absolute top-6 left-0 w-full z-[5010] p-4 pt-safe-top pointer-events-none">
                    <div class="pointer-events-auto bg-brand-black/95 backdrop-blur-xl border border-brand-dark/50 rounded-2xl shadow-2xl p-2 flex flex-col gap-2 max-w-2xl mx-auto">
                        <div class="flex items-center gap-2">
                            <button @click="closeMap" class="p-3 text-gray-400 hover:text-white hover:bg-white/10 rounded-xl transition flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                            </button>
                            <div class="h-8 w-px bg-white/10 mx-1"></div>
                            
                            <div class="flex-1 relative group">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg v-if="isSearching" class="animate-spin w-4 h-4 text-brand-neon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <svg v-else class="w-4 h-4 text-gray-500 group-focus-within:text-brand-neon transition" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/></svg>
                                </div>
                                <input v-model="searchQuery" @input="handleSearchInput" type="text" class="block w-full py-2.5 pl-10 pr-3 text-sm text-white bg-transparent border-none focus:ring-0 placeholder-gray-600" placeholder="Afegeix una parada..." autocomplete="off">
                            </div>
                            
                        </div>

                        <div v-if="searchResults.length > 0" class="border-t border-brand-dark/50 pt-2">
                            <ul class="max-h-40 overflow-y-auto">
                                <li v-for="(result, index) in searchResults" :key="index" @click="selectSearchResult(result)" class="p-2 hover:bg-brand-neon/20 rounded-lg cursor-pointer flex items-center gap-3 transition">
                                    <div class="bg-gray-800 p-1.5 rounded-full text-brand-neon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg></div>
                                    <div class="flex-1 min-w-0"><p class="text-sm text-white font-medium truncate">{{ result.display_name.split(',')[0] }}</p><p class="text-xs text-gray-500 truncate">{{ result.display_name }}</p></div>
                                </li>
                            </ul>
                        </div>

                        <div v-if="uiWaypoints.length > 0 && searchResults.length === 0" class="border-t border-brand-dark/50 pt-2 mt-1">
                             <div class="text-[10px] text-gray-500 uppercase font-bold mb-2 pl-1">Ordre de la Ruta</div>
                             <draggable v-model="uiWaypoints" item-key="id" @end="onDragEnd" handle=".drag-handle" class="flex flex-col gap-2 max-h-40 overflow-y-auto">
                                <template #item="{element, index}">
                                    <div class="flex items-center gap-2 bg-brand-black p-2 rounded-lg border border-gray-800 text-sm group">
                                        <div class="drag-handle text-gray-500 cursor-grab hover:text-white p-1"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg></div>
                                        <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0" :class="index === 0 ? 'bg-green-500 text-black' : (index === uiWaypoints.length -1 ? 'bg-red-500 text-white' : 'bg-brand-neon text-black')">{{ index + 1 }}</div>
                                        <div class="flex-1 truncate text-gray-300">{{ element.name || `Punt ${index + 1}` }}</div>
                                        <button @click="removeWaypoint(index)" class="text-gray-600 hover:text-red-500 p-1"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
                                    </div>
                                </template>
                            </draggable>
                        </div>
                    </div>
                </div>

                <div class="absolute bottom-8 left-0 w-full z-[5010] px-4 flex items-end justify-between pointer-events-none pb-safe-bottom">
                    <div class="pointer-events-auto bg-brand-black/90 backdrop-blur-md border border-brand-dark rounded-xl p-3 shadow-lg">
                        <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold mb-1">Total Ruta</div>
                        <div class="flex items-baseline gap-1">
                            <span class="text-2xl font-mono font-bold text-brand-neon">{{ form.planned_distance_km }}</span>
                            <span class="text-xs text-gray-500">km</span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3 pointer-events-auto">
                        <button @click="locateUser" class="bg-brand-neon text-brand-black p-3 rounded-full shadow-[0_0_15px_rgba(12,225,181,0.5)] hover:scale-110 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 transform rotate-45"><path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" /></svg>
                        </button>
                        <button @click="closeMap" class="bg-brand-neon text-brand-black p-3 rounded-full shadow-[0_0_15px_rgba(12,225,181,0.5)] hover:scale-110 transition flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 01.208 1.04l-9 13.5a.75.75 0 01-1.154.114l-6-6a.75.75 0 011.06-1.06l5.353 5.353 8.493-12.739a.75.75 0 011.04-.208z" clip-rule="evenodd" /></svg>
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
import AppLayout from '@/Layouts/AppLayout.vue';
import 'leaflet/dist/leaflet.css';
import 'leaflet-routing-machine/dist/leaflet-routing-machine.css';
import L from 'leaflet';
import 'leaflet-routing-machine';
import draggable from 'vuedraggable';

const props = defineProps({
    motorcycles: Array,
    categories: Array,
    mapRoute: Object // <--- CANVI: Ara es diu mapRoute per no xocar amb la funció route()
});

// Inicialitzem el form amb les dades de mapRoute I ELS PUNTS!
const form = useForm({
    title: props.mapRoute.title || '',
    description: props.mapRoute.description || '',
    difficulty: props.mapRoute.difficulty || 'medium',
    motorcycle_id: props.mapRoute.motorcycle_id || null,
    category_id: props.mapRoute.category_id || null,
    planned_distance_km: props.mapRoute.planned_distance_km || 0,
    duration_seconds: props.mapRoute.duration_seconds || 0,
    geo_json: props.mapRoute.geo_json || null,
    is_public: Boolean(props.mapRoute.is_public),
    photo: null,
    waypoints: props.mapRoute.waypoints ? props.mapRoute.waypoints.map(wp => ({
        lat: parseFloat(wp.lat || wp.latitude),
        lng: parseFloat(wp.lng || wp.longitude)
    })) : []
});

const map = ref(null);
const routingControl = ref(null);
const duration = ref(form.duration_seconds);
const isMapOpen = ref(false);
const userLocationMarker = ref(null);
const uiWaypoints = ref([]); 

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
    uiWaypoints.value.push({
        id: Date.now() + Math.random(),
        lat: latlng.lat,
        lng: latlng.lng,
        name: name || `Punt`
    });
    syncWaypointsToMap();
};

const removeWaypoint = (index) => {
    uiWaypoints.value.splice(index, 1);
    syncWaypointsToMap();
};

const onDragEnd = () => {
    syncWaypointsToMap();
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
        // 1. Arreglem el problema de "mapa gris"
        map.value.invalidateSize();

        // 2. FIX: Si tenim punts, fem zoom perquè es vegi tota la ruta
        if (uiWaypoints.value.length > 0) {
            const group = L.latLngBounds(uiWaypoints.value.map(wp => [wp.lat, wp.lng]));
            map.value.fitBounds(group, { padding: [50, 50] });
        }
    }
};

const closeMap = () => {
    isMapOpen.value = false;
};

const locateUser = () => {
    if (!navigator.geolocation) return;
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            map.value.flyTo([lat, lng], 15);
            if (uiWaypoints.value.length === 0) addPointToMap(L.latLng(lat, lng), "La meva ubicació");
            if (userLocationMarker.value) map.value.removeLayer(userLocationMarker.value);
            userLocationMarker.value = L.circleMarker([lat, lng], { radius: 8, fillColor: '#3b82f6', color: '#ffffff', weight: 2, opacity: 1, fillOpacity: 1 }).addTo(map.value);
        },
        () => {}, { enableHighAccuracy: true }
    );
};

onMounted(() => {
    // 1. Inicialitzar Mapa
    let startLat = 41.3851;
    let startLng = 2.1734;
    
    // --- ÚNIC CANVI IMPORTANT AQUÍ BAIX (props.mapRoute) ---
    if (props.mapRoute.waypoints && props.mapRoute.waypoints.length > 0) {
        startLat = parseFloat(props.mapRoute.waypoints[0].lat || props.mapRoute.waypoints[0].latitude);
        startLng = parseFloat(props.mapRoute.waypoints[0].lng || props.mapRoute.waypoints[0].longitude);
        
        uiWaypoints.value = props.mapRoute.waypoints.map((wp, i) => ({
            id: Date.now() + i,
            lat: parseFloat(wp.lat || wp.latitude),
            lng: parseFloat(wp.lng || wp.longitude),
            name: `Punt ${i + 1}`
        }));
    }

    map.value = L.map('map', { zoomControl: false, attributionControl: false }).setView([startLat, startLng], 13);
    
    L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
        maxZoom: 20,
        className: 'map-tiles-inverse',
        keepBuffer: 100,
        updateWhenIdle: false
    }).addTo(map.value);

    const initialWaypoints = uiWaypoints.value.map(wp => L.latLng(wp.lat, wp.lng));

    routingControl.value = L.Routing.control({
        waypoints: initialWaypoints,
        routeWhileDragging: true,
        show: false,
        addWaypoints: false,
        draggableWaypoints: true,
        fitSelectedRoutes: true,
        lineOptions: { styles: [{ color: '#0CE1B5', opacity: 0.8, weight: 6 }] },
        createMarker: function(i, wp, nWps) {
            return L.marker(wp.latLng, {
                draggable: true,
                icon: L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div style="background-color: ${i === 0 ? 'white' : '#0CE1B5'}; width: 12px; height: 12px; border-radius: 50%; box-shadow: 0 0 10px ${i === 0 ? 'white' : '#0CE1B5'}; border: 2px solid white;"></div>`,
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
        const routes = e.routes;
        const summary = routes[0].summary;
        form.planned_distance_km = (summary.totalDistance / 1000).toFixed(1);
        duration.value = summary.totalTime;
        form.duration_seconds = Math.round(summary.totalTime);
        form.geo_json = JSON.stringify(routes[0].coordinates); 
        const wps = routingControl.value.getWaypoints().filter(wp => wp.latLng);
        form.waypoints = wps.map(wp => ({ lat: wp.latLng.lat, lng: wp.latLng.lng }));
    });
});

const submit = () => {
    // form.put() no funciona amb fitxers (forceFormData). 
    // La solució és posar _method al useForm i fer post().
    form.transform(data => ({ ...data, _method: 'put' }))
        .post(route('routes.update', props.mapRoute.id), { forceFormData: true });
};
</script>

<style>
.pt-safe-top { padding-top: env(safe-area-inset-top, 20px); }
.pb-safe-bottom { padding-bottom: env(safe-area-inset-bottom, 20px); }
.leaflet-routing-container { display: none !important; }
.map-tiles-inverse { filter: brightness(150%) contrast(150%); }
</style>

<template>
    <AppLayout title="Nova Ruta">
        <div class="max-w-4xl mx-auto px-4 py-6 pb-24">
            
            <div v-show="!isMapOpen">
                
                <div class="flex items-center justify-between mb-8">
                    <Link :href="route('routes.index')" class="text-gray-500 hover:text-white flex items-center gap-1 text-sm">
                        &larr; Cancel·lar
                    </Link>
                    <h1 class="text-xl font-black text-white uppercase tracking-tighter">Nova <span class="text-brand-neon">Ruta</span></h1>
                </div>

                <form @submit.prevent="submit" class="space-y-8">
                    
                    <div class="bg-brand-surface p-6 rounded-xl border border-brand-dark shadow-lg space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Nom de la Ruta</label>
                                <input v-model="form.title" type="text" placeholder="Ex: Collada de Toses" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0">
                                <div v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Moto</label>
                                <select v-model="form.motorcycle_id" class="w-full bg-brand-black border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon">
                                    <option :value="null">-- Selecciona Moto --</option>
                                    <option v-for="moto in motorcycles" :key="moto.id" :value="moto.id">{{ moto.alias || moto.model }}</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Descripció</label>
                            <textarea v-model="form.description" rows="3" placeholder="Explica detalls de la ruta, estat de la carretera..." class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Dificultat</label>
                            <div class="grid grid-cols-3 gap-3">
                                <button type="button" @click="form.difficulty = 'easy'" :class="form.difficulty === 'easy' ? 'bg-green-500/20 border-green-500 text-green-400' : 'bg-brand-black border-brand-dark text-gray-500'" class="p-3 rounded-lg border text-sm font-bold transition hover:border-gray-500">🟢 Fàcil</button>
                                <button type="button" @click="form.difficulty = 'medium'" :class="form.difficulty === 'medium' ? 'bg-yellow-500/20 border-yellow-500 text-yellow-400' : 'bg-brand-black border-brand-dark text-gray-500'" class="p-3 rounded-lg border text-sm font-bold transition hover:border-gray-500">🟡 Mitjana</button>
                                <button type="button" @click="form.difficulty = 'hard'" :class="form.difficulty === 'hard' ? 'bg-red-500/20 border-red-500 text-red-400' : 'bg-brand-black border-brand-dark text-gray-500'" class="p-3 rounded-lg border text-sm font-bold transition hover:border-gray-500">🔴 Difícil</button>
                            </div>
                        </div>
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
                                {{ uiWaypoints.length > 0 ? 'Editar Mapa' : 'Dibuixar Ruta' }}
                            </span>
                        </button>
                    </div>

                    <button type="submit" :disabled="form.processing || uiWaypoints.length < 2" class="w-full bg-white text-black font-black py-4 rounded-xl uppercase tracking-widest hover:bg-gray-200 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-xl">
                        Guardar Ruta Completa
                    </button>
                </form>
            </div>

            <div v-show="isMapOpen" class="fixed inset-0 z-[5000] bg-gray-900 flex flex-col">
                
                <div id="map" class="absolute inset-0 w-full h-full z-0"></div>

                <div class="absolute top-3 left-0 w-full z-[5010] p-4 pt-safe-top pointer-events-none">
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
                            
                            <button @click="closeMap" class="bg-brand-neon text-brand-black px-4 py-2.5 rounded-xl font-bold text-xs uppercase hover:bg-white transition shadow-[0_0_15px_rgba(12,225,181,0.3)] flex items-center gap-2 flex-shrink-0">
                                <span>Fet</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 01.208 1.04l-9 13.5a.75.75 0 01-1.154.114l-6-6a.75.75 0 011.06-1.06l5.353 5.353 8.493-12.739a.75.75 0 011.04-.208z" clip-rule="evenodd" /></svg>
                            </button>
                        </div>

                        <div v-if="searchResults.length > 0" class="border-t border-brand-dark/50 pt-2">
                            <ul class="max-h-40 overflow-y-auto">
                                <li v-for="(result, index) in searchResults" :key="index" @click="selectSearchResult(result)" class="p-2 hover:bg-brand-neon/20 rounded-lg cursor-pointer flex items-center gap-3 transition">
                                    <div class="bg-gray-800 p-1.5 rounded-full text-brand-neon">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-white font-medium truncate">{{ result.display_name.split(',')[0] }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ result.display_name }}</p>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div v-if="uiWaypoints.length > 0 && searchResults.length === 0" class="border-t border-brand-dark/50 pt-2 mt-1">
                             <div class="text-[10px] text-gray-500 uppercase font-bold mb-2 pl-1">Ordre de la Ruta (Arrossega)</div>
                             
                             <draggable 
                                v-model="uiWaypoints" 
                                item-key="id" 
                                @end="onDragEnd"
                                handle=".drag-handle"
                                class="flex flex-col gap-2 max-h-40 overflow-y-auto"
                            >
                                <template #item="{element, index}">
                                    <div class="flex items-center gap-2 bg-brand-black p-2 rounded-lg border border-gray-800 text-sm group">
                                        <div class="drag-handle text-gray-500 cursor-grab hover:text-white p-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                                        </div>
                                        
                                        <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0" :class="index === 0 ? 'bg-green-500 text-black' : (index === uiWaypoints.length -1 ? 'bg-red-500 text-white' : 'bg-brand-neon text-black')">
                                            {{ index + 1 }}
                                        </div>

                                        <div class="flex-1 truncate text-gray-300">
                                            {{ element.name || `Punt ${index + 1}` }}
                                        </div>

                                        <button @click="removeWaypoint(index)" class="text-gray-600 hover:text-red-500 p-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                </template>
                            </draggable>
                        </div>

                    </div>
                </div>

                <div class="absolute bottom-3 left-0 w-full z-[5010] px-4 flex items-end justify-between pointer-events-none pb-safe-bottom">
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
    motorcycles: Array
});

const form = useForm({
    title: '',
    description: '',
    difficulty: 'medium',
    motorcycle_id: null,
    planned_distance_km: 0,
    duration_seconds: 0,
    geo_json: null,
    waypoints: []
});

const map = ref(null);
const routingControl = ref(null);
const duration = ref(0);
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
    if (map.value) map.value.invalidateSize();
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
            if (uiWaypoints.value.length === 0) {
                 addPointToMap(L.latLng(lat, lng), "La meva ubicació");
            }
            if (userLocationMarker.value) map.value.removeLayer(userLocationMarker.value);
            userLocationMarker.value = L.circleMarker([lat, lng], {
                radius: 8, fillColor: '#3b82f6', color: '#ffffff', weight: 2, opacity: 1, fillOpacity: 1
            }).addTo(map.value);
        },
        () => {}, { enableHighAccuracy: true }
    );
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

onMounted(() => {
    map.value = L.map('map', { zoomControl: false, attributionControl: false }).setView([41.3851, 2.1734], 13);
    
    L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
        maxZoom: 20,
        className: 'map-tiles-inverse',
        keepBuffer: 100,
        updateWhenIdle: false
    }).addTo(map.value);

    routingControl.value = L.Routing.control({
        waypoints: [],
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
        form.duration_seconds = summary.totalTime;
        form.geo_json = JSON.stringify(routes[0].coordinates); 
        const wps = routingControl.value.getWaypoints().filter(wp => wp.latLng);
        form.waypoints = wps.map(wp => ({ lat: wp.latLng.lat, lng: wp.latLng.lng }));
    });
    
    locateUser();
});

const submit = () => {
    form.post(route('routes.store'));
};
</script>

<style>
.pt-safe-top { padding-top: env(safe-area-inset-top, 20px); }
.pb-safe-bottom { padding-bottom: env(safe-area-inset-bottom, 20px); }
.leaflet-routing-container { display: none !important; }
.map-tiles-inverse { filter: brightness(150%) contrast(150%); }
</style>
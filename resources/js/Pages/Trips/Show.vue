<template>
    <AppLayout title="Recorregut">
        <div class="relative">

            <!-- CAPÇALERA FIXA -->
            <div class="fixed left-0 right-0 z-[40] bg-brand-surface border-b border-brand-dark px-4 py-3 flex items-center gap-3 shadow-[0_4px_10px_rgba(0,0,0,0.3)]" style="top: calc(3.5rem + env(safe-area-inset-top));">
                <Link :href="route('routes.MyRoutes')" class="w-10 h-10 rounded-full bg-brand-neon flex items-center justify-center text-black hover:bg-white transition flex-shrink-0 shadow-[0_0_15px_rgba(12,225,181,0.3)]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                </Link>
                <div class="min-w-0">
                    <h1 class="text-white font-black uppercase tracking-tighter text-sm leading-none">📍 RECORREGUT</h1>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest truncate mt-0.5">{{ formatDate(trip.started_at) }}</p>
                </div>
                <!-- Botó eliminar -->
                <button @click="deleteTrip" class="ml-auto text-red-500 hover:text-white hover:bg-red-500/20 p-2 rounded-xl border border-transparent hover:border-red-500/50 transition flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                </button>
            </div>

            <!-- MAPA -->
            <div class="fixed inset-0 bg-gray-900 z-[5]" style="top: calc(3.5rem + env(safe-area-inset-top) + 56px);">
                <div id="trip-map" class="absolute inset-0"></div>

                <!-- Toggle comparativa (si hi ha ruta vinculada) -->
                <div v-if="trip.route" class="absolute top-3 left-1/2 -translate-x-1/2 z-[500] bg-brand-black/90 backdrop-blur-sm border border-brand-dark rounded-full px-4 py-2 flex items-center gap-3">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Comparativa</span>
                    <button @click="toggleComparativa" 
                        class="relative w-10 h-5 rounded-full transition-colors duration-200 flex-shrink-0"
                        :class="showComparativa ? 'bg-blue-500' : 'bg-brand-dark border border-brand-dark'">
                        <span class="absolute top-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform duration-200"
                            :class="showComparativa ? 'left-5' : 'left-0.5'"></span>
                    </button>
                    <span class="text-[10px] text-blue-400 font-bold">{{ trip.route.title }}</span>
                </div>

                <!-- Llegenda -->
                <div class="absolute bottom-[160px] right-3 z-[500] bg-brand-black/90 backdrop-blur-sm border border-brand-dark rounded-xl p-3 space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-1 rounded bg-red-500"></div>
                        <span class="text-[10px] text-white font-bold uppercase tracking-widest">El teu GPS</span>
                    </div>
                    <div v-if="showComparativa && trip.route" class="flex items-center gap-2">
                        <div class="w-6 h-1 rounded bg-blue-400"></div>
                        <span class="text-[10px] text-blue-400 font-bold uppercase tracking-widest">Ruta oficial</span>
                    </div>
                </div>
            </div>

            <!-- TAULER INFERIOR -->
            <div class="fixed left-0 right-0 z-[40] p-4" style="bottom: calc(4.75rem + env(safe-area-inset-bottom));">
                <div class="bg-brand-black/95 backdrop-blur-xl border border-brand-dark rounded-2xl shadow-2xl overflow-hidden">
                    <!-- Stats -->
                    <div class="grid grid-cols-3 divide-x divide-brand-dark">
                        <div class="p-3 text-center">
                            <span class="block text-xl font-mono font-black text-brand-neon">{{ trip.distance_km ?? '—' }}</span>
                            <span class="text-[9px] uppercase tracking-widest text-gray-500 font-bold">km</span>
                        </div>
                        <div class="p-3 text-center">
                            <span class="block text-xl font-mono font-black text-white">{{ formatDuration(trip.duration_seconds) }}</span>
                            <span class="text-[9px] uppercase tracking-widest text-gray-500 font-bold">Temps</span>
                        </div>
                        <div class="p-3 text-center">
                            <span class="block text-sm font-bold text-white truncate">{{ trip.motorcycle ? trip.motorcycle.brand : '—' }}</span>
                            <span class="text-[9px] uppercase tracking-widest text-gray-500 font-bold truncate block">{{ trip.motorcycle?.model }}</span>
                        </div>
                    </div>
                    <!-- Ruta vinculada (si n'hi ha) -->
                    <div v-if="trip.route" class="px-4 py-2 border-t border-brand-dark flex items-center gap-2">
                        <span class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Ruta:</span>
                        <Link :href="route('routes.show', trip.route.id)" class="text-[10px] text-brand-neon font-bold uppercase tracking-widest hover:underline truncate">{{ trip.route.title }}</Link>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({
    trip: Object
});

const map = ref(null);
const showComparativa = ref(false);
let tripPolyline = null;
let routePolyline = null;

onMounted(async () => {
    await buildMap();
});

const buildMap = async () => {
    const startLat = props.trip.starting_lat ?? (props.trip.waypoints?.[0]?.lat) ?? 41.3851;
    const startLng = props.trip.starting_lng ?? (props.trip.waypoints?.[0]?.lng) ?? 2.1734;

    map.value = L.map('trip-map', { zoomControl: true, attributionControl: false }).setView([startLat, startLng], 13);

    L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
        maxZoom: 20,
    }).addTo(map.value);

    // Dibuixar la polilínia GPS del recorregut
    if (props.trip.waypoints && props.trip.waypoints.length > 1) {
        const latlngs = props.trip.waypoints.map(p => [p.lat, p.lng]);
        tripPolyline = L.polyline(latlngs, {
            color: '#ef4444',
            weight: 5,
            opacity: 0.95,
            lineJoin: 'round',
        }).addTo(map.value);
        map.value.fitBounds(tripPolyline.getBounds(), { padding: [30, 30] });

        // Marcador inici/fi
        L.circleMarker(latlngs[0], { radius: 7, color: '#0CE1B5', fillColor: '#0CE1B5', fillOpacity: 1, weight: 2 })
            .bindTooltip('Inici', { permanent: false }).addTo(map.value);
        L.circleMarker(latlngs[latlngs.length - 1], { radius: 7, color: '#ffffff', fillColor: '#ef4444', fillOpacity: 1, weight: 2 })
            .bindTooltip('Fi', { permanent: false }).addTo(map.value);
    }
};

const toggleComparativa = () => {
    showComparativa.value = !showComparativa.value;
    if (showComparativa.value && props.trip.route?.geo_json) {
        // Dibuixar la ruta oficial en blau
        try {
            const geoJson = typeof props.trip.route.geo_json === 'string'
                ? JSON.parse(props.trip.route.geo_json)
                : props.trip.route.geo_json;
            if (routePolyline) map.value.removeLayer(routePolyline);
            routePolyline = L.geoJSON(geoJson, {
                style: { color: '#60a5fa', weight: 3, dashArray: '6 4', opacity: 0.8 }
            }).addTo(map.value);
        } catch (e) {
            console.warn('Error carregant GeoJSON de la ruta', e);
        }
    } else if (!showComparativa.value && routePolyline) {
        map.value.removeLayer(routePolyline);
        routePolyline = null;
    }
};

const deleteTrip = async () => {
    if (!confirm('Segur que vols eliminar aquest recorregut? Aquesta acció no es pot desfer.')) return;
    router.delete(route('trips.destroy', props.trip.id), {
        onSuccess: () => router.visit(route('routes.MyRoutes'))
    });
};

const formatDate = (isoStr) => {
    if (!isoStr) return '';
    return new Intl.DateTimeFormat('ca-ES', { dateStyle: 'long', timeStyle: 'short' }).format(new Date(isoStr));
};

const formatDuration = (sec) => {
    if (!sec) return '0m';
    const hrs = Math.floor(sec / 3600);
    const mins = Math.floor((sec % 3600) / 60);
    if (hrs > 0) return `${hrs}h ${mins}m`;
    return `${mins}m`;
};
</script>

<style scoped>
#trip-map { z-index: 10; }
</style>

<template>
    <AppLayout v-if="mapRoute" :title="mapRoute.title || 'Detall Ruta'">

        <div class="fixed top-0 left-0 w-full h-[100dvh] bg-gray-900 overflow-hidden overscroll-none z-40">

            <div v-if="mapRoute" id="map-detail" class="absolute inset-0 z-0 bg-gray-900"></div>

            <Link :href="route('routes.index')" class="absolute top-safe-top right-4 z-50 bg-black/50 hover:bg-black/80 text-white p-2 rounded-full backdrop-blur-md border border-white/10 transition mt-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </Link>

            <div class="absolute bottom-20 left-0 w-full p-4 z-10 pb-safe-bottom">
                <div class="bg-brand-black/95 backdrop-blur-xl border border-brand-dark rounded-2xl shadow-2xl overflow-hidden">

                    <div class="p-4 border-b border-gray-800 flex justify-between items-start flex-shrink-0">
                        <div class="flex-1 pr-2">
                            <h1 class="text-xl font-black text-white uppercase tracking-tighter leading-none">{{ mapRoute.title || 'Sense Títol' }}</h1>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border"
                                    :class="{
                                        'border-green-500 text-green-400 bg-green-500/10': mapRoute.difficulty === 'easy',
                                        'border-yellow-500 text-yellow-400 bg-yellow-500/10': mapRoute.difficulty === 'medium',
                                        'border-red-500 text-red-400 bg-red-500/10': mapRoute.difficulty === 'hard'
                                    }">
                                    {{ mapRoute.difficulty === 'easy' ? 'Fàcil' : (mapRoute.difficulty === 'medium' ? 'Mitjana' : 'Difícil') }}
                                </span>

                                <span v-if="mapRoute.is_public" class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-brand-neon/30 text-brand-neon bg-brand-neon/10">🌍 Pública</span>
                                <span v-else class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-gray-500 text-gray-400 bg-gray-800">🔒 Privada</span>

                                <span v-if="motorcycle" class="text-xs text-gray-400 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3"><path d="M8 16.25a.75.75 0 01.75-.75h2.5a.75.75 0 010 1.5h-2.5a.75.75 0 01-.75-.75z" /><path fill-rule="evenodd" d="M4 4a3 3 0 013-3h6a3 3 0 013 3v12a3 3 0 01-3 3H7a3 3 0 01-3-3V4zm4-1.5a.75.75 0 000 1.5h4a.75.75 0 000-1.5H8z" clip-rule="evenodd" /></svg>
                                    {{ motorcycle.alias || motorcycle.model }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button v-if="$page.props.auth.user && mapRoute.user_id === $page.props.auth.user.id" @click="copyShareLink" class="bg-gray-800 hover:bg-brand-neon hover:text-black text-white p-2.5 rounded-lg transition border border-gray-700 flex items-center justify-center relative" title="Copia l'enllaç de Compartició">
                                <svg v-if="!copyLinkSuccess" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" /></svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            </button>

                            <Link v-if="$page.props.auth.user && mapRoute.user_id === $page.props.auth.user.id" :href="route('routes.edit', mapRoute.id)" class="bg-gray-800 hover:bg-gray-700 text-white p-2.5 rounded-lg transition border border-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                            </Link>
                        </div>
                    </div>

                    <div class="p-4 pt-2 pb-0">
                        <p class="text-sm text-gray-400 line-clamp-2">{{ mapRoute.description || 'Sense descripció.' }}</p>
                    </div>

                    <div v-if="mapRoute.photo" class="px-4 pt-3">
                        <img :src="$page.props.storageUrl + '/' + mapRoute.photo" alt="Foto Ruta" class="w-full h-32 object-cover rounded-xl border border-brand-dark">
                    </div>

                    <div class="grid grid-cols-2 gap-px bg-gray-800/50 mt-4 border-t border-gray-800">
                        <div class="p-3 text-center bg-brand-black/50">
                            <span class="block text-xl font-mono font-bold text-brand-neon">{{ mapRoute.planned_distance_km || 0 }}</span>
                            <span class="text-[10px] text-gray-500 uppercase tracking-wider">Km</span>
                        </div>
                        <div class="p-3 text-center bg-brand-black/50">
                            <span class="block text-xl font-mono font-bold text-white">{{ formattedDuration }}</span>
                            <span class="text-[10px] text-gray-500 uppercase tracking-wider">Temps</span>
                        </div>
                    </div>

                    <div class="p-4 flex flex-col gap-3">
                        <div class="flex gap-3">
                            <a :href="googleMapsLink" target="_blank" class="flex-1 flex items-center justify-center gap-2 bg-brand-neon text-brand-black font-black py-3.5 rounded-xl uppercase tracking-widest shadow-[0_0_15px_rgba(12,225,181,0.4)] hover:scale-[1.02] transition">
                                <span>Navegar</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M8.161 2.58a1.875 1.875 0 0 1 1.678 0l4.993 2.498c.106.052.23.052.336 0l3.869-1.935A1.875 1.875 0 0 1 21.75 4.82v12.485c0 .71-.401 1.36-1.037 1.677l-4.875 2.437a1.875 1.875 0 0 1-1.678 0l-4.993-2.498a.75.75 0 0 0-.336 0l-3.868 1.935A1.875 1.875 0 0 1 2.25 19.18V6.695c0-.71.401-1.36 1.036-1.677l4.875-2.437ZM9 6a.75.75 0 0 1 .75.75V15a.75.75 0 0 1-1.5 0V6.75A.75.75 0 0 1 9 6Zm6.75 3a.75.75 0 0 0-1.5 0v8.25a.75.75 0 0 0 1.5 0V9Z" clip-rule="evenodd" /></svg>
                            </a>

                            <Link
                                v-if="$page.props.auth.user && mapRoute.user_id !== $page.props.auth.user.id"
                                :href="route('routes.clone', mapRoute.id)"
                                method="post"
                                as="button"
                                class="px-4 flex items-center justify-center bg-transparent border-2 border-brand-neon hover:bg-brand-neon hover:text-black text-brand-neon font-black rounded-xl transition"
                                title="Guardar i Editar"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" /></svg>
                            </Link>
                        </div>

                        <button v-if="!isRecording" @click="startRecording" class="w-full flex items-center justify-center gap-2 bg-red-600 text-white font-black py-4 rounded-xl uppercase tracking-widest shadow-[0_0_15px_rgba(220,38,38,0.4)] hover:scale-[1.02] transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><circle cx="12" cy="12" r="8" fill="currentColor" /></svg>
                            <span>Gravar la meva Ruta</span>
                        </button>
                        <button v-else @click="stopRecording" class="w-full flex items-center justify-center gap-2 bg-red-900 border border-red-500 text-white font-black py-4 rounded-xl uppercase tracking-widest shadow-[0_0_15px_rgba(220,38,38,0.4)] animate-pulse transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z" fill="currentColor" /></svg>
                            <span>Aturar i Guardar</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
    <div v-else class="h-screen bg-gray-900 flex items-center justify-center text-white">
        <p class="animate-pulse">Carregant ruta...</p>
    </div>
</template>

<script setup>
import { onMounted, computed, ref, nextTick } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { registerPlugin } from '@capacitor/core';
const BackgroundGeolocation = registerPlugin('BackgroundGeolocation');
import { Geolocation } from '@capacitor/geolocation';
import { LocalNotifications } from '@capacitor/local-notifications';

const props = defineProps({
    mapRoute: Object,
    motorcycle: Object
});

const map = ref(null);
const copyLinkSuccess = ref(false);
const isRecording = ref(false);
const recordWatcherId = ref(null);
const recordedWaypoints = ref([]);

const copyShareLink = () => {
    if(!props.mapRoute || !props.mapRoute.share_token) return;
    let tokenToCopy = props.mapRoute.share_token;
    navigator.clipboard.writeText(tokenToCopy).then(() => {
        copyLinkSuccess.value = true;
        setTimeout(() => {
            copyLinkSuccess.value = false;
        }, 3000);
    });
};

const formattedDuration = computed(() => {
    if (!props.mapRoute || !props.mapRoute.duration_seconds) return '0h 0m';
    const h = Math.floor(props.mapRoute.duration_seconds / 3600);
    const m = Math.floor((props.mapRoute.duration_seconds % 3600) / 60);
    return `${h}h ${m}m`;
});

// Convertir GeoJSON a Array de punts
const getRoutePoints = () => {
    if(!props.mapRoute) return [];
    let data = props.mapRoute.geo_json;
    if (!data) return [];

    if (typeof data === 'string') {
        try {
            data = JSON.parse(data);
            if (typeof data === 'string') data = JSON.parse(data);
        } catch (e) {
            console.error("Error llegint ruta:", e);
            return [];
        }
    }
    return Array.isArray(data) ? data : [];
};

const googleMapsLink = computed(() => {
    if (!props.mapRoute || !props.mapRoute.waypoints || props.mapRoute.waypoints.length === 0) return '#';
    const baseUrl = "https://www.google.com/maps/dir/?api=1";

    try {
        const origin = `&origin=${props.mapRoute.waypoints[0].lat || props.mapRoute.waypoints[0].latitude},${props.mapRoute.waypoints[0].lng || props.mapRoute.waypoints[0].longitude}`;
        const last = props.mapRoute.waypoints[props.mapRoute.waypoints.length - 1];
        const destination = `&destination=${last.lat || last.latitude},${last.lng || last.longitude}`;

        let waypointsStr = "";
        if (props.mapRoute.waypoints.length > 2) {
            const intermediates = props.mapRoute.waypoints.slice(1, -1);
            const coords = intermediates.map(wp => `${wp.lat || wp.latitude},${wp.lng || wp.longitude}`).join('|');
            waypointsStr = `&waypoints=${coords}`;
        }

        return `${baseUrl}${origin}${destination}${waypointsStr}&travelmode=driving`;
    } catch(e) { return '#'; }
});

onMounted(async () => {
    await nextTick();
    if (!props.mapRoute) return;

    const startLat = parseFloat(props.mapRoute.starting_lat || 41.3851);
    const startLng = parseFloat(props.mapRoute.starting_lng || 2.1734);

    const mapElement = document.getElementById('map-detail');
    if(!mapElement) return;

    map.value = L.map('map-detail', { zoomControl: false, attributionControl: false }).setView([startLat, startLng], 13);

    L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
        maxZoom: 20,
        className: 'map-tiles-inverse'
    }).addTo(map.value);

    const points = getRoutePoints();

    if (points.length > 0) {
        const validPoints = points.map(p => {
            if (Array.isArray(p) && p.length >= 2) return [p[1], p[0]];
            if (p && (p.lat !== undefined || p.latitude !== undefined)) {
                return [p.lat ?? p.latitude, p.lng ?? p.longitude];
            }
            return null;
        }).filter(p => p && p[0] !== null && p[1] !== null && p[0] !== undefined && p[1] !== undefined);

        if (validPoints.length > 0) {
            const polyline = L.polyline(validPoints, {
                color: '#0CE1B5',
                weight: 5,
                opacity: 0.9,
                lineJoin: 'round'
            }).addTo(map.value);

            const first = validPoints[0];
            const last = validPoints[validPoints.length - 1];

            L.circleMarker([first[0], first[1]], { radius: 6, color: '#22c55e', fillColor: '#22c55e', fillOpacity: 1 }).addTo(map.value);
            L.circleMarker([last[0], last[1]], { radius: 6, color: '#ef4444', fillColor: '#ef4444', fillOpacity: 1 }).addTo(map.value);

            try {
                map.value.fitBounds(polyline.getBounds(), { padding: [50, 150] });
            } catch (e) {
                console.warn("No s'ha pogut centrar el mapa automàticament");
            }
        }
    }
});

const startRecording = async () => {
    try {
        await LocalNotifications.requestPermissions();
        const permStatus = await Geolocation.checkPermissions();
        if (permStatus.location !== 'granted') {
            await Geolocation.requestPermissions();
        }
    } catch (e) {
        console.warn('Avís de permisos natius', e);
    }

    isRecording.value = true;
    recordedWaypoints.value = [];

    window.alert("Gravació iniciada! Nota: Perquè es guardin punts, has de moure't físicament almenys 15 metres, o utilitzar el 'Play Route' de l'emulador.");

    BackgroundGeolocation.addWatcher(
        {
            backgroundMessage: "Clutch Control està gravant la ruta...",
            backgroundTitle: "Gravant Ruta",
            requestPermissions: true,
            stale: false,
            distanceFilter: 15
        },
        function callback(location, error) {
            if (error) {
                if (error.code === "NOT_AUTHORIZED") {
                    if (window.confirm("Clutch Control necessita la teva ubicació per gravar la ruta. Vols obrir la configuració per posar-ho a 'Sempre'?")) {
                        BackgroundGeolocation.openSettings();
                    }
                }
                console.error("Error BackgroundGeolocation:", error);
                return;
            }

            if (location) {
                recordedWaypoints.value.push({
                    id: Date.now() + Math.random(),
                    lat: location.latitude,
                    lng: location.longitude,
                    name: `Punt gravat GPS`
                });

                if (map.value) {
                    L.circleMarker([location.latitude, location.longitude], {
                        radius: 4, color: '#ff0000', fillColor: '#ff0000', fillOpacity: 1
                    }).addTo(map.value);
                }
            }
        }
    ).then(function afterTheWatcherHasBeenAdded(watcher_id) {
        recordWatcherId.value = watcher_id;
    });
};

const stopRecording = () => {
    isRecording.value = false;
    if (recordWatcherId.value) {
        BackgroundGeolocation.removeWatcher({ id: recordWatcherId.value });
        recordWatcherId.value = null;
    }

    if (recordedWaypoints.value.length > 1) {
        localStorage.setItem('clutch_recorded_route', JSON.stringify(recordedWaypoints.value));
        router.visit(route('routes.create'));
    } else {
        alert("No s'han enregistrat suficients punts per crear una ruta.");
    }
};
</script>

<style>
.pt-safe-top { padding-top: env(safe-area-inset-top, 40px); }
.top-safe-top { top: calc(env(safe-area-inset-top, 40px) + 1rem); }
.pb-safe-bottom { padding-bottom: env(safe-area-inset-bottom, 20px); }
.map-tiles-inverse { filter: brightness(150%) contrast(150%); }
</style>

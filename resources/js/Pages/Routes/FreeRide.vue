<template>
    <AppLayout :title="$t('free_ride.title')">

        <div class="fixed top-0 left-0 w-full h-[100dvh] bg-brand-surface overflow-hidden overscroll-none z-[5000]">

            <div id="map-freeride" class="absolute inset-0 z-0 bg-gray-900"></div>

            <Link :href="route('dashboard', motorcycle.id)" class="absolute top-safe-top left-4 z-50 w-10 h-10 rounded-full bg-brand-neon hover:bg-white text-black flex items-center justify-center flex-shrink-0 transition shadow-[0_0_15px_rgba(12,225,181,0.3)] mt-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
            </Link>

            <!-- LIVE TRACKING OVERLAY -->
            <div v-show="isRecording" class="absolute top-safe-top left-1/2 -translate-x-1/2 z-50 bg-brand-black/90 backdrop-blur-xl border border-brand-dark rounded-full px-6 py-3 shadow-[0_0_20px_rgba(239,68,68,0.3)] mt-2 flex items-center gap-6">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                    </span>
                    <span class="text-xs font-black text-red-500 uppercase tracking-widest">LIVE</span>
                </div>
                <div class="flex flex-col items-center">
                    <span class="text-[10px] text-gray-500 uppercase font-bold leading-none mb-1">{{ $t('free_ride.chrono') }}</span>
                    <span class="text-lg font-mono font-bold text-white leading-none">{{ formattedRecordingTime }}</span>
                </div>
                <div class="flex flex-col items-center">
                    <span class="text-[10px] text-gray-500 uppercase font-bold leading-none mb-1">{{ $t('free_ride.distance') }}</span>
                    <span class="text-lg font-mono font-bold text-brand-neon leading-none">{{ (recordedDistance / 1000).toFixed(2) }}<span class="text-xs text-gray-400 ml-1">km</span></span>
                </div>
            </div>

            <!-- TAULER INFERIOR -->
            <div class="absolute bottom-6 left-0 w-full p-4 z-10 pb-safe-bottom">
                <div class="bg-brand-black/95 backdrop-blur-xl border border-brand-dark rounded-2xl shadow-2xl overflow-hidden p-4">
                    
                    <div class="mb-4 text-center">
                        <h1 class="text-2xl font-black text-white uppercase tracking-tighter">{{ $t('free_ride.title').toUpperCase() }}</h1>
                        <p class="text-xs text-brand-neon font-bold flex items-center justify-center gap-1 mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path d="M8 16.25a.75.75 0 01.75-.75h2.5a.75.75 0 010 1.5h-2.5a.75.75 0 01-.75-.75z" /><path fill-rule="evenodd" d="M4 4a3 3 0 013-3h6a3 3 0 013 3v12a3 3 0 01-3 3H7a3 3 0 01-3-3V4zm4-1.5a.75.75 0 000 1.5h4a.75.75 0 000-1.5H8z" clip-rule="evenodd" /></svg>
                            {{ motorcycle.brand }} {{ motorcycle.model }}
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <button v-if="!isRecording" @click="startRecording" class="flex-1 flex items-center justify-center gap-2 bg-red-600/10 text-red-500 border border-red-500/30 py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-red-600/20 transition shadow-[0_0_15px_rgba(239,68,68,0.2)]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><circle cx="12" cy="12" r="8" fill="currentColor" /></svg>
                            {{ $t('free_ride.start_recording') }}
                        </button>
                        <button v-else @click="stopRecording" class="flex-1 flex items-center justify-center gap-2 bg-red-900 border border-red-500 text-white py-4 rounded-xl font-black text-xs uppercase tracking-widest animate-pulse shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z" fill="currentColor" /></svg>
                            {{ $t('free_ride.stop_round') }}
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { onMounted, computed, ref, nextTick } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { registerPlugin } from '@capacitor/core';
const BackgroundGeolocation = registerPlugin('BackgroundGeolocation');
import { Geolocation } from '@capacitor/geolocation';
import { LocalNotifications } from '@capacitor/local-notifications';

const { t } = useI18n();

const props = defineProps({
    motorcycle: Object
});

const map = ref(null);
const isRecording = ref(false);

const recordWatcherId = ref(null);
const recordedWaypoints = ref([]);
const recordedDistance = ref(0);
const recordingTime = ref(0);
let timerInterval = null;
const trackingPolyline = ref(null);
const currentLocationMarker = ref(null);
let recordingStartTime = 0;

const formattedRecordingTime = computed(() => {
    const h = Math.floor(recordingTime.value / 3600);
    const m = Math.floor((recordingTime.value % 3600) / 60);
    const s = recordingTime.value % 60;
    return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
});

onMounted(async () => {
    await nextTick();

    const mapElement = document.getElementById('map-freeride');
    if(!mapElement) return;

    // Centrat genèric o última posició coneguda si cal (per defecte BCN)
    map.value = L.map('map-freeride', { zoomControl: false, attributionControl: false }).setView([41.3851, 2.1734], 14);

    L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
        maxZoom: 20,
        className: 'map-tiles-inverse'
    }).addTo(map.value);

    // Intentar centrar el mapa a la posició actual directament abans de gravar
    try {
        const position = await Geolocation.getCurrentPosition();
        map.value.setView([position.coords.latitude, position.coords.longitude], 15);
        
        currentLocationMarker.value = L.circleMarker([position.coords.latitude, position.coords.longitude], {
            radius: 8, color: '#0CE1B5', fillColor: '#1e293b', weight: 3, fillOpacity: 1
        }).addTo(map.value);

    } catch (e) {
        console.warn("No es pot trobar la ubicació del dispositiu inicial", e);
    }
});

const startRecording = async () => {
    try {
        LocalNotifications.requestPermissions().catch(e => console.warn(e));
        const permStatus = await Geolocation.checkPermissions();
        if (permStatus.location !== 'granted') {
            await Geolocation.requestPermissions();
        }
    } catch (e) {
        console.warn('Avís permisos', e);
    }

    isRecording.value = true;
    recordedWaypoints.value = [];
    recordedDistance.value = 0;
    recordingTime.value = 0;

    recordingStartTime = Date.now();
    if (timerInterval) clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        recordingTime.value = Math.floor((Date.now() - recordingStartTime) / 1000);
    }, 1000);

    if (map.value) {
        if (trackingPolyline.value) map.value.removeLayer(trackingPolyline.value);
        if (currentLocationMarker.value) map.value.removeLayer(currentLocationMarker.value);

        trackingPolyline.value = L.polyline([], {
            color: '#ef4444',
            weight: 6,
            opacity: 0.9,
            lineJoin: 'round'
        }).addTo(map.value);

        currentLocationMarker.value = L.circleMarker([0, 0], {
            radius: 7, color: '#ffffff', fillColor: '#ef4444', weight: 2, fillOpacity: 1
        }).addTo(map.value);
    }

    BackgroundGeolocation.addWatcher(
        {
            backgroundMessage: t('free_ride.bg_message'),
            backgroundTitle: t('free_ride.bg_title'),
            requestPermissions: true,
            stale: false,
            distanceFilter: 10
        },
        function callback(location, error) {
            if (error) return;

            if (location) {
                const newLatLng = L.latLng(location.latitude, location.longitude);
                
                if (recordedWaypoints.value.length > 0 && map.value) {
                    const lastPoint = recordedWaypoints.value[recordedWaypoints.value.length - 1];
                    const lastLatLng = L.latLng(lastPoint.lat, lastPoint.lng);
                    const literalDistanceMeters = map.value.distance(lastLatLng, newLatLng);
                    recordedDistance.value += literalDistanceMeters;
                }

                recordedWaypoints.value.push({
                    id: Date.now(),
                    lat: location.latitude,
                    lng: location.longitude,
                });

                if (map.value && trackingPolyline.value) {
                    trackingPolyline.value.addLatLng(newLatLng);
                    currentLocationMarker.value.setLatLng(newLatLng);
                    map.value.panTo(newLatLng, { animate: true, duration: 1 });
                }
            }
        }
    ).then(function (watcher_id) {
        recordWatcherId.value = watcher_id;
    });
};

const stopRecording = () => {
    isRecording.value = false;
    if (timerInterval) clearInterval(timerInterval);

    if (recordWatcherId.value) {
        BackgroundGeolocation.removeWatcher({ id: recordWatcherId.value });
        recordWatcherId.value = null;
    }

    if (recordedWaypoints.value.length > 1) {
        const distanceKm = (recordedDistance.value / 1000).toFixed(2);
        
        try {
            const existingPending = JSON.parse(localStorage.getItem('pending_trips') || '[]');
            
            const newPendingTrip = {
                id: 'offline_' + Date.now(),
                started_at: new Date(recordingStartTime).toISOString(),
                motorcycle_id: props.motorcycle?.id || null,
                route_id: null, // Free Ride mai va vinculat a una ruta
                distance_km: parseFloat(distanceKm),
                duration_seconds: recordingTime.value,
                waypoints: recordedWaypoints.value
            };
            
            existingPending.push(newPendingTrip);
            localStorage.setItem('pending_trips', JSON.stringify(existingPending));
            
            alert(t('free_ride.stopped_title') + `\n` + t('free_ride.stopped_msg', { km: distanceKm }));
        } catch (e) {
            console.error('Error saving offline trip:', e);
            alert(t('free_ride.stopped_error', { km: distanceKm }));
        }
    } else {
        alert(t('free_ride.no_movement'));
        if (trackingPolyline.value) map.value.removeLayer(trackingPolyline.value);
        if (currentLocationMarker.value) map.value.removeLayer(currentLocationMarker.value);
    }
};
</script>

<style>
.pt-safe-top { padding-top: env(safe-area-inset-top, 40px); }
.top-safe-top { top: calc(env(safe-area-inset-top, 40px) + 1rem); }
.pb-safe-bottom { padding-bottom: env(safe-area-inset-bottom, 20px); }
.map-tiles-inverse { filter: brightness(150%) contrast(150%); }
</style>

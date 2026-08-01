<template>
    <AppLayout v-if="mapRoute" :title="mapRoute.title || $t('routes.loading_route')">

        <div class="fixed top-0 left-0 w-full h-[100dvh] bg-gray-900 overflow-hidden overscroll-none z-[5000]">

            <div v-if="mapRoute" id="map-detail" class="absolute inset-0 z-0 bg-gray-900"></div>

            <button
                type="button"
                @click="goBack"
                class="cc-icon-btn absolute top-safe-top left-4 z-[5010] mt-2 bg-black/50 backdrop-blur-md border-white/20 text-white"
                :aria-label="$t('common.back')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </button>

            <!-- LIVE TRACKING OVERLAY -->
            <div v-if="isRecording" class="absolute top-safe-top left-1/2 -translate-x-1/2 z-50 bg-black/80 backdrop-blur-xl border border-red-500/20 rounded-2xl px-6 py-3 mt-2 flex items-center gap-6">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                    </span>
                    <span class="text-xs font-semibold text-red-400 tracking-tight">LIVE</span>
                </div>
                <div class="flex flex-col items-center">
                    <span class="text-[10px] text-gray-500 font-medium leading-none mb-1">{{ $t('free_ride.chrono') }}</span>
                    <span class="text-lg font-mono font-semibold text-white leading-none">{{ formattedRecordingTime }}</span>
                </div>
                <div class="flex flex-col items-center">
                    <span class="text-[10px] text-gray-500 font-medium leading-none mb-1">{{ $t('free_ride.distance') }}</span>
                    <span class="text-lg font-mono font-semibold text-white leading-none">{{ (recordedDistance / 1000).toFixed(2) }}<span class="text-xs text-gray-400 ml-1">km</span></span>
                </div>
            </div>

            <!-- Full de ruta: el mapa és el fons, aquí hi ha el que has de saber i fer -->
            <div class="absolute bottom-0 left-0 w-full z-10">
                <div class="mx-3 mb-3 rounded-3xl bg-brand-black/95 backdrop-blur-xl border border-white/[0.08] overflow-hidden pb-safe-bottom cc-fade-in">

                    <button
                        type="button"
                        @click="isExpanded = !isExpanded"
                        class="w-full px-5 pt-4 pb-1 flex items-start gap-3 text-left"
                    >
                        <span class="min-w-0 flex-1">
                            <span class="block text-[19px] font-medium text-white leading-snug line-clamp-2">
                                {{ mapRoute.title || $t('events.no_title') }}
                            </span>
                            <span class="block mt-1 text-sm text-gray-500 truncate">{{ subtitleLine }}</span>
                        </span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            class="w-4 h-4 mt-2 text-gray-500 flex-shrink-0 transition-transform"
                            :class="isExpanded ? 'rotate-180' : ''"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                        </svg>
                    </button>

                    <!-- Les xifres de la ruta: text, no caixes -->
                    <div class="px-5 pt-4 flex items-baseline gap-8">
                        <span>
                            <span class="block text-[26px] font-light leading-none tabular-nums text-white">
                                {{ Math.round(mapRoute.distance_km || mapRoute.planned_distance_km || 0) }}<span class="text-sm text-gray-500 ml-1">km</span>
                            </span>
                        </span>
                        <span v-if="mapRoute.duration_seconds">
                            <span class="block text-[26px] font-light leading-none tabular-nums text-white">{{ formattedDuration }}</span>
                        </span>
                        <span class="ml-auto text-sm text-gray-500">
                            {{ $t('routes.diff_label_' + (mapRoute.difficulty || 'medium')) }}
                        </span>
                    </div>

                    <div class="px-5 pt-5 pb-5 flex items-center gap-2.5">
                        <a :href="googleMapsLink" target="_blank" class="cc-btn-primary flex-1 py-3">
                            {{ $t('routes.navigate') }}
                        </a>
                        <button v-if="!isRecording" type="button" @click="startRecording" class="cc-btn-secondary flex-1 py-3">
                            {{ $t('routes.follow') }}
                        </button>
                        <button v-else type="button" @click="stopRecording" class="cc-btn-danger flex-1 py-3">
                            {{ $t('routes.stop') }}
                        </button>
                    </div>

                    <!-- Tot el que no necessites per sortir a rodar viu aquí sota -->
                    <div v-show="isExpanded" class="border-t border-white/[0.06] overflow-y-auto max-h-[55vh] overscroll-contain">

                        <p v-if="mapRoute.description" class="px-5 pt-5 text-sm text-gray-400 leading-relaxed">
                            {{ mapRoute.description }}
                        </p>

                        <div v-if="mapRoute.photo" class="px-5 pt-5">
                            <img :src="$page.props.storageUrl + '/' + mapRoute.photo" alt="" class="w-full h-36 object-cover rounded-2xl">
                        </div>

                        <!-- Els teus recorreguts sobre aquesta ruta -->
                        <section v-if="$page.props.auth.user && myRouteTrips.length" class="px-5 pt-6">
                            <div class="flex items-center justify-between gap-3">
                                <p class="cc-section-label">{{ $t('routes.your_trips') }}</p>
                                <button type="button" @click="toggleAllTripsMap" class="cc-btn-text px-3 py-1 text-xs">
                                    {{ showingAllTrips ? $t('routes.hide_on_map') : $t('routes.show_on_map') }}
                                </button>
                            </div>

                            <div class="mt-2 divide-y divide-white/[0.06]">
                                <Link
                                    v-for="trip in myRouteTrips"
                                    :key="trip.id"
                                    :href="route('trips.show', trip.id)"
                                    class="flex items-baseline gap-3 py-3 group"
                                >
                                    <span class="flex-1 text-sm text-gray-300 group-hover:text-white transition-colors truncate">
                                        {{ formatTripDate(trip.started_at) }}
                                    </span>
                                    <span class="text-sm tabular-nums text-gray-400">{{ trip.distance_km }} km</span>
                                    <span class="text-xs text-gray-600 tabular-nums w-12 text-right">{{ formatTripDuration(trip.duration_seconds) }}</span>
                                </Link>
                            </div>
                        </section>

                        <!-- Valoracions -->
                        <section v-if="mapRoute.is_public" class="px-5 pt-6">
                            <div class="flex items-center justify-between gap-3">
                                <p class="cc-section-label">{{ $t('routes.ratings') }}</p>
                                <span v-if="mapRoute.reviews && mapRoute.reviews.length" class="text-sm text-white tabular-nums">
                                    ★ {{ (mapRoute.reviews.reduce((a, b) => a + b.rating, 0) / mapRoute.reviews.length).toFixed(1) }}
                                </span>
                            </div>

                            <div v-if="mapRoute.reviews && mapRoute.reviews.length" class="mt-2 divide-y divide-white/[0.06]">
                                <div v-for="review in mapRoute.reviews" :key="review.id" class="py-3">
                                    <div class="flex items-baseline justify-between gap-3">
                                        <span class="text-sm text-gray-200">{{ review.user.name }}</span>
                                        <span class="text-xs text-gray-500 tabular-nums">★ {{ review.rating }}</span>
                                    </div>
                                    <p v-if="review.comment" class="mt-1 text-xs text-gray-500 leading-relaxed">{{ review.comment }}</p>
                                </div>
                            </div>
                            <p v-else class="mt-2 text-sm text-gray-500">{{ $t('routes.no_reviews_yet') }}</p>

                            <div v-if="$page.props.auth?.user && mapRoute.user_id !== $page.props.auth.user.id" class="mt-4">
                                <p v-if="userHasReviewed" class="text-sm text-gray-500">{{ $t('routes.already_reviewed') }}</p>
                                <form v-else @submit.prevent="submitReview" class="space-y-3">
                                    <div class="flex items-center gap-1">
                                        <button
                                            v-for="n in 5"
                                            :key="n"
                                            type="button"
                                            @click="reviewForm.rating = n"
                                            class="text-xl transition-colors"
                                            :class="reviewForm.rating >= n ? 'text-white' : 'text-gray-700 hover:text-gray-500'"
                                        >★</button>
                                    </div>
                                    <textarea
                                        v-model="reviewForm.comment"
                                        rows="2"
                                        :placeholder="$t('routes.review_placeholder')"
                                        class="w-full rounded-xl bg-white/[0.04] border-white/[0.08] text-white text-sm focus:border-white/30 focus:ring-0"
                                    ></textarea>
                                    <button type="submit" :disabled="reviewForm.processing || reviewForm.rating === 0" class="cc-btn-secondary w-full py-2.5 disabled:opacity-40">
                                        {{ $t('routes.publish_review') }}
                                    </button>
                                </form>
                            </div>
                        </section>

                        <!-- Accions secundàries: text, mai una graella de botons -->
                        <nav class="px-5 py-6 mt-2 flex flex-wrap gap-2 border-t border-white/[0.06]">
                            <Link
                                v-if="$page.props.auth.user && !isRecording"
                                :href="route('routes.habitual', { route: mapRoute.id })"
                                class="cc-btn-text"
                            >
                                {{ $t('routes.add_to_habitual') }}
                            </Link>
                            <button
                                v-if="$page.props.auth.user && mapRoute.user_id !== $page.props.auth.user.id"
                                type="button"
                                @click="router.post(route('routes.clone', mapRoute.id))"
                                class="cc-btn-text"
                            >
                                {{ $t('routes.save_route') }}
                            </button>
                            <template v-if="$page.props.auth.user && mapRoute.user_id === $page.props.auth.user.id">
                                <button type="button" @click="copyShareLink" class="cc-btn-text">
                                    {{ copyLinkSuccess ? $t('routes.link_copied') : $t('routes.share') }}
                                </button>
                                <Link :href="route('routes.edit', mapRoute.id)" class="cc-btn-text">
                                    {{ $t('routes.edit') }}
                                </Link>
                            </template>
                            <ReportButton
                                v-if="!$page.props.auth.user || mapRoute.user_id !== $page.props.auth.user.id"
                                reportable-type="route"
                                :reportable-id="mapRoute.id"
                                label="Denunciar ruta"
                                :context-label="`Denunciar ruta: ${mapRoute.title}`"
                                button-class="cc-btn-text border-red-500/25 text-red-400 hover:text-red-300 hover:border-red-500/50"
                            />
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
    <div v-else class="h-screen bg-gray-900 flex items-center justify-center text-white">
        <p class="animate-pulse text-gray-400">{{ $t('routes.loading_route') }}</p>
    </div>
</template>

<script setup>
import { onMounted, computed, ref, nextTick } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import ReportButton from '@/Components/ReportButton.vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { smartBack, visitWithoutStack } from '@/Composables/navigationStack.js';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { registerPlugin } from '@capacitor/core';
const BackgroundGeolocation = registerPlugin('BackgroundGeolocation');
import { Geolocation } from '@capacitor/geolocation';
import { LocalNotifications } from '@capacitor/local-notifications';
import axios from 'axios';
import { addMapTileLayer } from '@/config/mapTiles.js';
import { buildGoogleMapsDirectionsUrl, isClosedLoopRoute, parseLatLngPath } from '@/services/routeGeometry.js';

const props = defineProps({
    mapRoute: Object,
    motorcycle: Object
});

const { t } = useI18n();

const map = ref(null);
const copyLinkSuccess = ref(false);
const isRecording = ref(false);
const isExpanded = ref(false);
const myRouteTrips = ref([]);
const showingAllTrips = ref(false);
const allTripsLayers = ref([]);

const toggleAllTripsMap = () => {
    showingAllTrips.value = !showingAllTrips.value;
    if (showingAllTrips.value) {
        myRouteTrips.value.forEach(trip => {
            if (trip.waypoints && trip.waypoints.length > 1) {
                const latlngs = trip.waypoints.map(p => [p.lat, p.lng]);
                const poly = L.polyline(latlngs, {
                    color: '#ef4444',
                    weight: 3,
                    opacity: 0.7,
                    dashArray: '5 5'
                }).addTo(map.value);
                allTripsLayers.value.push(poly);
            }
        });
        if (allTripsLayers.value.length > 0) {
            const group = L.featureGroup(allTripsLayers.value);
            map.value.fitBounds(group.getBounds(), { padding: [30, 30] });
        }
    } else {
        allTripsLayers.value.forEach(layer => map.value.removeLayer(layer));
        allTripsLayers.value = [];
        
        // Centrar de nou la ruta original
        const points = getRoutePoints();
        const validPoints = points.map(p => {
            if (Array.isArray(p) && p.length >= 2) return [p[1], p[0]];
            if (p && (p.lat !== undefined || p.latitude !== undefined)) return [p.lat ?? p.latitude, p.lng ?? p.longitude];
            return null;
        }).filter(p => p && p[0] !== null && p[1] !== null);
        
        if (validPoints.length > 0) {
            map.value.fitBounds(L.polyline(validPoints).getBounds(), { padding: [50, 150] });
        }
    }
};

const reviewForm = useForm({
    rating: 0,
    comment: ''
});

const urlParams = typeof window !== 'undefined' ? new URLSearchParams(window.location.search) : null;
const fromEventId = urlParams ? urlParams.get('from_event') : null;

const goBack = () => {
    if (fromEventId) {
        visitWithoutStack(route('events.show', fromEventId));
        return;
    }
    smartBack(route('routes.index'));
};

const userHasReviewed = computed(() => {
    if (!props.mapRoute || !props.mapRoute.reviews || !usePage().props.auth.user) return false;
    return props.mapRoute.reviews.some(r => r.user_id === usePage().props.auth.user.id);
});

const submitReview = () => {
    reviewForm.post(route('routes.reviews.store', props.mapRoute.id), {
        preserveScroll: true,
        onSuccess: () => {
            reviewForm.reset();
        }
    });
};

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

const writeToClipboard = async (text) => {
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
            return true;
        }
    } catch (e) { /* fallthrough */ }
    try {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        const ok = document.execCommand('copy');
        document.body.removeChild(ta);
        return ok;
    } catch (e) {
        return false;
    }
};

const copyShareLink = async () => {
    if (!props.mapRoute?.id) return;

    // URL publica amb share_token (no requereix login i no es endevinable).
    // Fallback a la URL protegida nomes si no hi ha token (no hauria de passar).
    const shareToken = props.mapRoute.share_token;
    const shareUrl = shareToken
        ? `${window.location.origin}/r/${shareToken}`
        : `${window.location.origin}/routes/${props.mapRoute.id}`;
    const shareTitle = props.mapRoute.title || 'Ruta compartida';
    const shareText = `Mira aquesta ruta: ${shareTitle}`;

    if (navigator.share) {
        try {
            await navigator.share({
                title: shareTitle,
                text: shareText,
                url: shareUrl,
            });
            return;
        } catch (error) {
            if (error?.name === 'AbortError') return;
        }
    }

    const ok = await writeToClipboard(shareUrl);
    if (ok) {
        copyLinkSuccess.value = true;
        setTimeout(() => {
            copyLinkSuccess.value = false;
        }, 3000);
    }
};

/** Qui l'ha feta, on i si és pública: context, no xips. */
const subtitleLine = computed(() => {
    const parts = [];
    if (props.mapRoute?.location_city) parts.push(props.mapRoute.location_city);
    if (props.mapRoute?.user?.name) parts.push(props.mapRoute.user.name);
    if (props.motorcycle) parts.push(props.motorcycle.alias || props.motorcycle.model);
    if (!props.mapRoute?.is_public) parts.push(t('routes.private_badge'));

    return parts.join(' · ');
});

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
    if (!props.mapRoute?.waypoints?.length) return '#';

    const waypoints = props.mapRoute.waypoints.map((wp) => ({
        lat: parseFloat(wp.lat ?? wp.latitude),
        lng: parseFloat(wp.lng ?? wp.longitude),
    })).filter((wp) => Number.isFinite(wp.lat) && Number.isFinite(wp.lng));

    if (waypoints.length < 2) return '#';

    const geoPoints = parseLatLngPath(props.mapRoute.geo_json);
    const isLoop = isClosedLoopRoute(waypoints, geoPoints);

    return buildGoogleMapsDirectionsUrl(waypoints, { isLoop });
});

onMounted(async () => {
    await nextTick();
    if (!props.mapRoute) return;

    const startLat = parseFloat(props.mapRoute.starting_lat || 41.3851);
    const startLng = parseFloat(props.mapRoute.starting_lng || 2.1734);

    const mapElement = document.getElementById('map-detail');
    if(!mapElement) return;

    map.value = L.map('map-detail', { zoomControl: false, attributionControl: false }).setView([startLat, startLng], 13);

    addMapTileLayer(map.value, L);

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
                color: '#fafafa',
                weight: 5,
                opacity: 0.95,
                lineJoin: 'round'
            }).addTo(map.value);

            const first = validPoints[0];
            const last = validPoints[validPoints.length - 1];

            L.circleMarker([first[0], first[1]], { radius: 6, color: '#0a0a0a', weight: 3, fillColor: '#fafafa', fillOpacity: 1 }).addTo(map.value);
            L.circleMarker([last[0], last[1]], { radius: 6, color: '#fafafa', weight: 2, fillColor: '#0a0a0a', fillOpacity: 1 }).addTo(map.value);

            try {
                map.value.fitBounds(polyline.getBounds(), { padding: [50, 150] });
            } catch (e) {
                console.warn("No s'ha pogut centrar el mapa automàticament");
            }
        }
    }
});

// Carregar els recorreguts de l'usuari sobre aquesta ruta
onMounted(async () => {
    if (props.mapRoute?.id) {
        try {
            const { data } = await axios.get(route('routes.trips', props.mapRoute.id));
            myRouteTrips.value = data;
        } catch (e) {
            console.warn('No s\'han pogut carregar els recorreguts', e);
        }
    }
});

const formatTripDate = (isoStr) => {
    if (!isoStr) return '';
    return new Intl.DateTimeFormat('ca-ES', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(isoStr));
};

const formatTripDuration = (sec) => {
    if (!sec) return '0m';
    const hrs = Math.floor(sec / 3600);
    const mins = Math.floor((sec % 3600) / 60);
    if (hrs > 0) return `${hrs}h ${mins}m`;
    return `${mins}m`;
};

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
    recordedDistance.value = 0;
    recordingTime.value = 0;

    // Usem timestamp real: setInterval + 1 s'endarrereix quan l'app va al background
    recordingStartTime = Date.now();
    if (timerInterval) clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        recordingTime.value = Math.floor((Date.now() - recordingStartTime) / 1000);
    }, 1000);

    // Prepare Polyline on map
    if (map.value) {
        if (trackingPolyline.value) map.value.removeLayer(trackingPolyline.value);
        if (currentLocationMarker.value) map.value.removeLayer(currentLocationMarker.value);

        trackingPolyline.value = L.polyline([], {
            color: '#ef4444', // Vermell/Taronja viu per contrastar amb la original
            weight: 5,
            opacity: 0.9,
            dashArray: '10, 10', // Una mica puntejat per donar sensació de telemetria
            lineJoin: 'round'
        }).addTo(map.value);

        currentLocationMarker.value = L.circleMarker([0, 0], {
            radius: 7, color: '#ffffff', fillColor: '#ef4444', weight: 2, fillOpacity: 1
        }).addTo(map.value);
    }

    BackgroundGeolocation.addWatcher(
        {
            backgroundMessage: "Clutch Control està seguint els teus passos...",
            backgroundTitle: "Seguiment de Ruta Actiu",
            requestPermissions: true,
            stale: false,
            distanceFilter: 10 // Punts cada 10 metres
        },
        function callback(location, error) {
            if (error) {
                if (error.code === "NOT_AUTHORIZED") {
                    if (window.confirm("Clutch Control necessita la teva ubicació. Vols obrir la configuració per posar-ho a 'Sempre'?")) {
                        BackgroundGeolocation.openSettings();
                    }
                }
                console.error("Error BackgroundGeolocation:", error);
                return;
            }

            if (location) {
                const newLatLng = L.latLng(location.latitude, location.longitude);
                
                // Mirem si hi havia un punt anterior per calcular distància línia recta
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
                    
                    // Opcional: Centrar el mapa a la nova posició de l'usuari automàticament
                    map.value.panTo(newLatLng, { animate: true, duration: 1 });
                }
            }
        }
    ).then(function afterTheWatcherHasBeenAdded(watcher_id) {
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
                route_id: props.mapRoute?.id || null, // Vinculat a aquesta ruta!
                motorcycle_id: usePage().props.auth.user?.last_motorcycle_id || null, // De LA TEVA moto, no de l'autor de la ruta
                distance_km: parseFloat(distanceKm),
                duration_seconds: recordingTime.value,
                waypoints: recordedWaypoints.value
            };

            existingPending.push(newPendingTrip);
            localStorage.setItem('pending_trips', JSON.stringify(existingPending));

            alert(`📍 Recorregut aturat!\nHas recorregut ${distanceKm} km. El trajecte s'ha guardat localment. Ves a "Les Meves Rutes" per sincronitzar-lo quan tinguis connexió.`);
        } catch (e) {
            console.error('Error saving offline trip:', e);
            alert(`📍 Recorregut aturat!\nHas recorregut ${distanceKm} km, però hi ha hagut un error guardant-lo localment.`);
        }
    } else {
        alert("S'ha aturat el seguiment, però no t'has mogut com per registrar la distància.");
        if (trackingPolyline.value) map.value.removeLayer(trackingPolyline.value);
        if (currentLocationMarker.value) map.value.removeLayer(currentLocationMarker.value);
    }
};
</script>

<style>
.pt-safe-top { padding-top: env(safe-area-inset-top, 40px); }
.top-safe-top { top: calc(env(safe-area-inset-top, 40px) + 1rem); }
.pb-safe-bottom { padding-bottom: env(safe-area-inset-bottom, 20px); }
</style>

<template>
    <AppLayout :title="$t('routes.trip_detail_title')">
        <div class="fixed top-0 left-0 w-full h-[100dvh] bg-gray-900 overflow-hidden overscroll-none z-[5000]">

            <div id="trip-map" class="absolute inset-0 z-0 bg-gray-900"></div>

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

            <button
                type="button"
                @click="deleteTrip"
                class="cc-icon-btn absolute top-safe-top right-4 z-[5010] mt-2 bg-black/50 backdrop-blur-md border-white/20 text-red-400"
                :aria-label="$t('common.delete')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </button>

            <div
                v-if="trip.route && !trip.manual_entry"
                class="absolute top-safe-top right-16 z-[5010] mt-2"
            >
                <button
                    type="button"
                    @click="toggleComparativa"
                    class="cc-btn-text bg-black/50 backdrop-blur-md"
                >
                    {{ showComparativa ? $t('routes.hide_on_map') : $t('routes.compare_route') }}
                </button>
            </div>

            <div class="absolute bottom-0 left-0 w-full z-10">
                <div class="mx-3 mb-3 rounded-3xl bg-brand-black/95 backdrop-blur-xl border border-white/[0.08] overflow-hidden pb-safe-bottom cc-fade-in">

                    <button
                        type="button"
                        @click="isExpanded = !isExpanded"
                        class="w-full px-5 pt-4 pb-1 flex items-start gap-3 text-left"
                    >
                        <span class="min-w-0 flex-1">
                            <span class="block text-[19px] font-medium text-white leading-snug">
                                {{ formatDate(trip.started_at) }}
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

                    <div class="px-5 pt-4 flex items-baseline gap-8">
                        <span>
                            <span class="block text-[26px] font-light leading-none tabular-nums text-white">
                                {{ trip.distance_km ?? '—' }}<span class="text-sm text-gray-500 ml-1">km</span>
                            </span>
                        </span>
                        <span>
                            <span class="block text-[26px] font-light leading-none tabular-nums text-white">
                                {{ formatDuration(trip.duration_seconds) }}
                            </span>
                        </span>
                        <span v-if="trip.manual_entry" class="ml-auto text-sm text-gray-500">
                            {{ $t('routes.manual_badge') }}
                        </span>
                    </div>

                    <div v-if="trip.route" class="px-5 pt-5 pb-5">
                        <Link :href="route('routes.show', trip.route.id)" class="cc-btn-primary w-full py-3 text-center">
                            {{ trip.route.title }}
                        </Link>
                    </div>
                    <div v-else class="pb-5"></div>

                    <div v-show="isExpanded" class="border-t border-white/[0.06] overflow-y-auto max-h-[40vh] overscroll-contain">
                        <div v-if="trip.motorcycle" class="px-5 pt-5">
                            <p class="cc-section-label">{{ $t('routes.motorcycle') }}</p>
                            <p class="mt-2 text-sm text-gray-300">
                                {{ [trip.motorcycle.brand, trip.motorcycle.model].filter(Boolean).join(' ') }}
                            </p>
                        </div>

                        <div v-if="trip.notes" class="px-5 pt-5 pb-5">
                            <p class="cc-section-label">{{ $t('routes.habitual_notes') }}</p>
                            <p class="mt-2 text-sm text-gray-400 leading-relaxed">{{ trip.notes }}</p>
                        </div>
                        <div v-else class="pb-5"></div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { smartBack } from '@/Composables/navigationStack.js';
import { addMapTileLayer } from '@/config/mapTiles.js';

const { t, locale } = useI18n();

const props = defineProps({
    trip: Object,
});

const goBack = () => smartBack(route('routes.history'));

const map = ref(null);
const showComparativa = ref(false);
const isExpanded = ref(false);
let tripPolyline = null;
let routePolyline = null;

const subtitleLine = computed(() => {
    const parts = [];
    if (props.trip.route?.title) parts.push(props.trip.route.title);
    if (props.trip.motorcycle) {
        parts.push([props.trip.motorcycle.brand, props.trip.motorcycle.model].filter(Boolean).join(' '));
    }
    return parts.join(' · ') || t('routes.trip_detail_title');
});

onMounted(async () => {
    await buildMap();
});

const drawRouteGeoJson = (geoJson) => {
    try {
        let data = geoJson;
        if (typeof data === 'string') {
            try {
                data = JSON.parse(data);
                if (typeof data === 'string') data = JSON.parse(data);
            } catch (e) {}
        }

        const points = Array.isArray(data) ? data : [];
        const validPoints = points.map((p) => {
            if (Array.isArray(p) && p.length >= 2) return [p[1], p[0]];
            if (p && (p.lat !== undefined || p.latitude !== undefined)) return [p.lat ?? p.latitude, p.lng ?? p.longitude];
            if (Array.isArray(p) && p.length === 2 && p[0] <= 90 && p[0] >= -90) return [p[0], p[1]];
            return null;
        }).filter(Boolean);

        if (routePolyline) map.value.removeLayer(routePolyline);

        if (validPoints.length > 0) {
            routePolyline = L.polyline(validPoints, {
                color: '#fafafa',
                weight: 4,
                dashArray: '8 6',
                opacity: 0.55,
            }).addTo(map.value);
            map.value.fitBounds(routePolyline.getBounds(), { padding: [30, 30] });
        }
    } catch (e) {
        console.warn('Error carregant GeoJSON de la ruta', e);
    }
};

const buildMap = async () => {
    const startLat = props.trip.starting_lat ?? (props.trip.waypoints?.[0]?.lat) ?? 41.3851;
    const startLng = props.trip.starting_lng ?? (props.trip.waypoints?.[0]?.lng) ?? 2.1734;

    map.value = L.map('trip-map', { zoomControl: false, attributionControl: false }).setView([startLat, startLng], 13);
    addMapTileLayer(map.value, L);

    if (props.trip.waypoints && props.trip.waypoints.length > 1) {
        const latlngs = props.trip.waypoints.map((p) => [p.lat, p.lng]);
        tripPolyline = L.polyline(latlngs, {
            color: props.trip.manual_entry ? '#a3a3a3' : '#ef4444',
            weight: 5,
            opacity: 0.95,
            lineJoin: 'round',
        }).addTo(map.value);
        map.value.fitBounds(tripPolyline.getBounds(), { padding: [30, 30] });

        L.circleMarker(latlngs[0], { radius: 7, color: '#0a0a0a', fillColor: '#fafafa', fillOpacity: 1, weight: 3 })
            .addTo(map.value);
        L.circleMarker(latlngs[latlngs.length - 1], {
            radius: 7,
            color: '#ffffff',
            fillColor: props.trip.manual_entry ? '#a3a3a3' : '#ef4444',
            fillOpacity: 1,
            weight: 2,
        }).addTo(map.value);
    } else if (props.trip.manual_entry && props.trip.route?.geo_json) {
        drawRouteGeoJson(props.trip.route.geo_json);
    }
};

const toggleComparativa = () => {
    showComparativa.value = !showComparativa.value;
    if (showComparativa.value && props.trip.route?.geo_json) {
        drawRouteGeoJson(props.trip.route.geo_json);
    } else if (!showComparativa.value && routePolyline) {
        map.value.removeLayer(routePolyline);
        routePolyline = null;
    }
};

const deleteTrip = async () => {
    if (!confirm(t('routes.delete_trip_confirm'))) return;
    router.delete(route('trips.destroy', props.trip.id));
};

const formatDate = (isoStr) => {
    if (!isoStr) return '';
    return new Intl.DateTimeFormat(locale.value, { dateStyle: 'long', timeStyle: 'short' }).format(new Date(isoStr));
};

const formatDuration = (sec) => {
    if (!sec) return '0m';
    const hrs = Math.floor(sec / 3600);
    const mins = Math.floor((sec % 3600) / 60);
    if (hrs > 0) return `${hrs}h ${mins}m`;
    return `${mins}m`;
};

onUnmounted(() => {
    // Alliberem el mapa: si no, en tornar-hi Leaflet troba el contenidor ocupat.
    if (map.value) {
        map.value.remove();
        map.value = null;
    }
    tripPolyline = null;
    routePolyline = null;
});
</script>

<style scoped>
#trip-map { z-index: 10; }
</style>

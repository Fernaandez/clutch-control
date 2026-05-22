<template>
    <AppLayout :title="$t('routes.plan_title')">
        <div class="max-w-2xl mx-auto px-4 py-6 pb-24">
            <div class="flex items-center gap-3 mb-6">
                <button type="button" @click="goBack" class="inline-flex items-center justify-center w-10 h-10 flex-shrink-0 rounded-full bg-brand-dark border border-brand-neon/50 text-brand-neon hover:bg-brand-neon hover:text-brand-black transition shadow-[0_0_10px_rgba(12,225,181,0.2)]" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                </button>
                <div>
                    <h1 class="text-2xl font-black uppercase tracking-tighter text-white leading-none">{{ $t('routes.plan_title') }}</h1>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">{{ $t('routes.plan_subtitle') }}</p>
                </div>
            </div>

            <div v-if="!hasOrsApiKey()" class="mb-4 p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-xl">
                <p class="text-yellow-400 text-sm">{{ $t('routes.plan_no_api_key') }}</p>
            </div>

            <div v-if="errorMessage" class="mb-4 p-4 bg-red-500/10 border border-red-500/30 rounded-xl">
                <p class="text-red-400 text-sm">{{ errorMessage }}</p>
            </div>

            <!-- Tipus de trajecte -->
            <div class="flex gap-2 mb-4">
                <button
                    type="button"
                    class="flex-1 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest border transition"
                    :class="tripType === 'p2p' ? 'bg-brand-neon text-black border-brand-neon' : 'bg-brand-surface text-gray-400 border-brand-dark'"
                    @click="tripType = 'p2p'"
                >
                    {{ $t('routes.plan_p2p') }}
                </button>
                <button
                    type="button"
                    disabled
                    class="flex-1 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest border border-brand-dark bg-brand-surface/40 text-gray-600 opacity-60 cursor-not-allowed relative"
                    :title="$t('routes.plan_loop_soon')"
                >
                    {{ $t('routes.plan_loop') }}
                    <span class="absolute -top-1 -right-1 text-[8px] bg-brand-dark px-1 rounded">{{ $t('routes.hub_coming_soon') }}</span>
                </button>
            </div>

            <div class="space-y-4">
                <!-- Temps objectiu -->
                <div class="bg-brand-surface p-5 rounded-2xl border border-brand-dark">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-3">{{ $t('routes.plan_duration') }}</label>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="mins in durationOptions"
                            :key="mins"
                            type="button"
                            class="px-3 py-2 rounded-lg text-xs font-bold uppercase border transition"
                            :class="durationMinutes === mins ? 'bg-brand-neon text-black border-brand-neon' : 'bg-brand-black text-gray-400 border-brand-dark hover:border-gray-500'"
                            @click="durationMinutes = mins"
                        >
                            {{ formatDurationLabel(mins) }}
                        </button>
                    </div>
                </div>

                <!-- Mode punt a punt -->
                <div class="bg-brand-surface p-5 rounded-2xl border border-brand-dark">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-3">{{ $t('routes.plan_p2p_mode') }}</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <button
                            type="button"
                            class="p-3 rounded-xl border text-left transition"
                            :class="p2pMode === 'direct' ? 'border-brand-neon bg-brand-neon/10' : 'border-brand-dark bg-brand-black'"
                            @click="p2pMode = 'direct'"
                        >
                            <span class="block text-white text-xs font-bold uppercase">{{ $t('routes.plan_mode_direct') }}</span>
                            <span class="block text-[10px] text-gray-500 mt-1">{{ $t('routes.plan_mode_direct_desc') }}</span>
                        </button>
                        <button
                            type="button"
                            class="p-3 rounded-xl border text-left transition"
                            :class="p2pMode === 'time_fit' ? 'border-brand-neon bg-brand-neon/10' : 'border-brand-dark bg-brand-black'"
                            @click="p2pMode = 'time_fit'"
                        >
                            <span class="block text-white text-xs font-bold uppercase">{{ $t('routes.plan_mode_time') }}</span>
                            <span class="block text-[10px] text-gray-500 mt-1">{{ $t('routes.plan_mode_time_desc') }}</span>
                        </button>
                    </div>
                </div>

                <!-- Preferències -->
                <div class="bg-brand-surface p-5 rounded-2xl border border-brand-dark space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">{{ $t('routes.plan_highway') }}</label>
                        <div class="flex gap-2">
                            <button
                                type="button"
                                class="flex-1 py-2 rounded-lg text-[10px] font-bold uppercase border transition"
                                :class="highway === 'allow' ? 'bg-brand-neon text-black border-brand-neon' : 'bg-brand-black text-gray-400 border-brand-dark'"
                                @click="highway = 'allow'"
                            >
                                {{ $t('routes.plan_highway_allow') }}
                            </button>
                            <button
                                type="button"
                                class="flex-1 py-2 rounded-lg text-[10px] font-bold uppercase border transition"
                                :class="highway === 'avoid' ? 'bg-brand-neon text-black border-brand-neon' : 'bg-brand-black text-gray-400 border-brand-dark'"
                                @click="highway = 'avoid'"
                            >
                                {{ $t('routes.plan_highway_avoid') }}
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">{{ $t('routes.plan_road_style') }}</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button
                                v-for="style in roadStyles"
                                :key="style.value"
                                type="button"
                                class="py-2 px-1 rounded-lg text-[10px] font-bold uppercase border transition"
                                :class="roadStyle === style.value ? 'bg-brand-neon text-black border-brand-neon' : 'bg-brand-black text-gray-400 border-brand-dark'"
                                @click="roadStyle = style.value"
                            >
                                {{ $t(style.labelKey) }}
                            </button>
                        </div>
                        <p class="text-[10px] text-gray-600 mt-2">{{ $t('routes.plan_asphalt_only') }}</p>
                    </div>
                </div>

                <!-- Origen i destí -->
                <div class="bg-brand-surface p-5 rounded-2xl border border-brand-dark space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-bold text-gray-400 uppercase">{{ $t('routes.plan_origin') }}</label>
                            <button type="button" class="text-[10px] font-bold text-brand-neon uppercase" @click="useMyLocation">
                                {{ $t('routes.plan_use_gps') }}
                            </button>
                        </div>
                        <input
                            v-model="originQuery"
                            type="text"
                            class="w-full bg-brand-black border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon"
                            :placeholder="$t('routes.plan_search_placeholder')"
                            @input="searchOrigin"
                        />
                        <p v-if="origin" class="text-[10px] text-brand-neon mt-1 truncate">✓ {{ origin.name }}</p>
                        <ul v-if="originResults.length" class="mt-2 border border-brand-dark rounded-lg overflow-hidden">
                            <li
                                v-for="(result, idx) in originResults"
                                :key="'o-' + idx"
                                class="p-2 text-xs text-gray-300 hover:bg-brand-dark cursor-pointer truncate"
                                @click="selectOrigin(result)"
                            >
                                {{ result.display_name }}
                            </li>
                        </ul>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">{{ $t('routes.plan_destination') }}</label>
                        <input
                            v-model="destQuery"
                            type="text"
                            class="w-full bg-brand-black border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon"
                            :placeholder="$t('routes.plan_search_placeholder')"
                            @input="searchDestination"
                        />
                        <p v-if="destination" class="text-[10px] text-brand-neon mt-1 truncate">✓ {{ destination.name }}</p>
                        <ul v-if="destResults.length" class="mt-2 border border-brand-dark rounded-lg overflow-hidden">
                            <li
                                v-for="(result, idx) in destResults"
                                :key="'d-' + idx"
                                class="p-2 text-xs text-gray-300 hover:bg-brand-dark cursor-pointer truncate"
                                @click="selectDestination(result)"
                            >
                                {{ result.display_name }}
                            </li>
                        </ul>
                    </div>
                </div>

                <button
                    type="button"
                    :disabled="isGenerating || !canGenerate"
                    class="w-full bg-white text-black font-black py-4 rounded-xl uppercase tracking-widest hover:bg-gray-200 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    @click="generateProposals"
                >
                    {{ isGenerating ? $t('routes.plan_generating') : $t('routes.plan_generate') }}
                </button>
            </div>

            <!-- Resultats -->
            <div v-if="proposals.length" class="mt-8 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-white font-black uppercase tracking-widest text-sm">{{ $t('routes.plan_results') }}</h2>
                    <button type="button" class="text-[10px] font-bold text-brand-neon uppercase" @click="generateProposals">
                        {{ $t('routes.plan_regenerate') }}
                    </button>
                </div>

                <div id="plan-map" class="h-48 rounded-xl border border-brand-dark overflow-hidden bg-gray-900"></div>

                <div class="space-y-3">
                    <button
                        v-for="proposal in proposals"
                        :key="proposal.id"
                        type="button"
                        class="w-full text-left p-4 rounded-xl border transition"
                        :class="selectedId === proposal.id ? 'border-brand-neon bg-brand-neon/10' : 'border-brand-dark bg-brand-surface hover:border-gray-600'"
                        @click="selectProposal(proposal)"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-white font-bold text-sm">{{ proposal.label }}</p>
                                <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-1">
                                    {{ proposalTag(proposal) }}
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-brand-neon font-mono font-bold">{{ proposal.distanceKm }} km</p>
                                <p class="text-white text-xs font-mono">{{ formatDuration(proposal.durationSeconds) }}</p>
                            </div>
                        </div>
                        <p
                            v-if="p2pMode === 'time_fit' && timeDiffLabel(proposal)"
                            class="text-[10px] mt-2"
                            :class="Math.abs(proposal.durationSeconds - targetDurationSeconds) <= 900 ? 'text-green-400' : 'text-yellow-500'"
                        >
                            {{ timeDiffLabel(proposal) }}
                        </p>
                    </button>
                </div>

                <button
                    type="button"
                    :disabled="!selectedId"
                    class="w-full bg-brand-neon text-black font-black py-4 rounded-xl uppercase tracking-widest hover:bg-white transition disabled:opacity-50"
                    @click="continueToCreate"
                >
                    {{ $t('routes.plan_use_route') }}
                </button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, watch, nextTick, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { Geolocation } from '@capacitor/geolocation';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import { smartBack } from '@/Composables/navigationStack.js';
import { addMapTileLayer } from '@/config/mapTiles.js';
import { fetchRouteProposals, hasOrsApiKey } from '@/services/openRouteService.js';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const { t } = useI18n();

const DRAFT_KEY = 'clutch_planned_route';

const tripType = ref('p2p');
const durationMinutes = ref(120);
const p2pMode = ref('direct');
const highway = ref('avoid');
const roadStyle = ref('balanced');
const durationOptions = [30, 60, 90, 120, 180, 240, 360];

const roadStyles = [
    { value: 'fast', labelKey: 'routes.plan_style_fast' },
    { value: 'balanced', labelKey: 'routes.plan_style_balanced' },
    { value: 'scenic', labelKey: 'routes.plan_style_scenic' },
];

const origin = ref(null);
const destination = ref(null);
const originQuery = ref('');
const destQuery = ref('');
const originResults = ref([]);
const destResults = ref([]);

const proposals = ref([]);
const selectedId = ref(null);
const isGenerating = ref(false);
const errorMessage = ref('');

const map = ref(null);
const routeLayers = ref([]);

let originTimeout = null;
let destTimeout = null;

const targetDurationSeconds = computed(() => durationMinutes.value * 60);

const canGenerate = computed(() => hasOrsApiKey() && origin.value && destination.value);

const goBack = () => smartBack(route('routes.index'));

const formatDurationLabel = (mins) => {
    if (mins < 60) return `${mins} min`;
    const h = Math.floor(mins / 60);
    const m = mins % 60;
    return m ? `${h}h ${m}m` : `${h}h`;
};

const formatDuration = (seconds) => {
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    if (h) return `${h}h ${m}m`;
    return `${m} min`;
};

const proposalTag = () => {
    const parts = [];
    if (highway.value === 'avoid') parts.push(t('routes.plan_highway_avoid'));
    else parts.push(t('routes.plan_highway_allow'));
    parts.push(t(`routes.plan_style_${roadStyle.value}`));
    return parts.join(' · ');
};

const timeDiffLabel = (proposal) => {
    const diff = proposal.durationSeconds - targetDurationSeconds.value;
    const abs = Math.abs(diff);
    const mins = Math.round(abs / 60);
    if (mins <= 15) return t('routes.plan_time_match');
    if (diff > 0) return t('routes.plan_time_over', { n: mins });
    return t('routes.plan_time_under', { n: mins });
};

const nominatimSearch = async (query) => {
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&addressdetails=1&limit=5`;
    const response = await fetch(url);
    return response.json();
};

const searchOrigin = () => {
    if (originTimeout) clearTimeout(originTimeout);
    if (originQuery.value.length < 3) {
        originResults.value = [];
        return;
    }
    originTimeout = setTimeout(async () => {
        originResults.value = await nominatimSearch(originQuery.value);
    }, 450);
};

const searchDestination = () => {
    if (destTimeout) clearTimeout(destTimeout);
    if (destQuery.value.length < 3) {
        destResults.value = [];
        return;
    }
    destTimeout = setTimeout(async () => {
        destResults.value = await nominatimSearch(destQuery.value);
    }, 450);
};

const pickPlace = (result) => ({
    lat: parseFloat(result.lat),
    lng: parseFloat(result.lon),
    name: result.display_name.split(',')[0],
});

const selectOrigin = (result) => {
    origin.value = pickPlace(result);
    originQuery.value = origin.value.name;
    originResults.value = [];
};

const selectDestination = (result) => {
    destination.value = pickPlace(result);
    destQuery.value = destination.value.name;
    destResults.value = [];
};

const useMyLocation = async () => {
    try {
        const perm = await Geolocation.checkPermissions();
        if (perm.location !== 'granted') {
            await Geolocation.requestPermissions();
        }
        const pos = await Geolocation.getCurrentPosition({ enableHighAccuracy: true });
        origin.value = {
            lat: pos.coords.latitude,
            lng: pos.coords.longitude,
            name: t('routes.plan_my_location'),
        };
        originQuery.value = origin.value.name;
        originResults.value = [];
    } catch {
        errorMessage.value = t('routes.plan_gps_error');
    }
};

const styleLabelPrefix = () => {
    if (roadStyle.value === 'fast') return t('routes.plan_style_fast');
    if (roadStyle.value === 'scenic') return t('routes.plan_style_scenic');
    return t('routes.plan_style_balanced');
};

const generateProposals = async () => {
    if (!canGenerate.value) return;

    isGenerating.value = true;
    errorMessage.value = '';
    proposals.value = [];
    selectedId.value = null;
    clearMapLayers();

    try {
        const results = await fetchRouteProposals({
            origin: origin.value,
            destination: destination.value,
            highway: highway.value,
            roadStyle: roadStyle.value,
            p2pMode: p2pMode.value,
            targetDurationSeconds: targetDurationSeconds.value,
            labelPrefix: styleLabelPrefix(),
        });

        if (!results.length) {
            errorMessage.value = t('routes.plan_no_results');
            return;
        }

        proposals.value = results;
        selectedId.value = results[0].id;
        await nextTick();
        renderMap();
    } catch (err) {
        if (err.message === 'ORS_API_KEY_MISSING') {
            errorMessage.value = t('routes.plan_no_api_key');
        } else {
            errorMessage.value = t('routes.plan_error', { msg: err.message });
        }
    } finally {
        isGenerating.value = false;
    }
};

const initMap = () => {
    if (map.value) return;
    const el = document.getElementById('plan-map');
    if (!el) return;

    map.value = L.map(el, { zoomControl: false, attributionControl: false }).setView([41.3851, 2.1734], 8);
    addMapTileLayer(map.value, L);
};

const clearMapLayers = () => {
    routeLayers.value.forEach((layer) => map.value?.removeLayer(layer));
    routeLayers.value = [];
};

const selectProposal = (proposal) => {
    selectedId.value = proposal.id;
    renderMap();
};

const renderMap = () => {
    initMap();
    if (!map.value || !proposals.value.length) return;

    clearMapLayers();
    const colors = ['#0CE1B5', '#60a5fa', '#f472b6', '#fbbf24'];
    const bounds = L.latLngBounds([]);

    proposals.value.forEach((proposal, index) => {
        const latlngs = proposal.latLngs.map((p) => [p.lat, p.lng]);
        const isSelected = proposal.id === selectedId.value;
        const layer = L.polyline(latlngs, {
            color: colors[index % colors.length],
            weight: isSelected ? 6 : 3,
            opacity: isSelected ? 0.95 : 0.45,
        }).addTo(map.value);
        routeLayers.value.push(layer);
        latlngs.forEach((ll) => bounds.extend(ll));
    });

    if (origin.value) {
        const m = L.circleMarker([origin.value.lat, origin.value.lng], {
            radius: 6, color: '#fff', fillColor: '#22c55e', weight: 2, fillOpacity: 1,
        }).addTo(map.value);
        routeLayers.value.push(m);
        bounds.extend([origin.value.lat, origin.value.lng]);
    }
    if (destination.value) {
        const m = L.circleMarker([destination.value.lat, destination.value.lng], {
            radius: 6, color: '#fff', fillColor: '#ef4444', weight: 2, fillOpacity: 1,
        }).addTo(map.value);
        routeLayers.value.push(m);
        bounds.extend([destination.value.lat, destination.value.lng]);
    }

    if (bounds.isValid()) {
        map.value.fitBounds(bounds, { padding: [24, 24] });
    }
};

watch(proposals, async (list) => {
    if (list.length) {
        await nextTick();
        renderMap();
    }
});

const continueToCreate = () => {
    const proposal = proposals.value.find((p) => p.id === selectedId.value);
    if (!proposal) return;

    const title = `${origin.value?.name || 'Origen'} → ${destination.value?.name || 'Destí'}`;

    sessionStorage.setItem(DRAFT_KEY, JSON.stringify({
        title,
        planned_distance_km: proposal.distanceKm,
        duration_seconds: proposal.durationSeconds,
        geo_json: proposal.geoJson,
        waypoints: proposal.waypoints,
    }));

    router.visit(route('routes.create'));
};

onUnmounted(() => {
    if (map.value) {
        map.value.remove();
        map.value = null;
    }
});
</script>

<style scoped>
#plan-map {
    z-index: 0;
}
</style>

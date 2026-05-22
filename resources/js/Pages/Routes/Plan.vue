<template>
    <AppLayout :title="$t('routes.plan_title')">
        <div class="max-w-2xl mx-auto px-4 py-6 pb-24">
            <div class="flex items-center gap-3 mb-6">
                <button type="button" @click="goBack" class="inline-flex items-center justify-center w-10 h-10 flex-shrink-0 rounded-full bg-brand-dark border border-brand-neon/50 text-brand-neon hover:bg-brand-neon hover:text-brand-black transition shadow-[0_0_10px_rgba(12,225,181,0.2)]" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                </button>
                <div>
                    <h1 class="text-2xl font-black uppercase tracking-tighter text-white leading-none">{{ $t('routes.plan_title') }}</h1>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">{{ pageSubtitle }}</p>
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
                    @click="switchTripType('p2p')"
                >
                    {{ $t('routes.plan_p2p') }}
                </button>
                <button
                    type="button"
                    class="flex-1 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest border transition"
                    :class="tripType === 'loop' ? 'bg-brand-neon text-black border-brand-neon' : 'bg-brand-surface text-gray-400 border-brand-dark'"
                    @click="switchTripType('loop')"
                >
                    {{ $t('routes.plan_loop') }}
                </button>
            </div>

            <div class="space-y-4">
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
                        <p v-if="highway === 'avoid'" class="text-[10px] text-gray-600 mt-2">{{ $t('routes.plan_highway_avoid_hint') }}</p>
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

                <!-- Origen / punt d'inici -->
                <div class="bg-brand-surface p-5 rounded-2xl border border-brand-dark space-y-4">
                    <div v-if="tripType === 'loop'" class="pb-1 border-b border-brand-dark/60 space-y-3">
                        <label class="block text-xs font-bold text-gray-400 uppercase">{{ $t('routes.plan_duration') }}</label>
                        <input
                            v-model.number="loopDurationMinutes"
                            type="range"
                            min="45"
                            max="480"
                            step="15"
                            class="w-full h-2 rounded-lg appearance-none cursor-pointer accent-brand-neon bg-brand-black"
                        />
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-brand-neon">{{ formatLoopDuration(loopDurationMinutes) }}</span>
                            <span class="text-[10px] text-gray-500">~{{ estimatedLoopKm }} km</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <button
                                v-for="preset in loopDurationPresets"
                                :key="preset.minutes"
                                type="button"
                                class="py-2 rounded-lg text-[10px] font-bold uppercase border transition"
                                :class="loopDurationMinutes === preset.minutes ? 'bg-brand-neon text-black border-brand-neon' : 'bg-brand-black text-gray-400 border-brand-dark'"
                                @click="loopDurationMinutes = preset.minutes"
                            >
                                {{ $t(preset.labelKey) }}
                            </button>
                        </div>
                        <p class="text-[10px] text-gray-600">{{ $t('routes.plan_loop_hint') }}</p>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-bold text-gray-400 uppercase">
                                {{ tripType === 'loop' ? $t('routes.plan_loop_start') : $t('routes.plan_origin') }}
                            </label>
                            <button type="button" class="text-[10px] font-bold text-brand-neon uppercase" @click="useMyLocation">
                                {{ $t('routes.plan_use_gps') }}
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <input
                                v-model="originQuery"
                                type="text"
                                class="flex-1 min-w-0 bg-brand-black border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon"
                                :placeholder="$t('routes.plan_search_placeholder')"
                                @input="searchOrigin"
                            />
                            <button
                                type="button"
                                class="flex-shrink-0 w-11 h-[42px] rounded-lg border border-brand-dark bg-brand-black text-brand-neon hover:border-brand-neon hover:bg-brand-dark transition flex items-center justify-center"
                                :title="$t('routes.plan_pick_on_map')"
                                @click="openMapModal('origin')"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                            </button>
                        </div>
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
                    <div v-if="tripType === 'p2p'">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">{{ $t('routes.plan_destination') }}</label>
                        <div class="flex gap-2">
                            <input
                                v-model="destQuery"
                                type="text"
                                class="flex-1 min-w-0 bg-brand-black border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon"
                                :placeholder="$t('routes.plan_search_placeholder')"
                                @input="searchDestination"
                            />
                            <button
                                type="button"
                                class="flex-shrink-0 w-11 h-[42px] rounded-lg border border-brand-dark bg-brand-black text-brand-neon hover:border-brand-neon hover:bg-brand-dark transition flex items-center justify-center"
                                :title="$t('routes.plan_pick_on_map')"
                                @click="openMapModal('destination')"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                            </button>
                        </div>
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

                <div v-if="longRouteNotice" class="p-3 bg-brand-black/60 border border-brand-dark rounded-xl">
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest">{{ longRouteNotice }}</p>
                </div>

                <div id="plan-map" ref="resultMapEl" class="h-48 rounded-xl border border-brand-dark overflow-hidden bg-gray-900"></div>

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
                                    <span v-if="proposal.isLoop && loopTimeNote(proposal)" class="text-brand-neon"> · {{ loopTimeNote(proposal) }}</span>
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-brand-neon font-mono font-bold">{{ proposal.distanceKm }} km</p>
                                <p class="text-white text-xs font-mono">{{ formatDuration(proposal.durationSeconds) }}</p>
                            </div>
                        </div>
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

        <Teleport to="body">
            <div v-if="mapModalOpen" class="fixed inset-0 z-[6000] flex items-end sm:items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="closeMapModal"></div>
                <div class="relative w-full max-w-lg bg-brand-surface border border-brand-dark rounded-2xl shadow-2xl overflow-hidden z-10">
                    <div class="flex items-center justify-between p-4 border-b border-brand-dark">
                        <div>
                            <p class="text-white font-black uppercase tracking-widest text-sm">
                                {{ mapModalOpen === 'destination' ? $t('routes.plan_destination') : (tripType === 'loop' ? $t('routes.plan_loop_start') : $t('routes.plan_origin')) }}
                            </p>
                            <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-1">{{ mapModalHint }}</p>
                        </div>
                        <button type="button" class="text-gray-400 hover:text-white p-2" @click="closeMapModal" :aria-label="$t('common.close')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div id="plan-picker-map" class="h-[55vh] sm:h-80 bg-gray-900"></div>
                </div>
            </div>
        </Teleport>
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
import { fetchRouteProposals, fetchLoopProposals, hasOrsApiKey, estimateLoopLengthMeters } from '@/services/openRouteService.js';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const { t } = useI18n();

const DRAFT_KEY = 'clutch_planned_route';

const tripType = ref('p2p');
const highway = ref('avoid');
const roadStyle = ref('balanced');
const loopDurationMinutes = ref(120);

const loopDurationPresets = [
    { minutes: 60, labelKey: 'routes.plan_loop_1h' },
    { minutes: 90, labelKey: 'routes.plan_loop_90m' },
    { minutes: 120, labelKey: 'routes.plan_loop_2h' },
    { minutes: 180, labelKey: 'routes.plan_loop_3h' },
    { minutes: 240, labelKey: 'routes.plan_loop_4h' },
    { minutes: 360, labelKey: 'routes.plan_loop_6h' },
];

const estimatedLoopKm = computed(() => (
    Math.round(estimateLoopLengthMeters(loopDurationMinutes.value, roadStyle.value) / 1000)
));

const formatLoopDuration = (minutes) => {
    const h = Math.floor(minutes / 60);
    const m = minutes % 60;
    if (h && m) return `${h}h ${m}m`;
    if (h) return `${h}h`;
    return `${m} min`;
};

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
const longRouteNotice = ref('');

const mapModalOpen = ref(null);
const pickerMap = ref(null);
const pickerMarker = ref(null);
const resultMap = ref(null);
const resultMapEl = ref(null);
const routeLayers = ref([]);

let originTimeout = null;
let destTimeout = null;

const canGenerate = computed(() => {
    if (!hasOrsApiKey() || !origin.value) return false;
    if (tripType.value === 'loop') return loopDurationMinutes.value >= 45;
    return !!destination.value;
});

const pageSubtitle = computed(() => (
    tripType.value === 'loop'
        ? t('routes.plan_loop_subtitle')
        : t('routes.plan_subtitle')
));

const mapModalHint = computed(() => {
    if (mapModalOpen.value === 'destination') {
        return t('routes.plan_map_pick_dest_hint');
    }
    if (tripType.value === 'loop') {
        return t('routes.plan_map_pick_loop_hint');
    }
    return t('routes.plan_map_pick_origin_hint');
});

const goBack = () => smartBack(route('routes.index'));

const formatDuration = (seconds) => {
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    if (h) return `${h}h ${m}m`;
    return `${m} min`;
};

const proposalTag = (proposal) => {
    const tag = proposal?.tag || { highway: highway.value, roadStyle: roadStyle.value };
    const parts = [];
    parts.push(tag.highway === 'avoid' ? t('routes.plan_highway_avoid') : t('routes.plan_highway_allow'));
    parts.push(t(`routes.plan_style_${tag.roadStyle}`));
    return parts.join(' · ');
};

const loopTimeNote = (proposal) => {
    const delta = proposal?.durationDeltaMinutes;
    if (delta == null) return '';
    if (Math.abs(delta) <= 5) return t('routes.plan_time_match');
    if (delta > 0) return t('routes.plan_time_over', { n: delta });
    return t('routes.plan_time_under', { n: Math.abs(delta) });
};

const resetResults = () => {
    proposals.value = [];
    selectedId.value = null;
    longRouteNotice.value = '';
    errorMessage.value = '';
};

const switchTripType = (type) => {
    if (tripType.value === type) return;
    tripType.value = type;
    resetResults();
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

const reverseGeocode = async (lat, lng) => {
    try {
        const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=14`;
        const response = await fetch(url);
        const data = await response.json();
        if (data?.display_name) {
            return data.display_name.split(',')[0];
        }
    } catch {
        // fallback below
    }
    return t('routes.plan_map_point');
};

const setPointFromMap = async (latlng) => {
    const name = await reverseGeocode(latlng.lat, latlng.lng);
    const point = { lat: latlng.lat, lng: latlng.lng, name };

    if (mapModalOpen.value === 'destination') {
        destination.value = point;
        destQuery.value = name;
        destResults.value = [];
    } else {
        origin.value = point;
        originQuery.value = name;
        originResults.value = [];
    }

    closeMapModal();
};

const destroyPickerMap = () => {
    if (pickerMap.value) {
        pickerMap.value.remove();
        pickerMap.value = null;
    }
    pickerMarker.value = null;
};

const openMapModal = async (target) => {
    mapModalOpen.value = target;
    await nextTick();
    destroyPickerMap();

    const el = document.getElementById('plan-picker-map');
    if (!el) return;

    const existing = target === 'destination' ? destination.value : origin.value;
    const center = existing
        ? [existing.lat, existing.lng]
        : (origin.value ? [origin.value.lat, origin.value.lng] : [41.3851, 2.1734]);
    const zoom = existing ? 12 : 8;

    pickerMap.value = L.map(el, { zoomControl: false, attributionControl: false }).setView(center, zoom);
    addMapTileLayer(pickerMap.value, L);

    pickerMap.value.on('click', (e) => {
        setPointFromMap(e.latlng);
    });

    if (existing) {
        pickerMarker.value = L.circleMarker([existing.lat, existing.lng], {
            radius: 8,
            color: '#fff',
            fillColor: target === 'destination' ? '#ef4444' : '#22c55e',
            weight: 2,
            fillOpacity: 1,
        }).addTo(pickerMap.value);
    }

    await nextTick();
    pickerMap.value.invalidateSize();
};

const closeMapModal = () => {
    mapModalOpen.value = null;
    destroyPickerMap();
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
    longRouteNotice.value = '';

    try {
        if (tripType.value === 'loop') {
            const { proposals: results } = await fetchLoopProposals({
                origin: origin.value,
                targetDurationMinutes: loopDurationMinutes.value,
                highway: highway.value,
                roadStyle: roadStyle.value,
                labelPrefix: t('routes.plan_loop'),
            });

            if (!results.length) {
                errorMessage.value = t('routes.plan_loop_no_results');
                return;
            }

            proposals.value = results;
            selectedId.value = results[0].id;
        } else {
            const { proposals: results, straightKm } = await fetchRouteProposals({
                origin: origin.value,
                destination: destination.value,
                highway: highway.value,
                roadStyle: roadStyle.value,
                labelPrefix: styleLabelPrefix(),
            });

            if (!results.length) {
                errorMessage.value = t('routes.plan_no_results');
                return;
            }

            if (straightKm >= 75) {
                longRouteNotice.value = t('routes.plan_long_route_notice');
            }

            proposals.value = results;
            selectedId.value = results[0].id;
        }

        await nextTick();
        renderResultMap();
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

const destroyResultMap = () => {
    if (resultMap.value) {
        resultMap.value.remove();
        resultMap.value = null;
    }
    routeLayers.value = [];
};

const initResultMap = () => {
    const el = resultMapEl.value || document.getElementById('plan-map');
    if (!el) return;

    if (resultMap.value) {
        resultMap.value.remove();
        resultMap.value = null;
    }

    resultMap.value = L.map(el, { zoomControl: false, attributionControl: false }).setView([41.3851, 2.1734], 8);
    addMapTileLayer(resultMap.value, L);
};

const clearResultMapLayers = () => {
    routeLayers.value.forEach((layer) => resultMap.value?.removeLayer(layer));
    routeLayers.value = [];
};

const selectProposal = (proposal) => {
    selectedId.value = proposal.id;
    renderResultMap();
};

const renderResultMap = () => {
    if (!proposals.value.length) return;

    initResultMap();
    if (!resultMap.value) return;

    clearResultMapLayers();
    const colors = ['#0CE1B5', '#60a5fa', '#f472b6', '#fbbf24'];
    const bounds = L.latLngBounds([]);

    proposals.value.forEach((proposal, index) => {
        const latlngs = proposal.latLngs.map((p) => [p.lat, p.lng]);
        const isSelected = proposal.id === selectedId.value;
        const layer = L.polyline(latlngs, {
            color: colors[index % colors.length],
            weight: isSelected ? 6 : 3,
            opacity: isSelected ? 0.95 : 0.45,
        }).addTo(resultMap.value);
        routeLayers.value.push(layer);
        latlngs.forEach((ll) => bounds.extend(ll));
    });

    if (origin.value) {
        const m = L.circleMarker([origin.value.lat, origin.value.lng], {
            radius: 6, color: '#fff', fillColor: tripType.value === 'loop' ? '#0CE1B5' : '#22c55e', weight: 2, fillOpacity: 1,
        }).addTo(resultMap.value);
        routeLayers.value.push(m);
        bounds.extend([origin.value.lat, origin.value.lng]);
    }
    if (tripType.value === 'p2p' && destination.value) {
        const m = L.circleMarker([destination.value.lat, destination.value.lng], {
            radius: 6, color: '#fff', fillColor: '#ef4444', weight: 2, fillOpacity: 1,
        }).addTo(resultMap.value);
        routeLayers.value.push(m);
        bounds.extend([destination.value.lat, destination.value.lng]);
    }

    if (bounds.isValid()) {
        resultMap.value.fitBounds(bounds, { padding: [24, 24] });
    }

    nextTick(() => resultMap.value?.invalidateSize());
};

watch(proposals, async (list) => {
    if (list.length) {
        await nextTick();
        renderResultMap();
    }
});

const continueToCreate = () => {
    const proposal = proposals.value.find((p) => p.id === selectedId.value);
    if (!proposal) return;

    const title = tripType.value === 'loop'
        ? `${t('routes.plan_loop')} · ${origin.value?.name || t('routes.plan_loop_start')}`
        : `${origin.value?.name || 'Origen'} → ${destination.value?.name || 'Destí'}`;

    sessionStorage.setItem(DRAFT_KEY, JSON.stringify({
        title,
        planned_distance_km: proposal.distanceKm,
        duration_seconds: proposal.durationSeconds,
        geo_json: proposal.geoJson,
        waypoints: proposal.waypoints,
        is_planned_route: true,
        is_loop: proposal.isLoop || tripType.value === 'loop',
    }));

    router.visit(route('routes.create'));
};

onUnmounted(() => {
    destroyResultMap();
    destroyPickerMap();
});
</script>

<style scoped>
#plan-picker-map,
#plan-map {
    z-index: 1;
}
</style>

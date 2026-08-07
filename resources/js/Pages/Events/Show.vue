<template>
    <AppLayout v-if="event" :title="event.title || $t('events.loading_event')" :hide-bottom-nav="isMapOpen">
        <div class="px-6 pt-10 pb-28 max-w-xl mx-auto cc-fade-in">

            <header class="flex items-center gap-3 mb-8">
                <button type="button" @click="goBack" class="cc-icon-btn" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </button>
                <h1 class="cc-title flex-1 truncate">{{ event.title || $t('events.no_title') }}</h1>
            </header>

            <!-- Hero: el dia mana -->
            <p class="text-sm text-gray-500">{{ $t('events.date_time') }}</p>
            <p class="mt-3 text-[48px] leading-[0.95] font-light tracking-tight text-white">
                {{ formatDateLong(event.start_time) }}
            </p>
            <p class="mt-2 text-sm text-gray-500 tabular-nums">{{ formatTime(event.start_time) }}</p>

            <p class="mt-4 text-sm text-gray-400">
                {{ event.location || $t('events.no_location_info') }}
                <template v-if="event.organizer"> · {{ event.organizer.name }}</template>
            </p>

            <!-- Riders -->
            <div class="mt-10 flex items-baseline gap-8">
                <div>
                    <p
                        class="text-[28px] font-light leading-none tabular-nums"
                        :class="isFull ? 'text-red-400' : 'text-white'"
                    >
                        <template v-if="event.max_participants">
                            {{ event.participants_count || 0 }}<span class="text-base text-gray-500">/{{ event.max_participants }}</span>
                        </template>
                        <template v-else>{{ event.participants_count || 0 }}</template>
                    </p>
                    <p class="mt-1.5 text-xs text-gray-500">{{ $t('events.riders_label') }}</p>
                </div>
                <div v-if="totalDistance > 0">
                    <p class="text-[28px] font-light leading-none tabular-nums text-white">
                        {{ totalDistance }}<span class="text-base text-gray-500 ml-1">km</span>
                    </p>
                    <p class="mt-1.5 text-xs text-gray-500">{{ $t('events.total_km') }}</p>
                </div>
            </div>

            <p v-if="isFull" class="mt-3 text-sm text-red-400">{{ $t('events.full', { n: event.max_participants }) }}</p>

            <!-- CTA principal -->
            <div class="mt-8 space-y-3">
                <template v-if="$page.props.auth.user && event.user_id !== $page.props.auth.user.id">
                    <Link
                        v-if="!event.is_attending && !isFull"
                        :href="route('events.join', event.id)"
                        method="post"
                        as="button"
                        class="cc-btn-primary w-full py-3.5"
                    >
                        {{ $t('events.join_short') }}
                    </Link>
                    <Link
                        v-else-if="event.is_attending"
                        :href="route('events.leave', event.id)"
                        method="post"
                        as="button"
                        class="cc-btn-secondary w-full py-3.5"
                    >
                        {{ $t('events.leave_short') }}
                    </Link>
                </template>

                <template v-else-if="!$page.props.auth.user">
                    <p class="text-sm text-gray-500 text-center">{{ $t('events.login_to_join') }}</p>
                    <Link :href="route('login')" class="cc-btn-primary w-full py-3.5 text-center">{{ $t('events.login') }}</Link>
                </template>
            </div>

            <p v-if="event.description" class="mt-10 text-sm text-gray-400 leading-relaxed">
                {{ event.description }}
            </p>

            <div v-if="event.photo" class="mt-8 overflow-hidden rounded-2xl">
                <img :src="$page.props.storageUrl + '/' + event.photo" alt="" class="w-full h-44 object-cover">
            </div>

            <!-- Itinerari -->
            <section v-if="event.routes && event.routes.length" class="mt-12">
                <div class="flex items-center justify-between gap-3">
                    <p class="cc-section-label">{{ $t('events.itinerary') }}</p>
                    <button type="button" @click="openGlobalMap" class="cc-btn-text">
                        {{ $t('events.view_full_map') }}
                    </button>
                </div>

                <div class="mt-2 divide-y divide-white/[0.06]">
                    <Link
                        v-for="(ruta, index) in event.routes"
                        :key="ruta.id"
                        :href="route('routes.show', ruta.id) + '?from_event=' + event.id"
                        class="flex items-center gap-4 py-4 group"
                    >
                        <span class="text-sm text-gray-600 tabular-nums w-5">{{ index + 1 }}</span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-[15px] font-medium text-gray-100 group-hover:text-white transition-colors truncate">
                                {{ ruta.title }}
                            </span>
                        </span>
                        <span class="text-[15px] tabular-nums text-gray-400 flex-shrink-0">
                            {{ Math.round(ruta.planned_distance_km || 0) }}<span class="text-xs text-gray-600 ml-1">km</span>
                        </span>
                    </Link>
                </div>
            </section>

            <!-- Accions terciàries -->
            <nav class="mt-12 pt-6 border-t border-white/[0.06] flex flex-wrap gap-2">
                <Link
                    v-if="event.group_chat_id"
                    :href="route('chats.show', event.group_chat_id)"
                    class="cc-btn-text"
                >
                    {{ $t('events.open_chat') }}
                </Link>
                <button
                    v-if="$page.props.auth.user && event.user_id === $page.props.auth.user.id"
                    type="button"
                    @click="copyShareLink"
                    class="cc-btn-text"
                >
                    {{ copyLinkSuccess ? $t('events.link_copied') : $t('events.copy_link_short') }}
                </button>
                <Link
                    v-if="$page.props.auth.user && event.user_id === $page.props.auth.user.id"
                    :href="route('events.edit', event.id)"
                    class="cc-btn-text"
                >
                    {{ $t('common.edit') }}
                </Link>
                <ReportButton
                    v-if="!$page.props.auth.user || event.user_id !== $page.props.auth.user.id"
                    reportable-type="event"
                    :reportable-id="event.id"
                    label="Denunciar"
                    :context-label="`Denunciar quedada: ${event.title}`"
                    button-class="cc-btn-text border-red-500/25 text-red-400 hover:text-red-300 hover:border-red-500/50"
                />
            </nav>
        </div>

        <Teleport to="body">
            <div v-show="isMapOpen" class="fixed inset-0 z-[6000] bg-brand-black flex flex-col">
                <div id="event-global-map" class="absolute inset-0 w-full h-full z-0"></div>

                <button
                    type="button"
                    @click="closeMap"
                    class="cc-icon-btn absolute top-4 left-4 z-[6010] bg-black/50 backdrop-blur-md border-white/20"
                    :aria-label="$t('common.back')"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </button>

                <div class="absolute bottom-6 left-0 w-full px-4 z-[6010] pointer-events-none">
                    <div class="pointer-events-auto mx-auto max-w-sm rounded-2xl border border-white/[0.08] bg-brand-black/95 backdrop-blur-xl px-5 py-4 flex items-baseline justify-between">
                        <div>
                            <p class="cc-section-label">{{ $t('events.full_itinerary') }}</p>
                            <p class="mt-1 text-sm text-gray-300">{{ (event.routes || []).length }} {{ $t('events.sections') }}</p>
                        </div>
                        <p class="text-[26px] font-light tabular-nums text-white">
                            {{ totalDistance }}<span class="text-sm text-gray-500 ml-1">km</span>
                        </p>
                    </div>
                </div>
            </div>
        </Teleport>
    </AppLayout>

    <div v-else class="h-screen bg-brand-black flex items-center justify-center text-gray-500">
        {{ $t('events.loading_event') }}
    </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import ReportButton from '@/Components/ReportButton.vue';
import { smartBack } from '@/Composables/navigationStack.js';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { addMapTileLayer } from '@/config/mapTiles.js';

const { t, locale } = useI18n();

const props = defineProps({ event: Object });

const isMapOpen = ref(false);
const map = ref(null);
const mapLayers = ref([]);
const copyLinkSuccess = ref(false);

const goBack = () => smartBack(route('events.index'));

const isFull = computed(() =>
    props.event?.max_participants && (props.event.participants_count || 0) >= props.event.max_participants,
);

const formatDateLong = (dateStr) => {
    if (!dateStr) return '';
    try {
        return new Date(dateStr).toLocaleDateString(locale.value, {
            weekday: 'short',
            day: 'numeric',
            month: 'long',
        });
    } catch {
        return '';
    }
};

const formatTime = (dateStr) => {
    if (!dateStr) return '';
    try {
        return new Date(dateStr).toLocaleTimeString(locale.value, { hour: '2-digit', minute: '2-digit' });
    } catch {
        return '';
    }
};

const writeToClipboard = async (text) => {
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
            return true;
        }
    } catch { /* fallthrough */ }
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
    } catch {
        return false;
    }
};

const copyShareLink = async () => {
    if (!props.event?.id) return;

    const shareToken = props.event.share_token;
    const shareUrl = shareToken
        ? `${window.location.origin}/e/${shareToken}`
        : `${window.location.origin}/events/${props.event.id}`;
    const shareTitle = props.event.title || 'Quedada';

    if (navigator.share) {
        try {
            await navigator.share({ title: shareTitle, text: shareTitle, url: shareUrl });
            return;
        } catch (error) {
            if (error?.name === 'AbortError') return;
        }
    }

    if (await writeToClipboard(shareUrl)) {
        copyLinkSuccess.value = true;
        setTimeout(() => { copyLinkSuccess.value = false; }, 3000);
    }
};

const totalDistance = computed(() => {
    if (!props.event?.routes) return 0;
    return props.event.routes
        .reduce((acc, route) => acc + parseFloat(route.planned_distance_km || 0), 0)
        .toFixed(0);
});

const openGlobalMap = async () => {
    isMapOpen.value = true;
    await nextTick();

    requestAnimationFrame(() => {
        setTimeout(() => {
            if (!map.value) {
                map.value = L.map('event-global-map', { zoomControl: false, attributionControl: false })
                    .setView([41.3851, 2.1734], 13);
                addMapTileLayer(map.value, L);
            } else {
                mapLayers.value.forEach((layer) => { if (map.value && layer) map.value.removeLayer(layer); });
                mapLayers.value = [];
                map.value.invalidateSize();
            }

            let allPoints = [];
            (props.event?.routes || []).forEach((route) => {
                let data = route.geo_json;
                if (!data) return;
                if (typeof data === 'string') {
                    try {
                        data = JSON.parse(data);
                        if (typeof data === 'string') data = JSON.parse(data);
                    } catch {
                        return;
                    }
                }
                if (!Array.isArray(data) || !data.length) return;

                const points = data
                    .map((p) => {
                        if (Array.isArray(p) && p.length >= 2) return [p[1], p[0]];
                        return [p.lat ?? p.latitude, p.lng ?? p.longitude];
                    })
                    .filter((p) => p[0] != null && p[1] != null);

                if (!points.length) return;

                const polyline = L.polyline(points, {
                    color: '#fafafa',
                    weight: 5,
                    opacity: 0.9,
                    lineJoin: 'round',
                }).addTo(map.value);

                const start = L.circleMarker(points[0], {
                    radius: 6,
                    color: '#0a0a0a',
                    weight: 3,
                    fillColor: '#fafafa',
                    fillOpacity: 1,
                }).addTo(map.value);

                mapLayers.value.push(polyline, start);
                allPoints = allPoints.concat(points);
            });

            if (allPoints.length && map.value) {
                try {
                    map.value.fitBounds(L.latLngBounds(allPoints), { padding: [50, 100] });
                } catch { /* ignore */ }
            }
        }, 80);
    });
};

const closeMap = () => { isMapOpen.value = false; };
</script>

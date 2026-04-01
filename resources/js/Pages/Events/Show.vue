<template>
    <AppLayout v-if="event" :title="event.title || $t('events.loading_event')">
        <div class="px-4 py-6 pb-24">

            <div class="flex items-center justify-between mb-6">
                <Link :href="route('events.index')" class="text-gray-400 text-sm hover:text-white flex items-center gap-1 transition">
                    {{ $t('events.back') }}
                </Link>

                <button v-if="$page.props.auth.user && event.user_id === $page.props.auth.user.id" @click="copyShareLink" class="bg-gray-800 hover:bg-brand-neon hover:text-black text-white px-3 py-1.5 rounded-lg transition border border-gray-700 flex items-center gap-2 text-xs font-bold">
                    <span v-if="copyLinkSuccess">{{ $t('events.link_copied') }}</span>
                    <span v-else>{{ $t('events.copy_link') }}</span>
                </button>
            </div>

            <div v-if="event.photo" class="relative h-56 w-full overflow-hidden mb-6 rounded-xl border border-brand-dark shadow-lg">
                <img :src="$page.props.storageUrl + '/' + event.photo" :alt="$t('events.event_title')" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 photo-gradient-overlay"></div>
            </div>

            <div class="mb-6">
                <h1 class="text-3xl font-black text-white uppercase tracking-tighter">{{ event.title || $t('events.no_title') }}</h1>
                <p class="text-gray-400 text-sm mt-1 flex items-center gap-2">
                    📍 {{ event.location || $t('events.no_location_info') }}
                </p>
            </div>

            <div class="bg-brand-surface rounded-xl p-5 border border-brand-dark shadow-lg mb-8">
                <div class="flex flex-wrap items-center gap-4 text-sm font-bold text-white mb-4">
                    <span v-if="event.start_time" class="bg-brand-black px-3 py-1.5 rounded-lg border border-brand-dark">📅 {{ formatDate(event.start_time) }}</span>
                    <span v-if="event.start_time" class="bg-brand-black px-3 py-1.5 rounded-lg border border-brand-dark">⏰ {{ formatTime(event.start_time) }}</span>

                    <span class="bg-brand-black px-3 py-1.5 rounded-lg border border-brand-dark flex items-center gap-2" :class="{'text-red-400': event.max_participants && (event.participants_count || 0) >= event.max_participants, 'text-brand-neon': !event.max_participants}">
                        👤
                        <span v-if="event.max_participants">
                            {{ event.participants_count || 0 }} / {{ event.max_participants }}
                        </span>
                        <span v-else>
                            {{ $t('events.num_attendees', { n: event.participants_count || 0 }) }}
                        </span>
                        <span v-if="event.max_participants && (event.participants_count || 0) >= event.max_participants" class="ml-1 text-[9px] bg-red-500 text-black px-1 rounded uppercase">FULL</span>
                    </span>
                </div>
                <p class="text-gray-300 text-sm mb-6">{{ event.description || $t('events.no_description') }}</p>

                <!-- Botons d'apuntar-se / desapuntar-se -->
                <div v-if="$page.props.auth.user && event.user_id !== $page.props.auth.user.id" class="border-t border-brand-dark pt-5 flex justify-center">
                    <div v-if="event.is_attending" class="flex flex-col sm:flex-row gap-3">
                        <Link v-if="event.group_chat_id" :href="route('chats.show', event.group_chat_id)" class="bg-purple-500/10 text-purple-400 border border-purple-500/50 hover:bg-purple-500 hover:text-white px-6 py-3 rounded-xl font-black uppercase tracking-widest transition flex items-center justify-center gap-2 shadow-lg w-full sm:w-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" /></svg>
                            Xat
                        </Link>
                        <Link :href="route('events.leave', event.id)" method="post" as="button" class="bg-red-500/10 text-red-500 border border-red-500/50 hover:bg-red-500 hover:text-white px-6 py-3 rounded-xl font-black uppercase tracking-widest transition flex items-center justify-center gap-2 shadow-lg w-full sm:w-auto">
                            {{ $t('events.leave') }}
                        </Link>
                    </div>
                    <div v-else>
                        <div v-if="event.max_participants && (event.participants_count || 0) >= event.max_participants" class="text-red-500 font-bold bg-red-500/10 px-6 py-3 rounded-xl border border-red-500/30 text-center">
                            {{ $t('events.full', { n: event.max_participants }) }}
                        </div>
                        <Link v-else :href="route('events.join', event.id)" method="post" as="button" class="bg-brand-neon text-brand-black hover:bg-white hover:scale-105 px-8 py-3 rounded-xl font-black uppercase tracking-widest transition flex items-center gap-2 shadow-[0_0_20px_rgba(12,225,181,0.4)]">
                            {{ $t('events.join') }}
                        </Link>
                    </div>
                </div>

                <div v-else-if="!$page.props.auth.user" class="border-t border-brand-dark pt-5">
                    <p class="text-center text-gray-400 text-sm mb-3">{{ $t('events.login_to_join') }}</p>
                    <div class="flex justify-center gap-4">
                        <Link :href="route('login')" class="bg-brand-dark text-white px-6 py-2 rounded-lg font-bold hover:bg-gray-700 transition">{{ $t('events.login') }}</Link>
                        <Link :href="route('register')" class="bg-brand-neon text-brand-black px-6 py-2 rounded-lg font-bold hover:bg-white transition shadow-neon">{{ $t('events.register') }}</Link>
                    </div>
                </div>

                <div v-else class="border-t border-brand-dark pt-5 flex flex-col items-center gap-3">
                    <div class="text-brand-neon font-bold text-sm bg-brand-neon/10 rounded-lg p-3 w-full text-center">
                        {{ $t('events.organizer') }}
                    </div>
                    <Link v-if="event.group_chat_id" :href="route('chats.show', event.group_chat_id)" class="bg-purple-500/10 text-purple-400 border border-purple-500/50 hover:bg-purple-500 hover:text-white px-8 py-3 rounded-xl font-black uppercase tracking-widest transition flex items-center justify-center gap-2 shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" /></svg>
                        Obrir Xat de la Quedada
                    </Link>
                </div>
            </div>

            <div v-if="event.routes && event.routes.length > 0" class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-black text-brand-neon uppercase tracking-widest flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" /></svg>
                        {{ $t('events.itinerary') }} ({{ event.routes.length }})
                    </h3>

                    <button @click="openGlobalMap" class="text-xs font-bold bg-brand-neon text-brand-black px-3 py-1.5 rounded uppercase shadow-[0_0_10px_rgba(12,225,181,0.3)] hover:scale-105 transition">
                        {{ $t('events.view_full_map') }}
                    </button>
                </div>

                <div class="space-y-3">
                    <Link
                        v-for="(ruta, index) in event.routes"
                        :key="ruta.id"
                        :href="route('routes.show', ruta.id)"
                        class="bg-brand-black border border-brand-dark rounded-xl p-3 flex items-center justify-between group hover:border-brand-neon transition shadow-lg relative overflow-hidden"
                    >
                        <div class="absolute left-0 top-0 bottom-0 w-1" :style="{ backgroundColor: mapColors[index % mapColors.length] }"></div>

                        <div class="flex items-center gap-3 pl-2">
                            <div class="w-8 h-8 rounded-full bg-brand-surface flex items-center justify-center text-xs font-bold border border-brand-dark transition" :style="{ color: mapColors[index % mapColors.length], borderColor: mapColors[index % mapColors.length] }">
                                {{ index + 1 }}
                            </div>
                            <div>
                                <h4 class="text-white font-bold text-sm truncate max-w-[200px]">{{ ruta.title }}</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] text-gray-500 font-mono">{{ ruta.planned_distance_km }} km</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-gray-600 group-hover:text-brand-neon transition">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                        </div>
                    </Link>
                </div>
            </div>

        </div>

        <div v-show="isMapOpen" class="fixed inset-0 z-[5000] bg-gray-900 flex flex-col">
            <div id="event-global-map" class="absolute inset-0 w-full h-full z-0 bg-gray-900"></div>

            <button @click="closeMap" class="absolute top-4 right-4 z-[5010] bg-black/50 hover:bg-black/80 text-white p-2 rounded-full backdrop-blur-md border border-white/10 transition pt-safe-top">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>

            <div class="absolute bottom-6 left-0 w-full p-4 z-[5010] pb-safe-bottom pointer-events-none">
                <div class="bg-brand-black/95 backdrop-blur-xl border border-brand-dark rounded-2xl shadow-2xl p-4 flex items-center justify-between pointer-events-auto max-w-lg mx-auto">
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">{{ $t('events.full_itinerary') }}</p>
                        <h2 class="text-lg font-black text-white uppercase mt-0.5">{{ event.routes ? event.routes.length : 0 }} {{ $t('events.sections') }}</h2>
                    </div>
                    <div class="text-right">
                        <span class="block text-2xl font-mono font-bold text-brand-neon">{{ totalDistance }}</span>
                        <span class="text-[10px] text-gray-500 uppercase">{{ $t('events.total_km') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
    <div v-else class="h-screen bg-gray-900 flex items-center justify-center text-white">
        <p class="animate-pulse">{{ $t('events.loading_event') }}</p>
    </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const { locale } = useI18n();

const props = defineProps({ event: Object });

const mapColors = ['#0CE1B5', '#E10C38', '#0C84E1', '#E1B50C', '#B50CE1'];

const isMapOpen = ref(false);
const map = ref(null);
const mapLayers = ref([]);
const copyLinkSuccess = ref(false);

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    try { return new Date(dateStr).toLocaleDateString(locale.value); } catch (e) { return ''; }
};

const formatTime = (dateStr) => {
    if (!dateStr) return '';
    try { return new Date(dateStr).toLocaleTimeString(locale.value, { hour: '2-digit', minute: '2-digit' }); } catch (e) { return ''; }
};

const copyShareLink = () => {
    if (!props.event || !props.event.share_token) return;
    navigator.clipboard.writeText(props.event.share_token).then(() => {
        copyLinkSuccess.value = true;
        setTimeout(() => { copyLinkSuccess.value = false; }, 3000);
    });
};

const totalDistance = computed(() => {
    if (!props.event || !props.event.routes) return 0;
    return props.event.routes.reduce((acc, route) => acc + parseFloat(route.planned_distance_km || 0), 0).toFixed(1);
});

const openGlobalMap = async () => {
    isMapOpen.value = true;
    await nextTick();
    if (!map.value) {
        map.value = L.map('event-global-map', { zoomControl: false, attributionControl: false }).setView([41.3851, 2.1734], 13);
        L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', { maxZoom: 20, className: 'map-tiles-inverse' }).addTo(map.value);
    } else {
        mapLayers.value.forEach(layer => { if (map.value && layer) map.value.removeLayer(layer); });
        mapLayers.value = [];
        map.value.invalidateSize();
    }
    let allPoints = [];
    if (props.event && props.event.routes) {
        props.event.routes.forEach((route, index) => {
            let data = route.geo_json;
            if (!data) return;
            if (typeof data === 'string') {
                try { data = JSON.parse(data); if (typeof data === 'string') data = JSON.parse(data); } catch (e) { return; }
            }
            if (Array.isArray(data) && data.length > 0) {
                const color = mapColors[index % mapColors.length];
                const points = data.map(p => [p.lat || p.latitude, p.lng || p.longitude]);
                const validPoints = points.filter(p => p[0] != null && p[1] != null);
                if (validPoints.length === 0) return;
                const polyline = L.polyline(validPoints, { color, weight: 6, opacity: 0.9, lineJoin: 'round' }).addTo(map.value);
                const startMarker = L.circleMarker(validPoints[0], { radius: 6, color, fillColor: '#111827', fillOpacity: 1, weight: 3 }).addTo(map.value);
                mapLayers.value.push(polyline, startMarker);
                allPoints = allPoints.concat(validPoints);
            }
        });
    }
    if (allPoints.length > 0 && map.value) {
        try { map.value.fitBounds(L.latLngBounds(allPoints), { padding: [50, 100] }); } catch (e) {}
    }
};

const closeMap = () => { isMapOpen.value = false; };
</script>

<style scoped>
.pt-safe-top { padding-top: env(safe-area-inset-top, 20px); }
.pb-safe-bottom { padding-bottom: env(safe-area-inset-bottom, 20px); }
.map-tiles-inverse { filter: brightness(150%) contrast(150%); }
</style>

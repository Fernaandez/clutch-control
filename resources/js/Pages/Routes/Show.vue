<template>
    <AppLayout v-if="mapRoute" :title="mapRoute.title || $t('routes.loading_route')">

        <div class="fixed top-0 left-0 w-full h-[100dvh] bg-gray-900 overflow-hidden overscroll-none z-[5000]">

            <div v-if="mapRoute" id="map-detail" class="absolute inset-0 z-0 bg-gray-900"></div>

            <Link :href="route('routes.index')" class="absolute top-safe-top right-4 z-50 bg-black/50 hover:bg-black/80 text-white p-2 rounded-full backdrop-blur-md border border-white/10 transition mt-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </Link>

            <!-- LIVE TRACKING OVERLAY -->
            <div v-if="isRecording" class="absolute top-safe-top left-1/2 -translate-x-1/2 z-50 bg-brand-black/90 backdrop-blur-xl border border-brand-dark rounded-full px-6 py-3 shadow-[0_0_20px_rgba(239,68,68,0.3)] mt-2 flex items-center gap-6">
                <!-- Parpelleig LIVE -->
                <div class="flex items-center gap-2">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                    </span>
                    <span class="text-xs font-black text-red-500 uppercase tracking-widest">LIVE</span>
                </div>
                <!-- Temps -->
                <div class="flex flex-col items-center">
                    <span class="text-[10px] text-gray-500 uppercase font-bold leading-none mb-1">{{ $t('free_ride.chrono') }}</span>
                    <span class="text-lg font-mono font-bold text-white leading-none">{{ formattedRecordingTime }}</span>
                </div>
                <!-- Distància -->
                <div class="flex flex-col items-center">
                    <span class="text-[10px] text-gray-500 uppercase font-bold leading-none mb-1">{{ $t('free_ride.distance') }}</span>
                    <span class="text-lg font-mono font-bold text-brand-neon leading-none">{{ (recordedDistance / 1000).toFixed(2) }}<span class="text-xs text-gray-400 ml-1">km</span></span>
                </div>
            </div>

            <div class="absolute bottom-6 left-0 w-full p-4 z-10 pb-safe-bottom">
                <div class="bg-brand-black/95 backdrop-blur-xl border border-brand-dark rounded-2xl shadow-2xl overflow-hidden flex flex-col transition-all duration-300">

                    <!-- HEADER COLLAPSIBLE -->
                    <div class="p-4 flex flex-col gap-3 flex-shrink-0 cursor-pointer hover:bg-white/5 transition" @click="isExpanded = !isExpanded">
                        <div class="flex justify-between items-start">
                            <div class="flex-1 pr-2">
                                <h1 class="text-xl font-black text-white uppercase tracking-tighter leading-none line-clamp-1">{{ mapRoute.title || $t('events.no_title') }}</h1>
                                <div class="flex items-center gap-2 mt-2 flex-wrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border"
                                        :class="{
                                            'border-green-500 text-green-400 bg-green-500/10': mapRoute.difficulty === 'easy',
                                            'border-yellow-500 text-yellow-400 bg-yellow-500/10': mapRoute.difficulty === 'medium',
                                            'border-red-500 text-red-400 bg-red-500/10': mapRoute.difficulty === 'hard',
                                            'border-purple-500 text-purple-400 bg-purple-500/10': mapRoute.difficulty === 'expert',
                                            'border-gray-500 text-white bg-gray-800': mapRoute.difficulty === 'extreme'
                                        }">
                                        {{ mapRoute.difficulty === 'easy' ? $t('routes.diff_label_easy') : (mapRoute.difficulty === 'medium' ? $t('routes.diff_label_medium') : (mapRoute.difficulty === 'hard' ? $t('routes.diff_label_hard') : (mapRoute.difficulty === 'expert' ? $t('routes.diff_label_expert') : $t('routes.diff_label_extreme')))) }}
                                    </span>

                                    <span v-if="mapRoute.is_public" class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-brand-neon/30 text-brand-neon bg-brand-neon/10">{{ $t('routes.public_badge') }}</span>
                                    <span v-else class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border border-gray-500 text-gray-400 bg-gray-800">{{ $t('routes.private_badge') }}</span>

                                    <span v-if="motorcycle" class="text-xs text-gray-400 flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3"><path d="M8 16.25a.75.75 0 01.75-.75h2.5a.75.75 0 010 1.5h-2.5a.75.75 0 01-.75-.75z" /><path fill-rule="evenodd" d="M4 4a3 3 0 013-3h6a3 3 0 013 3v12a3 3 0 01-3 3H7a3 3 0 01-3-3V4zm4-1.5a.75.75 0 000 1.5h4a.75.75 0 000-1.5H8z" clip-rule="evenodd" /></svg>
                                        {{ motorcycle.alias || motorcycle.model }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="p-2 text-gray-400 bg-gray-800 rounded-lg">
                                    <svg v-if="isExpanded" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" /></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Mini Actions (Visible when collapsed) -->
                        <div v-show="!isExpanded" class="flex items-center gap-2 mt-1" @click.stop>
                            <a :href="googleMapsLink" target="_blank" class="flex-1 flex items-center justify-center gap-1.5 bg-brand-neon/10 text-brand-neon border border-brand-neon/30 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-brand-neon/20 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M8.161 2.58a1.875 1.875 0 0 1 1.678 0l4.993 2.498c.106.052.23.052.336 0l3.869-1.935A1.875 1.875 0 0 1 21.75 4.82v12.485c0 .71-.401 1.36-1.037 1.677l-4.875 2.437a1.875 1.875 0 0 1-1.678 0l-4.993-2.498a.75.75 0 0 0-.336 0l-3.868 1.935A1.875 1.875 0 0 1 2.25 19.18V6.695c0-.71.401-1.36 1.036-1.677l4.875-2.437ZM9 6a.75.75 0 0 1 .75.75V15a.75.75 0 0 1-1.5 0V6.75A.75.75 0 0 1 9 6Zm6.75 3a.75.75 0 0 0-1.5 0v8.25a.75.75 0 0 0 1.5 0V9Z" clip-rule="evenodd" /></svg>
                                {{ $t('routes.navigate') }}
                            </a>
                            <button v-if="!isRecording" @click.stop="startRecording" class="flex-1 flex items-center justify-center gap-1.5 bg-red-600/10 text-red-500 border border-red-500/30 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-600/20 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><circle cx="12" cy="12" r="8" fill="currentColor" /></svg>
                                {{ $t('routes.follow') }}
                            </button>
                            <button v-else @click.stop="stopRecording" class="flex-1 flex items-center justify-center gap-1.5 bg-red-900 border border-red-500 text-white py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest animate-pulse">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z" fill="currentColor" /></svg>
                                {{ $t('routes.stop') }}
                            </button>
                        </div>
                    </div>

                    <!-- EXPANDED BODY -->
                    <div v-show="isExpanded" class="border-t border-gray-800 overflow-y-auto max-h-[55vh] overscroll-contain">

                        <!-- Utilitats (compartir / editar) -->
                        <div v-if="$page.props.auth.user && mapRoute.user_id === $page.props.auth.user.id" class="px-4 py-2 bg-brand-black/50 border-b border-gray-800 flex justify-end gap-2">
                            <button @click="copyShareLink" class="bg-gray-800 hover:bg-brand-neon hover:text-black text-white p-2 rounded-lg transition border border-gray-700 flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest" title="Copia l'enllaç de Compartició">
                                <svg v-if="!copyLinkSuccess" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" /></svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                {{ $t('routes.share') }}
                            </button>
                            <Link :href="route('routes.edit', mapRoute.id)" class="bg-gray-800 hover:bg-gray-700 text-white p-2 rounded-lg transition border border-gray-700 flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                {{ $t('routes.edit') }}
                            </Link>
                        </div>

                        <!-- Descripció -->
                        <div class="px-4 pt-4 pb-2">
                            <p class="text-sm text-gray-400">{{ mapRoute.description || $t('routes.no_description') }}</p>
                        </div>

                        <!-- Foto -->
                        <div v-if="mapRoute.photo" class="px-4 pt-2">
                            <img :src="$page.props.storageUrl + '/' + mapRoute.photo" alt="Foto Ruta" class="w-full h-32 object-cover rounded-xl border border-brand-dark">
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-px bg-gray-800/50 mt-4 border-t border-gray-800">
                            <div class="p-3 text-center bg-brand-black/50">
                                <span class="block text-xl font-mono font-bold text-brand-neon">{{ mapRoute.planned_distance_km || 0 }}</span>
                                <span class="text-[10px] text-gray-500 uppercase tracking-wider">Km</span>
                            </div>
                            <div class="p-3 text-center bg-brand-black/50">
                                <span class="block text-xl font-mono font-bold text-white">{{ formattedDuration }}</span>
                                <span class="text-[10px] text-gray-500 uppercase tracking-wider">{{ $t('routes.time_label') }}</span>
                            </div>
                        </div>

                        <!-- Valoracions -->
                        <div v-if="mapRoute.is_public" class="px-4 py-3 border-t border-gray-800">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $t('routes.ratings') }}</h3>
                                <span v-if="mapRoute.reviews && mapRoute.reviews.length" class="text-brand-neon font-bold text-sm">⭐ {{ (mapRoute.reviews.reduce((a, b) => a + b.rating, 0) / mapRoute.reviews.length).toFixed(1) }}</span>
                            </div>
                            <div v-if="mapRoute.reviews && mapRoute.reviews.length" class="space-y-2 mb-3">
                                <div v-for="review in mapRoute.reviews" :key="review.id" class="bg-brand-surface border border-brand-dark p-2 rounded-lg text-sm">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="font-bold text-gray-200">{{ review.user.name }}</span>
                                        <span class="text-yellow-400 text-xs">⭐ {{ review.rating }}/5</span>
                                    </div>
                                    <p class="text-gray-400 text-xs">{{ review.comment }}</p>
                                </div>
                            </div>
                            <p v-else class="text-xs text-gray-500 italic mb-3">{{ $t('routes.no_reviews_yet') }}</p>

                            <div v-if="$page.props.auth && $page.props.auth.user && mapRoute.user_id !== $page.props.auth.user.id" class="space-y-2 mt-3 pt-3 border-t border-gray-800/50">
                                <div v-if="userHasReviewed" class="text-xs text-brand-neon bg-brand-neon/10 border border-brand-neon/30 p-2 rounded text-center font-bold">
                                    {{ $t('routes.already_reviewed') }}
                                </div>
                                <form v-else @submit.prevent="submitReview" class="flex flex-col gap-2">
                                    <p class="text-[10px] uppercase font-bold text-gray-400 text-center">{{ $t('routes.leave_review') }}</p>
                                    <div class="flex items-center gap-1 justify-center py-1">
                                        <button type="button" @click="reviewForm.rating = n" v-for="n in 5" :key="n" :class="reviewForm.rating >= n ? 'text-yellow-400' : 'text-gray-600'" class="text-2xl transition-transform hover:scale-110">★</button>
                                    </div>
                                    <textarea v-model="reviewForm.comment" rows="2" :placeholder="$t('routes.review_placeholder')" class="w-full bg-brand-black/50 border border-brand-dark rounded-lg text-white text-xs p-2 focus:border-brand-neon focus:ring-1 focus:ring-brand-neon transition"></textarea>
                                    <button type="submit" :disabled="reviewForm.processing || reviewForm.rating === 0" class="w-full bg-gray-800 text-white text-xs font-bold py-2 rounded-lg hover:bg-brand-neon hover:text-black transition uppercase tracking-widest disabled:opacity-50">{{ $t('routes.publish_review') }}</button>
                                </form>
                            </div>
                        </div>

                        <!-- Accions principals (igual que els mini botons de dalt) -->
                        <div class="p-4 flex items-center gap-2 border-t border-gray-800">
                            <a :href="googleMapsLink" target="_blank" class="flex-1 flex items-center justify-center gap-1.5 bg-brand-neon/10 text-brand-neon border border-brand-neon/30 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-brand-neon/20 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M8.161 2.58a1.875 1.875 0 0 1 1.678 0l4.993 2.498c.106.052.23.052.336 0l3.869-1.935A1.875 1.875 0 0 1 21.75 4.82v12.485c0 .71-.401 1.36-1.037 1.677l-4.875 2.437a1.875 1.875 0 0 1-1.678 0l-4.993-2.498a.75.75 0 0 0-.336 0l-3.868 1.935A1.875 1.875 0 0 1 2.25 19.18V6.695c0-.71.401-1.36 1.036-1.677l4.875-2.437ZM9 6a.75.75 0 0 1 .75.75V15a.75.75 0 0 1-1.5 0V6.75A.75.75 0 0 1 9 6Zm6.75 3a.75.75 0 0 0-1.5 0v8.25a.75.75 0 0 0 1.5 0V9Z" clip-rule="evenodd" /></svg>
                                {{ $t('routes.navigate') }}
                            </a>
                            <Link v-if="$page.props.auth.user && mapRoute.user_id !== $page.props.auth.user.id" :href="route('routes.clone', mapRoute.id)" method="post" as="button" class="flex-1 flex items-center justify-center gap-1.5 bg-gray-800 text-brand-neon border border-brand-neon/30 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-brand-neon/20 transition" title="Guardar i Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" /></svg>
                                {{ $t('routes.save_route') }}
                            </Link>
                            <button v-if="!isRecording" @click="startRecording" class="flex-1 flex items-center justify-center gap-1.5 bg-red-600/10 text-red-500 border border-red-500/30 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-600/20 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><circle cx="12" cy="12" r="8" fill="currentColor" /></svg>
                                {{ $t('routes.follow') }}
                            </button>
                            <button v-else @click="stopRecording" class="flex-1 flex items-center justify-center gap-1.5 bg-red-900 border border-red-500 text-white py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest animate-pulse">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z" fill="currentColor" /></svg>
                                {{ $t('routes.stop') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
    <div v-else class="h-screen bg-gray-900 flex items-center justify-center text-white">
        <p class="animate-pulse">{{ $t('routes.loading_route') }}</p>
    </div>
</template>

<script setup>
import { onMounted, computed, ref, nextTick } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
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
const isExpanded = ref(false);

const reviewForm = useForm({
    rating: 0,
    comment: ''
});

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
        
        // --- OFFLINE FIX: Save to localStorage ---
        try {
            const existingPending = JSON.parse(localStorage.getItem('pending_routes') || '[]');
            
            const newPendingRoute = {
                id: 'offline_' + Date.now(),
                created_at: new Date().toISOString(),
                original_route_id: props.mapRoute?.id || null, // If tracking a specific route
                motorcycle_id: props.motorcycle?.id || null,
                distance_km: parseFloat(distanceKm),
                duration_seconds: recordingTime.value,
                waypoints: recordedWaypoints.value
            };
            
            existingPending.push(newPendingRoute);
            localStorage.setItem('pending_routes', JSON.stringify(existingPending));
            
            alert(`📍 Seguiment aturat!\nHas recorregut ${distanceKm} km. La ruta s'ha guardat localment al teu telèfon. Vés a "Les Meves Rutes" per sincronitzar-la quan tinguis connexió.`);
        } catch (e) {
            console.error('Error saving offline route:', e);
            alert(`📍 Seguiment aturat!\nHas recorregut ${distanceKm} km, però hi ha hagut un error guardant-la localment.`);
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
.map-tiles-inverse { filter: brightness(150%) contrast(150%); }
</style>

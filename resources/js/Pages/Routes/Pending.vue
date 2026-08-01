<template>
    <AppLayout title="Recorreguts Pendents">
        <div class="px-4 py-6 pb-24 max-w-3xl mx-auto cc-fade-in">

            <header class="flex items-center gap-3 mb-6">
                <button type="button" @click="goBack" class="cc-icon-btn" aria-label="Enrere">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                </button>
                <h1 class="cc-title flex-1 truncate">Pendents</h1>
            </header>

            <div v-if="pendingTrips.length === 0" class="cc-card">
                <div class="flex flex-col items-center justify-center text-center py-16 px-6">
                    <div class="mb-4 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </div>
                    <p class="text-base font-semibold text-gray-300">Tot sincronitzat</p>
                    <p class="mt-1 text-sm text-gray-500 max-w-xs">No tens cap recorregut gravat pendent de sincronitzar en aquest dispositiu.</p>
                    <div class="mt-6">
                        <Link :href="route('routes.index')" class="cc-btn-secondary">
                            Explorar rutes
                        </Link>
                    </div>
                </div>
            </div>

            <div v-else class="space-y-4">
                <div class="cc-card p-4 bg-white/[0.03]">
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Tens <strong class="text-gray-300 font-medium">{{ pendingTrips.length }}</strong> recorregut{{ pendingTrips.length > 1 ? 's' : '' }} gravat{{ pendingTrips.length > 1 ? 's' : '' }} localment. Un cop sincronitzis, es sumaran els km a la teva moto i podràs consultar el trajecte.
                    </p>
                </div>

                <div v-for="(trip, index) in pendingTrips" :key="trip.id" class="cc-card p-5 relative overflow-hidden">
                    <div v-if="syncingId === trip.id" class="absolute inset-0 bg-brand-black/80 backdrop-blur-sm z-10 flex flex-col items-center justify-center">
                        <svg class="animate-spin h-8 w-8 text-gray-300 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span class="text-gray-300 font-medium text-sm">Sincronitzant...</span>
                    </div>

                    <!-- Header: data + esborrar -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-white font-medium text-base flex items-center gap-1.5">
                                <AppIcon name="pin" size="sm" class="text-gray-400" />
                                Recorregut
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ formatDate(trip.started_at) }}</p>
                        </div>
                        <button @click="removeTrip(index)" class="cc-icon-btn w-9 h-9 text-gray-500 hover:text-red-400 hover:bg-red-500/10 hover:border-red-500/20">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                        </button>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-white/[0.04] p-3 rounded-xl border border-white/[0.08] text-center">
                            <span class="block text-2xl font-mono font-semibold text-white tracking-tight">{{ trip.distance_km }}</span>
                            <span class="text-xs text-gray-500 block mt-1">km</span>
                        </div>
                        <div class="bg-white/[0.04] p-3 rounded-xl border border-white/[0.08] text-center">
                            <span class="block text-2xl font-mono font-semibold text-white tracking-tight">{{ formatDuration(trip.duration_seconds) }}</span>
                            <span class="text-xs text-gray-500 block mt-1">Temps</span>
                        </div>
                    </div>

                    <!-- Botó pujar -->
                    <button @click="syncTrip(trip, index)" class="cc-btn-primary w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                        Sincronitzar
                    </button>
                </div>

                <button v-if="pendingTrips.length > 1" @click="syncAll" class="cc-btn-secondary w-full">
                    <AppIcon name="bolt" size="sm" />
                    Sincronitzar tots ({{ pendingTrips.length }})
                </button>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import AppIcon from '@/Components/AppIcon.vue';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import { smartBack } from '@/Composables/navigationStack.js';

const goBack = () => smartBack(route('routes.MyRoutes'));

const pendingTrips = ref([]);
const syncingId = ref(null);

onMounted(() => {
    loadPending();
});

const loadPending = () => {
    try {
        const stored = localStorage.getItem('pending_trips');
        if (stored) {
            pendingTrips.value = JSON.parse(stored);
        }
    } catch (e) {
        console.error('Error llegint memòria local', e);
    }
};

const removeTrip = (index) => {
    if (confirm('Segur que vols esborrar aquest recorregut permanentment?')) {
        pendingTrips.value.splice(index, 1);
        localStorage.setItem('pending_trips', JSON.stringify(pendingTrips.value));
    }
};

const syncTrip = async (tripObj, index) => {
    if (syncingId.value) return;
    syncingId.value = tripObj.id;

    try {
        const { data } = await axios.post(route('trips.store'), {
            distance_km:      tripObj.distance_km,
            duration_seconds: tripObj.duration_seconds,
            waypoints:        tripObj.waypoints,
            started_at:       tripObj.started_at,
            motorcycle_id:    tripObj.motorcycle_id,
            route_id:         tripObj.route_id || null,
        });

        if (data.success) {
            pendingTrips.value.splice(index, 1);
            localStorage.setItem('pending_trips', JSON.stringify(pendingTrips.value));
        }
    } catch (error) {
        alert('Hi ha hagut un error pujant el recorregut. Comprova la connexió i torna-ho a provar.');
        console.error(error);
    } finally {
        syncingId.value = null;
    }
};

const syncAll = async () => {
    for (let i = pendingTrips.value.length - 1; i >= 0; i--) {
        await syncTrip(pendingTrips.value[i], i);
    }
};

const formatDate = (isoStr) => {
    if (!isoStr) return '';
    return new Intl.DateTimeFormat('ca-ES', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(isoStr));
};

const formatDuration = (sec) => {
    if (!sec) return '0m';
    const hrs = Math.floor(sec / 3600);
    const mins = Math.floor((sec % 3600) / 60);
    if (hrs > 0) return `${hrs}h ${mins}m`;
    return `${mins}m`;
};
</script>

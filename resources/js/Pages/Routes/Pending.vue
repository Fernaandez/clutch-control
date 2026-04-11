<template>
    <AppLayout title="Recorreguts Pendents">
        <div class="px-4 py-8 pb-24 max-w-2xl mx-auto">
            
            <div class="flex items-center gap-4 mb-8">
                <button type="button" @click="goBack" class="w-10 h-10 rounded-full bg-brand-neon flex items-center justify-center text-black hover:bg-white transition flex-shrink-0 shadow-[0_0_15px_rgba(12,225,181,0.3)]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                </button>
                <div>
                    <h1 class="text-3xl font-black uppercase tracking-tighter text-white leading-none">RECORREGUTS</h1>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Pendents de sincronitzar</p>
                </div>
            </div>

            <div v-if="pendingTrips.length === 0" class="bg-brand-surface border border-brand-dark border-dashed rounded-2xl p-12 text-center opacity-80">
                <h3 class="text-xl font-bold text-white mb-2 uppercase tracking-widest">Tot Sincronitzat!</h3>
                <p class="text-sm text-gray-500 mb-6">No tens cap recorregut gravat pendent de sincronitzar en aquest dispositiu.</p>
                <Link :href="route('routes.index')" class="text-brand-neon border border-brand-neon px-6 py-3 rounded-xl font-bold text-sm hover:bg-brand-neon hover:text-black transition uppercase tracking-widest">
                    Explorar Rutes
                </Link>
            </div>

            <div v-else class="space-y-4">
                <div class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-4 text-blue-400 text-xs leading-relaxed">
                    Tens <strong>{{ pendingTrips.length }}</strong> recorregut{{ pendingTrips.length > 1 ? 's' : '' }} gravat{{ pendingTrips.length > 1 ? 's' : '' }} localment. Un cop sincronitzis, es sumaran els km a la teva moto i podràs consultar el trajecte.
                </div>

                <div v-for="(trip, index) in pendingTrips" :key="trip.id" class="bg-brand-surface border border-brand-dark rounded-2xl p-5 shadow-lg relative overflow-hidden">
                    <div v-if="syncingId === trip.id" class="absolute inset-0 bg-brand-black/80 backdrop-blur-sm z-10 flex flex-col items-center justify-center">
                        <svg class="animate-spin h-8 w-8 text-brand-neon mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span class="text-brand-neon font-black uppercase text-xs tracking-widest">Sincronitzant...</span>
                    </div>

                    <!-- Header: data + esborrar -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-white font-bold text-base">📍 Recorregut</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ formatDate(trip.started_at) }}</p>
                        </div>
                        <button @click="removeTrip(index)" class="text-red-500 hover:text-white hover:bg-red-500/20 p-2 rounded-xl border border-transparent hover:border-red-500/50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                        </button>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-px bg-brand-dark rounded-xl mb-4 overflow-hidden border border-brand-dark">
                        <div class="bg-brand-surface p-3 text-center">
                            <span class="block text-2xl font-mono text-brand-neon font-black">{{ trip.distance_km }}</span>
                            <span class="text-[9px] uppercase tracking-widest text-gray-500 font-bold block mt-1">km</span>
                        </div>
                        <div class="bg-brand-surface p-3 text-center">
                            <span class="block text-2xl font-mono text-white font-black">{{ formatDuration(trip.duration_seconds) }}</span>
                            <span class="text-[9px] uppercase tracking-widest text-gray-500 font-bold block mt-1">Temps</span>
                        </div>
                    </div>

                    <!-- Botó pujar -->
                    <button @click="syncTrip(trip, index)" class="w-full flex items-center justify-center gap-2 bg-brand-neon text-black font-black uppercase text-xs tracking-widest py-3 rounded-xl hover:bg-white transition shadow-neon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                        Sincronitzar
                    </button>
                </div>

                <button v-if="pendingTrips.length > 1" @click="syncAll" class="w-full bg-brand-dark text-brand-neon font-black uppercase text-xs tracking-widest border border-brand-neon/30 py-4 rounded-xl shadow-lg hover:bg-brand-neon hover:text-black transition">
                    ⚡ Sincronitzar tots ({{ pendingTrips.length }})
                </button>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
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

<template>
    <AppLayout :title="event.title">
        <div class="min-h-screen bg-brand-black pb-24">
            
            <div class="relative h-[40vh] w-full bg-gray-900 group">
                <div id="map-show" class="absolute inset-0 z-0 opacity-60"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-brand-black via-brand-black/20 to-transparent z-10"></div>
                
                <Link :href="route('events.index')" class="absolute top-4 left-4 z-20 bg-black/50 p-2 rounded-full text-white hover:bg-brand-neon hover:text-black transition backdrop-blur-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                </Link>

                <div class="absolute top-4 right-4 z-20">
                    <span v-if="event.is_public" class="bg-brand-neon/90 text-black px-3 py-1 rounded-full text-xs font-bold uppercase shadow-lg">🌍 Pública</span>
                    <span v-else class="bg-red-500/90 text-white px-3 py-1 rounded-full text-xs font-bold uppercase shadow-lg">🔒 Privada</span>
                </div>
            </div>

            <div class="relative z-20 -mt-12 px-4 max-w-4xl mx-auto">
                <div class="bg-brand-surface border border-brand-dark rounded-2xl p-6 shadow-2xl backdrop-blur-sm">
                    
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex-1 pr-4">
                            <h1 class="text-3xl font-black text-white uppercase leading-none tracking-tight">{{ event.title }}</h1>
                            <div class="flex items-center gap-2 mt-2">
                                <div class="h-6 w-6 rounded-full bg-gray-700 flex items-center justify-center text-[10px]">👤</div>
                                <p class="text-gray-400 text-sm">Organitzat per <span class="text-brand-neon font-bold">{{ event.organizer.name }}</span></p>
                            </div>
                        </div>
                        
                        <div class="bg-brand-black border border-gray-700 rounded-xl p-3 text-center min-w-[70px] shadow-lg">
                            <span class="block text-xs text-brand-neon font-bold uppercase tracking-widest">{{ getMonth(event.start_time) }}</span>
                            <span class="block text-3xl font-black text-white leading-none my-1">{{ getDay(event.start_time) }}</span>
                            <span class="block text-[10px] text-gray-400">{{ getTime(event.start_time) }}</span>
                        </div>
                    </div>

                    <div class="mb-8 border-l-2 border-brand-neon pl-4">
                        <p class="text-gray-300 text-sm leading-relaxed whitespace-pre-line">{{ event.description || 'Sense descripció detallada.' }}</p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4 mb-8">
                        <div class="bg-brand-black/50 p-4 rounded-xl border border-brand-dark flex items-center gap-3">
                            <div class="bg-gray-800 p-2 rounded-full text-brand-neon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" /></svg>
                            </div>
                            <div>
                                <span class="block text-[10px] text-gray-500 uppercase font-bold">Punt de Trobada</span>
                                <span class="text-white font-bold text-sm">{{ event.location || 'Coordenades GPS' }}</span>
                            </div>
                        </div>

                        <div v-if="event.routes && event.routes.length > 0" class="bg-brand-black/50 p-4 rounded-xl border border-brand-dark flex items-center gap-3">
                            <div class="bg-gray-800 p-2 rounded-full text-blue-400">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M4.5 3.75a3 3 0 00-3 3v.75h21v-.75a3 3 0 00-3-3h-15z" /><path fill-rule="evenodd" d="M22.5 9.75h-21v7.5a3 3 0 003 3h15a3 3 0 003-3v-7.5zm-18 3.75a.75.75 0 01.75-.75h6a.75.75 0 010 1.5h-6a.75.75 0 01-.75-.75zm.75 2.25a.75.75 0 000 1.5h3a.75.75 0 000-1.5h-3z" clip-rule="evenodd" /></svg>
                            </div>
                            <div>
                                <span class="block text-[10px] text-gray-500 uppercase font-bold">Ruta Vinculada</span>
                                <span class="text-white font-bold text-sm truncate">{{ event.routes[0].title }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4 border-b border-gray-700 pb-2">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Riders Confirmats ({{ event.participants.length }})</h3>
                        </div>
                        
                        <div v-if="event.participants.length > 0" class="flex flex-wrap gap-3">
                            <div v-for="participant in event.participants" :key="participant.id" class="flex items-center gap-2 bg-brand-black border border-brand-dark pl-2 pr-4 py-1.5 rounded-full shadow-sm">
                                <div class="w-6 h-6 rounded-full bg-gray-700 flex items-center justify-center text-[9px] text-white font-bold border border-gray-600">
                                    {{ participant.name.charAt(0).toUpperCase() }}
                                </div>
                                <span class="text-xs text-gray-300 font-medium" :class="{'text-brand-neon font-bold': participant.id === event.user_id}">
                                    {{ participant.name }} <span v-if="participant.id === event.user_id" class="text-[9px] text-brand-neon ml-1">★</span>
                                </span>
                            </div>
                        </div>
                        <p v-else class="text-gray-600 text-xs italic">Encara no s'ha apuntat ningú. Sigues el primer!</p>
                    </div>

                    <div v-if="$page.props.auth.user.id !== event.user_id">
                        <button 
                            v-if="!isAttending" 
                            @click="joinEvent" 
                            class="w-full bg-brand-neon text-brand-black font-black py-4 rounded-xl uppercase tracking-widest hover:bg-white hover:scale-[1.02] transition shadow-[0_0_20px_rgba(12,225,181,0.4)]"
                        >
                            M'apunto! 🤘
                        </button>
                        
                        <button 
                            v-else 
                            @click="leaveEvent" 
                            class="w-full bg-transparent border-2 border-red-500/30 text-red-400 font-bold py-3 rounded-xl uppercase hover:bg-red-500 hover:text-white transition"
                        >
                            No podré venir 😢
                        </button>
                    </div>
                    <div v-else class="p-4 bg-brand-neon/5 border border-brand-neon/20 rounded-xl text-center">
                        <p class="text-brand-neon text-sm font-bold">Ets l'organitzador d'aquesta quedada 👑</p>
                        <Link :href="route('events.edit', event.id)" class="inline-block mt-2 text-xs text-gray-400 underline hover:text-white">Editar detalls</Link>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { onMounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({
    event: Object,
    isAttending: Boolean
});

// ACCIONS
const joinEvent = () => router.post(route('events.join', props.event.id), {}, { preserveScroll: true });
const leaveEvent = () => router.post(route('events.leave', props.event.id), {}, { preserveScroll: true });

// HELPERS DATA
const getDay = (d) => new Date(d).getDate();
const getMonth = (d) => new Date(d).toLocaleString('ca-ES', { month: 'short' }).replace('.', '');
const getTime = (d) => new Date(d).toLocaleTimeString('ca-ES', { hour: '2-digit', minute: '2-digit' });

// MAPA
onMounted(() => {
    // Si no hi ha coordenades ni rutes, potser no cal carregar el mapa o el centrem a BCN
    const initialLat = props.event.latitude || 41.3851;
    const initialLng = props.event.longitude || 2.1734;

    const map = L.map('map-show', { 
        zoomControl: false, 
        attributionControl: false,
        dragging: false,      // Mapa estàtic (estil fons de pantalla)
        scrollWheelZoom: false,
        doubleClickZoom: false,
        touchZoom: false
    }).setView([initialLat, initialLng], 13);

    L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
        maxZoom: 20,
        className: 'map-tiles-inverse'
    }).addTo(map);

    // 1. PINTAR EL PUNT DE TROBADA
    if (props.event.latitude) {
        L.marker([props.event.latitude, props.event.longitude]).addTo(map);
    }

    // 2. PINTAR LA RUTA (SI N'HI HA)
    if (props.event.routes && props.event.routes.length > 0 && props.event.routes[0].geo_json) {
        try {
            let geoJson = props.event.routes[0].geo_json;
            // A vegades arriba com string, a vegades com objecte
            if (typeof geoJson === 'string') geoJson = JSON.parse(geoJson);
            
            // Comprovem si és un string doblement codificat
            if (typeof geoJson === 'string') geoJson = JSON.parse(geoJson);

            if (Array.isArray(geoJson)) {
                const latlngs = geoJson.map(p => [p.lat, p.lng]);
                const polyline = L.polyline(latlngs, { 
                    color: '#0CE1B5', 
                    weight: 4, 
                    opacity: 0.7 
                }).addTo(map);
                
                // Ajustar el mapa per veure tota la ruta
                map.fitBounds(polyline.getBounds(), { padding: [50, 50] });
            }
        } catch (e) {
            console.error("Error pintant la ruta al mapa:", e);
        }
    }
});
</script>

<style>
/* Filtre fosc per als mapes */
.map-tiles-inverse { filter: brightness(110%) contrast(120%) grayscale(20%); }
</style>
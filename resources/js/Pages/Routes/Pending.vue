<template>
    <AppLayout title="Sincronitzar Rutes">
        <div class="px-4 py-8 pb-24 max-w-2xl mx-auto">
            
            <div class="flex items-center justify-between mb-8">
                <Link :href="route('routes.MyRoutes')" class="text-gray-500 hover:text-white flex items-center gap-1 text-sm bg-brand-surface p-2 rounded-xl border border-brand-dark hover:border-brand-neon transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                    Tornar
                </Link>
            </div>

            <h1 class="text-3xl font-black uppercase tracking-tighter text-white leading-none mb-2">
                Rutes <span class="text-brand-neon mt-1 block">Pendents</span>
            </h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-8">
                Sincronitza les rutes gravades fora de línia
            </p>

            <div v-if="pendingRoutes.length === 0" class="bg-brand-surface border border-brand-dark rounded-2xl p-12 text-center shadow-lg">
                <div class="bg-brand-dark/50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 border border-brand-neon/30">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-brand-neon"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Tot al dia!</h3>
                <p class="text-sm text-gray-500 mb-6">No tens cap ruta gravada pendent de sincronitzar en aquest dispositiu.</p>
                <Link :href="route('routes.index')" class="text-brand-neon border border-brand-neon px-6 py-3 rounded-xl font-bold text-sm hover:bg-brand-neon hover:text-black transition uppercase tracking-widest">
                    Explorar Comunitat
                </Link>
            </div>

            <div v-else class="space-y-6">
                <!-- Informació -->
                <div class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-4 flex gap-4 text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                    <p class="text-xs leading-relaxed">
                        Tens <strong>{{ pendingRoutes.length }}</strong> rutes gravades localment. Si esborres les dades del navegador o canvies de mòbil, les perdràs. Verifica el títol i desenes de km abans de pujar-les.
                    </p>
                </div>

                <div v-for="(route, index) in pendingRoutes" :key="route.id" class="bg-brand-surface border border-brand-dark rounded-2xl p-5 shadow-lg relative overflow-hidden group">
                    <div v-if="syncingId === route.id" class="absolute inset-0 bg-brand-black/80 backdrop-blur-sm z-10 flex flex-col items-center justify-center">
                        <svg class="animate-spin h-8 w-8 text-brand-neon mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span class="text-brand-neon font-black uppercase text-xs tracking-widest">Sincronitzant...</span>
                    </div>

                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <input v-model="route.title" type="text" class="bg-transparent border-b border-brand-dark focus:border-brand-neon text-white font-bold text-lg p-0 pb-1 w-full placeholder-gray-600 focus:ring-0 transition" placeholder="Títol de la ruta...">
                            <p class="text-xs text-gray-500 mt-2 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" /></svg>
                                {{ formatDate(route.created_at) }}
                            </p>
                        </div>
                        <button @click="removeRoute(index)" class="text-red-500 hover:text-white hover:bg-red-500/20 p-2 rounded-xl border border-transparent hover:border-red-500/50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-px bg-brand-dark rounded-xl mb-4 overflow-hidden border border-brand-dark">
                        <div class="bg-brand-surface p-3 text-center">
                            <span class="block text-2xl font-mono text-brand-neon font-black">{{ route.distance_km }}</span>
                            <span class="text-[9px] uppercase tracking-widest text-gray-500 font-bold block mt-1">Quilòmetres</span>
                        </div>
                        <div class="bg-brand-surface p-3 text-center">
                            <span class="block text-2xl font-mono text-white font-black">{{ formatDuration(route.duration_seconds) }}</span>
                            <span class="text-[9px] uppercase tracking-widest text-gray-500 font-bold block mt-1">Temps</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-300">
                            <input type="checkbox" v-model="route.is_public" class="rounded bg-brand-dark border-brand-dark text-brand-neon focus:ring-brand-neon">
                            <span>Ruta Pública</span>
                        </label>
                        
                        <div class="flex-1"></div>

                        <button @click="syncRoute(route, index)" class="flex items-center justify-center gap-2 bg-brand-neon text-black font-black uppercase text-xs tracking-widest py-3 px-6 rounded-xl hover:bg-white transition shadow-neon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                            Pujar
                        </button>
                    </div>
                </div>

                <button v-if="pendingRoutes.length > 1" @click="syncAll" class="w-full bg-brand-dark text-brand-neon font-black uppercase text-xs tracking-widest border border-brand-neon/30 py-4 rounded-xl shadow-lg hover:bg-brand-neon hover:text-black transition">
                    ⚡ Sincronitzar totes ({{ pendingRoutes.length }})
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

const pendingRoutes = ref([]);
const syncingId = ref(null);

onMounted(() => {
    loadPending();
});

const loadPending = () => {
    try {
        const stored = localStorage.getItem('pending_routes');
        if (stored) {
            pendingRoutes.value = JSON.parse(stored).map(r => ({
                ...r,
                title: r.title || `Ruta ${formatDateShort(r.created_at)}`,
                is_public: r.is_public || false
            }));
        }
    } catch (e) {
        console.error('Error llegint memòria local', e);
    }
};

const removeRoute = (index) => {
    if(confirm('Segur que vols esborrar aquesta ruta permanentment? Les dades no es podran recuperar.')) {
        pendingRoutes.value.splice(index, 1);
        localStorage.setItem('pending_routes', JSON.stringify(pendingRoutes.value));
    }
};

const syncRoute = async (routeObj, index) => {
    if(syncingId.value) return; // Ja n'hi ha una sincronitzant
    
    syncingId.value = routeObj.id;
    
    try {
        const payload = {
            title: routeObj.title,
            is_public: routeObj.is_public,
            distance_km: routeObj.distance_km,
            duration_seconds: routeObj.duration_seconds,
            waypoints: routeObj.waypoints,
            created_at: routeObj.created_at,
            original_route_id: routeObj.original_route_id,
            motorcycle_id: routeObj.motorcycle_id
        };

        const { data } = await axios.post(route('routes.sync-offline'), payload);
        
        if (data.success) {
            pendingRoutes.value.splice(index, 1);
            localStorage.setItem('pending_routes', JSON.stringify(pendingRoutes.value));
        }

    } catch (error) {
        alert('Hi ha hagut un error pujant la ruta. Torna-ho a provar o revisa la connexió.');
        console.error(error);
    } finally {
        syncingId.value = null;
    }
};

const syncAll = async () => {
    for (let i = pendingRoutes.value.length - 1; i >= 0; i--) {
        await syncRoute(pendingRoutes.value[i], i);
    }
};

// Formats
const formatDate = (isoStr) => {
    if (!isoStr) return '';
    const d = new Date(isoStr);
    return new Intl.DateTimeFormat('ca-ES', { dateStyle: 'medium', timeStyle: 'short' }).format(d);
};

const formatDateShort = (isoStr) => {
    if (!isoStr) return '';
    const d = new Date(isoStr);
    return `${d.getDate()}/${d.getMonth()+1}/${d.getFullYear()}`;
};

const formatDuration = (sec) => {
    if (!sec) return '00:00';
    const hrs = Math.floor(sec / 3600);
    const mins = Math.floor((sec % 3600) / 60);
    const s = sec % 60;
    if (hrs > 0) return `${hrs}h ${mins}m`;
    return `${mins}m ${s}s`;
};
</script>

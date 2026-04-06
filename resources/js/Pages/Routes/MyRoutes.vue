<template>
    <AppLayout :title="$t('routes.my_routes_title')">
        <div class="px-4 py-6 pb-24">
            
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <button type="button" onclick="window.history.length > 1 ? window.history.back() : window.location.href='/dashboard'" class="w-10 h-10 rounded-full bg-brand-neon flex items-center justify-center text-black hover:bg-white transition flex-shrink-0 shadow-[0_0_15px_rgba(12,225,181,0.3)]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                    </button>
                    <h1 class="text-2xl font-black uppercase tracking-tighter text-white leading-none overflow-hidden text-ellipsis whitespace-nowrap">RUTES</h1>
                </div>
                <button @click="scrollToTrips" class="bg-gray-800 hover:bg-brand-neon hover:text-black text-white px-3 py-2 rounded-lg transition border border-gray-700 flex items-center shrink-0 gap-2 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap">
                    📍 Historial
                </button>
            </div>

            <!-- BÀNNER RECORREGUTS PENDENTS -->
            <div v-if="pendingTrips.length > 0" class="mb-6 bg-brand-dark/30 border border-brand-neon rounded-xl p-4 shadow-[0_0_15px_rgba(12,225,181,0.2)]">
                <div class="flex items-start gap-4">
                    <div class="bg-brand-neon text-black rounded-full p-2 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" /></svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-white font-bold text-lg">Tens {{ pendingTrips.length }} recorregut{{ pendingTrips.length > 1 ? 's' : '' }} pendent{{ pendingTrips.length > 1 ? 's' : '' }} de pujar!</h3>
                        <p class="text-gray-400 text-sm mt-1">Gravats sense connexió, només existeixen en aquest dispositiu.</p>
                        <div class="mt-3">
                            <Link :href="route('routes.pending')" class="inline-block bg-brand-neon text-black font-black uppercase tracking-wider text-xs px-4 py-2 rounded-lg hover:bg-white transition shadow-neon">
                                Sincronitzar ara
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <button 
                v-if="routes.length > 0"
                @click="showFilters = !showFilters"
                class="w-full mb-6 flex items-center justify-between bg-brand-surface border border-brand-dark p-3 rounded-xl text-sm font-bold text-gray-300 hover:text-white hover:border-brand-neon transition"
            >
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>
                    {{ showFilters ? $t('routes.hide_filters') : $t('routes.show_filters') }}
                </span>
                <span v-if="activeFiltersCount > 0" class="bg-brand-neon text-brand-black text-xs rounded-full w-5 h-5 flex items-center justify-center font-black">
                    {{ activeFiltersCount }}
                </span>
                <span v-else>▼</span>
            </button>

            <div v-if="showFilters && routes.length > 0" class="bg-brand-black border border-brand-dark rounded-xl p-4 mb-6 shadow-inner space-y-4 animate-fade-in">
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('routes.search') }}</label>
                        <input v-model="filters.search" type="text" :placeholder="$t('routes.search')" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('routes.difficulty') }}</label>
                        <select v-model="filters.difficulty" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                            <option value="all">{{ $t('routes.difficulty_all') }}</option>
                            <option value="easy">{{ $t('routes.difficulty_easy') }}</option>
                            <option value="medium">{{ $t('routes.difficulty_medium') }}</option>
                            <option value="hard">{{ $t('routes.difficulty_hard') }}</option>
                            <option value="expert">{{ $t('routes.difficulty_expert') }}</option>
                            <option value="extreme">{{ $t('routes.difficulty_extreme') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">Visibilitat</label>
                        <select v-model="filters.visibility" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                            <option value="all">{{ $t('routes.difficulty_all') }}</option>
                            <option value="public">🌍 {{ $t('events.public')}}</option>
                            <option value="private">🔒 {{ $t('events.private')}}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('routes.km_min') }}</label>
                        <input v-model="filters.kmMin" type="number" placeholder="0" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('routes.km_max') }}</label>
                        <input v-model="filters.kmMax" type="number" placeholder="Max" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                </div>

                <div class="border-t border-brand-dark pt-3 mt-2">
                    <label class="text-xs text-brand-neon uppercase font-bold mb-2 block">{{ $t('routes.sort_by') }}</label>
                    <div class="flex gap-2">
                        <select v-model="filters.sortBy" class="flex-1 bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon">
                            <option value="created_at">{{ $t('routes.sort_date') }}</option>
                            <option value="planned_distance_km">{{ $t('routes.sort_km') }}</option>
                        </select>
                        <button @click="toggleSortDir" class="bg-brand-surface border border-brand-dark px-3 rounded-lg text-white hover:border-brand-neon transition">
                            {{ filters.sortDir === 'desc' ? $t('common.desc') : $t('common.asc') }}
                        </button>
                    </div>
                </div>
                
                <button @click="resetFilters" class="w-full text-xs text-gray-500 hover:text-white underline mt-2">
                    {{ $t('routes.clear_all_filters') }}
                </button>
            </div>

            <div v-if="routes.length === 0" class="flex flex-col items-center justify-center py-12 text-center opacity-60">
                <p class="text-gray-400 font-medium">{{ $t('routes.no_my_routes') }}</p>
                <Link :href="route('routes.create')" class="mt-4 text-brand-neon text-sm font-bold border-b border-brand-neon">{{ $t('routes.create_one') }}</Link>
            </div>

            <div v-else-if="filteredRoutes.length === 0" class="flex flex-col items-center justify-center py-12 text-center opacity-60 bg-brand-surface rounded-xl border border-brand-dark border-dashed">
                <p class="text-gray-400 font-medium">{{ $t('routes.no_filter_results') }}</p>
                <button @click="resetFilters" class="text-brand-neon text-sm font-bold mt-2 hover:underline">{{ $t('routes.clear_filters') }}</button>
            </div>

            <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="ruta in filteredRoutes" :key="ruta.id" class="bg-brand-surface rounded-xl overflow-hidden border border-brand-dark shadow-lg group hover:border-brand-neon transition duration-300 flex flex-col animate-fade-in">
                    
                    <div class="h-40 bg-gray-900 relative w-full">
                        <RouteMiniMap :route-id="ruta.id" :geo-json="ruta.geo_json" />
                        
                        <div v-if="ruta.is_public" class="absolute top-2 left-2 bg-brand-neon/90 text-black px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide z-[400] shadow-md">🌍 {{ $t('events.public').replace('🌍 ', '') }}</div>
                        <div v-else class="absolute top-2 left-2 bg-gray-700/90 text-white px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide z-[400] shadow-md border border-gray-600">🔒 {{ $t('events.private').replace('🔒 ', '') }}</div>

                        <div class="absolute top-2 right-2 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide z-[400]"
                            :class="{
                                'bg-green-500/80 text-white': ruta.difficulty === 'easy',
                                'bg-yellow-500/80 text-black': ruta.difficulty === 'medium',
                                'bg-red-500/80 text-white': ruta.difficulty === 'hard',
                                'bg-purple-500/80 text-white': ruta.difficulty === 'expert',
                                'bg-gray-800 text-white border border-gray-600': ruta.difficulty === 'extreme'
                            }">
                            {{ difficultyLabel(ruta.difficulty) }}
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-white mb-1 truncate">{{ ruta.title }}</h3>
                            <p class="text-xs text-gray-500 line-clamp-2 mb-3">{{ ruta.description || $t('routes.no_description') }}</p>
                            
                            <div class="flex items-center gap-4 text-xs text-gray-300 font-mono bg-brand-black/30 p-2 rounded-lg">
                                <span class="flex items-center gap-1">
                                    🏁 {{ ruta.planned_distance_km }} km
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4 pt-4 border-t border-brand-dark/50">
                            <Link v-if="ruta && ruta.id" :href="route('routes.show', ruta.id)" class="flex-1 text-center bg-brand-dark hover:bg-white hover:text-black text-white text-xs font-bold uppercase py-2 rounded transition">
                                {{ $t('routes.view_detail') }}
                            </Link>
                            
                            <Link v-if="ruta && ruta.id" :href="route('routes.edit', ruta.id)" class="px-3 flex items-center justify-center bg-brand-dark border border-gray-600 hover:border-brand-neon hover:text-brand-neon text-gray-400 rounded transition" :title="$t('common.edit')">
                                ✏️
                            </Link>

                            <button 
                                v-if="ruta && ruta.id"
                                @click="deleteRoute(ruta.id)"
                                class="px-3 flex items-center justify-center bg-brand-dark border border-red-900/50 text-red-700 hover:bg-red-500 hover:text-white hover:border-red-500 rounded transition" 
                                :title="$t('common.delete')"
                            >
                                🗑️
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════ -->
            <!-- SECCIÓ: HISTORIAL DE RECORREGUTS          -->
            <!-- ══════════════════════════════════════════ -->
            <div id="historial-recorreguts" class="mt-10 border-t border-brand-dark pt-8">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-black uppercase tracking-tighter text-white leading-none">📍 Recorreguts</h2>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">El teu historial de trajectes GPS reals</p>
                    </div>
                </div>

                <div v-if="loadingTrips" class="text-center py-8 text-gray-500 text-sm">Carregant...</div>

                <div v-else-if="myTrips.length === 0" class="bg-brand-surface border border-brand-dark border-dashed rounded-2xl p-10 text-center opacity-70">
                    <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">Cap recorregut encara</p>
                    <p class="text-xs text-gray-600 mt-2">Inicia una Volta Lliure o un recorregut des d'una Ruta per veure'ls aquí.</p>
                </div>

                <div v-else class="space-y-3">
                    <Link v-for="trip in myTrips" :key="trip.id" :href="route('trips.show', trip.id)"
                        class="flex items-center gap-4 bg-brand-surface border border-brand-dark rounded-2xl p-4 hover:border-brand-neon/50 transition shadow-lg group">
                        <!-- Icona / moto -->
                        <div class="w-12 h-12 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-400"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                        </div>
                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-white font-bold text-sm">{{ formatDate(trip.started_at) }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-xs font-mono">
                                <span class="text-brand-neon font-black">{{ trip.distance_km ?? '?' }} km</span>
                                <span class="text-gray-500">{{ formatDuration(trip.duration_seconds) }}</span>
                                <span v-if="trip.motorcycle" class="text-gray-500 truncate">🏍 {{ trip.motorcycle.brand }} {{ trip.motorcycle.model }}</span>
                            </div>
                            <div v-if="trip.route" class="mt-1">
                                <span class="inline-flex items-center gap-1 bg-blue-500/10 border border-blue-500/20 text-blue-400 px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-widest">
                                    🗺 {{ trip.route.title }}
                                </span>
                            </div>
                        </div>
                        <!-- Arrow -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-600 group-hover:text-brand-neon transition"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                    </Link>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import RouteMiniMap from '@/Components/RouteMiniMap.vue';
import axios from 'axios';

const { t } = useI18n();

const props = defineProps({
    routes: Array
});

const showFilters = ref(false);
const pendingTrips = ref([]);
const myTrips = ref([]);
const loadingTrips = ref(true);

const scrollToTrips = () => {
    document.getElementById('historial-recorreguts')?.scrollIntoView({ behavior: 'smooth' });
};

onMounted(async () => {
    try {
        const stored = localStorage.getItem('pending_trips');
        if (stored) pendingTrips.value = JSON.parse(stored) || [];
    } catch (e) {}

    try {
        const { data } = await axios.get(route('trips.mine'));
        myTrips.value = data;
    } catch (e) {
        console.error('Error carregant recorreguts', e);
    } finally {
        loadingTrips.value = false;
    }
});

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

const filters = ref({
    search: '',
    difficulty: 'all',
    visibility: 'all',
    kmMin: '',
    kmMax: '',
    sortBy: 'created_at',
    sortDir: 'desc'
});

const activeFiltersCount = computed(() => {
    let count = 0;
    if (filters.value.search) count++;
    if (filters.value.difficulty !== 'all') count++;
    if (filters.value.visibility !== 'all') count++;
    if (filters.value.kmMin !== '' || filters.value.kmMax !== '') count++;
    return count;
});

const toggleSortDir = () => {
    filters.value.sortDir = filters.value.sortDir === 'asc' ? 'desc' : 'asc';
};

const resetFilters = () => {
    filters.value = {
        search: '',
        difficulty: 'all',
        visibility: 'all',
        kmMin: '',
        kmMax: '',
        sortBy: 'created_at',
        sortDir: 'desc'
    };
};

const filteredRoutes = computed(() => {
    let result = [...props.routes]; // Creem còpia per no mutar la prop
    
    // 1. Filtre text
    if (filters.value.search) {
        const q = filters.value.search.toLowerCase();
        result = result.filter(r => r.title.toLowerCase().includes(q) || (r.description && r.description.toLowerCase().includes(q)));
    }
    
    // 2. Filtre dificultat
    if (filters.value.difficulty !== 'all') {
        result = result.filter(r => r.difficulty === filters.value.difficulty);
    }

    // 3. Filtre Visibilitat
    if (filters.value.visibility !== 'all') {
        const wantsPublic = filters.value.visibility === 'public';
        result = result.filter(r => !!r.is_public === wantsPublic);
    }

    // 4. Filtre KM
    if (filters.value.kmMin !== '') {
        result = result.filter(r => parseFloat(r.planned_distance_km || 0) >= filters.value.kmMin);
    }
    if (filters.value.kmMax !== '') {
        result = result.filter(r => parseFloat(r.planned_distance_km || 0) <= filters.value.kmMax);
    }

    // 5. Ordenació
    return result.sort((a, b) => {
        let fieldA = a[filters.value.sortBy];
        let fieldB = b[filters.value.sortBy];

        if (filters.value.sortBy === 'planned_distance_km') {
            fieldA = parseFloat(fieldA || 0);
            fieldB = parseFloat(fieldB || 0);
        }

        if (fieldA < fieldB) return filters.value.sortDir === 'asc' ? -1 : 1;
        if (fieldA > fieldB) return filters.value.sortDir === 'asc' ? 1 : -1;
        return 0;
    });
});

const deleteRoute = (id) => {
    if (confirm(t('routes.delete_confirm'))) {
        router.delete(route('routes.destroy', id));
    }
};

const difficultyLabel = (d) => {
    const map = { easy: t('routes.diff_label_easy'), medium: t('routes.diff_label_medium'), hard: t('routes.diff_label_hard'), expert: t('routes.diff_label_expert'), extreme: t('routes.diff_label_extreme') };
    return map[d] || d;
};
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<template>
    <AppLayout title="Les meves Rutes">
        <div class="px-4 py-6 pb-24">
            
            <div class="flex items-center justify-between mb-8">
                <Link :href="route('routes.index')" class="text-gray-500 hover:text-white flex items-center gap-1 text-sm">
                    &larr; Tornar a Comunitat
                </Link>
            </div>

            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black uppercase tracking-tighter text-white leading-none">
                        Les meves <span class="text-brand-neon">Rutes</span>
                    </h1>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">
                        Gestiona les teves gravacions
                    </p>
                </div>
            </div>

            <button 
                v-if="routes.length > 0"
                @click="showFilters = !showFilters"
                class="w-full mb-6 flex items-center justify-between bg-brand-surface border border-brand-dark p-3 rounded-xl text-sm font-bold text-gray-300 hover:text-white hover:border-brand-neon transition"
            >
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>
                    {{ showFilters ? 'Amagar Filtres' : 'Mostrar Filtres i Ordenació' }}
                </span>
                <span v-if="activeFiltersCount > 0" class="bg-brand-neon text-brand-black text-xs rounded-full w-5 h-5 flex items-center justify-center font-black">
                    {{ activeFiltersCount }}
                </span>
                <span v-else>▼</span>
            </button>

            <div v-if="showFilters && routes.length > 0" class="bg-brand-black border border-brand-dark rounded-xl p-4 mb-6 shadow-inner space-y-4 animate-fade-in">
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">Cercar per nom</label>
                        <input v-model="filters.search" type="text" placeholder="Títol de la ruta..." class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">Dificultat</label>
                        <select v-model="filters.difficulty" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                            <option value="all">Totes</option>
                            <option value="easy">🟢 Fàcil</option>
                            <option value="medium">🟡 Mitjana</option>
                            <option value="hard">🔴 Difícil</option>
                            <option value="expert">🟣 Experta</option>
                            <option value="extreme">☠️ Extrema</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">Visibilitat</label>
                        <select v-model="filters.visibility" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                            <option value="all">Totes</option>
                            <option value="public">Només Públiques</option>
                            <option value="private">Només Privades</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">KM Mínim</label>
                        <input v-model="filters.kmMin" type="number" placeholder="0" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">KM Màxim</label>
                        <input v-model="filters.kmMax" type="number" placeholder="Max" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                </div>

                <div class="border-t border-brand-dark pt-3 mt-2">
                    <label class="text-xs text-brand-neon uppercase font-bold mb-2 block">Ordenar per:</label>
                    <div class="flex gap-2">
                        <select v-model="filters.sortBy" class="flex-1 bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon">
                            <option value="created_at">Data D'alta</option>
                            <option value="planned_distance_km">Distància (KM)</option>
                        </select>
                        <button @click="toggleSortDir" class="bg-brand-surface border border-brand-dark px-3 rounded-lg text-white hover:border-brand-neon transition">
                            {{ filters.sortDir === 'desc' ? '⬇ Desc' : '⬆ Asc' }}
                        </button>
                    </div>
                </div>
                
                <button @click="resetFilters" class="w-full text-xs text-gray-500 hover:text-white underline mt-2">
                    Netejar tots els filtres
                </button>
            </div>

            <div v-if="routes.length === 0" class="flex flex-col items-center justify-center py-12 text-center opacity-60">
                <p class="text-gray-400 font-medium">No tens cap ruta gravada.</p>
                <Link :href="route('routes.create')" class="mt-4 text-brand-neon text-sm font-bold border-b border-brand-neon">Crear-ne una ara</Link>
            </div>

            <div v-else-if="filteredRoutes.length === 0" class="flex flex-col items-center justify-center py-12 text-center opacity-60 bg-brand-surface rounded-xl border border-brand-dark border-dashed">
                <p class="text-gray-400 font-medium">No s'ha trobat cap ruta amb aquests filtres.</p>
                <button @click="resetFilters" class="text-brand-neon text-sm font-bold mt-2 hover:underline">Netejar Filtres</button>
            </div>

            <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="ruta in filteredRoutes" :key="ruta.id" class="bg-brand-surface rounded-xl overflow-hidden border border-brand-dark shadow-lg group hover:border-brand-neon transition duration-300 flex flex-col animate-fade-in">
                    
                    <div class="h-40 bg-gray-900 relative w-full">
                        <RouteMiniMap :route-id="ruta.id" :geo-json="ruta.geo_json" />
                        
                        <div v-if="ruta.is_public" class="absolute top-2 left-2 bg-brand-neon/90 text-black px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide z-[400] shadow-md">🌍 Pública</div>
                        <div v-else class="absolute top-2 left-2 bg-gray-700/90 text-white px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide z-[400] shadow-md border border-gray-600">🔒 Privada</div>

                        <div class="absolute top-2 right-2 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide z-[400]"
                            :class="{
                                'bg-green-500/80 text-white': ruta.difficulty === 'easy',
                                'bg-yellow-500/80 text-black': ruta.difficulty === 'medium',
                                'bg-red-500/80 text-white': ruta.difficulty === 'hard',
                                'bg-purple-500/80 text-white': ruta.difficulty === 'expert',
                                'bg-gray-800 text-white border border-gray-600': ruta.difficulty === 'extreme'
                            }">
                            {{ ruta.difficulty === 'easy' ? 'Fàcil' : (ruta.difficulty === 'medium' ? 'Mitjana' : (ruta.difficulty === 'hard' ? 'Difícil' : (ruta.difficulty === 'expert' ? 'Experta' : 'Extrema'))) }}
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-white mb-1 truncate">{{ ruta.title }}</h3>
                            <p class="text-xs text-gray-500 line-clamp-2 mb-3">{{ ruta.description || 'Sense descripció' }}</p>
                            
                            <div class="flex items-center gap-4 text-xs text-gray-300 font-mono bg-brand-black/30 p-2 rounded-lg">
                                <span class="flex items-center gap-1">
                                    🏁 {{ ruta.planned_distance_km }} km
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4 pt-4 border-t border-brand-dark/50">
                            <Link v-if="ruta && ruta.id" :href="route('routes.show', ruta.id)" class="flex-1 text-center bg-brand-dark hover:bg-white hover:text-black text-white text-xs font-bold uppercase py-2 rounded transition">
                                Veure Detall
                            </Link>
                            
                            <Link v-if="ruta && ruta.id" :href="route('routes.edit', ruta.id)" class="px-3 flex items-center justify-center bg-brand-dark border border-gray-600 hover:border-brand-neon hover:text-brand-neon text-gray-400 rounded transition" title="Editar">
                                ✏️
                            </Link>

                            <button 
                                v-if="ruta && ruta.id"
                                @click="deleteRoute(ruta.id)"
                                class="px-3 flex items-center justify-center bg-brand-dark border border-red-900/50 text-red-700 hover:bg-red-500 hover:text-white hover:border-red-500 rounded transition" 
                                title="Eliminar"
                            >
                                🗑️
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import RouteMiniMap from '@/Components/RouteMiniMap.vue';

const props = defineProps({
    routes: Array
});

const showFilters = ref(false);

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
    if (confirm("Estàs segur que vols esborrar aquesta ruta per sempre?")) {
        router.delete(route('routes.destroy', id));
    }
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

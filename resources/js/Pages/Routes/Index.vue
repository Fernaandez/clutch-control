<template>
    <AppLayout title="Rutes de la Comunitat">
        <div class="px-4 py-6 pb-24">
            
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-black uppercase tracking-tighter text-white leading-none">
                        Explorar <span class="text-brand-neon">Rutes</span>
                    </h1>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">
                        Descobreix nous traçats
                    </p>
                </div>
                <Link 
                    :href="route('routes.create')" 
                    class="bg-brand-neon text-brand-black p-2 rounded-full shadow-[0_0_15px_rgba(12,225,181,0.5)] hover:bg-white hover:scale-110 transition flex items-center justify-center"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M12 3.75a.75.75 0 01.75.75v6.75h6.75a.75.75 0 010 1.5h-6.75v6.75a.75.75 0 01-1.5 0v-6.75H4.5a.75.75 0 010-1.5h6.75V4.5a.75.75 0 01.75-.75z" clip-rule="evenodd" /></svg>
                </Link>
            </div>

            <Link :href="route('routes.MyRoutes')" class="w-full mb-4 bg-brand-surface border border-brand-neon/30 text-white py-3 rounded-xl flex items-center justify-between px-4 hover:bg-brand-neon/10 transition group shadow-lg">
                <span class="font-bold uppercase flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-brand-neon"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" /></svg>
                    Gestionar les meves Rutes
                </span>
                <span class="text-brand-neon group-hover:translate-x-1 transition">&rarr;</span>
            </Link>



            <button 
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

            <div v-if="showFilters" class="bg-brand-black border border-brand-dark rounded-xl p-4 mb-6 shadow-inner space-y-4 animate-fade-in">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">Cercar per nom</label>
                        <input v-model="filters.search" type="text" placeholder="Ex: Collada..." class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">Dificultat</label>
                        <select v-model="filters.difficulty" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                            <option value="all">Totes</option>
                            <option value="easy">🟢 Fàcil</option>
                            <option value="medium">🟡 Mitjana</option>
                            <option value="hard">🔴 Difícil</option>
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
                            <option value="created_at">Data de Publicació</option>
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

            <div v-if="filteredRoutes.length === 0" class="flex flex-col items-center justify-center py-12 text-center opacity-60 bg-brand-surface rounded-xl border border-brand-dark border-dashed">
                <p class="text-gray-400 font-medium">No s'han trobat rutes amb aquests filtres.</p>
                <button @click="resetFilters" class="text-brand-neon text-sm font-bold mt-2 hover:underline">Netejar Filtres</button>
            </div>

            <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="ruta in filteredRoutes" :key="ruta.id" class="bg-brand-surface rounded-xl overflow-hidden border border-brand-dark shadow-lg group hover:border-brand-neon transition duration-300 flex flex-col relative animate-fade-in">
                    
                    <div class="h-40 bg-gray-900 relative w-full">
                        <RouteMiniMap :route-id="ruta.id" :geo-json="ruta.geo_json" />
                        
                        <div class="absolute top-2 right-2 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide z-[400]"
                            :class="{
                                'bg-green-500/80 text-white': ruta.difficulty === 'easy',
                                'bg-yellow-500/80 text-black': ruta.difficulty === 'medium',
                                'bg-red-500/80 text-white': ruta.difficulty === 'hard'
                            }">
                            {{ ruta.difficulty === 'easy' ? 'Fàcil' : (ruta.difficulty === 'medium' ? 'Mitjana' : 'Difícil') }}
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-white mb-1 truncate">{{ ruta.title }}</h3>
                            <p class="text-[10px] text-brand-neon uppercase tracking-wider mb-2 font-bold">
                                Per: {{ ruta.user ? ruta.user.name : 'Rider' }}
                            </p>
                            <p class="text-xs text-gray-500 line-clamp-2 mb-3">{{ ruta.description || 'Sense descripció' }}</p>
                            
                            <div class="flex items-center gap-4 text-xs text-gray-300 font-mono bg-brand-black/30 p-2 rounded-lg">
                                <span class="flex items-center gap-1">
                                    🏁 {{ ruta.planned_distance_km }} km
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4 pt-4 border-t border-brand-dark/50">
                            <Link :href="route('routes.show', ruta.id)" class="flex-1 text-center bg-brand-dark hover:bg-white hover:text-black text-white text-xs font-bold uppercase py-2 rounded transition">
                                Veure Detall
                            </Link>
                            
                            <Link 
                                v-if="ruta.user_id !== $page.props.auth.user.id"
                                :href="route('routes.clone', ruta.id)" 
                                method="post" 
                                as="button"
                                class="px-3 flex items-center justify-center bg-brand-black border border-brand-neon/50 hover:bg-brand-neon hover:text-black text-brand-neon rounded transition" 
                                title="Guardar i Editar"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" /></svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botó Flotant Cerca Codi -->
        <button @click="showTokenModal = true" class="fixed bottom-24 right-4 z-40 bg-brand-surface text-white p-3 rounded-full border border-brand-dark shadow-lg flex items-center justify-center hover:scale-110 hover:border-brand-neon hover:text-brand-neon transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
        </button>

        <!-- Modal Cerca Codi -->
        <div v-if="showTokenModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div @click="closeTokenModal" class="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity"></div>
            <div class="relative bg-brand-surface border border-brand-neon rounded-2xl shadow-[0_0_30px_rgba(12,225,181,0.3)] w-full max-w-sm overflow-hidden transform transition-all scale-100">
                <div class="bg-brand-black p-4 flex justify-between items-center border-b border-brand-dark">
                    <h3 class="text-white font-bold text-lg">Tens un codi de ruta?</h3>
                    <button @click="closeTokenModal" class="text-gray-400 hover:text-white">✕</button>
                </div>
                <div class="p-6">
                    <form @submit.prevent="submitSearchForm" class="flex flex-col gap-4">
                        <input ref="tokenInputRef" v-model="searchForm.token" type="text" placeholder="Introdueix els 12 caràcters..." class="w-full bg-brand-black border border-brand-dark rounded-lg text-white font-mono uppercase focus:ring-brand-neon focus:border-brand-neon placeholder-gray-600 px-4 py-3 text-center tracking-widest">
                        <div v-if="searchForm.errors.token" class="text-red-500 text-sm font-bold text-center">{{ searchForm.errors.token }}</div>
                        <button type="submit" class="w-full bg-brand-neon text-brand-black font-black px-6 py-3 rounded-lg uppercase tracking-wider hover:bg-white transition whitespace-nowrap shadow-neon flex justify-center items-center gap-2" :disabled="searchForm.processing || !searchForm.token">
                            {{ searchForm.processing ? 'Buscant...' : 'Obrir Ruta 🧭' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, usePage, useForm } from '@inertiajs/vue3';
import RouteMiniMap from '@/Components/RouteMiniMap.vue';

const props = defineProps({
    routes: Array
});

const showFilters = ref(false);

const showTokenModal = ref(false);
const tokenInputRef = ref(null);

const searchForm = useForm({ token: '' });

const closeTokenModal = () => {
    showTokenModal.value = false;
    searchForm.reset();
    searchForm.clearErrors();
};

const submitSearchForm = () => {
    if(!searchForm.token) return;
    searchForm.post(route('search.token'), {
        preserveScroll: true,
        onError: () => {
             // Es queda obert mostrant l'error
        }
    });
};

const filters = ref({
    search: '',
    difficulty: 'all',
    kmMin: '',
    kmMax: '',
    sortBy: 'created_at',
    sortDir: 'desc'
});

const activeFiltersCount = computed(() => {
    let count = 0;
    if (filters.value.search) count++;
    if (filters.value.difficulty !== 'all') count++;
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

    // 3. Filtre KM
    if (filters.value.kmMin !== '') {
        result = result.filter(r => parseFloat(r.planned_distance_km || 0) >= filters.value.kmMin);
    }
    if (filters.value.kmMax !== '') {
        result = result.filter(r => parseFloat(r.planned_distance_km || 0) <= filters.value.kmMax);
    }

    // 4. Ordenació
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
<template>
    <AppLayout :current-moto-id="motorcycle.id">
        <div class="px-4 py-6 pb-24">
            
            <div class="flex items-center justify-between mb-6">
                <div>
                    <Link :href="route('dashboard', motorcycle.id)" class="text-gray-400 text-sm hover:text-white flex items-center gap-1 transition">
                        &larr; Tornar
                    </Link>
                    <h1 class="text-2xl font-bold text-white mt-1">Historial Complet 📜</h1>
                    <p class="text-brand-muted text-sm">{{ motorcycle.brand }} {{ motorcycle.model }}</p>
                </div>
                
                <div class="text-right bg-brand-surface border border-brand-dark px-3 py-2 rounded-lg shadow-lg">
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest">Total Filtrat</p>
                    <p class="text-xl font-mono font-bold text-brand-neon">{{ totalFilteredCost.toFixed(2) }} €</p>
                </div>
            </div>

            <button 
                @click="showFilters = !showFilters"
                class="w-full mb-4 flex items-center justify-between bg-brand-surface border border-brand-dark p-3 rounded-xl text-sm font-bold text-gray-300 hover:text-white hover:border-brand-neon transition"
            >
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>
                    {{ showFilters ? 'Amagar Filtres' : 'Mostrar Filtres i Ordenació' }}
                </span>
                <span v-if="activeFiltersCount > 0" class="bg-brand-neon text-brand-black text-xs rounded-full w-5 h-5 flex items-center justify-center">
                    {{ activeFiltersCount }}
                </span>
                <span v-else>▼</span>
            </button>

            <div v-if="showFilters" class="bg-brand-black border border-brand-dark rounded-xl p-4 mb-6 shadow-inner space-y-4 animate-fade-in-down">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">Cercar</label>
                        <input v-model="filters.search" type="text" placeholder="Ex: Rodes, Oli..." class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon focus:ring-0 mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">Tipus</label>
                        <select v-model="filters.type" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon focus:ring-0 mt-1">
                            <option value="all">Tots</option>
                            <option value="maintenance">Manteniment</option>
                            <option value="repair">Reparació</option>
                            <option value="upgrade">Millora</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">Data Inici</label>
                        <input v-model="filters.dateStart" type="date" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">Data Fi</label>
                        <input v-model="filters.dateEnd" type="date" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>

                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">KM Mínim</label>
                        <input v-model="filters.kmMin" type="number" placeholder="0" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">KM Màxim</label>
                        <input v-model="filters.kmMax" type="number" placeholder="Max" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>

                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">Preu Mín (€)</label>
                        <input v-model="filters.priceMin" type="number" placeholder="0" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">Preu Màx (€)</label>
                        <input v-model="filters.priceMax" type="number" placeholder="Max" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                </div>

                <div class="border-t border-brand-dark pt-3 mt-2">
                    <label class="text-xs text-brand-neon uppercase font-bold mb-2 block">Ordenar per:</label>
                    <div class="flex gap-2">
                        <select v-model="filters.sortBy" class="flex-1 bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon focus:ring-0">
                            <option value="date">Data</option>
                            <option value="cost">Preu</option>
                            <option value="km_at_moment">Quilometratge</option>
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


            <div v-if="filteredHistory.length === 0" class="text-center py-10 text-gray-500 bg-brand-surface rounded-xl border border-brand-dark border-dashed">
                <p>No s'han trobat resultats amb aquests filtres.</p>
                <button @click="resetFilters" class="text-brand-neon text-sm font-bold mt-2 hover:underline">Netejar Filtres</button>
            </div>

            <div v-else class="space-y-4">
                <div v-for="log in filteredHistory" :key="log.id" class="bg-brand-surface rounded-xl p-4 border border-brand-dark shadow-lg relative flex justify-between items-center group hover:border-brand-neon transition animate-fade-in">
                    
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span v-if="log.type === 'maintenance'" class="bg-blue-500/20 text-blue-400 text-[10px] font-bold px-2 py-0.5 rounded uppercase border border-blue-500/30">Manteniment</span>
                            <span v-else-if="log.type === 'repair'" class="bg-red-500/20 text-red-400 text-[10px] font-bold px-2 py-0.5 rounded uppercase border border-red-500/30">Reparació</span>
                            <span v-else-if="log.type === 'upgrade'" class="bg-purple-500/20 text-purple-400 text-[10px] font-bold px-2 py-0.5 rounded uppercase border border-purple-500/30">Millora</span>
                            
                            <span class="text-xs text-gray-500 font-mono">{{ formatDate(log.date) }}</span>
                        </div>
                        
                        <h3 class="text-lg font-bold text-white">{{ log.task_title }}</h3>
                        <p class="text-sm text-gray-400 mt-0.5">{{ log.description }}</p>
                    </div>

                    <div class="text-right min-w-[80px]">
                        <p class="text-lg font-bold text-white">{{ log.cost }} €</p>
                        <p class="text-xs text-gray-500">{{ log.km_at_moment }} km</p>
                    </div>

                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    motorcycle: Object,
    history: Array, 
    // Ignorem el totalCost que ve del backend perquè el calculem dinàmicament
});

const showFilters = ref(false);

// ESTAT DELS FILTRES
const filters = ref({
    search: '',
    type: 'all',
    dateStart: '',
    dateEnd: '',
    priceMin: '',
    priceMax: '',
    kmMin: '',
    kmMax: '',
    sortBy: 'date',
    sortDir: 'desc'
});

// FUNCIÓ MÀGICA: FILTRATGE I ORDENACIÓ
const filteredHistory = computed(() => {
    let result = props.history;

    // 1. Filtre Cerca (Text)
    if (filters.value.search) {
        const q = filters.value.search.toLowerCase();
        result = result.filter(log => 
            log.task_title.toLowerCase().includes(q) || 
            (log.description && log.description.toLowerCase().includes(q))
        );
    }

    // 2. Filtre Tipus
    if (filters.value.type !== 'all') {
        result = result.filter(log => log.type === filters.value.type);
    }

    // 3. Filtre Data
    if (filters.value.dateStart) {
        result = result.filter(log => log.date >= filters.value.dateStart);
    }
    if (filters.value.dateEnd) {
        result = result.filter(log => log.date <= filters.value.dateEnd);
    }

    // 4. Filtre KM
    if (filters.value.kmMin !== '') {
        result = result.filter(log => log.km_at_moment >= filters.value.kmMin);
    }
    if (filters.value.kmMax !== '') {
        result = result.filter(log => log.km_at_moment <= filters.value.kmMax);
    }

    // 5. Filtre Preu
    if (filters.value.priceMin !== '') {
        result = result.filter(log => parseFloat(log.cost) >= filters.value.priceMin);
    }
    if (filters.value.priceMax !== '') {
        result = result.filter(log => parseFloat(log.cost) <= filters.value.priceMax);
    }

    // 6. Ordenació
    return result.sort((a, b) => {
        let fieldA = a[filters.value.sortBy];
        let fieldB = b[filters.value.sortBy];

        // Convertir a números si cal
        if (filters.value.sortBy === 'cost' || filters.value.sortBy === 'km_at_moment') {
            fieldA = parseFloat(fieldA);
            fieldB = parseFloat(fieldB);
        }

        if (fieldA < fieldB) return filters.value.sortDir === 'asc' ? -1 : 1;
        if (fieldA > fieldB) return filters.value.sortDir === 'asc' ? 1 : -1;
        return 0;
    });
});

// CÀLCUL DINÀMIC DEL TOTAL (Basat en la llista filtrada)
const totalFilteredCost = computed(() => {
    return filteredHistory.value.reduce((acc, log) => acc + parseFloat(log.cost || 0), 0);
});

// Comptar quants filtres actius hi ha (per l'indicador visual)
const activeFiltersCount = computed(() => {
    let count = 0;
    if (filters.value.search) count++;
    if (filters.value.type !== 'all') count++;
    if (filters.value.dateStart || filters.value.dateEnd) count++;
    if (filters.value.priceMin !== '' || filters.value.priceMax !== '') count++;
    if (filters.value.kmMin !== '' || filters.value.kmMax !== '') count++;
    return count;
});

const toggleSortDir = () => {
    filters.value.sortDir = filters.value.sortDir === 'asc' ? 'desc' : 'asc';
};

const resetFilters = () => {
    filters.value = {
        search: '',
        type: 'all',
        dateStart: '',
        dateEnd: '',
        priceMin: '',
        priceMax: '',
        kmMin: '',
        kmMax: '',
        sortBy: 'date',
        sortDir: 'desc'
    };
};

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('ca-ES', options);
};
</script>

<style scoped>
/* Petita animació d'entrada suau */
.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
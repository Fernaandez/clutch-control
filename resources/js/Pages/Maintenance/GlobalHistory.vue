<template>
    <AppLayout :current-moto-id="motorcycle.id">
        <div class="px-4 py-6 pb-24">
            
            <div class="flex items-center gap-4 mb-6">
                <Link :href="route('dashboard', motorcycle.id)" class="inline-flex items-center justify-center w-10 h-10 flex-shrink-0 rounded-full bg-brand-dark border border-brand-neon/50 text-brand-neon hover:bg-brand-neon hover:text-brand-black transition shadow-[0_0_10px_rgba(12,225,181,0.2)]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                </Link>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-white">{{ $t('maintenance.full_history_title') }}</h1>
                    <p class="text-brand-muted text-sm">{{ motorcycle.brand }} {{ motorcycle.model }}</p>
                </div>
                <div class="text-right bg-brand-surface border border-brand-dark px-3 py-2 rounded-lg shadow-lg">
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest">{{ $t('maintenance.total_filtered') }}</p>
                    <p class="text-xl font-mono font-bold text-brand-neon">{{ totalFilteredCost.toFixed(2) }} €</p>
                </div>
            </div>

            <button 
                @click="showFilters = !showFilters"
                class="w-full mb-4 flex items-center justify-between bg-brand-surface border border-brand-dark p-3 rounded-xl text-sm font-bold text-gray-300 hover:text-white hover:border-brand-neon transition"
            >
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>
                    {{ showFilters ? $t('maintenance.hide_filters') : $t('maintenance.show_filters') }}
                </span>
                <span v-if="activeFiltersCount > 0" class="bg-brand-neon text-brand-black text-xs rounded-full w-5 h-5 flex items-center justify-center">
                    {{ activeFiltersCount }}
                </span>
                <span v-else>▼</span>
            </button>

            <div v-if="showFilters" class="bg-brand-black border border-brand-dark rounded-xl p-4 mb-6 shadow-inner space-y-4 animate-fade-in-down">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('maintenance.search') }}</label>
                        <input v-model="filters.search" type="text" :placeholder="$t('maintenance.search_placeholder')" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon focus:ring-0 mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('maintenance.type') }}</label>
                        <select v-model="filters.type" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon focus:ring-0 mt-1">
                            <option value="all">{{ $t('maintenance.type_all') }}</option>
                            <option value="maintenance">{{ $t('maintenance.type_maintenance') }}</option>
                            <option value="repair">{{ $t('maintenance.type_repair') }}</option>
                            <option value="upgrade">{{ $t('maintenance.type_upgrade') }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('maintenance.date_start') }}</label>
                        <input v-model="filters.dateStart" type="date" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('maintenance.date_end') }}</label>
                        <input v-model="filters.dateEnd" type="date" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>

                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('maintenance.km_min') }}</label>
                        <input v-model="filters.kmMin" type="number" placeholder="0" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('maintenance.km_max') }}</label>
                        <input v-model="filters.kmMax" type="number" placeholder="Max" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>

                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('maintenance.price_min') }}</label>
                        <input v-model="filters.priceMin" type="number" placeholder="0" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('maintenance.price_max') }}</label>
                        <input v-model="filters.priceMax" type="number" placeholder="Max" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                </div>

                <div class="border-t border-brand-dark pt-3 mt-2">
                    <label class="text-xs text-brand-neon uppercase font-bold mb-2 block">{{ $t('maintenance.sort_by') }}</label>
                    <div class="flex gap-2">
                        <select v-model="filters.sortBy" class="flex-1 bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon focus:ring-0">
                            <option value="date">{{ $t('maintenance.sort_date') }}</option>
                            <option value="cost">{{ $t('maintenance.sort_price') }}</option>
                            <option value="km_at_moment">{{ $t('maintenance.sort_km') }}</option>
                        </select>
                        <button @click="toggleSortDir" class="bg-brand-surface border border-brand-dark px-3 rounded-lg text-white hover:border-brand-neon transition">
                            {{ filters.sortDir === 'desc' ? $t('common.desc') : $t('common.asc') }}
                        </button>
                    </div>
                </div>
                
                <button @click="resetFilters" class="w-full text-xs text-gray-500 hover:text-white underline mt-2">
                    {{ $t('maintenance.clear_all_filters') }}
                </button>
            </div>


            <div v-if="filteredHistory.length === 0" class="text-center py-10 text-gray-500 bg-brand-surface rounded-xl border border-brand-dark border-dashed">
                <p>{{ $t('maintenance.no_results') }}</p>
                <button @click="resetFilters" class="text-brand-neon text-sm font-bold mt-2 hover:underline">{{ $t('maintenance.clear_filters') }}</button>
            </div>

            <div v-else class="space-y-4">
                <div v-for="log in filteredHistory" :key="log.id" class="bg-brand-surface rounded-xl p-4 border border-brand-dark shadow-lg relative flex justify-between items-center group hover:border-brand-neon transition animate-fade-in">
                    
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span v-if="log.type === 'maintenance'" class="bg-blue-500/20 text-blue-400 text-[10px] font-bold px-2 py-0.5 rounded uppercase border border-blue-500/30">{{ $t('maintenance.type_maintenance') }}</span>
                            <span v-else-if="log.type === 'repair'" class="bg-red-500/20 text-red-400 text-[10px] font-bold px-2 py-0.5 rounded uppercase border border-red-500/30">{{ $t('maintenance.type_repair') }}</span>
                            <span v-else-if="log.type === 'upgrade'" class="bg-purple-500/20 text-purple-400 text-[10px] font-bold px-2 py-0.5 rounded uppercase border border-purple-500/30">{{ $t('maintenance.type_upgrade') }}</span>
                            
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
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const { locale } = useI18n();

const props = defineProps({
    motorcycle: Object,
    history: Array, 
});

const showFilters = ref(false);

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

const filteredHistory = computed(() => {
    let result = props.history;

    if (filters.value.search) {
        const q = filters.value.search.toLowerCase();
        result = result.filter(log => 
            log.task_title.toLowerCase().includes(q) || 
            (log.description && log.description.toLowerCase().includes(q))
        );
    }

    if (filters.value.type !== 'all') {
        result = result.filter(log => log.type === filters.value.type);
    }

    if (filters.value.dateStart) {
        result = result.filter(log => log.date >= filters.value.dateStart);
    }
    if (filters.value.dateEnd) {
        result = result.filter(log => log.date <= filters.value.dateEnd);
    }

    if (filters.value.kmMin !== '') {
        result = result.filter(log => log.km_at_moment >= filters.value.kmMin);
    }
    if (filters.value.kmMax !== '') {
        result = result.filter(log => log.km_at_moment <= filters.value.kmMax);
    }

    if (filters.value.priceMin !== '') {
        result = result.filter(log => parseFloat(log.cost) >= filters.value.priceMin);
    }
    if (filters.value.priceMax !== '') {
        result = result.filter(log => parseFloat(log.cost) <= filters.value.priceMax);
    }

    return result.sort((a, b) => {
        let fieldA = a[filters.value.sortBy];
        let fieldB = b[filters.value.sortBy];

        if (filters.value.sortBy === 'cost' || filters.value.sortBy === 'km_at_moment') {
            fieldA = parseFloat(fieldA);
            fieldB = parseFloat(fieldB);
        }

        if (fieldA < fieldB) return filters.value.sortDir === 'asc' ? -1 : 1;
        if (fieldA > fieldB) return filters.value.sortDir === 'asc' ? 1 : -1;
        return 0;
    });
});

const totalFilteredCost = computed(() => {
    return filteredHistory.value.reduce((acc, log) => acc + parseFloat(log.cost || 0), 0);
});

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
    return new Date(dateString).toLocaleDateString(locale.value + '-ES', options);
};
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

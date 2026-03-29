<template>
    <AppLayout>
        <div class="px-4 py-6"> 
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-wide">
                    {{ $t('motorcycles.my_garage').split(' ').slice(0,-1).join(' ') }} <span class="text-brand-neon">{{ $t('motorcycles.my_garage').split(' ').slice(-1)[0] }}</span>
                </h1>
                
                <Link :href="route('motorcycles.create')" class="bg-brand-neon text-brand-black px-5 py-2.5 rounded-lg font-bold shadow-[0_0_15px_rgba(12,225,181,0.3)] hover:bg-white hover:shadow-[0_0_25px_rgba(12,225,181,0.5)] transition-all duration-300 transform active:scale-95 text-sm uppercase tracking-wider whitespace-nowrap">
                    {{ $t('motorcycles.new_moto') }}
                </Link>
            </div>

            <button 
                v-if="motos.length > 0"
                @click="showFilters = !showFilters"
                class="w-full mb-6 flex items-center justify-between bg-brand-surface border border-brand-dark p-3 rounded-xl text-sm font-bold text-gray-300 hover:text-white hover:border-brand-neon transition"
            >
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>
                    {{ showFilters ? $t('motorcycles.hide_filters') : $t('motorcycles.show_filters') }}
                </span>
                <span v-if="activeFiltersCount > 0" class="bg-brand-neon text-brand-black text-xs rounded-full w-5 h-5 flex items-center justify-center font-black">
                    {{ activeFiltersCount }}
                </span>
                <span v-else>▼</span>
            </button>

            <div v-if="showFilters && motos.length > 0" class="bg-brand-black border border-brand-dark rounded-xl p-4 mb-6 shadow-inner space-y-4 animate-fade-in">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('motorcycles.search') }}</label>
                        <input v-model="filters.search" type="text" :placeholder="$t('motorcycles.search_placeholder')" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('motorcycles.style') }}</label>
                        <select v-model="filters.type" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                            <option value="all">{{ $t('motorcycles.all_styles') }}</option>
                            <option value="Naked">Naked</option>
                            <option value="Sport">Sport / R</option>
                            <option value="Trail">Trail / Adventure</option>
                            <option value="Custom">Custom / Cruiser</option>
                            <option value="Scooter">Scooter / Maxi</option>
                            <option value="Touring">Touring</option>
                            <option value="Off-Road">Off-Road / Enduro</option>
                            <option value="Classic">{{ $t('motorcycles.style_classic') }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('motorcycles.year_min') }}</label>
                        <input v-model="filters.yearMin" type="number" placeholder="Ex: 2010" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('motorcycles.year_max') }}</label>
                        <input v-model="filters.yearMax" type="number" placeholder="Ex: 2024" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('motorcycles.km_max') }}</label>
                        <input v-model="filters.kmMax" type="number" placeholder="50000" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                </div>

                <div class="border-t border-brand-dark pt-3 mt-2">
                    <label class="text-xs text-brand-neon uppercase font-bold mb-2 block">{{ $t('motorcycles.sort_by') }}</label>
                    <div class="flex gap-2">
                        <select v-model="filters.sortBy" class="flex-1 bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon">
                            <option value="brand">{{ $t('motorcycles.sort_brand') }}</option>
                            <option value="year">{{ $t('motorcycles.sort_year') }}</option>
                            <option value="current_km">{{ $t('motorcycles.sort_km') }}</option>
                            <option value="created_at">{{ $t('motorcycles.sort_date') }}</option>
                        </select>
                        <button @click="toggleSortDir" class="bg-brand-surface border border-brand-dark px-3 rounded-lg text-white hover:border-brand-neon transition">
                            {{ filters.sortDir === 'asc' ? $t('motorcycles.asc') : $t('motorcycles.desc') }}
                        </button>
                    </div>
                </div>
                
                <button @click="resetFilters" class="w-full text-xs text-gray-500 hover:text-white underline mt-2">
                    {{ $t('motorcycles.clear_all_filters') }}
                </button>
            </div>

            <div v-if="motos.length === 0" class="flex flex-col items-center justify-center py-20 bg-brand-surface border border-brand-dark border-dashed rounded-2xl text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-brand-dark mb-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7m4.5 4.5 3-3m-3 3 1.5 4.5m-1.5-4.5-4.5 1.5" />
                </svg>
                <p class="text-sm">{{ $t('motorcycles.empty_garage') }}</p>
                <Link :href="route('motorcycles.create')" class="mt-4 text-brand-neon font-bold text-xs uppercase hover:text-white transition">{{ $t('motorcycles.add_now') }}</Link>
            </div>

            <div v-else-if="filteredMotos.length === 0" class="flex flex-col items-center justify-center py-12 text-center opacity-60 bg-brand-surface rounded-xl border border-brand-dark border-dashed">
                <p class="text-gray-400 font-medium">{{ $t('motorcycles.no_results') }}</p>
                <button @click="resetFilters" class="text-brand-neon text-sm font-bold mt-2 hover:underline">{{ $t('motorcycles.clear_filters') }}</button>
            </div>

             <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div 
                    v-for="moto in filteredMotos" 
                    :key="moto.id" 
                    class="bg-brand-surface rounded-xl border border-brand-dark shadow-lg hover:border-brand-neon transition duration-300 group relative overflow-hidden"
                >
                    <Link :href="route('dashboard', moto.id)" class="block">
                        <div v-if="moto.photo" class="h-48 w-full overflow-hidden border-b border-brand-dark">
                            <img :src="$page.props.storageUrl + '/' + moto.photo" alt="Foto Moto" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        </div>
                        <div class="p-6 pb-12">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h2 class="text-xl font-bold text-white group-hover:text-brand-neon transition">{{ moto.brand }} {{ moto.model }}</h2>
                                    <p class="text-brand-muted mt-2">{{ moto.plate }}</p>
                                </div>
                                <span class="text-xs font-mono text-gray-500 bg-brand-black px-2 py-1 rounded border border-brand-dark">{{ moto.year }}</span>
                            </div>

                            <div class="mt-4 pt-4 border-t border-brand-dark flex justify-between items-center">
                                <span class="text-brand-neon font-mono font-bold">{{ moto.current_km }} km</span>
                            </div>
                        </div>
                    </Link>

                    <Link :href="route('motorcycles.edit', moto.id)" class="absolute bottom-3 right-3 z-10 bg-brand-dark hover:bg-brand-neon hover:text-brand-black text-gray-300 p-2 rounded-full transition shadow-lg border border-transparent hover:border-brand-neon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </Link>

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
    motos: Array
});

const showFilters = ref(false);

const filters = ref({
    search: '',
    type: 'all',
    yearMin: '',
    yearMax: '',
    kmMax: '',
    sortBy: 'created_at',
    sortDir: 'desc'
});

const activeFiltersCount = computed(() => {
    let count = 0;
    if (filters.value.search) count++;
    if (filters.value.type !== 'all') count++;
    if (filters.value.yearMin !== '' || filters.value.yearMax !== '') count++;
    if (filters.value.kmMax !== '') count++;
    return count;
});

const toggleSortDir = () => {
    filters.value.sortDir = filters.value.sortDir === 'asc' ? 'desc' : 'asc';
};

const resetFilters = () => {
    filters.value = {
        search: '',
        type: 'all',
        yearMin: '',
        yearMax: '',
        kmMax: '',
        sortBy: 'created_at',
        sortDir: 'desc'
    };
};

const filteredMotos = computed(() => {
    let result = [...props.motos];
    
    if (filters.value.search) {
        const q = filters.value.search.toLowerCase();
        result = result.filter(m => 
            m.brand.toLowerCase().includes(q) || 
            m.model.toLowerCase().includes(q) ||
            m.plate?.toLowerCase().includes(q)
        );
    }
    
    if (filters.value.type !== 'all') {
        result = result.filter(m => m.type === filters.value.type);
    }

    if (filters.value.yearMin !== '') {
        result = result.filter(m => parseInt(m.year) >= parseInt(filters.value.yearMin));
    }
    if (filters.value.yearMax !== '') {
        result = result.filter(m => parseInt(m.year) <= parseInt(filters.value.yearMax));
    }
    
    if (filters.value.kmMax !== '') {
        result = result.filter(m => parseInt(m.current_km) <= parseInt(filters.value.kmMax));
    }

    return result.sort((a, b) => {
        let fieldA, fieldB;

        if (filters.value.sortBy === 'brand') {
            fieldA = a.brand + a.model;
            fieldB = b.brand + b.model;
        } else if (filters.value.sortBy === 'year' || filters.value.sortBy === 'current_km') {
            fieldA = parseInt(a[filters.value.sortBy] || 0);
            fieldB = parseInt(b[filters.value.sortBy] || 0);
        } else {
            fieldA = a[filters.value.sortBy];
            fieldB = b[filters.value.sortBy];
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

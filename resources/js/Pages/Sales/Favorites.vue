<template>
    <AppLayout :title="$t('sales.favorites')">
        <div class="px-4 py-6 pb-24 max-w-7xl mx-auto">
            
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-black text-white uppercase tracking-tighter">{{ $t('sales.saved_motos') }}</h1>
                    <p class="text-gray-400 text-sm">{{ $t('sales.saved_motos_subtitle') }}</p>
                </div>
            </div>

            <Link :href="route('sales.index')" class="w-full mb-6 bg-brand-surface border border-brand-dark hover:border-brand-neon text-white py-3 rounded-xl flex items-center justify-center px-4 hover:text-brand-neon transition group shadow-lg">
                <span class="font-bold uppercase flex items-center gap-2 text-sm text-center w-full justify-center">
                    &larr; {{ $t('sales.back_to_market') }}
                </span>
            </Link>

            <button 
                @click="showFilters = !showFilters"
                class="w-full mb-6 flex items-center justify-between bg-brand-surface border border-brand-dark p-3 rounded-xl text-sm font-bold text-gray-300 hover:text-white hover:border-brand-neon transition shadow-lg"
            >
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-brand-neon"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>
                    {{ showFilters ? $t('sales.hide_filters') : $t('sales.show_filters') }}
                </span>
                <span v-if="activeFiltersCount > 0" class="bg-brand-neon text-brand-black text-xs rounded-full w-5 h-5 flex items-center justify-center font-black">
                    {{ activeFiltersCount }}
                </span>
                <span v-else>▼</span>
            </button>

            <div v-if="showFilters" class="bg-brand-black border border-brand-dark rounded-xl p-5 mb-6 shadow-2xl space-y-6 animate-fade-in">
                
                <div>
                    <label class="text-[10px] text-gray-500 uppercase tracking-widest font-black mb-1 block">{{ $t('sales.search') }}</label>
                    <input v-model="filters.search" type="text" :placeholder="$t('sales.search_placeholder')" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon focus:ring-0">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div class="space-y-4 bg-brand-surface/50 p-4 rounded-xl border border-brand-dark/50">
                        <h3 class="text-xs font-black text-brand-neon uppercase tracking-widest mb-3 border-b border-brand-dark pb-2">{{ $t('sales.filter_price') }} &amp; Estat</h3>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] text-gray-500 uppercase font-bold block mb-1">{{ $t('sales.price_min') }} (€)</label>
                                <input v-model="filters.priceMin" type="number" placeholder="0" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon focus:ring-0">
                            </div>
                            <div>
                                <label class="text-[10px] text-gray-500 uppercase font-bold block mb-1">{{ $t('sales.price_max') }} (€)</label>
                                <input v-model="filters.priceMax" type="number" :placeholder="$t('common.no')" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon focus:ring-0">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] text-gray-500 uppercase font-bold block mb-1">{{ $t('sales.year_min') }}</label>
                                <input v-model="filters.yearMin" type="number" placeholder="Ex: 2010" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon focus:ring-0">
                            </div>
                            <div>
                                <label class="text-[10px] text-gray-500 uppercase font-bold block mb-1">{{ $t('sales.km_max') }}</label>
                                <input v-model="filters.kmMax" type="number" placeholder="Ex: 50000" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon focus:ring-0">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 bg-brand-surface/50 p-4 rounded-xl border border-brand-dark/50">
                        <h3 class="text-xs font-black text-brand-neon uppercase tracking-widest mb-3 border-b border-brand-dark pb-2">Especificacions</h3>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] text-gray-500 uppercase font-bold block mb-1">Carnet</label>
                                <select v-model="filters.license" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon focus:ring-0">
                                    <option value="all">Tots</option>
                                    <option value="AM">AM</option><option value="A1">A1</option>
                                    <option value="A2">A2</option><option value="A">A</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] text-gray-500 uppercase font-bold block mb-1">Tipus</label>
                                <select v-model="filters.type" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon focus:ring-0">
                                    <option value="all">Tots</option>
                                    <option value="Naked">Naked</option><option value="Sport">Sport / R</option>
                                    <option value="Trail">Trail / Adventure</option><option value="Custom">Custom / Cruiser</option>
                                    <option value="Scooter">Scooter / Maxi</option><option value="Touring">Touring</option>
                                    <option value="Off-Road">Off-Road / Enduro</option><option value="Classic">{{ $t('sales.classic') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex items-center gap-2">
                                <div class="flex-1">
                                    <label class="text-[10px] text-gray-500 uppercase font-bold block mb-1">Min CC</label>
                                    <input v-model="filters.ccMin" type="number" placeholder="125" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon focus:ring-0">
                                </div>
                                <div class="flex-1">
                                    <label class="text-[10px] text-gray-500 uppercase font-bold block mb-1">Max CC</label>
                                    <input v-model="filters.ccMax" type="number" placeholder="1000" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon focus:ring-0">
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1">
                                    <label class="text-[10px] text-gray-500 uppercase font-bold block mb-1">Min CV</label>
                                    <input v-model="filters.cvMin" type="number" placeholder="15" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon focus:ring-0">
                                </div>
                                <div class="flex-1">
                                    <label class="text-[10px] text-gray-500 uppercase font-bold block mb-1">Max CV</label>
                                    <input v-model="filters.cvMax" type="number" placeholder="200" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon focus:ring-0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-brand-dark pt-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="w-full sm:w-auto flex items-center gap-2">
                        <label class="text-xs text-gray-400 uppercase font-bold">{{ $t('sales.sort_by') }}</label>
                        <select v-model="filters.sortBy" class="bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon focus:ring-0">
                            <option value="created_at">{{ $t('sales.sort_date') }}</option>
                            <option value="price">{{ $t('sales.sort_price') }}</option>
                            <option value="year">{{ $t('sales.sort_year') }}</option>
                            <option value="current_km">{{ $t('sales.sort_km') }}</option>
                        </select>
                        <button @click="toggleSortDir" class="bg-brand-surface border border-brand-dark px-3 py-2 rounded-lg text-white hover:border-brand-neon transition text-xs font-bold">
                            {{ filters.sortDir === 'asc' ? $t('common.asc') : $t('common.desc') }}
                        </button>
                    </div>
                    
                    <button @click="resetFilters" class="w-full sm:w-auto text-xs font-bold text-red-400 hover:text-red-300 uppercase tracking-widest px-4 py-2 border border-red-900/50 bg-red-900/10 rounded-lg transition">
                        {{ $t('sales.clear_filters') }}
                    </button>
                </div>
            </div>

            <div v-if="filteredSales.length === 0" class="flex flex-col items-center justify-center py-16 text-center opacity-80 bg-brand-surface rounded-xl border border-brand-dark border-dashed">
                <div class="p-4 bg-brand-black rounded-full mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-500"><path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672 13.684 16.6m0 0-2.51 2.225.569-9.47 5.227 7.917-3.286-.672ZM12 2.25V4.5m5.834.166-1.591 1.591M20.25 10.5H18M7.757 14.743l-1.59 1.59M6 10.5H3.75m4.007-4.243-1.59-1.59" /></svg>
                </div>
                <p class="text-white font-black uppercase tracking-widest text-lg">{{ $t('sales.no_saved_motos') }}</p>
                <p class="text-gray-400 text-sm mt-1">{{ $t('sales.no_saved_motos_subtitle') }}</p>
                <button @click="resetFilters" class="bg-brand-neon text-black px-6 py-2 rounded-lg text-sm font-black uppercase tracking-widest mt-4 hover:bg-white transition">{{ $t('common.no_results') }}</button>
            </div>

            <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="sale in filteredSales" :key="sale.id" class="bg-brand-surface rounded-xl overflow-hidden border border-brand-dark shadow-lg hover:border-brand-neon transition duration-300 flex flex-col group animate-fade-in relative">
                    
                    <div v-if="sale.state === 'venuda'" class="absolute inset-0 bg-black/60 backdrop-blur-sm z-30 flex items-center justify-center">
                        <span class="bg-red-600 text-white px-6 py-2 font-black uppercase tracking-widest text-xl transform -rotate-12 border-2 border-red-900 shadow-2xl">Venuda</span>
                    </div>
                    <div v-else-if="sale.state === 'actiu (reservat) (nou)'" class="absolute inset-0 bg-black/40 backdrop-blur-sm z-30 flex items-center justify-center">
                        <span class="bg-yellow-500 text-black px-6 py-2 font-black uppercase tracking-widest text-xl transform -rotate-12 border-2 border-yellow-700 shadow-2xl">Reservada</span>
                    </div>

                    <div class="h-40 bg-gray-900 relative w-full overflow-hidden flex items-center justify-center">
                        <!-- Foto real si n'hi ha -->
                        <img
                            v-if="sale.images && sale.images.length > 0"
                            :src="$page.props.storageUrl + '/' + sale.images[0].image_path"
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                            alt="Foto"
                        >
                        <!-- Placeholder si no hi ha fotos -->
                        <template v-else>
                            <div class="absolute inset-0 opacity-40 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
                            <h2 class="relative z-20 text-3xl font-black text-white/50 uppercase tracking-widest text-center px-4">
                                {{ sale.motorcycle?.brand }}
                            </h2>
                        </template>
                        <!-- Botó de Favorit (Cor) a dalt a l'esquerra -->
                        <Link 
                            v-if="!$page.props.auth.user || sale.motorcycle?.user_id !== $page.props.auth.user.id"
                            :href="route('sales.toggle-favorite', sale.id)" 
                            method="post" 
                            as="button" 
                            preserve-scroll
                            class="absolute top-2 left-2 z-40 p-2 rounded-full backdrop-blur-md shadow-lg transition-transform hover:scale-110 flex items-center justify-center"
                            :class="sale.is_favorited ? 'bg-white/20' : 'bg-black/40 hover:bg-white/20'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" :fill="sale.is_favorited ? '#ef4444' : 'none'" viewBox="0 0 24 24" stroke-width="2" :stroke="sale.is_favorited ? '#ef4444' : 'white'" class="w-5 h-5 transition-colors">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                        </Link>
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-surface via-transparent to-transparent z-10"></div>

                        <div class="absolute top-2 right-2 px-3 py-1.5 rounded text-sm font-black tracking-wide z-20 bg-brand-neon text-black shadow-lg">
                            {{ parseFloat(sale.price).toLocaleString('ca-ES') }} €
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between relative z-20">
                        <div>
                            <h3 class="text-lg font-bold text-white mb-1 truncate">{{ sale.title }}</h3>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-1">📍 {{ sale.location }}</p>
                            
                            <div class="flex flex-wrap gap-2 text-[10px] text-brand-neon uppercase font-bold tracking-widest mb-3">
                                <span v-if="sale.motorcycle?.type" class="bg-brand-neon/10 border border-brand-neon/30 px-2 py-0.5 rounded">{{ sale.motorcycle.type }}</span>
                                <span v-if="sale.motorcycle?.license_type" class="bg-brand-neon/10 border border-brand-neon/30 px-2 py-0.5 rounded">Carnet {{ sale.motorcycle.license_type }}</span>
                                <span class="bg-gray-800 text-gray-300 border border-gray-700 px-2 py-0.5 rounded flex items-center gap-1">👁️ {{ sale.views_count || 0 }}</span>
                                <span v-if="sale.favorited_by_count > 0" class="bg-red-900/20 text-red-400 border border-red-500/30 px-2 py-0.5 rounded flex items-center gap-1">❤️ {{ sale.favorited_by_count }}</span>
                            </div>

                            <div class="grid grid-cols-4 gap-1 text-xs text-gray-300 font-mono bg-brand-black/50 p-2 rounded-lg mb-3">
                                <div class="flex flex-col items-center text-center p-1 border-r border-gray-700">
                                    <span class="text-[9px] text-gray-500 uppercase">Any</span>
                                    <span class="font-bold">{{ sale.motorcycle?.year || '-' }}</span>
                                </div>
                                <div class="flex flex-col items-center text-center p-1 border-r border-gray-700">
                                    <span class="text-[9px] text-gray-500 uppercase">CC</span>
                                    <span class="font-bold">{{ sale.motorcycle?.cc || '-' }}</span>
                                </div>
                                <div class="flex flex-col items-center text-center p-1 border-r border-gray-700">
                                    <span class="text-[9px] text-gray-500 uppercase">CV</span>
                                    <span class="font-bold">{{ sale.motorcycle?.power_cv || '-' }}</span>
                                </div>
                                <div class="flex flex-col items-center text-center p-1">
                                    <span class="text-[9px] text-gray-500 uppercase">KM</span>
                                    <span class="font-bold text-brand-neon">{{ parseFloat(sale.motorcycle?.current_km || 0).toLocaleString('ca-ES') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2 pt-4 border-t border-brand-dark/50">
                            <Link :href="route('sales.show', sale.id)" class="block w-full text-center bg-brand-dark hover:bg-white hover:text-black text-white text-xs font-bold uppercase py-2.5 rounded transition">
                                Veure Detall
                            </Link>
                        </div>
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

const props = defineProps({ sales: Array });

const showFilters = ref(false);

const filters = ref({
    search: '',
    priceMin: '',
    priceMax: '',
    yearMin: '',
    kmMax: '',
    ccMin: '',
    ccMax: '',
    cvMin: '',
    cvMax: '',
    license: 'all',
    type: 'all',
    sortBy: 'created_at',
    sortDir: 'desc'
});

const activeFiltersCount = computed(() => {
    let count = 0;
    if (filters.value.search) count++;
    if (filters.value.priceMin !== '' || filters.value.priceMax !== '') count++;
    if (filters.value.yearMin !== '' || filters.value.kmMax !== '') count++;
    if (filters.value.ccMin !== '' || filters.value.ccMax !== '') count++;
    if (filters.value.cvMin !== '' || filters.value.cvMax !== '') count++;
    if (filters.value.license !== 'all') count++;
    if (filters.value.type !== 'all') count++;
    return count;
});

const toggleSortDir = () => {
    filters.value.sortDir = filters.value.sortDir === 'asc' ? 'desc' : 'asc';
};

const resetFilters = () => {
    filters.value = {
        search: '',
        priceMin: '', priceMax: '',
        yearMin: '', kmMax: '',
        ccMin: '', ccMax: '',
        cvMin: '', cvMax: '',
        license: 'all', type: 'all',
        sortBy: 'created_at', sortDir: 'desc'
    };
};

const filteredSales = computed(() => {
    let result = [...props.sales];
    
    // Filtre text
    if (filters.value.search) {
        const q = filters.value.search.toLowerCase();
        result = result.filter(s => 
            s.title.toLowerCase().includes(q) || 
            s.location.toLowerCase().includes(q) ||
            (s.motorcycle && s.motorcycle.brand.toLowerCase().includes(q)) ||
            (s.motorcycle && s.motorcycle.model.toLowerCase().includes(q))
        );
    }

    // Selects
    if (filters.value.license !== 'all') {
        result = result.filter(s => s.motorcycle && s.motorcycle.license_type === filters.value.license);
    }
    if (filters.value.type !== 'all') {
        result = result.filter(s => s.motorcycle && s.motorcycle.type === filters.value.type);
    }

    // Preu
    if (filters.value.priceMin !== '') result = result.filter(s => parseFloat(s.price) >= filters.value.priceMin);
    if (filters.value.priceMax !== '') result = result.filter(s => parseFloat(s.price) <= filters.value.priceMax);

    // Any i KM
    if (filters.value.yearMin !== '') result = result.filter(s => s.motorcycle && s.motorcycle.year >= filters.value.yearMin);
    if (filters.value.kmMax !== '') result = result.filter(s => s.motorcycle && s.motorcycle.current_km <= filters.value.kmMax);

    // CC
    if (filters.value.ccMin !== '') result = result.filter(s => s.motorcycle && s.motorcycle.cc >= filters.value.ccMin);
    if (filters.value.ccMax !== '') result = result.filter(s => s.motorcycle && s.motorcycle.cc <= filters.value.ccMax);

    // CV
    if (filters.value.cvMin !== '') result = result.filter(s => s.motorcycle && s.motorcycle.power_cv >= filters.value.cvMin);
    if (filters.value.cvMax !== '') result = result.filter(s => s.motorcycle && s.motorcycle.power_cv <= filters.value.cvMax);

    // Ordenació
    return result.sort((a, b) => {
        let fieldA, fieldB;

        // Si ordenem per dades de la moto (KM o Any)
        if (filters.value.sortBy === 'year' || filters.value.sortBy === 'current_km') {
            fieldA = a.motorcycle ? parseFloat(a.motorcycle[filters.value.sortBy] || 0) : 0;
            fieldB = b.motorcycle ? parseFloat(b.motorcycle[filters.value.sortBy] || 0) : 0;
        } else {
            fieldA = a[filters.value.sortBy];
            fieldB = b[filters.value.sortBy];
            if (filters.value.sortBy === 'price') {
                fieldA = parseFloat(fieldA);
                fieldB = parseFloat(fieldB);
            }
        }

        if (fieldA < fieldB) return filters.value.sortDir === 'asc' ? -1 : 1;
        if (fieldA > fieldB) return filters.value.sortDir === 'asc' ? 1 : -1;
        return 0;
    });
});
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
</style>

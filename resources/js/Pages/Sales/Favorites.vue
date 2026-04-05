<template>
    <AppLayout :title="$t('sales.favorites')">
        <div class="px-4 py-6 pb-24 max-w-7xl mx-auto">
            
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <button @click="() => window.history.back()" class="w-10 h-10 rounded-full bg-brand-neon flex items-center justify-center text-black hover:bg-white transition flex-shrink-0 shadow-[0_0_15px_rgba(12,225,181,0.3)]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                    </button>
                    <h1 class="text-2xl font-black uppercase tracking-tighter text-white leading-none">FAVORITS</h1>
                </div>
            </div>



            <div v-if="sales.length === 0" class="flex flex-col items-center justify-center py-16 text-center opacity-80 bg-brand-surface rounded-xl border border-brand-dark border-dashed">
                <div class="p-4 bg-brand-black rounded-full mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-500"><path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672 13.684 16.6m0 0-2.51 2.225.569-9.47 5.227 7.917-3.286-.672ZM12 2.25V4.5m5.834.166-1.591 1.591M20.25 10.5H18M7.757 14.743l-1.59 1.59M6 10.5H3.75m4.007-4.243-1.59-1.59" /></svg>
                </div>
                <p class="text-white font-black uppercase tracking-widest text-lg">No tens motos a favorits</p>
            </div>

            <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="sale in sales" :key="sale.id" class="bg-brand-surface rounded-xl overflow-hidden border border-brand-dark shadow-lg hover:border-brand-neon transition duration-300 flex flex-col group animate-fade-in relative">
                    
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

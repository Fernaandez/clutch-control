<template>
    <AppLayout :title="$t('sales.my_listings_title')">
        <div class="px-4 py-6 pb-24">
            
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <Link :href="route('sales.index')" class="w-10 h-10 rounded-full bg-brand-neon flex items-center justify-center text-black hover:bg-white transition flex-shrink-0 shadow-[0_0_15px_rgba(12,225,181,0.3)]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                    </Link>
                    <h1 class="text-2xl font-black uppercase tracking-tighter text-white leading-none">ELS MEUS ANUNCIS</h1>
                </div>
                <Link :href="route('sales.create')" class="bg-brand-neon text-brand-black p-3 rounded-full shadow-[0_0_15px_rgba(12,225,181,0.4)] hover:scale-110 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                </Link>
            </div>



            <div v-if="sales.length === 0" class="flex flex-col items-center justify-center py-12 text-center opacity-60 bg-brand-surface rounded-xl border border-brand-dark border-dashed">
                <p class="text-gray-400 font-medium">{{ $t('sales.no_listings') }}</p>
                <Link :href="route('sales.create')" class="text-brand-neon text-sm font-bold mt-2 hover:underline">{{ $t('sales.create_listing') }}</Link>
            </div>

            <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="sale in sales" :key="sale.id" class="bg-brand-surface rounded-xl overflow-hidden border border-brand-dark shadow-lg group hover:border-brand-neon transition duration-300 flex flex-col animate-fade-in" :class="sale.state === 'venuda' ? 'opacity-70 grayscale-[30%]' : (sale.state === 'actiu (reservat) (nou)' ? 'ring-1 ring-yellow-500/50' : '')">
                    
                    <div class="h-40 bg-gray-900 relative w-full overflow-hidden flex items-center justify-center">
                        <img v-if="sale.images && sale.images.length > 0" :src="$page.props.storageUrl + '/' + sale.images[0].image_path" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" alt="Foto">
                        <template v-else>
                            <div class="absolute inset-0 opacity-40 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
                            <h2 class="relative z-20 text-3xl font-black text-white/50 uppercase tracking-widest text-center px-4">{{ sale.motorcycle?.brand }}</h2>
                        </template>
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-surface via-transparent to-transparent z-10"></div>

                        <!-- Pill d'estat a dalt esquerra -->
                        <div v-if="sale.state === 'venuda'" class="absolute top-2 left-2 bg-red-600/90 border border-red-500 text-white px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide z-[400] shadow-md">{{ $t('sales.sold') }}</div>
                        <div v-else-if="sale.state === 'actiu (reservat) (nou)'" class="absolute top-2 left-2 bg-yellow-500/90 border border-yellow-400 text-black px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide z-[400] shadow-md">Reservada</div>
                        <div v-else class="absolute top-2 left-2 bg-brand-neon/90 border border-brand-neon text-black px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide z-[400] shadow-md">{{ $t('sales.active') }}</div>

                        <!-- Vistes & Cors (badges petits on normalment aniria la dificultat) a dalt dreta -->
                        <div class="absolute top-2 right-2 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide z-[400] bg-brand-black/80 text-white shadow-md border border-brand-dark flex gap-2 backdrop-blur-sm">
                            <span class="text-brand-neon">👁️ {{ sale.views_count || 0 }}</span>
                            <span v-if="sale.favorited_by_count > 0" class="text-red-400">❤️ {{ sale.favorited_by_count }}</span>
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between relative z-20">
                        <div>
                            <h3 class="text-lg font-bold text-white mb-1 truncate">{{ sale.title }}</h3>
                            <p class="text-xs text-gray-400 mb-3 truncate uppercase tracking-widest">{{ sale.motorcycle?.brand }} {{ sale.motorcycle?.model }}</p>
                            
                            <div class="flex items-center gap-4 text-xs text-brand-neon font-black font-mono tracking-wider bg-brand-black/30 p-2 rounded-lg">
                                <span>{{ parseFloat(sale.price).toLocaleString('ca-ES') }} €</span>
                                <span class="text-[9px] text-gray-500 font-sans tracking-tight uppercase ml-auto">{{ new Date(sale.created_at).toLocaleDateString('ca-ES') }}</span>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4 pt-4 border-t border-brand-dark/50">
                            <Link :href="route('sales.show', sale.id)" class="flex-1 text-center bg-brand-dark hover:bg-white hover:text-black text-white text-xs font-bold uppercase py-2 rounded transition">
                                {{ $t('sales.view_detail') }}
                            </Link>

                            <Link :href="route('sales.edit', sale.id)" class="px-3 flex items-center justify-center bg-brand-dark border border-gray-600 hover:border-brand-neon hover:text-brand-neon text-gray-400 rounded transition" :title="$t('common.edit')">
                                ✏️
                            </Link>

                            <Link 
                                v-if="sale.state !== 'venuda'"
                                :href="route('sales.mark-sold', sale.id)"
                                method="patch" as="button"
                                class="px-3 flex items-center justify-center bg-brand-dark border border-red-900/50 text-red-700 hover:bg-red-500 hover:text-white hover:border-red-500 rounded transition" 
                                :title="$t('sales.mark_sold')"
                            >
                                ✓
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
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const { t } = useI18n();

const props = defineProps({ sales: Array });

const showFilters = ref(false);

const filters = ref({
    search: '',
    state: 'all',
    sortBy: 'created_at',
    sortDir: 'desc'
});

const activeFiltersCount = computed(() => {
    let count = 0;
    if (filters.value.search) count++;
    if (filters.value.state !== 'all') count++;
    return count;
});

const toggleSortDir = () => {
    filters.value.sortDir = filters.value.sortDir === 'asc' ? 'desc' : 'asc';
};

const resetFilters = () => {
    filters.value = {
        search: '',
        state: 'all',
        sortBy: 'created_at',
        sortDir: 'desc'
    };
};

const filteredSales = computed(() => {
    let result = [...props.sales];
    
    // Filtre text
    if (filters.value.search) {
        const q = filters.value.search.toLowerCase();
        result = result.filter(s => 
            s.title.toLowerCase().includes(q) || 
            (s.motorcycle && s.motorcycle.brand.toLowerCase().includes(q)) ||
            (s.motorcycle && s.motorcycle.model.toLowerCase().includes(q))
        );
    }
    
    // Filtre estat
    if (filters.value.state !== 'all') {
        result = result.filter(s => s.state === filters.value.state);
    }

    // Ordenació
    return result.sort((a, b) => {
        let fieldA = a[filters.value.sortBy];
        let fieldB = b[filters.value.sortBy];

        if (filters.value.sortBy === 'price' || filters.value.sortBy === 'views_count' || filters.value.sortBy === 'favorited_by_count') {
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

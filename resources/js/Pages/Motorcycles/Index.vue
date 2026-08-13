<template>
    <AppLayout>
        <div class="max-w-xl mx-auto px-6 py-6 pb-24 cc-fade-in">

            <header class="flex items-center gap-3 mb-6">
                <h1 class="cc-title flex-1 truncate">{{ $t('motorcycles.my_garage') }}</h1>
                <Link
                    :href="route('motorcycles.create')"
                    class="cc-icon-btn"
                    :aria-label="$t('motorcycles.new_moto')"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </Link>
            </header>

            <div v-if="motos.length === 0" class="py-8">
                <div class="flex flex-col items-center justify-center text-center py-16 px-6">
                    <p class="text-base font-medium text-gray-300">{{ $t('motorcycles.empty_garage') }}</p>
                    <div class="mt-6">
                        <Link :href="route('motorcycles.create')" class="cc-btn-primary">
                            {{ $t('motorcycles.new_moto') }}
                        </Link>
                    </div>
                </div>
            </div>

            <template v-else>
                <!-- Filtres només amb moltes motos (perfil tipus concessionari) -->
                <template v-if="motos.length > 6">
                    <button
                        type="button"
                        @click="showFilters = !showFilters"
                        class="cc-btn-text"
                    >
                        {{ showFilters ? $t('motorcycles.hide_filters') : $t('motorcycles.show_filters') }}
                        <span v-if="activeFiltersCount > 0" class="ml-1 tabular-nums">({{ activeFiltersCount }})</span>
                    </button>

                    <div v-if="showFilters" class="mt-6 space-y-4">
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('motorcycles.search') }}</label>
                                <input v-model="filters.search" type="text" :placeholder="$t('motorcycles.search_placeholder')" class="w-full text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('motorcycles.style') }}</label>
                                <select v-model="filters.type" class="w-full text-sm">
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
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('motorcycles.year_min') }}</label>
                                <input v-model="filters.yearMin" type="number" placeholder="2010" class="w-full text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('motorcycles.year_max') }}</label>
                                <input v-model="filters.yearMax" type="number" placeholder="2024" class="w-full text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('motorcycles.km_max') }}</label>
                                <input v-model="filters.kmMax" type="number" placeholder="50000" class="w-full text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('motorcycles.sort_by') }}</label>
                            <div class="flex gap-2">
                                <select v-model="filters.sortBy" class="flex-1 text-sm">
                                    <option value="brand">{{ $t('motorcycles.sort_brand') }}</option>
                                    <option value="year">{{ $t('motorcycles.sort_year') }}</option>
                                    <option value="current_km">{{ $t('motorcycles.sort_km') }}</option>
                                    <option value="created_at">{{ $t('motorcycles.sort_date') }}</option>
                                </select>
                                <button type="button" @click="toggleSortDir" class="cc-btn-ghost px-3 text-sm">
                                    {{ filters.sortDir === 'asc' ? $t('motorcycles.asc') : $t('motorcycles.desc') }}
                                </button>
                            </div>
                        </div>

                        <button type="button" @click="resetFilters" class="cc-btn-text">
                            {{ $t('motorcycles.clear_all_filters') }}
                        </button>
                    </div>
                </template>

                <div v-if="filteredMotos.length === 0" class="mt-12 text-center">
                    <p class="text-sm text-gray-500">{{ $t('motorcycles.no_results') }}</p>
                    <button v-if="motos.length > 6" type="button" @click="resetFilters" class="cc-btn-text mt-3">
                        {{ $t('motorcycles.clear_filters') }}
                    </button>
                </div>

                <div v-else class="mt-2 divide-y divide-white/[0.06]">
                    <div
                        v-for="moto in filteredMotos"
                        :key="moto.id"
                        class="flex items-center gap-4 py-5"
                    >
                        <Link :href="route('dashboard', moto.id)" class="min-w-0 flex-1">
                            <p class="text-lg font-medium text-gray-200 truncate">
                                {{ moto.brand }} {{ moto.model }}
                            </p>
                            <div class="mt-2 flex items-baseline gap-1.5">
                                <span class="text-[28px] font-light tracking-tight tabular-nums text-white leading-none">
                                    {{ moto.current_km }}
                                </span>
                                <span class="text-sm text-gray-500">km</span>
                            </div>
                            <p v-if="moto.itv_status && moto.itv_status !== 'ok'" class="mt-2 text-sm text-red-400">
                                {{ $t('motorcycles.itv_badge') }} · {{ $t(`motorcycles.status_${moto.itv_status}`) }}
                            </p>
                        </Link>
                        <Link :href="route('motorcycles.edit', moto.id)" class="cc-btn-text flex-shrink-0">
                            {{ $t('common.edit') }}
                        </Link>
                    </div>
                </div>
            </template>

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

const statusBadgeClass = (status) => {
    if (status === 'expired') return 'bg-red-500/10 text-red-400 border-red-500/30';
    if (status === 'expiring_soon') return 'bg-white/[0.06] text-white border-white/20';
    return 'bg-white/[0.04] text-gray-400 border-white/[0.08]';
};

const filteredMotos = computed(() => {
    let result = [...props.motos];
    
    if (filters.value.search) {
        const q = filters.value.search.toLowerCase();
        result = result.filter(m => 
            m.brand.toLowerCase().includes(q) || 
            m.model.toLowerCase().includes(q)
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

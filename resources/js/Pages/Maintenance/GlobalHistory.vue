<template>
    <AppLayout :current-moto-id="motorcycle.id">
        <div class="max-w-xl mx-auto px-6 py-6 pb-24 cc-fade-in">

            <header class="flex items-center gap-3 mb-6">
                <button type="button" @click="goBack" class="cc-icon-btn" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </button>
                <h1 class="cc-title flex-1 truncate">{{ $t('maintenance.history') }}</h1>
            </header>

            <p v-if="motorcycle.brand" class="text-sm text-gray-500 mb-8 truncate">{{ motorcycle.brand }} {{ motorcycle.model }}</p>

            <div v-if="filteredHistory.length" class="flex gap-12 mb-12">
                <div>
                    <p class="text-[40px] font-light tracking-tight tabular-nums text-white leading-none">{{ filteredHistory.length }}</p>
                    <p class="mt-2 text-sm text-gray-500">{{ $t('maintenance.total_records') }}</p>
                </div>
                <div>
                    <p class="text-[40px] font-light tracking-tight tabular-nums text-white leading-none">
                        {{ totalFilteredCost.toFixed(2) }}<span class="text-base text-gray-500 ml-1">€</span>
                    </p>
                    <p class="mt-2 text-sm text-gray-500">{{ $t('maintenance.total_filtered') }}</p>
                </div>
            </div>

            <div class="mb-8">
                <button
                    type="button"
                    @click="showFilters = !showFilters"
                    class="cc-btn-text"
                >
                    {{ showFilters ? $t('maintenance.hide_filters') : $t('maintenance.show_filters') }}
                    <span v-if="activeFiltersCount > 0" class="ml-1 text-gray-400">({{ activeFiltersCount }})</span>
                </button>

                <div v-if="showFilters" class="mt-6 space-y-5">
                    <div>
                        <label class="cc-section-label">{{ $t('maintenance.search') }}</label>
                        <input v-model="filters.search" type="text" :placeholder="$t('maintenance.search_placeholder')" class="w-full mt-2 text-sm">
                    </div>
                    <div>
                        <label class="cc-section-label">{{ $t('maintenance.type') }}</label>
                        <select v-model="filters.type" class="w-full mt-2 text-sm">
                            <option value="all">{{ $t('maintenance.type_all') }}</option>
                            <option value="maintenance">{{ $t('maintenance.type_maintenance') }}</option>
                            <option value="repair">{{ $t('maintenance.type_repair') }}</option>
                            <option value="upgrade">{{ $t('maintenance.type_upgrade') }}</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="cc-section-label">{{ $t('maintenance.date_start') }}</label>
                            <input v-model="filters.dateStart" type="date" class="w-full mt-2 text-sm">
                        </div>
                        <div>
                            <label class="cc-section-label">{{ $t('maintenance.date_end') }}</label>
                            <input v-model="filters.dateEnd" type="date" class="w-full mt-2 text-sm">
                        </div>
                        <div>
                            <label class="cc-section-label">{{ $t('maintenance.km_min') }}</label>
                            <input v-model="filters.kmMin" type="number" placeholder="0" class="w-full mt-2 text-sm">
                        </div>
                        <div>
                            <label class="cc-section-label">{{ $t('maintenance.km_max') }}</label>
                            <input v-model="filters.kmMax" type="number" placeholder="Max" class="w-full mt-2 text-sm">
                        </div>
                        <div>
                            <label class="cc-section-label">{{ $t('maintenance.price_min') }}</label>
                            <input v-model="filters.priceMin" type="number" placeholder="0" class="w-full mt-2 text-sm">
                        </div>
                        <div>
                            <label class="cc-section-label">{{ $t('maintenance.price_max') }}</label>
                            <input v-model="filters.priceMax" type="number" placeholder="Max" class="w-full mt-2 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="cc-section-label">{{ $t('maintenance.sort_by') }}</label>
                        <div class="flex gap-2 mt-2">
                            <select v-model="filters.sortBy" class="flex-1 text-sm">
                                <option value="date">{{ $t('maintenance.sort_date') }}</option>
                                <option value="cost">{{ $t('maintenance.sort_price') }}</option>
                                <option value="km_at_moment">{{ $t('maintenance.sort_km') }}</option>
                            </select>
                            <button type="button" @click="toggleSortDir" class="cc-btn-ghost text-sm px-3">
                                {{ filters.sortDir === 'desc' ? $t('common.desc') : $t('common.asc') }}
                            </button>
                        </div>
                    </div>
                    <button type="button" @click="resetFilters" class="cc-btn-text">
                        {{ $t('maintenance.clear_all_filters') }}
                    </button>
                </div>
            </div>

            <div v-if="filteredHistory.length" class="divide-y divide-white/[0.06]">
                <div v-for="log in filteredHistory" :key="log.id" class="py-5">
                    <button type="button" @click="openShowModal(log)" class="w-full text-left">
                        <p class="text-[15px] font-medium text-gray-100">{{ log.task_title }}</p>
                        <p class="mt-1 text-sm text-gray-500">
                            <span v-if="log.type !== 'maintenance'" class="text-gray-400">{{ typeLabel(log.type) }} · </span>
                            {{ formatDate(log.date) }} · {{ log.km_at_moment }} km · {{ log.cost }} €
                        </p>
                    </button>
                </div>
            </div>

            <div v-else class="flex flex-col items-center justify-center text-center py-16 px-6">
                <p class="text-base font-semibold text-gray-300">{{ $t('maintenance.no_results') }}</p>
                <button type="button" @click="resetFilters" class="cc-btn-text mt-4">
                    {{ $t('maintenance.clear_filters') }}
                </button>
            </div>
        </div>

        <div v-if="selectedLog" class="fixed inset-0 z-[4000] flex items-center justify-center p-4">
            <div @click="selectedLog = null" class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
            <div class="relative bg-brand-surface border border-white/[0.08] rounded-2xl p-6 max-w-sm w-full max-h-[min(90vh,36rem)] overflow-y-auto overscroll-contain">
                <button type="button" @click="selectedLog = null" class="cc-icon-btn absolute top-4 right-4" aria-label="Tancar">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
                <h3 class="text-lg font-medium text-white mb-1 pr-10">{{ selectedLog.task_title }}</h3>
                <p class="text-sm text-gray-500 mb-6">{{ typeLabel(selectedLog.type) }}</p>
                <div class="space-y-4">
                    <div>
                        <p class="cc-section-label">{{ $t('common.date') }}</p>
                        <p class="text-white tabular-nums mt-1">{{ formatDate(selectedLog.date) }}</p>
                    </div>
                    <div>
                        <p class="cc-section-label">{{ $t('common.price') }}</p>
                        <p class="text-white tabular-nums mt-1">{{ selectedLog.cost }} €</p>
                    </div>
                    <div>
                        <p class="cc-section-label">{{ $t('maintenance.done_at') }}</p>
                        <p class="text-white tabular-nums mt-1">{{ selectedLog.km_at_moment }} km</p>
                    </div>
                    <div v-if="selectedLog.location">
                        <p class="cc-section-label">{{ $t('maintenance.workshop') }}</p>
                        <p class="text-gray-300 text-sm mt-1">{{ selectedLog.location }}</p>
                    </div>
                    <button v-if="selectedLog.invoice_photo" type="button" @click="openPhoto(selectedLog.invoice_photo)" class="block w-full text-left">
                        <p class="cc-section-label">{{ $t('maintenance.invoice_photo') }}</p>
                        <img
                            :src="$page.props.storageUrl + '/' + selectedLog.invoice_photo"
                            alt=""
                            class="mt-2 max-h-56 w-full object-contain rounded-lg"
                            @error="($event.target).style.display = 'none'"
                        >
                    </button>
                </div>
            </div>
        </div>

        <div v-if="selectedPhoto" class="fixed inset-0 z-[4010] flex items-center justify-center p-4">
            <div @click="selectedPhoto = null" class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
            <button type="button" @click="selectedPhoto = null" class="cc-icon-btn absolute top-4 right-4 z-10" aria-label="Tancar">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
            <img
                :src="$page.props.storageUrl + '/' + selectedPhoto"
                alt=""
                class="relative max-h-[85vh] max-w-full object-contain rounded-lg"
                @error="($event.target).style.display = 'none'"
            >
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import { smartBack } from '@/Composables/navigationStack.js';

const { locale, t } = useI18n();

const props = defineProps({
    motorcycle: Object,
    history: Array,
});

const goBack = () => smartBack(route('dashboard', props.motorcycle.id));

const showFilters = ref(false);
const selectedLog = ref(null);
const selectedPhoto = ref(null);

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
            (log.location && String(log.location).toLowerCase().includes(q))
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

const openShowModal = (log) => {
    selectedLog.value = log;
};

const openPhoto = (photo) => {
    selectedPhoto.value = photo;
};

const typeLabel = (type) => {
    if (type === 'maintenance') return t('maintenance.type_maintenance');
    if (type === 'repair') return t('maintenance.type_repair');
    if (type === 'upgrade') return t('maintenance.type_upgrade');
    return type;
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

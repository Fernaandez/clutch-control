<template>
    <AppLayout :title="$t('sales.marketplace_title')">
        <div class="px-6 pt-10 pb-28 max-w-xl mx-auto cc-fade-in">

            <p class="text-sm text-gray-500">{{ $t('sales.marketplace_title') }}</p>
            <p class="mt-3 text-[64px] leading-[0.9] font-light tracking-[-0.04em] text-white tabular-nums">
                {{ marketCount }}
            </p>
            <p class="mt-2 text-sm text-gray-500">{{ $t('sales.for_sale_label') }}</p>

            <Link :href="route('sales.create')" class="cc-btn-primary w-full py-3.5 mt-8 text-center">
                {{ $t('sales.publish_short') }}
            </Link>

            <!-- Segments -->
            <div class="mt-10 flex items-center gap-5 border-b border-white/[0.06]">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    type="button"
                    @click="activeTab = tab.id"
                    class="relative -mb-px pb-3 text-[13px] font-medium transition-colors whitespace-nowrap"
                    :class="activeTab === tab.id ? 'text-white' : 'text-gray-500 hover:text-gray-300'"
                >
                    {{ tab.label }}
                    <span v-if="tab.count" class="ml-1 text-gray-600 tabular-nums">{{ tab.count }}</span>
                    <span v-if="activeTab === tab.id" class="absolute inset-x-0 -bottom-px h-px bg-white"></span>
                </button>
            </div>

            <div v-if="activeTab === 'market'" class="mt-4 flex items-center gap-2">
                <input
                    v-if="baseList.length > 4 || search"
                    v-model="search"
                    type="search"
                    :placeholder="$t('sales.search_placeholder')"
                    class="flex-1 rounded-xl bg-white/[0.04] border-white/[0.08] text-white text-sm focus:border-white/30 focus:ring-0"
                >
                <button type="button" @click="showFilters = !showFilters" class="cc-btn-text flex-shrink-0">
                    {{ showFilters ? $t('common.hide_filters') : $t('common.filter') }}
                    <span v-if="activeFiltersCount" class="ml-1 tabular-nums text-gray-400">{{ activeFiltersCount }}</span>
                </button>
            </div>

            <div v-if="activeTab === 'market' && showFilters" class="mt-4 space-y-3 pb-2">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ $t('sales.price_min') }}</label>
                        <input v-model="filters.priceMin" type="number" placeholder="0" class="w-full rounded-xl bg-white/[0.04] border-white/[0.08] text-white text-sm focus:border-white/30 focus:ring-0">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ $t('sales.price_max') }}</label>
                        <input v-model="filters.priceMax" type="number" :placeholder="$t('common.no')" class="w-full rounded-xl bg-white/[0.04] border-white/[0.08] text-white text-sm focus:border-white/30 focus:ring-0">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ $t('sales.year_min') }}</label>
                        <input v-model="filters.yearMin" type="number" placeholder="2010" class="w-full rounded-xl bg-white/[0.04] border-white/[0.08] text-white text-sm focus:border-white/30 focus:ring-0">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ $t('sales.km_max') }}</label>
                        <input v-model="filters.kmMax" type="number" placeholder="50000" class="w-full rounded-xl bg-white/[0.04] border-white/[0.08] text-white text-sm focus:border-white/30 focus:ring-0">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ $t('sales.license') }}</label>
                        <select v-model="filters.license" class="w-full rounded-xl bg-white/[0.04] border-white/[0.08] text-white text-sm focus:border-white/30 focus:ring-0">
                            <option value="all">{{ $t('sales.all') }}</option>
                            <option value="AM">AM</option>
                            <option value="A1">A1</option>
                            <option value="A2">A2</option>
                            <option value="A">A</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ $t('sales.style') }}</label>
                        <select v-model="filters.type" class="w-full rounded-xl bg-white/[0.04] border-white/[0.08] text-white text-sm focus:border-white/30 focus:ring-0">
                            <option value="all">{{ $t('sales.all') }}</option>
                            <option value="Naked">Naked</option>
                            <option value="Sport">Sport</option>
                            <option value="Trail">Trail</option>
                            <option value="Custom">Custom</option>
                            <option value="Scooter">Scooter</option>
                            <option value="Touring">Touring</option>
                            <option value="Off-Road">Off-Road</option>
                            <option value="Classic">Classic</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <select v-model="filters.sortBy" class="flex-1 rounded-xl bg-white/[0.04] border-white/[0.08] text-white text-sm focus:border-white/30 focus:ring-0">
                        <option value="created_at">{{ $t('sales.sort_date') }}</option>
                        <option value="price">{{ $t('sales.sort_price') }}</option>
                        <option value="year">{{ $t('sales.sort_year') }}</option>
                        <option value="current_km">{{ $t('sales.sort_km') }}</option>
                    </select>
                    <button type="button" @click="filters.sortDir = filters.sortDir === 'asc' ? 'desc' : 'asc'" class="cc-btn-text">
                        {{ filters.sortDir === 'asc' ? $t('common.asc') : $t('common.desc') }}
                    </button>
                    <button v-if="activeFiltersCount" type="button" @click="resetFilters" class="cc-btn-text text-red-400 border-red-500/25">
                        {{ $t('sales.clear_filters') }}
                    </button>
                </div>
            </div>

            <div v-if="filteredSales.length" class="divide-y divide-white/[0.06] mt-2">
                <div
                    v-for="sale in filteredSales"
                    :key="sale.id"
                    class="flex items-center gap-3 py-4"
                >
                    <div class="w-14 h-14 rounded-xl overflow-hidden bg-white/[0.04] border border-white/[0.06] flex-shrink-0">
                        <img
                            v-if="sale.images?.[0]"
                            :src="$page.props.storageUrl + '/' + sale.images[0].image_path"
                            alt=""
                            class="w-full h-full object-cover"
                        >
                        <div v-else class="w-full h-full flex items-center justify-center text-[10px] text-gray-600 uppercase tracking-wider px-1 text-center">
                            {{ sale.motorcycle?.brand || '—' }}
                        </div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-[15px] font-medium text-gray-100 truncate">{{ sale.title }}</p>
                        <p class="mt-1 text-xs text-gray-500 truncate">{{ metaLine(sale) }}</p>
                        <p
                            v-if="activeTab === 'mine' && sale.state !== 'actiu'"
                            class="mt-1 text-xs"
                            :class="sale.state === 'venuda' ? 'text-red-400' : 'text-gray-500'"
                        >
                            {{ stateLabel(sale.state) }}
                        </p>
                    </div>

                    <div class="text-right flex-shrink-0">
                        <p
                            class="text-[15px] tabular-nums font-medium"
                            :class="sale.state === 'venuda' ? 'text-red-400' : 'text-white'"
                        >
                            {{ formatPrice(sale.price) }}
                        </p>
                    </div>

                    <Link
                        :href="route('sales.show', sale.id) + showQuery"
                        class="cc-btn-text flex-shrink-0"
                    >
                        {{ $t('common.view') }}
                    </Link>
                </div>
            </div>

            <div v-else class="py-16 text-center">
                <p class="text-base font-semibold text-gray-300">{{ emptyTitle }}</p>
                <Link
                    v-if="activeTab === 'mine'"
                    :href="route('sales.create')"
                    class="cc-btn-text mt-4 inline-flex"
                >
                    {{ $t('sales.create_listing') }}
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    marketSales: { type: Array, default: () => [] },
    mySales: { type: Array, default: () => [] },
    favoriteSales: { type: Array, default: () => [] },
    initialTab: { type: String, default: 'market' },
    marketCount: { type: Number, default: 0 },
});

const { t, locale } = useI18n();

const VALID = ['market', 'mine', 'favorites'];
const activeTab = ref(VALID.includes(props.initialTab) ? props.initialTab : 'market');
const search = ref('');
const showFilters = ref(false);

const filters = ref({
    priceMin: '',
    priceMax: '',
    yearMin: '',
    kmMax: '',
    license: 'all',
    type: 'all',
    sortBy: 'created_at',
    sortDir: 'desc',
});

const tabs = computed(() => [
    { id: 'market', label: t('sales.tab_market'), count: props.marketSales.length },
    { id: 'mine', label: t('sales.tab_mine'), count: props.mySales.length },
    { id: 'favorites', label: t('sales.tab_favorites'), count: props.favoriteSales.length },
]);

const baseList = computed(() => {
    if (activeTab.value === 'mine') return props.mySales;
    if (activeTab.value === 'favorites') return props.favoriteSales;
    return props.marketSales;
});

const showQuery = computed(() => {
    if (activeTab.value === 'mine') return '?from=mine';
    if (activeTab.value === 'favorites') return '?from=fav';
    return '';
});

const activeFiltersCount = computed(() => {
    let n = 0;
    if (filters.value.priceMin !== '' || filters.value.priceMax !== '') n++;
    if (filters.value.yearMin !== '' || filters.value.kmMax !== '') n++;
    if (filters.value.license !== 'all') n++;
    if (filters.value.type !== 'all') n++;
    return n;
});

const resetFilters = () => {
    filters.value = {
        priceMin: '', priceMax: '', yearMin: '', kmMax: '',
        license: 'all', type: 'all', sortBy: 'created_at', sortDir: 'desc',
    };
};

const emptyTitle = computed(() => {
    if (activeTab.value === 'mine') return t('sales.no_listings');
    if (activeTab.value === 'favorites') return t('sales.no_favorites_short');
    return t('sales.no_results');
});

const formatPrice = (price) =>
    `${parseFloat(price || 0).toLocaleString(locale.value, { maximumFractionDigits: 0 })} €`;

const stateLabel = (state) => {
    const map = {
        actiu: t('sales.state_active'),
        reservat: t('sales.state_reserved'),
        venuda: t('sales.state_sold'),
        pausat: t('sales.state_paused'),
    };
    return map[state] || state;
};

const metaLine = (sale) => {
    const m = sale.motorcycle || {};
    const parts = [];
    if (m.brand || m.model) parts.push([m.brand, m.model].filter(Boolean).join(' '));
    if (m.year) parts.push(String(m.year));
    if (m.current_km != null) {
        parts.push(`${parseFloat(m.current_km).toLocaleString(locale.value, { maximumFractionDigits: 0 })} km`);
    }
    if (sale.location) parts.push(sale.location);
    return parts.join(' · ');
};

const filteredSales = computed(() => {
    let result = [...baseList.value];

    if (activeTab.value !== 'market') {
        const q = search.value.trim().toLowerCase();
        if (q) {
            result = result.filter((s) =>
                (s.title || '').toLowerCase().includes(q) ||
                (s.location || '').toLowerCase().includes(q) ||
                (s.motorcycle?.brand || '').toLowerCase().includes(q) ||
                (s.motorcycle?.model || '').toLowerCase().includes(q),
            );
        }
        return result;
    }

    const q = search.value.trim().toLowerCase();
    if (q) {
        result = result.filter((s) =>
            (s.title || '').toLowerCase().includes(q) ||
            (s.location || '').toLowerCase().includes(q) ||
            (s.motorcycle?.brand || '').toLowerCase().includes(q) ||
            (s.motorcycle?.model || '').toLowerCase().includes(q),
        );
    }

    if (filters.value.license !== 'all') {
        result = result.filter((s) => s.motorcycle?.license_type === filters.value.license);
    }
    if (filters.value.type !== 'all') {
        result = result.filter((s) => s.motorcycle?.type === filters.value.type);
    }
    if (filters.value.priceMin !== '') result = result.filter((s) => parseFloat(s.price) >= Number(filters.value.priceMin));
    if (filters.value.priceMax !== '') result = result.filter((s) => parseFloat(s.price) <= Number(filters.value.priceMax));
    if (filters.value.yearMin !== '') result = result.filter((s) => (s.motorcycle?.year || 0) >= Number(filters.value.yearMin));
    if (filters.value.kmMax !== '') result = result.filter((s) => (s.motorcycle?.current_km || 0) <= Number(filters.value.kmMax));

    return result.sort((a, b) => {
        let fieldA;
        let fieldB;
        if (filters.value.sortBy === 'year' || filters.value.sortBy === 'current_km') {
            fieldA = parseFloat(a.motorcycle?.[filters.value.sortBy] || 0);
            fieldB = parseFloat(b.motorcycle?.[filters.value.sortBy] || 0);
        } else if (filters.value.sortBy === 'price') {
            fieldA = parseFloat(a.price);
            fieldB = parseFloat(b.price);
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

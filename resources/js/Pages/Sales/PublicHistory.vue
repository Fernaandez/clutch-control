<template>
    <AppLayout :title="$t('sales.public_history_title')">
        <div class="px-6 pt-10 pb-28 max-w-xl mx-auto cc-fade-in">

            <header class="flex items-center gap-3 mb-8">
                <button type="button" @click="goBack" class="cc-icon-btn" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </button>
                <h1 class="cc-title flex-1 truncate">{{ $t('sales.public_history_title') }}</h1>
            </header>

            <p class="text-sm text-gray-500">{{ sale.motorcycle?.brand }} {{ sale.motorcycle?.model }}</p>
            <p class="mt-3 text-[48px] leading-[0.95] font-light tracking-tight text-white tabular-nums">
                {{ totalFilteredCost.toFixed(0) }}<span class="text-base text-gray-500 ml-1">€</span>
            </p>
            <p class="mt-2 text-sm text-gray-500">{{ $t('sales.history_total') }}</p>

            <input
                v-if="history.length > 6"
                v-model="filters.search"
                type="search"
                :placeholder="$t('maintenance.search_placeholder')"
                class="w-full mt-8 rounded-xl bg-white/[0.04] border-white/[0.08] text-white text-sm focus:border-white/30 focus:ring-0"
            >

            <div class="mt-4 flex items-center gap-2">
                <select v-model="filters.type" class="flex-1 rounded-xl bg-white/[0.04] border-white/[0.08] text-white text-sm focus:border-white/30 focus:ring-0">
                    <option value="all">{{ $t('maintenance.type_all') }}</option>
                    <option value="maintenance">{{ $t('maintenance.type_maintenance') }}</option>
                    <option value="repair">{{ $t('maintenance.type_repair') }}</option>
                    <option value="upgrade">{{ $t('maintenance.type_upgrade') }}</option>
                </select>
            </div>

            <div v-if="filteredHistory.length" class="mt-2 divide-y divide-white/[0.06]">
                <div v-for="log in filteredHistory" :key="log.id" class="py-4 flex items-start gap-4">
                    <div class="min-w-0 flex-1">
                        <p class="text-[15px] font-medium text-gray-100">{{ log.task_title }}</p>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ typeLabel(log.type) }}
                            · {{ formatDate(log.date) }}
                            <template v-if="log.km_at_moment"> · {{ log.km_at_moment }} km</template>
                        </p>
                    </div>
                    <p class="text-[15px] tabular-nums text-gray-300 flex-shrink-0">
                        {{ parseFloat(log.cost || 0).toFixed(0) }} €
                    </p>
                </div>
            </div>

            <div v-else class="py-16 text-center">
                <p class="text-base font-semibold text-gray-300">{{ $t('maintenance.no_results') }}</p>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import { smartBack } from '@/Composables/navigationStack.js';

const { t, locale } = useI18n();

const props = defineProps({
    sale: Object,
    history: { type: Array, default: () => [] },
});

const goBack = () => smartBack(route('sales.show', props.sale.id));

const filters = ref({ search: '', type: 'all' });

const formatDate = (dateString) => new Date(dateString).toLocaleDateString(locale.value);

const typeLabel = (type) => {
    const map = {
        maintenance: t('maintenance.type_maintenance'),
        repair: t('maintenance.type_repair'),
        upgrade: t('maintenance.type_upgrade'),
    };
    return map[type] || type;
};

const filteredHistory = computed(() => {
    let result = [...props.history];
    const q = filters.value.search.trim().toLowerCase();
    if (q) {
        result = result.filter((log) =>
            (log.task_title || '').toLowerCase().includes(q) ||
            (log.location && String(log.location).toLowerCase().includes(q)),
        );
    }
    if (filters.value.type !== 'all') {
        result = result.filter((log) => log.type === filters.value.type);
    }
    return result.sort((a, b) => (a.date < b.date ? 1 : -1));
});

const totalFilteredCost = computed(() =>
    filteredHistory.value.reduce((sum, log) => sum + parseFloat(log.cost || 0), 0),
);
</script>

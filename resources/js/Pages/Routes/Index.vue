<template>
    <AppLayout :title="$t('routes.title')">
        <div class="px-6 pt-10 pb-28 max-w-xl mx-auto cc-fade-in">

            <!-- El que has rodat mana per sobre de qualsevol menú -->
            <p class="text-sm text-gray-500">{{ $t('routes.ridden_in', { year: stats.year }) }}</p>
            <p class="mt-3 text-[64px] leading-[0.9] font-light tracking-[-0.04em] text-white tabular-nums">
                {{ formattedYearKm }}<span class="text-xl text-gray-500 ml-2">km</span>
            </p>

            <div v-if="stats.year_trips" class="mt-4 flex items-center gap-6 text-sm text-gray-500">
                <span>{{ $t('routes.trips_count', { n: stats.year_trips }) }}</span>
                <span v-if="stats.longest_km">{{ $t('routes.longest', { n: Math.round(stats.longest_km) }) }}</span>
            </div>

            <Link
                v-if="defaultMotorcycleId"
                :href="route('routes.free-ride', defaultMotorcycleId)"
                class="cc-btn-primary w-full py-3.5 mt-8"
            >
                {{ $t('routes.record_now') }}
            </Link>

            <!-- Última sortida: contingut, no un botó cap a una llista -->
            <section v-if="stats.last_trip" class="mt-10">
                <p class="cc-section-label">{{ $t('dashboard.last_ride') }}</p>
                <Link :href="route('trips.show', stats.last_trip.id)" class="mt-3 flex items-baseline gap-3 group">
                    <span class="text-[28px] font-light leading-none tabular-nums text-white">
                        {{ stats.last_trip.distance_km }}<span class="text-base text-gray-500 ml-1.5">km</span>
                    </span>
                    <span class="text-sm text-gray-500 group-hover:text-gray-300 transition-colors truncate">
                        <template v-if="stats.last_trip.title">{{ stats.last_trip.title }} · </template>
                        {{ relativeDate(stats.last_trip.started_at) }}
                    </span>
                </Link>
            </section>

            <!-- Segments -->
            <div class="mt-10 flex items-center gap-5 border-b border-white/[0.06]">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    type="button"
                    @click="activeTab = tab.id"
                    class="relative -mb-px pb-3 text-[13px] font-medium transition-colors"
                    :class="activeTab === tab.id ? 'text-white' : 'text-gray-500 hover:text-gray-300'"
                >
                    {{ tab.label }}
                    <span v-if="tab.count" class="ml-1 text-gray-600 tabular-nums">{{ tab.count }}</span>
                    <span v-if="activeTab === tab.id" class="absolute inset-x-0 -bottom-px h-px bg-white"></span>
                </button>

                <Link :href="route('routes.create')" class="cc-btn-text ml-auto mb-2.5">
                    {{ $t('routes.create_short') }}
                </Link>
            </div>

            <input
                v-if="activeTab !== 'habitual' && currentRoutes.length > 6"
                v-model="search"
                type="search"
                :placeholder="$t('routes.search_placeholder')"
                class="w-full mt-4 rounded-xl bg-white/[0.04] border-white/[0.08] text-white text-sm focus:border-white/30 focus:ring-0"
            >

            <!-- Rutes: files denses amb la forma real del traçat -->
            <div v-if="activeTab !== 'habitual'">
                <div v-if="filteredRoutes.length" class="divide-y divide-white/[0.06]">
                    <Link
                        v-for="ruta in filteredRoutes"
                        :key="ruta.id"
                        :href="route('routes.show', ruta.id)"
                        class="flex items-center gap-4 py-4 group"
                    >
                        <span class="w-11 h-11 flex-shrink-0 text-gray-600 group-hover:text-white transition-colors">
                            <RouteShape :geo-json="ruta.geo_json" />
                        </span>

                        <span class="min-w-0 flex-1">
                            <span class="block text-[15px] font-medium text-gray-100 group-hover:text-white transition-colors truncate">
                                {{ ruta.title }}
                            </span>
                            <span class="block mt-1 text-xs text-gray-500 truncate">
                                {{ metaLine(ruta) }}
                            </span>
                        </span>

                        <span class="text-right flex-shrink-0">
                            <span class="block text-[15px] tabular-nums text-gray-300">
                                {{ Math.round(ruta.distance_km || ruta.planned_distance_km || 0) }}<span class="text-xs text-gray-600 ml-1">km</span>
                            </span>
                            <span v-if="!ruta.is_public && activeTab === 'mine'" class="block mt-1 text-[11px] text-gray-600">
                                {{ $t('events.private') }}
                            </span>
                        </span>
                    </Link>
                </div>

                <div v-else class="py-16 text-center">
                    <p class="text-base font-semibold text-gray-300">{{ emptyTitle }}</p>
                    <Link v-if="activeTab === 'mine'" :href="route('routes.create')" class="cc-btn-text mt-4">
                        {{ $t('routes.create_one') }}
                    </Link>
                </div>
            </div>

            <!-- Habituals: trajectes que repeteixes, no rutes de cap de setmana -->
            <div v-else>
                <div v-if="habitualRoutes.length" class="divide-y divide-white/[0.06]">
                    <Link
                        v-for="item in habitualRoutes"
                        :key="item.id"
                        :href="item.route_id ? route('routes.show', item.route_id) : route('routes.habitual')"
                        class="flex items-center gap-4 py-4 group"
                    >
                        <span class="min-w-0 flex-1">
                            <span class="block text-[15px] font-medium text-gray-100 group-hover:text-white transition-colors truncate">
                                {{ item.title }}
                            </span>
                            <span v-if="item.motorcycle" class="block mt-1 text-xs text-gray-500 truncate">
                                {{ item.motorcycle.brand }} {{ item.motorcycle.model }}
                            </span>
                        </span>
                        <span v-if="item.distance_km" class="text-[15px] tabular-nums text-gray-300 flex-shrink-0">
                            {{ Math.round(item.distance_km) }}<span class="text-xs text-gray-600 ml-1">km</span>
                        </span>
                    </Link>
                </div>

                <p v-else class="py-16 text-center text-base font-semibold text-gray-300">
                    {{ $t('routes.no_habitual_routes') }}
                </p>

                <Link :href="route('routes.habitual')" class="cc-btn-text mt-6">
                    {{ $t('routes.manage_habitual') }}
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import RouteShape from '@/Components/RouteShape.vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    myRoutes: { type: Array, default: () => [] },
    communityRoutes: { type: Array, default: () => [] },
    habitualRoutes: { type: Array, default: () => [] },
    defaultMotorcycleId: { type: Number, default: null },
    initialTab: { type: String, default: 'mine' },
    ridingStats: { type: Object, default: () => ({}) },
});

const { t, locale } = useI18n();

const VALID_TABS = ['mine', 'community', 'habitual'];
const activeTab = ref(VALID_TABS.includes(props.initialTab) ? props.initialTab : 'mine');
const search = ref('');

const stats = computed(() => ({
    year: new Date().getFullYear(),
    year_km: 0,
    year_trips: 0,
    longest_km: 0,
    last_trip: null,
    ...props.ridingStats,
}));

const formattedYearKm = computed(() =>
    new Intl.NumberFormat(locale.value).format(Math.round(stats.value.year_km)),
);

const tabs = computed(() => [
    { id: 'mine', label: t('routes.tab_mine'), count: props.myRoutes.length },
    { id: 'community', label: t('routes.tab_community'), count: props.communityRoutes.length },
    { id: 'habitual', label: t('routes.tab_habitual'), count: props.habitualRoutes.length },
]);

const currentRoutes = computed(() =>
    activeTab.value === 'community' ? props.communityRoutes : props.myRoutes,
);

const filteredRoutes = computed(() => {
    const q = search.value.trim().toLowerCase();
    if (!q) return currentRoutes.value;

    return currentRoutes.value.filter((r) =>
        (r.title || '').toLowerCase().includes(q) ||
        (r.location_city || '').toLowerCase().includes(q),
    );
});

const emptyTitle = computed(() =>
    activeTab.value === 'community' ? t('routes.no_community_routes') : t('routes.no_my_routes'),
);

const metaLine = (ruta) => {
    const parts = [];
    if (ruta.location_city) parts.push(ruta.location_city);
    parts.push(t('routes.diff_label_' + (ruta.difficulty || 'medium')));
    if (activeTab.value === 'community' && ruta.user?.name) parts.push(ruta.user.name);

    return parts.join(' · ');
};

const relativeDate = (date) => {
    if (!date) return '';
    const days = Math.round((new Date(date).setHours(0, 0, 0, 0) - new Date().setHours(0, 0, 0, 0)) / 86400000);
    if (days === 0) return t('chats.today');
    if (days === -1) return t('chats.yesterday');

    return new Date(date).toLocaleDateString(locale.value, {
        day: 'numeric',
        month: 'long',
        ...(new Date(date).getFullYear() !== new Date().getFullYear() ? { year: 'numeric' } : {}),
    });
};
</script>

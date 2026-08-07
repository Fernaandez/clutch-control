<template>
    <AppLayout :title="$t('routes.history_title')">
        <div class="px-6 pt-10 pb-28 max-w-xl mx-auto cc-fade-in">

            <p class="text-sm text-gray-500">{{ $t('routes.history_title') }}</p>
            <p class="mt-3 text-[64px] leading-[0.9] font-light tracking-[-0.04em] text-white tabular-nums">
                {{ trips.length }}
            </p>
            <p class="mt-2 text-sm text-gray-500">
                {{ $t('routes.trips_count', { n: trips.length }) }}
                <template v-if="totalKm"> · {{ Math.round(totalKm) }} km</template>
            </p>

            <Link :href="route('routes.habitual')" class="cc-btn-primary w-full py-3.5 mt-8 text-center">
                {{ $t('routes.register_km') }}
            </Link>

            <section v-if="pendingTrips.length" class="mt-10">
                <div class="flex items-center justify-between gap-3">
                    <p class="cc-section-label text-red-400">{{ $t('routes.pending_trips_banner', { n: pendingTrips.length }) }}</p>
                    <Link :href="route('routes.pending')" class="cc-btn-text">
                        {{ $t('routes.sync_now') }}
                    </Link>
                </div>
                <p class="mt-2 text-sm text-gray-500">{{ $t('routes.pending_trips_desc') }}</p>
            </section>

            <div v-if="trips.length" class="divide-y divide-white/[0.06] mt-10">
                <Link
                    v-for="trip in trips"
                    :key="trip.id"
                    :href="route('trips.show', trip.id)"
                    class="flex items-center gap-4 py-4 group"
                >
                    <span class="min-w-0 flex-1">
                        <span class="block text-[15px] font-medium text-gray-100 group-hover:text-white transition-colors truncate">
                            {{ formatDate(trip.started_at) }}
                        </span>
                        <span class="block mt-1 text-xs text-gray-500 truncate">
                            {{ metaLine(trip) }}
                        </span>
                    </span>

                    <span class="text-right flex-shrink-0">
                        <span class="block text-[15px] tabular-nums text-gray-300">
                            {{ trip.distance_km ?? '—' }}<span class="text-xs text-gray-600 ml-1">km</span>
                        </span>
                        <span class="block mt-1 text-[11px] text-gray-600 tabular-nums">
                            {{ formatDuration(trip.duration_seconds) }}
                        </span>
                    </span>
                </Link>
            </div>

            <div v-else class="py-16 text-center mt-10">
                <p class="text-base font-semibold text-gray-300">{{ $t('routes.no_trips_yet') }}</p>
                <Link :href="route('routes.habitual')" class="cc-btn-text mt-4 inline-flex">
                    {{ $t('routes.register_km') }}
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n();

const props = defineProps({
    trips: {
        type: Array,
        default: () => [],
    },
});

const pendingTrips = ref([]);

const totalKm = computed(() =>
    props.trips.reduce((sum, trip) => sum + (parseFloat(trip.distance_km) || 0), 0),
);

onMounted(() => {
    try {
        const stored = localStorage.getItem('pending_trips');
        if (stored) pendingTrips.value = JSON.parse(stored) || [];
    } catch (e) {}
});

const formatDate = (isoStr) => {
    if (!isoStr) return '';
    return new Intl.DateTimeFormat(locale.value, { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(isoStr));
};

const formatDuration = (sec) => {
    if (!sec) return '0m';
    const hrs = Math.floor(sec / 3600);
    const mins = Math.floor((sec % 3600) / 60);
    if (hrs > 0) return `${hrs}h ${mins}m`;
    return `${mins}m`;
};

const metaLine = (trip) => {
    const parts = [];
    if (trip.manual_entry) parts.push(t('routes.manual_badge'));
    if (trip.route?.title) parts.push(trip.route.title);
    if (trip.motorcycle) {
        parts.push([trip.motorcycle.brand, trip.motorcycle.model].filter(Boolean).join(' '));
    }
    return parts.join(' · ');
};
</script>

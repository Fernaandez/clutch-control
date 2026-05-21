<template>
    <AppLayout :title="$t('routes.history_title')">
        <div class="px-4 py-6 pb-24">
            <div class="flex items-center gap-3 mb-6">
                <button type="button" @click="goBack" class="inline-flex items-center justify-center w-10 h-10 flex-shrink-0 rounded-full bg-brand-dark border border-brand-neon/50 text-brand-neon hover:bg-brand-neon hover:text-brand-black transition shadow-[0_0_10px_rgba(12,225,181,0.2)]" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                </button>
                <div>
                    <h1 class="text-2xl font-black uppercase tracking-tighter text-white leading-none">{{ $t('routes.history_title') }}</h1>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">{{ $t('routes.hub_history_desc') }}</p>
                </div>
            </div>

            <div v-if="pendingTrips.length > 0" class="mb-6 bg-brand-dark/30 border border-brand-neon rounded-xl p-4 shadow-[0_0_15px_rgba(12,225,181,0.2)]">
                <div class="flex items-start gap-4">
                    <div class="bg-brand-neon text-black rounded-full p-2 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" /></svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-white font-bold text-lg">{{ $t('routes.pending_trips_banner', { n: pendingTrips.length }) }}</h3>
                        <p class="text-gray-400 text-sm mt-1">{{ $t('routes.pending_trips_desc') }}</p>
                        <div class="mt-3">
                            <Link :href="route('routes.pending')" class="inline-block bg-brand-neon text-black font-black uppercase tracking-wider text-xs px-4 py-2 rounded-lg hover:bg-white transition shadow-neon">
                                {{ $t('routes.sync_now') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="trips.length === 0" class="bg-brand-surface border border-brand-dark border-dashed rounded-2xl p-10 text-center opacity-70">
                <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">{{ $t('routes.no_trips_yet') }}</p>
                <p class="text-xs text-gray-600 mt-2">{{ $t('routes.no_trips_hint') }}</p>
            </div>

            <div v-else class="space-y-3">
                <Link v-for="trip in trips" :key="trip.id" :href="route('trips.show', trip.id)"
                    class="flex items-center gap-4 bg-brand-surface border border-brand-dark rounded-2xl p-4 hover:border-brand-neon/50 transition shadow-lg group">
                    <div class="w-12 h-12 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-400"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-white font-bold text-sm">{{ formatDate(trip.started_at) }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-xs font-mono flex-wrap">
                            <span class="text-brand-neon font-black">{{ trip.distance_km ?? '?' }} km</span>
                            <span class="text-gray-500">{{ formatDuration(trip.duration_seconds) }}</span>
                            <span v-if="trip.motorcycle" class="text-gray-500 truncate">🏍 {{ trip.motorcycle.brand }} {{ trip.motorcycle.model }}</span>
                        </div>
                        <div v-if="trip.route" class="mt-1">
                            <span class="inline-flex items-center gap-1 bg-blue-500/10 border border-blue-500/20 text-blue-400 px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-widest">
                                🗺 {{ trip.route.title }}
                            </span>
                        </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-600 group-hover:text-brand-neon transition"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { smartBack } from '@/Composables/navigationStack.js';

const { locale } = useI18n();

defineProps({
    trips: {
        type: Array,
        default: () => [],
    },
});

const goBack = () => smartBack(route('routes.index'));
const pendingTrips = ref([]);

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
</script>

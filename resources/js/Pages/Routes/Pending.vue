<template>
    <AppLayout :title="$t('routes.pending_title')">
        <div class="px-6 pt-10 pb-28 max-w-xl mx-auto cc-fade-in">

            <p class="text-sm text-gray-500">{{ $t('routes.pending_title') }}</p>
            <p class="mt-3 text-[64px] leading-[0.9] font-light tracking-[-0.04em] text-white tabular-nums">
                {{ pendingTrips.length }}
            </p>
            <p class="mt-2 text-sm text-gray-500">{{ $t('routes.pending_subtitle') }}</p>

            <button
                v-if="pendingTrips.length > 1"
                type="button"
                @click="syncAll"
                class="cc-btn-primary w-full py-3.5 mt-8"
            >
                {{ $t('routes.sync_all', { n: pendingTrips.length }) }}
            </button>

            <div v-if="pendingTrips.length" class="divide-y divide-white/[0.06] mt-10">
                <div
                    v-for="(trip, index) in pendingTrips"
                    :key="trip.id"
                    class="py-4"
                >
                    <div class="flex items-center gap-4">
                        <div class="min-w-0 flex-1">
                            <p class="text-[15px] font-medium text-gray-100 truncate">
                                {{ formatDate(trip.started_at) }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500 tabular-nums">
                                {{ trip.distance_km }} km · {{ formatDuration(trip.duration_seconds) }}
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="removeTrip(index)"
                            class="cc-icon-btn w-9 h-9 text-gray-500 hover:text-red-400"
                            :aria-label="$t('common.delete')"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <button
                            type="button"
                            :disabled="syncingId === trip.id"
                            @click="syncTrip(trip, index)"
                            class="cc-btn-text flex-shrink-0 disabled:opacity-40"
                        >
                            {{ syncingId === trip.id ? '…' : $t('routes.sync_now') }}
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="py-16 text-center mt-10">
                <p class="text-base font-semibold text-gray-300">{{ $t('routes.pending_empty') }}</p>
                <Link :href="route('routes.index')" class="cc-btn-text mt-4 inline-flex">
                    {{ $t('routes.title') }}
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n();

const pendingTrips = ref([]);
const syncingId = ref(null);

onMounted(() => {
    loadPending();
});

const loadPending = () => {
    try {
        const stored = localStorage.getItem('pending_trips');
        if (stored) {
            pendingTrips.value = JSON.parse(stored);
        }
    } catch (e) {
        console.error('Error llegint memòria local', e);
    }
};

const removeTrip = (index) => {
    if (!confirm(t('routes.pending_delete_confirm'))) return;
    pendingTrips.value.splice(index, 1);
    localStorage.setItem('pending_trips', JSON.stringify(pendingTrips.value));
};

const syncTrip = async (tripObj, index) => {
    if (syncingId.value) return;
    syncingId.value = tripObj.id;

    try {
        const { data } = await axios.post(route('trips.store'), {
            distance_km: tripObj.distance_km,
            duration_seconds: tripObj.duration_seconds,
            waypoints: tripObj.waypoints,
            started_at: tripObj.started_at,
            motorcycle_id: tripObj.motorcycle_id,
            route_id: tripObj.route_id || null,
        });

        if (data.success) {
            pendingTrips.value.splice(index, 1);
            localStorage.setItem('pending_trips', JSON.stringify(pendingTrips.value));
        }
    } catch (error) {
        alert(t('routes.pending_sync_error'));
        console.error(error);
    } finally {
        syncingId.value = null;
    }
};

const syncAll = async () => {
    for (let i = pendingTrips.value.length - 1; i >= 0; i--) {
        await syncTrip(pendingTrips.value[i], i);
    }
};

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

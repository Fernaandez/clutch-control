<template>
    <AppLayout :title="$t('events.title')">
        <div class="px-6 pt-10 pb-28 max-w-xl mx-auto cc-fade-in">

            <!-- Hero: quant queda fins a la teva pròxima quedada -->
            <template v-if="nextEvent">
                <p class="text-sm text-gray-500">{{ $t('events.next_label') }}</p>
                <p class="mt-3 text-[64px] leading-[0.9] font-light tracking-[-0.04em] text-white tabular-nums">
                    {{ heroPrimary }}
                </p>
                <p class="mt-2 text-sm text-gray-500">{{ heroSecondary }}</p>

                <div class="mt-4 flex items-center justify-between gap-3">
                    <p class="text-sm text-gray-400 truncate min-w-0">{{ nextEvent.title }}</p>
                    <Link :href="route('events.show', nextEvent.id)" class="cc-btn-text flex-shrink-0">
                        {{ $t('common.view') }}
                    </Link>
                </div>
            </template>

            <template v-else>
                <p class="text-sm text-gray-500">{{ $t('events.title') }}</p>
                <p class="mt-3 text-[40px] leading-[0.95] font-light tracking-tight text-white">
                    {{ $t('events.no_upcoming_short') }}
                </p>
            </template>

            <Link :href="route('events.create')" class="cc-btn-primary w-full py-3.5 mt-8 text-center">
                {{ $t('events.create_short') }}
            </Link>

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
            </div>

            <input
                v-if="currentEvents.length > 6"
                v-model="search"
                type="search"
                :placeholder="$t('events.search_placeholder')"
                class="w-full mt-4 rounded-xl bg-white/[0.04] border-white/[0.08] text-white text-sm focus:border-white/30 focus:ring-0"
            >

            <div v-if="filteredEvents.length" class="divide-y divide-white/[0.06]">
                <div
                    v-for="event in filteredEvents"
                    :key="event.id"
                    class="flex items-center gap-4 py-4"
                >
                    <div class="min-w-0 flex-1">
                        <p class="text-[15px] font-medium text-gray-100 truncate">{{ event.title }}</p>
                        <p class="mt-1 text-xs text-gray-500 truncate">{{ metaLine(event) }}</p>
                    </div>

                    <div class="text-right flex-shrink-0">
                        <p class="text-[15px] tabular-nums text-gray-300">{{ dayMonth(event.start_time) }}</p>
                        <p
                            class="mt-1 text-xs tabular-nums"
                            :class="isFull(event) ? 'text-red-400' : 'text-gray-600'"
                        >
                            {{ ridersLabel(event) }}
                        </p>
                    </div>

                    <Link :href="route('events.show', event.id)" class="cc-btn-text flex-shrink-0">
                        {{ $t('common.view') }}
                    </Link>
                </div>
            </div>

            <div v-else class="py-16 text-center">
                <p class="text-base font-semibold text-gray-300">{{ emptyTitle }}</p>
                <Link
                    v-if="activeTab === 'mine'"
                    :href="route('events.create')"
                    class="cc-btn-text mt-4 inline-flex"
                >
                    {{ $t('events.create_one') }}
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
    myEvents: { type: Array, default: () => [] },
    discoverEvents: { type: Array, default: () => [] },
    initialTab: { type: String, default: 'mine' },
    nextEvent: { type: Object, default: null },
});

const { t, locale } = useI18n();

const VALID = ['mine', 'discover'];
const activeTab = ref(VALID.includes(props.initialTab) ? props.initialTab : 'mine');
const search = ref('');

const tabs = computed(() => [
    { id: 'mine', label: t('events.tab_mine'), count: props.myEvents.length },
    { id: 'discover', label: t('events.tab_discover'), count: props.discoverEvents.length },
]);

const currentEvents = computed(() =>
    activeTab.value === 'discover' ? props.discoverEvents : props.myEvents,
);

const filteredEvents = computed(() => {
    const q = search.value.trim().toLowerCase();
    if (!q) return currentEvents.value;

    return currentEvents.value.filter((e) =>
        (e.title || '').toLowerCase().includes(q) ||
        (e.location || '').toLowerCase().includes(q),
    );
});

const emptyTitle = computed(() =>
    activeTab.value === 'discover' ? t('events.no_discover') : t('events.no_events'),
);

const daysUntil = (date) => {
    if (!date) return null;
    const diff = new Date(date).setHours(0, 0, 0, 0) - new Date().setHours(0, 0, 0, 0);
    return Math.round(diff / 86400000);
};

const heroPrimary = computed(() => {
    if (!props.nextEvent?.start_time) return '—';
    const days = daysUntil(props.nextEvent.start_time);
    if (days === 0) return t('events.today');
    if (days === 1) return t('events.tomorrow');
    return String(Math.abs(days));
});

const heroSecondary = computed(() => {
    if (!props.nextEvent?.start_time) return '';
    const days = daysUntil(props.nextEvent.start_time);
    if (days === 0 || days === 1) {
        return new Date(props.nextEvent.start_time).toLocaleTimeString(locale.value, {
            hour: '2-digit',
            minute: '2-digit',
        });
    }
    return t('events.days_until', { n: days });
});

const dayMonth = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString(locale.value, { day: 'numeric', month: 'short' });
};

const isFull = (event) =>
    event.max_participants && event.participants_count >= event.max_participants;

const ridersLabel = (event) => {
    if (isFull(event)) return t('events.full_short');
    if (event.max_participants) return `${event.participants_count}/${event.max_participants}`;
    return String(event.participants_count ?? 0);
};

const metaLine = (event) => {
    const parts = [];
    if (event.location) parts.push(event.location);
    else parts.push(t('events.pending_location'));

    if (!event.is_public) parts.push(t('events.private'));
    if (event.is_organizer) parts.push(t('events.you_organize'));
    else if (event.is_attending) parts.push(t('events.attending'));
    else if (event.organizer?.name) parts.push(event.organizer.name);

    return parts.join(' · ');
};
</script>

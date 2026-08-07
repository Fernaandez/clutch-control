<template>
    <AppLayout :title="$t('events.my_agenda_title')">
        <div class="px-6 pt-10 pb-28 max-w-xl mx-auto cc-fade-in">
            <p class="text-sm text-gray-500">{{ $t('events.my_agenda_title') }}</p>
            <p class="mt-3 text-[64px] leading-[0.9] font-light tracking-[-0.04em] text-white tabular-nums">
                {{ events.length }}
            </p>
            <p class="mt-2 text-sm text-gray-500">{{ $t('events.tab_mine') }}</p>

            <Link :href="route('events.create')" class="cc-btn-primary w-full py-3.5 mt-8 text-center">
                {{ $t('events.create_short') }}
            </Link>

            <div v-if="events.length" class="divide-y divide-white/[0.06] mt-10">
                <div v-for="event in events" :key="event.id" class="flex items-center gap-4 py-4">
                    <div class="min-w-0 flex-1">
                        <p class="text-[15px] font-medium text-gray-100 truncate">{{ event.title }}</p>
                        <p class="mt-1 text-xs text-gray-500 truncate">
                            {{ metaLine(event) }}
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-[15px] tabular-nums text-gray-300">{{ dayMonth(event.start_time) }}</p>
                        <p class="mt-1 text-xs text-gray-600 tabular-nums">
                            {{ parseFloat(event.total_km || 0).toFixed(0) }} km
                        </p>
                    </div>
                    <Link :href="route('events.show', event.id)" class="cc-btn-text flex-shrink-0">
                        {{ $t('common.view') }}
                    </Link>
                </div>
            </div>

            <div v-else class="py-16 text-center mt-10">
                <p class="text-base font-semibold text-gray-300">{{ $t('events.no_events') }}</p>
                <Link :href="route('events.create')" class="cc-btn-text mt-4 inline-flex">
                    {{ $t('events.create_one') }}
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const { locale } = useI18n();

defineProps({ events: { type: Array, default: () => [] } });

const dayMonth = (date) => {
    if (!date) return '—';
    return new Intl.DateTimeFormat(locale.value, { day: 'numeric', month: 'short' }).format(new Date(date));
};

const metaLine = (event) => {
    const parts = [];
    if (event.location) parts.push(event.location);
    if (event.routes_count) parts.push(`${event.routes_count}`);
    return parts.join(' · ');
};
</script>

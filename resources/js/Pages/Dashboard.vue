<template>
    <AppLayout :current-moto-id="moto.id">
        <div class="relative min-h-[calc(100dvh-var(--app-header-total-height))] flex flex-col">

            <!-- La foto viu al fons, no ocupa mitja pantalla per decorar -->
            <div v-if="moto.photo && !photoFailed" class="absolute inset-x-0 top-0 h-[52vh] pointer-events-none">
                <img
                    :src="$page.props.storageUrl + '/' + moto.photo"
                    alt=""
                    class="w-full h-full object-cover opacity-[0.22]"
                    @error="photoFailed = true"
                >
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-brand-black/70 to-brand-black"></div>
            </div>

            <div class="relative flex-1 flex flex-col px-6 pt-10 pb-28 w-full max-w-xl mx-auto">

                <!-- Identitat: discreta. El protagonista és el número. -->
                <div class="flex items-center justify-between gap-3">
                    <Link
                        :href="route('motorcycles.index')"
                        class="inline-flex items-center gap-2 min-w-0 text-sm text-gray-500 hover:text-gray-300 transition-colors"
                    >
                        <span class="truncate">{{ moto.brand }} {{ moto.model }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-3.5 h-3.5 flex-shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </Link>
                    <Link :href="route('motorcycles.edit', moto.id)" class="cc-btn-text flex-shrink-0">
                        {{ $t('common.edit') }}
                    </Link>
                </div>

                <!-- El quilometratge és LA xifra d'una moto -->
                <p class="mt-6 text-[64px] leading-[0.9] font-light tracking-[-0.04em] text-white tabular-nums">
                    {{ formattedKm }}
                </p>
                <p class="mt-2 text-sm text-gray-500">{{ $t('dashboard.current_km') }}</p>

                <Link
                    :href="route('routes.free-ride', moto.id)"
                    class="cc-btn-primary w-full mt-8 py-3.5"
                >
                    {{ $t('routes.record_now') }}
                </Link>

                <!-- ATENCIÓ — aquesta secció només existeix si hi ha alguna cosa malament -->
                <section v-if="alerts.length" class="mt-12">
                    <p class="cc-section-label">{{ $t('dashboard.attention') }}</p>
                    <div class="mt-3 divide-y divide-white/[0.06]">
                        <Link
                            v-for="alert in alerts"
                            :key="alert.key"
                            :href="alert.href"
                            class="flex items-baseline gap-3 py-3.5 group"
                        >
                            <span class="w-1.5 h-1.5 rounded-full bg-red-400 flex-shrink-0 translate-y-[-2px]"></span>
                            <span class="flex-1 text-[15px] text-gray-200 group-hover:text-white transition-colors">
                                {{ alert.label }}
                            </span>
                            <span class="text-sm text-gray-500 tabular-nums">{{ alert.value }}</span>
                        </Link>
                    </div>
                </section>

                <!-- ÚLTIM — el fet és el contingut; l'acció és un botó explícit -->
                <section v-if="pulse.last_trip" class="mt-12">
                    <div class="flex items-center justify-between gap-3">
                        <p class="cc-section-label">{{ $t('dashboard.last_ride') }}</p>
                        <Link :href="route('trips.show', pulse.last_trip.id)" class="cc-btn-text">
                            {{ $t('common.view') }}
                        </Link>
                    </div>
                    <p class="mt-3 text-[28px] font-light tracking-tight text-white tabular-nums leading-none">
                        {{ pulse.last_trip.distance_km }}<span class="text-base text-gray-500 ml-1.5">km</span>
                    </p>
                    <p class="mt-2 text-sm text-gray-400">
                        <span v-if="pulse.last_trip.route_title">{{ pulse.last_trip.route_title }} · </span>
                        {{ relativeDate(pulse.last_trip.started_at) }}
                    </p>
                </section>

                <!-- PRÒXIM -->
                <section v-if="pulse.next_event" class="mt-12">
                    <div class="flex items-center justify-between gap-3">
                        <p class="cc-section-label">{{ $t('dashboard.upcoming') }}</p>
                        <Link :href="route('events.show', pulse.next_event.id)" class="cc-btn-text">
                            {{ $t('common.view') }}
                        </Link>
                    </div>
                    <p class="mt-3 text-[15px] text-gray-200">{{ pulse.next_event.title }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ relativeDate(pulse.next_event.start_time) }}</p>
                </section>

                <!-- HISTORIAL — dues xifres + botó per obrir -->
                <section v-if="pulse.logs_count > 0" class="mt-12">
                    <div class="flex items-center justify-between gap-3">
                        <p class="cc-section-label">{{ $t('dashboard.full_history') }}</p>
                        <Link :href="route('motorcycles.global-history', moto.id)" class="cc-btn-text">
                            {{ $t('common.view') }}
                        </Link>
                    </div>
                    <div class="flex items-baseline gap-8 mt-3">
                        <span>
                            <span class="block text-[28px] font-light tracking-tight text-white tabular-nums leading-none">{{ pulse.logs_count }}</span>
                            <span class="block mt-1.5 text-xs text-gray-500">{{ $t('dashboard.records') }}</span>
                        </span>
                        <span v-if="pulse.total_spent > 0">
                            <span class="block text-[28px] font-light tracking-tight text-white tabular-nums leading-none">{{ Math.round(pulse.total_spent) }}<span class="text-base text-gray-500 ml-1">€</span></span>
                            <span class="block mt-1.5 text-xs text-gray-500">{{ $t('dashboard.invested') }}</span>
                        </span>
                    </div>
                </section>

                <!-- Els mòduls de la moto: ocupen l'espai que queda, no floten com a text -->
                <nav class="mt-12 flex-1 min-h-[16rem] sm:max-h-[24rem] grid grid-cols-2 auto-rows-fr gap-3">
                    <Link
                        v-for="module in modules"
                        :key="module.key"
                        :href="module.href"
                        class="group flex flex-col justify-between rounded-2xl border border-white/[0.07] bg-white/[0.03] p-4 transition-colors hover:bg-white/[0.06] hover:border-white/20 active:bg-white/[0.08]"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <span class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500 transition-colors group-hover:text-white">
                                    <path stroke-linecap="round" stroke-linejoin="round" :d="module.icon" />
                                </svg>
                                <span v-if="module.alert" class="absolute -top-1 -right-1.5 w-1.5 h-1.5 rounded-full bg-red-400"></span>
                            </span>
                            <span v-if="module.count !== null" class="text-[26px] font-light leading-none tabular-nums text-white">
                                {{ module.count }}
                            </span>
                        </div>
                        <div class="mt-6">
                            <p class="text-[15px] font-medium text-gray-100">{{ module.label }}</p>
                            <p class="mt-0.5 text-xs text-gray-500 truncate">{{ module.hint }}</p>
                        </div>
                    </Link>
                </nav>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    moto: { type: Object, required: true },
    pulse: { type: Object, default: () => ({}) },
});

const { t, locale } = useI18n();

// Si la foto no carrega (fitxer absent en local), no mostrem el marc trencat.
const photoFailed = ref(false);

const formattedKm = computed(() =>
    new Intl.NumberFormat(locale.value).format(Math.round(props.moto.current_km ?? 0)),
);

const daysUntil = (date) => {
    if (!date) return null;
    const diff = new Date(date).setHours(0, 0, 0, 0) - new Date().setHours(0, 0, 0, 0);
    return Math.round(diff / 86400000);
};

const relativeDate = (date) => {
    if (!date) return '';
    const days = daysUntil(date);
    if (days === 0) return t('chats.today');
    if (days === -1) return t('chats.yesterday');

    return new Date(date).toLocaleDateString(locale.value, {
        day: 'numeric',
        month: 'long',
        ...(new Date(date).getFullYear() !== new Date().getFullYear() ? { year: 'numeric' } : {}),
    });
};

/** Només es mostra el que reclama acció. Si tot està bé, la secció no existeix. */
const alerts = computed(() => {
    const list = [];
    const docsHref = route('motorcycles.documentation.show', props.moto.id);

    const itvDays = daysUntil(props.moto.itv_expires_at);
    if (itvDays !== null && itvDays <= 30) {
        list.push({
            key: 'itv',
            label: 'ITV',
            value: itvDays < 0 ? t('dashboard.expired') : t('dashboard.in_days', { n: itvDays }),
            href: docsHref,
        });
    }

    const insuranceDays = daysUntil(props.moto.insurance_expires_at);
    if (insuranceDays !== null && insuranceDays <= 30) {
        list.push({
            key: 'insurance',
            label: t('motorcycles.insurance'),
            value: insuranceDays < 0 ? t('dashboard.expired') : t('dashboard.in_days', { n: insuranceDays }),
            href: docsHref,
        });
    }

    if (props.pulse.next_task && props.pulse.next_task.km_left <= 0) {
        list.push({
            key: 'task',
            label: props.pulse.next_task.title,
            value: t('dashboard.overdue_km', { n: Math.abs(props.pulse.next_task.km_left) }),
            href: route('motorcycles.maintenance.index', props.moto.id),
        });
    }

    return list;
});

const ICONS = {
    maintenance: 'M16.023 9.348h4.992V4.356m0 4.992-3.181-3.183a8.25 8.25 0 0 0-13.803 3.7M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7',
    repair: 'M21.75 6.75a4.5 4.5 0 0 1-4.884 4.484c-1.076-.091-2.264.071-2.95.904l-7.152 8.684a2.548 2.548 0 1 1-3.586-3.586l8.684-7.152c.833-.686.995-1.874.904-2.95a4.5 4.5 0 0 1 6.336-4.486l-3.276 3.276a3.004 3.004 0 0 0 2.25 2.25l3.276-3.276c.256.565.398 1.192.398 1.852Z',
    upgrade: 'M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z',
    documentation: 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z',
};

const docAlert = computed(() =>
    alerts.value.some(a => a.key === 'itv' || a.key === 'insurance'),
);

/** Els mòduls de la moto. Sempre hi són: són la manera d'arribar a tot el que la moto guarda. */
const modules = computed(() => {
    const counts = props.pulse.counts ?? {};

    return [
        {
            key: 'maintenance',
            label: t('dashboard.maintenance'),
            hint: t('dashboard.maintenance_subtitle'),
            icon: ICONS.maintenance,
            count: counts.maintenance ?? 0,
            alert: alerts.value.some(a => a.key === 'task'),
            href: route('motorcycles.maintenance.index', props.moto.id),
        },
        {
            key: 'repair',
            label: t('dashboard.repairs'),
            hint: t('dashboard.repairs_subtitle'),
            icon: ICONS.repair,
            count: counts.repair ?? 0,
            alert: false,
            href: route('motorcycles.repairs.index', props.moto.id),
        },
        {
            key: 'upgrade',
            label: t('dashboard.upgrades'),
            hint: t('dashboard.upgrades_subtitle'),
            icon: ICONS.upgrade,
            count: counts.upgrade ?? 0,
            alert: false,
            href: route('motorcycles.upgrades.index', props.moto.id),
        },
        {
            key: 'documentation',
            label: t('dashboard.documentation'),
            hint: t('dashboard.documentation_subtitle'),
            icon: ICONS.documentation,
            count: null,
            alert: docAlert.value,
            href: route('motorcycles.documentation.show', props.moto.id),
        },
    ];
});
</script>

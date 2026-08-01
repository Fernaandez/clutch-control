<template>
    <AppLayout :current-moto-id="moto.id">
        <div class="max-w-xl mx-auto px-6 py-6 pb-24 cc-fade-in">

            <header class="flex items-center gap-3 mb-2">
                <button type="button" @click="goBack" class="cc-icon-btn" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </button>
                <h1 class="cc-title flex-1 truncate">{{ $t('motorcycles.documentation') }}</h1>
            </header>

            <p class="text-sm text-gray-500 mb-8">{{ moto.brand }} {{ moto.model }}</p>

            <div v-if="!hasAnyData" class="py-12 text-center">
                <p class="text-sm text-gray-500">{{ $t('motorcycles.documentation_empty') }}</p>
            </div>

            <template v-else>
                <section v-if="moto.insurance_company || moto.insurance_policy_number || moto.insurance_expires_at" class="mt-12">
                    <p class="cc-section-label">{{ $t('motorcycles.insurance_badge') }}</p>

                    <div v-if="moto.insurance_expires_at" class="mt-3">
                        <p
                            class="text-[40px] font-light tracking-tight tabular-nums leading-none"
                            :class="daysUntil(moto.insurance_expires_at) !== null && daysUntil(moto.insurance_expires_at) < 30 ? 'text-red-400' : 'text-white'"
                        >
                            {{ formatDate(moto.insurance_expires_at) }}
                        </p>
                        <p
                            v-if="daysUntil(moto.insurance_expires_at) !== null"
                            class="mt-2 text-sm"
                            :class="daysUntil(moto.insurance_expires_at) < 30 ? 'text-red-400' : 'text-gray-500'"
                        >
                            <template v-if="daysUntil(moto.insurance_expires_at) < 0">{{ $t('dashboard.expired_days_ago', { n: Math.abs(daysUntil(moto.insurance_expires_at)) }) }}</template>
                            <template v-else-if="daysUntil(moto.insurance_expires_at) === 0">{{ $t('dashboard.expires_today') }}</template>
                            <template v-else>{{ $t('dashboard.days_left', { n: daysUntil(moto.insurance_expires_at) }) }}</template>
                        </p>
                    </div>

                    <div v-if="moto.insurance_company" class="mt-4">
                        <p class="text-sm text-gray-500">{{ $t('motorcycles.insurance_company') }}</p>
                        <p class="mt-0.5 text-[15px] font-medium text-gray-200">{{ moto.insurance_company }}</p>
                    </div>

                    <div v-if="moto.insurance_policy_number" class="mt-3">
                        <p class="text-sm text-gray-500">{{ $t('motorcycles.insurance_policy') }}</p>
                        <p class="mt-0.5 text-[15px] font-medium text-gray-200 tabular-nums">{{ moto.insurance_policy_number }}</p>
                    </div>
                </section>

                <section v-if="moto.itv_expires_at || moto.itv_last_passed_at" class="mt-12">
                    <p class="cc-section-label">{{ $t('motorcycles.itv_badge') }}</p>

                    <div v-if="moto.itv_expires_at" class="mt-3">
                        <p
                            class="text-[40px] font-light tracking-tight tabular-nums leading-none"
                            :class="moto.itv_status === 'expired' || moto.itv_status === 'expiring_soon' ? 'text-red-400' : 'text-white'"
                        >
                            {{ formatDate(moto.itv_expires_at) }}
                        </p>
                        <p
                            class="mt-2 text-sm"
                            :class="moto.itv_status === 'expired' || moto.itv_status === 'expiring_soon' ? 'text-red-400' : 'text-gray-500'"
                        >
                            {{ daysLeftLabel(moto.itv_expires_at, moto.itv_status) }}
                        </p>
                    </div>

                    <div v-if="moto.itv_last_passed_at" class="mt-4">
                        <p class="text-sm text-gray-500">{{ $t('motorcycles.itv_last_passed') }}</p>
                        <p class="mt-0.5 text-[15px] font-medium text-gray-200 tabular-nums">{{ formatDate(moto.itv_last_passed_at) }}</p>
                    </div>

                    <button
                        type="button"
                        @click="renewItvToday"
                        :disabled="renewing"
                        class="cc-btn-secondary w-full mt-6"
                    >
                        {{ renewing ? $t('motorcycles.saving') : $t('motorcycles.itv_renewed_today') }}
                    </button>
                </section>
            </template>

            <nav class="mt-14 pt-6 border-t border-white/[0.06] flex flex-wrap gap-2">
                <Link
                    :href="route('motorcycles.documentation.edit', moto.id)"
                    class="cc-btn-text"
                >
                    {{ $t('motorcycles.documentation_edit') }}
                </Link>
                <template v-if="otherExpirations.length > 0">
                    <Link
                        v-for="item in otherExpirations"
                        :key="`${item.motorcycle_id}-${item.type}`"
                        :href="route('motorcycles.documentation.show', item.motorcycle_id)"
                        class="cc-btn-text"
                        :class="item.status === 'expired' || item.status === 'expiring_soon' ? 'border-red-500/25 text-red-400 hover:text-red-300 hover:border-red-500/50' : ''"
                    >
                        {{ item.brand }} {{ item.model }} · {{ $t(`motorcycles.${item.type}_badge`) }}
                    </Link>
                </template>
            </nav>

        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import { smartBack } from '@/Composables/navigationStack.js';

const { t } = useI18n();

const props = defineProps({
    moto: Object,
    otherExpirations: { type: Array, default: () => [] },
});

const renewing = ref(false);

const goBack = () => smartBack(route('dashboard', props.moto.id));

const renewItvToday = () => {
    if (!confirm(t('motorcycles.itv_renew_confirm'))) {
        return;
    }

    renewing.value = true;
    router.post(route('motorcycles.documentation.itv-renew', props.moto.id), {}, {
        onFinish: () => { renewing.value = false; },
    });
};

const hasAnyData = computed(() =>
    props.moto.insurance_company ||
    props.moto.insurance_policy_number ||
    props.moto.insurance_expires_at ||
    props.moto.itv_expires_at ||
    props.moto.itv_last_passed_at
);

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const [y, m, d] = String(dateStr).slice(0, 10).split('-');
    return `${d}/${m}/${y}`;
};

const daysUntil = (dateStr) => {
    if (!dateStr) return null;
    const exp = new Date(String(dateStr).slice(0, 10) + 'T00:00:00');
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return Math.ceil((exp - today) / (1000 * 60 * 60 * 24));
};

const daysLeftLabel = (dateStr, status) => {
    const days = daysUntil(dateStr);
    if (days === null) return '';
    if (status === 'expired') return t('dashboard.expired_days_ago', { n: Math.abs(days) });
    if (days === 0) return t('dashboard.expires_today');
    return t('dashboard.days_left', { n: days });
};

const statusBadgeClass = (status) => {
    if (status === 'expired') return 'bg-red-500/10 text-red-400 border-red-500/30';
    if (status === 'expiring_soon') return 'bg-white/[0.06] text-white border-white/20';
    return 'bg-white/[0.04] text-gray-400 border-white/[0.08]';
};

const daysLeftClass = (status) => {
    if (status === 'expired') return 'text-red-400';
    if (status === 'expiring_soon') return 'text-white';
    return 'text-gray-500';
};

const docCardClass = (status) => {
    if (status === 'expired') return 'ring-1 ring-red-500/40 border-red-500/30';
    if (status === 'expiring_soon') return 'ring-1 ring-white/25 border-white/20';
    return '';
};
</script>

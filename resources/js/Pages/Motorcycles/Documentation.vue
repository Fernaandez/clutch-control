<template>
    <AppLayout :current-moto-id="moto.id">
        <div class="max-w-2xl mx-auto px-4 py-6 pb-24">
            <div class="mb-6 space-y-3">
                <div class="flex items-start gap-3">
                    <button type="button" @click="goBack" class="inline-flex items-center justify-center w-10 h-10 flex-shrink-0 rounded-full bg-brand-dark border border-brand-neon/50 text-brand-neon hover:bg-brand-neon hover:text-brand-black transition shadow-[0_0_10px_rgba(12,225,181,0.2)]" aria-label="Enrere">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                    </button>
                    <div class="min-w-0 flex-1 pt-0.5">
                        <h1 class="text-2xl font-bold text-white">{{ $t('motorcycles.documentation') }}</h1>
                        <p class="text-brand-muted text-sm truncate">{{ moto.brand }} {{ moto.model }}</p>
                    </div>
                </div>
                <Link :href="route('motorcycles.documentation.edit', moto.id)" class="inline-flex w-full sm:w-auto justify-center items-center gap-2 bg-brand-neon text-brand-black px-5 py-2.5 rounded-lg font-bold text-sm shadow-neon hover:bg-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" /></svg>
                    {{ $t('motorcycles.documentation_edit') }}
                </Link>
            </div>

            <div v-if="!hasAnyData" class="text-center py-12 text-gray-500 bg-brand-surface rounded-xl border border-brand-dark border-dashed mb-4">
                <p>{{ $t('motorcycles.documentation_empty') }}</p>
            </div>

            <div class="space-y-4">
                <div class="bg-brand-surface rounded-xl p-5 border border-brand-dark shadow-lg">
                    <div class="flex items-center justify-between gap-2 mb-4">
                        <div>
                            <h2 class="text-sm font-bold text-white uppercase tracking-widest">{{ $t('motorcycles.insurance_badge') }}</h2>
                            <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-0.5">{{ $t('motorcycles.insurance_auto') }}</p>
                        </div>
                    </div>
                    <dl class="space-y-3 text-sm">
                        <div v-if="moto.insurance_company">
                            <dt class="text-xs font-bold text-gray-500 uppercase">{{ $t('motorcycles.insurance_company') }}</dt>
                            <dd class="text-white font-medium mt-0.5">{{ moto.insurance_company }}</dd>
                        </div>
                        <div v-if="moto.insurance_policy_number">
                            <dt class="text-xs font-bold text-gray-500 uppercase">{{ $t('motorcycles.insurance_policy') }}</dt>
                            <dd class="text-white font-mono mt-0.5">{{ moto.insurance_policy_number }}</dd>
                        </div>
                        <p v-if="!moto.insurance_company && !moto.insurance_policy_number" class="text-gray-500 text-sm">{{ $t('dashboard.no_date_set') }}</p>
                    </dl>
                </div>

                <div class="bg-brand-surface rounded-xl p-5 border border-brand-dark shadow-lg" :class="docCardClass(moto.itv_status)">
                    <div class="flex items-center justify-between gap-2 mb-4">
                        <h2 class="text-sm font-bold text-white uppercase tracking-widest">{{ $t('motorcycles.itv_badge') }}</h2>
                        <span v-if="moto.itv_status" :class="statusBadgeClass(moto.itv_status)" class="text-[10px] font-black uppercase px-2 py-1 rounded border">
                            {{ $t(`motorcycles.status_${moto.itv_status}`) }}
                        </span>
                        <span v-else class="text-[10px] font-black uppercase px-2 py-1 rounded border bg-gray-500/10 text-gray-500 border-gray-500/30">
                            {{ $t('dashboard.no_date_set') }}
                        </span>
                    </div>
                    <dl class="space-y-3 text-sm">
                        <div v-if="moto.itv_expires_at">
                            <dt class="text-xs font-bold text-gray-500 uppercase">{{ $t('motorcycles.itv_expires') }}</dt>
                            <dd class="text-brand-neon font-mono mt-0.5">{{ formatDate(moto.itv_expires_at) }}</dd>
                            <dd class="text-xs mt-1" :class="daysLeftClass(moto.itv_status)">{{ daysLeftLabel(moto.itv_expires_at, moto.itv_status) }}</dd>
                        </div>
                        <div v-if="moto.itv_last_passed_at">
                            <dt class="text-xs font-bold text-gray-500 uppercase">{{ $t('motorcycles.itv_last_passed') }}</dt>
                            <dd class="text-white font-mono mt-0.5">{{ formatDate(moto.itv_last_passed_at) }}</dd>
                        </div>
                    </dl>
                    <button
                        type="button"
                        @click="renewItvToday"
                        :disabled="renewing"
                        class="mt-4 w-full bg-brand-neon hover:bg-white text-brand-black font-black uppercase tracking-wider py-3 rounded-xl transition text-xs disabled:opacity-50"
                    >
                        {{ renewing ? $t('motorcycles.saving') : $t('motorcycles.itv_renewed_today') }}
                    </button>
                </div>
            </div>

            <div v-if="otherExpirations.length > 0" class="mt-6 bg-brand-surface rounded-xl p-5 border border-brand-dark">
                <h3 class="text-brand-muted font-bold text-sm uppercase mb-3">{{ $t('dashboard.other_motos_expiring') }}</h3>
                <ul class="space-y-2">
                    <li v-for="item in otherExpirations" :key="`${item.motorcycle_id}-${item.type}`">
                        <Link :href="route('motorcycles.documentation.show', item.motorcycle_id)" class="flex items-center justify-between gap-3 text-sm hover:bg-brand-black/40 rounded-lg p-2 -mx-2 transition">
                            <span class="text-gray-300 truncate">{{ item.brand }} {{ item.model }} · {{ $t(`motorcycles.${item.type}_badge`) }}</span>
                            <span :class="statusBadgeClass(item.status)" class="text-[10px] font-black uppercase px-2 py-1 rounded border flex-shrink-0">
                                {{ formatDate(item.expires_at) }}
                            </span>
                        </Link>
                    </li>
                </ul>
            </div>
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
    renewing.value = true;
    router.post(route('motorcycles.documentation.itv-renew', props.moto.id), {}, {
        onFinish: () => { renewing.value = false; },
    });
};

const hasAnyData = computed(() =>
    props.moto.insurance_company ||
    props.moto.insurance_policy_number ||
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
    if (status === 'expiring_soon') return 'bg-yellow-500/10 text-yellow-400 border-yellow-500/30';
    return 'bg-green-500/10 text-green-400 border-green-500/30';
};

const daysLeftClass = (status) => {
    if (status === 'expired') return 'text-red-400';
    if (status === 'expiring_soon') return 'text-yellow-400';
    return 'text-gray-500';
};

const docCardClass = (status) => {
    if (status === 'expired') return 'ring-1 ring-red-500/40 border-red-500/30';
    if (status === 'expiring_soon') return 'ring-1 ring-yellow-500/40 border-yellow-500/30';
    return '';
};
</script>

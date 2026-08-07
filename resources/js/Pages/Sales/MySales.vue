<template>
    <AppLayout :title="$t('sales.my_listings_title')">
        <div class="px-6 pt-10 pb-28 max-w-xl mx-auto cc-fade-in">
            <p class="text-sm text-gray-500">{{ $t('sales.my_listings_title') }}</p>
            <p class="mt-3 text-[64px] leading-[0.9] font-light tracking-[-0.04em] text-white tabular-nums">
                {{ sales.length }}
            </p>
            <p class="mt-2 text-sm text-gray-500">{{ $t('sales.tab_mine') }}</p>

            <Link :href="route('sales.create')" class="cc-btn-primary w-full py-3.5 mt-8 text-center">
                {{ $t('sales.publish_short') }}
            </Link>

            <div v-if="sales.length" class="divide-y divide-white/[0.06] mt-10">
                <div v-for="sale in sales" :key="sale.id" class="flex items-center gap-3 py-4">
                    <div class="w-14 h-14 rounded-xl overflow-hidden bg-white/[0.04] border border-white/[0.06] flex-shrink-0">
                        <img
                            v-if="sale.images?.[0]"
                            :src="$page.props.storageUrl + '/' + sale.images[0].image_path"
                            alt=""
                            class="w-full h-full object-cover"
                        >
                        <div v-else class="w-full h-full flex items-center justify-center text-[10px] text-gray-600 uppercase tracking-wider px-1 text-center">
                            {{ sale.motorcycle?.brand || '—' }}
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[15px] font-medium text-gray-100 truncate">{{ sale.title }}</p>
                        <p class="mt-1 text-xs text-gray-500 truncate">
                            {{ [sale.motorcycle?.brand, sale.motorcycle?.model].filter(Boolean).join(' ') }}
                        </p>
                        <p
                            v-if="sale.state !== 'actiu'"
                            class="mt-1 text-xs"
                            :class="sale.state === 'venuda' ? 'text-red-400' : 'text-gray-500'"
                        >
                            {{ stateLabel(sale.state) }}
                        </p>
                    </div>
                    <p
                        class="text-[15px] tabular-nums font-medium flex-shrink-0"
                        :class="sale.state === 'venuda' ? 'text-red-400' : 'text-white'"
                    >
                        {{ formatPrice(sale.price) }}
                    </p>
                    <Link :href="route('sales.show', { sale: sale.id, from: 'mine' })" class="cc-btn-text flex-shrink-0">
                        {{ $t('common.view') }}
                    </Link>
                </div>
            </div>

            <div v-else class="py-16 text-center mt-10">
                <p class="text-base font-semibold text-gray-300">{{ $t('sales.no_listings') }}</p>
                <Link :href="route('sales.create')" class="cc-btn-text mt-4 inline-flex">
                    {{ $t('sales.create_listing') }}
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const { t, locale } = useI18n();

defineProps({ sales: { type: Array, default: () => [] } });

const formatPrice = (price) =>
    `${parseFloat(price || 0).toLocaleString(locale.value, { maximumFractionDigits: 0 })} €`;

const stateLabel = (state) => {
    const map = {
        actiu: t('sales.state_active'),
        reservat: t('sales.state_reserved'),
        venuda: t('sales.state_sold'),
        pausat: t('sales.state_paused'),
    };
    return map[state] || state;
};
</script>

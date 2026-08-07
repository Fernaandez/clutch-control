<template>
    <AppLayout :title="sale.title">
        <div class="px-6 pt-10 pb-28 max-w-xl mx-auto cc-fade-in">

            <header class="flex items-center gap-3 mb-8">
                <button type="button" @click="goBack" class="cc-icon-btn" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </button>
                <h1 class="cc-title flex-1 truncate">{{ sale.title }}</h1>
            </header>

            <!-- Galeria -->
            <div v-if="sale.images?.length" class="mb-8">
                <div class="relative overflow-hidden rounded-2xl border border-white/[0.06] bg-white/[0.03] aspect-[16/10]">
                    <img
                        :src="$page.props.storageUrl + '/' + currentPhoto"
                        alt=""
                        class="w-full h-full object-cover cursor-pointer"
                        @click="lightbox = true"
                    >
                    <span v-if="sale.images.length > 1" class="absolute bottom-3 right-3 text-xs tabular-nums text-white/80 bg-black/50 px-2 py-1 rounded-lg">
                        {{ selectedPhoto + 1 }}/{{ sale.images.length }}
                    </span>
                </div>
                <div v-if="sale.images.length > 1" class="mt-3 flex gap-2 overflow-x-auto">
                    <button
                        v-for="(img, i) in sale.images"
                        :key="img.id"
                        type="button"
                        @click="selectedPhoto = i"
                        class="w-14 h-14 rounded-xl overflow-hidden border flex-shrink-0 transition"
                        :class="selectedPhoto === i ? 'border-white/40' : 'border-white/[0.06] opacity-60'"
                    >
                        <img :src="$page.props.storageUrl + '/' + img.image_path" alt="" class="w-full h-full object-cover">
                    </button>
                </div>
            </div>

            <!-- Hero: el preu mana -->
            <p class="text-sm text-gray-500">{{ $t('sales.price') }}</p>
            <p
                class="mt-3 text-[48px] leading-[0.95] font-light tracking-tight tabular-nums"
                :class="sale.state === 'venuda' ? 'text-red-400' : 'text-white'"
            >
                {{ formatPrice(sale.price) }}
            </p>
            <p class="mt-2 text-sm text-gray-500">
                {{ sale.location }}
                <template v-if="sale.state !== 'actiu'"> · {{ stateLabel }}</template>
            </p>

            <div class="mt-8 flex items-baseline gap-8">
                <div v-if="sale.motorcycle?.year">
                    <p class="text-[28px] font-light leading-none tabular-nums text-white">{{ sale.motorcycle.year }}</p>
                    <p class="mt-1.5 text-xs text-gray-500">{{ $t('sales.year') }}</p>
                </div>
                <div v-if="sale.motorcycle?.current_km != null">
                    <p class="text-[28px] font-light leading-none tabular-nums text-white">
                        {{ formatKm(sale.motorcycle.current_km) }}
                    </p>
                    <p class="mt-1.5 text-xs text-gray-500">km</p>
                </div>
                <div v-if="sale.motorcycle?.cc">
                    <p class="text-[28px] font-light leading-none tabular-nums text-white">{{ sale.motorcycle.cc }}</p>
                    <p class="mt-1.5 text-xs text-gray-500">cc</p>
                </div>
            </div>

            <!-- CTA -->
            <div class="mt-8 space-y-3">
                <Link
                    v-if="!isOwner && sale.state !== 'venuda'"
                    :href="route('chats.start')"
                    method="post"
                    as="button"
                    :data="{ other_user_id: sale.motorcycle?.user_id, motorcycle_id: sale.motorcycle_id }"
                    class="cc-btn-primary w-full py-3.5"
                >
                    {{ $t('sales.chat_with_seller') }}
                </Link>

                <Link
                    v-if="isOwner && sale.state !== 'venuda'"
                    :href="route('sales.mark-sold', sale.id)"
                    method="patch"
                    as="button"
                    class="cc-btn-secondary w-full py-3.5"
                >
                    {{ $t('sales.mark_sold_btn') }}
                </Link>
            </div>

            <p v-if="sale.description" class="mt-10 text-sm text-gray-400 leading-relaxed whitespace-pre-line">
                {{ sale.description }}
            </p>

            <!-- Specs -->
            <section class="mt-12">
                <p class="cc-section-label">{{ $t('sales.technical_specs') }}</p>
                <div class="mt-2 divide-y divide-white/[0.06]">
                    <div v-for="row in specRows" :key="row.label" class="flex items-center justify-between py-3.5">
                        <span class="text-sm text-gray-500">{{ row.label }}</span>
                        <span class="text-sm text-gray-200 tabular-nums">{{ row.value }}</span>
                    </div>
                </div>
            </section>

            <section v-if="sale.motorcycle?.extras" class="mt-10">
                <p class="cc-section-label">{{ $t('sales.equipped_extras') }}</p>
                <p class="mt-3 text-sm text-gray-400 leading-relaxed whitespace-pre-line">{{ sale.motorcycle.extras }}</p>
            </section>

            <nav class="mt-12 pt-6 border-t border-white/[0.06] flex flex-wrap gap-2">
                <Link
                    v-if="!isOwner"
                    :href="route('sales.toggle-favorite', sale.id)"
                    method="post"
                    as="button"
                    preserve-scroll
                    class="cc-btn-text"
                >
                    {{ sale.is_favorited ? $t('sales.remove_fav') : $t('sales.save_fav') }}
                </Link>
                <Link
                    v-if="sale.show_history"
                    :href="route('sales.history', sale.id)"
                    class="cc-btn-text"
                >
                    {{ $t('sales.view_history') }}
                </Link>
                <Link
                    v-if="isOwner"
                    :href="route('sales.edit', sale.id)"
                    class="cc-btn-text"
                >
                    {{ $t('common.edit') }}
                </Link>
                <ReportButton
                    v-if="!isOwner"
                    reportable-type="sale"
                    :reportable-id="sale.id"
                    :label="$t('sales.report')"
                    :context-label="`${$t('sales.report')}: ${sale.title}`"
                    button-class="cc-btn-text border-red-500/25 text-red-400 hover:text-red-300 hover:border-red-500/50"
                />
            </nav>

            <p class="mt-8 text-xs text-gray-600">
                {{ sale.motorcycle?.user?.name || 'Rider' }}
                · {{ new Date(sale.created_at).toLocaleDateString(locale) }}
                · {{ sale.views_count || 0 }} {{ $t('sales.views') }}
            </p>
        </div>

        <Teleport to="body">
            <div
                v-if="lightbox && currentPhoto"
                class="fixed inset-0 z-[6000] bg-black flex items-center justify-center"
                @click="lightbox = false"
            >
                <img :src="$page.props.storageUrl + '/' + currentPhoto" alt="" class="max-w-full max-h-full object-contain">
            </div>
        </Teleport>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import ReportButton from '@/Components/ReportButton.vue';
import { smartBack } from '@/Composables/navigationStack.js';

const props = defineProps({ sale: Object });
const { t, locale } = useI18n();
const page = usePage();

const isOwner = computed(() => props.sale.motorcycle?.user_id === page.props.auth.user?.id);
const selectedPhoto = ref(0);
const lightbox = ref(false);
const currentPhoto = computed(() => props.sale.images?.[selectedPhoto.value]?.image_path || '');

const formatPrice = (price) =>
    `${parseFloat(price || 0).toLocaleString(locale.value, { maximumFractionDigits: 0 })} €`;

const formatKm = (km) =>
    parseFloat(km || 0).toLocaleString(locale.value, { maximumFractionDigits: 0 });

const stateLabel = computed(() => {
    const map = {
        actiu: t('sales.state_active'),
        reservat: t('sales.state_reserved'),
        venuda: t('sales.state_sold'),
        pausat: t('sales.state_paused'),
    };
    return map[props.sale.state] || props.sale.state;
});

const specRows = computed(() => {
    const m = props.sale.motorcycle || {};
    const rows = [
        { label: t('sales.brand'), value: m.brand || '—' },
        { label: t('sales.model'), value: m.model || '—' },
        { label: t('sales.year'), value: m.year || '—' },
        { label: 'km', value: m.current_km != null ? formatKm(m.current_km) : '—' },
        { label: 'cc', value: m.cc || '—' },
        { label: 'CV', value: m.power_cv || '—' },
        { label: t('sales.license'), value: m.license_type || '—' },
        { label: t('sales.style'), value: m.type || '—' },
    ];
    return rows;
});

const goBack = () => {
    const q = new URLSearchParams(window.location.search);
    if (q.get('from') === 'mine') {
        router.visit(route('sales.index', { tab: 'mine' }));
        return;
    }
    if (q.get('from') === 'fav') {
        router.visit(route('sales.index', { tab: 'favorites' }));
        return;
    }
    smartBack(route('sales.index'));
};
</script>

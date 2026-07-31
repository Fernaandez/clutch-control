<template>
    <AppLayout :title="$t('routes.title')">
        <div class="px-4 py-6 pb-24 max-w-3xl mx-auto">

            <PageHeader :title="$t('routes.title')" :back="false" />

            <!-- Acció principal: sortir a rodar ara -->
            <section class="cc-card p-5 mb-8 cc-fade-in">
                <p class="cc-section-label mb-3">{{ $t('routes.hub_ride_now') }}</p>

                <Link
                    v-if="defaultMotorcycleId"
                    :href="route('routes.free-ride', defaultMotorcycleId)"
                    class="cc-btn-primary w-full py-4 text-[15px]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                    {{ $t('free_ride.title') }}
                </Link>

                <Link v-else :href="route('motorcycles.index')" class="cc-btn-secondary w-full py-4">
                    {{ $t('routes.hub_no_moto') }}
                </Link>
            </section>

            <p class="cc-section-label mb-3">{{ $t('routes.hub_menu') }}</p>

            <nav class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <Link
                    v-for="action in actions"
                    :key="action.name"
                    :href="action.href"
                    class="cc-card cc-card-hover group flex items-center gap-3.5 p-4"
                >
                    <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-white/[0.05] text-gray-400 group-hover:text-brand-neon group-hover:bg-brand-neon/10 transition-colors flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" :d="action.icon" />
                        </svg>
                    </span>

                    <span class="font-medium text-[15px] text-gray-200 group-hover:text-white transition-colors">{{ action.label }}</span>

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 ml-auto text-gray-600 group-hover:text-gray-400 transition-colors flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </Link>
            </nav>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

defineProps({
    defaultMotorcycleId: {
        type: Number,
        default: null,
    },
});

const { t } = useI18n();

const actions = computed(() => [
    {
        name: 'create',
        label: t('routes.hub_create'),
        href: route('routes.create'),
        icon: 'M12 4.5v15m7.5-7.5h-15',
    },
    {
        name: 'explore',
        label: t('routes.hub_explore'),
        href: route('routes.explore'),
        icon: 'M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z',
    },
    {
        name: 'mine',
        label: t('routes.my_routes'),
        href: route('routes.MyRoutes'),
        icon: 'M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776',
    },
    {
        name: 'history',
        label: t('routes.hub_history'),
        href: route('routes.history'),
        icon: 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
    },
    {
        name: 'habitual',
        label: t('routes.hub_habitual'),
        href: route('routes.habitual'),
        icon: 'M20.25 14.15v4.25c0 .414-.336.75-.75.75h-4.5a.75.75 0 0 1-.75-.75v-4.25m0 0h4.125c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9m7.5 12.15v4.125A2.625 2.625 0 0 1 16.5 21h-9a2.625 2.625 0 0 1-2.625-2.625V15M12 9v6m0 0-3-3m3 3 3-3',
    },
    {
        name: 'plan',
        label: t('routes.hub_auto_route'),
        href: route('routes.plan'),
        icon: 'M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5',
    },
]);
</script>

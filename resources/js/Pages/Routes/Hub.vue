<template>
    <AppLayout :title="$t('routes.title')">
        <div class="px-4 py-6 pb-24">
            <div class="mb-6">
                <h1 class="text-3xl font-black text-white uppercase tracking-tighter">{{ $t('routes.title') }}</h1>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mt-1">{{ $t('routes.hub_subtitle') }}</p>
            </div>

            <div class="bg-brand-surface rounded-2xl p-5 border border-brand-dark shadow-neon flex flex-col gap-4 relative overflow-hidden mb-6">
                <div class="z-10">
                    <p class="text-gray-400 text-[10px] uppercase tracking-widest mb-1">{{ $t('routes.hub_ride_now') }}</p>
                    <p class="text-white font-bold text-sm">{{ $t('routes.hub_ride_now_desc') }}</p>
                </div>
                <div class="z-10">
                    <Link
                        v-if="defaultMotorcycleId"
                        :href="route('routes.free-ride', defaultMotorcycleId)"
                        class="flex items-center justify-center gap-2 bg-brand-dark hover:bg-brand-neon hover:text-brand-black text-brand-neon w-full px-6 py-4 rounded-xl transition border border-brand-neon font-black uppercase tracking-widest text-xs"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                        {{ $t('free_ride.title') }}
                    </Link>
                    <Link
                        v-else
                        :href="route('motorcycles.index')"
                        class="flex items-center justify-center gap-2 bg-brand-dark/50 text-gray-400 w-full px-6 py-4 rounded-xl border border-brand-dark font-bold uppercase tracking-widest text-xs"
                    >
                        {{ $t('routes.hub_no_moto') }}
                    </Link>
                </div>
                <div class="absolute right-0 top-0 w-48 h-48 bg-brand-neon blur-[80px] opacity-10 rounded-full pointer-events-none"></div>
            </div>

            <h3 class="text-brand-muted font-bold text-sm uppercase mb-3 px-1">{{ $t('routes.hub_menu') }}</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <Link
                    :href="route('routes.explore')"
                    class="flex items-center gap-3 bg-brand-surface border border-brand-dark rounded-xl p-4 hover:border-brand-neon hover:bg-brand-dark/50 transition group text-left"
                >
                    <div class="bg-brand-neon/20 p-2 sm:p-3 rounded-lg text-brand-neon group-hover:text-brand-neon flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" /></svg>
                    </div>
                    <div>
                        <span class="block font-bold text-gray-200 text-sm uppercase tracking-widest">{{ $t('routes.hub_explore') }}</span>
                        <span class="block text-[10px] text-gray-500 uppercase tracking-widest mt-0.5">{{ $t('routes.hub_explore_desc') }}</span>
                    </div>
                </Link>

                <Link
                    :href="route('routes.MyRoutes')"
                    class="flex items-center gap-3 bg-brand-surface border border-brand-dark rounded-xl p-4 hover:border-blue-500 hover:bg-brand-dark/50 transition group text-left"
                >
                    <div class="bg-blue-500/20 p-2 sm:p-3 rounded-lg text-blue-400 group-hover:text-blue-300 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" /></svg>
                    </div>
                    <div>
                        <span class="block font-bold text-gray-200 text-sm uppercase tracking-widest">{{ $t('routes.my_routes') }}</span>
                        <span class="block text-[10px] text-gray-500 uppercase tracking-widest mt-0.5">{{ $t('routes.hub_my_routes_desc') }}</span>
                    </div>
                </Link>

                <Link
                    :href="route('routes.create')"
                    class="flex items-center gap-3 bg-brand-surface border border-brand-dark rounded-xl p-4 hover:border-purple-500 hover:bg-brand-dark/50 transition group text-left"
                >
                    <div class="bg-purple-500/20 p-2 sm:p-3 rounded-lg text-purple-400 group-hover:text-purple-300 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    </div>
                    <div>
                        <span class="block font-bold text-gray-200 text-sm uppercase tracking-widest">{{ $t('routes.hub_plan') }}</span>
                        <span class="block text-[10px] text-gray-500 uppercase tracking-widest mt-0.5">{{ $t('routes.hub_plan_desc') }}</span>
                    </div>
                </Link>

                <Link
                    :href="historialUrl"
                    class="flex items-center gap-3 bg-brand-surface border border-brand-dark rounded-xl p-4 hover:border-yellow-500 hover:bg-brand-dark/50 transition group text-left"
                >
                    <div class="bg-yellow-500/20 p-2 sm:p-3 rounded-lg text-yellow-400 group-hover:text-yellow-300 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </div>
                    <div>
                        <span class="block font-bold text-gray-200 text-sm uppercase tracking-widest">{{ $t('routes.hub_history') }}</span>
                        <span class="block text-[10px] text-gray-500 uppercase tracking-widest mt-0.5">{{ $t('routes.hub_history_desc') }}</span>
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    defaultMotorcycleId: {
        type: Number,
        default: null,
    },
});

const historialUrl = computed(() => `${route('routes.MyRoutes')}#historial-recorreguts`);
</script>

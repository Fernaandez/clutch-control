<template>
    <AppLayout>
        <div class="px-4 py-6 pb-24">
            
            <div class="flex items-center justify-between mb-6">
                <div>
                    <Link :href="route('motorcycles.maintenance.index', motorcycle.id)" class="text-gray-400 text-sm hover:text-white flex items-center gap-1">
                        {{ $t('maintenance.back_to_tasks') }}
                    </Link>
                    <h1 class="text-2xl font-bold text-white mt-1">{{ $t('maintenance.title') }}</h1>
                    <p class="text-brand-muted text-sm">{{ motorcycle.brand }} {{ motorcycle.model }}</p>
                </div>
                
                <div class="text-right">
                    <p class="text-xs text-gray-500 uppercase">{{ $t('maintenance.total_records') }}</p>
                    <p class="text-xl font-mono text-white">{{ history.length }}</p>
                </div>
            </div>

            <div v-if="history.length === 0" class="text-center py-12 text-gray-500 bg-brand-surface rounded-xl border border-brand-dark border-dashed">
                <p class="text-lg">{{ $t('maintenance.no_history') }}</p>
                <p class="text-sm mt-2">{{ $t('maintenance.no_history_hint') }}</p>
            </div>

            <div v-else class="space-y-4">
                <div v-for="log in history" :key="log.id" class="bg-brand-surface border border-brand-dark rounded-xl p-4 flex flex-col gap-3 shadow-lg relative overflow-hidden">
                    
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-white text-lg">
                                {{ log.task_title }}
                            </h3>
                            <p class="text-brand-neon text-sm font-mono">{{ log.date }}</p>
                        </div>
                        <div class="text-right">
                            <span class="block text-xl font-bold text-white">{{ log.cost }} €</span>
                        </div>
                    </div>

                    <div class="bg-brand-black/50 p-3 rounded-lg border border-brand-dark/50 text-sm flex justify-between items-center">
                        <span class="text-gray-300 italic">"{{ log.description }}"</span>
                        <span class="text-gray-500 font-mono text-xs">{{ $t('maintenance.done_at') }} {{ log.km_at_moment }} km</span>
                    </div>

                    <div v-if="log.invoice_photo" class="mt-2">
                        <a :href="$page.props.storageUrl + '/' + log.invoice_photo" target="_blank">
                            <img :src="$page.props.storageUrl + '/' + log.invoice_photo" alt="Foto factura" class="h-28 w-full object-cover rounded-lg border border-brand-dark hover:opacity-80 transition cursor-pointer">
                        </a>
                        <p class="text-[10px] text-gray-600 mt-1">{{ $t('maintenance.invoice_hint') }}</p>
                    </div>

                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-brand-dark"></div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    motorcycle: Object,
    history: Array
});
</script>

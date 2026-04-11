<template>
    <AppLayout :current-moto-id="motorcycle.id">
        <div class="w-full max-w-full min-w-0 overflow-x-hidden box-border px-4 py-6 pb-24">
            
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between w-full min-w-0">
                <div class="flex items-start gap-3 min-w-0 flex-1">
                    <button type="button" @click="goBack" class="inline-flex items-center justify-center w-10 h-10 flex-shrink-0 rounded-full bg-brand-dark border border-brand-neon/50 text-brand-neon hover:bg-brand-neon hover:text-brand-black transition shadow-[0_0_10px_rgba(12,225,181,0.2)]" aria-label="Enrere">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                    </button>
                    <div class="min-w-0 flex-1 pt-0.5">
                        <h1 class="text-2xl font-bold text-white break-words">{{ $t('maintenance.title') }}</h1>
                        <p class="text-brand-muted text-sm truncate">{{ motorcycle.brand }} {{ motorcycle.model }}</p>
                    </div>
                </div>
                <div class="text-left sm:text-right flex-shrink-0">
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
import AppLayout from '@/Layouts/AppLayout.vue';
import { smartBack } from '@/Composables/navigationStack.js';

const props = defineProps({
    motorcycle: Object,
    history: Array
});

const goBack = () => smartBack(route('motorcycles.maintenance.index', props.motorcycle.id));
</script>

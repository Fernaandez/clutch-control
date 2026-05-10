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
            </div>

            <div v-else class="space-y-4">
                <div v-for="log in history" :key="log.id" class="bg-brand-surface border border-brand-dark rounded-xl p-4 flex flex-col gap-3 shadow-lg relative overflow-hidden">
                    
                    <div class="flex justify-between items-start gap-3">
                        <div>
                            <h3 class="font-bold text-white text-lg">
                                {{ log.task_title }}
                            </h3>
                            <p class="text-brand-neon text-sm font-mono">{{ log.date }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="block text-xl font-bold text-white">{{ log.cost }} €</span>
                            <button @click="openShowModal(log)" class="mt-2 inline-flex items-center justify-center w-8 h-8 rounded-full bg-brand-dark border border-brand-neon/40 text-brand-neon hover:bg-brand-neon hover:text-brand-black transition" title="Veure">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                            </button>
                        </div>
                    </div>

                    <div class="bg-brand-black/50 p-3 rounded-lg border border-brand-dark/50 text-sm flex justify-between items-center">
                        <span class="text-gray-300 italic">"{{ log.location || '—' }}"</span>
                        <span class="text-gray-500 font-mono text-xs">{{ $t('maintenance.done_at') }} {{ log.km_at_moment }} km</span>
                    </div>

                    <div v-if="log.invoice_photo" class="mt-2">
                        <button type="button" @click="openPhoto(log.invoice_photo)" class="block w-full text-left">
                            <img :src="$page.props.storageUrl + '/' + log.invoice_photo" alt="Foto factura" class="h-28 w-full object-cover rounded-lg border border-brand-dark hover:opacity-80 transition cursor-pointer">
                        </button>
                        <p class="text-[10px] text-gray-600 mt-1">{{ $t('maintenance.invoice_hint') }}</p>
                    </div>

                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-brand-dark"></div>
                </div>
            </div>

        </div>

        <div v-if="selectedLog" class="fixed inset-0 z-[4000] flex items-center justify-center p-4">
            <div @click="selectedLog = null" class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>
            <div class="relative bg-brand-surface border border-brand-neon/30 rounded-xl p-6 w-full max-w-sm max-h-[min(90vh,36rem)] overflow-y-auto overscroll-contain shadow-[0_0_20px_rgba(12,225,181,0.15)]">
                <button @click="selectedLog = null" class="absolute top-4 right-4 inline-flex items-center justify-center w-8 h-8 rounded-full bg-brand-dark border border-brand-neon/50 text-brand-neon hover:bg-brand-neon hover:text-brand-black transition" aria-label="Tancar">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                </button>
                <h3 class="text-xl font-bold text-white mb-1 pr-10">{{ selectedLog.task_title }}</h3>
                <p class="text-xs text-brand-muted uppercase tracking-widest mb-4">{{ $t('maintenance.title') }}</p>

                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="bg-brand-black/60 rounded-lg p-3 border border-brand-dark">
                            <p class="text-xs text-gray-500 mb-1">{{ $t('common.date') }}</p>
                            <p class="text-white font-mono text-sm">{{ selectedLog.date }}</p>
                        </div>
                        <div class="bg-brand-black/60 rounded-lg p-3 border border-brand-dark">
                            <p class="text-xs text-gray-500 mb-1">{{ $t('common.price') }}</p>
                            <p class="text-white font-mono text-sm">{{ selectedLog.cost }} €</p>
                        </div>
                    </div>
                    <div class="bg-brand-black/60 rounded-lg p-3 border border-brand-dark">
                        <p class="text-xs text-gray-500 mb-1">{{ $t('maintenance.done_at') }}</p>
                        <p class="text-white font-mono text-sm">{{ selectedLog.km_at_moment }} km</p>
                    </div>
                    <div class="bg-brand-black/60 rounded-lg p-3 border border-brand-dark">
                        <p class="text-xs text-gray-500 mb-1">{{ $t('maintenance.workshop') }}</p>
                        <p class="text-gray-200 text-sm">{{ selectedLog.location || '—' }}</p>
                    </div>
                    <button v-if="selectedLog.invoice_photo" type="button" @click="openPhoto(selectedLog.invoice_photo)" class="block w-full">
                        <img :src="$page.props.storageUrl + '/' + selectedLog.invoice_photo" alt="Foto factura" class="max-h-56 w-full object-contain rounded-lg border border-brand-dark bg-brand-black">
                    </button>
                </div>
            </div>
        </div>

        <div v-if="selectedPhoto" class="fixed inset-0 z-[4010] flex items-center justify-center p-4 bg-black/95">
            <button @click="selectedPhoto = null" class="absolute top-4 right-4 inline-flex items-center justify-center w-10 h-10 rounded-full bg-brand-dark border border-brand-neon/50 text-brand-neon hover:bg-brand-neon hover:text-brand-black transition" aria-label="Tancar">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
            </button>
            <img :src="$page.props.storageUrl + '/' + selectedPhoto" alt="Foto factura" class="max-h-[85vh] max-w-full object-contain rounded-lg border border-brand-dark">
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { smartBack } from '@/Composables/navigationStack.js';

const props = defineProps({
    motorcycle: Object,
    history: Array
});

const goBack = () => smartBack(route('motorcycles.maintenance.index', props.motorcycle.id));
const selectedLog = ref(null);
const selectedPhoto = ref(null);

const openShowModal = (log) => {
    selectedLog.value = log;
};

const openPhoto = (photo) => {
    selectedPhoto.value = photo;
};
</script>

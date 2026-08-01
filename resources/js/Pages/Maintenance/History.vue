<template>
    <AppLayout :current-moto-id="motorcycle.id">
        <div class="max-w-xl mx-auto px-6 py-6 pb-24 cc-fade-in">

            <header class="flex items-center gap-3 mb-6">
                <button type="button" @click="goBack" class="cc-icon-btn" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </button>
                <h1 class="cc-title flex-1 truncate">{{ $t('maintenance.history') }}</h1>
            </header>

            <p v-if="motorcycle.brand" class="text-sm text-gray-500 mb-8 truncate">{{ motorcycle.brand }} {{ motorcycle.model }}</p>

            <div v-if="history.length" class="flex gap-12 mb-12">
                <div>
                    <p class="text-[40px] font-light tracking-tight tabular-nums text-white leading-none">{{ history.length }}</p>
                    <p class="mt-2 text-sm text-gray-500">{{ $t('maintenance.total_records') }}</p>
                </div>
                <div>
                    <p class="text-[40px] font-light tracking-tight tabular-nums text-white leading-none">
                        {{ history.reduce((acc, log) => acc + parseFloat(log.cost || 0), 0).toFixed(2) }}<span class="text-base text-gray-500 ml-1">€</span>
                    </p>
                    <p class="mt-2 text-sm text-gray-500">{{ $t('maintenance.total_filtered') }}</p>
                </div>
            </div>

            <div v-if="history.length" class="divide-y divide-white/[0.06]">
                <div v-for="log in history" :key="log.id" class="py-5">
                    <button type="button" @click="openShowModal(log)" class="w-full text-left">
                        <p class="text-[15px] font-medium text-gray-100">{{ log.task_title }}</p>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ log.date }} · {{ log.km_at_moment }} km · {{ log.cost }} €
                        </p>
                    </button>
                    <div v-if="log.invoice_photo" class="mt-3 pl-[18px]">
                        <button type="button" @click="openPhoto(log.invoice_photo)" class="cc-btn-text">
                            {{ $t('maintenance.invoice_hint') }}
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="flex flex-col items-center justify-center text-center py-16 px-6">
                <p class="text-base font-semibold text-gray-300">{{ $t('maintenance.no_history') }}</p>
                <p v-if="$t('maintenance.no_history_hint')" class="mt-1 text-sm text-gray-500 max-w-xs">{{ $t('maintenance.no_history_hint') }}</p>
            </div>
        </div>

        <div v-if="selectedLog" class="fixed inset-0 z-[4000] flex items-center justify-center p-4">
            <div @click="selectedLog = null" class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
            <div class="relative bg-brand-surface border border-white/[0.08] rounded-2xl p-6 max-w-sm w-full max-h-[min(90vh,36rem)] overflow-y-auto overscroll-contain">
                <button type="button" @click="selectedLog = null" class="cc-icon-btn absolute top-4 right-4" aria-label="Tancar">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
                <h3 class="text-lg font-medium text-white mb-1 pr-10">{{ selectedLog.task_title }}</h3>
                <p class="text-sm text-gray-500 mb-6">{{ $t('maintenance.title') }}</p>
                <div class="space-y-4">
                    <div>
                        <p class="cc-section-label">{{ $t('common.date') }}</p>
                        <p class="text-white tabular-nums mt-1">{{ selectedLog.date }}</p>
                    </div>
                    <div>
                        <p class="cc-section-label">{{ $t('common.price') }}</p>
                        <p class="text-white tabular-nums mt-1">{{ selectedLog.cost }} €</p>
                    </div>
                    <div>
                        <p class="cc-section-label">{{ $t('maintenance.done_at') }}</p>
                        <p class="text-white tabular-nums mt-1">{{ selectedLog.km_at_moment }} km</p>
                    </div>
                    <div v-if="selectedLog.location">
                        <p class="cc-section-label">{{ $t('maintenance.workshop') }}</p>
                        <p class="text-gray-300 text-sm mt-1">{{ selectedLog.location }}</p>
                    </div>
                    <button v-if="selectedLog.invoice_photo" type="button" @click="openPhoto(selectedLog.invoice_photo)" class="block w-full text-left">
                        <p class="cc-section-label">{{ $t('maintenance.invoice_photo') }}</p>
                        <img
                            :src="$page.props.storageUrl + '/' + selectedLog.invoice_photo"
                            alt=""
                            class="mt-2 max-h-56 w-full object-contain rounded-lg"
                            @error="($event.target).style.display = 'none'"
                        >
                    </button>
                </div>
            </div>
        </div>

        <div v-if="selectedPhoto" class="fixed inset-0 z-[4010] flex items-center justify-center p-4">
            <div @click="selectedPhoto = null" class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
            <button type="button" @click="selectedPhoto = null" class="cc-icon-btn absolute top-4 right-4 z-10" aria-label="Tancar">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
            <img
                :src="$page.props.storageUrl + '/' + selectedPhoto"
                alt=""
                class="relative max-h-[85vh] max-w-full object-contain rounded-lg"
                @error="($event.target).style.display = 'none'"
            >
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

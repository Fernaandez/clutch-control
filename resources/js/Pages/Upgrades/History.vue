<template>
    <AppLayout :current-moto-id="motorcycle.id">
        <div class="w-full max-w-full min-w-0 overflow-x-hidden box-border px-4 py-6 pb-24">
            <div class="mb-6 w-full min-w-0">
                <button type="button" @click="goBack" class="text-gray-400 text-sm hover:text-white flex items-center gap-1 w-fit">
                    {{ $t('upgrades_history.back') }}
                </button>
                <h1 class="text-2xl font-bold text-purple-400 mt-1 break-words">{{ $t('upgrades_history.title') }}</h1>
                <p class="text-brand-muted text-sm truncate">{{ motorcycle.brand }} {{ motorcycle.model }}</p>
            </div>

            <div v-if="history.length === 0" class="text-center py-10 text-gray-500 bg-brand-surface rounded-xl border border-brand-dark border-dashed">
                <p>{{ $t('upgrades_history.no_history') }}</p>
                <p class="text-sm">{{ $t('upgrades_history.no_history_hint') }}</p>
            </div>

            <div v-else class="space-y-4">
                <div v-for="log in history" :key="log.id" class="bg-brand-surface rounded-xl p-4 border-l-4 border-purple-500/50 shadow-lg relative">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs text-purple-400 font-bold uppercase tracking-wider mb-1">{{ formatDate(log.date) }}</p>
                            <h3 class="text-lg font-bold text-white">{{ log.task_title }}</h3>
                            <p class="text-sm text-gray-400 mt-1">{{ log.description }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-bold text-white">{{ log.cost }} €</p>
                            <p class="text-xs text-gray-500">{{ $t('upgrades_history.at_km', { n: log.km_at_moment }) }}</p>
                        </div>
                    </div>
                    <div v-if="log.invoice_photo" class="mt-3">
                        <a :href="$page.props.storageUrl + '/' + log.invoice_photo" target="_blank">
                            <img :src="$page.props.storageUrl + '/' + log.invoice_photo" alt="Foto mod" class="h-28 w-full object-cover rounded-lg border border-purple-900/30 hover:opacity-80 transition cursor-pointer">
                        </a>
                        <p class="text-[10px] text-gray-600 mt-1">{{ $t('maintenance.invoice_hint') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import { smartBack } from '@/Composables/navigationStack.js';

const { locale } = useI18n();

const props = defineProps({ motorcycle: Object, history: Array });

const goBack = () => smartBack(route('motorcycles.upgrades.index', props.motorcycle.id));

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(locale.value + '-ES', options);
};
</script>

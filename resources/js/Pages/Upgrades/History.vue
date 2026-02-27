<template>
    <AppLayout :current-moto-id="motorcycle.id">
        <div class="px-4 py-6 pb-24">
            
            <div class="flex items-center justify-between mb-6">
                <div>
                    <Link :href="route('motorcycles.upgrades.index', motorcycle.id)" class="text-gray-400 text-sm hover:text-white flex items-center gap-1">
                        &larr; Tornar
                    </Link>
                    <h1 class="text-2xl font-bold text-purple-400 mt-1">Historial Millores</h1>
                    <p class="text-brand-muted text-sm">{{ motorcycle.brand }} {{ motorcycle.model }}</p>
                </div>
            </div>

            <div v-if="history.length === 0" class="text-center py-10 text-gray-500 bg-brand-surface rounded-xl border border-brand-dark border-dashed">
                <p>No hi ha millores registrades.</p>
                <p class="text-sm">La moto està de sèrie.</p>
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
                            <p class="text-xs text-gray-500">als {{ log.km_at_moment }} km</p>
                        </div>
                    </div>

                    <div v-if="log.invoice_photo" class="mt-3">
                        <a :href="$page.props.storageUrl + '/' + log.invoice_photo" target="_blank">
                            <img :src="$page.props.storageUrl + '/' + log.invoice_photo" alt="Foto mod" class="h-28 w-full object-cover rounded-lg border border-purple-900/30 hover:opacity-80 transition cursor-pointer">
                        </a>
                        <p class="text-[10px] text-gray-600 mt-1">📎 Foto del mod — clica per veure-la gran</p>
                    </div>

                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    motorcycle: Object,
    history: Array
});

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('ca-ES', options);
};
</script>
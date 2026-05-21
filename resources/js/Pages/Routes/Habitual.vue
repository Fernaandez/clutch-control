<template>
    <AppLayout :title="$t('routes.habitual_title')">
        <div class="max-w-2xl mx-auto px-4 py-6 pb-24">
            <div class="flex items-center gap-3 mb-6">
                <button type="button" @click="goBack" class="inline-flex items-center justify-center w-10 h-10 flex-shrink-0 rounded-full bg-brand-dark border border-brand-neon/50 text-brand-neon hover:bg-brand-neon hover:text-brand-black transition shadow-[0_0_10px_rgba(12,225,181,0.2)]" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                </button>
                <div>
                    <h1 class="text-2xl font-black uppercase tracking-tighter text-white leading-none">{{ $t('routes.habitual_title') }}</h1>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">{{ $t('routes.habitual_subtitle') }}</p>
                </div>
            </div>

            <div v-if="motorcycles.length === 0" class="bg-brand-surface border border-brand-dark border-dashed rounded-2xl p-8 text-center mb-6">
                <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">{{ $t('routes.habitual_no_moto') }}</p>
                <Link :href="route('motorcycles.create')" class="inline-block mt-4 bg-brand-neon text-black font-black uppercase tracking-wider text-xs px-4 py-2 rounded-lg hover:bg-white transition">
                    {{ $t('motorcycles.add_title') }}
                </Link>
            </div>

            <div v-else class="space-y-6">
                <!-- Aplicar ruta guardada -->
                <div class="bg-brand-surface p-5 rounded-2xl border border-brand-dark shadow-lg">
                    <h2 class="text-white font-black uppercase tracking-widest text-sm mb-1">{{ $t('routes.habitual_apply_route') }}</h2>
                    <p class="text-xs text-gray-500 mb-4">{{ $t('routes.habitual_apply_route_desc') }}</p>

                    <form @submit.prevent="submitRoute" class="space-y-4">
                        <div v-if="Object.keys(routeForm.errors).length" class="p-3 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-xs">
                            <p v-for="(err, key) in routeForm.errors" :key="key">{{ err }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('routes.habitual_select_route') }}</label>
                            <select v-model="routeForm.route_id" required :class="inputClass">
                                <option value="">{{ $t('routes.habitual_select_route_placeholder') }}</option>
                                <option v-for="r in routes" :key="r.id" :value="r.id">
                                    {{ r.title }}<template v-if="r.planned_distance_km"> — {{ r.planned_distance_km }} km</template>
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('routes.habitual_motorcycle') }}</label>
                            <select v-model="routeForm.motorcycle_id" required :class="inputClass">
                                <option value="">{{ $t('routes.habitual_select_moto') }}</option>
                                <option v-for="m in motorcycles" :key="m.id" :value="m.id">
                                    {{ m.alias || `${m.brand} ${m.model}` }} ({{ m.current_km ?? 0 }} km)
                                </option>
                            </select>
                        </div>

                        <label class="flex items-center gap-3 cursor-pointer">
                            <input v-model="routeForm.round_trip" type="checkbox" class="rounded border-brand-dark bg-brand-black text-brand-neon focus:ring-brand-neon">
                            <span class="text-sm text-gray-300">{{ $t('routes.habitual_round_trip') }}</span>
                            <span v-if="estimatedRouteKm" class="text-brand-neon font-mono text-xs ml-auto">{{ estimatedRouteKm }} km</span>
                        </label>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('routes.habitual_date') }}</label>
                            <input v-model="routeForm.started_at" type="datetime-local" required :class="inputClass">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('routes.habitual_notes') }}</label>
                            <input v-model="routeForm.notes" type="text" maxlength="500" :placeholder="$t('routes.habitual_notes_placeholder')" :class="inputClass">
                        </div>

                        <button type="submit" :disabled="routeForm.processing || !routeForm.route_id" class="w-full bg-brand-neon text-black font-black uppercase tracking-widest text-xs py-3 rounded-xl hover:bg-white transition disabled:opacity-50">
                            {{ $t('routes.habitual_apply_btn') }}
                        </button>
                    </form>
                </div>

                <!-- Entrada manual de km -->
                <div class="bg-brand-surface p-5 rounded-2xl border border-brand-dark shadow-lg">
                    <h2 class="text-white font-black uppercase tracking-widest text-sm mb-1">{{ $t('routes.habitual_manual') }}</h2>
                    <p class="text-xs text-gray-500 mb-4">{{ $t('routes.habitual_manual_desc') }}</p>

                    <form @submit.prevent="submitManual" class="space-y-4">
                        <div v-if="Object.keys(manualForm.errors).length" class="p-3 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-xs">
                            <p v-for="(err, key) in manualForm.errors" :key="key">{{ err }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('routes.habitual_motorcycle') }}</label>
                            <select v-model="manualForm.motorcycle_id" required :class="inputClass">
                                <option value="">{{ $t('routes.habitual_select_moto') }}</option>
                                <option v-for="m in motorcycles" :key="m.id" :value="m.id">
                                    {{ m.alias || `${m.brand} ${m.model}` }} ({{ m.current_km ?? 0 }} km)
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('routes.habitual_km') }}</label>
                            <input v-model="manualForm.distance_km" type="number" step="0.1" min="0.1" required :class="inputClass" placeholder="12.5">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('routes.habitual_date') }}</label>
                            <input v-model="manualForm.started_at" type="datetime-local" required :class="inputClass">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('routes.habitual_notes') }}</label>
                            <input v-model="manualForm.notes" type="text" maxlength="500" :placeholder="$t('routes.habitual_notes_placeholder')" :class="inputClass">
                        </div>

                        <button type="submit" :disabled="manualForm.processing" class="w-full bg-brand-dark text-brand-neon border border-brand-neon font-black uppercase tracking-widest text-xs py-3 rounded-xl hover:bg-brand-neon hover:text-black transition disabled:opacity-50">
                            {{ $t('routes.habitual_manual_btn') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { smartBack } from '@/Composables/navigationStack.js';

const props = defineProps({
    motorcycles: { type: Array, default: () => [] },
    routes: { type: Array, default: () => [] },
    preselectedRouteId: { type: Number, default: null },
});

const goBack = () => smartBack(route('routes.index'));

const defaultDatetime = () => {
    const d = new Date();
    d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
    return d.toISOString().slice(0, 16);
};

const inputClass = 'w-full bg-brand-black border border-brand-dark rounded-xl px-4 py-3 text-white text-sm focus:border-brand-neon focus:ring-1 focus:ring-brand-neon outline-none transition';

const routeForm = useForm({
    route_id: props.preselectedRouteId ?? '',
    motorcycle_id: props.motorcycles[0]?.id ?? '',
    round_trip: false,
    started_at: defaultDatetime(),
    notes: '',
});

const manualForm = useForm({
    motorcycle_id: props.motorcycles[0]?.id ?? '',
    distance_km: '',
    started_at: defaultDatetime(),
    notes: '',
});

const selectedRoute = computed(() =>
    props.routes.find(r => Number(r.id) === Number(routeForm.route_id))
);

const estimatedRouteKm = computed(() => {
    const base = parseFloat(selectedRoute.value?.planned_distance_km);
    if (!base || base <= 0) return null;
    const total = routeForm.round_trip ? base * 2 : base;
    return total.toFixed(1);
});

const submitRoute = () => {
    if (!routeForm.route_id) return;
    routeForm
        .transform((data) => ({
            motorcycle_id: data.motorcycle_id,
            round_trip: data.round_trip,
            started_at: data.started_at,
            notes: data.notes || null,
        }))
        .post(route('routes.apply-to-motorcycle', routeForm.route_id), {
            preserveScroll: true,
            onSuccess: () => routeForm.reset('notes'),
        });
};

const submitManual = () => {
    manualForm.post(route('trips.store-manual'), {
        preserveScroll: true,
        onSuccess: () => manualForm.reset('distance_km', 'notes'),
    });
};
</script>

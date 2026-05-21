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

            <div v-if="page.props.errors?.habitual" class="mb-4 p-4 bg-red-500/10 border border-red-500/30 rounded-xl">
                <p class="text-red-400 text-sm">{{ page.props.errors.habitual }}</p>
            </div>

            <div v-if="flashDone" class="mb-4 p-4 bg-brand-neon/10 border border-brand-neon/40 rounded-xl">
                <p class="text-brand-neon font-bold text-sm">{{ $t('routes.habitual_done_flash', { title: flashDone.title, km: flashDone.km }) }}</p>
            </div>

            <div v-if="motorcycles.length === 0" class="bg-brand-surface border border-brand-dark border-dashed rounded-2xl p-8 text-center mb-6">
                <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">{{ $t('routes.habitual_no_moto') }}</p>
                <Link :href="route('motorcycles.create')" class="inline-block mt-4 bg-brand-neon text-black font-black uppercase tracking-wider text-xs px-4 py-2 rounded-lg hover:bg-white transition">
                    {{ $t('motorcycles.add_title') }}
                </Link>
            </div>

            <div v-else class="space-y-6">
                <!-- Llistat: botó Feta -->
                <div class="bg-brand-surface p-5 rounded-2xl border border-brand-dark shadow-lg">
                    <h2 class="text-white font-black uppercase tracking-widest text-sm mb-1">{{ $t('routes.habitual_list_title') }}</h2>
                    <p class="text-xs text-gray-500 mb-4">{{ $t('routes.habitual_list_desc') }}</p>

                    <div v-if="habitualRoutes.length === 0" class="border border-brand-dark border-dashed rounded-xl p-6 text-center">
                        <p class="text-gray-500 text-xs uppercase tracking-widest font-bold">{{ $t('routes.habitual_list_empty') }}</p>
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="item in habitualRoutes"
                            :key="item.id"
                            class="flex items-center gap-3 bg-brand-black/50 border border-brand-dark rounded-xl p-3"
                        >
                            <div class="flex-1 min-w-0">
                                <p class="text-white font-bold text-sm truncate">{{ item.title }}</p>
                                <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-0.5 truncate">
                                    <span class="text-brand-neon font-mono">{{ item.distance_km ?? '?' }} km</span>
                                    <span v-if="item.round_trip" class="ml-2">{{ $t('routes.habitual_round_trip_short') }}</span>
                                    <span v-if="item.motorcycle" class="ml-2">· {{ item.motorcycle.brand }} {{ item.motorcycle.model }}</span>
                                </p>
                            </div>
                            <button
                                type="button"
                                :disabled="completingId === item.id || !item.distance_km"
                                @click="markDone(item)"
                                class="flex-shrink-0 bg-brand-neon text-black font-black uppercase tracking-widest text-[10px] px-4 py-2.5 rounded-xl hover:bg-white transition disabled:opacity-50"
                            >
                                {{ completingId === item.id ? '…' : $t('routes.habitual_done_btn') }}
                            </button>
                            <button
                                type="button"
                                @click="removeItem(item)"
                                class="flex-shrink-0 p-2 text-gray-600 hover:text-red-400 transition"
                                :aria-label="$t('common.delete')"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Afegir ruta habitual -->
                <div class="bg-brand-surface p-5 rounded-2xl border border-brand-dark shadow-lg">
                    <h2 class="text-white font-black uppercase tracking-widest text-sm mb-1">{{ $t('routes.habitual_add_title') }}</h2>
                    <p class="text-xs text-gray-500 mb-4">{{ $t('routes.habitual_add_desc') }}</p>

                    <form @submit.prevent="submitAdd" class="space-y-4">
                        <div v-if="Object.keys(addForm.errors).length" class="p-3 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-xs">
                            <p v-for="(err, key) in addForm.errors" :key="key">{{ err }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('routes.habitual_select_route') }}</label>
                            <select v-model="addForm.route_id" required :class="inputClass">
                                <option value="">{{ $t('routes.habitual_select_route_placeholder') }}</option>
                                <option v-for="r in routes" :key="r.id" :value="r.id">
                                    {{ r.title }}<template v-if="r.planned_distance_km"> — {{ r.planned_distance_km }} km</template>
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('routes.habitual_label') }}</label>
                            <input v-model="addForm.label" type="text" maxlength="120" :placeholder="$t('routes.habitual_label_placeholder')" :class="inputClass">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('routes.habitual_motorcycle') }}</label>
                            <select v-model="addForm.motorcycle_id" required :class="inputClass">
                                <option value="">{{ $t('routes.habitual_select_moto') }}</option>
                                <option v-for="m in motorcycles" :key="m.id" :value="m.id">
                                    {{ `${m.brand} ${m.model}` }} ({{ m.current_km ?? 0 }} km)
                                </option>
                            </select>
                        </div>

                        <label class="flex items-center gap-3 cursor-pointer">
                            <input v-model="addForm.round_trip" type="checkbox" class="rounded border-brand-dark bg-brand-black text-brand-neon focus:ring-brand-neon">
                            <span class="text-sm text-gray-300">{{ $t('routes.habitual_round_trip') }}</span>
                            <span v-if="estimatedAddKm" class="text-brand-neon font-mono text-xs ml-auto">{{ estimatedAddKm }} km</span>
                        </label>

                        <button type="submit" :disabled="addForm.processing || !addForm.route_id" class="w-full bg-brand-dark text-brand-neon border border-brand-neon font-black uppercase tracking-widest text-xs py-3 rounded-xl hover:bg-brand-neon hover:text-black transition disabled:opacity-50">
                            {{ $t('routes.habitual_add_btn') }}
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
                                    {{ `${m.brand} ${m.model}` }} ({{ m.current_km ?? 0 }} km)
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

                        <button type="submit" :disabled="manualForm.processing" class="w-full bg-brand-dark/80 text-gray-300 border border-brand-dark font-black uppercase tracking-widest text-xs py-3 rounded-xl hover:border-brand-neon hover:text-brand-neon transition disabled:opacity-50">
                            {{ $t('routes.habitual_manual_btn') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { smartBack } from '@/Composables/navigationStack.js';

const { t } = useI18n();

const props = defineProps({
    motorcycles: { type: Array, default: () => [] },
    routes: { type: Array, default: () => [] },
    habitualRoutes: { type: Array, default: () => [] },
    preselectedRouteId: { type: Number, default: null },
});

const page = usePage();
const completingId = ref(null);
const flashDone = computed(() => {
    const title = page.props.flash?.habitual_done_title;
    const km = page.props.flash?.habitual_done_km;
    if (!title || km == null) return null;
    return { title, km };
});

const goBack = () => smartBack(route('routes.index'));

const defaultDatetime = () => {
    const d = new Date();
    d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
    return d.toISOString().slice(0, 16);
};

const inputClass = 'w-full bg-brand-black border border-brand-dark rounded-xl px-4 py-3 text-white text-sm focus:border-brand-neon focus:ring-1 focus:ring-brand-neon outline-none transition';

const addForm = useForm({
    route_id: props.preselectedRouteId ?? '',
    motorcycle_id: props.motorcycles[0]?.id ?? '',
    round_trip: false,
    label: '',
});

const manualForm = useForm({
    motorcycle_id: props.motorcycles[0]?.id ?? '',
    distance_km: '',
    started_at: defaultDatetime(),
    notes: '',
});

const selectedAddRoute = computed(() =>
    props.routes.find(r => Number(r.id) === Number(addForm.route_id))
);

const estimatedAddKm = computed(() => {
    const base = parseFloat(selectedAddRoute.value?.planned_distance_km);
    if (!base || base <= 0) return null;
    const total = addForm.round_trip ? base * 2 : base;
    return total.toFixed(1);
});

const submitAdd = () => {
    if (!addForm.route_id) return;
    addForm.post(route('habitual-routes.store'), {
        preserveScroll: true,
        onSuccess: () => addForm.reset('label'),
    });
};

const markDone = (item) => {
    completingId.value = item.id;
    router.post(route('habitual-routes.complete', item.id), {}, {
        preserveScroll: true,
        onFinish: () => { completingId.value = null; },
        onError: (errors) => {
            console.error('habitual complete failed', errors);
        },
    });
};

const removeItem = (item) => {
    if (!confirm(t('routes.habitual_remove_confirm', { title: item.title }))) return;
    router.delete(route('habitual-routes.destroy', item.id), { preserveScroll: true });
};

const submitManual = () => {
    manualForm.post(route('trips.store-manual'), {
        preserveScroll: true,
        onSuccess: () => manualForm.reset('distance_km', 'notes'),
    });
};
</script>

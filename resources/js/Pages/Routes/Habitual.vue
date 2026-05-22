<template>
    <AppLayout :title="$t('routes.habitual_title')">
        <div class="max-w-2xl mx-auto px-4 py-6 pb-24">
            <div class="flex items-center gap-3 mb-6">
                <button type="button" @click="goBack" class="inline-flex items-center justify-center w-10 h-10 flex-shrink-0 rounded-full bg-brand-dark border border-brand-neon/50 text-brand-neon hover:bg-brand-neon hover:text-brand-black transition shadow-[0_0_10px_rgba(12,225,181,0.2)]" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                </button>
                <h1 class="text-2xl font-black uppercase tracking-tighter text-white leading-none">{{ $t('routes.habitual_title') }}</h1>
            </div>

            <div v-if="page.props.errors?.habitual" class="mb-4 p-4 bg-red-500/10 border border-red-500/30 rounded-xl">
                <p class="text-red-400 text-sm">{{ page.props.errors.habitual }}</p>
            </div>

            <div v-if="flashDone" class="mb-4 p-4 bg-brand-neon/10 border border-brand-neon/40 rounded-xl flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-brand-neon/20 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-brand-neon"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                </div>
                <p class="text-brand-neon font-bold text-sm">{{ $t('routes.habitual_done_flash', { title: flashDone.title, km: flashDone.km }) }}</p>
            </div>

            <div v-if="motorcycles.length === 0" class="bg-brand-surface border border-brand-dark border-dashed rounded-2xl p-10 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-brand-dark flex items-center justify-center text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177A48.016 48.016 0 0 0 6 6.094V6a3 3 0 0 1 3-3h2.25" /></svg>
                </div>
                <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">{{ $t('routes.habitual_no_moto') }}</p>
                <Link :href="route('motorcycles.create')" class="inline-block mt-5 bg-brand-neon text-black font-black uppercase tracking-wider text-xs px-5 py-2.5 rounded-xl hover:bg-white transition">
                    {{ $t('motorcycles.add_title') }}
                </Link>
            </div>

            <div v-else class="space-y-6">
                <!-- Llistat principal -->
                <section>
                    <div v-if="habitualRoutes.length === 0" class="bg-brand-surface border border-brand-dark border-dashed rounded-2xl p-10 text-center">
                        <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-brand-neon/10 border border-brand-neon/20 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-brand-neon"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" /></svg>
                        </div>
                        <p class="text-gray-500 text-xs uppercase tracking-widest font-bold mb-4">{{ $t('routes.habitual_list_empty') }}</p>
                        <button type="button" @click="activePanel = 'add'" class="bg-brand-neon text-black font-black uppercase tracking-widest text-[10px] px-5 py-2.5 rounded-xl hover:bg-white transition">
                            {{ $t('routes.habitual_empty_cta') }}
                        </button>
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="item in habitualRoutes"
                            :key="item.id"
                            class="relative overflow-hidden rounded-2xl border border-brand-dark bg-brand-surface"
                        >
                            <div class="absolute -right-6 -top-6 w-28 h-28 bg-brand-neon/5 blur-2xl rounded-full pointer-events-none"></div>

                            <button
                                type="button"
                                @click="removeItem(item)"
                                class="absolute top-2.5 right-2.5 z-10 p-1.5 text-gray-600 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition"
                                :aria-label="$t('common.delete')"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                            </button>

                            <div class="p-4 pr-10 flex items-center gap-3">
                                <div class="flex flex-col items-center justify-center min-w-[4.5rem] bg-brand-black/70 rounded-xl px-3 py-2.5 border border-brand-dark">
                                    <span class="text-2xl font-mono font-black text-brand-neon leading-none">{{ displayKm(item) }}</span>
                                    <span class="text-[9px] text-gray-500 uppercase font-bold mt-0.5">km</span>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-bold text-sm leading-snug truncate pr-2">{{ item.title }}</p>
                                    <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                        <span
                                            v-if="item.motorcycle"
                                            class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-brand-dark text-gray-400 border border-brand-dark"
                                        >
                                            {{ item.motorcycle.brand }} {{ item.motorcycle.model }}
                                        </span>
                                        <span
                                            v-if="item.round_trip"
                                            class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-brand-neon/10 text-brand-neon border border-brand-neon/20"
                                        >
                                            {{ $t('routes.habitual_round_trip_short') }}
                                        </span>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    :disabled="completingId === item.id || !item.distance_km"
                                    @click="markDone(item)"
                                    class="flex-shrink-0 min-w-[4.5rem] bg-brand-neon text-black font-black uppercase tracking-widest text-[10px] px-4 py-3.5 rounded-xl hover:bg-white active:scale-95 transition disabled:opacity-50 shadow-[0_0_20px_rgba(12,225,181,0.25)]"
                                >
                                    {{ completingId === item.id ? '…' : $t('routes.habitual_done_btn') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Pestanyes: afegir / manual -->
                <section class="bg-brand-surface rounded-2xl border border-brand-dark overflow-hidden">
                    <div class="grid grid-cols-2 border-b border-brand-dark">
                        <button
                            type="button"
                            class="py-3.5 text-[10px] font-black uppercase tracking-widest transition"
                            :class="activePanel === 'add' ? 'bg-brand-neon text-black' : 'text-gray-500 hover:text-white hover:bg-brand-dark/40'"
                            @click="activePanel = 'add'"
                        >
                            {{ $t('routes.habitual_tab_add') }}
                        </button>
                        <button
                            type="button"
                            class="py-3.5 text-[10px] font-black uppercase tracking-widest transition"
                            :class="activePanel === 'manual' ? 'bg-brand-neon text-black' : 'text-gray-500 hover:text-white hover:bg-brand-dark/40'"
                            @click="activePanel = 'manual'"
                        >
                            {{ $t('routes.habitual_tab_manual') }}
                        </button>
                    </div>

                    <div v-show="activePanel === 'add'" class="p-5">
                        <form @submit.prevent="submitAdd" class="space-y-4">
                            <div v-if="Object.keys(addForm.errors).length" class="p-3 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-xs">
                                <p v-for="(err, key) in addForm.errors" :key="key">{{ err }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1.5">{{ $t('routes.habitual_select_route') }}</label>
                                <select v-model="addForm.route_id" required :class="inputClass">
                                    <option value="">{{ $t('routes.habitual_select_route_placeholder') }}</option>
                                    <option v-for="r in routes" :key="r.id" :value="r.id">
                                        {{ r.title }}<template v-if="r.planned_distance_km"> — {{ r.planned_distance_km }} km</template>
                                    </option>
                                </select>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1.5">{{ $t('routes.habitual_label') }}</label>
                                    <input v-model="addForm.label" type="text" maxlength="120" :placeholder="$t('routes.habitual_label_placeholder')" :class="inputClass">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1.5">{{ $t('routes.habitual_motorcycle') }}</label>
                                    <select v-model="addForm.motorcycle_id" required :class="inputClass">
                                        <option value="">{{ $t('routes.habitual_select_moto') }}</option>
                                        <option v-for="m in motorcycles" :key="m.id" :value="m.id">
                                            {{ `${m.brand} ${m.model}` }} ({{ m.current_km ?? 0 }} km)
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <label class="flex items-center gap-3 cursor-pointer bg-brand-black/50 border border-brand-dark rounded-xl px-4 py-3">
                                <input v-model="addForm.round_trip" type="checkbox" class="rounded border-brand-dark bg-brand-black text-brand-neon focus:ring-brand-neon">
                                <span class="text-sm text-gray-300">{{ $t('routes.habitual_round_trip') }}</span>
                                <span v-if="estimatedAddKm" class="text-brand-neon font-mono font-bold text-sm ml-auto">{{ estimatedAddKm }} km</span>
                            </label>

                            <button type="submit" :disabled="addForm.processing || !addForm.route_id" class="w-full bg-brand-neon text-black font-black uppercase tracking-widest text-xs py-3.5 rounded-xl hover:bg-white transition disabled:opacity-50">
                                {{ $t('routes.habitual_add_btn') }}
                            </button>
                        </form>
                    </div>

                    <div v-show="activePanel === 'manual'" class="p-5">
                        <form @submit.prevent="submitManual" class="space-y-4">
                            <div v-if="Object.keys(manualForm.errors).length" class="p-3 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-xs">
                                <p v-for="(err, key) in manualForm.errors" :key="key">{{ err }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2 sm:col-span-1">
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1.5">{{ $t('routes.habitual_motorcycle') }}</label>
                                    <select v-model="manualForm.motorcycle_id" required :class="inputClass">
                                        <option value="">{{ $t('routes.habitual_select_moto') }}</option>
                                        <option v-for="m in motorcycles" :key="m.id" :value="m.id">
                                            {{ `${m.brand} ${m.model}` }} ({{ m.current_km ?? 0 }} km)
                                        </option>
                                    </select>
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1.5">{{ $t('routes.habitual_km') }}</label>
                                    <input v-model="manualForm.distance_km" type="number" step="0.1" min="0.1" required :class="inputClass" placeholder="12.5">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1.5">{{ $t('routes.habitual_date') }}</label>
                                <input v-model="manualForm.started_at" type="datetime-local" required :class="inputClass">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1.5">{{ $t('routes.habitual_notes') }}</label>
                                <input v-model="manualForm.notes" type="text" maxlength="500" :placeholder="$t('routes.habitual_notes_placeholder')" :class="inputClass">
                            </div>

                            <button type="submit" :disabled="manualForm.processing" class="w-full bg-brand-dark text-gray-200 border border-brand-dark font-black uppercase tracking-widest text-xs py-3.5 rounded-xl hover:border-brand-neon hover:text-brand-neon transition disabled:opacity-50">
                                {{ $t('routes.habitual_manual_btn') }}
                            </button>
                        </form>
                    </div>
                </section>
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
const activePanel = ref('add');

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

const displayKm = (item) => {
    const km = parseFloat(item.distance_km);
    if (!km || km <= 0) return '?';
    return Number.isInteger(km) ? km : km.toFixed(1);
};

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

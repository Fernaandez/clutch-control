<template>
    <AppLayout :title="$t('routes.habitual_title')">
        <div class="max-w-3xl mx-auto px-4 py-6 pb-24 cc-fade-in">

            <header class="flex items-center gap-3 mb-6">
                <button type="button" @click="goBack" class="cc-icon-btn" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                </button>
                <h1 class="cc-title flex-1 truncate">{{ $t('routes.habitual_title') }}</h1>
            </header>

            <div v-if="page.props.errors?.habitual" class="mb-4 cc-card p-4 border-red-500/20 bg-red-500/[0.06]">
                <p class="text-red-400 text-sm">{{ page.props.errors.habitual }}</p>
            </div>

            <div v-if="flashDone" class="mb-4 cc-card p-4 bg-white/[0.04] border-white/[0.08] flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/[0.06] flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-gray-300"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                </div>
                <p class="text-gray-300 font-medium text-sm">{{ $t('routes.habitual_done_flash', { title: flashDone.title, km: flashDone.km }) }}</p>
            </div>

            <div v-if="motorcycles.length === 0" class="py-16 text-center">
                <p class="text-base font-semibold text-gray-300">{{ $t('routes.habitual_no_moto') }}</p>
                <Link :href="route('motorcycles.create')" class="cc-btn-primary mt-6 inline-flex px-6 py-2.5">
                    {{ $t('motorcycles.add_title') }}
                </Link>
            </div>

            <div v-else class="space-y-6">
                <!-- Pestanyes: afegir / manual -->
                <section>
                    <div class="flex items-center gap-5 border-b border-white/[0.06]">
                        <button
                            type="button"
                            class="relative -mb-px pb-3 text-[13px] font-medium transition-colors"
                            :class="activePanel === 'add' ? 'text-white' : 'text-gray-500 hover:text-gray-300'"
                            @click="activePanel = 'add'"
                        >
                            {{ $t('routes.habitual_tab_add') }}
                            <span v-if="activePanel === 'add'" class="absolute inset-x-0 -bottom-px h-px bg-white"></span>
                        </button>
                        <button
                            type="button"
                            class="relative -mb-px pb-3 text-[13px] font-medium transition-colors"
                            :class="activePanel === 'manual' ? 'text-white' : 'text-gray-500 hover:text-gray-300'"
                            @click="activePanel = 'manual'"
                        >
                            {{ $t('routes.habitual_tab_manual') }}
                            <span v-if="activePanel === 'manual'" class="absolute inset-x-0 -bottom-px h-px bg-white"></span>
                        </button>
                    </div>

                    <div v-show="activePanel === 'add'" class="p-5">
                        <form @submit.prevent="submitAdd" class="space-y-4">
                            <div v-if="Object.keys(addForm.errors).length" class="p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-xs">
                                <p v-for="(err, key) in addForm.errors" :key="key">{{ err }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('routes.habitual_select_route') }}</label>
                                <select v-model="addForm.route_id" required :class="inputClass">
                                    <option value="">{{ $t('routes.habitual_select_route_placeholder') }}</option>
                                    <option v-for="r in routes" :key="r.id" :value="r.id">
                                        {{ r.title }}<template v-if="r.planned_distance_km"> — {{ r.planned_distance_km }} km</template>
                                    </option>
                                </select>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('routes.habitual_label') }}</label>
                                    <input v-model="addForm.label" type="text" maxlength="120" :placeholder="$t('routes.habitual_label_placeholder')" :class="inputClass">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('routes.habitual_motorcycle') }}</label>
                                    <select v-model="addForm.motorcycle_id" required :class="inputClass">
                                        <option value="">{{ $t('routes.habitual_select_moto') }}</option>
                                        <option v-for="m in motorcycles" :key="m.id" :value="m.id">
                                            {{ `${m.brand} ${m.model}` }} ({{ m.current_km ?? 0 }} km)
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <label class="flex items-center gap-3 cursor-pointer bg-white/[0.04] border border-white/[0.08] rounded-xl px-4 py-3">
                                <input v-model="addForm.round_trip" type="checkbox" class="rounded border-white/[0.08] bg-white/[0.04] text-white focus:ring-white/20">
                                <span class="text-sm text-gray-300">{{ $t('routes.habitual_round_trip') }}</span>
                                <span v-if="estimatedAddKm" class="text-white font-mono font-semibold text-sm ml-auto">{{ estimatedAddKm }} km</span>
                            </label>

                            <button type="submit" :disabled="addForm.processing || !addForm.route_id" class="cc-btn-primary w-full">
                                {{ $t('routes.habitual_add_btn') }}
                            </button>
                        </form>
                    </div>

                    <div v-show="activePanel === 'manual'" class="p-5">
                        <form @submit.prevent="submitManual" class="space-y-4">
                            <div v-if="Object.keys(manualForm.errors).length" class="p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-xs">
                                <p v-for="(err, key) in manualForm.errors" :key="key">{{ err }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2 sm:col-span-1">
                                    <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('routes.habitual_motorcycle') }}</label>
                                    <select v-model="manualForm.motorcycle_id" required :class="inputClass">
                                        <option value="">{{ $t('routes.habitual_select_moto') }}</option>
                                        <option v-for="m in motorcycles" :key="m.id" :value="m.id">
                                            {{ `${m.brand} ${m.model}` }} ({{ m.current_km ?? 0 }} km)
                                        </option>
                                    </select>
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('routes.habitual_km') }}</label>
                                    <input v-model="manualForm.distance_km" type="number" step="0.1" min="0.1" required :class="inputClass" placeholder="12.5">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('routes.habitual_date') }}</label>
                                <input v-model="manualForm.started_at" type="datetime-local" required :class="inputClass">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('routes.habitual_notes') }}</label>
                                <input v-model="manualForm.notes" type="text" maxlength="500" :placeholder="$t('routes.habitual_notes_placeholder')" :class="inputClass">
                            </div>

                            <button type="submit" :disabled="manualForm.processing" class="cc-btn-secondary w-full">
                                {{ $t('routes.habitual_manual_btn') }}
                            </button>
                        </form>
                    </div>
                </section>

                <!-- Llistat de rutes configurades -->
                <section>
                    <h2 v-if="habitualRoutes.length > 0" class="cc-section-label mb-3 px-1">
                        {{ $t('routes.habitual_list_title') }}
                    </h2>

                    <div v-if="habitualRoutes.length === 0" class="py-12 text-center">
                        <p class="text-base font-semibold text-gray-400">{{ $t('routes.habitual_list_empty') }}</p>
                    </div>

                    <div v-else class="divide-y divide-white/[0.06]">
                        <div
                            v-for="item in habitualRoutes"
                            :key="item.id"
                            class="flex items-center gap-3 p-4"
                        >
                            <div class="w-10 h-10 rounded-xl bg-white/[0.06] border border-white/[0.08] flex items-center justify-center flex-shrink-0 text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.875 2.437a1.125 1.125 0 0 0 1.006 0Z" /></svg>
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-white font-medium text-sm leading-snug truncate">{{ item.title }}</p>
                                <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                    <span class="text-xs font-mono font-semibold text-gray-300">{{ displayKm(item) }} km</span>
                                    <span
                                        v-if="item.motorcycle"
                                        class="cc-chip-neutral"
                                    >
                                        {{ item.motorcycle.brand }} {{ item.motorcycle.model }}
                                    </span>
                                    <span
                                        v-if="item.round_trip"
                                        :title="$t('routes.habitual_round_trip')"
                                        class="cc-chip-neutral"
                                    >
                                        {{ $t('routes.habitual_round_trip_short') }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1 flex-shrink-0">
                                <button
                                    type="button"
                                    :disabled="completingId === item.id || !item.distance_km"
                                    @click="markDone(item)"
                                    class="cc-btn-primary px-4 py-2.5 text-xs"
                                >
                                    {{ completingId === item.id ? '…' : $t('routes.habitual_done_btn') }}
                                </button>
                                <button
                                    type="button"
                                    @click="removeItem(item)"
                                    class="cc-icon-btn w-9 h-9 text-gray-500 hover:text-red-400 hover:bg-red-500/10 hover:border-red-500/20"
                                    :aria-label="$t('common.delete')"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        </div>
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

const inputClass = 'w-full rounded-xl bg-white/[0.04] border-white/[0.08] px-4 py-3 text-white text-sm focus:border-white/30 focus:ring-0 outline-none transition';

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

<template>
    <AppLayout :title="$t('events.create_title')" :hide-bottom-nav="isPickerOpen">
        <div class="max-w-xl mx-auto px-6 py-6 pb-24 cc-fade-in">

            <div v-show="!isPickerOpen">
                <header class="flex items-center gap-3 mb-8">
                    <button type="button" @click="goBack" class="cc-icon-btn" :aria-label="$t('common.back')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>
                    </button>
                    <h1 class="cc-title flex-1 truncate">{{ $t('events.create_title') }}</h1>
                </header>

                <form @submit.prevent="submit">

                    <div v-if="Object.keys(form.errors).length > 0" class="mb-8">
                        <p class="text-sm text-red-400 mb-2">{{ $t('events.check_errors') }}</p>
                        <ul class="space-y-1">
                            <li v-for="(error, field) in form.errors" :key="field" class="text-red-400 text-sm">{{ error }}</li>
                        </ul>
                    </div>

                    <section class="space-y-4">
                        <p class="cc-section-label">{{ $t('events.basic_info') }}</p>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('events.event_title') }}</label>
                            <input v-model="form.title" type="text" :placeholder="$t('events.event_title_placeholder')" :class="inputClass(form.errors.title)">
                            <p v-if="form.errors.title" class="text-red-400 text-xs mt-1">{{ form.errors.title }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('events.date_time') }}</label>
                            <input v-model="form.start_time" type="datetime-local" :class="[inputClass(form.errors.start_time), '[color-scheme:dark]']">
                            <p v-if="form.errors.start_time" class="text-red-400 text-xs mt-1">{{ form.errors.start_time }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('events.description_plan') }}</label>
                            <textarea v-model="form.description" rows="3" :placeholder="$t('events.description_placeholder')" :class="[inputClass(), 'resize-none']"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('events.rider_limit') }}</label>
                                <input
                                    v-model="form.max_participants"
                                    type="number"
                                    min="2"
                                    max="999"
                                    :placeholder="$t('events.unlimited')"
                                    :class="inputClass(form.errors.max_participants)"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('events.visibility') }}</label>
                                <div class="flex items-center gap-1.5 rounded-xl border border-white/[0.08] bg-white/[0.04] p-1 h-[42px]">
                                    <button
                                        type="button"
                                        @click="form.is_public = true"
                                        class="flex-1 py-1.5 rounded-lg text-xs font-medium transition"
                                        :class="form.is_public ? 'bg-white text-brand-black' : 'text-gray-500 hover:text-white'"
                                    >
                                        {{ $t('events.public') }}
                                    </button>
                                    <button
                                        type="button"
                                        @click="form.is_public = false"
                                        class="flex-1 py-1.5 rounded-lg text-xs font-medium transition"
                                        :class="!form.is_public ? 'bg-white/[0.12] text-white' : 'text-gray-500 hover:text-white'"
                                    >
                                        {{ $t('events.private_label') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="mt-10 space-y-4">
                        <p class="cc-section-label">{{ $t('events.event_photo') }}</p>
                        <input
                            @change="e => form.photo = e.target.files[0]"
                            type="file"
                            accept="image/*"
                            class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-white/[0.06] file:text-white hover:file:bg-white/[0.1] transition cursor-pointer"
                        >
                        <p v-if="form.errors.photo" class="text-red-400 text-xs">{{ form.errors.photo }}</p>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('events.chat_photo') }}</label>
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full overflow-hidden border border-white/[0.08] bg-white/[0.04] flex items-center justify-center flex-shrink-0">
                                    <img v-if="chatPhotoPreview" :src="chatPhotoPreview" alt="" class="w-full h-full object-cover">
                                    <span v-else class="text-xs text-gray-600">·</span>
                                </div>
                                <input
                                    @change="onChatPhotoChange"
                                    type="file"
                                    accept="image/*"
                                    class="flex-1 text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-white/[0.06] file:text-white hover:file:bg-white/[0.1] transition cursor-pointer"
                                >
                            </div>
                        </div>
                    </section>

                    <section class="mt-10">
                        <div class="flex items-baseline justify-between gap-3 mb-4">
                            <p class="cc-section-label mb-0">{{ $t('events.itinerary_section') }}</p>
                            <span class="text-xs text-gray-600 tabular-nums">{{ $t('events.stages', { n: form.stages.length }) }}</span>
                        </div>
                        <p v-if="form.errors.stages" class="text-red-400 text-xs mb-3">{{ form.errors.stages }}</p>

                        <div class="divide-y divide-white/[0.06]">
                            <div v-for="(stage, index) in form.stages" :key="index" class="py-5">
                                <div class="flex items-center justify-between gap-3 mb-3">
                                    <span class="text-sm text-gray-500 tabular-nums">{{ index + 1 }}</span>
                                    <button
                                        v-if="form.stages.length > 1"
                                        type="button"
                                        @click="removeStage(index)"
                                        class="text-xs text-gray-600 hover:text-red-400 transition"
                                    >
                                        {{ $t('common.delete') }}
                                    </button>
                                </div>

                                <div class="space-y-3">
                                    <div class="flex items-center gap-1.5 rounded-xl border border-white/[0.08] bg-white/[0.04] p-1">
                                        <button
                                            type="button"
                                            @click="stage.type = 'location'"
                                            class="flex-1 py-1.5 rounded-lg text-xs font-medium transition"
                                            :class="stage.type === 'location' ? 'bg-white text-brand-black' : 'text-gray-500 hover:text-white'"
                                        >
                                            {{ $t('events.stage_meeting_point') }}
                                        </button>
                                        <button
                                            type="button"
                                            @click="stage.type = 'route'"
                                            class="flex-1 py-1.5 rounded-lg text-xs font-medium transition"
                                            :class="stage.type === 'route' ? 'bg-white text-brand-black' : 'text-gray-500 hover:text-white'"
                                        >
                                            {{ $t('events.stage_route') }}
                                        </button>
                                    </div>

                                    <div v-if="stage.type === 'route'">
                                        <select v-model="stage.route_id" :class="inputClass(form.errors[`stages.${index}.route_id`])">
                                            <option :value="null">{{ $t('events.route_placeholder') }}</option>
                                            <option v-for="r in myRoutes" :key="r.id" :value="r.id">
                                                {{ r.title }} ({{ r.planned_distance_km }} km)
                                            </option>
                                        </select>
                                        <p v-if="form.errors[`stages.${index}.route_id`]" class="text-red-400 text-xs mt-1">{{ form.errors[`stages.${index}.route_id`] }}</p>
                                        <p v-else-if="myRoutes.length === 0" class="text-red-400 text-xs mt-1">{{ $t('events.no_routes') }}</p>
                                    </div>

                                    <div v-else class="space-y-3">
                                        <input
                                            v-model="stage.location_name"
                                            type="text"
                                            :placeholder="$t('events.place_placeholder')"
                                            :class="inputClass(form.errors[`stages.${index}.location_name`])"
                                        >
                                        <p v-if="form.errors[`stages.${index}.location_name`]" class="text-red-400 text-xs">{{ form.errors[`stages.${index}.location_name`] }}</p>
                                        <div class="flex items-center gap-2">
                                            <p class="flex-1 text-xs text-gray-500 truncate">
                                                <span v-if="stage.latitude">{{ $t('events.coords_saved') }}</span>
                                                <span v-else>{{ $t('events.no_coords') }}</span>
                                            </p>
                                            <button type="button" @click="openLocationPicker(index)" class="cc-btn-text">
                                                {{ $t('events.map_button') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" @click="addStage" class="cc-btn-text w-full mt-4 justify-center">
                            {{ $t('events.add_stage') }}
                        </button>
                    </section>

                    <button type="submit" :disabled="form.processing" class="cc-btn-primary w-full mt-12 py-3.5">
                        {{ $t('events.publish') }}
                    </button>
                </form>
            </div>
        </div>

        <Teleport to="body">
            <div v-if="isPickerOpen" class="fixed inset-0 z-[6000] bg-brand-black flex flex-col">
                <div id="map-picker" class="absolute inset-0 w-full h-full z-0"></div>

                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10 pointer-events-none pb-8 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 drop-shadow-lg">
                        <path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                    </svg>
                </div>

                <button
                    type="button"
                    @click="closePicker"
                    class="cc-icon-btn absolute top-4 left-4 z-[6010] bg-black/50 backdrop-blur-md border-white/20"
                    :aria-label="$t('common.back')"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </button>

                <div class="absolute bottom-6 left-0 w-full px-4 z-[6010]">
                    <button type="button" @click="confirmLocation" class="cc-btn-primary w-full max-w-sm mx-auto py-3.5">
                        {{ $t('events.confirm_location') }}
                    </button>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { smartBack } from '@/Composables/navigationStack.js';
import { addMapTileLayer } from '@/config/mapTiles.js';

const { t } = useI18n();

defineProps({ myRoutes: Array });

const goBack = () => smartBack(route('events.index'));

const form = useForm({
    title: '',
    description: '',
    start_time: '',
    is_public: true,
    max_participants: null,
    photo: null,
    chat_photo: null,
    stages: [{ type: 'location', route_id: null, location_name: '', latitude: null, longitude: null }],
});

const isPickerOpen = ref(false);
const activeStageIndex = ref(null);
const map = ref(null);
const chatPhotoPreview = ref(null);

const inputClass = (error) =>
    error
        ? 'w-full rounded-xl bg-brand-black border-red-500 ring-1 ring-red-500 text-white focus:border-red-400 focus:ring-0'
        : 'w-full rounded-xl bg-white/[0.04] border-white/[0.08] text-white focus:border-white/30 focus:ring-0';

const onChatPhotoChange = (e) => {
    const file = e.target.files[0];
    form.chat_photo = file || null;
    if (file) {
        const reader = new FileReader();
        reader.onload = (ev) => { chatPhotoPreview.value = ev.target.result; };
        reader.readAsDataURL(file);
    } else {
        chatPhotoPreview.value = null;
    }
};

const addStage = () => form.stages.push({ type: 'route', route_id: null, location_name: '', latitude: null, longitude: null });
const removeStage = (index) => {
    if (form.stages.length > 1) form.stages.splice(index, 1);
    else alert(t('events.min_one_stage'));
};

const openLocationPicker = async (index) => {
    activeStageIndex.value = index;
    isPickerOpen.value = true;
    await nextTick();
    requestAnimationFrame(() => {
        setTimeout(() => {
            if (!map.value) {
                map.value = L.map('map-picker', { zoomControl: false, attributionControl: false }).setView([41.3851, 2.1734], 13);
                addMapTileLayer(map.value, L);
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition((pos) => {
                        map.value?.flyTo([pos.coords.latitude, pos.coords.longitude], 15);
                    });
                }
            } else {
                map.value.invalidateSize();
            }
        }, 80);
    });
};

const closePicker = () => { isPickerOpen.value = false; };

const confirmLocation = () => {
    if (map.value && activeStageIndex.value !== null) {
        const center = map.value.getCenter();
        form.stages[activeStageIndex.value].latitude = center.lat;
        form.stages[activeStageIndex.value].longitude = center.lng;
        if (!form.stages[activeStageIndex.value].location_name) {
            form.stages[activeStageIndex.value].location_name = `GPS (${center.lat.toFixed(4)}, ${center.lng.toFixed(4)})`;
        }
        closePicker();
    }
};

const submit = () => form.post(route('events.store'), { forceFormData: true });
</script>

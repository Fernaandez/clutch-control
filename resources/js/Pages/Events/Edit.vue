<template>
    <AppLayout :title="$t('events.edit_title')">
        <div class="max-w-xl mx-auto px-6 py-6 pb-24 cc-fade-in">

            <header class="flex items-center gap-3 mb-8">
                <button type="button" @click="goBack" class="cc-icon-btn" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </button>
                <h1 class="cc-title flex-1 truncate">{{ $t('events.edit_title') }}</h1>
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
                        <input v-model="form.title" type="text" :class="inputClass(form.errors.title)">
                        <p v-if="form.errors.title" class="text-red-400 text-xs mt-1">{{ form.errors.title }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('events.date_time') }}</label>
                        <input v-model="form.start_time" type="datetime-local" :class="[inputClass(form.errors.start_time), '[color-scheme:dark]']">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('events.description_plan') }}</label>
                        <textarea v-model="form.description" rows="3" :class="[inputClass(), 'resize-none']"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('events.rider_limit') }}</label>
                            <input
                                v-model="form.max_participants"
                                type="number"
                                min="1"
                                :placeholder="$t('events.unlimited')"
                                :class="inputClass()"
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

                    <div v-if="event.photo" class="overflow-hidden rounded-2xl">
                        <img :src="$page.props.storageUrl + '/' + event.photo" alt="" class="w-full h-40 object-cover">
                    </div>

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
                                <img v-else-if="event.chat_photo && !form.remove_chat_photo" :src="$page.props.storageUrl + '/' + event.chat_photo" alt="" class="w-full h-full object-cover">
                                <span v-else class="text-xs text-gray-600">·</span>
                            </div>
                            <input
                                @change="onChatPhotoChange"
                                type="file"
                                accept="image/*"
                                class="flex-1 text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-white/[0.06] file:text-white hover:file:bg-white/[0.1] transition cursor-pointer"
                            >
                        </div>
                        <button
                            v-if="event.chat_photo && !form.chat_photo && !form.remove_chat_photo"
                            type="button"
                            @click="form.remove_chat_photo = true"
                            class="mt-2 text-xs text-red-400 hover:text-red-300"
                        >
                            {{ $t('events.remove_chat_photo') }}
                        </button>
                        <p v-else-if="form.remove_chat_photo" class="mt-2 text-xs text-gray-500">
                            {{ $t('events.chat_photo_will_be_removed') }}
                            <button type="button" @click="form.remove_chat_photo = false" class="text-white underline ml-1">{{ $t('events.undo') }}</button>
                        </p>
                    </div>
                </section>

                <section class="mt-10">
                    <div class="flex items-baseline justify-between gap-3 mb-2">
                        <p class="cc-section-label mb-0">{{ $t('events.itinerary_section') }}</p>
                        <span class="text-xs text-gray-600 tabular-nums">{{ $t('events.stages', { n: stages.length }) }}</span>
                    </div>
                    <p class="text-xs text-gray-600 mb-4">{{ $t('events.drag_hint') }}</p>

                    <draggable
                        v-model="stages"
                        item-key="id"
                        handle=".drag-handle"
                        class="divide-y divide-white/[0.06]"
                        ghost-class="opacity-40"
                        animation="200"
                    >
                        <template #item="{ element: stage, index }">
                            <div class="py-5">
                                <div class="flex items-center justify-between gap-3 mb-3">
                                    <div class="flex items-center gap-3">
                                        <button type="button" class="drag-handle text-gray-600 hover:text-gray-300 cursor-grab active:cursor-grabbing p-1" :aria-label="$t('events.drag_hint')">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                            </svg>
                                        </button>
                                        <span class="text-sm text-gray-500 tabular-nums">{{ index + 1 }}</span>
                                    </div>
                                    <button
                                        v-if="stages.length > 1"
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
                                        <select v-model="stage.route_id" :class="inputClass()">
                                            <option :value="null">{{ $t('events.route_placeholder') }}</option>
                                            <option v-for="r in myRoutes" :key="r.id" :value="r.id">
                                                {{ r.title }} ({{ r.planned_distance_km }} km)
                                            </option>
                                        </select>
                                    </div>
                                    <div v-else>
                                        <input
                                            v-model="stage.location_name"
                                            type="text"
                                            :placeholder="$t('events.place_placeholder')"
                                            :class="inputClass()"
                                        >
                                    </div>
                                </div>
                            </div>
                        </template>
                    </draggable>

                    <button type="button" @click="addStage" class="cc-btn-text w-full mt-4 justify-center">
                        {{ $t('events.add_stage') }}
                    </button>
                </section>

                <div class="mt-12 space-y-3">
                    <button type="submit" :disabled="form.processing" class="cc-btn-primary w-full py-3.5">
                        {{ $t('events.save_changes') }}
                    </button>
                    <Link :href="route('events.show', event.id)" class="cc-btn-text w-full justify-center">
                        {{ $t('common.cancel') }}
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import draggable from 'vuedraggable';
import { smartBack } from '@/Composables/navigationStack.js';

const { t } = useI18n();

const props = defineProps({
    event: Object,
    myRoutes: Array,
    currentStages: Array,
});

const goBack = () => smartBack(route('events.show', props.event.id));

const formatDateForInput = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const offset = date.getTimezoneOffset() * 60000;
    return (new Date(date.getTime() - offset)).toISOString().slice(0, 16);
};

const stages = ref(
    props.currentStages && props.currentStages.length > 0
        ? props.currentStages.map((s, i) => ({ ...s, id: Date.now() + i }))
        : [{ id: Date.now(), type: 'location', route_id: null, location_name: props.event.location || '', latitude: null, longitude: null }],
);

const form = useForm({
    _method: 'PUT',
    title: props.event.title,
    description: props.event.description,
    start_time: formatDateForInput(props.event.start_time),
    is_public: Boolean(props.event.is_public),
    max_participants: props.event.max_participants || null,
    photo: null,
    chat_photo: null,
    remove_chat_photo: false,
    stages_json: '',
});

const chatPhotoPreview = ref(null);

const inputClass = (error) =>
    error
        ? 'w-full rounded-xl bg-brand-black border-red-500 ring-1 ring-red-500 text-white focus:border-red-400 focus:ring-0'
        : 'w-full rounded-xl bg-white/[0.04] border-white/[0.08] text-white focus:border-white/30 focus:ring-0';

const onChatPhotoChange = (e) => {
    const file = e.target.files[0];
    form.chat_photo = file || null;
    form.remove_chat_photo = false;
    if (file) {
        const reader = new FileReader();
        reader.onload = (ev) => { chatPhotoPreview.value = ev.target.result; };
        reader.readAsDataURL(file);
    } else {
        chatPhotoPreview.value = null;
    }
};

const addStage = () => {
    stages.value.push({
        id: Date.now(),
        type: 'route',
        route_id: null,
        location_name: '',
        latitude: null,
        longitude: null,
    });
};

const removeStage = (index) => {
    if (stages.value.length > 1) stages.value.splice(index, 1);
    else alert(t('events.min_one_stage'));
};

const submit = () => {
    form.stages_json = JSON.stringify(stages.value);
    form.post(route('events.update', props.event.id), { forceFormData: true });
};
</script>

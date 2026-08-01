<template>
    <AppLayout :current-moto-id="motorcycle.id">
        <div class="max-w-xl mx-auto px-6 py-6 pb-24 cc-fade-in">

            <header class="flex items-center gap-3 mb-6">
                <button type="button" @click="goBack" class="cc-icon-btn" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </button>
                <h1 class="cc-title flex-1 truncate">{{ $t('maintenance.title') }}</h1>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button type="button" @click="openCreateModal" class="cc-icon-btn" :aria-label="$t('maintenance.new_task')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                </div>
            </header>

            <p v-if="motorcycle.brand" class="text-sm text-gray-500 mb-8 truncate">{{ motorcycle.brand }} {{ motorcycle.model }}</p>

            <div v-if="tasks.length" class="divide-y divide-white/[0.06]">
                <div
                    v-for="task in tasks"
                    :key="task.id"
                    class="flex items-start gap-3 py-5"
                >
                    <span
                        v-if="task.status === 'red' || task.km_remaining < 0"
                        class="w-1.5 h-1.5 rounded-full bg-red-400 mt-2 flex-shrink-0"
                    ></span>
                    <div class="flex-1 min-w-0" :class="task.status !== 'red' && task.km_remaining >= 0 ? 'pl-[18px]' : ''">
                        <p class="text-[15px] font-medium text-gray-100">{{ task.title }}</p>
                        <p
                            class="mt-1 text-sm"
                            :class="task.status === 'red' || task.km_remaining < 0 ? 'text-red-400' : 'text-gray-500'"
                        >
                            <template v-if="task.km_remaining < 0">
                                {{ $t('maintenance.over_km', { n: Math.abs(task.km_remaining).toFixed(0) }) }}
                            </template>
                            <template v-else-if="task.status === 'red'">
                                {{ $t('maintenance.due_now') }}
                            </template>
                            <template v-else>
                                {{ $t('maintenance.remaining_km', { n: task.km_remaining.toFixed(0) }) }}
                            </template>
                        </p>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button type="button" @click="openShowModal(task)" class="cc-icon-btn" title="Inspeccionar">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </button>
                        <button type="button" @click="deleteTask(task)" class="cc-icon-btn text-gray-500 hover:text-red-400" :title="$t('maintenance.delete_task_confirm')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                        <button type="button" @click="openCompleteModal(task)" class="cc-btn-text ml-1">
                            {{ $t('maintenance.register') }}
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="flex flex-col items-center justify-center text-center py-16 px-6">
                <p class="text-base font-semibold text-gray-300">{{ $t('maintenance.no_tasks') }}</p>
                <p v-if="$t('maintenance.no_tasks_hint')" class="mt-1 text-sm text-gray-500 max-w-xs">{{ $t('maintenance.no_tasks_hint') }}</p>
                <div class="mt-6">
                    <button type="button" @click="openCreateModal" class="cc-btn-secondary">
                        {{ $t('maintenance.new_task') }}
                    </button>
                </div>
            </div>

            <nav class="mt-12 pt-8 border-t border-white/[0.06]">
                <Link :href="route('motorcycles.maintenance.history', motorcycle.id)" class="cc-btn-text">
                    {{ $t('maintenance.history') }}
                </Link>
            </nav>
        </div>

        <div v-if="showCreateModal" class="fixed inset-0 z-[4000] flex items-center justify-center p-4">
            <div @click="showCreateModal = false" class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
            <div class="relative bg-brand-surface border border-white/[0.08] rounded-2xl p-6 max-w-sm w-full max-h-[min(90vh,32rem)] overflow-y-auto overscroll-contain">
                <h3 class="text-lg font-medium text-white mb-6">{{ $t('maintenance.new_task_title') }}</h3>
                <form @submit.prevent="submitCreate">
                    <div class="space-y-5">
                        <div>
                            <label class="cc-section-label">{{ $t('maintenance.task_what') }}</label>
                            <input v-model="createForm.title" type="text" :placeholder="$t('maintenance.task_placeholder')" class="w-full mt-2" required>
                            <p v-if="createForm.errors.title" class="text-red-400 text-xs mt-1">{{ createForm.errors.title }}</p>
                        </div>
                        <div>
                            <label class="cc-section-label">{{ $t('maintenance.task_frequency') }}</label>
                            <input v-model="createForm.frequency_km" type="number" :placeholder="$t('maintenance.task_frequency_placeholder')" class="w-full mt-2" required>
                            <p v-if="createForm.errors.frequency_km" class="text-red-400 text-xs mt-1">{{ createForm.errors.frequency_km }}</p>
                        </div>
                        <div>
                            <label class="cc-section-label">{{ $t('maintenance.task_last_done') }}</label>
                            <input v-model="createForm.last_km_done" type="number" class="w-full mt-2" required>
                            <p v-if="createForm.errors.last_km_done" class="text-red-400 text-xs mt-1">{{ createForm.errors.last_km_done }}</p>
                        </div>
                    </div>
                    <button type="submit" :disabled="createForm.processing" class="cc-btn-primary w-full mt-6">
                        {{ $t('maintenance.save_task') }}
                    </button>
                </form>
            </div>
        </div>

        <div v-if="showCompleteModal" class="fixed inset-0 z-[4000] flex items-center justify-center p-4">
            <div @click="closeCompleteModal" class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
            <div class="relative bg-brand-surface border border-white/[0.08] rounded-2xl p-6 max-w-sm w-full max-h-[min(90vh,36rem)] overflow-y-auto overscroll-contain">
                <h3 class="text-lg font-medium text-white mb-1">{{ $t('maintenance.register_title') }}</h3>
                <p class="text-sm text-gray-500 mb-6">{{ $t('maintenance.register_for') }} {{ selectedTask?.title }}</p>
                <form @submit.prevent="submitComplete">
                    <div class="space-y-5">
                        <div>
                            <label class="cc-section-label">{{ $t('maintenance.date') }}</label>
                            <input v-model="completeForm.date" type="date" class="w-full mt-2" required>
                            <p v-if="completeForm.errors.date" class="text-red-400 text-xs mt-1">{{ completeForm.errors.date }}</p>
                        </div>
                        <div>
                            <label class="cc-section-label">{{ $t('maintenance.km_current') }}</label>
                            <input v-model="completeForm.km_at_moment" type="number" class="w-full mt-2" required>
                            <p v-if="completeForm.errors.km_at_moment" class="text-red-400 text-xs mt-1">{{ completeForm.errors.km_at_moment }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="cc-section-label">{{ $t('maintenance.price') }}</label>
                                <input v-model="completeForm.cost" type="number" step="0.01" placeholder="0.00" class="w-full mt-2" required>
                                <p v-if="completeForm.errors.cost" class="text-red-400 text-xs mt-1">{{ completeForm.errors.cost }}</p>
                            </div>
                            <div>
                                <label class="cc-section-label">{{ $t('maintenance.workshop') }}</label>
                                <input v-model="completeForm.description" type="text" :placeholder="$t('maintenance.workshop_placeholder')" class="w-full mt-2" required>
                                <p v-if="completeForm.errors.description" class="text-red-400 text-xs mt-1">{{ completeForm.errors.description }}</p>
                            </div>
                        </div>
                        <div>
                            <label class="cc-section-label">{{ $t('maintenance.invoice_photo') }}</label>
                            <input @change="e => completeForm.invoice_photo = e.target.files[0]" type="file" accept="image/*" class="w-full text-sm text-gray-500 mt-2">
                            <p v-if="completeForm.errors.invoice_photo" class="text-red-400 text-xs mt-1">{{ completeForm.errors.invoice_photo }}</p>
                        </div>
                    </div>
                    <button type="submit" :disabled="completeForm.processing" class="cc-btn-primary w-full mt-6">
                        {{ $t('maintenance.confirm_register') }}
                    </button>
                </form>
            </div>
        </div>

        <div v-if="showShowModal" class="fixed inset-0 z-[4000] flex items-center justify-center p-4">
            <div @click="showShowModal = false" class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
            <div class="relative bg-brand-surface border border-white/[0.08] rounded-2xl p-6 max-w-sm w-full max-h-[min(90vh,32rem)] overflow-y-auto overscroll-contain">
                <button type="button" @click="showShowModal = false" class="cc-icon-btn absolute top-4 right-4" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
                <h3 class="text-lg font-medium text-white mb-1 pr-10">{{ selectedShowTask?.title }}</h3>
                <p class="text-sm text-gray-500 mb-6">{{ $t('maintenance.title') }}</p>
                <div class="space-y-4">
                    <div>
                        <p class="cc-section-label">{{ $t('maintenance.cycle') }}</p>
                        <p class="text-white tabular-nums mt-1">{{ selectedShowTask?.frequency_km }} km</p>
                    </div>
                    <div>
                        <p class="cc-section-label">{{ $t('maintenance.done_at') }}</p>
                        <p class="text-white tabular-nums mt-1">{{ selectedShowTask?.last_km_done }} km</p>
                    </div>
                    <div>
                        <p class="cc-section-label">{{ selectedShowTask?.km_remaining >= 0 ? 'Queden' : 'Passats' }}</p>
                        <p class="tabular-nums mt-1" :class="selectedShowTask?.km_remaining < 0 ? 'text-red-400' : 'text-white'">
                            {{ Math.abs(selectedShowTask?.km_remaining ?? 0).toFixed(0) }} km
                        </p>
                    </div>
                    <div>
                        <p class="cc-section-label">Estat</p>
                        <p class="mt-1" :class="selectedShowTask?.status === 'red' ? 'text-red-400' : 'text-gray-400'">
                            {{ selectedShowTask?.status === 'red' ? $t('maintenance.due_now') : selectedShowTask?.status === 'yellow' ? $t('maintenance.coming_soon') : $t('maintenance.ok') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import { smartBack } from '@/Composables/navigationStack.js';

const { t } = useI18n();

const props = defineProps({
    motorcycle: Object,
    tasks: Array
});

const goBack = () => smartBack(route('dashboard', props.motorcycle.id));

// MODAL SHOW
const showShowModal = ref(false);
const selectedShowTask = ref(null);
const openShowModal = (task) => { selectedShowTask.value = task; showShowModal.value = true; };

// MODAL CREAR
const showCreateModal = ref(false);
const createForm = useForm({
    title: '',
    frequency_km: '',
    last_km_done: Math.round(props.motorcycle.current_km)
});

const openCreateModal = () => {
    createForm.title = '';
    createForm.frequency_km = '';
    createForm.last_km_done = Math.round(props.motorcycle.current_km);
    createForm.clearErrors();
    showCreateModal.value = true;
};

const submitCreate = () => {
    createForm.post(route('motorcycles.maintenance.store', props.motorcycle.id), {
        onSuccess: () => { showCreateModal.value = false; createForm.reset(); }
    });
};

// MODAL COMPLETAR
const showCompleteModal = ref(false);
const selectedTask = ref(null);

const completeForm = useForm({
    _method: 'patch',
    date: new Date().toISOString().substr(0, 10), 
    km_at_moment: Math.round(props.motorcycle.current_km),    
    cost: '',
    description: '',
    invoice_photo: null
});

const openCompleteModal = (task) => {
    selectedTask.value = task;
    completeForm.date = new Date().toISOString().substr(0, 10);
    completeForm.km_at_moment = Math.round(props.motorcycle.current_km);
    completeForm.cost = '';
    completeForm.description = '';
    showCompleteModal.value = true;
};

const closeCompleteModal = () => {
    showCompleteModal.value = false;
    selectedTask.value = null;
};

const submitComplete = () => {
    if (!selectedTask.value) return;

    completeForm.post(route('maintenance.mark-done', selectedTask.value.id), {
        forceFormData: true,
        onSuccess: () => closeCompleteModal()
    });
};

const deleteTask = (task) => {
    if(confirm(t('maintenance.delete_task_confirm'))) {
        useForm({}).delete(route('maintenance.destroy', task.id));
    }
};
</script>

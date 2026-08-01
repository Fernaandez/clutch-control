<template>
    <AppLayout :current-moto-id="motorcycle.id">
        <div class="max-w-xl mx-auto px-6 py-6 pb-24 cc-fade-in">

            <header class="flex items-center gap-3 mb-6">
                <button type="button" @click="goBack" class="cc-icon-btn" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </button>
                <h1 class="cc-title flex-1 truncate">{{ $t('upgrades.title') }}</h1>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button type="button" @click="showCreateModal = true" class="cc-icon-btn" :aria-label="$t('upgrades.new_upgrade')">
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
                    <div class="flex-1 min-w-0 pl-[18px]">
                        <p class="text-[15px] font-medium text-gray-100">{{ task.title }}</p>
                        <p v-if="task.location" class="mt-1 text-sm text-gray-500">{{ task.location }}</p>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button type="button" @click="openShowModal(task)" class="cc-icon-btn" title="Inspeccionar">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </button>
                        <button type="button" @click="deleteTask(task)" class="cc-icon-btn text-gray-500 hover:text-red-400" :title="$t('upgrades.delete_confirm')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                        <button type="button" @click="openCompleteModal(task)" class="cc-btn-text">
                            {{ $t('upgrades.installed') }}
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="flex flex-col items-center justify-center text-center py-16 px-6">
                <p class="text-base font-semibold text-gray-300">{{ $t('upgrades.no_upgrades') }}</p>
                <p v-if="$t('upgrades.no_upgrades_hint')" class="mt-1 text-sm text-gray-500 max-w-xs">{{ $t('upgrades.no_upgrades_hint') }}</p>
                <div class="mt-6">
                    <button type="button" @click="showCreateModal = true" class="cc-btn-secondary">
                        {{ $t('upgrades.new_upgrade') }}
                    </button>
                </div>
            </div>

            <nav class="mt-12 pt-8 border-t border-white/[0.06]">
                <Link :href="route('motorcycles.upgrades.history', motorcycle.id)" class="cc-btn-text">
                    {{ $t('upgrades.history_link') }}
                </Link>
            </nav>
        </div>

        <div v-if="showCreateModal" class="fixed inset-0 z-[4000] flex items-center justify-center p-4">
            <div @click="showCreateModal = false" class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
            <div class="relative bg-brand-surface border border-white/[0.08] rounded-2xl p-6 max-w-sm w-full max-h-[min(90vh,32rem)] overflow-y-auto overscroll-contain">
                <h3 class="text-lg font-medium text-white mb-6">{{ $t('upgrades.new_upgrade_title') }}</h3>
                <form @submit.prevent="submitCreate">
                    <div class="space-y-5">
                        <div>
                            <label class="cc-section-label">{{ $t('upgrades.what_upgrade') }}</label>
                            <input v-model="createForm.title" type="text" class="w-full mt-2" required>
                            <p v-if="createForm.errors.title" class="text-red-400 text-xs mt-1">{{ createForm.errors.title }}</p>
                        </div>
                        <div>
                            <label class="cc-section-label">{{ $t('upgrades.details') }}</label>
                            <textarea v-model="createForm.location" rows="3" class="w-full mt-2"></textarea>
                            <p v-if="createForm.errors.location" class="text-red-400 text-xs mt-1">{{ createForm.errors.location }}</p>
                        </div>
                    </div>
                    <button type="submit" class="cc-btn-primary w-full mt-6">
                        {{ $t('upgrades.save') }}
                    </button>
                </form>
            </div>
        </div>

        <div v-if="showCompleteModal" class="fixed inset-0 z-[4000] flex items-center justify-center p-4">
            <div @click="showCompleteModal = false" class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
            <div class="relative bg-brand-surface border border-white/[0.08] rounded-2xl p-6 max-w-sm w-full max-h-[min(90vh,36rem)] overflow-y-auto overscroll-contain">
                <h3 class="text-lg font-medium text-white mb-1">{{ $t('upgrades.upgrade_done_title') }}</h3>
                <p class="text-sm text-gray-500 mb-6">{{ $t('upgrades.task_label') }} {{ selectedTask?.title }}</p>
                <form @submit.prevent="submitComplete">
                    <div class="space-y-5">
                        <div>
                            <label class="cc-section-label">{{ $t('upgrades.date') }}</label>
                            <input v-model="completeForm.date" type="date" class="w-full mt-2" required>
                            <p v-if="completeForm.errors.date" class="text-red-400 text-xs mt-1">{{ completeForm.errors.date }}</p>
                        </div>
                        <div>
                            <label class="cc-section-label">{{ $t('upgrades.km_current') }}</label>
                            <input v-model="completeForm.km_at_moment" type="number" class="w-full mt-2" required>
                            <p v-if="completeForm.errors.km_at_moment" class="text-red-400 text-xs mt-1">{{ completeForm.errors.km_at_moment }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="cc-section-label">{{ $t('upgrades.price') }}</label>
                                <input v-model="completeForm.cost" type="number" step="0.01" class="w-full mt-2" required>
                                <p v-if="completeForm.errors.cost" class="text-red-400 text-xs mt-1">{{ completeForm.errors.cost }}</p>
                            </div>
                            <div>
                                <label class="cc-section-label">{{ $t('upgrades.workshop') }}</label>
                                <input v-model="completeForm.description" type="text" class="w-full mt-2" required>
                                <p v-if="completeForm.errors.description" class="text-red-400 text-xs mt-1">{{ completeForm.errors.description }}</p>
                            </div>
                        </div>
                        <div>
                            <label class="cc-section-label">{{ $t('upgrades.invoice_photo') }}</label>
                            <input @change="e => completeForm.invoice_photo = e.target.files[0]" type="file" accept="image/*" class="w-full text-sm text-gray-500 mt-2">
                            <p v-if="completeForm.errors.invoice_photo" class="text-red-400 text-xs mt-1">{{ completeForm.errors.invoice_photo }}</p>
                        </div>
                    </div>
                    <button type="submit" class="cc-btn-primary w-full mt-6">
                        {{ $t('upgrades.confirm') }}
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
                <p class="text-sm text-gray-500 mb-6">{{ $t('upgrades.title') }}</p>
                <div>
                    <p class="cc-section-label">{{ $t('upgrades.details') }}</p>
                    <p class="text-gray-300 text-sm mt-1">{{ selectedShowTask?.location || '—' }}</p>
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
const props = defineProps({ motorcycle: Object, tasks: Array });

const goBack = () => smartBack(route('dashboard', props.motorcycle.id));

const showCreateModal = ref(false);
const showCompleteModal = ref(false);
const selectedTask = ref(null);

const showShowModal = ref(false);
const selectedShowTask = ref(null);
const openShowModal = (task) => { selectedShowTask.value = task; showShowModal.value = true; };

const createForm = useForm({ title: '', location: '' });
const completeForm = useForm({ _method: 'patch', date: new Date().toISOString().substr(0, 10), km_at_moment: Math.round(props.motorcycle.current_km), cost: '', description: '', invoice_photo: null });

const submitCreate = () => { createForm.post(route('motorcycles.upgrades.store', props.motorcycle.id), { onSuccess: () => { showCreateModal.value = false; createForm.reset(); } }); };
const openCompleteModal = (task) => { selectedTask.value = task; completeForm.km_at_moment = Math.round(props.motorcycle.current_km); showCompleteModal.value = true; };
const submitComplete = () => { if (!selectedTask.value) return; completeForm.post(route('maintenance.mark-done', selectedTask.value.id), { forceFormData: true, onSuccess: () => showCompleteModal.value = false }); };
const deleteTask = (task) => { if(confirm(t('upgrades.delete_confirm'))) useForm({}).delete(route('maintenance.destroy', task.id)); };
</script>

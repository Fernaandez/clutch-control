<template>
    <AppLayout :current-moto-id="motorcycle.id">
        <div class="w-full max-w-full min-w-0 overflow-x-hidden box-border px-4 py-6 pb-24">
            <div class="mb-6 space-y-3 w-full min-w-0">
                <div class="flex items-start gap-3 min-w-0">
                    <button type="button" @click="goBack" class="inline-flex items-center justify-center w-10 h-10 flex-shrink-0 rounded-full bg-brand-dark border border-brand-neon/50 text-brand-neon hover:bg-brand-neon hover:text-brand-black transition shadow-[0_0_10px_rgba(12,225,181,0.2)]" aria-label="Enrere">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                    </button>
                    <div class="min-w-0 flex-1 pt-0.5">
                        <h1 class="text-2xl font-bold text-red-500 break-words">{{ $t('repairs.title') }}</h1>
                        <p class="text-brand-muted text-sm truncate">{{ motorcycle.brand }} {{ motorcycle.model }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 w-full">
                    <Link :href="route('motorcycles.repairs.history', motorcycle.id)" class="flex-1 min-w-[calc(50%-0.25rem)] sm:flex-initial sm:min-w-0 inline-flex justify-center items-center bg-brand-black border border-brand-dark text-gray-300 px-4 py-2.5 rounded-lg font-bold text-sm hover:border-red-500 hover:text-red-500 transition">{{ $t('repairs.history_link') }}</Link>
                    <button type="button" @click="showCreateModal = true" class="flex-1 min-w-[calc(50%-0.25rem)] sm:flex-initial sm:min-w-0 bg-red-600 text-white px-4 py-2.5 rounded-lg font-bold text-sm shadow-lg hover:bg-red-500 transition">{{ $t('repairs.new_repair') }}</button>
                </div>
            </div>

            <div v-if="tasks.length === 0" class="text-center py-10 text-gray-500 bg-brand-surface rounded-xl border border-brand-dark border-dashed">
                <p>{{ $t('repairs.no_repairs') }}</p>
                <p class="text-sm">{{ $t('repairs.no_repairs_hint') }}</p>
            </div>

            <div v-else class="space-y-4">
                <div v-for="task in tasks" :key="task.id" class="bg-brand-surface rounded-xl p-5 border-l-4 border-red-500 shadow-lg relative overflow-hidden group">
                    <div class="flex justify-between items-start mb-2 relative z-10">
                        <div>
                            <h3 class="text-lg font-bold text-white">{{ task.title }}</h3>
                            <p class="text-sm text-gray-400">{{ task.location }}</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 relative z-10 mt-4">
                        <button @click="openShowModal(task)" class="text-gray-500 hover:text-brand-neon transition p-1" title="Inspeccionar">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                        </button>
                        <button @click="deleteTask(task)" class="text-gray-600 hover:text-red-500 transition p-1"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button>
                        <button @click="openCompleteModal(task)" class="bg-brand-dark border border-red-500/30 text-red-400 hover:bg-red-500 hover:text-white px-3 py-1 rounded text-sm font-bold transition flex items-center gap-1 active:scale-95 shadow-lg">{{ $t('repairs.fixed') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div @click="showCreateModal = false" class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>
            <div class="relative bg-brand-surface border border-red-900 rounded-xl p-6 w-full max-w-sm max-h-[min(90vh,32rem)] overflow-y-auto overscroll-contain shadow-lg">
                <h3 class="text-xl font-bold text-white mb-4">{{ $t('repairs.new_repair_title') }}</h3>
                <form @submit.prevent="submitCreate">
                    <div class="space-y-4">
                        <div>
                            <label class="text-gray-400 text-sm">{{ $t('repairs.what_broke') }}</label>
                            <input v-model="createForm.title" type="text" class="w-full bg-brand-black border-brand-dark rounded text-white focus:border-red-500" required>
                            <p v-if="createForm.errors.title" class="text-red-500 text-xs mt-1">{{ createForm.errors.title }}</p>
                        </div>
                        <div>
                            <label class="text-gray-400 text-sm">{{ $t('repairs.details') }}</label>
                            <textarea v-model="createForm.location" rows="3" class="w-full bg-brand-black border-brand-dark rounded text-white focus:border-red-500"></textarea>
                            <p v-if="createForm.errors.location" class="text-red-500 text-xs mt-1">{{ createForm.errors.location }}</p>
                        </div>
                    </div>
                    <button type="submit" class="mt-6 w-full bg-red-600 text-white font-bold py-2 rounded hover:bg-red-500 transition">{{ $t('repairs.save') }}</button>
                </form>
            </div>
        </div>

        <div v-if="showCompleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div @click="showCompleteModal = false" class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>
            <div class="relative bg-brand-surface border border-red-500 rounded-xl p-6 w-full max-w-sm max-h-[min(90vh,36rem)] overflow-y-auto overscroll-contain shadow-lg">
                <h3 class="text-xl font-bold text-white mb-1">{{ $t('repairs.repair_done_title') }}</h3>
                <p class="text-sm text-brand-muted mb-4">{{ $t('repairs.task_label') }} <span class="text-red-400">{{ selectedTask?.title }}</span></p>
                <form @submit.prevent="submitComplete">
                    <div class="space-y-4">
                        <div><label class="text-gray-400 text-sm">{{ $t('repairs.date') }}</label><input v-model="completeForm.date" type="date" class="w-full bg-brand-black border-brand-dark rounded text-white focus:border-red-500" required></div>
                        <div><label class="text-gray-400 text-sm">{{ $t('repairs.km_current') }}</label><input v-model="completeForm.km_at_moment" type="number" class="w-full bg-brand-black border-brand-dark rounded text-white focus:border-red-500" required></div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="text-gray-400 text-sm">{{ $t('repairs.price') }}</label><input v-model="completeForm.cost" type="number" step="0.01" class="w-full bg-brand-black border-brand-dark rounded text-white focus:border-red-500" required></div>
                            <div><label class="text-gray-400 text-sm">{{ $t('repairs.workshop') }}</label><input v-model="completeForm.description" type="text" class="w-full bg-brand-black border-brand-dark rounded text-white focus:border-red-500" required></div>
                        </div>
                        <div>
                            <label class="text-gray-400 text-sm">{{ $t('repairs.invoice_photo') }}</label>
                            <input @change="e => completeForm.invoice_photo = e.target.files[0]" type="file" accept="image/*" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-red-900/30 file:text-red-400 hover:file:bg-red-900/50 cursor-pointer mt-1">
                        </div>
                    </div>
                    <button type="submit" class="mt-6 w-full bg-red-600 text-white font-bold py-3 rounded-lg hover:bg-red-500 transition shadow-lg">{{ $t('repairs.confirm') }}</button>
                </form>
            </div>
        </div>

        <!-- MODAL SHOW (Read-only) -->
        <div v-if="showShowModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div @click="showShowModal = false" class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>
            <div class="relative bg-brand-surface border border-red-500/30 rounded-xl p-6 w-full max-w-sm max-h-[min(90vh,32rem)] overflow-y-auto overscroll-contain shadow-[0_0_20px_rgba(239,68,68,0.1)]">
                <button @click="showShowModal = false" class="absolute top-4 right-4 inline-flex items-center justify-center w-8 h-8 rounded-full bg-brand-dark border border-brand-neon/50 text-brand-neon hover:bg-brand-neon hover:text-brand-black transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                </button>
                <h3 class="text-xl font-bold text-white mb-1 pr-10">{{ selectedShowTask?.title }}</h3>
                <p class="text-xs text-red-400/70 uppercase tracking-widest mb-4">{{ $t('repairs.title') }}</p>
                <div class="space-y-3">
                    <div class="bg-brand-black/60 rounded-lg p-3 border border-brand-dark">
                        <p class="text-xs text-gray-500 mb-1">Detalls</p>
                        <p class="text-gray-200 text-sm">{{ selectedShowTask?.location || '—' }}</p>
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

const submitCreate = () => { createForm.post(route('motorcycles.repairs.store', props.motorcycle.id), { onSuccess: () => { showCreateModal.value = false; createForm.reset(); } }); };
const openCompleteModal = (task) => { selectedTask.value = task; completeForm.km_at_moment = Math.round(props.motorcycle.current_km); showCompleteModal.value = true; };
const submitComplete = () => { if (!selectedTask.value) return; completeForm.post(route('maintenance.mark-done', selectedTask.value.id), { forceFormData: true, onSuccess: () => showCompleteModal.value = false }); };
const deleteTask = (task) => { if(confirm(t('repairs.delete_confirm'))) useForm({}).delete(route('maintenance.destroy', task.id)); };
</script>

<template>
    <AppLayout :current-moto-id="motorcycle.id">
        <div class="px-4 py-6 pb-24">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <Link :href="route('dashboard', motorcycle.id)" class="text-gray-400 text-sm hover:text-white flex items-center gap-1">&larr; Tornar</Link>
                    <h1 class="text-2xl font-bold text-purple-400 mt-1">Millores i Mods 🚀</h1>
                    <p class="text-brand-muted text-sm">{{ motorcycle.brand }} {{ motorcycle.model }}</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('motorcycles.upgrades.history', motorcycle.id)" class="bg-brand-black border border-brand-dark text-gray-300 px-4 py-2 rounded-lg font-bold text-sm hover:border-purple-500 hover:text-purple-400 transition flex items-center gap-2">Historial</Link>
                    <button @click="showCreateModal = true" class="bg-purple-600 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-lg hover:bg-purple-500 transition">+ Upgrade</button>
                </div>
            </div>

            <div v-if="tasks.length === 0" class="text-center py-10 text-gray-500 bg-brand-surface rounded-xl border border-brand-dark border-dashed">
                <p>No tens millores planejades.</p>
                <p class="text-sm">Vols posar un escape nou? Uns punys? 😉</p>
            </div>

            <div v-else class="space-y-4">
                <div v-for="task in tasks" :key="task.id" class="bg-brand-surface rounded-xl p-5 border-l-4 border-purple-500 shadow-lg relative overflow-hidden group">
                    <div class="flex justify-between items-start mb-2 relative z-10">
                        <div>
                            <h3 class="text-lg font-bold text-white">{{ task.title }}</h3>
                            <p class="text-sm text-gray-400">{{ task.description }}</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 relative z-10 mt-4">
                        <button @click="deleteTask(task)" class="text-gray-600 hover:text-purple-500 transition p-1"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button>
                        <button @click="openCompleteModal(task)" class="bg-brand-dark border border-purple-500/30 text-purple-400 hover:bg-purple-500 hover:text-white px-3 py-1 rounded text-sm font-bold transition flex items-center gap-1 active:scale-95 shadow-lg">Instal·lat ✅</button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div @click="showCreateModal = false" class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>
            <div class="relative bg-brand-surface border border-purple-900 rounded-xl p-6 w-full max-w-sm shadow-lg">
                <h3 class="text-xl font-bold text-white mb-4">Nova Millora 🚀</h3>
                <form @submit.prevent="submitCreate">
                    <div class="space-y-4">
                        <div><label class="text-gray-400 text-sm">Què vols posar?</label><input v-model="createForm.title" type="text" class="w-full bg-brand-black border-brand-dark rounded text-white focus:border-purple-500" required></div>
                        <div><label class="text-gray-400 text-sm">Detalls / Link</label><textarea v-model="createForm.description" rows="3" class="w-full bg-brand-black border-brand-dark rounded text-white focus:border-purple-500"></textarea></div>
                    </div>
                    <button type="submit" class="mt-6 w-full bg-purple-600 text-white font-bold py-2 rounded hover:bg-purple-500 transition">Guardar</button>
                </form>
            </div>
        </div>

        <div v-if="showCompleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div @click="showCompleteModal = false" class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>
            <div class="relative bg-brand-surface border border-purple-500 rounded-xl p-6 w-full max-w-sm shadow-lg">
                <h3 class="text-xl font-bold text-white mb-1">Upgrade Completat!</h3>
                <p class="text-sm text-brand-muted mb-4">Tasca: <span class="text-purple-400">{{ selectedTask?.title }}</span></p>
                <form @submit.prevent="submitComplete">
                    <div class="space-y-4">
                        <div><label class="text-gray-400 text-sm">Data</label><input v-model="completeForm.date" type="date" class="w-full bg-brand-black border-brand-dark rounded text-white focus:border-purple-500" required></div>
                        <div><label class="text-gray-400 text-sm">KM Actuals</label><input v-model="completeForm.km_at_moment" type="number" step="0.1" class="w-full bg-brand-black border-brand-dark rounded text-white focus:border-purple-500" required></div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="text-gray-400 text-sm">Preu (€)</label><input v-model="completeForm.cost" type="number" step="0.01" class="w-full bg-brand-black border-brand-dark rounded text-white focus:border-purple-500" required></div>
                            <div><label class="text-gray-400 text-sm">Notes</label><input v-model="completeForm.description" type="text" class="w-full bg-brand-black border-brand-dark rounded text-white focus:border-purple-500" required></div>
                        </div>
                    </div>
                    <button type="submit" class="mt-6 w-full bg-purple-600 text-white font-bold py-3 rounded-lg hover:bg-purple-500 transition shadow-lg">Confirmar</button>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ motorcycle: Object, tasks: Array });
const showCreateModal = ref(false);
const showCompleteModal = ref(false);
const selectedTask = ref(null);

const createForm = useForm({ title: '', description: '' });
const completeForm = useForm({ date: new Date().toISOString().substr(0, 10), km_at_moment: props.motorcycle.current_km, cost: '', description: '' });

const submitCreate = () => { createForm.post(route('motorcycles.upgrades.store', props.motorcycle.id), { onSuccess: () => showCreateModal.value = false }); };
const openCompleteModal = (task) => { selectedTask.value = task; completeForm.km_at_moment = props.motorcycle.current_km; showCompleteModal.value = true; };
const submitComplete = () => { if (!selectedTask.value) return; completeForm.patch(route('maintenance.mark-done', selectedTask.value.id), { onSuccess: () => showCompleteModal.value = false }); };
const deleteTask = (task) => { if(confirm('Segur?')) useForm({}).delete(route('maintenance.destroy', task.id)); };
</script>
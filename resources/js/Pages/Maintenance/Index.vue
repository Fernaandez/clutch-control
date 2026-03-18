<template>
    <AppLayout>
        <div class="px-4 py-6 pb-24">
            
            <div class="flex items-center justify-between mb-6">
                <div>
                    <Link :href="route('dashboard', motorcycle.id)" class="text-gray-400 text-sm hover:text-white flex items-center gap-1">
                        &larr; Tornar
                    </Link>
                    <h1 class="text-2xl font-bold text-white mt-1">Manteniment</h1>
                    <p class="text-brand-muted text-sm">{{ motorcycle.brand }} {{ motorcycle.model }}</p>
                </div>
                
                <div class="flex gap-2">
                    <Link :href="route('motorcycles.maintenance.history', motorcycle.id)" class="bg-brand-black border border-brand-dark text-gray-300 px-4 py-2 rounded-lg font-bold text-sm hover:border-brand-neon hover:text-brand-neon transition flex items-center gap-2">
                        Historial
                    </Link>

                    <button @click="openCreateModal" class="bg-brand-neon text-brand-black px-4 py-2 rounded-lg font-bold text-sm shadow-neon hover:bg-white transition">
                        + Tasca
                    </button>
                </div>
            </div>

            <div v-if="tasks.length === 0" class="text-center py-10 text-gray-500 bg-brand-surface rounded-xl border border-brand-dark border-dashed">
                <p>No tens tasques programades.</p>
                <p class="text-sm">Afegeix "Canvi d'Oli" o "Rodes" per començar!</p>
            </div>

            <div v-else class="space-y-4">
                <div v-for="task in tasks" :key="task.id" class="bg-brand-surface rounded-xl p-5 border border-brand-dark shadow-lg relative overflow-hidden group">
                    
                    <div class="flex justify-between items-start mb-2 relative z-10">
                        <div>
                            <h3 class="text-lg font-bold text-white">{{ task.title }}</h3>
                            <p class="text-xs text-gray-400">
                                Fet als: <span class="text-gray-200">{{ task.last_km_done }} km</span> | 
                                Cicle: {{ task.frequency_km }} km
                            </p>
                        </div>
                        
                        <div class="text-right">
                             <span v-if="task.status === 'red'" class="text-red-500 font-bold text-sm flex items-center gap-1 justify-end">
                                ⚠ Toca ja!
                             </span>
                             <span v-else-if="task.status === 'yellow'" class="text-yellow-400 font-bold text-sm">
                                Aviat...
                             </span>
                             <span v-else class="text-green-500 font-bold text-sm">
                                OK
                             </span>

                             <p v-if="task.km_remaining < 0" class="text-xs text-red-400 font-bold mt-1">
                                T'has passat {{ Math.abs(task.km_remaining).toFixed(0) }} km!
                             </p>
                             <p v-else class="text-xs text-gray-500 mt-1">
                                Falten {{ task.km_remaining.toFixed(0) }} km
                             </p>
                        </div>
                    </div>

                    <div class="w-full bg-brand-black h-3 rounded-full overflow-hidden mb-4 relative z-10 border border-brand-dark/50">
                        <div 
                            class="h-full transition-all duration-500 ease-out rounded-full"
                            :class="{
                                'bg-green-500': task.status === 'green',
                                'bg-yellow-400': task.status === 'yellow',
                                'bg-red-500 animate-pulse': task.status === 'red' 
                            }"
                            :style="{ width: task.percentage + '%' }"
                        ></div>
                    </div>

                    <div class="flex justify-end gap-3 relative z-10">
                        <button @click="deleteTask(task)" class="text-gray-600 hover:text-red-500 transition p-1" title="Eliminar Tasca">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                        </button>
                        
                        <button 
                            @click="openCompleteModal(task)"
                            class="bg-brand-dark border border-brand-neon/30 text-brand-neon hover:bg-brand-neon hover:text-brand-black px-3 py-1 rounded text-sm font-bold transition flex items-center gap-1 active:scale-95 shadow-lg"
                        >
                            Registrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div @click="showCreateModal = false" class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>
            <div class="relative bg-brand-surface border border-brand-dark rounded-xl p-6 w-full max-w-sm shadow-neon">
                <h3 class="text-xl font-bold text-white mb-4">Nova Tasca</h3>
                <form @submit.prevent="submitCreate">
                    <div class="space-y-4">
                        <div>
                            <label class="text-gray-400 text-sm">Què cal fer?</label>
                            <input v-model="createForm.title" type="text" placeholder="Ex: Canvi Oli" class="w-full bg-brand-black border-brand-dark rounded text-white focus:border-brand-neon" required>
                            <p v-if="createForm.errors.title" class="text-red-500 text-xs mt-1">{{ createForm.errors.title }}</p>
                        </div>
                        <div>
                            <label class="text-gray-400 text-sm">Cada quants KM es fa?</label>
                            <input v-model="createForm.frequency_km" type="number" placeholder="Ex: 5000" class="w-full bg-brand-black border-brand-dark rounded text-white focus:border-brand-neon" required>
                            <p v-if="createForm.errors.frequency_km" class="text-red-500 text-xs mt-1">{{ createForm.errors.frequency_km }}</p>
                        </div>
                        <div>
                            <label class="text-gray-400 text-sm">Quan es va fer l'últim cop? (KM)</label>
                            <input v-model="createForm.last_km_done" type="number" class="w-full bg-brand-black border-brand-dark rounded text-white focus:border-brand-neon" required>
                            <p v-if="createForm.errors.last_km_done" class="text-red-500 text-xs mt-1">{{ createForm.errors.last_km_done }}</p>
                        </div>
                    </div>
                    <button type="submit" :disabled="createForm.processing" class="mt-6 w-full bg-brand-neon text-brand-black font-bold py-2 rounded hover:bg-white transition">
                        Guardar Tasca
                    </button>
                </form>
            </div>
        </div>

        <div v-if="showCompleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div @click="closeCompleteModal" class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>
            <div class="relative bg-brand-surface border border-brand-neon rounded-xl p-6 w-full max-w-sm shadow-[0_0_20px_rgba(12,225,181,0.2)]">
                
                <h3 class="text-xl font-bold text-white mb-1">Registrar Manteniment</h3>
                <p class="text-sm text-brand-muted mb-4">Per a la tasca: <span class="text-brand-neon">{{ selectedTask?.title }}</span></p>
                
                <form @submit.prevent="submitComplete">
                    <div class="space-y-4">
                        <div>
                            <label class="text-gray-400 text-sm">Data</label>
                            <input v-model="completeForm.date" type="date" class="w-full bg-brand-black border-brand-dark rounded text-white focus:border-brand-neon" required>
                            <p v-if="completeForm.errors.date" class="text-red-500 text-xs mt-1">{{ completeForm.errors.date }}</p>
                        </div>
                        
                        <div>
                            <label class="text-gray-400 text-sm">KM Actuals de la Moto</label>
                            <input v-model="completeForm.km_at_moment" type="number" class="w-full bg-brand-black border-brand-dark rounded text-white focus:border-brand-neon" required>
                            <p v-if="completeForm.errors.km_at_moment" class="text-red-500 text-xs mt-1">{{ completeForm.errors.km_at_moment }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-gray-400 text-sm">Taller / Lloc</label>
                                <input v-model="completeForm.description" type="text" placeholder="Marca, taller..." class="w-full bg-brand-black border-brand-dark rounded text-white focus:border-brand-neon" required>
                                <p v-if="completeForm.errors.description" class="text-red-500 text-xs mt-1">{{ completeForm.errors.description }}</p>
                            </div>
                            <div>
                                <label class="text-gray-400 text-sm">Preu (€)</label>
                                <input v-model="completeForm.cost" type="number" step="0.01" placeholder="0.00" class="w-full bg-brand-black border-brand-dark rounded text-white focus:border-brand-neon" required>
                                <p v-if="completeForm.errors.cost" class="text-red-500 text-xs mt-1">{{ completeForm.errors.cost }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="text-gray-400 text-sm">Foto de la factura / recanvi</label>
                            <input @change="e => completeForm.invoice_photo = e.target.files[0]" type="file" accept="image/*" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-brand-base/20 file:text-brand-neon hover:file:bg-brand-base/30 cursor-pointer mt-1">
                            <p v-if="completeForm.errors.invoice_photo" class="text-red-500 text-xs mt-1">{{ completeForm.errors.invoice_photo }}</p>
                        </div>
                    </div>
                    
                    <button type="submit" :disabled="completeForm.processing" class="mt-6 w-full bg-brand-neon text-brand-black font-bold py-3 rounded-lg hover:bg-white transition transform active:scale-95 shadow-neon">
                        Confirmar Registre
                    </button>
                </form>
            </div>
        </div>

    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    motorcycle: Object,
    tasks: Array
    // history eliminat d'aquí
});

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
    if(confirm('Segur que vols esborrar aquesta tasca?')) {
        useForm({}).delete(route('maintenance.destroy', task.id));
    }
};
</script>

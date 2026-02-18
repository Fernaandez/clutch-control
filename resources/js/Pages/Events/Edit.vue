<template>
    <AppLayout title="Editar Quedada">
        <div class="max-w-4xl mx-auto px-4 py-6 pb-24">
            
            <div class="flex items-center justify-between mb-6">
                <Link :href="route('events.mine')" class="text-gray-500 hover:text-white flex items-center gap-1 text-sm">
                    &larr; Cancel·lar
                </Link>
                <h1 class="text-xl font-black text-white uppercase tracking-tighter">Editar <span class="text-brand-neon">Quedada</span></h1>
            </div>

            <form @submit.prevent="submit" class="space-y-8">

                <div class="bg-brand-surface p-6 rounded-xl border border-brand-dark shadow-lg space-y-5">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-700 pb-2 mb-4">Dades Generals</h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Títol</label>
                            <input v-model="form.title" type="text" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0">
                            <div v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Dia i Hora</label>
                            <input v-model="form.start_time" type="datetime-local" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0 [color-scheme:dark]">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Punt de Sortida (Nom)</label>
                            <input v-model="form.location" type="text" placeholder="Ex: Benzinera Repsol" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Límit de Riders (Opcional)</label>
                            <input v-model="form.max_participants" type="number" min="1" placeholder="Sense límit" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0">
                            <p class="text-[10px] text-gray-500 mt-1">Deixa-ho buit si no hi ha límit.</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Descripció / Pla</label>
                        <textarea v-model="form.description" rows="3" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Visibilitat</label>
                        <div class="flex items-center gap-3 bg-brand-black p-2 rounded-lg border border-brand-dark max-w-md">
                            <button type="button" @click="form.is_public = true" class="flex-1 py-2 rounded text-xs font-bold uppercase transition flex items-center justify-center gap-2" :class="form.is_public ? 'bg-brand-neon text-black shadow-lg' : 'text-gray-500 hover:text-white'">
                                🌍 Pública
                            </button>
                            <button type="button" @click="form.is_public = false" class="flex-1 py-2 rounded text-xs font-bold uppercase transition flex items-center justify-center gap-2" :class="!form.is_public ? 'bg-gray-700 text-white shadow-lg' : 'text-gray-500 hover:text-white'">
                                🔒 Privada
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-brand-surface p-6 rounded-xl border border-brand-dark shadow-lg">
                    <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-2">
                        <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Itinerari / Ruta</h2>
                        <span class="text-xs text-brand-neon bg-brand-neon/10 px-2 py-1 rounded border border-brand-neon/20 font-bold">{{ form.stages.length }} Etapes</span>
                    </div>

                    <div class="space-y-4">
                        <div v-for="(stage, index) in form.stages" :key="index" class="relative group bg-brand-black border border-brand-dark rounded-xl p-4 transition hover:border-gray-500">
                            
                            <div class="absolute -left-3 top-4 w-6 h-6 bg-brand-neon text-black font-black text-xs rounded-full flex items-center justify-center shadow-lg border-2 border-brand-surface z-10">
                                {{ index + 1 }}
                            </div>

                            <button @click="removeStage(index)" type="button" class="absolute top-2 right-2 text-gray-600 hover:text-red-500 p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>

                            <div class="grid md:grid-cols-12 gap-4 items-start ml-3">
                                <div class="md:col-span-4">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Tipus</label>
                                    <select v-model="stage.type" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-xs focus:border-brand-neon py-2.5">
                                        <option value="location">📍 Punt de Trobada</option>
                                        <option value="route">🏍️ Ruta GPS</option>
                                    </select>
                                </div>

                                <div class="md:col-span-8">
                                    <div v-if="stage.type === 'route'">
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Selecciona Ruta</label>
                                        <select v-model="stage.route_id" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon py-2.5">
                                            <option :value="null">-- Tria una ruta --</option>
                                            <option v-for="r in myRoutes" :key="r.id" :value="r.id">
                                                {{ r.title }} ({{ r.planned_distance_km }} km)
                                            </option>
                                        </select>
                                    </div>
                                    <div v-else>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nom del Lloc</label>
                                        <input v-model="stage.location_name" type="text" placeholder="Ex: Hotel..." class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" @click="addStage" class="mt-4 w-full border-2 border-dashed border-gray-700 hover:border-brand-neon hover:text-brand-neon text-gray-500 rounded-xl py-3 flex items-center justify-center gap-2 text-sm font-bold uppercase transition bg-transparent hover:bg-brand-neon/5">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Afegir Etapa / Parada
                    </button>
                </div>

                <div class="pt-2 pb-12 flex gap-4">
                    <button type="submit" :disabled="form.processing" class="flex-1 bg-brand-neon text-brand-black font-black py-4 rounded-xl uppercase tracking-widest hover:bg-white transition shadow-[0_0_20px_rgba(12,225,181,0.4)] disabled:opacity-50">
                        Guardar Canvis
                    </button>
                    
                    <Link :href="route('events.mine')" class="px-6 flex items-center justify-center border border-gray-600 text-gray-400 font-bold rounded-xl hover:bg-gray-800 hover:text-white transition">
                        Cancel·lar
                    </Link>
                </div>

            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    event: Object,
    myRoutes: Array,
    currentStages: Array // Aquestes venen del controlador
});

// Helper Data
const formatDateForInput = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const offset = date.getTimezoneOffset() * 60000;
    return (new Date(date.getTime() - offset)).toISOString().slice(0, 16);
};

// PREPARAR ELS STAGES INICIALS
// Si l'event té rutes, les posem. Si no, posem una etapa buida per defecte.
const initialStages = props.currentStages && props.currentStages.length > 0 
    ? props.currentStages 
    : [{ type: 'location', route_id: null, location_name: props.event.location || '', latitude: null, longitude: null }];

const form = useForm({
    title: props.event.title,
    description: props.event.description,
    start_time: formatDateForInput(props.event.start_time),
    location: props.event.location,
    latitude: props.event.latitude,
    longitude: props.event.longitude,
    is_public: Boolean(props.event.is_public),
    max_participants: props.event.max_participants, // <--- NOU
    stages: initialStages // <--- Carreguem l'itinerari
});

const addStage = () => {
    form.stages.push({
        type: 'route',
        route_id: null,
        location_name: '',
        latitude: null,
        longitude: null
    });
};

const removeStage = (index) => {
    if (form.stages.length > 1) {
        form.stages.splice(index, 1);
    } else {
        alert("Has de mantenir almenys una etapa.");
    }
};

const submit = () => {
    form.put(route('events.update', props.event.id));
};
</script>
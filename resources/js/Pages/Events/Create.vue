<template>
    <AppLayout title="Nova Quedada">
        <div class="max-w-4xl mx-auto px-4 py-6 pb-24">
            
            <div class="flex items-center justify-between mb-6">
                <Link :href="route('events.index')" class="text-gray-500 hover:text-white flex items-center gap-1 text-sm">
                    &larr; Cancel·lar
                </Link>
                <h1 class="text-xl font-black text-white uppercase tracking-tighter">Organitzar <span class="text-brand-neon">Quedada</span></h1>
            </div>

            <form @submit.prevent="submit" class="space-y-8">

                <!-- Errors globals -->
                <div v-if="Object.keys(form.errors).length > 0" class="p-4 bg-red-500/10 border border-red-500/30 rounded-xl">
                    <p class="text-red-500 font-black text-xs uppercase tracking-widest mb-2">⚠️ Revisa els errors:</p>
                    <ul class="list-disc pl-5 text-red-400 text-sm space-y-1">
                        <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
                    </ul>
                </div>

                <p class="text-[10px] text-gray-600 uppercase tracking-widest"><span class="text-red-400 font-bold">*</span> Camp obligatori</p>

                <div class="bg-brand-surface p-6 rounded-xl border border-brand-dark shadow-lg space-y-5">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-700 pb-2 mb-4">Informació Bàsica</h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">
                                Títol <span class="text-red-400">*</span>
                            </label>
                            <input v-model="form.title" type="text" placeholder="Ex: Esmorzar a Rupit"
                                :class="form.errors.title ? 'w-full bg-brand-black border-red-500 ring-1 ring-red-500 rounded-lg text-white focus:border-red-400 focus:ring-0' : 'w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0'">
                            <p v-if="form.errors.title" class="text-red-400 text-xs mt-1">⚠ {{ form.errors.title }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">
                                Dia i Hora <span class="text-red-400">*</span>
                            </label>
                            <input v-model="form.start_time" type="datetime-local"
                                :class="form.errors.start_time ? 'w-full bg-brand-black border-red-500 ring-1 ring-red-500 rounded-lg text-white focus:border-red-400 focus:ring-0 [color-scheme:dark]' : 'w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0 [color-scheme:dark]'">
                            <p class="text-[10px] text-gray-500 mt-1">Format: <span class="text-brand-neon font-mono">DD/MM/AAAA HH:MM</span></p>
                            <p v-if="form.errors.start_time" class="text-red-400 text-xs mt-1">⚠ {{ form.errors.start_time }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Descripció del Pla</label>
                        <textarea v-model="form.description" rows="3" placeholder="Ritme, parades, logística..." class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0"></textarea>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6 pt-2">
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">
                                Límit de Riders <span class="text-gray-600 font-normal">(opcional)</span>
                            </label>
                            <div class="relative">
                                <input 
                                    v-model="form.max_participants" 
                                    type="number" 
                                    min="2" 
                                    max="999"
                                    placeholder="Sense límit (∞)" 
                                    class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0 placeholder-gray-600"
                                >
                                <div class="absolute right-3 top-2.5 text-xs text-gray-500 pointer-events-none">
                                    {{ form.max_participants ? 'persones' : 'il·limitat' }}
                                </div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">Número enter ≥ 2 (o deixa buit per il·limitat)</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Visibilitat</label>
                            <div class="flex items-center gap-3 bg-brand-black p-2 rounded-lg border border-brand-dark h-[42px]">
                                <button type="button" @click="form.is_public = true" class="flex-1 py-1 rounded text-xs font-bold uppercase transition" :class="form.is_public ? 'bg-brand-neon text-black' : 'text-gray-500 hover:text-white'">
                                    🌍 Pública
                                </button>
                                <button type="button" @click="form.is_public = false" class="flex-1 py-1 rounded text-xs font-bold uppercase transition" :class="!form.is_public ? 'bg-gray-600 text-white' : 'text-gray-500 hover:text-white'">
                                    🔒 Privada
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Foto de la Quedada (Opcional)</label>
                        <input @change="e => form.photo = e.target.files[0]" type="file" accept="image/*" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-brand-base/20 file:text-brand-neon hover:file:bg-brand-base/30 transition cursor-pointer">
                        <div v-if="form.errors.photo" class="text-red-500 text-xs mt-1">{{ form.errors.photo }}</div>
                        <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-wider">📐 Recomanat: <span class="text-brand-neon font-bold">1200×800 px</span> (3:2) &mdash; JPG o PNG &mdash; Màx 2MB</p>
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
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Tria la Ruta</label>
                                        <select v-model="stage.route_id" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon py-2.5">
                                            <option :value="null">-- Selecciona una de les teves rutes --</option>
                                            <option v-for="r in myRoutes" :key="r.id" :value="r.id">
                                                {{ r.title }} ({{ r.planned_distance_km }} km)
                                            </option>
                                        </select>
                                        <p v-if="myRoutes.length === 0" class="text-[10px] text-red-400 mt-1">No tens rutes. Crea'n una primer!</p>
                                    </div>
                                    <div v-else class="space-y-3">
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nom del Lloc</label>
                                            <input v-model="stage.location_name" type="text" placeholder="Ex: Benzinera Repsol" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon">
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 bg-gray-800/50 rounded px-3 py-2 text-xs text-gray-400 border border-brand-dark truncate">
                                                <span v-if="stage.latitude">✅ Coordenades guardades</span>
                                                <span v-else class="italic">Sense ubicació al mapa</span>
                                            </div>
                                            <button type="button" @click="openLocationPicker(index)" class="bg-brand-dark hover:bg-brand-neon hover:text-black text-white px-3 py-2 rounded-lg text-xs font-bold uppercase transition flex items-center gap-2 border border-gray-600 hover:border-brand-neon">
                                                🗺️ Mapa
                                            </button>
                                        </div>
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

                <div class="pt-6 pb-12">
                    <button type="submit" :disabled="form.processing" class="w-full bg-brand-neon text-brand-black font-black py-4 rounded-xl uppercase tracking-widest hover:bg-white transition shadow-[0_0_20px_rgba(12,225,181,0.4)] disabled:opacity-50">
                        Publicar Quedada 🚀
                    </button>
                </div>
            </form>

            <div v-if="isPickerOpen" class="fixed inset-0 z-[6000] bg-black/90 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-brand-surface border border-brand-dark rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden flex flex-col h-[70vh]">
                    <div class="p-4 bg-brand-black border-b border-brand-dark flex justify-between items-center">
                        <h3 class="text-white font-bold uppercase text-sm">Mou el mapa per triar</h3>
                        <button @click="closePicker" class="text-gray-400 hover:text-white p-2">✕</button>
                    </div>
                    <div class="flex-1 relative bg-gray-900 w-full">
                        <div id="map-picker" class="absolute inset-0 w-full h-full z-0"></div>
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10 pointer-events-none text-brand-neon drop-shadow-2xl pb-8">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-12 h-12"><path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" /></svg>
                        </div>
                    </div>
                    <div class="p-4 bg-brand-black border-t border-brand-dark">
                        <button @click="confirmLocation" class="w-full bg-brand-neon text-brand-black font-black py-3 rounded-xl uppercase hover:bg-white transition">Confirmar Ubicació</button>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({ myRoutes: Array });

const form = useForm({
    title: '',
    description: '',
    start_time: '',
    is_public: true,
    max_participants: null,
    photo: null,
    stages: [{ type: 'location', route_id: null, location_name: '', latitude: null, longitude: null }]
});

// GESTIÓ MAPA
const isPickerOpen = ref(false);
const activeStageIndex = ref(null);
const map = ref(null);

const addStage = () => form.stages.push({ type: 'route', route_id: null, location_name: '', latitude: null, longitude: null });
const removeStage = (index) => form.stages.length > 1 ? form.stages.splice(index, 1) : alert("Mínim una etapa!");

const openLocationPicker = async (index) => {
    activeStageIndex.value = index;
    isPickerOpen.value = true;
    await nextTick();
    if (!map.value) initMap();
    else map.value.invalidateSize(); // CLAU per evitar mapa gris
};

const closePicker = () => isPickerOpen.value = false;

const initMap = () => {
    map.value = L.map('map-picker', { zoomControl: false }).setView([41.3851, 2.1734], 13);
    L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', { maxZoom: 20, className: 'map-tiles-inverse' }).addTo(map.value);
    if(navigator.geolocation) navigator.geolocation.getCurrentPosition(pos => map.value.flyTo([pos.coords.latitude, pos.coords.longitude], 15));
};

const confirmLocation = () => {
    if (map.value && activeStageIndex.value !== null) {
        const center = map.value.getCenter();
        form.stages[activeStageIndex.value].latitude = center.lat;
        form.stages[activeStageIndex.value].longitude = center.lng;
        if (!form.stages[activeStageIndex.value].location_name) form.stages[activeStageIndex.value].location_name = `Punt GPS (${center.lat.toFixed(4)}, ${center.lng.toFixed(4)})`;
        closePicker();
    }
};

const submit = () => form.post(route('events.store'), { forceFormData: true });
</script>

<style>
.map-tiles-inverse { filter: brightness(150%) contrast(150%); }
</style>
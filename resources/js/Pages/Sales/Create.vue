<template>
    <AppLayout title="Vendre Moto">
        <div class="px-4 py-6 pb-24 max-w-2xl mx-auto">
            
            <div class="mb-8">
                <Link :href="route('sales.index')" class="text-gray-400 text-sm hover:text-white flex items-center gap-1 transition mb-4">
                    &larr; Tornar al Mercat
                </Link>
                <h1 class="text-3xl font-black text-white uppercase tracking-tighter">Posar a la <span class="text-brand-neon">Venda</span></h1>
                <p class="text-gray-400 text-sm mt-1">Comparteix la teva màquina amb la comunitat.</p>
            </div>

            <div v-if="motorcycles.length === 0" class="bg-brand-surface border border-brand-dark rounded-xl p-6 text-center">
                <div class="text-4xl mb-3">😅</div>
                <h3 class="text-lg font-bold text-white mb-2">No tens motos disponibles</h3>
                <p class="text-sm text-gray-400 mb-4">No tens cap moto al teu garatge, o bé totes ja estan anunciades.</p>
                <Link :href="route('motorcycles.create')" class="inline-block bg-brand-neon text-brand-black font-bold uppercase text-xs px-6 py-3 rounded hover:bg-white transition">
                    Afegir Moto al Garatge
                </Link>
            </div>

            <form v-else @submit.prevent="submit" class="space-y-6">

                <!-- Avís de contacte -->
                <div v-if="!$page.props.auth.user.phone_number" class="p-4 rounded-xl border border-yellow-500/30 bg-yellow-500/5 flex items-start gap-4">
                    <div class="p-2 bg-yellow-500/10 rounded-lg text-yellow-500 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-bold text-yellow-500 uppercase tracking-widest mb-1">Sense Telèfon</h3>
                        <p class="text-xs text-gray-400 mb-2">Els compradors podran contactar-te per email. Si vols WhatsApp, afegeix un telèfon al perfil.</p>
                        <Link :href="route('profile.edit')" class="text-xs font-bold text-yellow-400 hover:underline">&rarr; Afegir telèfon al perfil</Link>
                    </div>
                </div>

                <!-- Selecció de moto -->
                <div class="bg-brand-surface border border-brand-dark rounded-xl p-5 shadow-xl space-y-5">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-700 pb-2">La Moto</h2>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Quina moto vens?</label>
                        <select v-model="form.motorcycle_id" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon">
                            <option :value="null" disabled>Selecciona una moto del teu garatge...</option>
                            <option v-for="moto in motorcycles" :key="moto.id" :value="moto.id">
                                {{ moto.brand }} {{ moto.model }} ({{ moto.year }}) — {{ moto.current_km?.toLocaleString('ca-ES') }} km
                            </option>
                        </select>
                    </div>

                    <div v-if="form.motorcycle_id" class="bg-brand-black p-4 rounded-xl border border-brand-dark space-y-4 animate-fade-in">
                        <h3 class="text-xs font-black text-brand-neon uppercase tracking-widest">Revisa les Dades Tècniques</h3>
                        <p class="text-[10px] text-gray-500">Això actualitzarà la fitxa de la moto i es mostrarà a l'anunci.</p>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">CC</label>
                                <input v-model="form.cc" type="number" class="w-full text-sm rounded bg-brand-surface border-brand-dark text-white focus:border-brand-neon">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">CV</label>
                                <input v-model="form.power_cv" type="number" class="w-full text-sm rounded bg-brand-surface border-brand-dark text-white focus:border-brand-neon">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Carnet</label>
                                <select v-model="form.license_type" class="w-full text-sm rounded bg-brand-surface border-brand-dark text-white focus:border-brand-neon">
                                    <option value="">Sense definir</option>
                                    <option value="AM">AM</option><option value="A1">A1</option><option value="A2">A2</option><option value="A">A</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Estil</label>
                                <select v-model="form.type" class="w-full text-sm rounded bg-brand-surface border-brand-dark text-white focus:border-brand-neon">
                                    <option value="">Sense definir</option>
                                    <option value="Naked">Naked</option><option value="Sport">Sport / R</option>
                                    <option value="Trail">Trail / Adventure</option><option value="Custom">Custom / Cruiser</option>
                                    <option value="Scooter">Scooter / Maxi</option><option value="Touring">Touring</option>
                                    <option value="Off-Road">Off-Road</option><option value="Classic">Clàssica</option>
                                </select>
                            </div>
                            <div class="flex items-center pt-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input v-model="form.has_abs" type="checkbox" class="rounded bg-brand-surface border-brand-dark text-brand-neon focus:ring-brand-neon h-4 w-4">
                                    <span class="text-xs font-bold text-gray-300">Equipada amb ABS</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Extres instal·lats</label>
                            <textarea v-model="form.extras" rows="2" class="w-full text-sm rounded bg-brand-surface border-brand-dark text-white focus:border-brand-neon resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Dades de l'anunci -->
                <div v-if="form.motorcycle_id" class="bg-brand-surface border border-brand-dark rounded-xl p-5 shadow-xl space-y-5 animate-fade-in">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-700 pb-2">L'Anunci</h2>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Títol de l'anunci</label>
                        <input v-model="form.title" type="text" placeholder="Ex: Yamaha MT-07 Impecable (A2)" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon">
                        <div v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Preu (€)</label>
                            <input v-model="form.price" type="number" step="0.01" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon text-brand-neon font-bold text-lg">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Ubicació</label>
                            <input v-model="form.location" type="text" placeholder="Ex: Barcelona" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Comentaris pel comprador</label>
                        <textarea v-model="form.description" rows="4" placeholder="Motiu de la venda, revisions al dia, historial..." class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon resize-none"></textarea>
                    </div>

                    <!-- Fotos -->
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Fotos de la Moto (màx. 8)</label>
                        <div class="border-2 border-dashed border-brand-dark hover:border-brand-neon rounded-xl p-6 text-center transition cursor-pointer bg-brand-black" @click="$refs.photoInput.click()">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-500 mx-auto mb-2"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                            <p class="text-sm text-gray-400">Toca per seleccionar fotos</p>
                            <p class="text-[10px] text-gray-600 mt-1">JPG, PNG, WEBP — Màx 3MB cada una</p>
                        </div>
                        <input ref="photoInput" type="file" multiple accept="image/*" class="hidden" @change="handleImages">
                        
                        <!-- Previsualitzar -->
                        <div v-if="imagePreviews.length > 0" class="grid grid-cols-3 gap-2 mt-3">
                            <div v-for="(preview, i) in imagePreviews" :key="i" class="relative group">
                                <img :src="preview" class="w-full h-24 object-cover rounded-lg border border-brand-dark">
                                <button @click="removePreview(i)" type="button" class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition">✕</button>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-brand-dark">
                        <button type="submit" :disabled="form.processing" class="w-full bg-brand-neon text-brand-black font-black uppercase tracking-wider py-4 rounded-xl hover:bg-white transition flex items-center justify-center gap-2 disabled:opacity-50">
                            <span v-if="form.processing">Publicant...</span>
                            <span v-else>Publicar Anunci</span>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ motorcycles: Array });

const photoInput = ref(null);
const imagePreviews = ref([]);
const selectedFiles = ref([]);

const form = useForm({
    motorcycle_id: null,
    title: '',
    description: '',
    price: '',
    location: '',
    cc: '',
    power_cv: '',
    license_type: '',
    type: '',
    has_abs: false,
    extras: '',
    images: [],
});

watch(() => form.motorcycle_id, (newId) => {
    if (newId) {
        const moto = props.motorcycles.find(m => m.id === newId);
        if (moto) {
            form.cc = moto.cc || '';
            form.power_cv = moto.power_cv || '';
            form.license_type = moto.license_type || '';
            form.type = moto.type || '';
            form.has_abs = Boolean(moto.has_abs);
            form.extras = moto.extras || '';
            if (!form.title) form.title = `${moto.brand} ${moto.model} (${moto.year})`;
        }
    }
});

const handleImages = (e) => {
    const files = Array.from(e.target.files);
    files.forEach(file => {
        if (selectedFiles.value.length < 8) {
            selectedFiles.value.push(file);
            const reader = new FileReader();
            reader.onload = (ev) => imagePreviews.value.push(ev.target.result);
            reader.readAsDataURL(file);
        }
    });
    form.images = selectedFiles.value;
};

const removePreview = (index) => {
    imagePreviews.value.splice(index, 1);
    selectedFiles.value.splice(index, 1);
    form.images = selectedFiles.value;
};

const submit = () => {
    form.post(route('sales.store'), { forceFormData: true });
};
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
</style>
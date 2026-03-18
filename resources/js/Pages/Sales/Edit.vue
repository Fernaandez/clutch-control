<template>
    <AppLayout title="Editar Anunci">
        <div class="px-4 py-6 pb-24 max-w-2xl mx-auto">
            
            <div class="mb-8">
                <Link :href="route('sales.mine')" class="text-gray-400 text-sm hover:text-white flex items-center gap-1 transition mb-4">
                    &larr; Els meus Anuncis
                </Link>
                <h1 class="text-3xl font-black text-white uppercase tracking-tighter">Editar <span class="text-brand-neon">Anunci</span></h1>
                <p class="text-gray-400 text-sm mt-1">{{ sale.motorcycle?.brand }} {{ sale.motorcycle?.model }}</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">

                <!-- Especificacions tècniques -->
                <div class="bg-brand-surface border border-brand-dark rounded-xl p-5 shadow-xl space-y-4">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-700 pb-2">Especificacions de la Moto</h2>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">CC</label>
                            <input v-model="form.cc" type="number" class="w-full text-sm rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">CV</label>
                            <input v-model="form.power_cv" type="number" class="w-full text-sm rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Carnet</label>
                            <select v-model="form.license_type" class="w-full text-sm rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon">
                                <option value="">Sense definir</option>
                                <option value="AM">AM</option><option value="A1">A1</option><option value="A2">A2</option><option value="A">A</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Estil</label>
                        <select v-model="form.type" class="w-full text-sm rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon">
                            <option value="">Sense definir</option>
                            <option value="Naked">Naked</option><option value="Sport">Sport / R</option>
                            <option value="Trail">Trail / Adventure</option><option value="Custom">Custom / Cruiser</option>
                            <option value="Scooter">Scooter / Maxi</option><option value="Touring">Touring</option>
                            <option value="Off-Road">Off-Road</option><option value="Classic">Clàssica</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Extres instal·lats</label>
                        <textarea v-model="form.extras" rows="2" class="w-full text-sm rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon resize-none"></textarea>
                    </div>
                </div>

                <!-- Dades de l'anunci -->
                <div class="bg-brand-surface border border-brand-dark rounded-xl p-5 shadow-xl space-y-5">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-700 pb-2">Dades de l'Anunci</h2>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Títol</label>
                        <input v-model="form.title" type="text" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0">
                        <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Preu (€)</label>
                            <input v-model="form.price" type="number" step="0.01" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0 text-brand-neon font-bold text-lg">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Ubicació</label>
                            <input v-model="form.location" type="text" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Descripció</label>
                        <textarea v-model="form.description" rows="5" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0 resize-none"></textarea>
                    </div>
                </div>

                <!-- Fotos existents -->
                <div v-if="sale.images && sale.images.length > 0" class="bg-brand-surface border border-brand-dark rounded-xl p-5 shadow-xl">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-700 pb-2 mb-4">Fotos Actuals</h2>
                    <div class="grid grid-cols-3 gap-2">
                        <div v-for="img in sale.images" :key="img.id" class="relative group">
                            <img :src="$page.props.storageUrl + '/' + img.image_path" class="w-full h-24 object-cover rounded-lg border border-brand-dark">
                            <Link
                                :href="route('sales.images.destroy', { sale: sale.id, image: img.id })"
                                method="delete" as="button"
                                class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-6 h-6 text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition"
                            >✕</Link>
                        </div>
                    </div>
                </div>

                <!-- Afegir fotos noves -->
                <div class="bg-brand-surface border border-brand-dark rounded-xl p-5 shadow-xl">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-700 pb-2 mb-4">Afegir Fotos Noves</h2>
                    <div class="border-2 border-dashed border-brand-dark hover:border-brand-neon rounded-xl p-5 text-center transition cursor-pointer bg-brand-black" @click="$refs.photoInput.click()">
                        <p class="text-sm text-gray-500">Toca per seleccionar fotos (màx. {{ 8 - (sale.images?.length || 0) }} restants)</p>
                    </div>
                    <input ref="photoInput" type="file" multiple accept="image/*" class="hidden" @change="handleImages">
                    <div v-if="imagePreviews.length > 0" class="grid grid-cols-3 gap-2 mt-3">
                        <div v-for="(preview, i) in imagePreviews" :key="i" class="relative group">
                            <img :src="preview" class="w-full h-24 object-cover rounded-lg border border-brand-neon/50">
                            <button @click="removePreview(i)" type="button" class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition">✕</button>
                        </div>
                    </div>
                </div>

                <!-- Estat -->
                <div class="bg-brand-surface border border-brand-dark rounded-xl p-5 shadow-xl">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-700 pb-2 mb-4">Estat de l'Anunci</h2>
                    <div class="flex flex-col gap-3">
                        <select v-model="form.state" class="w-full bg-brand-black border-brand-dark rounded-lg text-white font-bold focus:border-brand-neon focus:ring-brand-neon p-3">
                            <option value="actiu">Visible al Mercat (Actiu)</option>
                            <option value="actiu (reservat) (nou)">Actiu (Reservat)</option>
                            <option value="venuda">Marcar com a VENUDA</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <button type="submit" :disabled="form.processing" class="w-full bg-brand-neon text-brand-black font-black uppercase tracking-wider py-4 rounded-xl hover:bg-white transition disabled:opacity-50">
                        Actualitzar Anunci
                    </button>
                    <button type="button" @click="destroy" class="w-full text-red-500 hover:text-red-400 hover:bg-brand-black py-3 rounded-xl transition text-sm border border-red-900/30">
                        Eliminar Anunci Definitivament
                    </button>
                </div>

            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ sale: Object });

const photoInput = ref(null);
const imagePreviews = ref([]);
const selectedFiles = ref([]);

const form = useForm({
    _method: 'PUT',
    title: props.sale.title,
    description: props.sale.description,
    price: props.sale.price,
    location: props.sale.location,
    state: props.sale.state || 'actiu',
    cc: props.sale.motorcycle?.cc || '',
    power_cv: props.sale.motorcycle?.power_cv || '',
    license_type: props.sale.motorcycle?.license_type || '',
    type: props.sale.motorcycle?.type || '',
    extras: props.sale.motorcycle?.extras || '',
    images: [],
});

const handleImages = (e) => {
    const files = Array.from(e.target.files);
    const remaining = 8 - (props.sale.images?.length || 0) - selectedFiles.value.length;
    files.slice(0, remaining).forEach(file => {
        selectedFiles.value.push(file);
        const reader = new FileReader();
        reader.onload = (ev) => imagePreviews.value.push(ev.target.result);
        reader.readAsDataURL(file);
    });
    form.images = selectedFiles.value;
};

const removePreview = (index) => {
    imagePreviews.value.splice(index, 1);
    selectedFiles.value.splice(index, 1);
    form.images = selectedFiles.value;
};

const submit = () => {
    form.post(route('sales.update', props.sale.id), { forceFormData: true });
};

const destroy = () => {
    if (confirm("Estàs segur? La moto no s'esborrarà del teu garatge.")) {
        form.delete(route('sales.destroy', props.sale.id));
    }
};
</script>

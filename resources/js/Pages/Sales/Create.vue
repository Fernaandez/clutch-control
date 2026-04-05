<template>
    <AppLayout :title="$t('sales.put_on_sale')">
        <div class="px-4 py-6 pb-24 max-w-2xl mx-auto">
            
            <div class="mb-8">
                <div class="flex items-center gap-3">
                    <Link :href="route('sales.index')" class="w-10 h-10 rounded-full bg-brand-neon flex items-center justify-center text-black hover:bg-white transition flex-shrink-0 shadow-[0_0_15px_rgba(12,225,181,0.3)]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                    </Link>
                    <h1 class="text-2xl font-black uppercase tracking-tighter text-white leading-none">POSAR A LA VENDA</h1>
                </div>
            </div>

            <div v-if="motorcycles.length === 0" class="bg-brand-surface border border-brand-dark rounded-xl p-6 text-center">
                <div class="text-4xl mb-3">😅</div>
                <h3 class="text-lg font-bold text-white mb-2">{{ $t('sales.no_motos_avail') }}</h3>
                <p class="text-sm text-gray-400 mb-4">{{ $t('sales.no_motos_desc') }}</p>
                <Link :href="route('motorcycles.create')" class="inline-block bg-brand-neon text-brand-black font-bold uppercase text-xs px-6 py-3 rounded hover:bg-white transition">
                    {{ $t('sales.add_moto_btn') }}
                </Link>
            </div>

            <form v-else @submit.prevent="submit" class="space-y-6">



                <!-- Selecció de moto -->
                <div class="bg-brand-surface border border-brand-dark rounded-xl p-5 shadow-xl space-y-5">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-700 pb-2">{{ $t('sales.the_moto') }}</h2>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">{{ $t('sales.which_moto') }}</label>
                        <select v-model="form.motorcycle_id" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon">
                            <option :value="null" disabled>{{ $t('sales.select_moto') }}</option>
                            <option v-for="moto in motorcycles" :key="moto.id" :value="moto.id">
                                {{ moto.brand }} {{ moto.model }} ({{ moto.year }}) — {{ moto.current_km?.toLocaleString('ca-ES') }} km
                            </option>
                        </select>
                    </div>

                    <div v-if="form.motorcycle_id" class="bg-brand-black p-4 rounded-xl border border-brand-dark space-y-4 animate-fade-in">
                        <h3 class="text-xs font-black text-brand-neon uppercase tracking-widest">{{ $t('sales.review_data') }}</h3>
                        <p class="text-[10px] text-gray-500">{{ $t('sales.update_notice') }}</p>

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
                                    <option value="">{{ $t('common.undefined') }}</option>
                                    <option value="AM">AM</option><option value="A1">A1</option><option value="A2">A2</option><option value="A">A</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">{{ $t('sales.style') }}</label>
                                <select v-model="form.type" class="w-full text-sm rounded bg-brand-surface border-brand-dark text-white focus:border-brand-neon">
                                    <option value="">{{ $t('common.undefined') }}</option>
                                    <option value="Naked">Naked</option><option value="Sport">Sport / R</option>
                                    <option value="Trail">Trail / Adventure</option><option value="Custom">Custom / Cruiser</option>
                                    <option value="Scooter">Scooter / Maxi</option><option value="Touring">Touring</option>
                                    <option value="Off-Road">Off-Road</option><option value="Classic">{{ $t('sales.classic') }}</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">{{ $t('sales.equipped_extras') }}</label>
                            <textarea v-model="form.extras" rows="2" class="w-full text-sm rounded bg-brand-surface border-brand-dark text-white focus:border-brand-neon resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Dades de l'anunci -->
                <div v-if="form.motorcycle_id" class="bg-brand-surface border border-brand-dark rounded-xl p-5 shadow-xl space-y-5 animate-fade-in">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-700 pb-2">{{ $t('sales.the_ad') }}</h2>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">{{ $t('sales.ad_title') }}</label>
                        <input v-model="form.title" type="text" :placeholder="$t('sales.ad_title_ph')" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon">
                        <div v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">{{ $t('sales.price_eur') }}</label>
                            <input v-model="form.price" type="number" step="0.01" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon text-brand-neon font-bold text-lg">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">{{ $t('sales.location') }}</label>
                            <input v-model="form.location" type="text" :placeholder="$t('sales.location_ph')" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">{{ $t('sales.seller_comments') }}</label>
                        <textarea v-model="form.description" rows="4" :placeholder="$t('sales.desc_ph')" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon resize-none"></textarea>
                    </div>

                    <div class="bg-brand-black p-3 rounded-lg border border-brand-dark flex items-start gap-3">
                        <input v-model="form.show_history" type="checkbox" id="show_history" class="mt-1 bg-brand-surface border-brand-dark rounded text-brand-neon focus:ring-0 cursor-pointer">
                        <div>
                            <label for="show_history" class="text-sm font-bold text-white block cursor-pointer">Compartir historial de manteniment?</label>
                            <p class="text-[10px] text-gray-400">Si actives això, els compradors podran veure el registre de revisions i factures que hagis desat.</p>
                        </div>
                    </div>

                    <!-- Fotos -->
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">{{ $t('sales.item_photos') }}</label>
                        <div class="border-2 border-dashed border-brand-dark hover:border-brand-neon rounded-xl p-6 text-center transition cursor-pointer bg-brand-black" @click="$refs.photoInput.click()">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-500 mx-auto mb-2"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                            <p class="text-sm text-gray-400">{{ $t('sales.tap_photos', { n: 8 }) }}</p>
                            <p class="text-[10px] text-gray-600 mt-1">{{ $t('sales.photo_format') }}</p>
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
                            <span v-if="form.processing">{{ $t('sales.publishing') }}</span>
                            <span v-else>{{ $t('sales.publish_btn') }}</span>
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
    extras: '',
    show_history: false,
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

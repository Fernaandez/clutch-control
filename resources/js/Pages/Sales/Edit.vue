<template>
    <AppLayout :title="$t('sales.edit_listing_btn')">
        <div class="px-4 py-6 pb-24 max-w-2xl mx-auto">
            
            <div class="mb-8">
                <div class="flex items-center gap-3">
                    <Link :href="route('sales.mine')" class="w-10 h-10 rounded-full bg-brand-neon flex items-center justify-center text-black hover:bg-white transition flex-shrink-0 shadow-[0_0_15px_rgba(12,225,181,0.3)]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-black uppercase tracking-tighter text-white leading-none">EDITAR ANUNCI</h1>
                        <p class="text-brand-neon/70 text-xs mt-1 font-bold">{{ sale.motorcycle?.brand }} {{ sale.motorcycle?.model }}</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">

                <!-- Especificacions tècniques -->
                <div class="bg-brand-surface border border-brand-dark rounded-xl p-5 shadow-xl space-y-4">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-700 pb-2">{{ $t('sales.moto_specs') }}</h2>
                    
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
                                <option value="">{{ $t('common.undefined') }}</option>
                                <option value="AM">AM</option><option value="A1">A1</option><option value="A2">A2</option><option value="A">A</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">{{ $t('sales.style') }}</label>
                        <select v-model="form.type" class="w-full text-sm rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon">
                            <option value="">{{ $t('common.undefined') }}</option>
                            <option value="Naked">Naked</option>
                            <option value="Sport">Sport / R</option>
                            <option value="Trail">Trail / Adventure</option>
                            <option value="Custom">Custom / Cruiser</option>
                            <option value="Scooter">Scooter / Maxi</option>
                            <option value="Touring">Touring</option>
                            <option value="Off-Road">Off-Road</option>
                            <option value="Classic">{{ $t('sales.classic') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">{{ $t('sales.equipped_extras') }}</label>
                        <textarea v-model="form.extras" rows="2" class="w-full text-sm rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon resize-none"></textarea>
                    </div>
                </div>

                <!-- Dades de l'anunci -->
                <div class="bg-brand-surface border border-brand-dark rounded-xl p-5 shadow-xl space-y-5">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-700 pb-2">{{ $t('sales.listing_data') }}</h2>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">{{ $t('sales.title') }}</label>
                        <input v-model="form.title" type="text" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0">
                        <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">{{ $t('sales.price_eur') }}</label>
                            <input v-model="form.price" type="number" step="0.01" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0 text-brand-neon font-bold text-lg">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">{{ $t('sales.location') }}</label>
                            <input v-model="form.location" type="text" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">{{ $t('routes.description_label') }}</label>
                        <textarea v-model="form.description" rows="5" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0 resize-none"></textarea>
                    </div>

                    <div class="bg-brand-black p-3 rounded-lg border border-brand-dark flex items-start gap-3">
                        <input v-model="form.show_history" type="checkbox" id="show_history" class="mt-1 bg-brand-surface border-brand-dark rounded text-brand-neon focus:ring-0 cursor-pointer">
                        <div>
                            <label for="show_history" class="text-sm font-bold text-white block cursor-pointer">Compartir historial de manteniment?</label>
                            <p class="text-[10px] text-gray-400">Si actives això, els compradors podran veure el registre de revisions i factures que hagis desat.</p>
                        </div>
                    </div>
                </div>

                <!-- Fotos existents -->
                <div v-if="sale.images && sale.images.length > 0" class="bg-brand-surface border border-brand-dark rounded-xl p-5 shadow-xl">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-700 pb-2 mb-4">{{ $t('sales.current_photos') }}</h2>
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
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-700 pb-2 mb-4">{{ $t('sales.add_new_photos') }}</h2>
                    <div class="border-2 border-dashed border-brand-dark hover:border-brand-neon rounded-xl p-5 text-center transition cursor-pointer bg-brand-black" @click="$refs.photoInput.click()">
                        <p class="text-sm text-gray-500">{{ $t('sales.tap_photos', { n: 8 - (sale.images?.length || 0) }) }}</p>
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
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-700 pb-2 mb-4">{{ $t('sales.sale_status') }}</h2>
                    <div class="flex flex-col gap-3">
                        <select v-model="form.state" class="w-full bg-brand-black border-brand-dark rounded-lg text-white font-bold focus:border-brand-neon focus:ring-brand-neon p-3">
                            <option value="actiu">{{ $t('sales.state_active') }}</option>
                            <option value="actiu (reservat) (nou)">{{ $t('sales.state_reserved') }}</option>
                            <option value="venuda">{{ $t('sales.state_sold') }}</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <button type="submit" :disabled="form.processing" class="w-full bg-brand-neon text-brand-black font-black uppercase tracking-wider py-4 rounded-xl hover:bg-white transition disabled:opacity-50">
                        {{ $t('sales.update_btn') }}
                    </button>
                    <button type="button" @click="destroy" class="w-full text-red-500 hover:text-red-400 hover:bg-brand-black py-3 rounded-xl transition text-sm border border-red-900/30">
                        {{ $t('sales.delete_btn') }}
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
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

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
    show_history: Boolean(props.sale.show_history),
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
    if (confirm(t('common.confirm_delete'))) {
        form.delete(route('sales.destroy', props.sale.id));
    }
};
</script>

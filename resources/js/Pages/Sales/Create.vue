<template>
    <AppLayout :title="$t('sales.put_on_sale')">
        <div class="max-w-xl mx-auto px-6 py-6 pb-24 cc-fade-in">

            <header class="flex items-center gap-3 mb-8">
                <button type="button" @click="goBack" class="cc-icon-btn" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </button>
                <h1 class="cc-title flex-1 truncate">{{ $t('sales.put_on_sale') }}</h1>
            </header>

            <div v-if="motorcycles.length === 0" class="py-16 text-center">
                <p class="text-base font-semibold text-gray-300">{{ $t('sales.no_motos_avail') }}</p>
                <p class="mt-2 text-sm text-gray-500">{{ $t('sales.no_motos_desc') }}</p>
                <Link :href="route('motorcycles.create')" class="cc-btn-text mt-4 inline-flex">
                    {{ $t('sales.add_moto_btn') }}
                </Link>
            </div>

            <form v-else @submit.prevent="submit">

                <div v-if="Object.keys(form.errors).length > 0" class="mb-8">
                    <ul class="space-y-1">
                        <li v-for="(error, field) in form.errors" :key="field" class="text-red-400 text-sm">{{ error }}</li>
                    </ul>
                </div>

                <section class="space-y-4">
                    <p class="cc-section-label">{{ $t('sales.the_moto') }}</p>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('sales.which_moto') }}</label>
                        <select v-model="form.motorcycle_id" :class="inputClass(form.errors.motorcycle_id)">
                            <option :value="null" disabled>{{ $t('sales.select_moto') }}</option>
                            <option v-for="moto in motorcycles" :key="moto.id" :value="moto.id">
                                {{ moto.brand }} {{ moto.model }} ({{ moto.year }}) — {{ Number(moto.current_km || 0).toLocaleString() }} km
                            </option>
                        </select>
                    </div>

                    <template v-if="form.motorcycle_id">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">cc</label>
                                <input v-model="form.cc" type="number" :class="inputClass()">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">CV</label>
                                <input v-model="form.power_cv" type="number" :class="inputClass()">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('sales.license') }}</label>
                                <select v-model="form.license_type" :class="inputClass()">
                                    <option value="">—</option>
                                    <option value="AM">AM</option>
                                    <option value="A1">A1</option>
                                    <option value="A2">A2</option>
                                    <option value="A">A</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('sales.style') }}</label>
                                <select v-model="form.type" :class="inputClass()">
                                    <option value="">—</option>
                                    <option value="Naked">Naked</option>
                                    <option value="Sport">Sport</option>
                                    <option value="Trail">Trail</option>
                                    <option value="Custom">Custom</option>
                                    <option value="Scooter">Scooter</option>
                                    <option value="Touring">Touring</option>
                                    <option value="Off-Road">Off-Road</option>
                                    <option value="Classic">Classic</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('sales.equipped_extras') }}</label>
                            <textarea v-model="form.extras" rows="2" :class="[inputClass(), 'resize-none']"></textarea>
                        </div>
                    </template>
                </section>

                <section v-if="form.motorcycle_id" class="mt-10 space-y-4">
                    <p class="cc-section-label">{{ $t('sales.the_ad') }}</p>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('sales.ad_title') }}</label>
                        <input v-model="form.title" type="text" :placeholder="$t('sales.ad_title_ph')" :class="inputClass(form.errors.title)">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('sales.price_eur') }}</label>
                            <input v-model="form.price" type="number" step="1" :class="inputClass(form.errors.price)">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('sales.location') }}</label>
                            <input v-model="form.location" type="text" :placeholder="$t('sales.location_ph')" :class="inputClass(form.errors.location)">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('sales.seller_comments') }}</label>
                        <textarea v-model="form.description" rows="4" :placeholder="$t('sales.desc_ph')" :class="[inputClass(), 'resize-none']"></textarea>
                    </div>

                    <label class="flex items-start gap-3 cursor-pointer">
                        <input v-model="form.show_history" type="checkbox" class="mt-1 rounded border-white/20 bg-white/[0.04] text-white focus:ring-0">
                        <span>
                            <span class="block text-sm text-gray-200">{{ $t('sales.share_history') }}</span>
                            <span class="block text-xs text-gray-500 mt-0.5">{{ $t('sales.share_history_hint') }}</span>
                        </span>
                    </label>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('sales.item_photos') }}</label>
                        <input
                            type="file"
                            multiple
                            accept="image/*"
                            @change="handleImages"
                            class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-white/[0.06] file:text-white hover:file:bg-white/[0.1] transition cursor-pointer"
                        >
                        <div v-if="imagePreviews.length" class="grid grid-cols-3 gap-2 mt-3">
                            <div v-for="(preview, i) in imagePreviews" :key="i" class="relative">
                                <img :src="preview" alt="" class="w-full h-24 object-cover rounded-xl border border-white/[0.06]">
                                <button type="button" @click="removePreview(i)" class="absolute top-1 right-1 w-6 h-6 rounded-full bg-black/70 text-white text-xs">✕</button>
                            </div>
                        </div>
                    </div>
                </section>

                <button
                    v-if="form.motorcycle_id"
                    type="submit"
                    :disabled="form.processing"
                    class="cc-btn-primary w-full mt-12 py-3.5"
                >
                    {{ form.processing ? $t('sales.publishing') : $t('sales.publish_btn') }}
                </button>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { smartBack } from '@/Composables/navigationStack.js';

const props = defineProps({ motorcycles: Array });

const goBack = () => smartBack(route('sales.index'));

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

const inputClass = (error) =>
    error
        ? 'w-full rounded-xl bg-brand-black border-red-500 ring-1 ring-red-500 text-white focus:border-red-400 focus:ring-0'
        : 'w-full rounded-xl bg-white/[0.04] border-white/[0.08] text-white focus:border-white/30 focus:ring-0';

watch(() => form.motorcycle_id, (newId) => {
    if (!newId) return;
    const moto = props.motorcycles.find((m) => m.id === newId);
    if (!moto) return;
    form.cc = moto.cc || '';
    form.power_cv = moto.power_cv || '';
    form.license_type = moto.license_type || '';
    form.type = moto.type || '';
    form.extras = moto.extras || '';
    if (!form.title) form.title = `${moto.brand} ${moto.model} (${moto.year})`;
});

const handleImages = (e) => {
    Array.from(e.target.files || []).forEach((file) => {
        if (selectedFiles.value.length >= 8) return;
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

const submit = () => form.post(route('sales.store'), { forceFormData: true });
</script>

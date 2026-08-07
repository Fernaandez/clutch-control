<template>
    <AppLayout :title="$t('sales.edit_listing_btn')">
        <div class="max-w-xl mx-auto px-6 py-6 pb-24 cc-fade-in">

            <header class="flex items-center gap-3 mb-8">
                <button type="button" @click="goBack" class="cc-icon-btn" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </button>
                <h1 class="cc-title flex-1 truncate">{{ $t('sales.edit_listing_btn') }}</h1>
            </header>

            <form @submit.prevent="submit">

                <div v-if="Object.keys(form.errors).length > 0" class="mb-8">
                    <ul class="space-y-1">
                        <li v-for="(error, field) in form.errors" :key="field" class="text-red-400 text-sm">{{ error }}</li>
                    </ul>
                </div>

                <section class="space-y-4">
                    <p class="cc-section-label">{{ $t('sales.moto_specs') }}</p>
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
                </section>

                <section class="mt-10 space-y-4">
                    <p class="cc-section-label">{{ $t('sales.listing_data') }}</p>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('sales.ad_title') }}</label>
                        <input v-model="form.title" type="text" :class="inputClass(form.errors.title)">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('sales.price_eur') }}</label>
                            <input v-model="form.price" type="number" step="1" :class="inputClass(form.errors.price)">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('sales.location') }}</label>
                            <input v-model="form.location" type="text" :class="inputClass(form.errors.location)">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('sales.seller_comments') }}</label>
                        <textarea v-model="form.description" rows="4" :class="[inputClass(), 'resize-none']"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('sales.sale_status') }}</label>
                        <select v-model="form.state" :class="inputClass()">
                            <option value="actiu">{{ $t('sales.state_active') }}</option>
                            <option value="reservat">{{ $t('sales.state_reserved') }}</option>
                            <option value="venuda">{{ $t('sales.state_sold') }}</option>
                            <option value="pausat">{{ $t('sales.state_paused') }}</option>
                        </select>
                    </div>

                    <label class="flex items-start gap-3 cursor-pointer">
                        <input v-model="form.show_history" type="checkbox" class="mt-1 rounded border-white/20 bg-white/[0.04] text-white focus:ring-0">
                        <span>
                            <span class="block text-sm text-gray-200">{{ $t('sales.share_history') }}</span>
                            <span class="block text-xs text-gray-500 mt-0.5">{{ $t('sales.share_history_hint') }}</span>
                        </span>
                    </label>
                </section>

                <section class="mt-10 space-y-4">
                    <p class="cc-section-label">{{ $t('sales.current_photos') }}</p>

                    <div v-if="sale.images?.length" class="grid grid-cols-3 gap-2">
                        <div v-for="img in sale.images" :key="img.id" class="relative group">
                            <img :src="$page.props.storageUrl + '/' + img.image_path" alt="" class="w-full h-24 object-cover rounded-xl border border-white/[0.06]">
                            <Link
                                :href="route('sales.images.destroy', { sale: sale.id, image: img.id })"
                                method="delete"
                                as="button"
                                class="absolute top-1 right-1 w-6 h-6 rounded-full bg-black/70 text-white text-xs opacity-0 group-hover:opacity-100 transition"
                            >✕</Link>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('sales.add_new_photos') }}</label>
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

                <div class="mt-12 space-y-3">
                    <button type="submit" :disabled="form.processing" class="cc-btn-primary w-full py-3.5">
                        {{ $t('sales.update_btn') }}
                    </button>
                    <button type="button" @click="destroy" class="cc-btn-text w-full justify-center border-red-500/25 text-red-400">
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
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import { smartBack } from '@/Composables/navigationStack.js';

const { t } = useI18n();
const props = defineProps({ sale: Object });

const goBack = () => smartBack(route('sales.show', props.sale.id));

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

const inputClass = (error) =>
    error
        ? 'w-full rounded-xl bg-brand-black border-red-500 ring-1 ring-red-500 text-white focus:border-red-400 focus:ring-0'
        : 'w-full rounded-xl bg-white/[0.04] border-white/[0.08] text-white focus:border-white/30 focus:ring-0';

const handleImages = (e) => {
    const remaining = 8 - (props.sale.images?.length || 0) - selectedFiles.value.length;
    Array.from(e.target.files || []).slice(0, remaining).forEach((file) => {
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

const submit = () => form.post(route('sales.update', props.sale.id), { forceFormData: true });

const destroy = () => {
    if (confirm(t('sales.delete_confirm'))) {
        form.delete(route('sales.destroy', props.sale.id));
    }
};
</script>

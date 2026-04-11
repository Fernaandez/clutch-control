<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto px-4 py-8 pb-24">
            
            <div class="mb-6 flex items-center gap-4">
                <button type="button" @click="goBack" class="inline-flex items-center justify-center w-10 h-10 flex-shrink-0 rounded-full bg-brand-dark border border-brand-neon/50 text-brand-neon hover:bg-brand-neon hover:text-brand-black transition shadow-[0_0_10px_rgba(12,225,181,0.2)]" aria-label="Enrere">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                </button>
                <h1 class="text-2xl font-bold text-white">{{ $t('motorcycles.edit_title') }}</h1>
            </div>

            <div class="bg-brand-surface p-6 rounded-xl border border-brand-dark shadow-lg">
                <form @submit.prevent="submit" class="space-y-6">

                    <!-- Errors globals -->
                    <div v-if="Object.keys(form.errors).length > 0" class="p-4 bg-red-500/10 border border-red-500/30 rounded-xl">
                        <p class="text-red-500 font-black text-xs uppercase tracking-widest mb-2">{{ $t('motorcycles.check_errors') }}</p>
                        <ul class="list-disc pl-5 text-red-400 text-sm space-y-1">
                            <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
                        </ul>
                    </div>

                    <!-- DADES -->
                    <div>
                        <!-- Selector Marca / Model amb autocomplete -->
                        <div class="mb-4">
                            <MotorcycleBrandModelSelector
                                :initial-brand="form.brand"
                                :initial-model="form.model"
                                :brand-error="form.errors.brand"
                                :model-error="form.errors.model"
                                @update:brand="form.brand = $event"
                                @update:model="form.model = $event"
                                @update:cc="val => { if (!form.cc) form.cc = val }"
                                @update:cv="val => { if (!form.power_cv) form.power_cv = val }"
                            />
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('motorcycles.plate') }}</label>
                            <div class="flex gap-2 mb-2">
                                <button type="button"
                                    @click="plateType = 'moto'"
                                    :class="plateType === 'moto' ? 'flex-1 py-1.5 text-xs font-bold uppercase rounded border border-brand-neon bg-brand-neon/10 text-brand-neon' : 'flex-1 py-1.5 text-xs font-bold uppercase rounded border border-brand-dark text-gray-500 hover:border-gray-500 hover:text-gray-300 transition'"
                                >{{ $t('motorcycles.plate_moto') }}</button>
                                <button type="button"
                                    @click="plateType = 'ciclomotor'"
                                    :class="plateType === 'ciclomotor' ? 'flex-1 py-1.5 text-xs font-bold uppercase rounded border border-brand-neon bg-brand-neon/10 text-brand-neon' : 'flex-1 py-1.5 text-xs font-bold uppercase rounded border border-brand-dark text-gray-500 hover:border-gray-500 hover:text-gray-300 transition'"
                                >{{ $t('motorcycles.plate_ciclomotor') }}</button>
                            </div>
                            <input v-model="form.plate" type="text"
                                :class="inputClass(form.errors.plate)"
                                class="uppercase" maxlength="15"
                                @input="form.plate = form.plate.toUpperCase()">
                            <p v-if="form.errors.plate" class="text-red-400 text-xs mt-1">⚠ {{ form.errors.plate }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('motorcycles.photo') }}</label>
                            <div v-if="moto.photo" class="mb-3">
                                <img :src="$page.props.storageUrl + '/' + moto.photo" alt="Foto Moto" class="h-40 w-full object-cover rounded-xl border border-brand-dark">
                            </div>
                            <input @change="e => form.photo = e.target.files[0]" type="file" accept="image/*"
                                class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-brand-base/20 file:text-brand-neon hover:file:bg-brand-base/30 transition cursor-pointer">
                            <p v-if="form.errors.photo" class="text-red-400 text-xs mt-1">⚠ {{ form.errors.photo }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('motorcycles.year') }}</label>
                                <input v-model="form.year" type="number" min="1900" :max="currentYear" :class="inputClass(form.errors.year)">
                                <p v-if="form.errors.year" class="text-red-400 text-xs mt-1">⚠ {{ form.errors.year }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('motorcycles.km') }}</label>
                                <input v-model="form.current_km" type="number" step="1" min="0" :class="inputClass(form.errors.current_km)">
                                <p v-if="form.errors.current_km" class="text-red-400 text-xs mt-1">⚠ {{ form.errors.current_km }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- ESPECIFICACIONS -->
                    <div class="mt-6">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('motorcycles.cc') }}</label>
                                <input v-model="form.cc" type="number" min="0" max="9999" :class="inputClass(form.errors.cc)">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('motorcycles.cv') }}</label>
                                <input v-model="form.power_cv" type="number" min="0" max="999" :class="inputClass(form.errors.power_cv)">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('motorcycles.license') }}</label>
                                <select v-model="form.license_type" :class="inputClass(form.errors.license_type)">
                                    <option value="">{{ $t('motorcycles.license_placeholder') }}</option>
                                    <option value="AM">{{ $t('motorcycles.license_am') }}</option>
                                    <option value="A1">{{ $t('motorcycles.license_a1') }}</option>
                                    <option value="A2">{{ $t('motorcycles.license_a2') }}</option>
                                    <option value="A">{{ $t('motorcycles.license_a') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('motorcycles.moto_style') }}</label>
                                <select v-model="form.type" :class="inputClass(form.errors.type)">
                                    <option value="">{{ $t('motorcycles.style_placeholder') }}</option>
                                    <option value="Naked">Naked</option>
                                    <option value="Sport">Sport / R</option>
                                    <option value="Trail">Trail / Adventure</option>
                                    <option value="Custom">Custom / Cruiser</option>
                                    <option value="Scooter">Scooter / Maxi-Scooter</option>
                                    <option value="Touring">Touring</option>
                                    <option value="Off-Road">Off-Road / Enduro</option>
                                    <option value="Classic">{{ $t('motorcycles.style_classic') }}</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('motorcycles.extras') }}</label>
                            <textarea v-model="form.extras" rows="3" :class="inputClass(form.errors.extras)" class="resize-none"></textarea>
                        </div>
                    </div>

                    <div class="pt-6 flex flex-col gap-4 border-t border-brand-dark">
                        <button type="submit" :disabled="form.processing" class="w-full bg-brand-base hover:bg-brand-neon text-brand-black font-black uppercase tracking-wider py-4 rounded-xl transition disabled:opacity-50">
                            {{ form.processing ? $t('motorcycles.saving') : $t('motorcycles.update') }}
                        </button>
                        <button type="button" @click="destroy" class="w-full text-red-500 hover:text-red-400 hover:bg-brand-black py-2 rounded transition text-sm border border-red-900/20">
                            {{ $t('motorcycles.delete_moto') }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import MotorcycleBrandModelSelector from '@/Components/MotorcycleBrandModelSelector.vue';
import { smartBack } from '@/Composables/navigationStack.js';

const { t } = useI18n();
const props = defineProps({ moto: Object });

const goBack = () => smartBack(route('motorcycles.index'));

const currentYear = new Date().getFullYear();

const detectPlateType = (plate) => {
    if (!plate) return 'moto';
    return /^[A-Z]{1,2}[-\s]/.test(plate) ? 'ciclomotor' : 'moto';
};

const plateType = ref(detectPlateType(props.moto.plate));

const plateHint = computed(() => {
    if (plateType.value === 'ciclomotor') return {
        placeholder: 'Ex: MA-1234-A',
        format: 'CC-1234-X o L-1234-A',
        desc: 'Format provincial (varia segons l\'ajuntament)'
    };
    return {
        placeholder: 'Ex: 1234 ABC',
        format: '1234 ABC o B-1234-CD',
        desc: 'Format nacional estàndard'
    };
});

const inputClass = (error) =>
    error
        ? 'w-full rounded bg-brand-black border-red-500 ring-1 ring-red-500 text-white focus:border-red-400 focus:ring-red-400'
        : 'w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-0';

const form = useForm({
    brand: props.moto.brand,
    model: props.moto.model,
    plate: props.moto.plate,
    year: props.moto.year,
    current_km: props.moto.current_km,
    cc: props.moto.cc || '',
    power_cv: props.moto.power_cv || '',
    license_type: props.moto.license_type || '',
    type: props.moto.type || '',
    extras: props.moto.extras || '',
    photo: null,
    _method: 'put'
});

const submit = () => {
    form.post(route('motorcycles.update', props.moto.id), { forceFormData: true });
};

const destroy = () => {
    if (confirm(t('motorcycles.delete_confirm'))) {
        form.delete(route('motorcycles.destroy', props.moto.id));
    }
};
</script>

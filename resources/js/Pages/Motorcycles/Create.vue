<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto px-4 py-8 pb-24">
            
            <div class="mb-6 flex items-center gap-4">
                <Link :href="route('motorcycles.index')" class="inline-flex items-center justify-center w-10 h-10 flex-shrink-0 rounded-full bg-brand-dark border border-brand-neon/50 text-brand-neon hover:bg-brand-neon hover:text-brand-black transition shadow-[0_0_10px_rgba(12,225,181,0.2)]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                </Link>
                <h1 class="text-2xl font-bold text-white">{{ $t('motorcycles.add_title') }}</h1>
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
                                :brand-error="form.errors.brand"
                                :model-error="form.errors.model"
                                @update:brand="form.brand = $event"
                                @update:model="form.model = $event"
                                @update:cc="form.cc = $event"
                                @update:cv="form.power_cv = $event"
                            />
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('motorcycles.photo') }}</label>
                            <input @change="e => form.photo = e.target.files[0]" type="file" accept="image/*"
                                class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-brand-base/20 file:text-brand-neon hover:file:bg-brand-base/30 transition cursor-pointer">
                            <p v-if="form.errors.photo" class="text-red-400 text-xs mt-1 flex items-center gap-1"><span>⚠</span> {{ form.errors.photo }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('motorcycles.year') }}</label>
                                <input v-model="form.year" type="number" min="1900" :max="currentYear" :class="inputClass(form.errors.year)">
                                <p v-if="form.errors.year" class="text-red-400 text-xs mt-1 flex items-center gap-1"><span>⚠</span> {{ form.errors.year }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('motorcycles.km') }}</label>
                                <input v-model="form.current_km" type="number" step="0.01" min="0" :class="inputClass(form.errors.current_km)">
                                <p v-if="form.errors.current_km" class="text-red-400 text-xs mt-1 flex items-center gap-1"><span>⚠</span> {{ form.errors.current_km }}</p>
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

                    <!-- DOCUMENTACIÓ -->
                    <div class="mt-6 pt-6 border-t border-brand-dark">
                        <h3 class="text-sm font-bold text-brand-neon uppercase tracking-widest mb-4">{{ $t('motorcycles.documentation') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('motorcycles.insurance_company') }}</label>
                                <input v-model="form.insurance_company" type="text" :class="inputClass(form.errors.insurance_company)">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('motorcycles.insurance_policy') }}</label>
                                <input v-model="form.insurance_policy_number" type="text" :class="inputClass(form.errors.insurance_policy_number)">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('motorcycles.insurance_expires') }}</label>
                                <input v-model="form.insurance_expires_at" type="date" :class="inputClass(form.errors.insurance_expires_at)">
                                <p v-if="form.errors.insurance_expires_at" class="text-red-400 text-xs mt-1">⚠ {{ form.errors.insurance_expires_at }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('motorcycles.itv_expires') }}</label>
                                <input v-model="form.itv_expires_at" type="date" :class="inputClass(form.errors.itv_expires_at)">
                                <p v-if="form.errors.itv_expires_at" class="text-red-400 text-xs mt-1">⚠ {{ form.errors.itv_expires_at }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('motorcycles.itv_last_passed') }}</label>
                                <input v-model="form.itv_last_passed_at" type="date" :class="inputClass(form.errors.itv_last_passed_at)">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-brand-dark">
                        <button type="submit" :disabled="form.processing" class="w-full bg-brand-base hover:bg-brand-neon text-brand-black font-black uppercase tracking-wider py-4 rounded-xl transition duration-300 transform hover:-translate-y-1 disabled:opacity-50">
                            {{ form.processing ? $t('motorcycles.saving') : $t('motorcycles.save') }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MotorcycleBrandModelSelector from '@/Components/MotorcycleBrandModelSelector.vue';

const currentYear = new Date().getFullYear();

const form = useForm({
    brand: '',
    model: '',
    year: '',
    current_km: '',
    cc: '',
    power_cv: '',
    license_type: '',
    type: '',
    extras: '',
    insurance_company: '',
    insurance_policy_number: '',
    insurance_expires_at: '',
    itv_expires_at: '',
    itv_last_passed_at: '',
    photo: null
});

const inputClass = (error) =>
    error
        ? 'w-full rounded bg-brand-black border-red-500 ring-1 ring-red-500 text-white focus:border-red-400 focus:ring-red-400'
        : 'w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-0';

const submit = () => {
    form.post(route('motorcycles.store'), { forceFormData: true });
};
</script>

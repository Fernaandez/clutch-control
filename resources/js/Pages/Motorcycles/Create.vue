<template>
    <AppLayout>
        <div class="max-w-xl mx-auto px-6 py-6 pb-24 cc-fade-in">

            <header class="flex items-center gap-3 mb-6">
                <Link :href="route('motorcycles.index')" class="cc-icon-btn" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </Link>
                <h1 class="cc-title flex-1 truncate">{{ $t('motorcycles.add_title') }}</h1>
            </header>

            <form @submit.prevent="submit">

                <div v-if="Object.keys(form.errors).length > 0" class="mb-8">
                    <ul class="space-y-1">
                        <li v-for="(error, field) in form.errors" :key="field" class="text-red-400 text-sm">{{ error }}</li>
                    </ul>
                </div>

                <section>
                    <MotorcycleBrandModelSelector
                        :brand-error="form.errors.brand"
                        :model-error="form.errors.model"
                        @update:brand="form.brand = $event"
                        @update:model="form.model = $event"
                        @update:cc="form.cc = $event"
                        @update:cv="form.power_cv = $event"
                    />
                </section>

                <section class="mt-10 space-y-4">
                    <p class="cc-section-label">{{ $t('motorcycles.photo') }}</p>

                    <div>
                        <input
                            @change="e => form.photo = e.target.files[0]"
                            type="file"
                            accept="image/*"
                            class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-white/[0.06] file:text-white hover:file:bg-white/[0.1] transition cursor-pointer"
                        >
                        <p v-if="form.errors.photo" class="text-red-400 text-xs mt-1">{{ form.errors.photo }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('motorcycles.year') }}</label>
                            <input v-model="form.year" type="number" min="1900" :max="currentYear" :class="inputClass(form.errors.year)">
                            <p v-if="form.errors.year" class="text-red-400 text-xs mt-1">{{ form.errors.year }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('motorcycles.km') }}</label>
                            <input v-model="form.current_km" type="number" step="0.01" min="0" :class="inputClass(form.errors.current_km)">
                            <p v-if="form.errors.current_km" class="text-red-400 text-xs mt-1">{{ form.errors.current_km }}</p>
                        </div>
                    </div>
                </section>

                <section class="mt-10 space-y-4">
                    <p class="cc-section-label">{{ $t('motorcycles.moto_style') }}</p>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('motorcycles.cc') }}</label>
                            <input v-model="form.cc" type="number" min="0" max="9999" :class="inputClass(form.errors.cc)">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('motorcycles.cv') }}</label>
                            <input v-model="form.power_cv" type="number" min="0" max="999" :class="inputClass(form.errors.power_cv)">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('motorcycles.license') }}</label>
                        <select v-model="form.license_type" :class="inputClass(form.errors.license_type)">
                            <option value="">{{ $t('motorcycles.license_placeholder') }}</option>
                            <option value="AM">{{ $t('motorcycles.license_am') }}</option>
                            <option value="A1">{{ $t('motorcycles.license_a1') }}</option>
                            <option value="A2">{{ $t('motorcycles.license_a2') }}</option>
                            <option value="A">{{ $t('motorcycles.license_a') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('motorcycles.moto_style') }}</label>
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

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('motorcycles.extras') }}</label>
                        <textarea v-model="form.extras" rows="3" :class="inputClass(form.errors.extras)" class="resize-none"></textarea>
                    </div>
                </section>

                <section class="mt-10 space-y-4">
                    <p class="cc-section-label">{{ $t('motorcycles.documentation') }}</p>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('motorcycles.insurance_company') }}</label>
                        <input v-model="form.insurance_company" type="text" :class="inputClass(form.errors.insurance_company)">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('motorcycles.insurance_policy') }}</label>
                        <input v-model="form.insurance_policy_number" type="text" :class="inputClass(form.errors.insurance_policy_number)">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('motorcycles.insurance_expires') }}</label>
                            <input v-model="form.insurance_expires_at" type="date" :class="inputClass(form.errors.insurance_expires_at)">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('motorcycles.itv_expires') }}</label>
                            <input v-model="form.itv_expires_at" type="date" :class="inputClass(form.errors.itv_expires_at)">
                            <p v-if="form.errors.itv_expires_at" class="text-red-400 text-xs mt-1">{{ form.errors.itv_expires_at }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('motorcycles.itv_last_passed') }}</label>
                        <input v-model="form.itv_last_passed_at" type="date" :class="inputClass(form.errors.itv_last_passed_at)">
                    </div>
                </section>

                <button type="submit" :disabled="form.processing" class="cc-btn-primary w-full mt-12">
                    {{ form.processing ? $t('motorcycles.saving') : $t('motorcycles.save') }}
                </button>

            </form>
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
        : 'w-full rounded-xl bg-white/[0.04] border-white/[0.08] text-white focus:border-white/30 focus:ring-0';

const submit = () => {
    form.post(route('motorcycles.store'), { forceFormData: true });
};
</script>

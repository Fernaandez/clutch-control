<template>
    <AppLayout :current-moto-id="moto.id">
        <div class="max-w-2xl mx-auto px-4 py-8 pb-24">
            <div class="mb-6 flex items-center gap-4">
                <button type="button" @click="goBack" class="inline-flex items-center justify-center w-10 h-10 flex-shrink-0 rounded-full bg-brand-dark border border-brand-neon/50 text-brand-neon hover:bg-brand-neon hover:text-brand-black transition shadow-[0_0_10px_rgba(12,225,181,0.2)]" aria-label="Enrere">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                </button>
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $t('motorcycles.documentation_edit') }}</h1>
                    <p class="text-brand-muted text-sm">{{ moto.brand }} {{ moto.model }}</p>
                </div>
            </div>

            <div class="bg-brand-surface p-6 rounded-xl border border-brand-dark shadow-lg">
                <form @submit.prevent="submit" class="space-y-6">
                    <div v-if="Object.keys(form.errors).length > 0" class="p-4 bg-red-500/10 border border-red-500/30 rounded-xl">
                        <p class="text-red-500 font-black text-xs uppercase tracking-widest mb-2">{{ $t('motorcycles.check_errors') }}</p>
                        <ul class="list-disc pl-5 text-red-400 text-sm space-y-1">
                            <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
                        </ul>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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

                    <div class="pt-4 flex flex-col sm:flex-row gap-3 border-t border-brand-dark">
                        <button type="submit" :disabled="form.processing" class="flex-1 bg-brand-base hover:bg-brand-neon text-brand-black font-black uppercase tracking-wider py-4 rounded-xl transition disabled:opacity-50">
                            {{ form.processing ? $t('motorcycles.saving') : $t('motorcycles.save') }}
                        </button>
                        <Link :href="route('motorcycles.documentation.show', moto.id)" class="flex-1 text-center text-gray-400 hover:text-white py-4 rounded-xl border border-brand-dark hover:border-brand-neon transition text-sm font-bold uppercase tracking-wider">
                            {{ $t('common.cancel') }}
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { smartBack } from '@/Composables/navigationStack.js';

const props = defineProps({ moto: Object });

const goBack = () => smartBack(route('motorcycles.documentation.show', props.moto.id));

const formatDate = (val) => val ? String(val).slice(0, 10) : '';

const inputClass = (error) =>
    error
        ? 'w-full rounded bg-brand-black border-red-500 ring-1 ring-red-500 text-white focus:border-red-400 focus:ring-red-400'
        : 'w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-0';

const form = useForm({
    insurance_company: props.moto.insurance_company || '',
    insurance_policy_number: props.moto.insurance_policy_number || '',
    insurance_expires_at: formatDate(props.moto.insurance_expires_at),
    itv_expires_at: formatDate(props.moto.itv_expires_at),
    itv_last_passed_at: formatDate(props.moto.itv_last_passed_at),
});

const submit = () => {
    form.put(route('motorcycles.documentation.update', props.moto.id));
};
</script>

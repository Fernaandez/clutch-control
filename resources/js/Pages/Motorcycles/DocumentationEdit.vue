<template>
    <AppLayout :current-moto-id="moto.id">
        <div class="max-w-xl mx-auto px-6 py-8 pb-24 cc-fade-in">

            <header class="flex items-center gap-3 mb-8">
                <button type="button" @click="goBack" class="cc-icon-btn" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </button>
                <div class="min-w-0 flex-1">
                    <h1 class="cc-title truncate">{{ $t('motorcycles.documentation_edit') }}</h1>
                    <p class="text-sm text-gray-500 truncate mt-1">{{ moto.brand }} {{ moto.model }}</p>
                </div>
            </header>

            <form @submit.prevent="submit" class="space-y-10">
                <div v-if="Object.keys(form.errors).length > 0" class="p-4 rounded-xl bg-red-500/[0.06] border border-red-500/20">
                    <p class="text-red-400 text-sm font-medium mb-2">{{ $t('motorcycles.check_errors') }}</p>
                    <ul class="list-disc pl-5 text-red-400 text-sm space-y-1">
                        <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
                    </ul>
                </div>

                <section>
                    <p class="cc-section-label mb-4">{{ $t('motorcycles.insurance_company') }}</p>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1.5">{{ $t('motorcycles.insurance_company') }}</label>
                            <input v-model="form.insurance_company" type="text" :class="inputClass(form.errors.insurance_company)">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1.5">{{ $t('motorcycles.insurance_policy') }}</label>
                            <input v-model="form.insurance_policy_number" type="text" :class="inputClass(form.errors.insurance_policy_number)">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1.5">{{ $t('motorcycles.insurance_expires') }}</label>
                            <input v-model="form.insurance_expires_at" type="date" :class="inputClass(form.errors.insurance_expires_at)">
                        </div>
                    </div>
                </section>

                <section>
                    <p class="cc-section-label mb-4">ITV</p>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1.5">{{ $t('motorcycles.itv_expires') }}</label>
                            <input v-model="form.itv_expires_at" type="date" :class="inputClass(form.errors.itv_expires_at)">
                            <p v-if="form.errors.itv_expires_at" class="text-red-400 text-xs mt-1">{{ form.errors.itv_expires_at }}</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1.5">{{ $t('motorcycles.itv_last_passed') }}</label>
                            <input v-model="form.itv_last_passed_at" type="date" :class="inputClass(form.errors.itv_last_passed_at)">
                        </div>
                    </div>
                </section>

                <div class="pt-6 border-t border-white/[0.06] space-y-3">
                    <button type="submit" :disabled="form.processing" class="cc-btn-primary w-full py-3.5">
                        {{ form.processing ? $t('common.saving') : $t('common.save') }}
                    </button>
                    <Link :href="route('motorcycles.documentation.show', moto.id)" class="cc-btn-ghost w-full">
                        {{ $t('common.cancel') }}
                    </Link>
                </div>
            </form>
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
        ? 'w-full rounded-xl bg-white/[0.04] border-red-500 ring-1 ring-red-500 text-white focus:border-red-400 focus:ring-red-400'
        : 'w-full rounded-xl bg-white/[0.04] border-white/[0.08] text-white focus:border-white/30 focus:ring-0';

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

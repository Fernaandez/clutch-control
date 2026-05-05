<template>
    <div v-if="reportableId" class="inline-flex">
        <button type="button" :class="buttonClass" @click="openModal">
            {{ label }}
        </button>

        <div v-if="isOpen" class="fixed inset-0 z-[6000] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="closeModal"></div>
            <div class="relative w-full max-w-sm rounded-xl border border-red-500/40 bg-brand-surface p-5 shadow-2xl">
                <button type="button" class="absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded-full border border-red-500/40 bg-brand-dark text-red-400 transition hover:bg-red-500 hover:text-white" @click="closeModal" aria-label="Tancar">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                </button>

                <h3 class="pr-8 text-xl font-black text-white">Denunciar</h3>
                <p class="mt-1 text-sm text-gray-400">{{ contextLabel }}</p>

                <form class="mt-5 space-y-4" @submit.prevent="submit">
                    <div>
                        <label class="text-sm text-gray-400">Motiu</label>
                        <select v-model="form.reason" class="mt-1 w-full rounded-lg border-brand-dark bg-brand-black text-white focus:border-red-500 focus:ring-red-500" required>
                            <option value="spam">Spam</option>
                            <option value="harassment">Assetjament</option>
                            <option value="scam">Estafa</option>
                            <option value="inappropriate">Contingut inapropiat</option>
                            <option value="dangerous">Perillos o il·legal</option>
                            <option value="other">Altres</option>
                        </select>
                        <p v-if="form.errors.reason" class="mt-1 text-xs text-red-400">{{ form.errors.reason }}</p>
                    </div>

                    <div>
                        <label class="text-sm text-gray-400">Detalls</label>
                        <textarea v-model="form.details" rows="4" class="mt-1 w-full rounded-lg border-brand-dark bg-brand-black text-white focus:border-red-500 focus:ring-red-500" placeholder="Explica breument que passa..."></textarea>
                        <p v-if="form.errors.details" class="mt-1 text-xs text-red-400">{{ form.errors.details }}</p>
                    </div>

                    <div v-if="!currentUser">
                        <label class="text-sm text-gray-400">Email de contacte opcional</label>
                        <input v-model="form.contact_email" type="email" class="mt-1 w-full rounded-lg border-brand-dark bg-brand-black text-white focus:border-red-500 focus:ring-red-500" placeholder="tu@email.com">
                        <p v-if="form.errors.contact_email" class="mt-1 text-xs text-red-400">{{ form.errors.contact_email }}</p>
                    </div>

                    <p v-if="sent" class="rounded-lg border border-brand-neon/40 bg-brand-neon/10 p-3 text-sm font-bold text-brand-neon">
                        Denuncia enviada. Gracies.
                    </p>

                    <button type="submit" :disabled="form.processing || sent" class="w-full rounded-lg bg-red-600 py-3 text-sm font-black uppercase tracking-widest text-white transition hover:bg-red-500 disabled:opacity-60">
                        {{ form.processing ? 'Enviant...' : 'Enviar denuncia' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    reportableType: {
        type: String,
        required: true,
    },
    reportableId: {
        type: [Number, String],
        required: true,
    },
    label: {
        type: String,
        default: 'Denunciar',
    },
    contextLabel: {
        type: String,
        default: 'Aquest contingut sera revisat per un administrador.',
    },
    buttonClass: {
        type: String,
        default: 'text-xs font-bold uppercase tracking-widest text-red-400 hover:text-red-300 underline',
    },
});

const page = usePage();
const currentUser = computed(() => page.props.auth?.user || null);
const isOpen = ref(false);
const sent = ref(false);

const form = useForm({
    reportable_type: props.reportableType,
    reportable_id: props.reportableId,
    reason: 'inappropriate',
    details: '',
    contact_email: '',
});

const openModal = () => {
    sent.value = false;
    form.clearErrors();
    isOpen.value = true;
};

const closeModal = () => {
    isOpen.value = false;
};

const submit = () => {
    form.reportable_type = props.reportableType;
    form.reportable_id = props.reportableId;

    form.post(route('reports.store'), {
        preserveScroll: true,
        onSuccess: () => {
            sent.value = true;
            form.reset('details', 'contact_email');
        },
    });
};
</script>

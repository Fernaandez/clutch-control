<template>
    <Head title="Verificació de Correu" />

    <div class="min-h-screen flex flex-col justify-center items-center bg-brand-black px-6 py-12">
        
        <div class="w-full max-w-md bg-brand-surface border border-brand-dark p-8 rounded-2xl shadow-2xl text-center relative overflow-hidden">
            
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-2 bg-brand-neon rounded-b-xl shadow-[0_0_30px_rgba(12,225,181,0.8)]"></div>

            <div class="flex justify-center mb-6 mt-4">
                <div class="p-4 border-2 border-brand-neon rounded-full shadow-[0_0_15px_rgba(12,225,181,0.2)]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-brand-neon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                </div>
            </div>

            <h2 class="text-2xl font-black text-white uppercase tracking-tight mb-4">Revisa el teu <span class="text-brand-neon">Correu</span></h2>

            <div class="mb-6 text-sm text-gray-400 leading-relaxed">
                Gràcies per unir-te a la comunitat! Abans de donar-li gas, necessitem que verifiquis la teva adreça de correu. 
                <br><br>
                Fes clic a l'enllaç que t'acabem d'enviar. Si no l'has rebut (revisa l'Spam per si de cas), te n'enviarem un altre encantats.
            </div>

            <div v-if="verificationLinkSent" class="mb-6 text-xs font-bold text-brand-black bg-brand-neon p-3 rounded-lg animate-fade-in shadow-[0_0_15px_rgba(12,225,181,0.3)]">
                ✅ S'ha enviat un nou enllaç de verificació a la teva adreça!
            </div>

            <form @submit.prevent="submit" class="flex flex-col gap-4">
                <button 
                    type="submit" 
                    :disabled="form.processing" 
                    class="w-full bg-brand-dark border border-brand-neon/50 text-brand-neon px-6 py-4 rounded-xl font-black uppercase tracking-wider text-sm hover:bg-brand-neon hover:text-black transition duration-300"
                >
                    Tornar a enviar l'enllaç
                </button>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-gray-500 hover:text-white text-xs font-bold uppercase tracking-widest underline underline-offset-4 transition mt-2"
                >
                    Tancar Sessió
                </Link>
            </form>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);

let intervalId = null;

onMounted(() => {
    intervalId = setInterval(async () => {
        try {
            const response = await axios.get(route('verification.check-status', { t: Date.now() }));
            if (response.data.verified) {
                clearInterval(intervalId);
                router.visit(route('dashboard'));
            }
        } catch (error) {
            // Silently ignore errors during polling
        }
    }, 3000);
});

onUnmounted(() => {
    if (intervalId) clearInterval(intervalId);
});
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
</style>

<template>
    <Head title="Recuperar Contrasenya" />

    <div class="min-h-screen flex flex-col justify-center items-center bg-brand-black px-6 py-12">
        
        <div class="w-full max-w-md bg-brand-surface border border-brand-dark p-8 rounded-2xl shadow-2xl relative overflow-hidden">
            
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-2 bg-brand-neon rounded-b-xl shadow-[0_0_30px_rgba(12,225,181,0.8)]"></div>

            <div class="flex justify-center mb-6 mt-4">
                <div class="p-4 border-2 border-brand-neon rounded-full shadow-[0_0_15px_rgba(12,225,181,0.2)]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-brand-neon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                    </svg>
                </div>
            </div>

            <h2 class="text-2xl font-black text-white uppercase tracking-tight mb-4 text-center">Recuperar <span class="text-brand-neon">Accés</span></h2>

            <div class="mb-6 text-sm text-gray-400 leading-relaxed text-center">
                Has oblidat la contrasenya? Cap problema. 
                Deixa'ns el teu correu electrònic i t'enviarem un enllaç per crear-ne una de nova i tornar a donar-li gas.
            </div>

            <div v-if="status" class="mb-6 text-xs font-bold text-brand-black bg-brand-neon p-3 rounded-lg animate-fade-in shadow-[0_0_15px_rgba(12,225,181,0.3)] text-center">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label class="block font-bold text-xs uppercase tracking-wider text-gray-400 mb-2 ml-1" for="email">Correu Electrònic</label>
                    <input 
                        id="email" 
                        type="email" 
                        class="w-full rounded-xl bg-brand-black border border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition py-3 px-4 placeholder-gray-600"
                        placeholder="exemple@correu.com"
                        v-model="form.email" 
                        required 
                        autofocus 
                        autocomplete="username" 
                    />
                    <div v-if="form.errors.email" class="text-red-500 text-xs mt-1 ml-1">{{ form.errors.email }}</div>
                </div>

                <div class="flex flex-col gap-4">
                    <button 
                        type="submit" 
                        :disabled="form.processing" 
                        class="w-full bg-brand-neon text-brand-black px-6 py-4 rounded-xl font-black uppercase tracking-wider text-sm hover:bg-white transition duration-300 shadow-[0_0_20px_rgba(12,225,181,0.4)] disabled:opacity-50"
                    >
                        {{ form.processing ? 'Enviant...' : 'Enviar enllaç de recuperació' }}
                    </button>

                    <Link
                        :href="route('login')"
                        class="text-gray-500 hover:text-white text-xs font-bold uppercase tracking-widest text-center transition mt-2"
                    >
                        &larr; Tornar a Iniciar Sessió
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
</style>

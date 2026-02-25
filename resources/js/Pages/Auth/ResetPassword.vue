<template>
    <Head title="Nova Contrasenya" />

    <div class="min-h-screen flex flex-col justify-center items-center bg-brand-black px-6 py-12">
        
        <div class="w-full max-w-md bg-brand-surface border border-brand-dark p-8 rounded-2xl shadow-2xl relative overflow-hidden">
            
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-2 bg-brand-neon rounded-b-xl shadow-[0_0_30px_rgba(12,225,181,0.8)]"></div>

            <h2 class="text-2xl font-black text-white uppercase tracking-tight mb-2 text-center mt-4">Nova <span class="text-brand-neon">Contrasenya</span></h2>
            <p class="text-gray-400 text-xs text-center mb-6">Estableix una nova contrasenya segura per al teu compte.</p>

            <form @submit.prevent="submit" class="space-y-5">
                
                <div>
                    <label class="block font-bold text-xs uppercase tracking-wider text-gray-400 mb-2 ml-1" for="email">Correu Electrònic</label>
                    <input 
                        id="email" 
                        type="email" 
                        class="w-full rounded-xl bg-brand-black border border-brand-dark text-gray-500 focus:border-brand-neon focus:ring-0 transition py-3 px-4"
                        v-model="form.email" 
                        required 
                        readonly
                        autocomplete="username" 
                    />
                    <div v-if="form.errors.email" class="text-red-500 text-xs mt-1 ml-1">{{ form.errors.email }}</div>
                </div>

                <div>
                    <label class="block font-bold text-xs uppercase tracking-wider text-gray-400 mb-2 ml-1" for="password">Nova Contrasenya</label>
                    <input 
                        id="password" 
                        type="password" 
                        class="w-full rounded-xl bg-brand-black border border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition py-3 px-4 placeholder-gray-600"
                        placeholder="••••••••"
                        v-model="form.password" 
                        required 
                        autofocus 
                        autocomplete="new-password" 
                    />
                    <div v-if="form.errors.password" class="text-red-500 text-xs mt-1 ml-1">{{ form.errors.password }}</div>
                </div>

                <div>
                    <label class="block font-bold text-xs uppercase tracking-wider text-gray-400 mb-2 ml-1" for="password_confirmation">Confirmar Contrasenya</label>
                    <input 
                        id="password_confirmation" 
                        type="password" 
                        class="w-full rounded-xl bg-brand-black border border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition py-3 px-4 placeholder-gray-600"
                        placeholder="••••••••"
                        v-model="form.password_confirmation" 
                        required 
                        autocomplete="new-password" 
                    />
                    <div v-if="form.errors.password_confirmation" class="text-red-500 text-xs mt-1 ml-1">{{ form.errors.password_confirmation }}</div>
                </div>

                <button 
                    type="submit" 
                    :disabled="form.processing" 
                    class="w-full mt-4 bg-brand-neon text-brand-black px-6 py-4 rounded-xl font-black uppercase tracking-wider text-sm hover:bg-white transition duration-300 shadow-[0_0_20px_rgba(12,225,181,0.4)] disabled:opacity-50"
                >
                    {{ form.processing ? 'Guardant...' : 'Guardar Nova Contrasenya' }}
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>
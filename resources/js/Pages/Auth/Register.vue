<template>
    <GuestLayout>
        <Head title="Registrar-se" />

        <form @submit.prevent="submit">
            
            <div>
                <label class="block font-medium text-sm text-gray-400 mb-1" for="name">Nom</label>
                <input 
                    id="name" 
                    type="text" 
                    class="w-full rounded-lg bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition shadow-sm"
                    v-model="form.name" 
                    required 
                    autofocus 
                    autocomplete="name" 
                />
                <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
            </div>

            <div class="mt-4">
                <label class="block font-medium text-sm text-gray-400 mb-1" for="email">Correu Electrònic</label>
                <input 
                    id="email" 
                    type="email" 
                    class="w-full rounded-lg bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition shadow-sm"
                    v-model="form.email" 
                    required 
                    autocomplete="username" 
                />
                <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
            </div>

            <div class="mt-4">
                <label class="block font-medium text-sm text-gray-400 mb-1" for="password">Contrasenya</label>
                <input 
                    id="password" 
                    type="password" 
                    class="w-full rounded-lg bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition shadow-sm"
                    v-model="form.password" 
                    required 
                    autocomplete="new-password" 
                />
                <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
            </div>

            <div class="mt-4">
                <label class="block font-medium text-sm text-gray-400 mb-1" for="password_confirmation">Confirmar Contrasenya</label>
                <input 
                    id="password_confirmation" 
                    type="password" 
                    class="w-full rounded-lg bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition shadow-sm"
                    v-model="form.password_confirmation" 
                    required 
                    autocomplete="new-password" 
                />
                <div v-if="form.errors.password_confirmation" class="text-red-500 text-xs mt-1">{{ form.errors.password_confirmation }}</div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-500 hover:text-brand-neon rounded-md focus:outline-none transition"
                >
                    Ja tens compte?
                </Link>

                <button 
                    type="submit" 
                    :disabled="form.processing" 
                    class="ms-4 bg-brand-neon text-brand-black px-5 py-2 rounded-lg font-bold uppercase tracking-wide text-xs hover:bg-white transition duration-300 shadow-[0_0_15px_rgba(12,225,181,0.4)]"
                >
                    Crear Compte
                </button>
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>
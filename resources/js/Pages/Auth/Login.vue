<template>
    <GuestLayout>
        <Head title="Iniciar Sessió" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-400 text-center">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <label class="block font-medium text-sm text-gray-400 mb-1" for="email">Correu Electrònic</label>
                <input 
                    id="email" 
                    type="email" 
                    class="w-full rounded-lg bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition shadow-sm placeholder-gray-600"
                    placeholder="exemple@correu.com"
                    v-model="form.email" 
                    required 
                    autofocus 
                    autocomplete="username" 
                />
                <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
            </div>

            <div class="mt-4">
                <label class="block font-medium text-sm text-gray-400 mb-1" for="password">Contrasenya</label>
                <input 
                    id="password" 
                    type="password" 
                    class="w-full rounded-lg bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition shadow-sm placeholder-gray-600"
                    placeholder="••••••••"
                    v-model="form.password" 
                    required 
                    autocomplete="current-password" 
                />
                <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        v-model="form.remember" 
                        class="rounded bg-brand-black border-brand-dark text-brand-neon focus:ring-brand-neon"
                    />
                    <span class="ms-2 text-sm text-gray-400">Recorda'm</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-6">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="underline text-sm text-gray-500 hover:text-brand-neon rounded-md focus:outline-none transition"
                >
                    Has oblidat la contrasenya?
                </Link>

                <button 
                    type="submit" 
                    :disabled="form.processing" 
                    class="ms-4 bg-brand-neon text-brand-black px-5 py-2 rounded-lg font-bold uppercase tracking-wide text-xs hover:bg-white transition duration-300 shadow-[0_0_15px_rgba(12,225,181,0.4)]"
                >
                    Entrar
                </button>
            </div>
            
            <div class="mt-6 pt-6 border-t border-brand-dark text-center">
                <span class="text-gray-500 text-sm">Encara no tens compte?</span>
                <Link :href="route('register')" class="ml-2 text-brand-neon hover:underline text-sm font-bold">
                    Registra't aquí
                </Link>
            </div>

        </form>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>
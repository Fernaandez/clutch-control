<script setup>
import { useForm, usePage, Link } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-bold text-white">Informació del Perfil</h2>
            <p class="mt-1 text-sm text-gray-400">
                Actualitza el teu nom i adreça de correu electrònic.
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
            <div>
                <label class="block font-medium text-sm text-gray-400 mb-1" for="name">Nom</label>
                <input 
                    id="name" 
                    type="text" 
                    class="w-full rounded-lg bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition"
                    v-model="form.name" 
                    required 
                    autocomplete="name" 
                />
                <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
            </div>

            <div>
                <label class="block font-medium text-sm text-gray-400 mb-1" for="email">Correu Electrònic</label>
                <input 
                    id="email" 
                    type="email" 
                    class="w-full rounded-lg bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition"
                    v-model="form.email" 
                    required 
                    autocomplete="username" 
                />
                <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
            </div>

            <div class="flex items-center gap-4">
                <button 
                    :disabled="form.processing" 
                    class="bg-brand-base text-brand-black px-4 py-2 rounded font-bold hover:bg-brand-neon transition shadow-neon text-sm uppercase"
                >
                    Guardar Canvis
                </button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-green-400">Guardat.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
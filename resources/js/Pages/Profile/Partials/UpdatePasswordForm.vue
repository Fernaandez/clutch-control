<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-bold text-white">Actualitzar Contrasenya</h2>
            <p class="mt-1 text-sm text-gray-400">
                Assegura't de fer servir una contrasenya llarga i segura.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            
            <div>
                <label class="block font-medium text-sm text-gray-400 mb-1">Contrasenya Actual</label>
                <input 
                    ref="currentPasswordInput"
                    type="password" 
                    class="w-full rounded-lg bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition"
                    v-model="form.current_password" 
                    autocomplete="current-password" 
                />
                <div v-if="form.errors.current_password" class="text-red-500 text-xs mt-1">{{ form.errors.current_password }}</div>
            </div>

            <div>
                <label class="block font-medium text-sm text-gray-400 mb-1">Nova Contrasenya</label>
                <input 
                    ref="passwordInput"
                    type="password" 
                    class="w-full rounded-lg bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition"
                    v-model="form.password" 
                    autocomplete="new-password" 
                />
                <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
            </div>

            <div>
                <label class="block font-medium text-sm text-gray-400 mb-1">Confirmar Contrasenya</label>
                <input 
                    type="password" 
                    class="w-full rounded-lg bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition"
                    v-model="form.password_confirmation" 
                    autocomplete="new-password" 
                />
                <div v-if="form.errors.password_confirmation" class="text-red-500 text-xs mt-1">{{ form.errors.password_confirmation }}</div>
            </div>

            <div class="flex items-center gap-4">
                <button 
                    :disabled="form.processing" 
                    class="bg-brand-base text-brand-black px-4 py-2 rounded font-bold hover:bg-brand-neon transition shadow-neon text-sm uppercase"
                >
                    Canviar Password
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
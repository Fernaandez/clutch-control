<script setup>
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import Modal from '@/Components/Modal.vue'; // Assegura't que tens aquest component, ve amb Breeze

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-bold text-red-500">Esborrar Compte</h2>
            <p class="mt-1 text-sm text-gray-400">
                Un cop esborris el compte, tota la informació de les teves motos es perdrà per sempre.
            </p>
        </header>

        <button 
            @click="confirmUserDeletion" 
            class="bg-red-900/50 border border-red-700 text-red-400 px-4 py-2 rounded font-bold hover:bg-red-800 hover:text-white transition text-sm uppercase"
        >
            Esborrar Compte
        </button>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6 bg-brand-surface border border-brand-dark">
                <h2 class="text-lg font-bold text-white">
                    Estàs segur que vols esborrar el compte?
                </h2>

                <p class="mt-1 text-sm text-gray-400">
                    Siusplau, introdueix la contrasenya per confirmar que vols eliminar permanentment les teves dades.
                </p>

                <div class="mt-6">
                    <label class="sr-only">Password</label>
                    <input 
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="w-full rounded-lg bg-brand-black border-brand-dark text-white focus:border-red-500 focus:ring-red-500 transition"
                        placeholder="Contrasenya"
                        @keyup.enter="deleteUser"
                    />
                    <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button @click="closeModal" class="text-gray-400 hover:text-white transition">
                        Cancel·lar
                    </button>

                    <button
                        class="bg-red-600 text-white px-4 py-2 rounded font-bold hover:bg-red-500 transition shadow-lg"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Eliminar definitivament
                    </button>
                </div>
            </div>
        </Modal>
    </section>
</template>
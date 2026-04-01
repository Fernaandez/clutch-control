<template>
    <Head :title="$t('legal.privacy_title')" />

    <div class="min-h-screen bg-brand-black text-gray-300 py-12 px-4 sm:px-6 lg:px-8 selection:bg-brand-neon selection:text-brand-black">
        <div class="max-w-3xl mx-auto">
            <!-- Logo / Home Link -->
            <div class="flex items-center justify-center mb-10 pb-6 border-b border-brand-dark/50">
                <Link :href="route('dashboard')" class="flex items-center gap-3 group">
                    <div class="p-2 border-2 border-brand-neon rounded-full shadow-[0_0_15px_rgba(12,225,181,0.2)] group-hover:shadow-[0_0_25px_rgba(12,225,181,0.5)] transition duration-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-brand-neon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                        </svg>
                    </div>
                </Link>
            </div>

            <div class="bg-brand-surface border border-brand-dark p-8 md:p-12 rounded-3xl shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-brand-neon opacity-[0.03] blur-3xl rounded-full"></div>
                
                <h1 class="text-4xl md:text-5xl font-black text-white uppercase tracking-tighter mb-4 relative z-10">
                    {{ $t('legal.privacy_title') }}
                </h1>
                
                <p class="text-xs text-brand-neon font-mono mb-10 tracking-widest uppercase relative z-10">
                    {{ $t('legal.updated') }}: <span class="text-gray-400">March 2026</span>
                </p>
                
                <div class="space-y-8 text-base leading-relaxed relative z-10 font-medium">
                    <section>
                        <h2 class="text-xl font-bold text-white mb-4 uppercase tracking-wider">{{ $t('legal.privacy_h2_1') }}</h2>
                        <p>{{ $t('legal.privacy_p1') }}</p>
                        <p class="mt-4">{{ $t('legal.privacy_p2') }}</p>
                    </section>

                    <div class="h-px bg-brand-dark/50"></div>

                    <section>
                        <h2 class="text-xl font-bold text-white mb-4 uppercase tracking-wider">{{ $t('legal.privacy_h2_2') }}</h2>
                        <p>{{ $t('legal.privacy_p3') }}</p>
                    </section>
                    
                    <div class="h-px bg-brand-dark/50"></div>
                    
                    <section>
                        <h2 class="text-xl font-bold text-white mb-4 uppercase tracking-wider">{{ $t('legal.privacy_h2_4') }}</h2>
                        <div>
                            <p class="mb-6">{{ $t('legal.privacy_p5') }}</p>
                            
                            <!-- Botó Esborrar que obre el Modal per a tothom -->
                            <button 
                                @click="handleDeleteRequest"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-500/10 border border-red-500/50 text-red-400 text-sm font-bold uppercase tracking-wider rounded-xl hover:bg-red-500 hover:text-white transition-all duration-300 shadow-lg shadow-red-500/5"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 01 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                {{ $t('legal.privacy_delete_btn') }}
                            </button>
                        </div>
                    </section>
                </div>
            </div>
            
            <div class="mt-8 text-center" v-if="$page.props.auth.user">
                <Link :href="route('profile.edit')" class="inline-block px-8 py-3 bg-brand-neon text-black font-black uppercase tracking-widest rounded-xl hover:bg-white transition-all">
                    {{ $t('common.back') }}
                </Link>
            </div>
        </div>

        <!-- MODAL DE CONFIRMACIÓ DE SEGURETAT (Correu + Contrasenya) -->
        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6 bg-brand-surface border border-brand-dark">
                <h2 class="text-xl font-black text-white uppercase tracking-tight">
                    {{ $t('profile.delete_confirm_title') }}
                </h2>

                <p class="mt-3 text-sm text-gray-400">
                    Aquesta acció és irreversible. Si us plau, introdueix el teu correu i contrasenya per confirmar l'eliminació total de les teves dades.
                </p>

                <div class="mt-6 flex flex-col gap-4">
                    <div>
                        <label class="block text-xs font-bold text-brand-neon uppercase tracking-widest mb-2">
                            Correu Electrònic
                        </label>
                        <input 
                            ref="emailInput"
                            v-model="form.email"
                            type="email"
                            class="w-full rounded-xl bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition-all"
                            placeholder="correu@exemple.com"
                            @keyup.enter="deleteUser"
                        />
                        <div v-if="form.errors.email" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.email }}</div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-brand-neon uppercase tracking-widest mb-2">
                            Contrasenya
                        </label>
                        <input 
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="w-full rounded-xl bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition-all"
                            placeholder="••••••••"
                            @keyup.enter="deleteUser"
                        />
                        <div v-if="form.errors.password" class="text-red-500 text-xs mt-1 font-bold">{{ form.errors.password }}</div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-4">
                    <button @click="closeModal" class="px-4 py-2 text-gray-400 font-bold uppercase text-xs hover:text-white transition">
                        {{ $t('common.cancel') }}
                    </button>

                    <button
                        class="bg-red-600 text-white px-6 py-2.5 rounded-xl font-black uppercase text-xs hover:bg-red-500 transition shadow-lg shadow-red-600/20"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        {{ $t('profile.delete_permanently') }}
                    </button>
                </div>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { nextTick, ref, onMounted } from 'vue';
import Modal from '@/Components/Modal.vue';

const page = usePage();
const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);
const emailInput = ref(null);

const form = useForm({
    email: '',
    password: '',
});

const handleDeleteRequest = () => {
    confirmUserDeletion();
};

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => {
        if (!form.email) {
            emailInput.value.focus();
        } else {
            passwordInput.value.focus();
        }
    });
};

const deleteUser = () => {
    form.post(route('profile.public_destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => {
            if (form.errors.email) {
                emailInput.value.focus();
            } else {
                passwordInput.value.focus();
            }
        },
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
};
</script>

<template>
    <Head :title="$t('legal.privacy_title')" />

    <div class="min-h-screen bg-brand-black text-gray-300">
        <div class="max-w-xl mx-auto px-6 pt-10 pb-28">

            <div class="flex items-center gap-3 mb-10">
                <!-- goBack() ja té en compte els visitants sense sessió: aquesta
                     pàgina és pública i 'dashboard' els enviava al login. -->
                <button
                    type="button"
                    @click="goBack"
                    class="cc-icon-btn"
                    :aria-label="$t('common.back')"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </button>
            </div>

            <h1 class="cc-title">{{ $t('legal.privacy_title') }}</h1>
            <p class="mt-3 text-xs text-gray-500 uppercase tracking-[0.14em]">
                {{ $t('legal.updated') }}: March 2026
            </p>

            <div class="mt-12 space-y-10 text-[15px] leading-relaxed text-gray-300 divide-y divide-white/[0.06]">
                <section class="pb-10">
                    <h2 class="cc-section-label mb-4">{{ $t('legal.privacy_h2_1') }}</h2>
                    <p>{{ $t('legal.privacy_p1') }}</p>
                    <p class="mt-4">{{ $t('legal.privacy_p2') }}</p>
                </section>

                <section class="pt-10 pb-10">
                    <h2 class="cc-section-label mb-4">{{ $t('legal.privacy_h2_2') }}</h2>
                    <p>{{ $t('legal.privacy_p3') }}</p>
                </section>

                <section class="pt-10">
                    <h2 class="cc-section-label mb-4">{{ $t('legal.privacy_h2_4') }}</h2>
                    <p class="mb-6">{{ $t('legal.privacy_p5') }}</p>

                    <button
                        type="button"
                        @click="handleDeleteRequest"
                        class="cc-btn-danger"
                    >
                        {{ $t('legal.privacy_delete_btn') }}
                    </button>
                </section>
            </div>

            <div class="mt-12">
                <button
                    type="button"
                    @click="goBack"
                    class="cc-btn-secondary w-full"
                >
                    {{ $t('common.back') }}
                </button>
            </div>
        </div>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6 bg-brand-black border border-white/[0.08]">
                <h2 class="text-lg font-medium text-white">
                    {{ $t('profile.delete_confirm_title') }}
                </h2>

                <p class="mt-3 text-sm text-gray-400">
                    Aquesta acció és irreversible. Si us plau, introdueix el teu correu i contrasenya per confirmar l'eliminació total de les teves dades.
                </p>

                <div class="mt-6 flex flex-col gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">
                            Correu Electrònic
                        </label>
                        <input
                            ref="emailInput"
                            v-model="form.email"
                            type="email"
                            class="w-full rounded-xl bg-white/[0.04] border-white/[0.08] text-white focus:border-white/30 focus:ring-0"
                            placeholder="correu@exemple.com"
                            @keyup.enter="deleteUser"
                        />
                        <p v-if="form.errors.email" class="text-red-400 text-xs mt-1">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">
                            Contrasenya
                        </label>
                        <input
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="w-full rounded-xl bg-white/[0.04] border-white/[0.08] text-white focus:border-white/30 focus:ring-0"
                            placeholder="••••••••"
                            @keyup.enter="deleteUser"
                        />
                        <p v-if="form.errors.password" class="text-red-400 text-xs mt-1">{{ form.errors.password }}</p>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" @click="closeModal" class="cc-btn-text">
                        {{ $t('common.cancel') }}
                    </button>

                    <button
                        type="button"
                        class="cc-btn-danger"
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
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import { smartBack } from '@/Composables/navigationStack.js';

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

const goBack = () => {
    const fallback = page.props.auth.user ? route('profile.edit') : route('welcome');
    smartBack(fallback);
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

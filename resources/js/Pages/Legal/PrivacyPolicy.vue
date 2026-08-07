<template>
    <Head :title="$t('legal.privacy_title')" />

    <div class="min-h-screen bg-brand-black text-gray-300">
        <div class="max-w-xl mx-auto px-6 pt-10 pb-28">

            <div class="flex items-center gap-3 mb-10">
                <Link
                    :href="route('dashboard')"
                    class="cc-icon-btn"
                    :aria-label="$t('common.back')"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                    </svg>
                </Link>
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
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
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

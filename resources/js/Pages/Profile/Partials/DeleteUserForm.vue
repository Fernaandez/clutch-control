<script setup>
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import Modal from '@/Components/Modal.vue';

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
    <section>
        <p class="cc-section-label text-red-400/80">{{ $t('profile.delete_account') }}</p>
        <p class="mt-1 text-sm text-gray-500">
            {{ $t('profile.delete_account_hint') }}
        </p>

        <button
            type="button"
            @click="confirmUserDeletion"
            class="cc-btn-danger mt-6"
        >
            {{ $t('profile.delete_button') }}
        </button>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6 bg-brand-black border border-white/[0.08]">
                <h2 class="text-lg font-medium text-white">
                    {{ $t('profile.delete_confirm_title') }}
                </h2>

                <p class="mt-2 text-sm text-gray-400">
                    {{ $t('profile.delete_confirm_hint') }}
                </p>

                <div class="mt-6">
                    <label class="sr-only">{{ $t('profile.password_placeholder') }}</label>
                    <input
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="w-full rounded-xl bg-white/[0.04] border-white/[0.08] text-white focus:border-white/30 focus:ring-0"
                        :placeholder="$t('profile.password_placeholder')"
                        @keyup.enter="deleteUser"
                    />
                    <p v-if="form.errors.password" class="text-red-400 text-xs mt-1">{{ form.errors.password }}</p>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="closeModal" class="cc-btn-text">
                        {{ $t('profile.cancel') }}
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
    </section>
</template>

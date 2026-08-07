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

const inputClass = (error) =>
    error
        ? 'w-full rounded-xl bg-white/[0.04] border-red-500 ring-1 ring-red-500 text-white focus:border-red-400 focus:ring-0'
        : 'w-full rounded-xl bg-white/[0.04] border-white/[0.08] text-white focus:border-white/30 focus:ring-0';
</script>

<template>
    <section>
        <p class="cc-section-label">{{ $t('profile.update_password') }}</p>
        <p class="mt-1 text-sm text-gray-500">
            {{ $t('profile.update_password_hint') }}
        </p>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-5">

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('profile.current_password') }}</label>
                <input
                    ref="currentPasswordInput"
                    type="password"
                    :class="inputClass(form.errors.current_password)"
                    v-model="form.current_password"
                    autocomplete="current-password"
                />
                <p v-if="form.errors.current_password" class="text-red-400 text-xs mt-1">{{ form.errors.current_password }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('profile.new_password') }}</label>
                <input
                    ref="passwordInput"
                    type="password"
                    :class="inputClass(form.errors.password)"
                    v-model="form.password"
                    autocomplete="new-password"
                />
                <p v-if="form.errors.password" class="text-red-400 text-xs mt-1">{{ form.errors.password }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ $t('profile.confirm_password') }}</label>
                <input
                    type="password"
                    :class="inputClass(form.errors.password_confirmation)"
                    v-model="form.password_confirmation"
                    autocomplete="new-password"
                />
                <p v-if="form.errors.password_confirmation" class="text-red-400 text-xs mt-1">{{ form.errors.password_confirmation }}</p>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="cc-btn-primary"
                >
                    {{ $t('profile.change_password') }}
                </button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-400">{{ $t('profile.saved') }}</p>
                </Transition>
            </div>
        </form>
    </section>
</template>

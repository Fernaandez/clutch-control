<template>
    <Head :title="$t('reset_password.title')" />

    <div class="min-h-screen flex flex-col justify-center items-center bg-brand-black text-white px-6 py-12">
        <div class="w-full max-w-sm">
            <div class="flex justify-center mb-10">
                <img
                    :src="appLogo"
                    alt="Clutch Control"
                    class="h-14 w-auto opacity-90"
                >
            </div>

            <h1 class="cc-title text-center mb-2">{{ $t('reset_password.title') }}</h1>
            <p class="text-gray-400 text-sm text-center mb-8">{{ $t('reset_password.subtitle') }}</p>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-sm text-gray-400 mb-2" for="email">{{ $t('reset_password.email') }}</label>
                    <input
                        id="email"
                        type="email"
                        class="w-full rounded-xl bg-white/[0.04] border border-white/[0.08] text-gray-500 focus:border-white/30 focus:ring-0 py-3 px-4"
                        v-model="form.email"
                        required
                        readonly
                        autocomplete="username"
                    >
                    <div v-if="form.errors.email" class="text-red-400 text-xs mt-1.5">{{ form.errors.email }}</div>
                </div>

                <div>
                    <label class="block text-sm text-gray-400 mb-2" for="password">{{ $t('reset_password.new_password') }}</label>
                    <input
                        id="password"
                        type="password"
                        class="w-full rounded-xl bg-white/[0.04] border border-white/[0.08] text-white focus:border-white/30 focus:ring-0 py-3 px-4 placeholder-gray-600"
                        placeholder="••••••••"
                        v-model="form.password"
                        required
                        autofocus
                        autocomplete="new-password"
                    >
                    <div v-if="form.errors.password" class="text-red-400 text-xs mt-1.5">{{ form.errors.password }}</div>
                </div>

                <div>
                    <label class="block text-sm text-gray-400 mb-2" for="password_confirmation">{{ $t('reset_password.confirm_password') }}</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        class="w-full rounded-xl bg-white/[0.04] border border-white/[0.08] text-white focus:border-white/30 focus:ring-0 py-3 px-4 placeholder-gray-600"
                        placeholder="••••••••"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    >
                    <div v-if="form.errors.password_confirmation" class="text-red-400 text-xs mt-1.5">{{ form.errors.password_confirmation }}</div>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="cc-btn-primary w-full py-4"
                >
                    {{ form.processing ? $t('reset_password.saving') : $t('reset_password.submit') }}
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import appLogo from '@/../images/logo.svg';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

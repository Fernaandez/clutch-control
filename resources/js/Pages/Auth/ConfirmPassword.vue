<template>
    <Head :title="$t('confirm_password.title')" />

    <div class="min-h-screen flex flex-col justify-center items-center bg-brand-black text-white px-6 py-12">
        <div class="w-full max-w-sm">
            <div class="flex justify-center mb-10">
                <img
                    :src="appLogo"
                    alt="Clutch Control"
                    class="h-14 w-auto opacity-90"
                >
            </div>

            <h1 class="cc-title text-center mb-3">{{ $t('confirm_password.title') }}</h1>

            <p class="mb-8 text-sm text-gray-400 leading-relaxed text-center">
                {{ $t('confirm_password.description') }}
            </p>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-sm text-gray-400 mb-2" for="password">{{ $t('confirm_password.password') }}</label>
                    <input
                        id="password"
                        type="password"
                        class="w-full rounded-xl bg-white/[0.04] border border-white/[0.08] text-white focus:border-white/30 focus:ring-0 py-3 px-4 placeholder-gray-600"
                        placeholder="••••••••"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        autofocus
                    >
                    <div v-if="form.errors.password" class="text-red-400 text-xs mt-1.5">{{ form.errors.password }}</div>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="cc-btn-primary w-full py-4"
                >
                    {{ $t('confirm_password.submit') }}
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import appLogo from '@/../images/logo.svg';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

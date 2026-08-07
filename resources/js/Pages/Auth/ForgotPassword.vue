<template>
    <Head :title="$t('forgot_password.title')" />

    <div class="min-h-screen flex flex-col justify-center items-center bg-brand-black text-white px-6 py-12">
        <div class="w-full max-w-sm">
            <div class="flex justify-center mb-10">
                <Link :href="route('welcome')">
                    <img
                        :src="appLogo"
                        alt="Clutch Control"
                        class="h-14 w-auto opacity-90"
                    >
                </Link>
            </div>

            <h1 class="cc-title text-center mb-3">{{ $t('forgot_password.title') }}</h1>

            <p class="mb-8 text-sm text-gray-400 leading-relaxed text-center">
                {{ $t('forgot_password.description') }}
            </p>

            <div v-if="status" class="mb-6 text-sm text-green-400 text-center">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-sm text-gray-400 mb-2" for="email">{{ $t('forgot_password.email') }}</label>
                    <input
                        id="email"
                        type="email"
                        class="w-full rounded-xl bg-white/[0.04] border border-white/[0.08] text-white focus:border-white/30 focus:ring-0 py-3 px-4 placeholder-gray-600"
                        placeholder="exemple@correu.com"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    >
                    <div v-if="form.errors.email" class="text-red-400 text-xs mt-1.5">{{ form.errors.email }}</div>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="cc-btn-primary w-full py-4"
                >
                    {{ form.processing ? $t('forgot_password.sending') : $t('forgot_password.submit') }}
                </button>

                <div class="pt-4 flex justify-center">
                    <Link :href="route('login')" class="cc-btn-text">
                        {{ $t('forgot_password.back_to_login') }}
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import appLogo from '@/../images/logo.svg';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

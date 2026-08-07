<template>
    <Head :title="$t('login.title')" />

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

            <div v-if="status" class="mb-4 text-sm text-green-400 text-center">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-sm text-gray-400 mb-2" for="email">{{ $t('login.email') }}</label>
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

                <div>
                    <label class="block text-sm text-gray-400 mb-2" for="password">{{ $t('login.password') }}</label>
                    <input
                        id="password"
                        type="password"
                        class="w-full rounded-xl bg-white/[0.04] border border-white/[0.08] text-white focus:border-white/30 focus:ring-0 py-3 px-4 placeholder-gray-600"
                        placeholder="••••••••"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    >
                    <div v-if="form.errors.password" class="text-red-400 text-xs mt-1.5">{{ form.errors.password }}</div>
                </div>

                <div class="flex items-center justify-between">
                    <!-- Recorda'm està amagat i sempre és true per defecte per evitar pèrdues de sessió al mòbil -->
                    <label class="hidden">
                        <input type="checkbox" name="remember" v-model="form.remember">
                    </label>

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="cc-btn-text"
                    >
                        {{ $t('login.forgot') }}
                    </Link>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="cc-btn-primary w-full py-4"
                >
                    {{ $t('login.submit') }}
                </button>

                <button
                    type="button"
                    @click="loginWithGoogle"
                    class="cc-btn-secondary w-full py-4 gap-3"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    {{ $t('login.continue_with_google') }}
                </button>

                <div class="pt-6 flex flex-col items-center gap-3">
                    <span class="text-gray-500 text-sm">{{ $t('login.no_account') }}</span>
                    <Link :href="route('register')" class="cc-btn-text">
                        {{ $t('login.register_here') }}
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { Capacitor } from '@capacitor/core';
import appLogo from '@/../images/logo.svg';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: true,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const loginWithGoogle = async () => {
    const googleUrl = route('google.login');

    if (Capacitor.isNativePlatform()) {
        const { Browser } = await import('@capacitor/browser');

        await Browser.open({
            url: googleUrl,
            windowName: '_self',
        });

        Browser.addListener('browserFinished', () => {
            router.visit(route('dashboard'));
        });
    } else {
        window.location.href = googleUrl;
    }
};
</script>

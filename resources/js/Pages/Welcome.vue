<template>
    <Head :title="$t('welcome.title')" />

    <div class="h-[100dvh] w-full bg-brand-black text-white overflow-hidden flex flex-col">
        <main class="relative z-10 flex flex-col flex-1 items-center justify-center p-6">

            <div class="mb-14 p-8 rounded-[2rem] border border-white/[0.08] bg-white/[0.03] flex items-center justify-center">
                <img
                    :src="appLogo"
                    alt="Clutch Control Logo"
                    class="h-32 md:h-40 w-auto opacity-90"
                >
            </div>

            <div v-if="canLogin" class="flex flex-col gap-3 w-full max-w-sm">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="cc-btn-primary w-full py-4 text-base"
                >
                    {{ $t('welcome.enter_garage') }}
                </Link>

                <template v-else>
                    <Link :href="route('login')" class="cc-btn-primary w-full py-4 text-base">
                        {{ $t('welcome.login') }}
                    </Link>

                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="cc-btn-secondary w-full py-4 text-base"
                    >
                        {{ $t('welcome.register') }}
                    </Link>
                </template>
            </div>
        </main>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import appLogo from '@/../images/logo.svg';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

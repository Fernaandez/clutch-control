<template>
    <Head :title="$t('verify_email.title')" />

    <div class="min-h-screen flex flex-col justify-center items-center bg-brand-black text-white px-6 py-12">
        <div class="w-full max-w-sm text-center">
            <div class="flex justify-center mb-10">
                <img
                    :src="appLogo"
                    alt="Clutch Control"
                    class="h-14 w-auto opacity-90"
                >
            </div>

            <h1 class="cc-title mb-4">{{ $t('verify_email.title') }}</h1>

            <p class="mb-8 text-sm text-gray-400 leading-relaxed">
                {{ $t('verify_email.description') }}
            </p>

            <div v-if="verificationLinkSent" class="mb-6 text-sm text-green-400">
                {{ $t('verify_email.link_sent') }}
            </div>

            <form @submit.prevent="submit" class="flex flex-col gap-4">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="cc-btn-primary w-full py-4"
                >
                    {{ $t('verify_email.resend') }}
                </button>

                <div class="pt-2 flex justify-center">
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="cc-btn-text"
                    >
                        {{ $t('verify_email.logout') }}
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import axios from 'axios';
import appLogo from '@/../images/logo.svg';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);

let intervalId = null;

onMounted(() => {
    intervalId = setInterval(async () => {
        try {
            const response = await axios.get(route('verification.check-status', { t: Date.now() }));
            if (response.data.verified) {
                clearInterval(intervalId);
                router.visit(route('dashboard'));
            }
        } catch (error) {
            // Silently ignore errors during polling
        }
    }, 3000);
});

onUnmounted(() => {
    if (intervalId) clearInterval(intervalId);
});
</script>

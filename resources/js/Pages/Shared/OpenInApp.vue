<template>
    <div class="min-h-screen bg-brand-black text-gray-200 px-6 py-10 flex items-center justify-center">
        <div class="w-full max-w-md">
            <h1 class="cc-title">{{ title }}</h1>
            <p class="mt-3 text-sm text-gray-500">{{ subtitle }}</p>

            <div class="mt-10 space-y-3">
                <button
                    type="button"
                    @click="openApp"
                    class="cc-btn-primary w-full py-3.5"
                >
                    {{ openAppLabel }}
                </button>

                <a
                    :href="storeUrl"
                    class="cc-btn-secondary w-full"
                >
                    Descarregar app
                </a>

                <a
                    :href="webUrl"
                    class="cc-btn-text w-full justify-center"
                >
                    {{ openWebLabel }}
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';

const props = defineProps({
    title: String,
    subtitle: String,
    webUrl: {
        type: String,
        required: true,
    },
    deepLinkUrl: {
        type: String,
        required: true,
    },
    androidStoreUrl: {
        type: String,
        required: true,
    },
    iosStoreUrl: {
        type: String,
        required: true,
    },
    openAppLabel: {
        type: String,
        default: 'Obrir app',
    },
    openWebLabel: {
        type: String,
        default: 'Continuar en web',
    },
});

const userAgent = navigator.userAgent || '';
const isAndroid = /Android/i.test(userAgent);
const isIos = /iPhone|iPad|iPod/i.test(userAgent);

const storeUrl = computed(() => {
    if (isAndroid) return props.androidStoreUrl;
    if (isIos) return props.iosStoreUrl;
    return props.androidStoreUrl;
});

const openApp = () => {
    const fallbackTimer = setTimeout(() => {
        window.location.href = storeUrl.value;
    }, 1200);

    window.location.href = props.deepLinkUrl;

    setTimeout(() => clearTimeout(fallbackTimer), 2000);
};

onMounted(() => {
    const isNative = typeof window !== 'undefined'
        && window.Capacitor
        && typeof window.Capacitor.isNativePlatform === 'function'
        && window.Capacitor.isNativePlatform();

    if (isNative) {
        window.location.href = props.webUrl;
        return;
    }

    if (isAndroid || isIos) {
        openApp();
    }
});
</script>

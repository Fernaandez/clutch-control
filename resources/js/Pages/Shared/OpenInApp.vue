<template>
    <div class="min-h-screen bg-brand-black text-gray-200 px-6 py-10 flex items-center justify-center">
        <div class="w-full max-w-md bg-brand-surface border border-brand-dark rounded-2xl p-6 shadow-2xl">
            <h1 class="text-2xl font-black text-white uppercase tracking-tight mb-3">{{ title }}</h1>
            <p class="text-sm text-gray-400 mb-6">{{ subtitle }}</p>

            <div class="space-y-3">
                <button
                    type="button"
                    @click="openApp"
                    class="w-full bg-brand-neon text-brand-black px-4 py-3 rounded-xl font-black uppercase tracking-widest text-sm hover:bg-white transition"
                >
                    {{ openAppLabel }}
                </button>

                <a
                    :href="storeUrl"
                    class="w-full inline-flex items-center justify-center bg-brand-dark border border-brand-neon/40 text-brand-neon px-4 py-3 rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-brand-neon hover:text-brand-black transition"
                >
                    Descarregar app
                </a>

                <a
                    :href="webUrl"
                    class="w-full inline-flex items-center justify-center text-gray-400 hover:text-white text-xs font-bold uppercase tracking-widest transition"
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

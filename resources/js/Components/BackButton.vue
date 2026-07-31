<script setup>
import { smartBack } from '@/Composables/navigationStack.js';

const props = defineProps({
    /** Ruta interna a la qual anar si no hi ha històric de navegació. */
    fallback: { type: String, default: null },
    /** Variant visual: 'default' sobre fons pla, 'overlay' sobre mapes i fotos. */
    variant: { type: String, default: 'default' },
});

const goBack = () => smartBack(props.fallback);
</script>

<template>
    <button
        type="button"
        @click="goBack"
        class="cc-icon-btn"
        :class="variant === 'overlay' ? 'bg-black/50 backdrop-blur-md border-white/20 text-white' : ''"
        :aria-label="$t('common.back')"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
    </button>
</template>

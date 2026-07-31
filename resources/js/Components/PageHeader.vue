<script setup>
import BackButton from '@/Components/BackButton.vue';

defineProps({
    /** Títol curt, una paraula sempre que sigui possible. */
    title: { type: String, required: true },
    /** Mostrar botó de tornar. */
    back: { type: Boolean, default: true },
    /** Ruta de retorn si no hi ha històric. */
    fallback: { type: String, default: null },
});
</script>

<template>
    <header class="flex items-center gap-3 mb-6">
        <BackButton v-if="back" :fallback="fallback" />

        <h1 class="cc-title flex-1 truncate">{{ title }}</h1>

        <!-- Accions contextuals a la dreta (crear, filtrar, editar…) -->
        <div v-if="$slots.actions" class="flex items-center gap-2 flex-shrink-0">
            <slot name="actions" />
        </div>
    </header>
</template>

<script setup>
defineProps({
    /** Missatge principal, curt i directe. */
    title: { type: String, required: true },
    /** Text opcional d'una línia sota el títol. */
    description: { type: String, default: null },
});
</script>

<template>
    <div class="flex flex-col items-center justify-center text-center py-16 px-6">
        <div v-if="$slots.icon" class="mb-4 text-gray-600">
            <slot name="icon" />
        </div>

        <p class="text-base font-semibold text-gray-300">{{ title }}</p>
        <p v-if="description" class="mt-1 text-sm text-gray-500 max-w-xs">{{ description }}</p>

        <div v-if="$slots.action" class="mt-6">
            <slot name="action" />
        </div>
    </div>
</template>

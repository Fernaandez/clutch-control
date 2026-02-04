<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps(['href', 'active']);

const classes = computed(() =>
    props.active
        ? 'inline-flex items-center px-1 pt-1 border-b-2 border-brand-neon text-sm font-medium leading-5 text-brand-neon focus:outline-none transition duration-150 ease-in-out drop-shadow-[0_0_8px_rgba(12,225,181,0.6)]'
        : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-400 hover:text-brand-base hover:border-brand-muted focus:outline-none focus:text-gray-300 transition duration-150 ease-in-out'
);
</script>
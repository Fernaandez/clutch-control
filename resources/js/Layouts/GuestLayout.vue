<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-brand-black relative overflow-hidden safe-horizontal safe-top safe-bottom">
        
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1558981806-ec527fa84c3d?q=80&w=2670&auto=format&fit=crop" 
                 class="w-full h-full object-cover opacity-40 blur-sm" 
                 alt="Moto Background">
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/70 to-transparent"></div>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-8 mx-4 bg-brand-surface border border-brand-dark shadow-[0_0_50px_rgba(0,0,0,0.5)] overflow-hidden rounded-2xl relative z-10 backdrop-blur-md">
            
            <div class="flex justify-center mb-8">
                <Link href="/" class="flex items-center justify-center group">
                    <img :src="appLogo" alt="Clutch Control Logo" class="h-16 w-auto group-hover:scale-105 transition duration-500">
                </Link>
            </div>

            <slot />
            
        </div>
    </div>
</template>

<script setup>
import { watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { recordNavigationVisit } from '@/Composables/navigationStack.js';
import appLogo from '@/../images/logo.svg';

const page = usePage();

watch(
    () => page.url,
    () => {
        recordNavigationVisit();
    },
    { immediate: true },
);
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-brand-black relative overflow-hidden">
        
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1558981806-ec527fa84c3d?q=80&w=2670&auto=format&fit=crop" 
                 class="w-full h-full object-cover opacity-40 blur-sm" 
                 alt="Moto Background">
            <div class="absolute inset-0 bg-gradient-to-t from-brand-black via-brand-black/80 to-transparent"></div>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-8 mx-4 bg-brand-surface border border-brand-dark shadow-[0_0_50px_rgba(0,0,0,0.5)] overflow-hidden rounded-2xl relative z-10 backdrop-blur-md">
            
            <div class="flex justify-center mb-8">
                <Link href="/" class="flex items-center gap-2 group">
                    <div class="bg-brand-neon/10 p-2 rounded-full border border-brand-neon/50 group-hover:scale-110 transition duration-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8 text-brand-neon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-black tracking-tighter text-white">
                        CLUTCH<span class="text-brand-neon">CONTROL</span>
                    </span>
                </Link>
            </div>

            <slot />
            
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
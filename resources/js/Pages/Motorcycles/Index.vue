<template>
    <AppLayout>
        <div class="px-4 py-6"> 
            
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-white tracking-wide">
                    El meu <span class="text-brand-neon">Garatge</span>
                </h1>
                
                <Link :href="route('motorcycles.create')" class="bg-brand-base text-brand-black px-5 py-2 rounded font-bold shadow-neon hover:bg-brand-neon hover:shadow-neon-hover transition-all duration-300 transform hover:-translate-y-1">
                    + Nova Moto
                </Link>
            </div>

            <div v-if="motos.length === 0" class="text-center py-10 text-gray-500">
                Encara no tens cap moto. Afegeix la primera!
            </div>

             <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div 
                    v-for="moto in motos" 
                    :key="moto.id" 
                    class="bg-brand-surface rounded-xl border border-brand-dark shadow-lg hover:border-brand-neon transition duration-300 group relative overflow-hidden"
                >
                    <Link :href="route('dashboard', moto.id)" class="block p-6 pb-12">
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="text-xl font-bold text-white group-hover:text-brand-neon transition">{{ moto.brand }} {{ moto.model }}</h2>
                                <p class="text-brand-muted mt-2">{{ moto.plate }}</p>
                            </div>
                            <span class="text-xs font-mono text-gray-500 bg-brand-black px-2 py-1 rounded border border-brand-dark">{{ moto.year }}</span>
                        </div>

                        <div class="mt-4 pt-4 border-t border-brand-dark flex justify-between items-center">
                            <span class="text-brand-neon font-mono font-bold">{{ moto.current_km }} km</span>
                        </div>
                    </Link>

                    <Link :href="route('motorcycles.edit', moto.id)" class="absolute bottom-3 right-3 z-10 bg-brand-dark hover:bg-brand-neon hover:text-brand-black text-gray-300 p-2 rounded-full transition shadow-lg border border-transparent hover:border-brand-neon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </Link>

                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    motos: Array
});
</script>
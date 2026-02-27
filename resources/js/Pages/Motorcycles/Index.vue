<template>
    <AppLayout>
        <div class="px-4 py-6"> 
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-wide">
                    El meu <span class="text-brand-neon">Garatge</span>
                </h1>
                
                <Link :href="route('motorcycles.create')" class="bg-brand-neon text-brand-black px-5 py-2.5 rounded-lg font-bold shadow-[0_0_15px_rgba(12,225,181,0.3)] hover:bg-white hover:shadow-[0_0_25px_rgba(12,225,181,0.5)] transition-all duration-300 transform active:scale-95 text-sm uppercase tracking-wider whitespace-nowrap">
                    + Nova Moto
                </Link>
            </div>

            <div v-if="motos.length === 0" class="flex flex-col items-center justify-center py-20 bg-brand-surface border border-brand-dark border-dashed rounded-2xl text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-brand-dark mb-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7m4.5 4.5 3-3m-3 3 1.5 4.5m-1.5-4.5-4.5 1.5" />
                </svg>
                <p class="text-sm">Encara no tens cap moto al teu garatge.</p>
                <Link :href="route('motorcycles.create')" class="mt-4 text-brand-neon font-bold text-xs uppercase hover:text-white transition">Afegir ara →</Link>
            </div>

             <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div 
                    v-for="moto in motos" 
                    :key="moto.id" 
                    class="bg-brand-surface rounded-xl border border-brand-dark shadow-lg hover:border-brand-neon transition duration-300 group relative overflow-hidden"
                >
                    <Link :href="route('dashboard', moto.id)" class="block">
                        <div v-if="moto.photo" class="h-48 w-full overflow-hidden border-b border-brand-dark">
                            <img :src="$page.props.storageUrl + '/' + moto.photo" alt="Foto Moto" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        </div>
                        <div class="p-6 pb-12">
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
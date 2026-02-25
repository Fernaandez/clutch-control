<template>
    <AppLayout title="Els Meus Anuncis">
        <div class="px-4 py-6 pb-24">
            
            <div class="flex items-center justify-between mb-6">
                <div>
                    <Link :href="route('sales.index')" class="text-gray-400 text-sm hover:text-white flex items-center gap-1 transition">
                        &larr; Tornar al Mur
                    </Link>
                    <h1 class="text-3xl font-black text-white uppercase tracking-tighter mt-1">Els Meus <span class="text-brand-neon">Anuncis</span></h1>
                </div>
                <Link :href="route('sales.create')" class="bg-brand-neon text-brand-black p-3 rounded-full shadow-[0_0_15px_rgba(12,225,181,0.4)] hover:scale-110 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                </Link>
            </div>

            <div v-if="sales.length === 0" class="flex flex-col items-center justify-center py-12 text-center opacity-60 bg-brand-surface rounded-xl border border-brand-dark border-dashed">
                <p class="text-gray-400 font-medium">Encara no tens cap moto a la venda.</p>
                <Link :href="route('sales.create')" class="text-brand-neon text-sm font-bold mt-2 hover:underline">Crear el meu primer anunci</Link>
            </div>

            <div v-else class="space-y-4">
                <div v-for="sale in sales" :key="sale.id" class="bg-brand-surface rounded-xl p-4 border shadow-lg flex flex-col md:flex-row gap-4 transition" :class="sale.is_sold ? 'border-red-500/30 opacity-75' : (!sale.is_active ? 'border-gray-600 opacity-60' : 'border-brand-dark hover:border-brand-neon')">
                    
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span v-if="sale.is_sold" class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase">Venuda</span>
                            <span v-else-if="sale.is_active" class="bg-brand-neon text-brand-black text-[10px] font-bold px-2 py-0.5 rounded uppercase">Actiu</span>
                            <span v-else class="bg-gray-600 text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase">Ocult</span>
                            
                            <span class="text-xs text-gray-500">{{ new Date(sale.created_at).toLocaleDateString('ca-ES') }}</span>
                        </div>
                        
                        <h3 class="text-lg font-bold text-white">{{ sale.title }}</h3>
                        <p class="text-xs text-gray-400 uppercase tracking-widest">{{ sale.motorcycle?.brand }} {{ sale.motorcycle?.model }}</p>
                        
                        <div class="text-xl font-mono font-bold text-brand-neon mt-2">
                            {{ parseFloat(sale.price).toLocaleString('ca-ES') }} €
                        </div>
                    </div>

                    <div class="flex flex-row md:flex-col gap-2 items-end justify-center">
                        <Link :href="route('sales.show', sale.id)" class="px-4 py-2 bg-brand-black border border-brand-dark rounded text-white text-xs font-bold uppercase hover:bg-gray-800 transition text-center w-full md:w-auto">
                            Veure
                        </Link>
                        <Link :href="route('sales.edit', sale.id)" class="px-4 py-2 bg-brand-dark text-white rounded text-xs font-bold uppercase hover:bg-brand-neon hover:text-black transition text-center w-full md:w-auto">
                            Editar
                        </Link>
                    </div>

                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    sales: Array
});
</script>
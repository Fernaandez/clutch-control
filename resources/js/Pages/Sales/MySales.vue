<template>
    <AppLayout title="Els Meus Anuncis">
        <div class="px-4 py-6 pb-24">
            
            <div class="flex items-center justify-between mb-6">
                <div>
                    <Link :href="route('sales.index')" class="text-gray-400 text-sm hover:text-white flex items-center gap-1 transition">&larr; Tornar al Mercat</Link>
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
                <div 
                    v-for="sale in sales" :key="sale.id" 
                    class="bg-brand-surface rounded-xl border shadow-lg transition overflow-hidden"
                    :class="sale.is_sold ? 'border-red-500/40 opacity-80' : (!sale.is_active ? 'border-gray-700 opacity-60' : 'border-brand-dark hover:border-brand-neon/50')"
                >
                    <div class="flex gap-0">
                        <!-- Foto o placeholder -->
                        <div class="w-28 h-28 flex-shrink-0 bg-gray-900 relative overflow-hidden">
                            <img 
                                v-if="sale.images && sale.images.length > 0"
                                :src="$page.props.storageUrl + '/' + sale.images[0].image_path"
                                class="w-full h-full object-cover"
                                alt="Foto"
                            >
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <div class="absolute inset-0 opacity-30 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
                                <span class="text-3xl font-black text-white/20 uppercase relative z-10">{{ sale.motorcycle?.brand?.[0] }}</span>
                            </div>
                            <!-- Badge venut -->
                            <div v-if="sale.is_sold" class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                <span class="bg-red-600 text-white text-[9px] font-black px-1.5 py-0.5 rounded uppercase transform -rotate-12">Venut</span>
                            </div>
                        </div>

                        <!-- Info + accions -->
                        <div class="flex-1 p-3 flex flex-col justify-between min-w-0">
                            <div>
                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                    <span v-if="sale.is_sold" class="bg-red-500/20 text-red-400 text-[9px] font-bold px-2 py-0.5 rounded-full uppercase border border-red-500/30">Venuda</span>
                                    <span v-else-if="sale.is_active" class="bg-brand-neon/10 text-brand-neon text-[9px] font-bold px-2 py-0.5 rounded-full uppercase border border-brand-neon/20">Actiu</span>
                                    <span v-else class="bg-gray-700 text-gray-300 text-[9px] font-bold px-2 py-0.5 rounded-full uppercase">Ocult</span>
                                    <span class="text-[10px] text-gray-600">{{ new Date(sale.created_at).toLocaleDateString('ca-ES') }}</span>
                                </div>
                                <h3 class="text-sm font-bold text-white truncate">{{ sale.title }}</h3>
                                <p class="text-[10px] text-gray-500 uppercase truncate">{{ sale.motorcycle?.brand }} {{ sale.motorcycle?.model }}</p>
                            </div>

                            <div class="flex items-center justify-between mt-2">
                                <span class="text-lg font-mono font-bold text-brand-neon">{{ parseFloat(sale.price).toLocaleString('ca-ES') }} €</span>

                                <div class="flex gap-1.5">
                                    <Link :href="route('sales.show', sale.id)" class="px-3 py-1.5 bg-brand-black border border-brand-dark rounded-lg text-white text-[10px] font-bold uppercase hover:bg-gray-800 transition">
                                        Veure
                                    </Link>
                                    <Link :href="route('sales.edit', sale.id)" class="px-3 py-1.5 bg-brand-dark text-white rounded-lg text-[10px] font-bold uppercase hover:bg-brand-neon hover:text-black transition">
                                        Editar
                                    </Link>
                                    <!-- Marcar venut ràpid (només si no ja venut) -->
                                    <Link 
                                        v-if="!sale.is_sold"
                                        :href="route('sales.mark-sold', sale.id)"
                                        method="patch" as="button"
                                        class="px-3 py-1.5 bg-red-900/30 border border-red-700/30 text-red-400 rounded-lg text-[10px] font-bold uppercase hover:bg-red-900/60 hover:text-red-300 transition"
                                        :title="'Marcar com a venuda'"
                                    >
                                        ✓ Venuda
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ sales: Array });
</script>
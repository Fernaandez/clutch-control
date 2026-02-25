<template>
    <AppLayout :title="sale.title">
        <div class="px-4 py-6 pb-24 max-w-3xl mx-auto">
            
            <div class="flex items-center justify-between mb-4">
                <Link :href="route('sales.index')" class="text-gray-400 text-sm hover:text-white flex items-center gap-1 transition">
                    &larr; Tornar al Mercat
                </Link>
                <div class="text-xs text-brand-neon font-bold uppercase tracking-widest bg-brand-neon/10 px-3 py-1 rounded border border-brand-neon/30">
                    En Venda
                </div>
            </div>

            <div class="w-full h-64 sm:h-80 bg-gray-900 rounded-2xl overflow-hidden relative mb-6 border border-brand-dark shadow-2xl flex items-center justify-center">
                <div class="absolute inset-0 opacity-40 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-brand-black via-transparent to-transparent z-10"></div>
                
                <h2 class="relative z-20 text-5xl font-black text-white/30 uppercase tracking-widest text-center px-4">
                    {{ sale.motorcycle?.brand }}
                </h2>
            </div>

            <div class="mb-8">
                <h1 class="text-3xl font-black text-white uppercase tracking-tighter leading-tight">{{ sale.title }}</h1>
                <div class="flex items-center justify-between mt-2">
                    <p class="text-3xl font-mono font-bold text-brand-neon">{{ parseFloat(sale.price).toLocaleString('ca-ES') }} €</p>
                    <p class="text-sm text-gray-400 font-bold uppercase tracking-wider flex items-center gap-1">
                        📍 {{ sale.location }}
                    </p>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Venedor: <span class="text-white">{{ sale.motorcycle?.user?.name || 'Rider' }}</span> 
                    • Publicat el {{ new Date(sale.created_at).toLocaleDateString('ca-ES') }}
                </p>
            </div>

            <div class="bg-brand-surface border border-brand-dark rounded-xl p-5 shadow-lg mb-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-32 h-32"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.492-3.053c.206-.252.427-.538.65-.83L15.17 11.42m-3.75 3.75-2.096-2.096m0 0a1.25 1.25 0 011.768-1.768m-1.768 1.768L3.25 21A2.652 2.652 0 010 17.25l7.583-7.583m0 0L11.42 11.42M11.42 11.42l3.053-2.492c.292-.223.578-.444.83-.65L11.42 11.42z" /></svg>
                </div>

                <h3 class="text-sm font-black text-brand-neon uppercase tracking-widest mb-4 border-b border-brand-dark pb-2">
                    Fitxa Tècnica
                </h3>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-y-6 gap-x-4">
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold">Marca</p>
                        <p class="text-sm text-white font-medium">{{ sale.motorcycle?.brand }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold">Model</p>
                        <p class="text-sm text-white font-medium">{{ sale.motorcycle?.model }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold">Any</p>
                        <p class="text-sm text-white font-medium">{{ sale.motorcycle?.year }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-brand-neon uppercase font-bold">Quilòmetres</p>
                        <p class="text-sm text-brand-neon font-bold font-mono">{{ parseFloat(sale.motorcycle?.current_km || 0).toLocaleString('ca-ES') }}</p>
                    </div>
                    
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold">Cilindrada</p>
                        <p class="text-sm text-white font-medium">{{ sale.motorcycle?.cc ? sale.motorcycle.cc + ' cc' : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold">Potència</p>
                        <p class="text-sm text-white font-medium">{{ sale.motorcycle?.power_cv ? sale.motorcycle.power_cv + ' CV' : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold">Carnet</p>
                        <p class="text-sm text-white font-medium">{{ sale.motorcycle?.license_type || '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold">Estil</p>
                        <p class="text-sm text-white font-medium">{{ sale.motorcycle?.type || '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-sm font-black text-brand-neon uppercase tracking-widest mb-3">Comentaris del venedor</h3>
                <div class="bg-brand-black border border-brand-dark rounded-xl p-5 text-gray-300 text-sm whitespace-pre-line shadow-inner leading-relaxed">
                    {{ sale.description || 'El venedor no ha afegit cap descripció addicional.' }}
                </div>
            </div>

            <div v-if="sale.motorcycle?.extras" class="mb-8">
                <h3 class="text-sm font-black text-brand-neon uppercase tracking-widest mb-3">Extres Equipats</h3>
                <div class="bg-brand-surface border border-brand-dark rounded-xl p-4 text-gray-300 text-sm whitespace-pre-line shadow-lg">
                    ✨ {{ sale.motorcycle.extras }}
                </div>
            </div>

            <div class="fixed bottom-16 left-0 w-full p-4 bg-brand-black/90 backdrop-blur-xl border-t border-brand-dark z-50">
                <div class="max-w-3xl mx-auto flex gap-3">
                    <a :href="`https://wa.me/${sale.motorcycle?.user?.phone}?text=Hola ${sale.motorcycle?.user?.name}, he vist el teu anunci a Riders i m'interessa la teva ${sale.motorcycle?.brand} ${sale.motorcycle?.model} per ${sale.price}€.`" 
                       target="_blank"
                       rel="noopener noreferrer"
                       class="flex-1 bg-[#25D366] text-white font-black uppercase tracking-wider py-4 rounded-xl shadow-[0_0_20px_rgba(37,211,102,0.3)] hover:bg-[#1ebd5a] hover:scale-[1.02] transition transform text-center flex items-center justify-center gap-2">
                        
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                        </svg>
                        Contactar per WhatsApp
                    </a>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    sale: Object
});
</script>
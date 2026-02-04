<template>
    <AppLayout :current-moto-id="moto.id">
        <div class="relative w-full h-64 bg-brand-surface border-b border-brand-dark overflow-hidden group">
            
            <div class="absolute inset-0 bg-gradient-to-br from-brand-dark to-brand-black opacity-80 flex items-center justify-center">
                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-32 h-32 text-brand-muted opacity-20">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                </svg>
            </div>
            
            <Link 
                :href="route('motorcycles.index')" 
                class="absolute top-4 right-4 z-20 text-white/80 hover:text-brand-neon hover:scale-110 transition duration-300 p-2 bg-black/40 rounded-full backdrop-blur-sm border border-white/10 shadow-lg"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                </svg>
            </Link>

            <div class="absolute bottom-0 left-0 w-full p-4 bg-gradient-to-t from-brand-black to-transparent">
                <h1 class="text-3xl font-black text-white italic tracking-tighter uppercase">{{ moto.brand }}</h1>
                <h2 class="text-xl text-brand-neon font-bold">{{ moto.model }}</h2>
                <p class="text-gray-400 text-sm mt-1">{{ moto.plate }} | {{ moto.year }}</p>
            </div>
        </div>

        <div class="p-4 space-y-4">
            
            <div class="bg-brand-surface rounded-xl p-6 border border-brand-dark shadow-neon flex justify-between items-center relative overflow-hidden">
                <div class="z-10">
                    <p class="text-gray-400 text-xs uppercase tracking-widest mb-1">Quilometratge Actual</p>
                    <p class="text-4xl font-mono font-bold text-white">{{ moto.current_km }} <span class="text-lg text-brand-neon">km</span></p>
                </div>
                
                <button @click="openKmModal" class="z-10 bg-brand-dark hover:bg-brand-neon hover:text-brand-black text-brand-neon p-3 rounded-full transition border border-brand-neon/30 shadow-[0_0_15px_rgba(12,225,181,0.3)]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </button>

                <div class="absolute right-0 top-0 w-32 h-32 bg-brand-neon blur-[60px] opacity-10 rounded-full pointer-events-none"></div>
            </div>

            <div class="pt-2">
                <h3 class="text-brand-muted font-bold text-sm uppercase mb-3 px-1">Tauler de Control</h3>
                <div class="grid grid-cols-2 gap-3">
                    
                    <Link :href="route('motorcycles.maintenance.index', moto.id)" class="flex items-center gap-3 bg-brand-surface border border-brand-dark rounded-xl p-4 hover:border-brand-neon hover:bg-brand-dark/50 transition group text-left">
                        <div class="bg-blue-500/20 p-2 rounded-lg text-blue-400 group-hover:text-blue-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.826-5.645" /></svg>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-200 text-sm">Manteniment</span>
                            <span class="block text-xs text-gray-500">Revisió d'oli, rodes...</span>
                        </div>
                    </Link>

                    <button class="flex items-center gap-3 bg-brand-surface border border-brand-dark rounded-xl p-4 hover:border-brand-neon hover:bg-brand-dark/50 transition group text-left">
                        <div class="bg-purple-500/20 p-2 rounded-lg text-purple-400 group-hover:text-purple-300">
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-200 text-sm">Documents</span>
                            <span class="block text-xs text-gray-500">ITV, Assegurança...</span>
                        </div>
                    </button>

                    <button class="flex items-center gap-3 bg-brand-surface border border-brand-dark rounded-xl p-4 hover:border-brand-neon hover:bg-brand-dark/50 transition group text-left">
                        <div class="bg-green-500/20 p-2 rounded-lg text-green-400 group-hover:text-green-300">
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-200 text-sm">Estadístiques</span>
                            <span class="block text-xs text-gray-500">Consums i despeses</span>
                        </div>
                    </button>

                </div>
             </div>
        </div>

        <div v-if="showKmModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            
            <div @click="closeKmModal" class="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity"></div>

            <div class="relative bg-brand-surface border border-brand-neon rounded-2xl shadow-[0_0_30px_rgba(12,225,181,0.3)] w-full max-w-sm overflow-hidden transform transition-all scale-100">
                
                <div class="bg-brand-black p-4 flex justify-between items-center border-b border-brand-dark">
                    <h3 class="text-white font-bold text-lg">Afegir Quilometratge</h3>
                    <button @click="closeKmModal" class="text-gray-400 hover:text-white">✕</button>
                </div>

                <div class="p-6">
                    <div class="flex flex-col items-end font-mono text-xl sm:text-2xl text-gray-300 mb-6 bg-brand-black/50 p-4 rounded-lg border border-brand-dark">
                        
                        <div class="mb-1 text-gray-500 flex items-center gap-2">
                             <span class="text-xs uppercase tracking-widest font-sans mr-2">Abans:</span>
                             {{ parseFloat(moto.current_km).toFixed(1) }}
                        </div>

                        <div class="flex items-center gap-2 text-brand-neon font-bold w-full justify-end">
                            <span class="text-2xl">+</span>
                            <input 
                                ref="kmInputRef"
                                v-model="addKmForm.km_to_add" 
                                type="number" 
                                step="0.1" 
                                placeholder="0" 
                                class="w-32 bg-transparent border-b-2 border-brand-neon text-right focus:ring-0 focus:border-white text-white font-bold p-1 text-2xl placeholder-brand-dark"
                                @keyup.enter="submitKm"
                            >
                        </div>

                        <div class="w-full h-0.5 bg-gray-500 my-2"></div>

                        <div class="text-white font-black flex items-center gap-2">
                             <span class="text-xs uppercase tracking-widest font-sans text-brand-muted mr-2">Total:</span>
                             {{ (parseFloat(moto.current_km) + (parseFloat(addKmForm.km_to_add) || 0)).toFixed(1) }}
                        </div>
                    </div>

                    <button 
                        @click="submitKm" 
                        :disabled="addKmForm.processing"
                        class="w-full bg-brand-neon text-brand-black font-bold py-3 rounded-lg shadow-neon hover:bg-white transition transform active:scale-95"
                    >
                        {{ addKmForm.processing ? 'Guardant...' : 'Confirmar Suma ✅' }}
                    </button>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    moto: Object
});

// Estat del Modal
const showKmModal = ref(false);
const kmInputRef = ref(null);

const addKmForm = useForm({
    km_to_add: '' 
});

const openKmModal = () => {
    showKmModal.value = true;
    addKmForm.km_to_add = ''; 
    nextTick(() => {
        if(kmInputRef.value) kmInputRef.value.focus();
    });
};

const closeKmModal = () => {
    showKmModal.value = false;
    addKmForm.reset();
};

const submitKm = () => {
    if (!addKmForm.km_to_add || addKmForm.km_to_add <= 0) return;

    addKmForm.post(route('motorcycles.add-km', props.moto.id), {
        onSuccess: () => closeKmModal(),
        preserveScroll: true
    });
};
</script>
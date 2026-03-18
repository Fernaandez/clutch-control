<template>
    <AppLayout :current-moto-id="moto.id">
        <div class="relative w-full h-64 bg-brand-surface border-b border-brand-dark overflow-hidden group">
            <!-- Background Image -->
            <img v-if="moto.photo" :src="$page.props.storageUrl + '/' + moto.photo" alt="Moto Background" class="absolute inset-0 w-full h-full object-cover">
            
            <!-- Overlay -->
            <div :class="['absolute inset-0 flex items-center justify-center', moto.photo ? 'photo-gradient-overlay' : 'bg-gradient-to-br from-brand-dark to-brand-black opacity-80']">
                 <svg v-if="!moto.photo" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-32 h-32 text-brand-muted opacity-20">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                </svg>
            </div>
            <Link :href="route('motorcycles.index')" class="absolute top-4 right-4 z-20 text-white/80 hover:text-brand-neon hover:scale-110 transition duration-300 p-2 bg-black/40 rounded-full backdrop-blur-sm border border-white/10 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" /></svg>
            </Link>
            <div class="absolute bottom-0 left-0 w-full p-4 photo-gradient-bottom">
                <div class="inline-block bg-black/65 backdrop-blur-sm border border-white/15 rounded-xl px-4 py-3 shadow-lg">
                    <h1 class="text-2xl sm:text-3xl font-black italic tracking-tighter uppercase line-clamp-1" style="color: white;">{{ moto.brand }}</h1>
                    <h2 class="text-lg sm:text-xl text-brand-neon font-bold line-clamp-1">{{ moto.model }}</h2>
                    <p class="text-gray-300 text-xs sm:text-sm mt-0.5">{{ moto.plate }} <span v-if="moto.plate">|</span> {{ moto.year }}</p>
                </div>
            </div>
        </div>

        <div class="p-4 space-y-4">
            <div class="bg-brand-surface rounded-xl p-6 border border-brand-dark shadow-neon flex justify-between items-center relative overflow-hidden">
                <div class="z-10">
                    <p class="text-gray-400 text-xs uppercase tracking-widest mb-1">Quilometratge Actual</p>
                    <p class="text-4xl font-mono font-bold text-white">{{ moto.current_km }} <span class="text-lg text-brand-neon">km</span></p>
                </div>
                <button @click="openKmModal" class="z-10 bg-brand-dark hover:bg-brand-neon hover:text-brand-black text-brand-neon p-3 rounded-full transition border border-brand-neon/30 shadow-[0_0_15px_rgba(12,225,181,0.3)]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                </button>
                <div class="absolute right-0 top-0 w-32 h-32 bg-brand-neon blur-[60px] opacity-10 rounded-full pointer-events-none"></div>
            </div>

            <div class="pt-2">
                <h3 class="text-brand-muted font-bold text-sm uppercase mb-3 px-1">Tauler de Control</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    
                    <Link :href="route('motorcycles.maintenance.index', moto.id)" class="sm:col-span-2 flex items-center gap-3 bg-brand-surface border border-brand-dark rounded-xl p-4 hover:border-brand-neon hover:bg-brand-dark/50 transition group text-left">
                        <div class="bg-blue-500/20 p-2 sm:p-3 rounded-lg text-blue-400 group-hover:text-blue-300 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-200 text-sm">Manteniment</span>
                            <span class="block text-xs text-gray-500">Oli, frens, revisions...</span>
                        </div>
                    </Link>

                    <Link :href="route('motorcycles.repairs.index', moto.id)" class="flex items-center gap-3 bg-brand-surface border border-brand-dark rounded-xl p-4 hover:border-red-500 hover:bg-brand-dark/50 transition group text-left">
                        <div class="bg-red-500/20 p-2 sm:p-3 rounded-lg text-red-400 group-hover:text-red-300 flex-shrink-0">
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" /></svg>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-200 text-sm">Reparacions</span>
                            <span class="block text-xs text-gray-500">Avaries i fallos</span>
                        </div>
                    </Link>

                    <Link :href="route('motorcycles.upgrades.index', moto.id)" class="flex items-center gap-3 bg-brand-surface border border-brand-dark rounded-xl p-4 hover:border-purple-500 hover:bg-brand-dark/50 transition group text-left">
                        <div class="bg-purple-500/20 p-2 sm:p-3 rounded-lg text-purple-400 group-hover:text-purple-300 flex-shrink-0">
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" /></svg>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-200 text-sm">Millores</span>
                            <span class="block text-xs text-gray-500">Extres i mods</span>
                        </div>
                    </Link>

                    <Link :href="route('motorcycles.global-history', moto.id)" class="sm:col-span-2 flex items-center gap-3 bg-brand-surface border border-brand-dark rounded-xl p-4 hover:border-yellow-500 hover:bg-brand-dark/50 transition group text-left">
                        <div class="bg-yellow-500/20 p-2 sm:p-3 rounded-lg text-yellow-400 group-hover:text-yellow-300 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-200 text-sm">Historial Complet</span>
                            <span class="block text-xs text-gray-500">Timeline de tota la vida de la moto</span>
                        </div>
                    </Link>

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
                             <span class="text-xs uppercase tracking-widest font-sans mr-2">Abans:</span>{{ Math.round(moto.current_km) }}
                        </div>
                        <div class="flex items-center gap-2 text-brand-neon font-bold w-full justify-end">
                            <span class="text-2xl">+</span>
                            <input ref="kmInputRef" v-model="addKmForm.km_to_add" type="number" step="1" placeholder="0" class="w-32 bg-transparent border-b-2 border-brand-neon text-right focus:ring-0 focus:border-white text-white font-bold p-1 text-2xl placeholder-brand-dark" @keyup.enter="submitKm">
                        </div>
                        <div class="w-full h-0.5 bg-gray-500 my-2"></div>
                        <div class="text-white font-black flex items-center gap-2">
                             <span class="text-xs uppercase tracking-widest font-sans text-brand-muted mr-2">Total:</span>{{ Math.round(moto.current_km) + Math.round(addKmForm.km_to_add || 0) }}
                        </div>
                    </div>
                    <button @click="submitKm" :disabled="addKmForm.processing" class="w-full bg-brand-neon text-brand-black font-bold py-3 rounded-lg shadow-neon hover:bg-white transition transform active:scale-95">
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

const props = defineProps({ moto: Object });
const showKmModal = ref(false);
const kmInputRef = ref(null);
const addKmForm = useForm({ km_to_add: '' });

const openKmModal = () => { showKmModal.value = true; addKmForm.km_to_add = ''; nextTick(() => { if(kmInputRef.value) kmInputRef.value.focus(); }); };
const closeKmModal = () => { showKmModal.value = false; addKmForm.reset(); };
const submitKm = () => { if (!addKmForm.km_to_add || addKmForm.km_to_add <= 0) return; addKmForm.post(route('motorcycles.add-km', props.moto.id), { onSuccess: () => closeKmModal(), preserveScroll: true }); };
</script>

<template>
    <AppLayout title="Quedades">
        <div class="px-4 py-6 pb-24">
            
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-black text-white uppercase tracking-tighter">Comunitat <span class="text-brand-neon">Riders</span></h1>
                    <p class="text-gray-400 text-sm">Uneix-te al grup i gas! ✌️</p>
                </div>
                <Link :href="route('events.create')" class="bg-brand-neon text-brand-black p-3 rounded-full shadow-[0_0_15px_rgba(12,225,181,0.4)] hover:scale-110 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                </Link>
            </div>

            <div class="flex flex-col gap-4 mb-8">
                <Link :href="route('events.mine')" class="w-full bg-brand-surface border border-brand-neon/30 text-white py-3 rounded-xl flex items-center justify-between px-4 hover:bg-brand-neon/10 transition group">
                    <span class="font-bold uppercase flex items-center gap-2">📅 Gestionar les meves Quedades</span>
                    <span class="text-brand-neon group-hover:translate-x-1 transition">&rarr;</span>
                </Link>

                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">🔍</div>
                        <input v-model="searchQuery" type="text" placeholder="Buscar ruta..." class="w-full bg-brand-surface border-brand-dark rounded-xl pl-9 text-sm text-white focus:border-brand-neon focus:ring-0 placeholder-gray-600">
                    </div>
                    <button @click="onlyWeekend = !onlyWeekend" class="px-3 rounded-xl border transition flex items-center gap-1" :class="onlyWeekend ? 'bg-brand-neon/20 border-brand-neon text-brand-neon' : 'bg-brand-surface border-brand-dark text-gray-500'">
                        <span class="text-[10px] font-bold uppercase">Weekend</span>
                    </button>
                </div>
            </div>

            <div v-if="filteredEvents.length === 0" class="flex flex-col items-center justify-center py-12 text-center opacity-60">
                <p class="text-gray-400 font-medium">No hi ha quedades públiques properes.</p>
            </div>

            <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="event in filteredEvents" :key="event.id" class="bg-brand-surface rounded-xl overflow-hidden border border-brand-dark shadow-lg hover:border-brand-neon transition duration-300 flex flex-col">
                    
                    <div class="h-40 bg-gray-900 relative w-full overflow-hidden">
                        <div class="absolute inset-0 opacity-30 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-surface via-transparent to-transparent"></div>
                        
                        <div class="absolute top-2 right-2 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide z-[400] bg-brand-black/80 text-brand-neon border border-brand-dark">
                            {{ new Date(event.start_time).toLocaleDateString('ca-ES', { day: '2-digit', month: 'short' }) }}
                        </div>
                        <div v-if="event.is_attending" class="absolute top-2 left-2 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide z-[400] bg-green-500/80 text-white">Hi vas</div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-white mb-1 truncate uppercase">{{ event.title }}</h3>
                            
                            <div class="flex items-center justify-between text-xs text-gray-300 bg-brand-black/30 p-2 rounded-lg mt-2">
                                <span class="truncate max-w-[50%] flex items-center gap-1">
                                    📍 {{ event.location || 'Pendent' }}
                                </span>
                                
                                <span class="font-mono font-bold flex items-center gap-1" 
                                      :class="{'text-red-400': event.max_participants && event.participants_count >= event.max_participants, 'text-brand-neon': !event.max_participants}">
                                    
                                    👤 
                                    <span v-if="event.max_participants">
                                        {{ event.participants_count }} / {{ event.max_participants }}
                                    </span>
                                    <span v-else>
                                        {{ event.participants_count }} <span class="text-lg leading-none align-middle">∞</span>
                                    </span>
                                    
                                    <span v-if="event.max_participants && event.participants_count >= event.max_participants" class="ml-1 text-[9px] bg-red-500 text-black px-1 rounded uppercase">FULL</span>
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4 pt-4 border-t border-brand-dark/50">
                            <Link :href="route('events.show', event.id)" class="flex-1 text-center bg-brand-dark hover:bg-white hover:text-black text-white text-xs font-bold uppercase py-2 rounded transition">
                                Veure Detall
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ events: Array });
const searchQuery = ref('');
const onlyWeekend = ref(false);

const filteredEvents = computed(() => {
    let result = props.events;
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        result = result.filter(e => e.title.toLowerCase().includes(q) || (e.location && e.location.toLowerCase().includes(q)));
    }
    if (onlyWeekend.value) {
        result = result.filter(e => {
            const day = new Date(e.start_time).getDay();
            return day === 0 || day === 6;
        });
    }
    return result;
});
</script>
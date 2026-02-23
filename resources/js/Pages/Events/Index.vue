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

            <Link :href="route('events.mine')" class="w-full mb-4 bg-brand-surface border border-brand-neon/30 text-white py-3 rounded-xl flex items-center justify-between px-4 hover:bg-brand-neon/10 transition group shadow-lg">
                <span class="font-bold uppercase flex items-center gap-2 text-sm">
                    📅 Gestionar les meves Quedades
                </span>
                <span class="text-brand-neon group-hover:translate-x-1 transition">&rarr;</span>
            </Link>

            <button 
                @click="showFilters = !showFilters"
                class="w-full mb-6 flex items-center justify-between bg-brand-surface border border-brand-dark p-3 rounded-xl text-sm font-bold text-gray-300 hover:text-white hover:border-brand-neon transition"
            >
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>
                    {{ showFilters ? 'Amagar Filtres' : 'Mostrar Filtres i Ordenació' }}
                </span>
                <span v-if="activeFiltersCount > 0" class="bg-brand-neon text-brand-black text-xs rounded-full w-5 h-5 flex items-center justify-center font-black">
                    {{ activeFiltersCount }}
                </span>
                <span v-else>▼</span>
            </button>

            <div v-if="showFilters" class="bg-brand-black border border-brand-dark rounded-xl p-4 mb-6 shadow-inner space-y-4 animate-fade-in">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">Cercar</label>
                        <input v-model="filters.search" type="text" placeholder="Títol o lloc..." class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">Disponibilitat</label>
                        <select v-model="filters.availability" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                            <option value="all">Totes</option>
                            <option value="available">✅ Només amb places lliures</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">Data Inici (Des de)</label>
                        <input v-model="filters.dateStart" type="date" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1 [color-scheme:dark]">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">Data Fi (Fins)</label>
                        <input v-model="filters.dateEnd" type="date" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1 [color-scheme:dark]">
                    </div>
                </div>

                <div class="flex items-center gap-2 mt-2">
                    <input type="checkbox" id="weekend" v-model="filters.onlyWeekend" class="rounded bg-brand-surface border-brand-dark text-brand-neon focus:ring-brand-neon focus:ring-offset-gray-900">
                    <label for="weekend" class="text-sm text-gray-300 font-bold">Només Quedades de Cap de Setmana</label>
                </div>

                <div class="border-t border-brand-dark pt-3 mt-2">
                    <label class="text-xs text-brand-neon uppercase font-bold mb-2 block">Ordenar per:</label>
                    <div class="flex gap-2">
                        <select v-model="filters.sortBy" class="flex-1 bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon">
                            <option value="start_time">Data de la Quedada</option>
                            <option value="participants_count">Nombre d'Assistents</option>
                        </select>
                        <button @click="toggleSortDir" class="bg-brand-surface border border-brand-dark px-3 rounded-lg text-white hover:border-brand-neon transition">
                            {{ filters.sortDir === 'asc' ? '⬆ Asc' : '⬇ Desc' }}
                        </button>
                    </div>
                </div>
                
                <button @click="resetFilters" class="w-full text-xs text-gray-500 hover:text-white underline mt-2">
                    Netejar tots els filtres
                </button>
            </div>

            <div v-if="filteredEvents.length === 0" class="flex flex-col items-center justify-center py-12 text-center opacity-60 bg-brand-surface rounded-xl border border-brand-dark border-dashed">
                <p class="text-gray-400 font-medium">No s'han trobat quedades properes amb aquests filtres.</p>
                <button @click="resetFilters" class="text-brand-neon text-sm font-bold mt-2 hover:underline">Netejar Filtres</button>
            </div>

            <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="event in filteredEvents" :key="event.id" class="bg-brand-surface rounded-xl overflow-hidden border border-brand-dark shadow-lg hover:border-brand-neon transition duration-300 flex flex-col animate-fade-in">
                    
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
const showFilters = ref(false);

const filters = ref({
    search: '',
    availability: 'all',
    dateStart: '',
    dateEnd: '',
    onlyWeekend: false,
    sortBy: 'start_time',
    sortDir: 'asc' // Default ascèndent perquè les primeres en passar surtin primer
});

const activeFiltersCount = computed(() => {
    let count = 0;
    if (filters.value.search) count++;
    if (filters.value.availability !== 'all') count++;
    if (filters.value.dateStart || filters.value.dateEnd) count++;
    if (filters.value.onlyWeekend) count++;
    return count;
});

const toggleSortDir = () => {
    filters.value.sortDir = filters.value.sortDir === 'asc' ? 'desc' : 'asc';
};

const resetFilters = () => {
    filters.value = {
        search: '',
        availability: 'all',
        dateStart: '',
        dateEnd: '',
        onlyWeekend: false,
        sortBy: 'start_time',
        sortDir: 'asc'
    };
};

const filteredEvents = computed(() => {
    let result = [...props.events];
    
    // 1. Filtre text
    if (filters.value.search) {
        const q = filters.value.search.toLowerCase();
        result = result.filter(e => e.title.toLowerCase().includes(q) || (e.location && e.location.toLowerCase().includes(q)));
    }
    
    // 2. Filtre Disponibilitat
    if (filters.value.availability === 'available') {
        result = result.filter(e => !e.max_participants || e.participants_count < e.max_participants);
    }

    // 3. Filtre Data
    if (filters.value.dateStart) {
        result = result.filter(e => e.start_time >= filters.value.dateStart);
    }
    if (filters.value.dateEnd) {
        // Afegim 'T23:59:59' perquè inclogui tot el dia final
        result = result.filter(e => e.start_time <= filters.value.dateEnd + 'T23:59:59');
    }

    // 4. Filtre Cap de Setmana
    if (filters.value.onlyWeekend) {
        result = result.filter(e => {
            const day = new Date(e.start_time).getDay();
            return day === 0 || day === 6; // 0 Diumenge, 6 Dissabte
        });
    }

    // 5. Ordenació
    return result.sort((a, b) => {
        let fieldA = a[filters.value.sortBy];
        let fieldB = b[filters.value.sortBy];

        if (filters.value.sortBy === 'participants_count') {
            fieldA = parseInt(fieldA || 0);
            fieldB = parseInt(fieldB || 0);
        }

        if (fieldA < fieldB) return filters.value.sortDir === 'asc' ? -1 : 1;
        if (fieldA > fieldB) return filters.value.sortDir === 'asc' ? 1 : -1;
        return 0;
    });
});
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
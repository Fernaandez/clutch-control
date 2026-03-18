<template>
    <AppLayout title="Les meves Quedades">
        <div class="px-4 py-6 pb-24">
            
            <div class="flex items-center justify-between mb-6">
                <Link :href="route('events.index')" class="text-gray-500 hover:text-white flex items-center gap-1 text-sm">
                    &larr; Tornar a Públiques
                </Link>
            </div>

            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-white uppercase tracking-tighter">La meva <span class="text-brand-neon">Agenda</span></h1>
                    <p class="text-gray-400 text-sm">Esdeveniments que organitzes o hi participes.</p>
                </div>
            </div>

            <button 
                v-if="events.length > 0"
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

            <div v-if="showFilters && events.length > 0" class="bg-brand-black border border-brand-dark rounded-xl p-4 mb-6 shadow-inner space-y-4 animate-fade-in">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">Cercar</label>
                        <input v-model="filters.search" type="text" placeholder="Títol o lloc..." class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">El teu Rol</label>
                        <select v-model="filters.role" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                            <option value="all">Tots</option>
                            <option value="organizer">Només Organitzant</option>
                            <option value="participant">Només Participant</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">Anteriors a (Data)</label>
                        <input v-model="filters.dateEnd" type="date" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1 [color-scheme:dark]">
                    </div>
                </div>

                <div class="border-t border-brand-dark pt-3 mt-2">
                    <label class="text-xs text-brand-neon uppercase font-bold mb-2 block">Ordenar per:</label>
                    <div class="flex gap-2">
                        <select v-model="filters.sortBy" class="flex-1 bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon">
                            <option value="start_time">Data de la Quedada</option>
                            <option value="created_at">Creació</option>
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

            <div v-if="events.length === 0" class="flex flex-col items-center justify-center py-12 text-center opacity-60">
                <p class="text-gray-400 font-medium">No tens cap quedada programada.</p>
                <Link :href="route('events.create')" class="mt-4 text-brand-neon hover:underline">Crear-ne una ara</Link>
            </div>

            <div v-else-if="filteredEvents.length === 0" class="flex flex-col items-center justify-center py-12 text-center opacity-60 bg-brand-surface rounded-xl border border-brand-dark border-dashed">
                <p class="text-gray-400 font-medium">No s'ha trobat cap quedada amb aquests filtres.</p>
                <button @click="resetFilters" class="text-brand-neon text-sm font-bold mt-2 hover:underline">Netejar Filtres</button>
            </div>

            <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="event in filteredEvents" :key="event.id" class="bg-brand-surface rounded-xl overflow-hidden border border-brand-dark shadow-lg flex flex-col animate-fade-in">
                    
                    <div class="h-32 bg-gray-900 relative w-full overflow-hidden">
                        <div class="absolute inset-0 opacity-30 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
                        
                        <div v-if="event.user_id === $page.props.auth.user.id" class="absolute top-2 left-2 bg-brand-neon text-black px-2 py-1 rounded text-[10px] font-bold uppercase">👑 Organitzes</div>
                        <div v-else class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 rounded text-[10px] font-bold uppercase">✅ Participes</div>

                        <div v-if="!event.is_public" class="absolute bottom-2 right-2 bg-red-500/20 text-red-400 border border-red-500/50 px-2 py-0.5 rounded text-[10px] font-bold uppercase">🔒 Privada</div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start">
                                <h3 class="text-lg font-bold text-white mb-1 truncate uppercase">{{ event.title }}</h3>
                                <span class="text-brand-neon text-xs font-bold">{{ new Date(event.start_time).toLocaleDateString('ca-ES', { day: '2-digit', month: 'short' }) }}</span>
                            </div>
                            
                            <div class="flex items-center gap-2 text-xs text-gray-400 mt-2">
                                <span>📍 {{ event.location || 'Sense lloc' }}</span>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4 pt-4 border-t border-brand-dark/50">
                            
                            <Link :href="route('events.show', event.id)" class="flex-1 text-center bg-brand-dark hover:bg-white hover:text-black text-white text-xs font-bold uppercase py-2 rounded transition">
                                Veure
                            </Link>

                            <template v-if="event.user_id === $page.props.auth.user.id">
                                <Link :href="route('events.edit', event.id)" class="px-3 flex items-center justify-center bg-brand-dark border border-gray-600 hover:border-brand-neon hover:text-brand-neon text-gray-400 rounded transition">
                                    ✏️
                                </Link>
                                <button @click="deleteEvent(event.id)" class="px-3 flex items-center justify-center bg-brand-dark border border-red-900/50 text-red-700 hover:bg-red-500 hover:text-white hover:border-red-500 rounded transition">
                                    🗑️
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    events: Array
});

const page = usePage();
const authUserId = page.props.auth.user.id;

const showFilters = ref(false);

const filters = ref({
    search: '',
    role: 'all',
    dateEnd: '',
    sortBy: 'start_time',
    sortDir: 'desc'
});

const activeFiltersCount = computed(() => {
    let count = 0;
    if (filters.value.search) count++;
    if (filters.value.role !== 'all') count++;
    if (filters.value.dateEnd) count++;
    return count;
});

const toggleSortDir = () => {
    filters.value.sortDir = filters.value.sortDir === 'asc' ? 'desc' : 'asc';
};

const resetFilters = () => {
    filters.value = {
        search: '',
        role: 'all',
        dateEnd: '',
        sortBy: 'start_time',
        sortDir: 'desc'
    };
};

const filteredEvents = computed(() => {
    let result = [...props.events];
    
    // Filtre text
    if (filters.value.search) {
        const q = filters.value.search.toLowerCase();
        result = result.filter(e => e.title.toLowerCase().includes(q) || (e.location && e.location.toLowerCase().includes(q)));
    }
    
    // Filtre Rol (Organitzador vs Participant)
    if (filters.value.role !== 'all') {
        if (filters.value.role === 'organizer') {
            result = result.filter(e => e.user_id === authUserId);
        } else {
            result = result.filter(e => e.user_id !== authUserId);
        }
    }

    // Filtre Data
    if (filters.value.dateEnd) {
        result = result.filter(e => e.start_time <= filters.value.dateEnd + 'T23:59:59');
    }

    // Ordenació
    return result.sort((a, b) => {
        let fieldA = a[filters.value.sortBy];
        let fieldB = b[filters.value.sortBy];

        if (fieldA < fieldB) return filters.value.sortDir === 'asc' ? -1 : 1;
        if (fieldA > fieldB) return filters.value.sortDir === 'asc' ? 1 : -1;
        return 0;
    });
});

const deleteEvent = (id) => {
    if (confirm("Segur que vols eliminar aquesta quedada?")) {
        router.delete(route('events.destroy', id));
    }
};
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

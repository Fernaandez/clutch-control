<template>
    <AppLayout :title="$t('events.my_agenda_title')">
        <div class="px-4 py-6 pb-24">
            
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <button @click="() => window.history.back()" class="w-10 h-10 rounded-full bg-brand-neon flex items-center justify-center text-black hover:bg-white transition flex-shrink-0 shadow-[0_0_15px_rgba(12,225,181,0.3)]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                    </button>
                    <h1 class="text-2xl font-black uppercase tracking-tighter text-white leading-none">LA MEVA AGENDA</h1>
                </div>
            </div>

            <button 
                v-if="events.length > 0"
                @click="showFilters = !showFilters"
                class="w-full mb-6 flex items-center justify-between bg-brand-surface border border-brand-dark p-3 rounded-xl text-sm font-bold text-gray-300 hover:text-white hover:border-brand-neon transition"
            >
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>
                    {{ showFilters ? $t('events.hide_filters') : $t('events.show_filters') }}
                </span>
                <span v-if="activeFiltersCount > 0" class="bg-brand-neon text-brand-black text-xs rounded-full w-5 h-5 flex items-center justify-center font-black">
                    {{ activeFiltersCount }}
                </span>
                <span v-else>▼</span>
            </button>

            <div v-if="showFilters && events.length > 0" class="bg-brand-black border border-brand-dark rounded-xl p-4 mb-6 shadow-inner space-y-4 animate-fade-in">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('events.search') }}</label>
                        <input v-model="filters.search" type="text" :placeholder="$t('events.search_placeholder')" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('events.my_role') }}</label>
                        <select v-model="filters.role" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                            <option value="all">{{ $t('events.role_all') }}</option>
                            <option value="organizer">{{ $t('events.role_organizer') }}</option>
                            <option value="participant">{{ $t('events.role_participant') }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('events.before_date') }}</label>
                        <input v-model="filters.dateEnd" type="date" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1 [color-scheme:dark]">
                    </div>
                </div>

                <div class="border-t border-brand-dark pt-3 mt-2">
                    <label class="text-xs text-brand-neon uppercase font-bold mb-2 block">{{ $t('events.sort_by') }}</label>
                    <div class="flex gap-2">
                        <select v-model="filters.sortBy" class="flex-1 bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon">
                            <option value="start_time">{{ $t('events.sort_date') }}</option>
                            <option value="created_at">{{ $t('events.sort_creation') }}</option>
                        </select>
                        <button @click="toggleSortDir" class="bg-brand-surface border border-brand-dark px-3 rounded-lg text-white hover:border-brand-neon transition">
                            {{ filters.sortDir === 'asc' ? $t('common.asc') : $t('common.desc') }}
                        </button>
                    </div>
                </div>
                
                <button @click="resetFilters" class="w-full text-xs text-gray-500 hover:text-white underline mt-2">
                    {{ $t('events.clear_all_filters') }}
                </button>
            </div>

            <div v-if="events.length === 0" class="flex flex-col items-center justify-center py-12 text-center opacity-60">
                <p class="text-gray-400 font-medium">{{ $t('events.no_events') }}</p>
                <Link :href="route('events.create')" class="mt-4 text-brand-neon hover:underline">{{ $t('events.create_one') }}</Link>
            </div>

            <div v-else-if="filteredEvents.length === 0" class="flex flex-col items-center justify-center py-12 text-center opacity-60 bg-brand-surface rounded-xl border border-brand-dark border-dashed">
                <p class="text-gray-400 font-medium">{{ $t('events.no_filter_results') }}</p>
                <button @click="resetFilters" class="text-brand-neon text-sm font-bold mt-2 hover:underline">{{ $t('events.clear_filters') }}</button>
            </div>

            <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="event in filteredEvents" :key="event.id" class="bg-brand-surface rounded-xl overflow-hidden border border-brand-dark shadow-lg flex flex-col animate-fade-in">
                    
                    <div class="h-32 bg-gray-900 relative w-full overflow-hidden">
                        <div class="absolute inset-0 opacity-30 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
                        
                        <div v-if="event.user_id === $page.props.auth.user.id" class="absolute top-2 left-2 bg-brand-neon text-black px-2 py-1 rounded text-[10px] font-bold uppercase">{{ $t('events.you_organize') }}</div>
                        <div v-else class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 rounded text-[10px] font-bold uppercase">{{ $t('events.you_attend') }}</div>

                        <div v-if="!event.is_public" class="absolute bottom-2 right-2 bg-red-500/20 text-red-400 border border-red-500/50 px-2 py-0.5 rounded text-[10px] font-bold uppercase">{{ $t('events.private') }}</div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex flex-col gap-1 text-[11px] text-gray-300 bg-brand-black/30 p-2 rounded-lg mt-2">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-lg font-bold text-white mb-1 truncate uppercase">{{ event.title }}</h3>
                                    <span class="bg-brand-black text-brand-neon text-[10px] font-bold px-2 py-1 rounded shadow-[0_0_15px_rgba(0,0,0,0.9)] border border-brand-neon/30">{{ new Date(event.start_time).toLocaleDateString(currentLocale, { day: '2-digit', month: 'short' }) }}</span>
                                </div>
                                <div class="flex items-center justify-between border-t border-brand-dark/50 pt-1 mt-1">
                                    <span class="truncate flex items-center gap-1">📍 {{ event.location || $t('events.no_location') }}</span>
                                </div>
                                <div class="flex items-center justify-between text-gray-400 border-t border-brand-dark/50 pt-1 mt-1">
                                    <span class="flex items-center gap-1">🛣️ {{ event.routes_count || 0 }} Rutes</span>
                                    <span class="font-mono font-bold bg-brand-dark px-1.5 py-0.5 rounded text-white shadow-inner">{{ parseFloat(event.total_km || 0).toFixed(1) }} KM</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4 pt-4 border-t border-brand-dark/50">
                            
                            <Link :href="route('events.show', event.id)" class="flex-1 text-center bg-brand-dark hover:bg-white hover:text-black text-white text-xs font-bold uppercase py-2 rounded transition">
                                {{ $t('events.view') }}
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
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const { locale, t } = useI18n();
const currentLocale = computed(() => locale.value + '-ES');

const props = defineProps({ events: Array });

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
    filters.value = { search: '', role: 'all', dateEnd: '', sortBy: 'start_time', sortDir: 'desc' };
};

const filteredEvents = computed(() => {
    let result = [...props.events];
    if (filters.value.search) {
        const q = filters.value.search.toLowerCase();
        result = result.filter(e => e.title.toLowerCase().includes(q) || (e.location && e.location.toLowerCase().includes(q)));
    }
    if (filters.value.role !== 'all') {
        if (filters.value.role === 'organizer') result = result.filter(e => e.user_id === authUserId);
        else result = result.filter(e => e.user_id !== authUserId);
    }
    if (filters.value.dateEnd) {
        result = result.filter(e => e.start_time <= filters.value.dateEnd + 'T23:59:59');
    }
    return result.sort((a, b) => {
        let fieldA = a[filters.value.sortBy];
        let fieldB = b[filters.value.sortBy];
        if (fieldA < fieldB) return filters.value.sortDir === 'asc' ? -1 : 1;
        if (fieldA > fieldB) return filters.value.sortDir === 'asc' ? 1 : -1;
        return 0;
    });
});

const deleteEvent = (id) => {
    if (confirm(t('events.delete_confirm'))) {
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

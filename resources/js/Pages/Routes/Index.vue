<template>
    <AppLayout :title="$t('routes.explore_title')">
        <div class="px-4 py-6 pb-24">
            
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-black text-white uppercase tracking-tighter">RUTES</h1>
                </div>
                <div class="relative z-[60]">
                    <button
                        type="button"
                        @click="showQuickActions = !showQuickActions"
                        class="bg-brand-neon text-brand-black p-3 rounded-full shadow-[0_0_15px_rgba(12,225,181,0.4)] hover:scale-110 transition"
                        :aria-expanded="showQuickActions"
                        aria-haspopup="menu"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    </button>

                    <Transition
                        enter-active-class="transition ease-out duration-150"
                        enter-from-class="opacity-0 scale-95 -translate-y-1"
                        enter-to-class="opacity-100 scale-100 translate-y-0"
                        leave-active-class="transition ease-in duration-100"
                        leave-from-class="opacity-100 scale-100 translate-y-0"
                        leave-to-class="opacity-0 scale-95 -translate-y-1"
                    >
                        <div
                            v-if="showQuickActions"
                            class="absolute right-0 mt-2 w-52 bg-brand-surface border border-brand-dark rounded-xl shadow-2xl overflow-hidden z-[70]"
                            role="menu"
                        >
                            <Link
                                :href="route('routes.create')"
                                class="w-full flex items-center gap-3 px-4 py-3 text-sm text-white hover:bg-brand-dark transition"
                                role="menuitem"
                                @click="showQuickActions = false"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 text-brand-neon"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                {{ newRouteActionLabel }}
                            </Link>

                            <button
                                type="button"
                                class="w-full flex items-center gap-3 px-4 py-3 text-sm text-white hover:bg-brand-dark transition disabled:opacity-50 disabled:cursor-not-allowed"
                                role="menuitem"
                                :disabled="!defaultMotorcycleId"
                                @click="goToFreeRide"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 text-brand-neon"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 5.5-7.5 11.25-7.5 11.25S4.5 16 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                                {{ $t('free_ride.title') }}
                            </button>
                        </div>
                    </Transition>
                </div>
            </div>

            <Link :href="route('routes.MyRoutes')" class="w-full mb-4 bg-brand-surface border border-brand-neon/30 text-white py-3 rounded-xl flex items-center justify-between px-4 hover:bg-brand-neon/10 transition group shadow-lg">
                <span class="font-bold uppercase flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-brand-neon"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" /></svg>
                    {{ $t('routes.manage_mine') }}
                </span>
                <span class="text-brand-neon group-hover:translate-x-1 transition">&rarr;</span>
            </Link>

            <button 
                @click="showFilters = !showFilters"
                class="w-full mb-6 flex items-center justify-between bg-brand-surface border border-brand-dark p-3 rounded-xl text-sm font-bold text-gray-300 hover:text-white hover:border-brand-neon transition"
            >
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>
                    {{ showFilters ? $t('routes.hide_filters') : $t('routes.show_filters') }}
                </span>
                <span v-if="activeFiltersCount > 0" class="bg-brand-neon text-brand-black text-xs rounded-full w-5 h-5 flex items-center justify-center font-black">
                    {{ activeFiltersCount }}
                </span>
                <span v-else>▼</span>
            </button>

            <div v-if="showFilters" class="bg-brand-black border border-brand-dark rounded-xl p-4 mb-6 shadow-inner space-y-4 animate-fade-in">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('routes.search') }}</label>
                        <input v-model="filters.search" type="text" placeholder="Ex: Collada..." class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('routes.difficulty') }}</label>
                        <select v-model="filters.difficulty" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                            <option value="all">{{ $t('routes.difficulty_all') }}</option>
                            <option value="easy">{{ $t('routes.difficulty_easy') }}</option>
                            <option value="medium">{{ $t('routes.difficulty_medium') }}</option>
                            <option value="hard">{{ $t('routes.difficulty_hard') }}</option>
                            <option value="expert">{{ $t('routes.difficulty_expert') }}</option>
                            <option value="extreme">{{ $t('routes.difficulty_extreme') }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('routes.km_min') }}</label>
                        <input v-model="filters.kmMin" type="number" placeholder="0" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('routes.km_max') }}</label>
                        <input v-model="filters.kmMax" type="number" placeholder="Max" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                    </div>
                </div>

                <div>
                    <label class="text-xs text-gray-500 uppercase font-bold">{{ $t('routes.rating_min') }}</label>
                    <select v-model="filters.ratingMin" class="w-full bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon mt-1">
                        <option value="0">{{ $t('routes.rating_all') }}</option>
                        <option value="3">⭐⭐⭐ 3+</option>
                        <option value="4">⭐⭐⭐⭐ 4+</option>
                        <option value="5">⭐⭐⭐⭐⭐ 5</option>
                    </select>
                </div>

                <div class="border-t border-brand-dark pt-3 mt-2">
                    <label class="text-xs text-brand-neon uppercase font-bold mb-2 block">{{ $t('routes.sort_by') }}</label>
                    <div class="flex gap-2">
                        <select v-model="filters.sortBy" class="flex-1 bg-brand-surface border-brand-dark rounded-lg text-white text-sm focus:border-brand-neon">
                            <option value="created_at">{{ $t('routes.sort_date') }}</option>
                            <option value="planned_distance_km">{{ $t('routes.sort_km') }}</option>
                            <option value="avg_rating">{{ $t('routes.sort_rating') }}</option>
                        </select>
                        <button @click="toggleSortDir" class="bg-brand-surface border border-brand-dark px-3 rounded-lg text-white hover:border-brand-neon transition">
                            {{ filters.sortDir === 'desc' ? $t('common.desc') : $t('common.asc') }}
                        </button>
                    </div>
                </div>
                
                <button @click="resetFilters" class="w-full text-xs text-gray-500 hover:text-white underline mt-2">
                    {{ $t('routes.clear_all_filters') }}
                </button>
            </div>

            <div v-if="filteredRoutes.length === 0" class="flex flex-col items-center justify-center py-12 text-center opacity-60 bg-brand-surface rounded-xl border border-brand-dark border-dashed">
                <p class="text-gray-400 font-medium">{{ $t('routes.no_results') }}</p>
                <button @click="resetFilters" class="text-brand-neon text-sm font-bold mt-2 hover:underline">{{ $t('routes.clear_filters') }}</button>
            </div>

            <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="ruta in filteredRoutes" :key="ruta.id" class="bg-brand-surface rounded-xl overflow-hidden border border-brand-dark shadow-lg group hover:border-brand-neon transition duration-300 flex flex-col relative animate-fade-in">
                    
                    <div class="h-40 bg-gray-900 relative w-full">
                        <RouteMiniMap :route-id="ruta.id" :geo-json="ruta.geo_json" />
                        
                        <div class="absolute top-2 right-2 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide z-[400]"
                            :class="{
                                'bg-green-500/80 text-white': ruta.difficulty === 'easy',
                                'bg-yellow-500/80 text-black': ruta.difficulty === 'medium',
                                'bg-red-500/80 text-white': ruta.difficulty === 'hard',
                                'bg-purple-500/80 text-white': ruta.difficulty === 'expert',
                                'bg-gray-800 text-white border border-gray-600': ruta.difficulty === 'extreme'
                            }">
                            {{ difficultyLabel(ruta.difficulty) }}
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-white mb-1 truncate">{{ ruta.title }}</h3>
                            <p class="text-[10px] text-brand-neon uppercase tracking-wider mb-2 font-bold">
                                {{ $t('routes.by_user') }} {{ ruta.user ? ruta.user.name : 'Rider' }}
                            </p>
                            <p class="text-xs text-gray-500 line-clamp-2 mb-3">{{ ruta.description || $t('routes.no_description') }}</p>
                            
                            <div class="flex items-center gap-4 text-xs text-gray-300 font-mono bg-brand-black/30 p-2 rounded-lg flex-wrap">
                                <span class="flex items-center gap-1">🏁 {{ ruta.planned_distance_km }} km</span>
                                <span v-if="ruta.reviews && ruta.reviews.length" class="flex items-center gap-1 text-yellow-400 font-bold">
                                    ⭐ {{ (ruta.reviews.reduce((a, b) => a + b.rating, 0) / ruta.reviews.length).toFixed(1) }}
                                    <span class="text-gray-500 font-normal">({{ ruta.reviews.length }})</span>
                                </span>
                                <span v-else class="text-gray-600 text-[10px]">{{ $t('routes.no_reviews') }}</span>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4 pt-4 border-t border-brand-dark/50">
                            <Link v-if="ruta && ruta.id" :href="route('routes.show', ruta.id)" class="flex-1 text-center bg-brand-dark hover:bg-white hover:text-black text-white text-xs font-bold uppercase py-2 rounded transition">
                                {{ $t('routes.view_detail') }}
                            </Link>
                            
                            <button 
                                v-if="$page.props.auth.user && ruta && ruta.id && ruta.user_id !== $page.props.auth.user.id"
                                @click="router.post(route('routes.clone', ruta.id))"
                                class="px-3 flex items-center justify-center bg-brand-black border border-brand-neon/50 hover:bg-brand-neon hover:text-black text-brand-neon rounded transition" 
                                :title="$t('common.save')"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="showQuickActions"
            class="fixed inset-0 z-40 bg-black/35 backdrop-blur-[2px]"
            @click="showQuickActions = false"
        ></div>

    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import RouteMiniMap from '@/Components/RouteMiniMap.vue';
import { useRoutesStore } from '@/Stores/useRoutesStore';

const { t } = useI18n();

const props = defineProps({
    routes: Array,
    defaultMotorcycleId: {
        type: Number,
        default: null,
    },
});

const routesStore = useRoutesStore();

onMounted(() => {
    if (props.routes && props.routes.length > 0) {
        routesStore.setRoutes(props.routes);
    }
});

const showFilters = ref(false);
const showQuickActions = ref(false);

const goToFreeRide = () => {
    if (!props.defaultMotorcycleId) return;
    showQuickActions.value = false;
    router.visit(route('routes.free-ride', props.defaultMotorcycleId));
};

const newRouteActionLabel = computed(() => t('routes.new_route').replace(/^\+\s*/, ''));

const filters = ref({
    search: '',
    difficulty: 'all',
    kmMin: '',
    kmMax: '',
    ratingMin: 0,
    sortBy: 'created_at',
    sortDir: 'desc'
});

const activeFiltersCount = computed(() => {
    let count = 0;
    if (filters.value.search) count++;
    if (filters.value.difficulty !== 'all') count++;
    if (filters.value.kmMin !== '' || filters.value.kmMax !== '') count++;
    if (filters.value.ratingMin > 0) count++;
    return count;
});

const toggleSortDir = () => { filters.value.sortDir = filters.value.sortDir === 'asc' ? 'desc' : 'asc'; };

const resetFilters = () => {
    filters.value = { search: '', difficulty: 'all', kmMin: '', kmMax: '', ratingMin: 0, sortBy: 'created_at', sortDir: 'desc' };
};

const difficultyLabel = (d) => {
    const map = { easy: t('routes.diff_label_easy'), medium: t('routes.diff_label_medium'), hard: t('routes.diff_label_hard'), expert: t('routes.diff_label_expert'), extreme: t('routes.diff_label_extreme') };
    return map[d] || d;
};

const filteredRoutes = computed(() => {
    // FASE 3: Fallback Offline amb Pinia
    let sourceRoutes = props.routes?.length ? props.routes : routesStore.routes;
    let result = [...sourceRoutes];
    
    if (filters.value.search) {
        const q = filters.value.search.toLowerCase();
        result = result.filter(r => r.title.toLowerCase().includes(q) || (r.description && r.description.toLowerCase().includes(q)));
    }
    if (filters.value.difficulty !== 'all') result = result.filter(r => r.difficulty === filters.value.difficulty);
    if (filters.value.kmMin !== '') result = result.filter(r => parseFloat(r.planned_distance_km || 0) >= filters.value.kmMin);
    if (filters.value.kmMax !== '') result = result.filter(r => parseFloat(r.planned_distance_km || 0) <= filters.value.kmMax);
    if (filters.value.ratingMin > 0) {
        result = result.filter(r => {
            if (!r.reviews || r.reviews.length === 0) return false;
            return r.reviews.reduce((a, b) => a + b.rating, 0) / r.reviews.length >= filters.value.ratingMin;
        });
    }
    return result.sort((a, b) => {
        let fieldA, fieldB;
        if (filters.value.sortBy === 'avg_rating') {
            fieldA = a.reviews && a.reviews.length ? a.reviews.reduce((s, r) => s + r.rating, 0) / a.reviews.length : 0;
            fieldB = b.reviews && b.reviews.length ? b.reviews.reduce((s, r) => s + r.rating, 0) / b.reviews.length : 0;
        } else {
            fieldA = a[filters.value.sortBy];
            fieldB = b[filters.value.sortBy];
            if (filters.value.sortBy === 'planned_distance_km') { fieldA = parseFloat(fieldA || 0); fieldB = parseFloat(fieldB || 0); }
        }
        if (fieldA < fieldB) return filters.value.sortDir === 'asc' ? -1 : 1;
        if (fieldA > fieldB) return filters.value.sortDir === 'asc' ? 1 : -1;
        return 0;
    });
});
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
</style>

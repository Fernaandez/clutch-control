<template>
    <AppLayout title="Les meves Rutes">
        <div class="px-4 py-6 pb-24">
            
            <div class="flex items-center justify-between mb-8">
                <Link :href="route('routes.index')" class="text-gray-500 hover:text-white flex items-center gap-1 text-sm">
                    &larr; Tornar a Comunitat
                </Link>
            </div>

            <div class="mb-8">
                <h1 class="text-2xl font-black uppercase tracking-tighter text-white leading-none">
                    Les meves <span class="text-brand-neon">Rutes</span>
                </h1>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">
                    Gestiona les teves gravacions
                </p>
            </div>

            <div v-if="routes.length === 0" class="flex flex-col items-center justify-center py-12 text-center opacity-60">
                <p class="text-gray-400 font-medium">No tens cap ruta gravada.</p>
                <Link :href="route('routes.create')" class="mt-4 text-brand-neon text-sm font-bold border-b border-brand-neon">Crear-ne una ara</Link>
            </div>

            <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="ruta in routes" :key="ruta.id" class="bg-brand-surface rounded-xl overflow-hidden border border-brand-dark shadow-lg group hover:border-brand-neon transition duration-300 flex flex-col">
                    
                    <div class="h-40 bg-gray-900 relative w-full">
                        <RouteMiniMap :route-id="ruta.id" :geo-json="ruta.geo_json" />
                        
                        <div v-if="ruta.is_public" class="absolute top-2 left-2 bg-brand-neon/90 text-black px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide z-[400] shadow-md">🌍 Pública</div>
                        <div v-else class="absolute top-2 left-2 bg-gray-700/90 text-white px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide z-[400] shadow-md border border-gray-600">🔒 Privada</div>

                        <div class="absolute top-2 right-2 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide z-[400]"
                            :class="{
                                'bg-green-500/80 text-white': ruta.difficulty === 'easy',
                                'bg-yellow-500/80 text-black': ruta.difficulty === 'medium',
                                'bg-red-500/80 text-white': ruta.difficulty === 'hard'
                            }">
                            {{ ruta.difficulty === 'easy' ? 'Fàcil' : (ruta.difficulty === 'medium' ? 'Mitjana' : 'Difícil') }}
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-white mb-1 truncate">{{ ruta.title }}</h3>
                            <p class="text-xs text-gray-500 line-clamp-2 mb-3">{{ ruta.description || 'Sense descripció' }}</p>
                            
                            <div class="flex items-center gap-4 text-xs text-gray-300 font-mono bg-brand-black/30 p-2 rounded-lg">
                                <span class="flex items-center gap-1">
                                    🏁 {{ ruta.planned_distance_km }} km
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4 pt-4 border-t border-brand-dark/50">
                            <Link :href="route('routes.show', ruta.id)" class="flex-1 text-center bg-brand-dark hover:bg-white hover:text-black text-white text-xs font-bold uppercase py-2 rounded transition">
                                Veure Detall
                            </Link>
                            
                            <Link :href="route('routes.edit', ruta.id)" class="px-3 flex items-center justify-center bg-brand-dark border border-gray-600 hover:border-brand-neon hover:text-brand-neon text-gray-400 rounded transition" title="Editar">
                                ✏️
                            </Link>

                            <button 
                                @click="deleteRoute(ruta.id)"
                                class="px-3 flex items-center justify-center bg-brand-dark border border-red-900/50 text-red-700 hover:bg-red-500 hover:text-white hover:border-red-500 rounded transition" 
                                title="Eliminar"
                            >
                                🗑️
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import RouteMiniMap from '@/Components/RouteMiniMap.vue';

defineProps({
    routes: Array
});

const deleteRoute = (id) => {
    if (confirm("Estàs segur que vols esborrar aquesta ruta per sempre?")) {
        router.delete(route('routes.destroy', id));
    }
};
</script>
<template>
    <AppLayout title="Les meves Quedades">
        <div class="px-4 py-6 pb-24">
            
            <div class="flex items-center justify-between mb-6">
                <Link :href="route('events.index')" class="text-gray-500 hover:text-white flex items-center gap-1 text-sm">
                    &larr; Tornar a Públiques
                </Link>
            </div>

            <div class="mb-8">
                <h1 class="text-3xl font-black text-white uppercase tracking-tighter">La meva <span class="text-brand-neon">Agenda</span></h1>
                <p class="text-gray-400 text-sm">Esdeveniments que organitzes o hi participes.</p>
            </div>

            <div v-if="events.length === 0" class="flex flex-col items-center justify-center py-12 text-center opacity-60">
                <p class="text-gray-400 font-medium">No tens cap quedada programada.</p>
                <Link :href="route('events.create')" class="mt-4 text-brand-neon hover:underline">Crear-ne una ara</Link>
            </div>

            <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="event in events" :key="event.id" class="bg-brand-surface rounded-xl overflow-hidden border border-brand-dark shadow-lg flex flex-col">
                    
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
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    events: Array
});

const deleteEvent = (id) => {
    if (confirm("Segur que vols eliminar aquesta quedada?")) {
        router.delete(route('events.destroy', id));
    }
};
</script>
<template>
    <AppLayout title="Missatges">
        <div class="px-4 py-8 pb-24 max-w-2xl mx-auto">
            <h1 class="text-3xl font-black uppercase tracking-tighter text-white leading-none mb-8">XATS</h1>

            <div v-if="conversations.length === 0" class="bg-brand-surface border border-brand-dark rounded-2xl p-12 text-center shadow-lg border-dashed opacity-80">
                <h3 class="text-xl font-bold text-white mb-2 uppercase tracking-widest text-center">Sense Missatges</h3>
                <p class="text-sm text-gray-500 mb-6">Contacta amb el venedor d'alguna moto o apunta't a una quedada per iniciar converses.</p>
                <Link :href="route('sales.index')" class="text-brand-neon border border-brand-neon px-6 py-3 rounded-xl font-bold text-sm hover:bg-brand-neon hover:text-black transition uppercase tracking-widest inline-block">
                    Anar al Mercat
                </Link>
            </div>

            <div v-else class="space-y-3">
                <Link v-for="chat in conversations" :key="chat.id" :href="route('chats.show', chat.id)" 
                      class="block bg-brand-surface border border-brand-dark rounded-2xl p-4 hover:border-brand-neon/50 transition relative shadow-lg">
                    
                    <div class="flex items-center gap-4">
                        <!-- AVATAR -->
                        <div class="relative min-w-[3rem]">
                            <!-- Direct: foto de l'altre usuari -->
                            <template v-if="chat.type === 'direct' && chat.other_user">
                                <img v-if="chat.other_user.avatar" :src="avatarUrl(chat.other_user)" class="w-12 h-12 rounded-full object-cover border-2 border-brand-dark">
                                <div v-else class="w-12 h-12 rounded-full bg-brand-dark flex items-center justify-center text-brand-neon font-black text-xl border-2 border-brand-dark">
                                    {{ chat.other_user.name.charAt(0).toUpperCase() }}
                                </div>
                            </template>
                            <!-- Group -->
                            <template v-else>
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-brand-neon/40 to-brand-dark flex items-center justify-center text-white font-black text-xl border-2 border-brand-neon/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                                </div>
                            </template>

                            <!-- Unread badge -->
                            <div v-if="chat.unread_count > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold h-5 min-w-[20px] px-1 rounded-full flex items-center justify-center shadow-lg border border-brand-surface">
                                {{ chat.unread_count }}
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="font-bold text-white truncate pr-2 text-sm">
                                    {{ chatTitle(chat) }}
                                </h3>
                                <span class="text-[10px] text-gray-500 font-mono whitespace-nowrap">{{ timeAgo(chat.last_message_time) }}</span>
                            </div>
                            
                            <p class="text-xs text-gray-400 truncate" :class="{'text-white font-bold': chat.unread_count > 0}">{{ chat.last_message }}</p>

                            <!-- Etiqueta visual de context -->
                            <div class="mt-2 flex flex-wrap gap-1">
                                <span v-if="chat.type === 'group'" class="inline-flex items-center gap-1 bg-purple-500/10 border border-purple-500/20 text-purple-400 px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-widest">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                                    Grup · {{ chat.participants?.length || 0 }} membres
                                </span>
                                <span v-if="chat.motorcycle" class="inline-flex items-center gap-1 bg-brand-dark/50 border border-brand-neon/20 text-brand-neon px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-widest">
                                    🏍 {{ chat.motorcycle.brand }} {{ chat.motorcycle.model }}
                                </span>
                                <span v-if="chat.event" class="inline-flex items-center gap-1 bg-brand-dark/50 border border-brand-neon/20 text-brand-neon px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-widest">
                                    📅 {{ chat.event.title }}
                                </span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    conversations: Array
});

const page = usePage();
const storageUrl = page.props.storageUrl;

const chatTitle = (chat) => {
    if (chat.type === 'group') {
        return chat.name || chat.event?.title || 'Grup';
    }
    return chat.other_user?.name || 'Usuari';
};

const avatarUrl = (user) => {
    if (!user.avatar) return null;
    // Si és una URL completa (Google) retorna directament
    if (user.avatar.startsWith('http')) return user.avatar;
    return storageUrl + '/' + user.avatar;
};

const timeAgo = (dateStr) => {
    if (!dateStr) return '';
    const now = new Date();
    const d = new Date(dateStr);
    const diff = Math.floor((now - d) / 1000);
    if (diff < 60) return 'Ara';
    if (diff < 3600) return Math.floor(diff/60) + 'm';
    if (diff < 86400) return Math.floor(diff/3600) + 'h';
    if (diff < 604800) return Math.floor(diff/86400) + 'd';
    return d.toLocaleDateString('ca-ES', { day: 'numeric', month: 'short' });
};
</script>

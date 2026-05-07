<template>
    <AppLayout :title="pageTitle" :hide-bottom-nav="true">
        <div class="relative">
            
            <!-- CAPÇALERA DEL XAT -->
            <div class="fixed left-0 right-0 z-[40] bg-brand-surface border-b border-brand-dark px-5 py-3 flex items-center justify-between transition-all safe-horizontal" style="top: var(--app-header-total-height);">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <button type="button" @click="goBack" class="inline-flex items-center justify-center w-10 h-10 flex-shrink-0 rounded-full bg-brand-dark border border-brand-neon/50 text-brand-neon hover:bg-brand-neon hover:text-brand-black transition shadow-[0_0_10px_rgba(12,225,181,0.2)]" aria-label="Enrere">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                    </button>

                    <button type="button" @click="showInfoModal = true" class="flex items-center gap-3 min-w-0 flex-1 -ml-1 pl-1 py-1 rounded-lg hover:bg-brand-dark/40 active:bg-brand-dark/60 transition text-left" aria-label="Veure informació del xat">
                        <!-- Avatar: directe o grup -->
                        <template v-if="conversation.type === 'direct' && otherUser">
                            <img v-if="resolvedAvatar(otherUser)" :src="resolvedAvatar(otherUser)" class="w-9 h-9 rounded-full object-cover border-2 border-brand-dark flex-shrink-0">
                            <div v-else class="w-9 h-9 rounded-full bg-brand-dark flex items-center justify-center text-brand-neon font-black text-base border-2 border-brand-dark flex-shrink-0">
                                {{ otherUser.name.charAt(0).toUpperCase() }}
                            </div>
                        </template>
                        <template v-else>
                            <img v-if="resolvedChatPhoto" :src="resolvedChatPhoto" class="w-9 h-9 rounded-full object-cover border-2 border-brand-neon/30 flex-shrink-0">
                            <div v-else class="w-9 h-9 rounded-full bg-gradient-to-br from-brand-neon/30 to-brand-dark flex items-center justify-center text-white border-2 border-brand-neon/30 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                            </div>
                        </template>

                        <div class="min-w-0 flex-1">
                            <h2 class="text-white font-bold text-sm leading-none mb-0.5 truncate">{{ pageTitle }}</h2>
                            <p v-if="conversation.type === 'group'" class="text-[10px] text-gray-400 font-bold truncate max-w-[200px] leading-tight">
                                {{ participantsLabel }}
                            </p>
                            <p v-if="conversation.motorcycle" class="text-[9px] text-brand-neon uppercase font-bold tracking-widest truncate flex items-center gap-1.5">
                                <AppIcon name="moto" size="xs" />
                                {{ conversation.motorcycle.brand }} {{ conversation.motorcycle.model }}
                            </p>
                            <p v-if="conversation.event" class="text-[9px] text-brand-neon uppercase font-bold tracking-widest truncate flex items-center gap-1.5">
                                <AppIcon name="calendar" size="xs" />
                                {{ conversation.event.title }}
                            </p>
                        </div>

                        <AppIcon name="chevronRight" size="sm" class="text-gray-500 flex-shrink-0" />
                    </button>
                </div>
                <ReportButton
                    v-if="conversation.type === 'direct' && otherUser"
                    reportable-type="user"
                    :reportable-id="otherUser.id"
                    label="Denunciar"
                    :context-label="`Denunciar usuari: ${otherUser.name}`"
                    button-class="text-[10px] font-black uppercase tracking-widest text-red-400 border border-red-500/40 rounded-lg px-2 py-1 hover:bg-red-500 hover:text-white transition flex-shrink-0 ml-2"
                />
            </div>

            <!-- MODAL: INFORMACIÓ DEL XAT -->
            <Teleport to="body">
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-if="showInfoModal" class="fixed inset-0 z-[4000] bg-black/70 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4" @click.self="showInfoModal = false">
                        <Transition
                            enter-active-class="transition duration-300 ease-out"
                            enter-from-class="translate-y-full sm:translate-y-0 sm:opacity-0 sm:scale-95"
                            enter-to-class="translate-y-0 sm:opacity-100 sm:scale-100"
                            leave-active-class="transition duration-200 ease-in"
                            leave-from-class="translate-y-0 sm:opacity-100 sm:scale-100"
                            leave-to-class="translate-y-full sm:translate-y-0 sm:opacity-0 sm:scale-95"
                            appear
                        >
                            <div v-if="showInfoModal" class="w-full sm:max-w-md bg-brand-surface border border-brand-dark rounded-t-2xl sm:rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh]">
                                
                                <!-- Capçalera del modal -->
                                <div class="flex items-center justify-between gap-3 p-5 border-b border-brand-dark bg-brand-black/30">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">{{ conversation.type === 'group' ? 'Grup' : 'Conversa' }}</p>
                                        <h3 class="text-white font-bold text-lg truncate">{{ pageTitle }}</h3>
                                    </div>
                                    <button type="button" @click="showInfoModal = false" class="w-9 h-9 flex-shrink-0 rounded-full bg-brand-dark/50 hover:bg-brand-dark text-gray-400 hover:text-white transition flex items-center justify-center" aria-label="Tancar">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>

                                <!-- Contingut scrollable -->
                                <div class="flex-1 overflow-y-auto p-5 space-y-5">

                                    <!-- Avatar gran centrat -->
                                    <div class="flex flex-col items-center gap-2">
                                        <template v-if="conversation.type === 'direct' && otherUser">
                                            <img v-if="resolvedAvatar(otherUser)" :src="resolvedAvatar(otherUser)" class="w-24 h-24 rounded-full object-cover border-2 border-brand-neon/40 shadow-[0_0_20px_rgba(12,225,181,0.2)]">
                                            <div v-else class="w-24 h-24 rounded-full bg-brand-dark flex items-center justify-center text-brand-neon font-black text-4xl border-2 border-brand-neon/40 shadow-[0_0_20px_rgba(12,225,181,0.2)]">
                                                {{ otherUser.name.charAt(0).toUpperCase() }}
                                            </div>
                                        </template>
                                        <template v-else>
                                            <img v-if="resolvedChatPhoto" :src="resolvedChatPhoto" class="w-24 h-24 rounded-full object-cover border-2 border-brand-neon/40 shadow-[0_0_20px_rgba(12,225,181,0.2)]">
                                            <div v-else class="w-24 h-24 rounded-full bg-gradient-to-br from-brand-neon/30 to-brand-dark flex items-center justify-center text-white border-2 border-brand-neon/40 shadow-[0_0_20px_rgba(12,225,181,0.2)]">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" /></svg>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Info contextual: quedada o moto vinculada -->
                                    <div v-if="conversation.event || conversation.motorcycle" class="space-y-2">
                                        <Link 
                                            v-if="conversation.event" 
                                            :href="route('events.show', conversation.event.id)" 
                                            @click="showInfoModal = false"
                                            class="flex items-center justify-between gap-3 p-3 rounded-xl bg-brand-black/40 border border-brand-neon/20 hover:border-brand-neon hover:bg-brand-neon/10 transition group"
                                        >
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="w-10 h-10 rounded-lg bg-brand-neon/10 border border-brand-neon/30 flex items-center justify-center text-brand-neon flex-shrink-0">
                                                    <AppIcon name="calendar" size="md" />
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-[9px] text-gray-500 uppercase font-bold tracking-widest">Quedada</p>
                                                    <p class="text-white font-bold text-sm truncate">{{ conversation.event.title }}</p>
                                                </div>
                                            </div>
                                            <span class="text-brand-neon group-hover:translate-x-1 transition">&rarr;</span>
                                        </Link>

                                        <div v-if="conversation.motorcycle" class="flex items-center gap-3 p-3 rounded-xl bg-brand-black/40 border border-brand-dark">
                                            <div class="w-10 h-10 rounded-lg bg-brand-neon/10 border border-brand-neon/30 flex items-center justify-center text-brand-neon flex-shrink-0">
                                                <AppIcon name="moto" size="md" />
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-[9px] text-gray-500 uppercase font-bold tracking-widest">Moto</p>
                                                <p class="text-white font-bold text-sm truncate">{{ conversation.motorcycle.brand }} {{ conversation.motorcycle.model }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Llista de participants (només grups) -->
                                    <div v-if="conversation.type === 'group'">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="text-[11px] uppercase font-black tracking-widest text-brand-neon flex items-center gap-2">
                                                <AppIcon name="users" size="sm" />
                                                Membres
                                            </h4>
                                            <span class="text-[10px] text-gray-500 font-bold">{{ conversation.participants?.length || 0 }}</span>
                                        </div>
                                        <ul class="space-y-1.5">
                                            <li v-for="participant in conversation.participants" :key="participant.id" class="flex items-center gap-3 p-2 rounded-lg hover:bg-brand-dark/40 transition">
                                                <img v-if="resolvedAvatar(participant)" :src="resolvedAvatar(participant)" class="w-10 h-10 rounded-full object-cover border border-brand-dark flex-shrink-0">
                                                <div v-else class="w-10 h-10 rounded-full bg-brand-dark flex items-center justify-center text-brand-neon font-black text-base flex-shrink-0">
                                                    {{ participant.name.charAt(0).toUpperCase() }}
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-white font-bold text-sm truncate flex items-center gap-1.5">
                                                        {{ participant.name }}
                                                        <span v-if="participant.id === currentUser.id" class="text-[9px] text-brand-neon uppercase font-bold tracking-widest">(tu)</span>
                                                        <AppIcon v-if="conversation.event && conversation.event.user_id === participant.id" name="crown" size="xs" class="text-yellow-400" />
                                                    </p>
                                                </div>
                                                <ReportButton
                                                    v-if="participant.id !== currentUser.id"
                                                    reportable-type="user"
                                                    :reportable-id="participant.id"
                                                    label="Denunciar"
                                                    :context-label="`Denunciar usuari: ${participant.name}`"
                                                    button-class="text-[10px] font-bold uppercase tracking-widest text-red-400/70 hover:text-red-300 underline"
                                                />
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Direct chat: info bàsica de l'altre usuari -->
                                    <div v-else-if="otherUser" class="rounded-xl bg-brand-black/40 border border-brand-dark p-4 text-center">
                                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Conversa privada amb</p>
                                        <p class="text-white font-bold text-lg">{{ otherUser.name }}</p>
                                    </div>

                                </div>
                            </div>
                        </Transition>
                    </div>
                </Transition>
            </Teleport>

            <!-- ÀREA DE MISSATGES (amb margin top/bottom per les capçaleres fixes) -->
            <div class="pt-[90px] pb-3 px-6 space-y-1 h-[calc(100vh-var(--app-header-total-height)-var(--safe-bottom)-5.5rem)] overflow-y-auto flex flex-col safe-horizontal" ref="messagesContainer">
                <template v-for="(msg, idx) in localMessages" :key="msg.id">
                    <!-- Separador de data si el dia canvia -->
                    <div v-if="showDateSeparator(msg, localMessages[idx - 1])" class="flex items-center gap-3 py-3">
                        <div class="flex-1 h-px bg-brand-dark"></div>
                        <span class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">{{ formatDate(msg.created_at) }}</span>
                        <div class="flex-1 h-px bg-brand-dark"></div>
                    </div>

                    <div class="flex flex-col max-w-[78%] mb-4"
                         :class="isMine(msg) ? 'self-end items-end ml-auto mr-4' : 'self-start items-start ml-4'">
                        
                        <!-- Nom del participant en grups (si no sóc jo) -->
                        <div v-if="conversation.type === 'group' && !isMine(msg) && showSenderName(msg, localMessages[idx - 1])"
                             class="flex items-center gap-1.5 mb-1 px-1">
                            <img v-if="resolvedAvatar(msg.sender)" :src="resolvedAvatar(msg.sender)" class="w-4 h-4 rounded-full object-cover">
                            <div v-else class="w-4 h-4 rounded-full bg-brand-dark flex items-center justify-center text-brand-neon font-black text-[8px]">
                                {{ msg.sender?.name?.charAt(0) || '?' }}
                            </div>
                            <span class="text-[10px] text-gray-400 font-bold">{{ msg.sender?.name }}</span>
                        </div>

                        <div class="px-4 py-2.5 rounded-2xl relative shadow-md text-sm"
                             :class="isMine(msg) 
                                ? 'bg-brand-neon text-black rounded-br-none font-medium' 
                                : 'bg-brand-surface border border-brand-dark text-white rounded-bl-none'">
                            {{ msg.body }}
                        </div>
                        
                        <div class="flex items-center gap-1 mt-0.5 px-1">
                            <span class="text-[10px] text-gray-600">{{ formatTime(msg.created_at) }}</span>
                            <ReportButton
                                v-if="!isMine(msg)"
                                reportable-type="message"
                                :reportable-id="msg.id"
                                label="Denunciar"
                                :context-label="`Denunciar missatge de ${msg.sender?.name || 'usuari'}`"
                                button-class="text-[10px] text-red-500/80 hover:text-red-400 underline"
                            />
                            <!-- Check de lectura per directs -->
                            <template v-if="isMine(msg) && conversation.type === 'direct'">
                                <svg v-if="msg.read_at" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 text-brand-neon">
                                    <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 text-gray-600">
                                    <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" />
                                </svg>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- CAIXA D'ENVIAMENT -->
            <div class="fixed left-0 right-0 z-[40] bg-brand-surface border-t border-brand-dark px-8 pt-3 transition-all safe-horizontal" style="bottom: 0; padding-bottom: calc(var(--safe-bottom) + 0.5rem);">
                <form @submit.prevent="submit" class="flex gap-3 max-w-[720px] mx-auto w-full px-2">
                    <input type="text" v-model="messageText" :placeholder="$t('chats.write_message')" 
                           class="flex-1 bg-brand-dark border-transparent focus:border-brand-neon focus:ring-brand-neon text-white rounded-2xl px-5 text-sm transition placeholder-gray-500"
                           autocomplete="off" @keydown.enter.prevent="submit">
                    
                    <button type="submit" :disabled="isSending || !messageText.trim()" 
                            class="bg-brand-neon text-black p-3.5 rounded-full hover:bg-white transition disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                    </button>
                </form>
                <p v-if="sendError" class="text-red-400 text-xs mt-2 px-1">{{ sendError }}</p>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, nextTick, onUnmounted, computed, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import AppIcon from '@/Components/AppIcon.vue';
import ReportButton from '@/Components/ReportButton.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { smartBack } from '@/Composables/navigationStack.js';

const { t, locale } = useI18n();

const goBack = () => smartBack(route('chats.index'));

const props = defineProps({
    conversation: Object,
    otherUser: Object  // null if group
});

const page = usePage();
const currentUser = page.props.auth.user;
const storageUrl = page.props.storageUrl;

const localMessages = ref([...props.conversation.messages]);
const messagesContainer = ref(null);
const messageText = ref('');
const isSending = ref(false);
const sendError = ref('');
const pollingTimer = ref(null);
const showInfoModal = ref(false);

const participantsLabel = computed(() => {
    const list = props.conversation.participants || [];
    return list.map(p => p.id === currentUser.id ? 'Tu' : p.name).join(', ');
});

const resolvedChatPhoto = computed(() => {
    const photo = props.conversation.photo;
    if (!photo) return null;
    if (photo.startsWith('http')) return photo;
    return `${storageUrl}/${photo}`;
});

const pageTitle = computed(() => {
    if (props.conversation.type === 'group') {
        return props.conversation.name || props.conversation.event?.title || t('chats.group_chat');
    }
    return props.otherUser?.name || t('chats.chat');
});

const isMine = (msg) => msg.sender_id === currentUser.id;

const resolvedAvatar = (user) => {
    if (!user?.avatar) return null;
    if (user.avatar.startsWith('http')) return user.avatar;
    return storageUrl + '/' + user.avatar;
};

// Show sender name only when sender changes (group chats)
const showSenderName = (msg, prevMsg) => {
    if (!prevMsg) return true;
    return prevMsg.sender_id !== msg.sender_id;
};

// Show date separator when day changes
const showDateSeparator = (msg, prevMsg) => {
    if (!prevMsg) return true;
    const d1 = new Date(msg.created_at).toDateString();
    const d2 = new Date(prevMsg.created_at).toDateString();
    return d1 !== d2;
};

const scrollToBottom = (smooth = false) => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTo({ top: messagesContainer.value.scrollHeight, behavior: smooth ? 'smooth' : 'auto' });
    }
};

const mergeIncomingMessages = (incomingMessages = []) => {
    if (!Array.isArray(incomingMessages) || incomingMessages.length === 0) return false;

    const ids = new Set(localMessages.value.map(m => String(m.id)));
    let hasNew = false;

    incomingMessages.forEach((message) => {
        if (!ids.has(String(message.id))) {
            localMessages.value.push(message);
            hasNew = true;
        }
    });

    return hasNew;
};

const fetchNewMessages = async () => {
    const numericIds = localMessages.value
        .map(m => Number(m.id))
        .filter(id => Number.isFinite(id));
    const lastId = numericIds.length ? Math.max(...numericIds) : 0;

    try {
        const response = await window.axios.get(route('chats.messages', props.conversation.id), {
            params: { since_id: lastId },
            headers: { Accept: 'application/json' },
        });

        const hasNew = mergeIncomingMessages(response?.data?.messages ?? []);
        if (hasNew) nextTick(() => scrollToBottom(true));
    } catch (error) {
        // Silenciem errors de polling per no molestar UX del xat
    }
};

const startPolling = () => {
    stopPolling();
    pollingTimer.value = setInterval(fetchNewMessages, 3000);
};

const stopPolling = () => {
    if (pollingTimer.value) {
        clearInterval(pollingTimer.value);
        pollingTimer.value = null;
    }
};

onMounted(() => {
    // On first paint, wait a tick so fixed header/footer heights are applied
    nextTick(() => scrollToBottom());
    // Extra pass to guarantee final position on slower devices/webviews
    setTimeout(() => scrollToBottom(), 120);
    startPolling();

    if (window.Echo) {
        window.Echo.private(`chat.${props.conversation.id}`)
            .listen('MessageSent', (e) => {
                const hasNew = mergeIncomingMessages([e.message]);
                if (hasNew) {
                    nextTick(() => scrollToBottom(true));
                }
            });
    }
});



watch(() => props.conversation.messages, (newMessages) => {
    // Merge new messages or completely replace
    localMessages.value = [...newMessages];
    nextTick(() => scrollToBottom());
}, { deep: true });

onUnmounted(() => {
    stopPolling();
    if (window.Echo) window.Echo.leave(`chat.${props.conversation.id}`);
});

const submit = () => {
    const trimmedBody = messageText.value.trim();
    if (!trimmedBody || isSending.value) return;

    sendError.value = '';
    isSending.value = true;

    const bodyBackup = trimmedBody;

    const tempMsg = {
        id: 'opt_' + Date.now(),
        sender_id: currentUser.id,
        sender: currentUser,
        body: bodyBackup,
        read_at: null,
        created_at: new Date().toISOString()
    };

    localMessages.value.push(tempMsg);
    nextTick(() => scrollToBottom(true));

    messageText.value = '';

    window.axios.post(route('chats.message', props.conversation.id), {
        body: bodyBackup
    }, {
        headers: {
            'Accept': 'application/json',
        }
    }).then((response) => {
        const serverMessage = response?.data?.message;
        localMessages.value = localMessages.value.filter(m => m.id !== tempMsg.id);
        if (serverMessage) {
            localMessages.value.push(serverMessage);
        }
        nextTick(() => scrollToBottom(true));
    }).catch(() => {
            localMessages.value = localMessages.value.filter(m => m.id !== tempMsg.id);
            messageText.value = bodyBackup;
            sendError.value = 'No s\'ha pogut enviar el missatge. Torna-ho a provar.';
    }).finally(() => {
        isSending.value = false;
    });
};

const formatTime = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleTimeString('ca-ES', { hour: '2-digit', minute: '2-digit' });
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(today.getDate() - 1);
    if (d.toDateString() === today.toDateString()) return t('chats.today');
    if (d.toDateString() === yesterday.toDateString()) return t('chats.yesterday');
    return d.toLocaleDateString(locale.value, { day: 'numeric', month: 'long' });
};
</script>

<style scoped>
.overflow-y-auto::-webkit-scrollbar { display: none; }
.overflow-y-auto { -ms-overflow-style: none; scrollbar-width: none; }
.pt-safe-top { padding-top: env(safe-area-inset-top, 0px); }
.pb-safe-bottom { padding-bottom: env(safe-area-inset-bottom, 20px); }
</style>

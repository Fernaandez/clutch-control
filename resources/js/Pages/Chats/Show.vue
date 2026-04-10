<template>
    <AppLayout :title="pageTitle" :hide-bottom-nav="true">
        <div class="relative">
            
            <!-- CAPÇALERA DEL XAT -->
            <div class="fixed left-0 right-0 z-[40] bg-brand-surface border-t border-b border-brand-dark px-5 py-3 flex items-center justify-between transition-all safe-horizontal" style="top: var(--app-header-total-height);">
                <div class="flex items-center gap-3 min-w-0">
                    <button type="button" onclick="window.history.length > 1 ? window.history.back() : window.location.href='/chats'" class="w-10 h-10 rounded-full bg-brand-neon flex items-center justify-center text-black hover:bg-white transition flex-shrink-0 shadow-[0_0_15px_rgba(12,225,181,0.3)]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                    </button>

                    <!-- Avatar: directe o grup -->
                    <template v-if="conversation.type === 'direct' && otherUser">
                        <img v-if="resolvedAvatar(otherUser)" :src="resolvedAvatar(otherUser)" class="w-9 h-9 rounded-full object-cover border-2 border-brand-dark flex-shrink-0">
                        <div v-else class="w-9 h-9 rounded-full bg-brand-dark flex items-center justify-center text-brand-neon font-black text-base border-2 border-brand-dark flex-shrink-0">
                            {{ otherUser.name.charAt(0).toUpperCase() }}
                        </div>
                    </template>
                    <template v-else>
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-brand-neon/30 to-brand-dark flex items-center justify-center text-white border-2 border-brand-neon/30 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                        </div>
                    </template>

                    <div class="min-w-0">
                        <h2 class="text-white font-bold text-sm leading-none mb-0.5 truncate">{{ pageTitle }}</h2>
                        <p v-if="conversation.type === 'group'" class="text-[10px] text-gray-400 font-bold truncate max-w-[200px] leading-tight">
                            {{ conversation.participants?.map(p => p.name).join(', ') }}
                        </p>
                        <p v-if="conversation.motorcycle" class="text-[9px] text-brand-neon uppercase font-bold tracking-widest truncate">
                            🏍 {{ conversation.motorcycle.brand }} {{ conversation.motorcycle.model }}
                        </p>
                        <p v-if="conversation.event" class="text-[9px] text-brand-neon uppercase font-bold tracking-widest truncate">
                            📅 {{ conversation.event.title }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- ÀREA DE MISSATGES (amb margin top/bottom per les capçaleres fixes) -->
            <div class="pt-[90px] pb-3 px-6 space-y-1 h-[calc(100vh-var(--app-header-total-height)-var(--safe-bottom)-5.5rem)] overflow-y-auto flex flex-col safe-horizontal" ref="messagesContainer">
                <template v-for="(msg, idx) in localMessages" :key="msg.id">
                    <!-- Separador de data si el dia canvia -->
                    <div v-if="showDateSeparator(msg, localMessages[idx - 1])" class="flex items-center gap-3 py-3">
                        <div class="flex-1 h-px bg-brand-dark"></div>
                        <span class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">{{ formatDate(msg.created_at) }}</span>
                        <div class="flex-1 h-px bg-brand-dark"></div>
                    </div>

                    <div class="flex flex-col max-w-[80%] mb-4"
                         :class="isMine(msg) ? 'self-end items-end ml-auto mr-1' : 'self-start items-start ml-1'">
                        
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
            <div class="fixed left-0 right-0 z-[40] bg-brand-surface border-t border-brand-dark px-5 pt-3 transition-all safe-horizontal" style="bottom: 0; padding-bottom: calc(var(--safe-bottom) + 0.5rem);">
                <form @submit.prevent="submit" class="flex gap-2 max-w-[720px] mx-auto w-full">
                    <input type="text" v-model="messageText" :placeholder="$t('chats.write_message')" 
                           class="flex-1 bg-brand-dark border-transparent focus:border-brand-neon focus:ring-brand-neon text-white rounded-xl px-4 text-sm transition placeholder-gray-500"
                           autocomplete="off" @keydown.enter.prevent="submit">
                    
                    <button type="submit" :disabled="isSending || !messageText.trim()" 
                            class="bg-brand-neon text-black p-3 rounded-xl hover:bg-white transition disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0">
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
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n();

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

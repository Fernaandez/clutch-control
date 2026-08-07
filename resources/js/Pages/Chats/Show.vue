<template>
    <AppLayout :title="pageTitle" :hide-bottom-nav="true">
        <div class="relative">

            <!-- Capçalera -->
            <div
                class="fixed left-0 right-0 z-[40] bg-brand-black/95 backdrop-blur-md border-b border-white/[0.06] px-4 py-3 flex items-center gap-3 safe-horizontal"
                style="top: var(--app-header-total-height);"
            >
                <button type="button" @click="goBack" class="cc-icon-btn flex-shrink-0" :aria-label="$t('common.back')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </button>

                <button
                    type="button"
                    @click="showInfoModal = true"
                    class="flex items-center gap-3 min-w-0 flex-1 text-left"
                >
                    <div class="w-9 h-9 rounded-full overflow-hidden bg-white/[0.06] border border-white/[0.08] flex items-center justify-center flex-shrink-0">
                        <img v-if="headerAvatar" :src="headerAvatar" alt="" class="w-full h-full object-cover">
                        <span v-else class="text-sm font-medium text-gray-400">{{ headerLetter }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[15px] font-medium text-white truncate">{{ pageTitle }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ headerSub }}</p>
                    </div>
                </button>

                <ReportButton
                    v-if="conversation.type === 'direct' && otherUser"
                    reportable-type="user"
                    :reportable-id="otherUser.id"
                    :label="$t('chats.report')"
                    :context-label="`${$t('chats.report')}: ${otherUser.name}`"
                    button-class="cc-btn-text border-red-500/25 text-red-400 hover:text-red-300 hover:border-red-500/50 flex-shrink-0"
                />
            </div>

            <!-- Modal info -->
            <Teleport to="body">
                <div
                    v-if="showInfoModal"
                    class="fixed inset-0 z-[4000] bg-black/70 flex items-end sm:items-center justify-center"
                    @click.self="showInfoModal = false"
                >
                    <div class="w-full sm:max-w-md bg-brand-black border border-white/[0.08] rounded-t-2xl sm:rounded-2xl max-h-[85vh] overflow-y-auto">
                        <div class="flex items-center justify-between gap-3 px-6 py-5 border-b border-white/[0.06]">
                            <div class="min-w-0">
                                <p class="cc-section-label mb-1">{{ conversation.type === 'group' ? $t('chats.group') : $t('chats.chat') }}</p>
                                <h3 class="text-lg font-medium text-white truncate">{{ pageTitle }}</h3>
                            </div>
                            <button type="button" @click="showInfoModal = false" class="cc-icon-btn" :aria-label="$t('common.close')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="px-6 py-6 space-y-6">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-20 h-20 rounded-full overflow-hidden bg-white/[0.06] border border-white/[0.08] flex items-center justify-center">
                                    <img v-if="headerAvatar" :src="headerAvatar" alt="" class="w-full h-full object-cover">
                                    <span v-else class="text-2xl font-light text-gray-400">{{ headerLetter }}</span>
                                </div>
                            </div>

                            <div v-if="conversation.event || conversation.motorcycle" class="space-y-2">
                                <Link
                                    v-if="conversation.event"
                                    :href="route('events.show', conversation.event.id)"
                                    @click="showInfoModal = false"
                                    class="flex items-center justify-between gap-3 py-3 border-b border-white/[0.06]"
                                >
                                    <div class="min-w-0">
                                        <p class="text-xs text-gray-500">{{ $t('nav.events') }}</p>
                                        <p class="text-sm text-white truncate">{{ conversation.event.title }}</p>
                                    </div>
                                    <span class="cc-btn-text flex-shrink-0">{{ $t('common.view') }}</span>
                                </Link>
                                <div v-if="conversation.motorcycle" class="py-3 border-b border-white/[0.06]">
                                    <p class="text-xs text-gray-500">{{ $t('nav.moto') }}</p>
                                    <p class="text-sm text-white">{{ conversation.motorcycle.brand }} {{ conversation.motorcycle.model }}</p>
                                </div>
                            </div>

                            <div v-if="conversation.type === 'group'">
                                <div class="flex items-baseline justify-between mb-2">
                                    <p class="cc-section-label mb-0">{{ $t('chats.members') }}</p>
                                    <span class="text-xs text-gray-600 tabular-nums">{{ conversation.participants?.length || 0 }}</span>
                                </div>
                                <ul class="divide-y divide-white/[0.06]">
                                    <li
                                        v-for="participant in conversation.participants"
                                        :key="participant.id"
                                        class="flex items-center gap-3 py-3"
                                    >
                                        <div class="w-9 h-9 rounded-full overflow-hidden bg-white/[0.06] border border-white/[0.08] flex items-center justify-center flex-shrink-0">
                                            <img v-if="resolvedAvatar(participant)" :src="resolvedAvatar(participant)" alt="" class="w-full h-full object-cover">
                                            <span v-else class="text-xs text-gray-400">{{ participant.name?.charAt(0) }}</span>
                                        </div>
                                        <p class="min-w-0 flex-1 text-sm text-gray-200 truncate">
                                            {{ participant.name }}
                                            <span v-if="participant.id === currentUser.id" class="text-gray-500"> · {{ $t('chats.you') }}</span>
                                        </p>
                                        <ReportButton
                                            v-if="participant.id !== currentUser.id"
                                            reportable-type="user"
                                            :reportable-id="participant.id"
                                            :label="$t('chats.report')"
                                            :context-label="`${$t('chats.report')}: ${participant.name}`"
                                            button-class="text-xs text-red-400 hover:text-red-300"
                                        />
                                    </li>
                                </ul>
                            </div>

                            <p v-else-if="otherUser" class="text-sm text-gray-500 text-center">
                                {{ otherUser.name }}
                            </p>
                        </div>
                    </div>
                </div>
            </Teleport>

            <!-- Missatges -->
            <div
                class="pt-[84px] pb-3 px-5 space-y-1 h-[calc(100vh-var(--app-header-total-height)-var(--safe-bottom)-5.5rem)] overflow-y-auto flex flex-col safe-horizontal"
                ref="messagesContainer"
            >
                <template v-for="(msg, idx) in localMessages" :key="msg.id">
                    <div v-if="showDateSeparator(msg, localMessages[idx - 1])" class="flex items-center gap-3 py-4">
                        <div class="flex-1 h-px bg-white/[0.06]"></div>
                        <span class="text-[11px] text-gray-600">{{ formatDate(msg.created_at) }}</span>
                        <div class="flex-1 h-px bg-white/[0.06]"></div>
                    </div>

                    <div
                        class="flex flex-col max-w-[78%] mb-3"
                        :class="isMine(msg) ? 'self-end items-end ml-auto' : 'self-start items-start'"
                    >
                        <div
                            v-if="conversation.type === 'group' && !isMine(msg) && showSenderName(msg, localMessages[idx - 1])"
                            class="mb-1 px-1 text-[11px] text-gray-500"
                        >
                            {{ msg.sender?.name }}
                        </div>

                        <div
                            class="px-3.5 py-2.5 rounded-2xl text-sm leading-relaxed"
                            :class="isMine(msg)
                                ? 'bg-white text-brand-black rounded-br-md'
                                : 'bg-white/[0.06] border border-white/[0.08] text-gray-100 rounded-bl-md'"
                        >
                            {{ msg.body }}
                        </div>

                        <div class="flex items-center gap-2 mt-1 px-1">
                            <span class="text-[10px] text-gray-600 tabular-nums">{{ formatTime(msg.created_at) }}</span>
                            <ReportButton
                                v-if="!isMine(msg)"
                                reportable-type="message"
                                :reportable-id="msg.id"
                                :label="$t('chats.report')"
                                :context-label="`${$t('chats.report')}: ${msg.sender?.name || ''}`"
                                button-class="text-[10px] text-red-500/70 hover:text-red-400"
                            />
                            <span
                                v-if="isMine(msg) && conversation.type === 'direct'"
                                class="text-[10px]"
                                :class="msg.read_at ? 'text-gray-400' : 'text-gray-700'"
                            >
                                {{ msg.read_at ? '✓' : '·' }}
                            </span>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Composer -->
            <div
                class="fixed left-0 right-0 z-[40] bg-brand-black/95 backdrop-blur-md border-t border-white/[0.06] px-4 pt-3 safe-horizontal"
                style="bottom: 0; padding-bottom: calc(var(--safe-bottom) + 0.5rem);"
            >
                <form @submit.prevent="submit" class="flex gap-2 max-w-xl mx-auto w-full">
                    <input
                        type="text"
                        v-model="messageText"
                        :placeholder="$t('chats.type_message')"
                        class="flex-1 rounded-xl bg-white/[0.04] border-white/[0.08] text-white text-sm focus:border-white/30 focus:ring-0 placeholder-gray-600"
                        autocomplete="off"
                        @keydown.enter.prevent="submit"
                    >
                    <button
                        type="submit"
                        :disabled="isSending || !messageText.trim()"
                        class="cc-btn-primary px-4 py-2.5 disabled:opacity-40"
                    >
                        {{ $t('chats.send') }}
                    </button>
                </form>
                <p v-if="sendError" class="text-red-400 text-xs mt-2">{{ sendError }}</p>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, nextTick, onUnmounted, computed, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import ReportButton from '@/Components/ReportButton.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { smartBack } from '@/Composables/navigationStack.js';

const { t, locale } = useI18n();
const goBack = () => smartBack(route('chats.index'));

const props = defineProps({
    conversation: Object,
    otherUser: Object,
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

const pageTitle = computed(() => {
    if (props.conversation.type === 'group') {
        return props.conversation.name || props.conversation.event?.title || t('chats.group_fallback');
    }
    return props.otherUser?.name || t('chats.chat');
});

const headerSub = computed(() => {
    if (props.conversation.type === 'group') {
        return t('chats.group_members', { n: props.conversation.participants?.length || 0 });
    }
    if (props.conversation.motorcycle) {
        return `${props.conversation.motorcycle.brand} ${props.conversation.motorcycle.model}`.trim();
    }
    return t('chats.direct');
});

const resolvedAvatar = (user) => {
    if (!user?.avatar) return null;
    if (user.avatar.startsWith('http')) return user.avatar;
    return `${storageUrl}/${user.avatar}`;
};

const headerAvatar = computed(() => {
    if (props.conversation.type === 'direct' && props.otherUser) {
        return resolvedAvatar(props.otherUser);
    }
    const photo = props.conversation.photo;
    if (!photo) return null;
    if (photo.startsWith('http')) return photo;
    return `${storageUrl}/${photo}`;
});

const headerLetter = computed(() => (pageTitle.value || '?').charAt(0).toUpperCase());

const isMine = (msg) => msg.sender_id === currentUser.id;

const showSenderName = (msg, prevMsg) => {
    if (!prevMsg) return true;
    return prevMsg.sender_id !== msg.sender_id;
};

const showDateSeparator = (msg, prevMsg) => {
    if (!prevMsg) return true;
    return new Date(msg.created_at).toDateString() !== new Date(prevMsg.created_at).toDateString();
};

const scrollToBottom = (smooth = false) => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTo({
            top: messagesContainer.value.scrollHeight,
            behavior: smooth ? 'smooth' : 'auto',
        });
    }
};

const mergeIncomingMessages = (incomingMessages = []) => {
    if (!Array.isArray(incomingMessages) || !incomingMessages.length) return false;
    const ids = new Set(localMessages.value.map((m) => String(m.id)));
    let hasNew = false;
    incomingMessages.forEach((message) => {
        if (!ids.has(String(message.id))) {
            localMessages.value.push(message);
            hasNew = true;
        }
    });
    return hasNew;
};

let pollAbort = null;
let pollInFlight = false;

const fetchNewMessages = async () => {
    if (pollInFlight) return;
    if (typeof document !== 'undefined' && document.hidden) return;

    const numericIds = localMessages.value.map((m) => Number(m.id)).filter((id) => Number.isFinite(id));
    const lastId = numericIds.length ? Math.max(...numericIds) : 0;

    pollAbort?.abort?.();
    pollAbort = new AbortController();
    pollInFlight = true;

    try {
        const response = await window.axios.get(route('chats.messages', props.conversation.id), {
            params: { since_id: lastId },
            headers: { Accept: 'application/json' },
            signal: pollAbort.signal,
        });
        const hasNew = mergeIncomingMessages(response?.data?.messages ?? []);
        if (hasNew) nextTick(() => scrollToBottom(true));
    } catch {
        /* silent */
    } finally {
        pollInFlight = false;
    }
};

const startPolling = () => {
    stopPolling();
    pollingTimer.value = setInterval(fetchNewMessages, 5000);
};

const stopPolling = () => {
    if (pollingTimer.value) {
        clearInterval(pollingTimer.value);
        pollingTimer.value = null;
    }
    pollAbort?.abort?.();
    pollAbort = null;
};

const handleVisibilityChange = () => {
    if (document.hidden) stopPolling();
    else {
        fetchNewMessages();
        startPolling();
    }
};

onMounted(() => {
    nextTick(() => scrollToBottom());
    setTimeout(() => scrollToBottom(), 120);
    startPolling();
    if (typeof document !== 'undefined') {
        document.addEventListener('visibilitychange', handleVisibilityChange);
    }
    if (window.Echo) {
        window.Echo.private(`chat.${props.conversation.id}`).listen('MessageSent', (e) => {
            const hasNew = mergeIncomingMessages([e.message]);
            if (hasNew) nextTick(() => scrollToBottom(true));
        });
    }
});

watch(() => props.conversation.messages, (newMessages) => {
    localMessages.value = [...newMessages];
    nextTick(() => scrollToBottom());
}, { deep: true });

onUnmounted(() => {
    stopPolling();
    if (typeof document !== 'undefined') {
        document.removeEventListener('visibilitychange', handleVisibilityChange);
    }
    if (window.Echo) window.Echo.leave(`chat.${props.conversation.id}`);
});

const submit = () => {
    const trimmedBody = messageText.value.trim();
    if (!trimmedBody || isSending.value) return;

    sendError.value = '';
    isSending.value = true;
    const bodyBackup = trimmedBody;

    const tempMsg = {
        id: `opt_${Date.now()}`,
        sender_id: currentUser.id,
        sender: currentUser,
        body: bodyBackup,
        read_at: null,
        created_at: new Date().toISOString(),
    };

    localMessages.value.push(tempMsg);
    nextTick(() => scrollToBottom(true));
    messageText.value = '';

    window.axios.post(route('chats.message', props.conversation.id), { body: bodyBackup }, {
        headers: { Accept: 'application/json' },
    }).then((response) => {
        const serverMessage = response?.data?.message;
        localMessages.value = localMessages.value.filter((m) => m.id !== tempMsg.id);
        if (serverMessage) localMessages.value.push(serverMessage);
        nextTick(() => scrollToBottom(true));
    }).catch(() => {
        localMessages.value = localMessages.value.filter((m) => m.id !== tempMsg.id);
        messageText.value = bodyBackup;
        sendError.value = t('chats.send_error');
    }).finally(() => {
        isSending.value = false;
    });
};

const formatTime = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleTimeString(locale.value, { hour: '2-digit', minute: '2-digit' });
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
</style>

<template>
    <AppLayout :title="$t('chats.title')">
        <div class="px-6 pt-10 pb-28 max-w-xl mx-auto cc-fade-in">

            <p class="text-sm text-gray-500">{{ $t('chats.title') }}</p>
            <p
                class="mt-3 text-[64px] leading-[0.9] font-light tracking-[-0.04em] tabular-nums"
                :class="unreadTotal > 0 ? 'text-white' : 'text-white'"
            >
                {{ unreadTotal > 0 ? unreadTotal : conversations.length }}
            </p>
            <p class="mt-2 text-sm text-gray-500">
                {{ unreadTotal > 0 ? $t('chats.unread_label') : $t('chats.conversations_label') }}
            </p>

            <div class="mt-10 flex items-center gap-5 border-b border-white/[0.06]">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    type="button"
                    @click="activeTab = tab.id"
                    class="relative -mb-px pb-3 text-[13px] font-medium transition-colors"
                    :class="activeTab === tab.id ? 'text-white' : 'text-gray-500 hover:text-gray-300'"
                >
                    {{ tab.label }}
                    <span v-if="tab.count" class="ml-1 text-gray-600 tabular-nums">{{ tab.count }}</span>
                    <span v-if="activeTab === tab.id" class="absolute inset-x-0 -bottom-px h-px bg-white"></span>
                </button>
            </div>

            <input
                v-if="filtered.length > 6"
                v-model="search"
                type="search"
                :placeholder="$t('chats.search_placeholder')"
                class="w-full mt-4 rounded-xl bg-white/[0.04] border-white/[0.08] text-white text-sm focus:border-white/30 focus:ring-0"
            >

            <div v-if="filtered.length" class="divide-y divide-white/[0.06]">
                <div
                    v-for="chat in filtered"
                    :key="chat.id"
                    class="flex items-center gap-3 py-4"
                >
                    <div class="relative flex-shrink-0">
                        <div class="w-11 h-11 rounded-full overflow-hidden bg-white/[0.06] border border-white/[0.08] flex items-center justify-center">
                            <img v-if="avatarSrc(chat)" :src="avatarSrc(chat)" alt="" class="w-full h-full object-cover">
                            <span v-else class="text-sm font-medium text-gray-400">{{ avatarLetter(chat) }}</span>
                        </div>
                        <span
                            v-if="chat.unread_count > 0"
                            class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full bg-red-500 text-white text-[10px] font-medium tabular-nums flex items-center justify-center"
                        >
                            {{ chat.unread_count }}
                        </span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p
                            class="text-[15px] truncate"
                            :class="chat.unread_count > 0 ? 'font-semibold text-white' : 'font-medium text-gray-100'"
                        >
                            {{ chatTitle(chat) }}
                        </p>
                        <p
                            class="mt-1 text-xs truncate"
                            :class="chat.unread_count > 0 ? 'text-gray-300' : 'text-gray-500'"
                        >
                            {{ chat.last_message }}
                        </p>
                        <p class="mt-1 text-xs text-gray-600 truncate">{{ metaLine(chat) }}</p>
                    </div>

                    <div class="text-right flex-shrink-0">
                        <p class="text-xs text-gray-600 tabular-nums">{{ timeAgo(chat.last_message_time) }}</p>
                        <Link :href="route('chats.show', chat.id)" class="cc-btn-text mt-2 inline-flex">
                            {{ $t('common.view') }}
                        </Link>
                    </div>
                </div>
            </div>

            <div v-else class="py-16 text-center">
                <p class="text-base font-semibold text-gray-300">{{ $t('chats.no_messages') }}</p>
                <Link :href="route('sales.index')" class="cc-btn-text mt-4 inline-flex">
                    {{ $t('chats.go_to_market') }}
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    conversations: { type: Array, default: () => [] },
    unreadTotal: { type: Number, default: 0 },
});

const { t, locale } = useI18n();
const page = usePage();
const storageUrl = page.props.storageUrl;

const activeTab = ref('all');
const search = ref('');

const tabs = computed(() => {
    const all = props.conversations;
    const direct = all.filter((c) => c.type === 'direct');
    const groups = all.filter((c) => c.type === 'group');
    return [
        { id: 'all', label: t('chats.tab_all'), count: all.length },
        { id: 'direct', label: t('chats.tab_direct'), count: direct.length },
        { id: 'group', label: t('chats.tab_group'), count: groups.length },
    ];
});

const filtered = computed(() => {
    let list = props.conversations;
    if (activeTab.value === 'direct') list = list.filter((c) => c.type === 'direct');
    if (activeTab.value === 'group') list = list.filter((c) => c.type === 'group');

    const q = search.value.trim().toLowerCase();
    if (!q) return list;

    return list.filter((c) =>
        chatTitle(c).toLowerCase().includes(q) ||
        (c.last_message || '').toLowerCase().includes(q) ||
        (c.event?.title || '').toLowerCase().includes(q) ||
        `${c.motorcycle?.brand || ''} ${c.motorcycle?.model || ''}`.toLowerCase().includes(q),
    );
});

const chatTitle = (chat) => {
    if (chat.type === 'group') return chat.name || chat.event?.title || t('chats.group_fallback');
    return chat.other_user?.name || t('chats.user_fallback');
};

const metaLine = (chat) => {
    const parts = [];
    if (chat.type === 'group') {
        parts.push(t('chats.group_members', { n: chat.participants?.length || 0 }));
    }
    if (chat.motorcycle) {
        parts.push(`${chat.motorcycle.brand} ${chat.motorcycle.model}`.trim());
    }
    if (chat.event) parts.push(chat.event.title);
    return parts.join(' · ');
};

const resolveUrl = (path) => {
    if (!path) return null;
    if (String(path).startsWith('http')) return path;
    return `${storageUrl}/${path}`;
};

const avatarSrc = (chat) => {
    if (chat.type === 'direct' && chat.other_user) return resolveUrl(chat.other_user.avatar);
    return resolveUrl(chat.photo);
};

const avatarLetter = (chat) => {
    const title = chatTitle(chat);
    return (title || '?').charAt(0).toUpperCase();
};

const timeAgo = (dateStr) => {
    if (!dateStr) return '';
    const now = new Date();
    const d = new Date(dateStr);
    const diff = Math.floor((now - d) / 1000);
    if (diff < 60) return t('chats.recently');
    if (diff < 3600) return `${Math.floor(diff / 60)}m`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h`;
    if (diff < 604800) return `${Math.floor(diff / 86400)}d`;
    return d.toLocaleDateString(locale.value, { day: 'numeric', month: 'short' });
};
</script>

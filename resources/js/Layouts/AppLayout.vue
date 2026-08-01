<template>
    <div
        class="min-h-screen bg-brand-black text-gray-200 font-sans relative safe-horizontal overflow-x-hidden max-w-[100vw]"
        :style="hideBottomNav ? 'padding-bottom: calc(var(--safe-bottom) + 0.75rem);' : 'padding-bottom: calc(var(--app-bottom-nav-total-height) + 1rem);'"
    >

        <nav
            class="fixed top-0 left-0 right-0 w-full bg-brand-black/80 backdrop-blur-xl border-b border-white/[0.06] z-[3000] px-4 flex items-center justify-between safe-top safe-horizontal"
            style="height: var(--app-header-total-height);"
        >
            <div class="min-w-[1.75rem]">
                <Link
                    v-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'"
                    :href="route('admin.dashboard')"
                    class="cc-chip-accent"
                >
                    ADMIN
                </Link>
            </div>

            <!-- Centrat absolutament per garantir simetria perfecta -->
            <div class="absolute left-1/2 -translate-x-1/2 flex items-center gap-2 whitespace-nowrap">
                <img :src="appLogo" alt="Clutch Control Logo" class="h-5 w-auto flex-shrink-0">
                <span class="text-[15px] font-semibold tracking-tight text-gray-400">Clutch<span class="text-white">Control</span></span>
            </div>

            <div class="relative min-w-[1.75rem] flex justify-end">
                <button
                    @click="isMenuOpen = !isMenuOpen"
                    class="p-2 -mr-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/[0.06] transition"
                    :aria-label="$t('nav.settings')"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <div v-show="isMenuOpen" class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm" @click="isMenuOpen = false"></div>

                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 -translate-y-1 scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 translate-y-0 scale-100"
                    leave-to-class="opacity-0 -translate-y-1 scale-95"
                >
                    <div
                        v-show="isMenuOpen"
                        class="absolute top-[125%] right-0 w-60 cc-card z-50 overflow-hidden origin-top-right"
                        style="box-shadow: var(--shadow-lg);"
                    >
                        <div v-if="$page.props.auth.user" class="px-4 py-3 border-b border-white/[0.06]">
                            <p class="cc-section-label">{{ $t('nav.logged_in_as') }}</p>
                            <p class="text-sm font-semibold text-white truncate mt-0.5">{{ $page.props.auth.user.name }}</p>
                        </div>

                        <div class="p-1.5">
                            <Link
                                :href="route('profile.edit')"
                                @click="isMenuOpen = false"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:bg-white/[0.06] hover:text-white transition"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                {{ $t('nav.settings') }}
                            </Link>
                        </div>

                        <div class="p-1.5 border-t border-white/[0.06]">
                            <Link :href="route('terms.service')" @click="isMenuOpen = false" class="block px-3 py-2 rounded-lg text-xs text-gray-500 hover:text-gray-300 hover:bg-white/[0.04] transition">{{ $t('legal.terms_title') }}</Link>
                            <Link :href="route('privacy.policy')" @click="isMenuOpen = false" class="block px-3 py-2 rounded-lg text-xs text-gray-500 hover:text-gray-300 hover:bg-white/[0.04] transition">{{ $t('legal.privacy_title') }}</Link>
                        </div>

                        <div v-if="$page.props.auth.user" class="p-1.5 border-t border-white/[0.06]">
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="w-full text-left px-3 py-2.5 rounded-lg text-sm font-medium text-red-400 hover:bg-red-500/10 transition flex items-center gap-3"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" /></svg>
                                {{ $t('nav.logout') }}
                            </Link>
                        </div>
                    </div>
                </Transition>
            </div>
        </nav>

        <main class="relative flex-1 min-w-0 max-w-full" style="padding-top: var(--app-header-total-height);">
            <div class="w-full h-full">
                <slot />
            </div>
        </main>

        <nav
            v-if="!hideBottomNav"
            class="fixed left-0 right-0 w-full bg-brand-black/90 backdrop-blur-xl border-t border-white/[0.06] flex justify-around items-stretch z-[3000] safe-horizontal"
            :style="bottomNavStyle"
        >
            <Link
                v-for="item in navItems"
                :key="item.name"
                :href="item.href"
                prefetch
                class="group relative flex flex-col items-center justify-center gap-1 w-16 pt-1 cursor-pointer transition-colors"
                :class="item.active ? 'text-brand-neon' : 'text-gray-500 hover:text-gray-300'"
            >
                <!-- Indicador de pestanya activa -->
                <span
                    class="absolute top-0 h-0.5 w-8 rounded-full bg-brand-neon transition-all duration-300"
                    :class="item.active ? 'opacity-100 scale-100' : 'opacity-0 scale-50'"
                ></span>

                <span v-if="item.badge" class="absolute top-2 right-3.5 w-2 h-2 rounded-full bg-red-500 ring-2 ring-brand-black"></span>

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="w-6 h-6 transition-transform duration-200" :class="item.active ? 'scale-105' : ''">
                    <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" />
                </svg>
                <span class="text-[10px] font-medium tracking-tight">{{ item.label }}</span>
            </Link>
        </nav>

        <!-- Onboarding Tutorial (first launch) -->
        <OnboardingTutorial />

    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { recordNavigationVisit } from '@/Composables/navigationStack.js';
import appLogo from '@/../images/logo.svg';
import { useTheme } from '@/Composables/useTheme.js';
import { useI18n } from 'vue-i18n';
import { Capacitor } from '@capacitor/core';
import { PushNotifications } from '@capacitor/push-notifications';
import axios from 'axios';
import OnboardingTutorial from '@/Components/OnboardingTutorial.vue';

const props = defineProps({
    currentMotoId: Number,
    hideBottomNav: {
        type: Boolean,
        default: false,
    },
});

const isMenuOpen = ref(false);
const isIos = Capacitor.getPlatform() === 'ios';

const { initTheme } = useTheme();
const { t } = useI18n();
const page = usePage();

const ICONS = {
    moto: 'M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z',
    routes: 'M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z',
    events: 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
    sales: 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
    chats: 'M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155',
};

const navItems = computed(() => {
    // El layout persisteix entre pàgines: depenem de page.url perquè
    // l'indicador de pestanya activa es recalculi a cada navegació.
    void page.url;

    return [
    {
        name: 'moto',
        label: t('nav.moto'),
        icon: ICONS.moto,
        href: props.currentMotoId ? route('dashboard', props.currentMotoId) : route('dashboard'),
        active: route().current('dashboard'),
        badge: page.props.has_pending_maintenance,
    },
    {
        name: 'routes',
        label: t('nav.routes'),
        icon: ICONS.routes,
        href: route('routes.index'),
        active: route().current('routes.*'),
        badge: false,
    },
    {
        name: 'events',
        label: t('nav.events'),
        icon: ICONS.events,
        href: route('events.index'),
        active: route().current('events.*'),
        badge: false,
    },
    {
        name: 'sales',
        label: t('nav.sales'),
        icon: ICONS.sales,
        href: route('sales.index'),
        active: route().current('sales.*'),
        badge: false,
    },
    {
        name: 'chats',
        label: t('nav.chats'),
        icon: ICONS.chats,
        href: route('chats.index'),
        active: route().current('chats.*'),
        badge: page.props.unread_chats_count > 0,
    },
    ];
});

const bottomNavStyle = computed(() => ({
    paddingBottom: isIos ? 'var(--safe-bottom)' : 'calc(0.75rem + var(--safe-bottom))',
    height: 'var(--app-bottom-nav-total-height)',
    bottom: isIos ? '-20px' : '-2px',
}));

watch(
    () => page.url,
    () => {
        recordNavigationVisit();
    },
    { immediate: true },
);

const registerPushNotifications = async () => {
    if (!Capacitor.isNativePlatform()) return;
    if (!page.props.auth.user) return;

    let permStatus = await PushNotifications.checkPermissions();

    if (permStatus.receive === 'prompt') {
        permStatus = await PushNotifications.requestPermissions();
    }

    if (permStatus.receive !== 'granted') {
        return;
    }

    await PushNotifications.register();

    PushNotifications.addListener('registration', (token) => {
        axios.post(route('profile.device-token'), { token: token.value }).catch(console.error);
    });
};

onMounted(() => {
    initTheme();
    registerPushNotifications();
});

onUnmounted(() => {
    if (Capacitor.isNativePlatform()) {
        PushNotifications.removeAllListeners();
    }
});
</script>

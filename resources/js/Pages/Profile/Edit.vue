<template>
    <AppLayout>
        
        <div class="px-4 py-8 pb-24 space-y-6">
            
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-white">{{ $t('profile.title') }}</h1>
                
                <Link 
                    :href="route('logout')" 
                    method="post" 
                    as="button" 
                    class="bg-brand-dark border border-brand-neon/30 text-brand-neon px-4 py-2 rounded-lg font-bold text-xs hover:bg-brand-neon hover:text-brand-black transition uppercase tracking-widest shadow-neon"
                >
                    {{ $t('profile.logout') }}
                </Link>
            </div>

            <!-- AVATAR -->
            <div class="p-4 sm:p-8 bg-brand-surface border border-brand-dark shadow-lg rounded-xl">
                <h2 class="text-lg font-bold text-white uppercase tracking-wider flex items-center gap-2 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-brand-neon"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Foto de Perfil
                </h2>
                <div class="flex items-center gap-6">
                    <!-- Preview actual -->
                    <div class="relative group cursor-pointer" @click="$refs.avatarInput.click()">
                        <img v-if="currentAvatar" :src="currentAvatar" class="w-20 h-20 rounded-full object-cover border-4 border-brand-dark group-hover:border-brand-neon transition">
                        <div v-else class="w-20 h-20 rounded-full bg-brand-dark flex items-center justify-center text-brand-neon font-black text-3xl border-4 border-brand-dark group-hover:border-brand-neon transition">
                            {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-white"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" /></svg>
                        </div>
                    </div>
                    <div>
                        <input ref="avatarInput" type="file" accept="image/*" class="hidden" @change="uploadAvatar">
                        <button @click="$refs.avatarInput.click()" class="bg-brand-dark hover:bg-brand-neon hover:text-black text-brand-neon font-bold text-xs uppercase tracking-widest px-4 py-2 rounded-xl border border-brand-neon/30 transition">
                            Canviar foto
                        </button>
                        <p class="text-xs text-gray-500 mt-2">JPG, PNG o WebP. Màx 2MB.</p>
                        <p v-if="avatarError" class="text-xs text-red-400 mt-1">{{ avatarError }}</p>
                        <p v-if="avatarSuccess" class="text-xs text-brand-neon mt-1">✓ Foto actualitzada!</p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-brand-surface border border-brand-dark shadow-lg rounded-xl">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <div v-if="!$page.props.auth.user.google_id" class="p-4 sm:p-8 bg-brand-surface border border-brand-dark shadow-lg rounded-xl">
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <!-- Configuració Global (Aparença i Idioma) -->
            <div class="p-4 sm:p-8 bg-brand-surface border border-brand-dark shadow-lg rounded-xl">
                <!-- Idioma -->
                <section class="mb-8 border-b border-brand-dark pb-8">
                    <header>
                        <h2 class="text-lg font-bold text-white uppercase tracking-wider flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-brand-neon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                            </svg>
                            Idioma / Language
                        </h2>
                        <p class="mt-1 text-xs text-gray-400">Selecciona el teu idioma preferit.</p>
                    </header>
                    <div class="mt-6 flex flex-wrap gap-4">
                        <button @click="$i18n.locale = 'ca'; changeLocalLang('ca')" 
                                :class="$i18n.locale === 'ca' ? 'bg-brand-neon text-black font-black' : 'bg-brand-dark text-white hover:bg-brand-dark/80'" 
                                class="px-6 py-3 rounded-xl border border-brand-neon border-opacity-30 uppercase tracking-widest text-xs transition">
                            Català
                        </button>
                        <button @click="$i18n.locale = 'es'; changeLocalLang('es')" 
                                :class="$i18n.locale === 'es' ? 'bg-brand-neon text-black font-black' : 'bg-brand-dark text-white hover:bg-brand-dark/80'" 
                                class="px-6 py-3 rounded-xl border border-brand-neon border-opacity-30 uppercase tracking-widest text-xs transition">
                            Español
                        </button>
                        <button @click="$i18n.locale = 'en'; changeLocalLang('en')" 
                                :class="$i18n.locale === 'en' ? 'bg-brand-neon text-black font-black' : 'bg-brand-dark text-white hover:bg-brand-dark/80'" 
                                class="px-6 py-3 rounded-xl border border-brand-neon border-opacity-30 uppercase tracking-widest text-xs transition">
                            English
                        </button>
                    </div>
                </section>

                <!-- Aparença Tema -->
                <section>
                    <header>
                        <h2 class="text-lg font-bold text-white uppercase tracking-wider flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-brand-neon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                            </svg>
                            {{ $t('profile.appearance') }}
                        </h2>
                        <p class="mt-1 text-xs text-gray-400">{{ $t('profile.appearance_hint') }}</p>
                    </header>
                    <div class="mt-6 flex items-center gap-6">
                        <button
                            @click="setTheme('dark')"
                            :class="isDark ? 'ring-2 ring-brand-neon bg-brand-black text-brand-neon' : 'bg-brand-black/50 text-gray-400 hover:text-white'"
                            class="flex flex-col items-center gap-2 px-6 py-4 rounded-xl border border-brand-dark transition-all"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                            </svg>
                            <span class="text-xs font-bold uppercase tracking-widest">{{ $t('profile.dark_mode') }}</span>
                        </button>
                        <button
                            @click="setTheme('light')"
                            :class="!isDark ? 'ring-2 ring-brand-neon text-brand-neon' : 'bg-brand-black/50 text-gray-400 hover:text-white'"
                            class="flex flex-col items-center gap-2 px-6 py-4 rounded-xl border border-brand-dark transition-all bg-brand-surface"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                            </svg>
                            <span class="text-xs font-bold uppercase tracking-widest">{{ $t('profile.light_mode') }}</span>
                        </button>
                    </div>
                </section>
            </div>

            <div class="p-4 sm:p-8 bg-brand-surface border border-red-900/30 shadow-lg rounded-xl">
                <DeleteUserForm class="max-w-xl" />
            </div>

            <div class="mt-8 text-center flex justify-center items-center gap-4 text-[10px] pb-10">
                <Link :href="route('terms.service')" class="text-gray-500 hover:text-white transition uppercase tracking-widest font-bold">{{ $t('legal.terms_title') }}</Link>
                <span class="text-gray-600">&bull;</span>
                <Link :href="route('privacy.policy')" class="text-gray-500 hover:text-white transition uppercase tracking-widest font-bold">{{ $t('legal.privacy_title') }}</Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useTheme } from '@/Composables/useTheme.js';
import { useI18n } from 'vue-i18n';
import axios from 'axios';

const { isDark, setTheme } = useTheme();
const { locale } = useI18n();
const page = usePage();

defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
});

const changeLocalLang = (lang) => {
    localStorage.setItem('locale', lang);
};

// AVATAR LOGIC
const avatarError = ref('');
const avatarSuccess = ref(false);
const localAvatar = ref(null); // URL local optimista

const currentAvatar = computed(() => {
    if (localAvatar.value) return localAvatar.value;
    const avatar = page.props.auth.user.avatar;
    if (!avatar) return null;
    if (avatar.startsWith('http')) return avatar;
    return page.props.storageUrl + '/' + avatar;
});

const uploadAvatar = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    avatarError.value = '';
    avatarSuccess.value = false;

    if (file.size > 2 * 1024 * 1024) {
        avatarError.value = "La imatge no pot pesar més de 2MB.";
        return;
    }

    // Preview optimístic
    localAvatar.value = URL.createObjectURL(file);

    const formData = new FormData();
    formData.append('avatar', file);
    formData.append('_method', 'POST');

    try {
        await axios.post(route('profile.avatar'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        avatarSuccess.value = true;
        setTimeout(() => { avatarSuccess.value = false; }, 3000);
    } catch (e) {
        avatarError.value = "Error pujant la imatge. Torna-ho a provar.";
        localAvatar.value = null;
    }
};
</script>

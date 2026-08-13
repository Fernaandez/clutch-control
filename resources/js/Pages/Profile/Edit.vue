<template>
    <AppLayout>
        <div class="px-6 pt-10 pb-28 max-w-xl mx-auto">

            <div class="flex items-start justify-between gap-4">
                <h1 class="cc-title">{{ $t('profile.title') }}</h1>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="cc-btn-text flex-shrink-0"
                >
                    {{ $t('profile.logout') }}
                </Link>
            </div>

            <!-- AVATAR -->
            <section class="mt-12">
                <p class="cc-section-label">Foto de Perfil</p>
                <div class="mt-4 flex items-center gap-5">
                    <div class="relative group cursor-pointer flex-shrink-0" @click="$refs.avatarInput.click()">
                        <img
                            v-if="currentAvatar"
                            :src="currentAvatar"
                            class="w-20 h-20 rounded-full object-cover border border-white/[0.1] group-hover:border-white/30 transition"
                            alt=""
                        >
                        <div
                            v-else
                            class="w-20 h-20 rounded-full bg-white/[0.06] border border-white/[0.1] flex items-center justify-center text-white text-2xl font-light group-hover:border-white/30 transition"
                        >
                            {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <input ref="avatarInput" type="file" accept="image/*" class="hidden" @change="uploadAvatar">
                        <button type="button" @click="$refs.avatarInput.click()" class="cc-btn-text">
                            Canviar foto
                        </button>
                        <p class="text-xs text-gray-500 mt-2">JPG, PNG o WebP. Màx 2MB.</p>
                        <p v-if="avatarError" class="text-xs text-red-400 mt-1">{{ avatarError }}</p>
                        <p v-if="avatarSuccess" class="text-xs text-gray-300 mt-1">Foto actualitzada</p>
                    </div>
                </div>
            </section>

            <section class="mt-12">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                />
            </section>

            <!-- Canviar la contrasenya demana la contrasenya actual: només té
                 sentit si el compte en té. Abans s'amagava a tothom que tingués
                 google_id, i això deixava sense opció qui s'havia registrat amb
                 correu i després havia entrat amb Google. -->
            <section v-if="$page.props.auth.user.has_password" class="mt-12 pt-12 border-t border-white/[0.06]">
                <UpdatePasswordForm />
            </section>

            <!-- Idioma -->
            <section class="mt-12 pt-12 border-t border-white/[0.06]">
                <p class="cc-section-label">Idioma / Language</p>
                <p class="mt-1 text-sm text-gray-500">Selecciona el teu idioma preferit.</p>
                <div class="mt-4 flex items-center gap-1 p-1 rounded-xl bg-white/[0.04] border border-white/[0.06]">
                    <button
                        type="button"
                        @click="$i18n.locale = 'ca'; changeLocalLang('ca')"
                        class="flex-1 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
                        :class="$i18n.locale === 'ca' ? 'bg-white/[0.1] text-white' : 'text-gray-500 hover:text-gray-300'"
                    >
                        Català
                    </button>
                    <button
                        type="button"
                        @click="$i18n.locale = 'es'; changeLocalLang('es')"
                        class="flex-1 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
                        :class="$i18n.locale === 'es' ? 'bg-white/[0.1] text-white' : 'text-gray-500 hover:text-gray-300'"
                    >
                        Español
                    </button>
                    <button
                        type="button"
                        @click="$i18n.locale = 'en'; changeLocalLang('en')"
                        class="flex-1 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
                        :class="$i18n.locale === 'en' ? 'bg-white/[0.1] text-white' : 'text-gray-500 hover:text-gray-300'"
                    >
                        English
                    </button>
                </div>
            </section>

            <!-- Aparença -->
            <section class="mt-12">
                <p class="cc-section-label">{{ $t('profile.appearance') }}</p>
                <p class="mt-1 text-sm text-gray-500">{{ $t('profile.appearance_hint') }}</p>
                <div class="mt-4 flex items-center gap-1 p-1 rounded-xl bg-white/[0.04] border border-white/[0.06]">
                    <button
                        type="button"
                        @click="setTheme('dark')"
                        class="flex-1 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
                        :class="isDark ? 'bg-white/[0.1] text-white' : 'text-gray-500 hover:text-gray-300'"
                    >
                        {{ $t('profile.dark_mode') }}
                    </button>
                    <button
                        type="button"
                        @click="setTheme('light')"
                        class="flex-1 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
                        :class="!isDark ? 'bg-white/[0.1] text-white' : 'text-gray-500 hover:text-gray-300'"
                    >
                        {{ $t('profile.light_mode') }}
                    </button>
                </div>
            </section>

            <section class="mt-12 pt-12 border-t border-white/[0.06]">
                <DeleteUserForm />
            </section>

            <div class="mt-12 flex justify-center items-center gap-3 pb-4">
                <Link :href="route('terms.service')" class="cc-btn-text">
                    {{ $t('legal.terms_title') }}
                </Link>
                <Link :href="route('privacy.policy')" class="cc-btn-text">
                    {{ $t('legal.privacy_title') }}
                </Link>
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
import axios from 'axios';

const { isDark, setTheme } = useTheme();
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
const localAvatar = ref(null);

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

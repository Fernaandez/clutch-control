<template>
    <AppLayout>
        
        <div class="px-4 py-8 pb-24 space-y-6">
            
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-white">Ajustos de Compte</h1>
                
                <Link 
                    :href="route('logout')" 
                    method="post" 
                    as="button" 
                    class="bg-brand-dark border border-brand-neon/30 text-brand-neon px-4 py-2 rounded-lg font-bold text-xs hover:bg-brand-neon hover:text-brand-black transition uppercase tracking-widest shadow-neon"
                >
                    Tancar Sessió
                </Link>
            </div>

            <div class="p-4 sm:p-8 bg-brand-surface border border-brand-dark shadow-lg rounded-xl">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <div class="p-4 sm:p-8 bg-brand-surface border border-brand-dark shadow-lg rounded-xl">
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <!-- Aparença / Theme Toggle -->
            <div class="p-4 sm:p-8 bg-brand-surface border border-brand-dark shadow-lg rounded-xl">
                <section>
                    <header>
                        <h2 class="text-lg font-bold text-white uppercase tracking-wider flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-brand-neon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                            </svg>
                            Aparença
                        </h2>
                        <p class="mt-1 text-xs text-gray-400">Canvia entre el mode fosc i el mode clar.</p>
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
                            <span class="text-xs font-bold uppercase tracking-widest">Mode Fosc</span>
                        </button>
                        <button
                            @click="setTheme('light')"
                            :class="!isDark ? 'ring-2 ring-brand-neon text-brand-neon' : 'bg-brand-black/50 text-gray-400 hover:text-white'"
                            class="flex flex-col items-center gap-2 px-6 py-4 rounded-xl border border-brand-dark transition-all bg-brand-surface"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                            </svg>
                            <span class="text-xs font-bold uppercase tracking-widest">Mode Clar</span>
                        </button>
                    </div>
                </section>
            </div>

            <div class="p-4 sm:p-8 bg-brand-surface border border-red-900/30 shadow-lg rounded-xl">
                <DeleteUserForm class="max-w-xl" />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useTheme } from '@/Composables/useTheme.js';

const { isDark, setTheme } = useTheme();

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

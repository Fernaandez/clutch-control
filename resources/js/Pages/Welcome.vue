<template>
    <Head title="Benvingut" />

    <div class="h-[100dvh] w-full bg-brand-black text-white selection:bg-brand-neon selection:text-brand-black overflow-hidden flex flex-col relative">
        
        <!-- Ambient Glowing Background -->
        <div class="absolute inset-0 w-full h-full opacity-20 pointer-events-none flex items-center justify-center">
            <div class="absolute top-10 -left-20 w-96 h-96 bg-brand-neon rounded-full blur-[150px]"></div>
            <div class="absolute bottom-10 -right-20 w-96 h-96 bg-purple-600 rounded-full blur-[150px]"></div>
        </div>

        <main class="relative z-10 flex flex-col flex-1 items-center justify-center p-6 h-full">
            
            <!-- Centered Large Logo -->
            <div class="mb-14 p-8 border border-brand-neon/30 rounded-[3rem] bg-brand-black/50 backdrop-blur-sm shadow-[0_0_50px_rgba(12,225,181,0.15)] flex items-center justify-center">
                <img :src="appLogo" alt="Clutch Control Logo" class="h-32 md:h-40 w-auto drop-shadow-[0_0_15px_rgba(12,225,181,0.8)]">
            </div>

            <!-- Big Action Buttons -->
            <div v-if="canLogin" class="flex flex-col gap-5 w-full max-w-sm">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="w-full text-center bg-brand-neon text-brand-black px-6 py-5 mx-auto rounded-2xl font-black uppercase tracking-widest hover:bg-white active:scale-95 transition-all shadow-[0_0_25px_rgba(12,225,181,0.4)] text-lg">
                    Entrar al Garatge ➜
                </Link>

                <template v-else>
                    <Link :href="route('login')" class="w-full text-center bg-brand-neon text-brand-black px-6 py-5 mx-auto rounded-2xl font-black uppercase tracking-widest hover:bg-white active:scale-95 transition-all shadow-[0_0_25px_rgba(12,225,181,0.4)] text-lg">
                        Iniciar Sessió
                    </Link>
                    
                    <Link v-if="canRegister" :href="route('register')" class="w-full text-center border border-gray-700 bg-gray-900/50 backdrop-blur-sm text-gray-300 px-6 py-5 mx-auto rounded-2xl font-bold uppercase tracking-widest hover:border-brand-neon hover:text-white active:scale-95 transition-all text-lg">
                        Registrar-se
                    </Link>
                </template>
            </div>

        </main>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import appLogo from '@/../images/logo.svg';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

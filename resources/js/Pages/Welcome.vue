<template>
    <Head title="Benvingut" />

    <div class="min-h-screen bg-brand-black text-white selection:bg-brand-neon selection:text-brand-black overflow-hidden relative">
        
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full max-w-4xl opacity-20 pointer-events-none">
            <div class="absolute top-20 left-20 w-72 h-72 bg-brand-neon rounded-full blur-[128px]"></div>
            <div class="absolute bottom-20 right-20 w-96 h-96 bg-purple-600 rounded-full blur-[128px]"></div>
        </div>

        <nav class="relative z-10 flex justify-between items-center p-6 max-w-7xl mx-auto">
            <div class="flex items-center gap-2">
                <img :src="appLogo" alt="Clutch Control Logo" class="h-8 w-auto">
            </div>

            <div v-if="canLogin" class="flex gap-4">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-sm font-bold text-white hover:text-brand-neon transition">
                    Garatge
                </Link>
                <template v-else>
                    <Link :href="route('login')" class="text-sm font-bold text-gray-300 hover:text-white transition">
                        Entrar
                    </Link>
                    <Link v-if="canRegister" :href="route('register')" class="text-sm font-bold text-brand-neon hover:text-white transition">
                        Registrar-se
                    </Link>
                </template>
            </div>
        </nav>

        <main class="relative z-10 flex flex-col items-center justify-center text-center mt-20 px-4 min-h-[60vh]">
            
            <div class="mb-8 p-6 border border-brand-neon/30 rounded-full bg-brand-black/50 backdrop-blur-sm shadow-[0_0_40px_rgba(12,225,181,0.2)]">
                <img :src="appLogo" alt="Clutch Control Main Logo" class="h-24 w-auto drop-shadow-[0_0_10px_rgba(12,225,181,0.8)]">
            </div>

            <h1 class="text-5xl md:text-7xl font-black uppercase tracking-tighter text-white mb-6 drop-shadow-xl">
                Controla la teva <br />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-neon to-blue-500">Passió</span>
            </h1>

            <p class="text-lg md:text-xl text-gray-400 max-w-2xl mb-10 leading-relaxed">
                La plataforma definitiva per gestionar el <span class="text-white font-bold">manteniment</span>, 
                les <span class="text-white font-bold">reparacions</span> i les <span class="text-white font-bold">millores</span> 
                de la teva moto. Tot en un sol lloc.
            </p>

            <div v-if="canLogin" class="flex flex-col sm:flex-row gap-4 w-full max-w-md justify-center">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="bg-brand-neon text-brand-black px-8 py-4 rounded-xl font-black uppercase tracking-wider hover:bg-white hover:scale-105 transition shadow-[0_0_20px_rgba(12,225,181,0.4)]">
                    Anar al Garatge ➜
                </Link>

                <template v-else>
                    <Link :href="route('register')" class="bg-brand-neon text-brand-black px-8 py-4 rounded-xl font-black uppercase tracking-wider hover:bg-white hover:scale-105 transition shadow-[0_0_20px_rgba(12,225,181,0.4)]">
                        Començar Ara
                    </Link>
                    <Link :href="route('login')" class="border border-brand-dark bg-brand-surface text-white px-8 py-4 rounded-xl font-bold uppercase tracking-wider hover:border-brand-neon hover:text-brand-neon transition">
                        Ja tinc compte
                    </Link>
                </template>
            </div>

        </main>

        <footer class="absolute bottom-6 w-full text-center text-gray-600 text-xs uppercase tracking-widest">
            &copy; 2024 Clutch Control. Gas a fons.
        </footer>

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

<template>
    <section>
        <header>
            <h2 class="text-lg font-bold text-white uppercase tracking-wider flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-brand-neon"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                Informació del Perfil
            </h2>
            <p class="mt-1 text-xs text-gray-400">
                Actualitza el teu nom, correu electrònic i número de telèfon (necessari per a vendre motos).
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
            
            <div>
                <label class="block font-bold text-xs uppercase text-gray-400 mb-1 ml-1" for="name">
                    Nom <span class="text-red-400">*</span>
                </label>
                <input 
                    id="name" 
                    type="text" 
                    :class="form.errors.name ? 'w-full rounded-lg bg-brand-black border-red-500 ring-1 ring-red-500 text-white focus:border-red-400 focus:ring-0 transition' : 'w-full rounded-lg bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-0 transition'"
                    v-model="form.name" 
                    required 
                    autocomplete="name" 
                />
                <div v-if="form.errors.name" class="text-red-400 text-xs mt-1 ml-1">⚠ {{ form.errors.name }}</div>
            </div>

            <div>
                <label class="block font-bold text-xs uppercase text-gray-400 mb-1 ml-1" for="email">
                    Correu Electrònic <span class="text-red-400">*</span>
                </label>
                <input 
                    id="email" 
                    type="email" 
                    :class="form.errors.email ? 'w-full rounded-lg bg-brand-black border-red-500 ring-1 ring-red-500 text-white focus:border-red-400 focus:ring-0 transition' : 'w-full rounded-lg bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-0 transition'"
                    v-model="form.email" 
                    required 
                    autocomplete="username" 
                />
                <p class="text-[10px] text-gray-500 mt-1 ml-1">Format: <span class="text-brand-neon font-mono">nom@exemple.com</span></p>
                <div v-if="form.errors.email" class="text-red-400 text-xs mt-1 ml-1">⚠ {{ form.errors.email }}</div>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null" class="mt-4 p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-xl">
                <p class="text-xs text-yellow-500 font-bold mb-2 uppercase tracking-wide">
                    ⚠️ La teva adreça de correu no està verificada.
                </p>
                <Link
                    :href="route('verification.send')"
                    method="post"
                    as="button"
                    class="text-xs font-bold text-white uppercase tracking-widest hover:text-brand-neon underline underline-offset-4 transition"
                >
                    Clica aquí per reenviar l'enllaç de verificació.
                </Link>
                <div v-show="status === 'verification-link-sent'" class="mt-3 text-xs font-bold text-brand-black bg-brand-neon px-3 py-2 rounded">
                    S'ha enviat un nou enllaç a la teva adreça.
                </div>
            </div>

            <div>
                <label class="block font-bold text-xs uppercase text-gray-400 mb-1 ml-1" for="phone_number">
                    Telèfon <span class="text-gray-600 font-normal">(opcional)</span>
                </label>
                <input 
                    id="phone_number" 
                    type="tel" 
                    placeholder="Ex: 612 345 678"
                    :class="form.errors.phone_number ? 'w-full rounded-lg bg-brand-black border-red-500 ring-1 ring-red-500 text-white focus:border-red-400 focus:ring-0 transition' : 'w-full rounded-lg bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-0 transition'"
                    v-model="form.phone_number" 
                    autocomplete="tel" 
                    maxlength="20"
                />
                <p class="text-[10px] text-gray-500 mt-1 ml-1">
                    Format: <span class="text-brand-neon font-mono">612 345 678</span> o <span class="text-brand-neon font-mono">+34 612 345 678</span> — Necessari per WhatsApp al mercat.
                </p>
                <div v-if="form.errors.phone_number" class="text-red-400 text-xs mt-1 ml-1">⚠ {{ form.errors.phone_number }}</div>
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-brand-dark">
                <button 
                    :disabled="form.processing" 
                    class="bg-brand-neon text-brand-black px-6 py-3 rounded-lg font-black hover:bg-white transition shadow-[0_0_15px_rgba(12,225,181,0.3)] text-xs uppercase tracking-widest disabled:opacity-50"
                >
                    {{ form.processing ? 'Guardant...' : 'Guardar Canvis' }}
                </button>

                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-2"
                >
                    <p v-if="form.recentlySuccessful" class="text-xs font-bold text-[#25D366] uppercase tracking-widest flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        Guardat
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>

<script setup>
import { useForm, usePage, Link } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    phone_number: user.phone_number || '', // Pre-carrega el telèfon si ja el tenia
});
</script>
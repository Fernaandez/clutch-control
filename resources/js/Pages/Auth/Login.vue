<template>
    <Head :title="$t('login.title')" />

    <div class="min-h-screen flex flex-col justify-center items-center bg-brand-black px-6 py-12">
        
        <div class="w-full max-w-sm">
            
            <div class="flex justify-center mb-10">
                <Link :href="route('welcome')" class="flex items-center gap-3 group hover:scale-105 transition duration-300">
                    <div class="p-2 border-2 border-brand-neon rounded-full shadow-[0_0_15px_rgba(12,225,181,0.2)] group-hover:shadow-[0_0_25px_rgba(12,225,181,0.5)] transition duration-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8 text-brand-neon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                        </svg>
                    </div>
                    <span class="text-3xl font-black text-white tracking-tighter drop-shadow-lg group-hover:text-brand-neon transition duration-300">
                        CLUTCH<span class="text-brand-neon group-hover:text-white transition duration-300">CONTROL</span>
                    </span>
                </Link>
            </div>


            <div v-if="status" class="mb-4 font-medium text-sm text-green-400 text-center">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                
                <div>
                    <label class="block font-bold text-sm text-gray-400 mb-2 ml-1" for="email">{{ $t('login.email') }}</label>
                    <input 
                        id="email" 
                        type="email" 
                        class="w-full rounded-xl bg-brand-surface border border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition py-3 px-4 placeholder-gray-600 shadow-none"
                        placeholder="exemple@correu.com"
                        v-model="form.email" 
                        required 
                        autofocus 
                        autocomplete="username" 
                    />
                    <div v-if="form.errors.email" class="text-red-500 text-xs mt-1 ml-1">{{ form.errors.email }}</div>
                </div>

                <div>
                    <label class="block font-bold text-sm text-gray-400 mb-2 ml-1" for="password">{{ $t('login.password') }}</label>
                    <input 
                        id="password" 
                        type="password" 
                        class="w-full rounded-xl bg-brand-surface border border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition py-3 px-4 placeholder-gray-600 shadow-none"
                        placeholder="••••••••"
                        v-model="form.password" 
                        required 
                        autocomplete="current-password" 
                    />
                    <div v-if="form.errors.password" class="text-red-500 text-xs mt-1 ml-1">{{ form.errors.password }}</div>
                </div>

                <div class="flex items-center justify-between mt-2 px-1">
                    <!-- Recorda'm està amagat i sempre és true per defecte per evitar pèrdues de sessió al mòbil -->
                    <label class="hidden">
                        <input type="checkbox" name="remember" v-model="form.remember" />
                    </label>

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-brand-neon hover:text-white transition underline-offset-4 hover:underline"
                    >
                        {{ $t('login.forgot') }}
                    </Link>
                </div>

                <button 
                    type="submit" 
                    :disabled="form.processing" 
                    class="w-full mt-6 bg-brand-neon text-brand-black px-6 py-4 rounded-xl font-black uppercase tracking-wider text-sm hover:bg-white hover:scale-[1.02] transition duration-300 shadow-[0_0_20px_rgba(12,225,181,0.4)]"
                >
                    {{ $t('login.submit') }}
                </button>

                <a :href="route('google.login')" class="w-full mt-4 flex items-center justify-center gap-3 bg-white text-black px-6 py-4 rounded-xl font-black uppercase tracking-wider text-sm hover:bg-gray-200 hover:scale-[1.02] transition duration-300">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    {{ $t('login.continue_with_google') }}
                </a>
                
                <div class="pt-8 text-center">
                    <span class="text-gray-500 text-sm">{{ $t('login.no_account') }}</span>
                    <Link :href="route('register')" class="ml-2 text-white font-bold hover:text-brand-neon transition">
                        {{ $t('login.register_here') }}
                    </Link>
                </div>

            </form>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: true,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head :title="$t('register.title')" />

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

            <form @submit.prevent="submit" class="space-y-5">
                
                <div>
                    <label class="block font-bold text-sm text-gray-400 mb-2 ml-1" for="name">{{ $t('register.name') }}</label>
                    <input 
                        id="name" 
                        type="text" 
                        class="w-full rounded-xl bg-brand-surface border border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition py-3 px-4 placeholder-gray-600 shadow-none"
                        :placeholder="$t('register.name_placeholder')"
                        v-model="form.name" 
                        required 
                        autofocus 
                        autocomplete="name" 
                    />
                    <div v-if="form.errors.name" class="text-red-500 text-xs mt-1 ml-1">{{ form.errors.name }}</div>
                </div>

                <div>
                    <label class="block font-bold text-sm text-gray-400 mb-2 ml-1" for="email">{{ $t('register.email') }}</label>
                    <input 
                        id="email" 
                        type="email" 
                        class="w-full rounded-xl bg-brand-surface border border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition py-3 px-4 placeholder-gray-600 shadow-none"
                        placeholder="exemple@correu.com"
                        v-model="form.email" 
                        required 
                        autocomplete="username" 
                    />
                    <div v-if="form.errors.email" class="text-red-500 text-xs mt-1 ml-1">{{ form.errors.email }}</div>
                </div>

                <div>
                    <label class="block font-bold text-sm text-gray-400 mb-2 ml-1" for="phone">{{ $t('register.phone') }}</label>
                    <input 
                        id="phone" 
                        type="tel" 
                        class="w-full rounded-xl bg-brand-surface border border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition py-3 px-4 placeholder-gray-600 shadow-none"
                        placeholder="Ex: +34 600 000 000"
                        v-model="form.phone" 
                        autocomplete="tel" 
                    />
                    <p class="text-[10px] text-gray-500 mt-1 ml-1">{{ $t('register.phone_hint') }}</p>
                    <div v-if="form.errors.phone" class="text-red-500 text-xs mt-1 ml-1">{{ form.errors.phone }}</div>
                </div>

                <div>
                    <label class="block font-bold text-sm text-gray-400 mb-2 ml-1" for="password">{{ $t('register.password') }}</label>
                    <input 
                        id="password" 
                        type="password" 
                        class="w-full rounded-xl bg-brand-surface border border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition py-3 px-4 placeholder-gray-600 shadow-none"
                        placeholder="••••••••"
                        v-model="form.password" 
                        required 
                        autocomplete="new-password" 
                    />
                    <div v-if="form.errors.password" class="text-red-500 text-xs mt-1 ml-1">{{ form.errors.password }}</div>
                </div>

                <div>
                    <label class="block font-bold text-sm text-gray-400 mb-2 ml-1" for="password_confirmation">{{ $t('register.confirm_password') }}</label>
                    <input 
                        id="password_confirmation" 
                        type="password" 
                        class="w-full rounded-xl bg-brand-surface border border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon transition py-3 px-4 placeholder-gray-600 shadow-none"
                        placeholder="••••••••"
                        v-model="form.password_confirmation" 
                        required 
                        autocomplete="new-password" 
                    />
                    <div v-if="form.errors.password_confirmation" class="text-red-500 text-xs mt-1 ml-1">{{ form.errors.password_confirmation }}</div>
                </div>

                <div class="mt-4 pt-2">
                    <p class="text-[11px] text-gray-500 text-center uppercase tracking-wide">
                        {{ $t('legal.auth_accept') }} 
                        <Link :href="route('terms.service')" target="_blank" class="text-brand-neon hover:text-white underline transition">
                            {{ $t('legal.terms_title') }}
                        </Link> 
                        {{ $t('legal.and') }} 
                        <Link :href="route('privacy.policy')" target="_blank" class="text-brand-neon hover:text-white underline transition">
                            {{ $t('legal.privacy_title') }}
                        </Link>.
                    </p>
                </div>

                <button 
                    type="submit" 
                    :disabled="form.processing" 
                    class="w-full mt-8 bg-brand-neon text-brand-black px-6 py-4 rounded-xl font-black uppercase tracking-wider text-sm hover:bg-white hover:scale-[1.02] transition duration-300 shadow-[0_0_20px_rgba(12,225,181,0.4)]"
                >
                    {{ $t('register.submit') }}
                </button>

                <div class="pt-8 text-center">
                    <span class="text-gray-500 text-sm">{{ $t('register.already') }}</span>
                    <Link :href="route('login')" class="ml-2 text-white font-bold hover:text-brand-neon transition">
                        {{ $t('register.login_here') }}
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

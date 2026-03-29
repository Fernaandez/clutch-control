<template>
    <Head :title="$t('legal.privacy_title')" />

    <div class="min-h-screen bg-brand-black text-gray-300 py-12 px-4 sm:px-6 lg:px-8 selection:bg-brand-neon selection:text-brand-black">
        <div class="max-w-3xl mx-auto">
            <div class="flex items-center justify-center mb-10 pb-6 border-b border-brand-dark/50">
                <Link href="/" class="flex items-center gap-3 group">
                    <div class="p-2 border-2 border-brand-neon rounded-full shadow-[0_0_15px_rgba(12,225,181,0.2)] group-hover:shadow-[0_0_25px_rgba(12,225,181,0.5)] transition duration-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-brand-neon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                        </svg>
                    </div>
                </Link>
            </div>

            <div class="bg-brand-surface border border-brand-dark p-8 md:p-12 rounded-3xl shadow-2xl relative overflow-hidden">
                <!-- Decorative element -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-brand-neon opacity-[0.03] blur-3xl rounded-full"></div>
                
                <h1 class="text-4xl md:text-5xl font-black text-white uppercase tracking-tighter mb-4 relative z-10">
                    {{ $t('legal.privacy_title') }}
                </h1>
                
                <p class="text-xs text-brand-neon font-mono mb-10 tracking-widest uppercase relative z-10">
                    {{ $t('legal.updated') }}: <span class="text-gray-400">March 2026</span>
                </p>
                
                <div class="space-y-8 text-base leading-relaxed relative z-10 font-medium">
                    
                    <section>
                        <div class="flex items-start gap-4">
                            <div class="mt-1 bg-brand-black p-2 rounded-lg border border-brand-dark flex-shrink-0 text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>
                            </div>
                            <p>{{ $t('legal.privacy_p1') }}</p>
                        </div>
                    </section>
                    
                    <div class="h-px bg-brand-dark/50"></div>
                    
                    <section>
                        <h2 class="text-xl font-bold text-white mb-4 uppercase tracking-wider">{{ $t('legal.privacy_h2_1') }}</h2>
                        <div class="flex items-start gap-4">
                            <div class="mt-1 bg-brand-black p-2 rounded-lg border border-brand-dark flex-shrink-0 text-brand-neon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                            </div>
                            <p>{{ $t('legal.privacy_p2') }}</p>
                        </div>
                    </section>

                    <div class="h-px bg-brand-dark/50"></div>
                    
                    <section>
                        <h2 class="text-xl font-bold text-white mb-4 uppercase tracking-wider">{{ $t('legal.privacy_h2_2') }}</h2>
                        <div class="flex items-start gap-4">
                            <div class="mt-1 bg-brand-black p-2 rounded-lg border border-brand-dark flex-shrink-0 text-blue-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
                            </div>
                            <p>{{ $t('legal.privacy_p3') }}</p>
                        </div>
                    </section>

                    <div class="h-px bg-brand-dark/50"></div>
                    
                    <section>
                        <h2 class="text-xl font-bold text-white mb-4 uppercase tracking-wider">{{ $t('legal.privacy_h2_3') }}</h2>
                        <div class="flex items-start gap-4">
                            <div class="mt-1 bg-brand-black p-2 rounded-lg border border-brand-dark flex-shrink-0 text-purple-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
                            </div>
                            <p>{{ $t('legal.privacy_p4') }}</p>
                        </div>
                    </section>
                </div>
            </div>
            
            <div class="mt-8 text-center">
                <Link :href="route('profile.edit')" class="inline-block px-8 py-3 bg-brand-neon text-black font-black uppercase tracking-widest rounded-xl hover:bg-white hover:scale-105 transition-all shadow-[0_0_20px_rgba(12,225,181,0.3)]">
                    {{ $t('common.back') }}
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

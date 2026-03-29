<template>
    <Head :title="$t('legal.terms_title')" />

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
                <div class="absolute top-0 right-0 w-64 h-64 bg-red-600 opacity-[0.03] blur-3xl rounded-full"></div>
                
                <h1 class="text-4xl md:text-5xl font-black text-white uppercase tracking-tighter mb-4 relative z-10 w-full lg:w-3/4">
                    {{ $t('legal.terms_title') }}
                </h1>
                
                <p class="text-xs text-red-500 font-mono mb-10 tracking-widest uppercase relative z-10">
                    {{ $t('legal.updated') }}: <span class="text-gray-400">March 2026</span>
                </p>
                
                <div class="space-y-8 text-base leading-relaxed relative z-10 font-medium">
                    
                    <section>
                        <div class="flex items-start gap-4">
                            <div class="mt-1 bg-brand-black p-2 rounded-lg border border-brand-dark flex-shrink-0 text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>
                            </div>
                            <p>{{ $t('legal.terms_p1') }}</p>
                        </div>
                    </section>
                    
                    <div class="h-px bg-brand-dark/50"></div>
                    
                    <section>
                        <h2 class="text-xl font-bold text-white mb-4 uppercase tracking-wider">{{ $t('legal.terms_h2_1') }}</h2>
                        <div class="flex items-start gap-4">
                            <div class="mt-1 bg-brand-black p-2 rounded-lg border border-brand-dark flex-shrink-0 text-brand-neon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3Z" /></svg>
                            </div>
                            <p>{{ $t('legal.terms_p2') }}</p>
                        </div>
                    </section>

                    <div class="h-px bg-brand-dark/50"></div>
                    
                    <section>
                        <h2 class="text-xl font-bold text-white mb-4 uppercase tracking-wider">{{ $t('legal.terms_h2_2') }}</h2>
                        <div class="flex items-start gap-4">
                            <div class="mt-1 bg-brand-black p-2 rounded-lg border border-brand-dark flex-shrink-0 text-blue-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" /></svg>
                            </div>
                            <p>{{ $t('legal.terms_p3') }}</p>
                        </div>
                    </section>

                    <div class="h-px bg-brand-dark/50"></div>
                    
                    <section>
                        <h2 class="text-xl font-bold text-white mb-4 uppercase tracking-wider">{{ $t('legal.terms_h2_3') }}</h2>
                        <div class="flex items-start gap-4">
                            <div class="mt-1 bg-brand-black p-2 rounded-lg border border-brand-dark flex-shrink-0 text-red-500">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                            </div>
                            <p>{{ $t('legal.terms_p4') }}</p>
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

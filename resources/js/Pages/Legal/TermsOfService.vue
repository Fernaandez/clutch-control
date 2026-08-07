<template>
    <Head :title="$t('legal.terms_title')" />

    <div class="min-h-screen bg-brand-black text-gray-300">
        <div class="max-w-xl mx-auto px-6 pt-10 pb-28">

            <div class="flex items-center gap-3 mb-10">
                <Link
                    href="/"
                    class="cc-icon-btn"
                    :aria-label="$t('common.back')"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                    </svg>
                </Link>
            </div>

            <h1 class="cc-title">{{ $t('legal.terms_title') }}</h1>
            <p class="mt-3 text-xs text-gray-500 uppercase tracking-[0.14em]">
                {{ $t('legal.updated') }}: March 2026
            </p>

            <div class="mt-12 space-y-10 text-[15px] leading-relaxed text-gray-300 divide-y divide-white/[0.06]">
                <section class="pb-10">
                    <p>{{ $t('legal.terms_p1') }}</p>
                </section>

                <section class="pt-10 pb-10">
                    <h2 class="cc-section-label mb-4">{{ $t('legal.terms_h2_1') }}</h2>
                    <p>{{ $t('legal.terms_p2') }}</p>
                </section>

                <section class="pt-10 pb-10">
                    <h2 class="cc-section-label mb-4">{{ $t('legal.terms_h2_2') }}</h2>
                    <p>{{ $t('legal.terms_p3') }}</p>
                </section>

                <section class="pt-10">
                    <h2 class="cc-section-label mb-4">{{ $t('legal.terms_h2_3') }}</h2>
                    <p>{{ $t('legal.terms_p4') }}</p>
                </section>
            </div>

            <div class="mt-12">
                <button
                    type="button"
                    @click="goBack"
                    class="cc-btn-secondary w-full"
                >
                    {{ $t('common.back') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { smartBack } from '@/Composables/navigationStack.js';

const page = usePage();

const goBack = () => {
    const fallback = page.props.auth.user ? route('profile.edit') : route('welcome');
    smartBack(fallback);
};
</script>

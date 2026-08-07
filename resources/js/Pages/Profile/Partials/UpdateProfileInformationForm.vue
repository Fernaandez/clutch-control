<template>
    <section>
        <p class="cc-section-label">{{ $t('profile.profile_info') }}</p>
        <p class="mt-1 text-sm text-gray-500">
            {{ $t('profile.profile_info_hint') }}
        </p>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-5">

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1.5" for="name">
                    {{ $t('profile.name') }} <span class="text-red-400">*</span>
                </label>
                <input
                    id="name"
                    type="text"
                    :class="inputClass(form.errors.name)"
                    v-model="form.name"
                    required
                    autocomplete="name"
                />
                <p v-if="form.errors.name" class="text-red-400 text-xs mt-1">{{ form.errors.name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1.5" for="email">
                    {{ $t('profile.email') }} <span class="text-red-400">*</span>
                </label>
                <input
                    id="email"
                    type="email"
                    :class="inputClass(form.errors.email)"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <p class="text-xs text-gray-500 mt-1">{{ $t('profile.email_format') }} <span class="font-mono text-gray-400">nom@exemple.com</span></p>
                <p v-if="form.errors.email" class="text-red-400 text-xs mt-1">{{ form.errors.email }}</p>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null" class="pt-1">
                <p class="text-sm text-red-400">
                    {{ $t('profile.email_not_verified') }}
                </p>
                <Link
                    :href="route('verification.send')"
                    method="post"
                    as="button"
                    class="cc-btn-text mt-3"
                >
                    {{ $t('profile.resend_verification') }}
                </Link>
                <p v-show="status === 'verification-link-sent'" class="mt-3 text-sm text-gray-300">
                    {{ $t('profile.verification_sent') }}
                </p>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="cc-btn-primary"
                >
                    {{ form.processing ? $t('profile.saving') : $t('profile.save_changes') }}
                </button>

                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-2"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-400">
                        {{ $t('profile.saved') }}
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
});

const inputClass = (error) =>
    error
        ? 'w-full rounded-xl bg-white/[0.04] border-red-500 ring-1 ring-red-500 text-white focus:border-red-400 focus:ring-0'
        : 'w-full rounded-xl bg-white/[0.04] border-white/[0.08] text-white focus:border-white/30 focus:ring-0';
</script>

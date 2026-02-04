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

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>
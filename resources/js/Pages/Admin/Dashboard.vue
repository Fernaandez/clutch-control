<template>
    <AdminLayout>
        <template #header>
            {{ $t('admin.dashboard') }}
        </template>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ $t('admin.users') }}</p>
                <p class="mt-2 text-3xl font-light tabular-nums text-gray-900">{{ stats.users }}</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ $t('admin.routes') }}</p>
                <p class="mt-2 text-3xl font-light tabular-nums text-gray-900">{{ stats.routes }}</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ $t('admin.events') }}</p>
                <p class="mt-2 text-3xl font-light tabular-nums text-gray-900">{{ stats.events }}</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ $t('admin.sales') }}</p>
                <p class="mt-2 text-3xl font-light tabular-nums text-gray-900">{{ stats.sales }}</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ $t('admin.motorcycles') }}</p>
                <p class="mt-2 text-3xl font-light tabular-nums text-gray-900">{{ stats.motorcycles }}</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ $t('admin.maintenance') }}</p>
                <p class="mt-2 text-3xl font-light tabular-nums text-gray-900">{{ stats.maintenance }}</p>
            </div>

            <Link
                :href="route('admin.reports.index')"
                class="bg-white rounded-xl border border-gray-200 p-5 hover:border-gray-400 transition col-span-2 lg:col-span-2"
            >
                <p class="text-xs font-medium uppercase tracking-wider text-red-600">Denuncies pendents</p>
                <p class="mt-2 text-3xl font-light tabular-nums text-gray-900">{{ stats.reports_pending }}</p>
            </Link>
        </div>

        <div class="mt-8 bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-1">{{ $t('admin.welcome_title') }}</h2>
            <p class="text-gray-600 leading-relaxed text-sm">
                {{ $t('admin.welcome_text') }}
            </p>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ stats: Object });
</script>

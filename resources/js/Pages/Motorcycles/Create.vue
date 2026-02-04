<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto px-4 py-8">
            
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-white">Afegir Nova Moto</h1>
                <Link :href="route('motorcycles.index')" class="text-gray-400 hover:text-white transition">Cancel·lar</Link>
            </div>

            <div class="bg-brand-surface p-6 rounded-xl border border-brand-dark shadow-lg">
                <form @submit.prevent="submit" class="space-y-6">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Marca</label>
                            <input v-model="form.brand" type="text" placeholder="Ex: Yamaha" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon" required>
                            <div v-if="form.errors.brand" class="text-red-500 text-xs mt-1">{{ form.errors.brand }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Model</label>
                            <input v-model="form.model" type="text" placeholder="Ex: MT-07" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon" required>
                            <div v-if="form.errors.model" class="text-red-500 text-xs mt-1">{{ form.errors.model }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Matrícula</label>
                        <input v-model="form.plate" type="text" placeholder="1234-XYZ" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon uppercase" required>
                        <div v-if="form.errors.plate" class="text-red-500 text-xs mt-1">{{ form.errors.plate }}</div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Any</label>
                            <input v-model="form.year" type="number" placeholder="2024" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon" required>
                            <div v-if="form.errors.year" class="text-red-500 text-xs mt-1">{{ form.errors.year }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Quilòmetres Actuals</label>
                            <input v-model="form.current_km" type="number" step="0.1" placeholder="0" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon" required>
                            <div v-if="form.errors.current_km" class="text-red-500 text-xs mt-1">{{ form.errors.current_km }}</div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" :disabled="form.processing" class="w-full bg-brand-base hover:bg-brand-neon text-brand-black font-bold py-3 rounded-lg shadow-neon transition duration-300 transform hover:-translate-y-1">
                            {{ form.processing ? 'Guardant...' : 'Guardar Moto' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const form = useForm({
    brand: '',
    model: '',
    plate: '',
    year: '',
    current_km: ''
});

const submit = () => {
    form.post(route('motorcycles.store'));
};
</script>
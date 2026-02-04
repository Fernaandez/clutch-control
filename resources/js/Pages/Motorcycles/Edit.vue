<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto px-4 py-8">
            
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-white">Editar Moto</h1>
                <Link :href="route('motorcycles.index')" class="text-gray-400 hover:text-white transition">Tornar</Link>
            </div>

            <div class="bg-brand-surface p-6 rounded-xl border border-brand-dark shadow-lg">
                <form @submit.prevent="submit" class="space-y-6">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Marca</label>
                            <input v-model="form.brand" type="text" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Model</label>
                            <input v-model="form.model" type="text" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Matrícula</label>
                        <input v-model="form.plate" type="text" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon uppercase" required>
                        <div v-if="form.errors.plate" class="text-red-500 text-xs mt-1">{{ form.errors.plate }}</div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Any</label>
                            <input v-model="form.year" type="number" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Quilòmetres</label>
                            <input v-model="form.current_km" type="number" step="0.1" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-brand-neon" required>
                        </div>
                    </div>

                    <div class="pt-6 flex flex-col gap-4">
                        <button type="submit" :disabled="form.processing" class="w-full bg-brand-base hover:bg-brand-neon text-brand-black font-bold py-3 rounded-lg shadow-neon transition duration-300">
                            Actualitzar Dades
                        </button>

                        <div class="border-t border-brand-dark my-2"></div>

                        <button type="button" @click="destroy" class="w-full text-red-500 hover:text-red-400 hover:bg-brand-black py-2 rounded transition text-sm">
                            Eliminar aquesta moto definitivament
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

const props = defineProps({
    moto: Object
});

const form = useForm({
    brand: props.moto.brand,
    model: props.moto.model,
    plate: props.moto.plate,
    year: props.moto.year,
    current_km: props.moto.current_km
});

const submit = () => {
    form.put(route('motorcycles.update', props.moto.id));
};

const destroy = () => {
    if (confirm('Estàs segur? Això esborrarà la moto i tot el seu historial.')) {
        form.delete(route('motorcycles.destroy', props.moto.id));
    }
};
</script>
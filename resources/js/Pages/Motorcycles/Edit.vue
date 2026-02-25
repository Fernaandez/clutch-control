<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto px-4 py-8 pb-24">
            
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-white">Editar Moto</h1>
                <Link :href="route('motorcycles.index')" class="text-gray-400 hover:text-white transition">Tornar</Link>
            </div>

            <div class="bg-brand-surface p-6 rounded-xl border border-brand-dark shadow-lg">
                <form @submit.prevent="submit" class="space-y-6">
                    
                    <div>
                        <h2 class="text-sm font-black text-brand-neon uppercase tracking-widest mb-4 border-b border-brand-dark pb-2">Dades Obligatòries</h2>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Marca</label>
                                <input v-model="form.brand" type="text" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Model</label>
                                <input v-model="form.model" type="text" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Matrícula</label>
                            <input v-model="form.plate" type="text" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon uppercase" required>
                            <div v-if="form.errors.plate" class="text-red-500 text-xs mt-1">{{ form.errors.plate }}</div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Any</label>
                                <input v-model="form.year" type="number" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Quilòmetres</label>
                                <input v-model="form.current_km" type="number" step="0.1" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon" required>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-4">
                        <h2 class="text-sm font-black text-brand-neon uppercase tracking-widest mb-4 border-b border-brand-dark pb-2">Especificacions (Opcional)</h2>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                            <div class="col-span-1">
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">CC</label>
                                <input v-model="form.cc" type="number" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon">
                            </div>
                            <div class="col-span-1">
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Cavalls (CV)</label>
                                <input v-model="form.power_cv" type="number" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Carnet</label>
                                <select v-model="form.license_type" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon">
                                    <option value="">Selecciona...</option>
                                    <option value="AM">AM (Fins 50cc)</option>
                                    <option value="A1">A1 (Fins 125cc)</option>
                                    <option value="A2">A2 (Limitada 35kW)</option>
                                    <option value="A">A (Sense límit)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Estil de Moto</label>
                                <select v-model="form.type" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon">
                                    <option value="">Selecciona...</option>
                                    <option value="Naked">Naked</option>
                                    <option value="Sport">Sport / R</option>
                                    <option value="Trail">Trail / Adventure</option>
                                    <option value="Custom">Custom / Cruiser</option>
                                    <option value="Scooter">Scooter / Maxi-Scooter</option>
                                    <option value="Touring">Touring</option>
                                    <option value="Off-Road">Off-Road / Enduro</option>
                                    <option value="Classic">Clàssica / Cafe Racer</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Extres instal·lats</label>
                            <textarea v-model="form.extras" rows="3" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon resize-none"></textarea>
                        </div>
                    </div>

                    <div class="pt-6 flex flex-col gap-4 border-t border-brand-dark">
                        <button type="submit" :disabled="form.processing" class="w-full bg-brand-base hover:bg-brand-neon text-brand-black font-black uppercase tracking-wider py-4 rounded-xl transition duration-300">
                            {{ form.processing ? 'Guardant...' : 'Actualitzar Dades' }}
                        </button>

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
    current_km: props.moto.current_km,
    cc: props.moto.cc || '',
    power_cv: props.moto.power_cv || '',
    license_type: props.moto.license_type || '',
    type: props.moto.type || '',
    extras: props.moto.extras || ''
});

const submit = () => {
    form.put(route('motorcycles.update', props.moto.id));
};

const destroy = () => {
    if (confirm('Estàs segur? Això esborrarà la moto, el seu historial i l\'anunci de venda si en té un.')) {
        form.delete(route('motorcycles.destroy', props.moto.id));
    }
};
</script>
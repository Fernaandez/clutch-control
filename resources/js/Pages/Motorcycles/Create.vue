<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto px-4 py-8 pb-24">
            
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-white">Afegir Nova Moto</h1>
                <Link :href="route('motorcycles.index')" class="text-gray-400 hover:text-white transition">Cancel·lar</Link>
            </div>

            <div class="bg-brand-surface p-6 rounded-xl border border-brand-dark shadow-lg">
                <form @submit.prevent="submit" class="space-y-6">
                    
                    <div v-if="Object.keys(form.errors).length > 0" class="p-4 bg-red-500/10 border border-red-500/30 rounded-xl animate-fade-in">
                        <p class="text-red-500 font-black text-xs uppercase tracking-widest mb-2">⚠️ S'han trobat errors:</p>
                        <ul class="list-disc pl-5 text-red-400 text-sm">
                            <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-sm font-black text-brand-neon uppercase tracking-widest mb-4 border-b border-brand-dark pb-2">Dades Obligatòries</h2>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Marca</label>
                                <input v-model="form.brand" type="text" placeholder="Ex: Yamaha" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon" required>
                                <div v-if="form.errors.brand" class="text-red-500 text-xs mt-1">{{ form.errors.brand }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Model</label>
                                <input v-model="form.model" type="text" placeholder="Ex: MT-07" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon" required>
                                <div v-if="form.errors.model" class="text-red-500 text-xs mt-1">{{ form.errors.model }}</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Matrícula</label>
                            <input v-model="form.plate" type="text" placeholder="1234-XYZ" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon uppercase" required>
                            <div v-if="form.errors.plate" class="text-red-500 text-xs mt-1">{{ form.errors.plate }}</div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Any</label>
                                <input v-model="form.year" type="number" placeholder="2024" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon" required>
                                <div v-if="form.errors.year" class="text-red-500 text-xs mt-1">{{ form.errors.year }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Quilòmetres Actuals</label>
                                <input v-model="form.current_km" type="number" step="0.1" placeholder="0" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon" required>
                                <div v-if="form.errors.current_km" class="text-red-500 text-xs mt-1">{{ form.errors.current_km }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-4">
                        <h2 class="text-sm font-black text-brand-neon uppercase tracking-widest mb-1 border-b border-brand-dark pb-2">Especificacions (Opcional)</h2>
                        <p class="text-xs text-gray-500 mb-4">Afegeix-ho ara i el teu anunci de venda es crearà automàticament.</p>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                            <div class="col-span-1">
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">CC</label>
                                <input v-model="form.cc" type="number" placeholder="689" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon">
                            </div>
                            <div class="col-span-1">
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Cavalls (CV)</label>
                                <input v-model="form.power_cv" type="number" placeholder="73" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Carnet</label>
                                <select v-model="form.license_type" class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon">
                                    <option value="">Selecciona...</option>
                                    <option value="AM">AM</option>
                                    <option value="A1">A1</option>
                                    <option value="A2">A2</option>
                                    <option value="A">A</option>
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
                            <textarea v-model="form.extras" rows="3" placeholder="Tub d'escapament, maletes, cúpula..." class="w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon resize-none"></textarea>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-brand-dark">
                        <button type="submit" :disabled="form.processing" class="w-full bg-brand-base hover:bg-brand-neon text-brand-black font-black uppercase tracking-wider py-4 rounded-xl transition duration-300 transform hover:-translate-y-1">
                            {{ form.processing ? 'Guardant...' : 'Guardar al Garatge' }}
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
    current_km: '',
    cc: '',
    power_cv: '',
    license_type: '',
    type: '',
    extras: ''
});

const submit = () => {
    form.post(route('motorcycles.store'));
};
</script>
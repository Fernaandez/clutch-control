<template>
    <AppLayout title="Editar Anunci">
        <div class="px-4 py-6 pb-24 max-w-2xl mx-auto">
            
            <div class="mb-8">
                <Link :href="route('sales.mine')" class="text-gray-400 text-sm hover:text-white flex items-center gap-1 transition mb-4">
                    &larr; Tornar a Els meus Anuncis
                </Link>
                <h1 class="text-3xl font-black text-white uppercase tracking-tighter">Editar <span class="text-brand-neon">Anunci</span></h1>
                <p class="text-gray-400 text-sm mt-1">Estàs editant: {{ sale.motorcycle?.brand }} {{ sale.motorcycle?.model }}</p>
            </div>

            <form @submit.prevent="submit" class="bg-brand-surface border border-brand-dark rounded-xl p-5 shadow-xl space-y-6">
                
                <div class="bg-brand-black p-4 rounded-xl border border-brand-dark">
                    <h3 class="text-xs font-black text-brand-neon uppercase tracking-widest mb-3">Especificacions de la Moto</h3>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
                        <div class="col-span-1">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">CC</label>
                            <input v-model="form.cc" type="number" class="w-full text-sm rounded bg-brand-surface border-brand-dark text-white focus:border-brand-neon">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">CV</label>
                            <input v-model="form.power_cv" type="number" class="w-full text-sm rounded bg-brand-surface border-brand-dark text-white focus:border-brand-neon">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Carnet</label>
                            <select v-model="form.license_type" class="w-full text-sm rounded bg-brand-surface border-brand-dark text-white focus:border-brand-neon">
                                <option value="">Sense definir</option>
                                <option value="AM">AM</option><option value="A1">A1</option><option value="A2">A2</option><option value="A">A</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Estil</label>
                        <select v-model="form.type" class="w-full text-sm rounded bg-brand-surface border-brand-dark text-white focus:border-brand-neon">
                            <option value="">Sense definir</option>
                            <option value="Naked">Naked</option><option value="Sport">Sport / R</option>
                            <option value="Trail">Trail / Adventure</option><option value="Custom">Custom / Cruiser</option>
                            <option value="Scooter">Scooter / Maxi</option><option value="Touring">Touring</option>
                            <option value="Off-Road">Off-Road</option><option value="Classic">Clàssica</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Extres instal·lats</label>
                        <textarea v-model="form.extras" rows="2" class="w-full text-sm rounded bg-brand-surface border-brand-dark text-white focus:border-brand-neon resize-none"></textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Títol de l'anunci</label>
                    <input v-model="form.title" type="text" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0">
                    <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Preu (€)</label>
                        <input v-model="form.price" type="number" step="0.01" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0 text-brand-neon font-bold text-lg">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Ubicació</label>
                        <input v-model="form.location" type="text" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Descripció addicional</label>
                    <textarea v-model="form.description" rows="5" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon focus:ring-0 resize-none"></textarea>
                </div>

                <div class="border-t border-brand-dark pt-4">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-3">Estat de l'anunci</label>
                    <div class="flex flex-col gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input v-model="form.is_active" type="checkbox" class="rounded bg-brand-black border-brand-dark text-brand-neon focus:ring-brand-neon h-5 w-5">
                            <span class="text-sm font-bold text-gray-300">Anunci Visible al Mur (Actiu)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input v-model="form.is_sold" type="checkbox" class="rounded bg-brand-black border-brand-dark text-red-500 focus:ring-red-500 h-5 w-5">
                            <span class="text-sm font-bold text-red-400">Marcar com a VENUDA</span>
                        </label>
                    </div>
                </div>

                <div class="pt-6 border-t border-brand-dark flex flex-col gap-3">
                    <button type="submit" :disabled="form.processing" class="w-full bg-brand-neon text-brand-black font-black uppercase tracking-wider py-4 rounded-xl hover:bg-white transition flex items-center justify-center gap-2">
                        <span v-if="form.processing">Guardant...</span>
                        <span v-else>Actualitzar Anunci i Moto</span>
                    </button>
                    
                    <button type="button" @click="destroy" class="w-full text-red-500 hover:text-red-400 hover:bg-brand-black py-2 rounded transition text-sm">
                        Eliminar Anunci Definitivament
                    </button>
                </div>
            </form>

        </div>
    </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    sale: Object
});

const form = useForm({
    title: props.sale.title,
    description: props.sale.description,
    price: props.sale.price,
    location: props.sale.location,
    is_active: Boolean(props.sale.is_active),
    is_sold: Boolean(props.sale.is_sold),
    // Dades de la moto precargades per editar
    cc: props.sale.motorcycle?.cc || '',
    power_cv: props.sale.motorcycle?.power_cv || '',
    license_type: props.sale.motorcycle?.license_type || '',
    type: props.sale.motorcycle?.type || '',
    extras: props.sale.motorcycle?.extras || ''
});

const submit = () => {
    form.put(route('sales.update', props.sale.id));
};

const destroy = () => {
    if (confirm('Estàs segur que vols esborrar aquest anunci per sempre? La moto no s\'esborrarà del teu garatge.')) {
        form.delete(route('sales.destroy', props.sale.id));
    }
};
</script>
<template>
    <AppLayout title="Vendre Moto">
        <div class="px-4 py-6 pb-24 max-w-2xl mx-auto">
            
            <div class="mb-8">
                <Link :href="route('sales.index')" class="text-gray-400 text-sm hover:text-white flex items-center gap-1 transition mb-4">
                    &larr; Tornar al Mercat
                </Link>
                <h1 class="text-3xl font-black text-white uppercase tracking-tighter">Posar a la <span class="text-brand-neon">Venda</span></h1>
                <p class="text-gray-400 text-sm mt-1">Comparteix la teva màquina amb la comunitat.</p>
            </div>

            <div v-if="motorcycles.length === 0" class="bg-brand-surface border border-brand-dark rounded-xl p-6 text-center">
                <div class="text-4xl mb-3">😅</div>
                <h3 class="text-lg font-bold text-white mb-2">No tens motos disponibles</h3>
                <p class="text-sm text-gray-400 mb-4">No tens cap moto al teu garatge, o bé totes ja estan anunciades actualment.</p>
                <Link :href="route('motorcycles.create')" class="inline-block bg-brand-neon text-brand-black font-bold uppercase text-xs px-6 py-3 rounded hover:bg-white transition">
                    Afegir Moto al Garatge
                </Link>
            </div>



            <form v-else @submit.prevent="submit" class="bg-brand-surface border border-brand-dark rounded-xl p-5 shadow-xl space-y-6">
                

            <div v-if="!$page.props.auth.user.phone_number" class="mb-6 p-4 rounded-xl border border-yellow-500/30 bg-yellow-500/5 flex items-start gap-4 animate-fade-in">
                <div class="p-2 bg-yellow-500/10 rounded-lg text-yellow-500 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-yellow-500 uppercase tracking-widest mb-1">Telèfon Requerit</h3>
                    <p class="text-xs text-gray-400 mb-2">
                        Per publicar l'anunci, necessites tenir un número de telèfon guardat perquè els compradors et puguin contactar.
                    </p>
                    <Link :href="route('profile.edit')" class="text-xs font-bold text-white hover:text-yellow-400 transition underline underline-offset-4">
                        &rarr; Anar al perfil per afegir-lo
                    </Link>
                </div>
            </div>

            <div v-else class="border border-brand-dark rounded-xl p-5 mb-6 bg-brand-surface shadow-lg">
                <h3 class="text-sm font-black text-white uppercase tracking-widest mb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-[#25D366]"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    Contacte via WhatsApp
                </h3>
                <p class="text-xs text-gray-400 mb-4">
                    Necessitem el teu permís per mostrar el botó de WhatsApp amb el teu número (<span class="text-white font-mono">{{ $page.props.auth.user.phone_number }}</span>) a l'anunci.
                </p>

                <div class="flex gap-3">
                    <button type="button" @click="form.accept_whatsapp = true"
                            :class="form.accept_whatsapp === true ? 'bg-[#25D366] text-white border-[#25D366] shadow-[0_0_15px_rgba(37,211,102,0.3)]' : 'bg-brand-black text-gray-400 border-brand-dark hover:border-gray-500'"
                            class="flex-1 border py-3 rounded-lg font-bold text-xs uppercase tracking-wider transition duration-300">
                        Acceptar
                    </button>
                    <button type="button" @click="form.accept_whatsapp = false"
                            :class="form.accept_whatsapp === false ? 'bg-red-500/10 text-red-500 border-red-500' : 'bg-brand-black text-gray-400 border-brand-dark hover:border-gray-500'"
                            class="flex-1 border py-3 rounded-lg font-bold text-xs uppercase tracking-wider transition duration-300">
                        Rebutjar
                    </button>
                </div>

                <div v-if="form.accept_whatsapp === false" class="mt-3 text-xs text-red-400 font-bold uppercase tracking-widest text-center animate-fade-in">
                    ⚠️ Has d'acceptar per poder publicar l'anunci.
                </div>
            
            </div>


                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Quina moto vens?</label>
                    <select v-model="form.motorcycle_id" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon">
                        <option :value="null" disabled>Selecciona una moto del teu garatge...</option>
                        <option v-for="moto in motorcycles" :key="moto.id" :value="moto.id">
                            {{ moto.brand }} {{ moto.model }} ({{ moto.year }}) - {{ moto.current_km }} km
                        </option>
                    </select>
                </div>

                <div v-if="form.motorcycle_id" class="bg-brand-black p-4 rounded-xl border border-brand-dark animate-fade-in">
                    <h3 class="text-xs font-black text-brand-neon uppercase tracking-widest mb-3">Revisa les Dades Tècniques</h3>
                    <p class="text-[10px] text-gray-500 mb-4">Això actualitzarà la fitxa de la teva moto i es mostrarà a l'anunci.</p>

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

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Estil</label>
                            <select v-model="form.type" class="w-full text-sm rounded bg-brand-surface border-brand-dark text-white focus:border-brand-neon">
                                <option value="">Sense definir</option>
                                <option value="Naked">Naked</option><option value="Sport">Sport / R</option>
                                <option value="Trail">Trail / Adventure</option><option value="Custom">Custom / Cruiser</option>
                                <option value="Scooter">Scooter / Maxi</option><option value="Touring">Touring</option>
                                <option value="Off-Road">Off-Road</option><option value="Classic">Clàssica</option>
                            </select>
                        </div>
                        <div class="flex items-center pt-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input v-model="form.has_abs" type="checkbox" class="rounded bg-brand-surface border-brand-dark text-brand-neon focus:ring-brand-neon h-4 w-4">
                                <span class="text-xs font-bold text-gray-300">Equipada amb ABS</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Extres instal·lats</label>
                        <textarea v-model="form.extras" rows="2" class="w-full text-sm rounded bg-brand-surface border-brand-dark text-white focus:border-brand-neon resize-none"></textarea>
                    </div>
                </div>

                <div v-if="form.motorcycle_id" class="animate-fade-in space-y-6 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Títol de l'anunci</label>
                        <input v-model="form.title" type="text" placeholder="Ex: Yamaha MT-07 Impecable (A2)" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Preu (€)</label>
                            <input v-model="form.price" type="number" step="0.01" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon text-brand-neon font-bold text-lg" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Ubicació</label>
                            <input v-model="form.location" type="text" placeholder="Ex: Barcelona" class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Comentaris pel comprador</label>
                        <textarea v-model="form.description" rows="4" placeholder="Explica el motiu de la venda, si les revisions estan al dia..." class="w-full bg-brand-black border-brand-dark rounded-lg text-white focus:border-brand-neon resize-none"></textarea>
                    </div>

                    <div class="pt-4 border-t border-brand-dark">
                        <button type="submit" :disabled="form.processing" class="w-full bg-brand-neon text-brand-black font-black uppercase tracking-wider py-4 rounded-xl hover:bg-white transition flex items-center justify-center gap-2">
                            <span v-if="form.processing">Publicant...</span>
                            <span v-else>Publicar Anunci</span>
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </AppLayout>
</template>

<script setup>
import { watch } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    motorcycles: Array
});

const form = useForm({
    motorcycle_id: null,
    title: '',
    description: '',
    price: '',
    location: '',
    // Dades tècniques
    cc: '',
    power_cv: '',
    license_type: '',
    type: '',
    has_abs: false,
    extras: ''
});

// AQUESTA ÉS LA MÀGIA: Quan l'usuari tria la moto, copiem les seves dades al formulari
watch(() => form.motorcycle_id, (newId) => {
    if (newId) {
        const moto = props.motorcycles.find(m => m.id === newId);
        if (moto) {
            form.cc = moto.cc || '';
            form.power_cv = moto.power_cv || '';
            form.license_type = moto.license_type || '';
            form.type = moto.type || '';
            form.has_abs = Boolean(moto.has_abs);
            form.extras = moto.extras || '';
            
            // Suggerim un títol automàtic si està buit
            if (!form.title) {
                form.title = `${moto.brand} ${moto.model} (${moto.year})`;
            }
        }
    }
});

const submit = () => {
    form.post(route('sales.store'));
};
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>
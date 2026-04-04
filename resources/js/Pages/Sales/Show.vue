<template>
    <AppLayout :title="sale.title">
        <div class="px-4 py-6 pb-32 max-w-3xl mx-auto">
            
            <div class="flex items-center justify-between mb-4">
                <Link :href="route('sales.index')" class="text-gray-400 text-sm hover:text-white flex items-center gap-1 transition">
                    &larr; {{ $t('sales.back_to_market') }}
                </Link>
                <div class="flex items-center gap-2">
                    <div v-if="sale.is_sold" class="text-[10px] font-bold uppercase px-2 py-1 rounded bg-red-500/20 text-red-400 border border-red-500/30">{{ $t('sales.sold_badge') }}</div>
                    <div v-else-if="!sale.is_active" class="text-[10px] font-bold uppercase px-2 py-1 rounded bg-gray-700 text-gray-300">Oculta</div>
                    <div v-else class="text-[10px] font-bold uppercase px-2 py-1 rounded bg-brand-neon/10 text-brand-neon border border-brand-neon/20">{{ $t('sales.state_active') }}</div>
                </div>
            </div>

            <!-- Galeria de fotos -->
            <div v-if="sale.images && sale.images.length > 0" class="mb-6">
                <!-- Foto principal gran -->
                <div class="relative h-64 sm:h-80 rounded-2xl overflow-hidden border border-brand-dark shadow-2xl mb-2 cursor-pointer" @click="activePhoto = selectedPhoto">
                    <img :src="$page.props.storageUrl + '/' + currentPhoto" class="w-full h-full object-cover transition duration-500" alt="Foto principal">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                    <span class="absolute bottom-3 right-3 bg-black/60 text-white text-xs px-2 py-1 rounded-full backdrop-blur-sm">{{ selectedPhoto + 1 }} / {{ sale.images.length }}</span>
                </div>
                <!-- Miniatures -->
                <div v-if="sale.images.length > 1" class="flex gap-2 overflow-x-auto pb-1">
                    <button 
                        v-for="(img, i) in sale.images" :key="img.id"
                        @click="selectedPhoto = i"
                        class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition"
                        :class="selectedPhoto === i ? 'border-brand-neon' : 'border-transparent opacity-60 hover:opacity-100'"
                    >
                        <img :src="$page.props.storageUrl + '/' + img.image_path" class="w-full h-full object-cover">
                    </button>
                </div>
            </div>
            <!-- Placeholder si no hi ha fotos -->
            <div v-else class="w-full h-56 bg-gray-900 rounded-2xl overflow-hidden relative mb-6 border border-brand-dark shadow-2xl flex items-center justify-center">
                <div class="absolute inset-0 opacity-30 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
                <h2 class="relative z-10 text-5xl font-black text-white/20 uppercase tracking-widest">{{ sale.motorcycle?.brand }}</h2>
            </div>

            <!-- Títol i preu -->
            <div class="mb-6">
                <h1 class="text-3xl font-black text-white uppercase tracking-tighter leading-tight">{{ sale.title }}</h1>
                <div class="flex items-center justify-between mt-2 flex-wrap gap-2">
                    <p class="text-3xl font-mono font-bold text-brand-neon">{{ parseFloat(sale.price).toLocaleString('ca-ES') }} €</p>
                    
                    <div class="flex gap-4 items-center">
                        <p class="text-sm text-gray-400 font-bold uppercase flex items-center gap-1">📍 {{ sale.location }}</p>
                        <!-- Favorits a la dreta del preu -->
                        <Link 
                            v-if="!isOwner"
                            :href="route('sales.toggle-favorite', sale.id)" 
                            method="post" 
                            as="button" 
                            preserve-scroll
                            class="p-2 rounded-full border shadow-lg transition-transform hover:scale-110 flex items-center justify-center gap-2 text-sm font-bold uppercase"
                            :class="sale.is_favorited ? 'bg-red-500/10 border-red-500/50 text-red-500' : 'bg-brand-surface border-brand-dark text-gray-400 hover:text-white'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" :fill="sale.is_favorited ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                            <span v-if="sale.is_favorited">{{ $t('sales.saved_fav') }}</span>
                        </Link>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2 flex items-center gap-3">
                    <span>Venedor: <span class="text-white">{{ sale.motorcycle?.user?.name || 'Rider' }}</span></span>
                    <span>• Publicat el {{ new Date(sale.created_at).toLocaleDateString('ca-ES') }}</span>
                    <span>• 👁️ {{ sale.views_count || 0 }} {{ $t('sales.views') }}</span>
                    <span v-if="sale.favorited_by_count > 0">• ❤️ {{ sale.favorited_by_count }} {{ $t('sales.saved_count') }}</span>
                </p>
            </div>

            <!-- Fitxa tècnica -->
            <div class="bg-brand-surface border border-brand-dark rounded-xl p-5 shadow-lg mb-6 relative overflow-hidden">
                <div class="absolute -top-4 -right-4 text-brand-neon/5 pointer-events-none select-none text-[8rem] font-black">🏍</div>
                <h3 class="text-sm font-black text-brand-neon uppercase tracking-widest mb-4 border-b border-brand-dark pb-2">{{ $t('sales.technical_specs') }}</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-y-5 gap-x-4">
                    <div><p class="text-[10px] text-gray-500 uppercase font-bold">Marca</p><p class="text-sm text-white font-medium">{{ sale.motorcycle?.brand }}</p></div>
                    <div><p class="text-[10px] text-gray-500 uppercase font-bold">Model</p><p class="text-sm text-white font-medium">{{ sale.motorcycle?.model }}</p></div>
                    <div><p class="text-[10px] text-gray-500 uppercase font-bold">Any</p><p class="text-sm text-white font-medium">{{ sale.motorcycle?.year }}</p></div>
                    <div><p class="text-[10px] text-brand-neon uppercase font-bold">{{ $t('nav.km') }}</p><p class="text-sm text-brand-neon font-bold font-mono">{{ parseFloat(sale.motorcycle?.current_km || 0).toLocaleString('ca-ES') }}</p></div>
                    <div><p class="text-[10px] text-gray-500 uppercase font-bold">Cilindrada</p><p class="text-sm text-white font-medium">{{ sale.motorcycle?.cc ? sale.motorcycle.cc + ' cc' : '-' }}</p></div>
                    <div><p class="text-[10px] text-gray-500 uppercase font-bold">Potència</p><p class="text-sm text-white font-medium">{{ sale.motorcycle?.power_cv ? sale.motorcycle.power_cv + ' CV' : '-' }}</p></div>
                    <div><p class="text-[10px] text-gray-500 uppercase font-bold">Carnet</p><p class="text-sm text-white font-medium">{{ sale.motorcycle?.license_type || '-' }}</p></div>
                    <div><p class="text-[10px] text-gray-500 uppercase font-bold">Estil</p><p class="text-sm text-white font-medium">{{ sale.motorcycle?.type || '-' }}</p></div>
                </div>
            </div>

            <!-- Descripció -->
            <div class="mb-6">
                <h3 class="text-sm font-black text-brand-neon uppercase tracking-widest mb-3">{{ $t('sales.seller_comments') }}</h3>
                <div class="bg-brand-black border border-brand-dark rounded-xl p-5 text-gray-300 text-sm whitespace-pre-line shadow-inner leading-relaxed">
                    {{ sale.description || $t('sales.no_seller_desc') }}
                </div>
            </div>

            <!-- Extres -->
            <div v-if="sale.motorcycle?.extras" class="mb-6">
                <h3 class="text-sm font-black text-brand-neon uppercase tracking-widest mb-3">{{ $t('sales.equipped_extras') }}</h3>
                <div class="bg-brand-surface border border-brand-dark rounded-xl p-4 text-gray-300 text-sm whitespace-pre-line shadow-lg">
                    ✨ {{ sale.motorcycle.extras }}
                </div>
            </div>

            <!-- Botons propietari (si és el teu anunci) -->
            <div v-if="isOwner" class="mb-6 flex gap-3">
                <Link :href="route('sales.edit', sale.id)" class="flex-1 bg-brand-dark hover:bg-gray-700 border border-gray-600 text-white font-bold uppercase tracking-wider text-xs py-3 rounded-xl text-center transition">
                    {{ $t('sales.edit_listing_btn') }}
                </Link>
                <Link 
                    v-if="!sale.is_sold"
                    :href="route('sales.mark-sold', sale.id)"
                    method="patch" as="button"
                    class="flex-1 bg-red-900/30 border border-red-700/30 text-red-400 hover:bg-red-900/60 font-bold uppercase tracking-wider text-xs py-3 rounded-xl text-center transition"
                >
                    {{ $t('sales.mark_sold_btn') }}
                </Link>
            </div>

        </div>

        <!-- Barra de contacte fixa (si no és el teu anunci i no està venut) -->
        <div v-if="!isOwner && !sale.is_sold" class="fixed bottom-16 left-0 w-full p-4 bg-brand-black/90 backdrop-blur-xl border-t border-brand-dark z-50">
            <div class="max-w-3xl mx-auto">
                <Link 
                    :href="route('chats.start')"
                    method="post"
                    as="button"
                    :data="{ other_user_id: sale.motorcycle?.user_id, motorcycle_id: sale.motorcycle_id }"
                    class="w-full bg-brand-neon text-brand-black font-black uppercase tracking-wider py-4 rounded-xl shadow-[0_0_20px_rgba(12,225,181,0.4)] hover:scale-[1.02] transition text-center flex items-center justify-center gap-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" /></svg>
                    {{ $t('sales.chat_with_seller') }}
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ sale: Object });

const page = usePage();
const isOwner = computed(() => props.sale.motorcycle?.user_id === page.props.auth.user.id);

const selectedPhoto = ref(0);
const currentPhoto = computed(() => props.sale.images?.[selectedPhoto.value]?.image_path || '');
</script>

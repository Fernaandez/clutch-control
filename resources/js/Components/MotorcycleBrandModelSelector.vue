<template>
    <div class="space-y-4">
        <!-- MARCA -->
        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('motorcycles.brand') }}</label>
            <div class="relative">
                <input
                    v-model="brandQuery"
                    @input="onBrandInput"
                    @focus="showBrandDropdown = true"
                    @blur="handleBrandBlur"
                    type="text"
                    :placeholder="$t('motorcycles.brand_placeholder')"
                    :class="inputClass(brandError)"
                    autocomplete="off"
                />
                <!-- Dropdown de marques -->
                <div v-if="showBrandDropdown && filteredBrands.length > 0" class="absolute z-50 w-full mt-1 bg-brand-black border border-brand-neon/30 rounded-lg shadow-[0_0_20px_rgba(12,225,181,0.1)] max-h-48 overflow-y-auto">
                    <button
                        v-for="brand in filteredBrands"
                        :key="brand.id"
                        @mousedown.prevent="selectBrand(brand)"
                        type="button"
                        class="w-full text-left px-4 py-2.5 text-sm text-gray-200 hover:bg-brand-neon/10 hover:text-brand-neon transition flex items-center justify-between"
                    >
                        <span>{{ brand.name }}</span>
                        <span class="text-xs text-gray-600">{{ brand.country }}</span>
                    </button>
                </div>
                <!-- Indicador de carrega -->
                <div v-if="loadingBrands" class="absolute right-3 top-1/2 -translate-y-1/2">
                    <svg class="animate-spin w-4 h-4 text-brand-neon" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </div>
            </div>
            <p v-if="brandError" class="text-red-400 text-xs mt-1 flex items-center gap-1"><span>⚠</span> {{ brandError }}</p>
        </div>

        <!-- MODEL -->
        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ $t('motorcycles.model') }}</label>
            <div class="relative">
                <input
                    v-model="modelQuery"
                    @input="onModelInput"
                    @focus="showModelDropdown = true"
                    @blur="handleModelBlur"
                    type="text"
                    :placeholder="selectedBrand ? $t('motorcycles.model_placeholder') : $t('motorcycles.model_placeholder_brand_first')"
                    :disabled="!brandQuery"
                    :class="inputClass(modelError)"
                    autocomplete="off"
                />
                <!-- Dropdown de models -->
                <div v-if="showModelDropdown && filteredModels.length > 0" class="absolute z-50 w-full mt-1 bg-brand-black border border-brand-neon/30 rounded-lg shadow-[0_0_20px_rgba(12,225,181,0.1)] max-h-48 overflow-y-auto">
                    <button
                        v-for="model in filteredModels"
                        :key="model.id"
                        @mousedown.prevent="selectModel(model)"
                        type="button"
                        class="w-full text-left px-4 py-2.5 text-sm text-gray-200 hover:bg-brand-neon/10 hover:text-brand-neon transition flex items-center justify-between"
                    >
                        <span>{{ model.name }}</span>
                        <span v-if="model.cc" class="text-xs text-gray-600">{{ model.cc }} cc / {{ model.power_cv }} cv</span>
                    </button>
                </div>
                <!-- Spinner -->
                <div v-if="loadingModels" class="absolute right-3 top-1/2 -translate-y-1/2">
                    <svg class="animate-spin w-4 h-4 text-brand-neon" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </div>
            </div>
            <p v-if="modelError" class="text-red-400 text-xs mt-1 flex items-center gap-1"><span>⚠</span> {{ modelError }}</p>
        </div>

        <!-- NOTA: model personalitzat -->
        <p v-if="isCustomModel" class="text-xs text-brand-neon/70 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>
            Model personalitzat — es guardarà per a futurs usuaris
        </p>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';

const props = defineProps({
    initialBrand: { type: String, default: '' },
    initialModel: { type: String, default: '' },
    brandError: { type: String, default: '' },
    modelError: { type: String, default: '' },
});

const emit = defineEmits(['update:brand', 'update:model', 'update:cc', 'update:cv']);

const inputClass = (error) =>
    error
        ? 'w-full rounded bg-brand-black border-red-500 ring-1 ring-red-500 text-white focus:border-red-400 focus:ring-red-400'
        : 'w-full rounded bg-brand-black border-brand-dark text-white focus:border-brand-neon focus:ring-0';

// ─── State ────────────────────────────────────────────────────────────────────
const allBrands     = ref([]);
const allModels     = ref([]);
const brandQuery    = ref(props.initialBrand || '');
const modelQuery    = ref(props.initialModel || '');
const selectedBrand = ref(null);
const selectedModel = ref(null);
const showBrandDropdown = ref(false);
const showModelDropdown = ref(false);
const loadingBrands = ref(false);
const loadingModels = ref(false);
const isCustomModel = ref(false);

// ─── Computed filtres ─────────────────────────────────────────────────────────
const filteredBrands = computed(() => {
    if (!brandQuery.value) return allBrands.value.slice(0, 20);
    const q = brandQuery.value.toLowerCase();
    return allBrands.value.filter(b => b.name.toLowerCase().includes(q)).slice(0, 20);
});

const filteredModels = computed(() => {
    if (!modelQuery.value) return allModels.value.slice(0, 30);
    const q = modelQuery.value.toLowerCase();
    return allModels.value.filter(m => m.name.toLowerCase().includes(q)).slice(0, 30);
});

// ─── Càrrega inicial ──────────────────────────────────────────────────────────
onMounted(async () => {
    loadingBrands.value = true;
    try {
        const res = await fetch('/api/motorcycle-brands');
        allBrands.value = await res.json();
    } catch (e) { console.error(e); }
    finally { loadingBrands.value = false; }

    // Si tenim valors inicials (en mode Edit), carreguem els models de la marca
    if (props.initialBrand) {
        const found = allBrands.value.find(b => b.name.toLowerCase() === props.initialBrand.toLowerCase());
        if (found) {
            selectedBrand.value = found;
            await loadModels(found.id);
        }
    }
});

// ─── Handlers Marca ───────────────────────────────────────────────────────────
const onBrandInput = () => {
    selectedBrand.value = null;
    selectedModel.value = null;
    modelQuery.value = '';
    allModels.value = [];
    isCustomModel.value = false;
    emit('update:brand', brandQuery.value);
    emit('update:model', '');
    showBrandDropdown.value = true;
};

const selectBrand = async (brand) => {
    selectedBrand.value = brand;
    brandQuery.value = brand.name;
    showBrandDropdown.value = false;
    modelQuery.value = '';
    selectedModel.value = null;
    isCustomModel.value = false;
    emit('update:brand', brand.name);
    emit('update:model', '');
    await loadModels(brand.id);
};

const handleBrandBlur = () => {
    setTimeout(() => {
        showBrandDropdown.value = false;
        // Si l'usuari ha escrit una marca que no existeix, l'acceptem igualment
        if (brandQuery.value && !selectedBrand.value) {
            emit('update:brand', brandQuery.value);
        }
    }, 150);
};

const loadModels = async (brandId) => {
    loadingModels.value = true;
    try {
        const res = await fetch(`/api/motorcycle-models?brand_id=${brandId}`);
        allModels.value = await res.json();
    } catch (e) { console.error(e); }
    finally { loadingModels.value = false; }
};

// ─── Handlers Model ───────────────────────────────────────────────────────────
const onModelInput = () => {
    selectedModel.value = null;
    isCustomModel.value = false;
    emit('update:model', modelQuery.value);
    showModelDropdown.value = true;
};

const selectModel = (model) => {
    selectedModel.value = model;
    modelQuery.value = model.name;
    showModelDropdown.value = false;
    isCustomModel.value = false;
    emit('update:model', model.name);
    if (model.cc)        emit('update:cc', model.cc);
    if (model.power_cv)  emit('update:cv', model.power_cv);
};

const handleModelBlur = () => {
    setTimeout(() => {
        showModelDropdown.value = false;
        if (modelQuery.value && !selectedModel.value) {
            // Model personalitzat — avisem i guardem quan s'enviï el formulari
            isCustomModel.value = true;
            emit('update:model', modelQuery.value);
            // Intentem guardar el model personalitzat a la base de dades
            if (brandQuery.value) {
                fetch('/api/motorcycle-brands/save-custom', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
                    body: JSON.stringify({ brand: brandQuery.value, model: modelQuery.value }),
                }).catch(e => console.warn('No s\'ha pogut guardar el model personalitzat:', e));
            }
        }
    }, 150);
};

// ─── Sync valors externs (mode Edit) ─────────────────────────────────────────
watch(() => props.initialBrand, (val) => { if (val && !brandQuery.value) brandQuery.value = val; });
watch(() => props.initialModel, (val) => { if (val && !modelQuery.value) modelQuery.value = val; });
</script>

<template>
    <svg
        v-if="path"
        :viewBox="`0 0 ${SIZE} ${SIZE}`"
        fill="none"
        class="w-full h-full"
        aria-hidden="true"
    >
        <polyline
            :points="path"
            stroke="currentColor"
            :stroke-width="strokeWidth"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
        <circle :cx="start[0]" :cy="start[1]" :r="strokeWidth * 1.4" fill="currentColor" />
    </svg>

    <svg v-else :viewBox="`0 0 ${SIZE} ${SIZE}`" fill="none" class="w-full h-full opacity-40" aria-hidden="true">
        <path d="M6 34 C 14 12, 26 40, 34 14" stroke="currentColor" :stroke-width="strokeWidth" stroke-linecap="round" stroke-dasharray="3 4" />
    </svg>
</template>

<script setup>
import { computed } from 'vue';

/**
 * La forma real del traçat, dibuixada com a línia.
 * No és un mapa: no carrega tiles ni xarxa, i llegeix millor en una llista.
 */
const props = defineProps({
    geoJson: { type: [String, Array, Object], default: null },
    strokeWidth: { type: Number, default: 1.6 },
});

const SIZE = 40;
const PAD = 5;

const points = computed(() => {
    let raw = props.geoJson;

    if (typeof raw === 'string') {
        try {
            raw = JSON.parse(raw);
            if (typeof raw === 'string') raw = JSON.parse(raw);
        } catch {
            return [];
        }
    }

    if (raw && !Array.isArray(raw)) {
        raw = raw.geometry?.coordinates ?? raw.coordinates ?? raw.features?.[0]?.geometry?.coordinates ?? [];
    }

    if (!Array.isArray(raw)) return [];

    return raw
        .map((p) => {
            if (Array.isArray(p) && p.length >= 2) return [Number(p[0]), Number(p[1])];
            if (p && (p.lng !== undefined || p.longitude !== undefined)) {
                return [Number(p.lng ?? p.longitude), Number(p.lat ?? p.latitude)];
            }
            return null;
        })
        .filter((p) => p && Number.isFinite(p[0]) && Number.isFinite(p[1]));
});

/** Coordenades normalitzades al quadre, mantenint la proporció del traçat. */
const projected = computed(() => {
    const list = points.value;
    if (list.length < 2) return [];

    const lngs = list.map((p) => p[0]);
    const lats = list.map((p) => p[1]);
    const minLng = Math.min(...lngs);
    const minLat = Math.min(...lats);
    const spanLng = Math.max(...lngs) - minLng || 1;
    const spanLat = Math.max(...lats) - minLat || 1;
    const span = Math.max(spanLng, spanLat);
    const inner = SIZE - PAD * 2;

    const offsetX = (inner - (spanLng / span) * inner) / 2;
    const offsetY = (inner - (spanLat / span) * inner) / 2;

    // Cada 3 punts n'hi ha prou per llegir la forma i manté l'SVG petit.
    const step = Math.max(1, Math.floor(list.length / 60));

    return list
        .filter((_, i) => i % step === 0 || i === list.length - 1)
        .map(([lng, lat]) => [
            +(PAD + offsetX + ((lng - minLng) / span) * inner).toFixed(2),
            +(SIZE - PAD - offsetY - ((lat - minLat) / span) * inner).toFixed(2),
        ]);
});

const path = computed(() =>
    projected.value.length ? projected.value.map((p) => p.join(',')).join(' ') : null,
);

const start = computed(() => projected.value[0] ?? [0, 0]);
</script>

<template>
    <div :id="mapId" class="w-full h-full bg-gray-900 pointer-events-none z-0 relative"></div>
</template>

<script setup>
import { onMounted } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({
    routeId: Number,
    geoJson: [String, Array, Object]
});

const mapId = `minimap-${props.routeId}`;

onMounted(() => {
    if (!props.geoJson) return;

    // Lògica de parseig robusta
    let points = props.geoJson;
    if (typeof points === 'string') {
        try {
            points = JSON.parse(points);
            if (typeof points === 'string') points = JSON.parse(points);
        } catch (e) { return; }
    }

    if (!Array.isArray(points) || points.length === 0) return;

    // Crear mapa
    const map = L.map(mapId, {
        zoomControl: false,
        dragging: false,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        boxZoom: false,
        attributionControl: false
    });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);

    const latlngs = points.map(p => [p.lat, p.lng]);
    const polyline = L.polyline(latlngs, {
        color: '#0CE1B5',
        weight: 3,
        opacity: 1
    }).addTo(map);

    map.fitBounds(polyline.getBounds(), { padding: [20, 20] });
});
</script>
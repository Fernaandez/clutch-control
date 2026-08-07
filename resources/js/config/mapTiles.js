/**
 * Map tile providers for Leaflet.
 *
 * Stadia Alidade Smooth Dark requires VITE_STADIA_API_KEY.
 * Without it we use Carto Dark Matter (free, works in local and prod).
 */

const CARTO_DARK = {
    url: 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
    options: {
        subdomains: 'abcd',
        maxZoom: 20,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
    },
};

const STADIA_DARK = {
    url: 'https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png',
    options: {
        maxZoom: 20,
        attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a> &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a>',
    },
};

export function getMapTileProvider() {
    const apiKey = import.meta.env.VITE_STADIA_API_KEY;
    // Sense clau, Stadia falla en molts entorns. Fallback gratis: Carto.
    if (apiKey) return 'stadia';
    return 'carto';
}

export function getMapTileConfig(extraOptions = {}) {
    const provider = getMapTileProvider();

    if (provider === 'stadia') {
        const apiKey = import.meta.env.VITE_STADIA_API_KEY;
        const url = apiKey
            ? `${STADIA_DARK.url}?api_key=${encodeURIComponent(apiKey)}`
            : STADIA_DARK.url;

        return {
            url,
            options: { ...STADIA_DARK.options, ...extraOptions },
        };
    }

    return {
        url: CARTO_DARK.url,
        options: { ...CARTO_DARK.options, ...extraOptions },
    };
}

/** Add the configured tile layer to a Leaflet map instance. */
export function addMapTileLayer(map, L, extraOptions = {}) {
    const { url, options } = getMapTileConfig(extraOptions);
    return L.tileLayer(url, options).addTo(map);
}

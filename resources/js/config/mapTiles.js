/**
 * Map tile providers for Leaflet.
 *
 * Stadia Alidade Smooth Dark (Waze-like) requires an API key in production.
 * Without it, tiles return 404. Carto Dark Matter is the free fallback (OSM data,
 * secondary roads included).
 *
 * Set VITE_STADIA_API_KEY in .env / Forge for the smooth dark style in production.
 * Free tier: https://stadiamaps.com (no credit card).
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

function isLocalDev() {
    if (typeof window === 'undefined') return false;
    const host = window.location.hostname;
    return host === 'localhost' || host === '127.0.0.1';
}

export function getMapTileProvider() {
    const apiKey = import.meta.env.VITE_STADIA_API_KEY;
    if (apiKey) return 'stadia';
    if (isLocalDev()) return 'stadia';
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

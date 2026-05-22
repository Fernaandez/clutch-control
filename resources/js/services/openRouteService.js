const ORS_URL = 'https://api.openrouteservice.org/v2/directions/driving-car/geojson';

/** ORS alternative_routes only works when routed distance is under ~100 km */
const ALTERNATIVES_MAX_STRAIGHT_KM = 75;

export function getOrsApiKey() {
    return import.meta.env.VITE_ORS_API_KEY || '';
}

export function hasOrsApiKey() {
    return Boolean(getOrsApiKey());
}

export function haversineKm(a, b) {
    const R = 6371;
    const dLat = ((b.lat - a.lat) * Math.PI) / 180;
    const dLng = ((b.lng - a.lng) * Math.PI) / 180;
    const lat1 = (a.lat * Math.PI) / 180;
    const lat2 = (b.lat * Math.PI) / 180;
    const x = Math.sin(dLat / 2) ** 2
        + Math.cos(lat1) * Math.cos(lat2) * Math.sin(dLng / 2) ** 2;

    return R * 2 * Math.atan2(Math.sqrt(x), Math.sqrt(1 - x));
}

export function buildRequestOptions({ highway, roadStyle, variant = 'primary' }) {
    const avoid_features = [];

    if (highway === 'avoid') {
        avoid_features.push('highways');
    }

    if (roadStyle === 'scenic') {
        if (!avoid_features.includes('highways')) {
            avoid_features.push('highways');
        }
        avoid_features.push('tollways');
    }

    let preference = 'recommended';
    if (roadStyle === 'fast') {
        preference = 'fastest';
    } else if (roadStyle === 'scenic') {
        preference = variant === 'secondary' ? 'shortest' : 'recommended';
    } else if (roadStyle === 'balanced' && variant === 'secondary') {
        preference = 'shortest';
    }

    const options = {};

    if (avoid_features.length) {
        options.avoid_features = [...new Set(avoid_features)];
    }

    if (roadStyle === 'scenic') {
        options.weightings = { green: 1, quiet: 1 };
    }

    return {
        preference,
        options: Object.keys(options).length ? options : undefined,
        tag: { highway, roadStyle },
    };
}

export async function fetchDirections({
    coordinates,
    highway,
    roadStyle,
    variant = 'primary',
    useAlternatives = false,
    alternativeCount = 2,
}) {
    const apiKey = getOrsApiKey();
    if (!apiKey) {
        throw new Error('ORS_API_KEY_MISSING');
    }

    const { preference, options } = buildRequestOptions({ highway, roadStyle, variant });

    const body = {
        coordinates,
        preference,
    };

    if (options) {
        body.options = options;
    }

    if (useAlternatives) {
        body.alternative_routes = {
            target_count: Math.min(alternativeCount, 2),
            share_factor: 0.45,
            weight_factor: 1.6,
        };
    }

    const response = await fetch(ORS_URL, {
        method: 'POST',
        headers: {
            Authorization: apiKey,
            'Content-Type': 'application/json',
            Accept: 'application/geo+json;charset=UTF-8',
        },
        body: JSON.stringify(body),
    });

    if (!response.ok) {
        let message = `HTTP ${response.status}`;
        try {
            const payload = await response.json();
            message = payload?.error?.message || message;
        } catch {
            // ignore parse errors
        }
        throw new Error(message);
    }

    return response.json();
}

export function parseProposals(geojson, { origin, destination, labelPrefix, tag }) {
    const features = geojson?.features || [];

    return features.map((feature, index) => {
        const summary = feature.properties?.summary
            || feature.properties?.segments?.[0]?.summary
            || {};
        const coords = feature.geometry?.coordinates || [];
        const latLngs = coords.map(([lng, lat]) => ({ lat, lng }));

        return {
            id: `${labelPrefix}-${index}-${Math.round(summary.duration || 0)}-${Math.round(summary.distance || 0)}`,
            label: features.length > 1 ? `${labelPrefix} ${index + 1}` : labelPrefix,
            distanceKm: Math.round(((summary.distance || 0) / 1000) * 10) / 10,
            durationSeconds: Math.round(summary.duration || 0),
            latLngs,
            geoJson: JSON.stringify(latLngs),
            waypoints: [
                { lat: origin.lat, lng: origin.lng, name: origin.name || 'Origen' },
                { lat: destination.lat, lng: destination.lng, name: destination.name || 'Destí' },
            ],
            tag,
        };
    });
}

function dedupeProposals(list) {
    const seen = new Set();

    return list.filter((proposal) => {
        const key = `${Math.round(proposal.distanceKm)}_${Math.round(proposal.durationSeconds / 60)}`;
        if (seen.has(key)) {
            return false;
        }
        seen.add(key);
        return true;
    });
}

function isAlternativesLimitError(message) {
    return message.includes('100000') || message.toLowerCase().includes('alternative');
}

function wantsStrictRouting(highway, roadStyle) {
    return highway === 'avoid' || roadStyle !== 'fast';
}

export async function fetchRouteProposals({
    origin,
    destination,
    highway,
    roadStyle,
    labelPrefix = 'Ruta',
}) {
    const coordinates = [
        [origin.lng, origin.lat],
        [destination.lng, destination.lat],
    ];

    const straightKm = haversineKm(origin, destination);
    const tag = buildRequestOptions({ highway, roadStyle }).tag;
    const strict = wantsStrictRouting(highway, roadStyle);
    let proposals = [];

    if (!strict && straightKm < ALTERNATIVES_MAX_STRAIGHT_KM) {
        try {
            const geojson = await fetchDirections({
                coordinates,
                highway,
                roadStyle,
                useAlternatives: true,
                alternativeCount: 2,
            });
            proposals = parseProposals(geojson, { origin, destination, labelPrefix, tag });
        } catch (err) {
            if (!isAlternativesLimitError(err.message)) {
                throw err;
            }
        }
    }

    if (!proposals.length) {
        const geojson = await fetchDirections({
            coordinates,
            highway,
            roadStyle,
            variant: 'primary',
            useAlternatives: false,
        });
        proposals = parseProposals(geojson, { origin, destination, labelPrefix, tag });
    }

    if (strict && straightKm < ALTERNATIVES_MAX_STRAIGHT_KM && proposals.length < 2) {
        try {
            const altGeo = await fetchDirections({
                coordinates,
                highway,
                roadStyle,
                variant: 'secondary',
                useAlternatives: false,
            });
            const altLabel = `${labelPrefix} B`;
            proposals = [
                ...proposals,
                ...parseProposals(altGeo, {
                    origin,
                    destination,
                    labelPrefix: altLabel,
                    tag,
                }),
            ];
        } catch {
            // optional second variant
        }
    }

    proposals = dedupeProposals(proposals).slice(0, 4);
    proposals.sort((a, b) => a.durationSeconds - b.durationSeconds);

    return { proposals, straightKm, usedAlternatives: !strict && proposals.length > 1 };
}

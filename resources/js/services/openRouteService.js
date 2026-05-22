const ORS_URL = 'https://api.openrouteservice.org/v2/directions/driving-car/geojson';

export function getOrsApiKey() {
    return import.meta.env.VITE_ORS_API_KEY || '';
}

export function hasOrsApiKey() {
    return Boolean(getOrsApiKey());
}

function buildRequestOptions({ highway, roadStyle }) {
    const avoid_features = [];

    if (highway === 'avoid' || roadStyle !== 'fast') {
        avoid_features.push('highways');
    }

    if (roadStyle === 'scenic') {
        if (!avoid_features.includes('tollways')) {
            avoid_features.push('tollways');
        }
    }

    let preference = 'recommended';
    if (roadStyle === 'fast' && highway === 'allow') {
        preference = 'fastest';
    }

    return {
        preference,
        options: avoid_features.length ? { avoid_features } : undefined,
    };
}

export async function fetchDirections({ coordinates, highway, roadStyle, alternativeCount = 2 }) {
    const apiKey = getOrsApiKey();
    if (!apiKey) {
        throw new Error('ORS_API_KEY_MISSING');
    }

    const { preference, options } = buildRequestOptions({ highway, roadStyle });

    const body = {
        coordinates,
        preference,
        alternative_routes: {
            target_count: Math.min(alternativeCount, 2),
            share_factor: 0.55,
            weight_factor: 1.35,
        },
    };

    if (options) {
        body.options = options;
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

export function parseProposals(geojson, { origin, destination, labelPrefix }) {
    const features = geojson?.features || [];

    return features.map((feature, index) => {
        const summary = feature.properties?.summary
            || feature.properties?.segments?.[0]?.summary
            || {};
        const coords = feature.geometry?.coordinates || [];
        const latLngs = coords.map(([lng, lat]) => ({ lat, lng }));

        return {
            id: `${labelPrefix}-${index}-${Math.round(summary.duration || 0)}`,
            label: `${labelPrefix} ${index + 1}`,
            distanceKm: Math.round(((summary.distance || 0) / 1000) * 10) / 10,
            durationSeconds: Math.round(summary.duration || 0),
            latLngs,
            geoJson: JSON.stringify(latLngs),
            waypoints: [
                { lat: origin.lat, lng: origin.lng, name: origin.name || 'Origen' },
                { lat: destination.lat, lng: destination.lng, name: destination.name || 'Destí' },
            ],
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

export async function fetchRouteProposals({
    origin,
    destination,
    highway,
    roadStyle,
    p2pMode,
    targetDurationSeconds,
    labelPrefix = 'Ruta',
}) {
    const coordinates = [
        [origin.lng, origin.lat],
        [destination.lng, destination.lat],
    ];

    const geojson = await fetchDirections({
        coordinates,
        highway,
        roadStyle,
        alternativeCount: 2,
    });

    let proposals = parseProposals(geojson, { origin, destination, labelPrefix });

    if (proposals.length < 3 && roadStyle === 'fast' && highway === 'allow') {
        try {
            const scenicGeo = await fetchDirections({
                coordinates,
                highway: 'avoid',
                roadStyle: 'balanced',
                alternativeCount: 1,
            });
            proposals = [
                ...proposals,
                ...parseProposals(scenicGeo, { origin, destination, labelPrefix: `${labelPrefix} alt` }),
            ];
        } catch {
            // optional enrichment
        }
    }

    proposals = dedupeProposals(proposals).slice(0, 4);

    if (p2pMode === 'time_fit' && targetDurationSeconds) {
        proposals.sort(
            (a, b) => Math.abs(a.durationSeconds - targetDurationSeconds)
                - Math.abs(b.durationSeconds - targetDurationSeconds),
        );
    } else {
        proposals.sort((a, b) => a.durationSeconds - b.durationSeconds);
    }

    return proposals;
}

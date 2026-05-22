const ORS_URL = 'https://api.openrouteservice.org/v2/directions/driving-car/geojson';

/** ORS alternative_routes only works when routed distance is under ~100 km */
const ALTERNATIVES_MAX_STRAIGHT_KM = 75;

/** ORS waycategory bit flag for motorway / motorway_link only */
const WAYCATEGORY_HIGHWAY = 1;

/** ORS waytype: state road (motorway, trunk, primary, …) */
const WAYTYPE_STATE_ROAD = 1;

/** Matches Spanish autopistes/autovies in turn-by-turn names (A-2, AP-7, N-II, …) */
const FAST_ROAD_NAME = /\b(AP|A|N|E|C)-?\d+\b|\bautopista\b|\bautov[ií]a\b|\bvia\s+(de\s+)?alta\s+capacitat\b/i;

const MAX_FAST_ROAD_AVOID_ATTEMPTS = 3;
const AVOID_POLYGON_BUFFER_DEG = 0.0035;

const LOOP_MIN_LENGTH_M = 5000;
const LOOP_MAX_LENGTH_M = 400000;
const LOOP_DURATION_TOLERANCE = 0.18;

const LOOP_VARIANTS = [
    { seed: 11, points: 3 },
    { seed: 22, points: 4 },
    { seed: 33, points: 5 },
];

const LOOP_SPEED_KMH = {
    fast: 65,
    balanced: 50,
    scenic: 42,
};

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
    if (highway === 'avoid') {
        // ORS "highways" only blocks motorway/motorway_link; prefer smaller roads for autovies too.
        preference = variant === 'secondary' ? 'shortest' : 'recommended';
    } else if (roadStyle === 'fast') {
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

    return {
        preference,
        options: Object.keys(options).length ? options : undefined,
        tag: { highway, roadStyle },
    };
}

function segmentBBoxPolygon(coordinates, startIdx, endIdx, bufferDeg = AVOID_POLYGON_BUFFER_DEG) {
    const from = Math.max(0, Math.min(startIdx, endIdx));
    const to = Math.min(coordinates.length - 1, Math.max(startIdx, endIdx));
    const slice = coordinates.slice(from, to + 1);
    if (!slice.length) {
        return null;
    }

    const lngs = slice.map(([lng]) => lng);
    const lats = slice.map(([, lat]) => lat);
    const minLng = Math.min(...lngs) - bufferDeg;
    const maxLng = Math.max(...lngs) + bufferDeg;
    const minLat = Math.min(...lats) - bufferDeg;
    const maxLat = Math.max(...lats) + bufferDeg;

    return {
        type: 'Polygon',
        coordinates: [[
            [minLng, minLat],
            [maxLng, minLat],
            [maxLng, maxLat],
            [minLng, maxLat],
            [minLng, minLat],
        ]],
    };
}

function polygonKey(polygon) {
    const ring = polygon?.coordinates?.[0] || [];
    const lngs = ring.map(([lng]) => lng);
    const lats = ring.map(([, lat]) => lat);
    return `${Math.round(Math.min(...lngs) * 1000)}_${Math.round(Math.max(...lngs) * 1000)}_${Math.round(Math.min(...lats) * 1000)}_${Math.round(Math.max(...lats) * 1000)}`;
}

function mergeAvoidPolygons(polygons) {
    const unique = [];
    const seen = new Set();

    for (const polygon of polygons) {
        if (!polygon) continue;
        const key = polygonKey(polygon);
        if (seen.has(key)) continue;
        seen.add(key);
        unique.push(polygon);
    }

    if (!unique.length) {
        return undefined;
    }

    if (unique.length === 1) {
        return unique[0];
    }

    return {
        type: 'MultiPolygon',
        coordinates: unique.map((polygon) => polygon.coordinates),
    };
}

function findFastRoadViolations(geojson) {
    const feature = geojson?.features?.[0];
    if (!feature) {
        return [];
    }

    const props = feature.properties || {};
    const coordinates = feature.geometry?.coordinates || [];
    const violations = [];
    const seen = new Set();

    const pushViolation = (start, end, bufferDeg = AVOID_POLYGON_BUFFER_DEG) => {
        const polygon = segmentBBoxPolygon(coordinates, start, end, bufferDeg);
        if (!polygon) return;
        const key = polygonKey(polygon);
        if (seen.has(key)) return;
        seen.add(key);
        violations.push({ start, end, polygon });
    };

    for (const [start, end, value] of (props.extras?.waycategory?.values || [])) {
        if ((value & WAYCATEGORY_HIGHWAY) === WAYCATEGORY_HIGHWAY) {
            pushViolation(start, end);
        }
    }

    for (const segment of (props.segments || [])) {
        for (const step of (segment.steps || [])) {
            const name = `${step.name || ''} ${step.ref || ''}`.trim();
            const wayPoints = step.way_points || [];
            const start = wayPoints[0] ?? 0;
            const end = wayPoints[1] ?? start;

            if (FAST_ROAD_NAME.test(name)) {
                pushViolation(start, end, AVOID_POLYGON_BUFFER_DEG * 1.2);
                continue;
            }

            if ((props.extras?.waytype?.values || []).some(([s, e, type]) => (
                type === WAYTYPE_STATE_ROAD
                && s <= start
                && e >= end
                && FAST_ROAD_NAME.test(name)
            ))) {
                pushViolation(start, end);
            }
        }
    }

    return violations;
}

export function estimateLoopLengthMeters(targetDurationMinutes, roadStyle) {
    const speedKmh = LOOP_SPEED_KMH[roadStyle] || LOOP_SPEED_KMH.balanced;
    const lengthM = (targetDurationMinutes / 60) * speedKmh * 1000;

    return Math.round(Math.min(LOOP_MAX_LENGTH_M, Math.max(LOOP_MIN_LENGTH_M, lengthM)));
}

function clampLoopLength(lengthM) {
    return Math.round(Math.min(LOOP_MAX_LENGTH_M, Math.max(LOOP_MIN_LENGTH_M, lengthM)));
}

export async function fetchDirections({
    coordinates,
    highway,
    roadStyle,
    variant = 'primary',
    useAlternatives = false,
    alternativeCount = 2,
    avoidPolygons = [],
    withFastRoadAnalysis = false,
    roundTrip,
}) {
    const apiKey = getOrsApiKey();
    if (!apiKey) {
        throw new Error('ORS_API_KEY_MISSING');
    }

    const { preference, options: baseOptions } = buildRequestOptions({ highway, roadStyle, variant });
    const options = { ...(baseOptions || {}) };
    const mergedAvoid = mergeAvoidPolygons(avoidPolygons);

    if (mergedAvoid) {
        options.avoid_polygons = mergedAvoid;
    }

    if (roundTrip) {
        options.round_trip = roundTrip;
    }

    const body = {
        coordinates,
        preference,
        instructions: true,
    };

    if (Object.keys(options).length) {
        body.options = options;
    }

    if (withFastRoadAnalysis || highway === 'avoid') {
        body.extra_info = ['waytype', 'waycategory'];
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

async function fetchDirectionsAvoidingFastRoads(params) {
    const { highway, roundTrip, ...rest } = params;

    if (highway !== 'avoid') {
        return fetchDirections({ ...rest, highway, roundTrip });
    }

    const avoidPolygons = [];
    let geojson = null;

    for (let attempt = 0; attempt < MAX_FAST_ROAD_AVOID_ATTEMPTS; attempt += 1) {
        geojson = await fetchDirections({
            ...rest,
            highway,
            roundTrip,
            avoidPolygons,
            withFastRoadAnalysis: true,
        });

        const violations = findFastRoadViolations(geojson);
        if (!violations.length) {
            break;
        }

        for (const violation of violations) {
            avoidPolygons.push(violation.polygon);
        }
    }

    return geojson;
}

export function parseProposals(geojson, {
    origin,
    destination,
    labelPrefix,
    tag,
    targetDurationMinutes = null,
    isLoop = false,
}) {
    const features = geojson?.features || [];
    const dest = destination || origin;

    return features.map((feature, index) => {
        const summary = feature.properties?.summary
            || feature.properties?.segments?.[0]?.summary
            || {};
        const coords = feature.geometry?.coordinates || [];
        const latLngs = coords.map(([lng, lat]) => ({ lat, lng }));
        const durationSeconds = Math.round(summary.duration || 0);
        const durationDeltaMinutes = targetDurationMinutes != null
            ? Math.round(durationSeconds / 60) - targetDurationMinutes
            : null;

        return {
            id: `${labelPrefix}-${index}-${durationSeconds}-${Math.round(summary.distance || 0)}`,
            label: features.length > 1 ? `${labelPrefix} ${index + 1}` : labelPrefix,
            distanceKm: Math.round(((summary.distance || 0) / 1000) * 10) / 10,
            durationSeconds,
            durationDeltaMinutes,
            isLoop,
            latLngs,
            geoJson: JSON.stringify(latLngs),
            waypoints: [
                { lat: origin.lat, lng: origin.lng, name: origin.name || 'Origen' },
                { lat: dest.lat, lng: dest.lng, name: dest.name || (isLoop ? origin.name : 'Destí') },
            ],
            tag,
        };
    });
}

async function fetchSingleLoopRoute({
    origin,
    targetDurationMinutes,
    highway,
    roadStyle,
    seed,
    points,
}) {
    const coordinates = [[origin.lng, origin.lat]];
    const targetSeconds = targetDurationMinutes * 60;
    let lengthM = estimateLoopLengthMeters(targetDurationMinutes, roadStyle);

    let geojson = await fetchDirectionsAvoidingFastRoads({
        coordinates,
        highway,
        roadStyle,
        roundTrip: { length: lengthM, points, seed },
    });

    let durationSeconds = geojson?.features?.[0]?.properties?.summary?.duration || 0;

    if (durationSeconds > 0) {
        const ratio = targetSeconds / durationSeconds;
        if (Math.abs(1 - ratio) > LOOP_DURATION_TOLERANCE) {
            lengthM = clampLoopLength(lengthM * ratio);
            geojson = await fetchDirectionsAvoidingFastRoads({
                coordinates,
                highway,
                roadStyle,
                roundTrip: { length: lengthM, points, seed: seed + 1000 },
            });
        }
    }

    return geojson;
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
            const geojson = await fetchDirectionsAvoidingFastRoads({
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
        const geojson = await fetchDirectionsAvoidingFastRoads({
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
            const altGeo = await fetchDirectionsAvoidingFastRoads({
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

export async function fetchLoopProposals({
    origin,
    targetDurationMinutes,
    highway,
    roadStyle,
    labelPrefix = 'Volta',
}) {
    const tag = buildRequestOptions({ highway, roadStyle }).tag;
    const results = await Promise.allSettled(
        LOOP_VARIANTS.map(({ seed, points }, index) => (
            fetchSingleLoopRoute({
                origin,
                targetDurationMinutes,
                highway,
                roadStyle,
                seed,
                points,
            }).then((geojson) => parseProposals(geojson, {
                origin,
                destination: origin,
                labelPrefix: `${labelPrefix} ${String.fromCharCode(65 + index)}`,
                tag,
                targetDurationMinutes,
                isLoop: true,
            })[0])
        )),
    );

    let proposals = results
        .filter((result) => result.status === 'fulfilled' && result.value)
        .map((result) => result.value);

    proposals = dedupeProposals(proposals);

    const targetSeconds = targetDurationMinutes * 60;
    proposals.sort((a, b) => {
        const diffA = Math.abs(a.durationSeconds - targetSeconds);
        const diffB = Math.abs(b.durationSeconds - targetSeconds);
        return diffA - diffB;
    });

    return { proposals, targetDurationMinutes };
}

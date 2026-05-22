/** Google Maps: origin + destination + up to 23 intermediate stops */
export const MAX_NAV_WAYPOINTS = 23;

export function haversineMeters(a, b) {
    const R = 6371000;
    const dLat = ((b.lat - a.lat) * Math.PI) / 180;
    const dLng = ((b.lng - a.lng) * Math.PI) / 180;
    const lat1 = (a.lat * Math.PI) / 180;
    const lat2 = (b.lat * Math.PI) / 180;
    const x = Math.sin(dLat / 2) ** 2
        + Math.cos(lat1) * Math.cos(lat2) * Math.sin(dLng / 2) ** 2;

    return R * 2 * Math.atan2(Math.sqrt(x), Math.sqrt(1 - x));
}

export function parseLatLngPath(geoJsonOrArray) {
    if (!geoJsonOrArray) return [];

    let data = geoJsonOrArray;
    if (typeof data === 'string') {
        try {
            data = JSON.parse(data);
            if (typeof data === 'string') {
                data = JSON.parse(data);
            }
        } catch {
            return [];
        }
    }

    if (!Array.isArray(data)) return [];

    return data.map((point) => {
        if (Array.isArray(point) && point.length >= 2) {
            return { lat: point[1], lng: point[0] };
        }
        if (point?.lat != null && point?.lng != null) {
            return { lat: point.lat, lng: point.lng };
        }
        if (point?.latitude != null && point?.longitude != null) {
            return { lat: point.latitude, lng: point.longitude };
        }
        return null;
    }).filter(Boolean);
}

function cumulativeDistances(latLngs) {
    const dists = [0];
    for (let i = 1; i < latLngs.length; i += 1) {
        dists.push(dists[i - 1] + haversineMeters(latLngs[i - 1], latLngs[i]));
    }
    return dists;
}

function findIndexAtDistance(latLngs, dists, targetDist) {
    for (let i = 1; i < dists.length; i += 1) {
        if (dists[i] >= targetDist) {
            const prevDist = dists[i - 1];
            const segment = dists[i] - prevDist;
            return segment > 0 && targetDist - prevDist < segment / 2 ? i - 1 : i;
        }
    }
    return latLngs.length - 1;
}

function waypointName(index, total, isLoop) {
    if (index === 0) return 'Sortida';
    if (!isLoop && index === total - 1) return 'Arribada';
    return `Punt ${index + 1}`;
}

/**
 * Sample key navigation points along a full route geometry for storage and Google Maps.
 */
export function extractNavigationWaypoints(latLngs, { maxPoints = MAX_NAV_WAYPOINTS, isLoop = false } = {}) {
    if (!latLngs?.length) return [];

    if (latLngs.length <= maxPoints) {
        return latLngs.map((point, index) => ({
            lat: point.lat,
            lng: point.lng,
            name: waypointName(index, latLngs.length, isLoop),
        }));
    }

    const dists = cumulativeDistances(latLngs);
    const total = dists[dists.length - 1];
    const indices = new Set([0]);

    const innerCount = isLoop ? maxPoints - 1 : maxPoints - 2;
    for (let i = 1; i <= innerCount; i += 1) {
        const targetDist = (total * i) / (innerCount + 1);
        indices.add(findIndexAtDistance(latLngs, dists, targetDist));
    }

    if (!isLoop) {
        indices.add(latLngs.length - 1);
    }

    const sorted = [...indices].sort((a, b) => a - b);
    return sorted.map((index, order) => ({
        lat: latLngs[index].lat,
        lng: latLngs[index].lng,
        name: waypointName(order, sorted.length, isLoop),
    }));
}

export function isClosedLoopRoute(waypoints, geoJsonPoints = null) {
    const geo = geoJsonPoints?.length ? geoJsonPoints : waypoints;
    if (geo?.length >= 2) {
        const first = geo[0];
        const last = geo[geo.length - 1];
        if (haversineMeters(first, last) < 400) {
            return true;
        }
    }

    if (waypoints?.length >= 3) {
        const first = waypoints[0];
        const last = waypoints[waypoints.length - 1];
        return haversineMeters(first, last) < 400;
    }

    return false;
}

export function buildGoogleMapsDirectionsUrl(waypoints, { isLoop = false } = {}) {
    if (!waypoints?.length) return '#';

    const baseUrl = 'https://www.google.com/maps/dir/?api=1&travelmode=driving';
    const fmt = (point) => `${point.lat},${point.lng}`;

    if (isLoop) {
        const origin = fmt(waypoints[0]);
        const via = waypoints.slice(1).map(fmt).join('|');
        return via
            ? `${baseUrl}&origin=${origin}&destination=${origin}&waypoints=${via}`
            : `${baseUrl}&origin=${origin}&destination=${origin}`;
    }

    const origin = fmt(waypoints[0]);
    const destination = fmt(waypoints[waypoints.length - 1]);

    if (waypoints.length <= 2) {
        return `${baseUrl}&origin=${origin}&destination=${destination}`;
    }

    const intermediates = waypoints.slice(1, -1).map(fmt).join('|');
    return `${baseUrl}&origin=${origin}&destination=${destination}&waypoints=${intermediates}`;
}

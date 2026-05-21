<?php

namespace App\Services;

use App\Models\HabitualRoute;
use App\Models\Motorcycle;
use App\Models\Route;
use App\Models\Trip;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ManualTripService
{
    public function registerFromHabitual(HabitualRoute $habitualRoute): array
    {
        $habitualRoute->loadMissing(['route', 'motorcycle']);

        $route = $habitualRoute->route;
        if (! $route) {
            throw new \RuntimeException('La ruta associada ja no existeix.');
        }

        $moto = Motorcycle::where('id', $habitualRoute->motorcycle_id)
            ->where('user_id', Auth::id())
            ->first();

        if (! $moto) {
            throw new \RuntimeException('La moto associada ja no existeix.');
        }

        $distance = $this->routeDistanceKm($route, (bool) $habitualRoute->round_trip);
        if ($distance === null) {
            throw new \RuntimeException('La ruta no té quilòmetres definits.');
        }

        $waypoints = $this->waypointsFromRoute($route);
        $first = $waypoints[0] ?? null;

        $payload = [
            'user_id'          => Auth::id(),
            'motorcycle_id'    => $moto->id,
            'route_id'         => $route->id,
            'distance_km'      => $distance,
            'duration_seconds' => null,
            'starting_lat'     => (is_array($first) ? ($first['lat'] ?? null) : null) ?? $route->starting_lat ?? null,
            'starting_lng'     => (is_array($first) ? ($first['lng'] ?? null) : null) ?? $route->starting_lng ?? null,
            'waypoints'        => $waypoints,
            'started_at'       => now(),
        ];

        if (Schema::hasColumn('trips', 'manual_entry')) {
            $payload['manual_entry'] = true;
        }
        if (Schema::hasColumn('trips', 'notes')) {
            $payload['notes'] = $habitualRoute->label;
        }

        Trip::create($payload);

        $moto->current_km = ($moto->current_km ?? 0) + $distance;
        $moto->save();

        return [
            'title' => $habitualRoute->displayTitle(),
            'km'    => round($distance, 1),
        ];
    }

    private function routeDistanceKm(Route $route, bool $roundTrip): ?float
    {
        $base = (float) ($route->planned_distance_km ?? $route->distance_km ?? 0);
        if ($base <= 0) {
            return null;
        }

        return $roundTrip ? $base * 2 : $base;
    }

    private function waypointsFromRoute(Route $route): array
    {
        try {
            $geo = $route->getRawOriginal('geo_json');
            if (is_string($geo)) {
                $geo = json_decode($geo, true);
                if (is_string($geo)) {
                    $geo = json_decode($geo, true);
                }
            }
        } catch (\Throwable) {
            return [];
        }

        if (! is_array($geo)) {
            return [];
        }

        if (($geo['type'] ?? null) === 'LineString' && is_array($geo['coordinates'] ?? null)) {
            $geo = $geo['coordinates'];
        } elseif (($geo['type'] ?? null) === 'Feature' && is_array($geo['geometry']['coordinates'] ?? null)) {
            $geo = $geo['geometry']['coordinates'];
        }

        $waypoints = [];
        foreach ($geo as $point) {
            if (! is_array($point)) {
                continue;
            }

            if (isset($point['lat'], $point['lng'])) {
                $waypoints[] = ['lat' => (float) $point['lat'], 'lng' => (float) $point['lng']];
                continue;
            }

            if (isset($point['latitude'], $point['longitude'])) {
                $waypoints[] = ['lat' => (float) $point['latitude'], 'lng' => (float) $point['longitude']];
                continue;
            }

            if (! isset($point[0], $point[1])) {
                continue;
            }

            $a = (float) $point[0];
            $b = (float) $point[1];
            if (abs($a) <= 90 && abs($b) <= 180) {
                $waypoints[] = ['lat' => $a, 'lng' => $b];
            } else {
                $waypoints[] = ['lat' => $b, 'lng' => $a];
            }
        }

        return $waypoints;
    }
}

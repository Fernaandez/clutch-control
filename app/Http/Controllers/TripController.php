<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Motorcycle;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class TripController extends Controller
{
    private function formatTripListItem(Trip $trip): array
    {
        return [
            'id'               => $trip->id,
            'distance_km'      => $trip->distance_km,
            'duration_seconds' => $trip->duration_seconds,
            'started_at'       => $trip->started_at,
            'manual_entry'     => (bool) ($trip->manual_entry ?? false),
            'notes'            => $trip->notes,
            'motorcycle'       => $trip->motorcycle ? [
                'id'    => $trip->motorcycle->id,
                'brand' => $trip->motorcycle->brand,
                'model' => $trip->motorcycle->model,
            ] : null,
            'route' => $trip->route ? [
                'id'    => $trip->route->id,
                'title' => $trip->route->title,
            ] : null,
            'starting_lat' => $trip->starting_lat,
            'starting_lng' => $trip->starting_lng,
        ];
    }

    private function userTripsQuery()
    {
        return Trip::where('user_id', Auth::id())
            ->with(['motorcycle', 'route'])
            ->orderBy('started_at', 'desc');
    }

    private function ownedMotorcycle(int $motorcycleId): Motorcycle
    {
        return Motorcycle::where('id', $motorcycleId)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }

    private function addKmToMotorcycle(Motorcycle $moto, float $km): void
    {
        if ($km <= 0) {
            return;
        }
        $moto->current_km = ($moto->current_km ?? 0) + $km;
        $moto->save();
    }

    private function subtractKmFromMotorcycle(Motorcycle $moto, float $km): void
    {
        if ($km <= 0) {
            return;
        }
        $moto->current_km = max(0, ($moto->current_km ?? 0) - $km);
        $moto->save();
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
        } catch (\Throwable $e) {
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

    private function createManualTrip(array $data): Trip
    {
        $moto = $this->ownedMotorcycle((int) $data['motorcycle_id']);
        $waypoints = $data['waypoints'] ?? [];
        $first = $waypoints[0] ?? null;

        $payload = [
            'user_id'          => Auth::id(),
            'motorcycle_id'    => $moto->id,
            'route_id'         => $data['route_id'] ?? null,
            'distance_km'      => $data['distance_km'],
            'duration_seconds' => $data['duration_seconds'] ?? null,
            'starting_lat'     => (is_array($first) ? ($first['lat'] ?? null) : null) ?? $data['starting_lat'] ?? null,
            'starting_lng'     => (is_array($first) ? ($first['lng'] ?? null) : null) ?? $data['starting_lng'] ?? null,
            'waypoints'        => $waypoints ?: [],
            'started_at'       => $data['started_at'],
        ];

        if (Schema::hasColumn('trips', 'manual_entry')) {
            $payload['manual_entry'] = true;
        }
        if (Schema::hasColumn('trips', 'notes')) {
            $payload['notes'] = $data['notes'] ?? null;
        }

        $trip = Trip::create($payload);

        $this->addKmToMotorcycle($moto, (float) $data['distance_km']);

        return $trip;
    }

    public function history()
    {
        $trips = $this->userTripsQuery()
            ->get()
            ->map(fn (Trip $trip) => $this->formatTripListItem($trip));

        return Inertia::render('Trips/History', ['trips' => $trips]);
    }

    public function myTrips()
    {
        $trips = $this->userTripsQuery()
            ->get()
            ->map(fn (Trip $trip) => $this->formatTripListItem($trip));

        return response()->json($trips);
    }

    /**
     * Recorreguts propis fets sobre una ruta concreta (per comparar passades).
     */
    public function forRoute(Route $route)
    {
        $trips = $this->userTripsQuery()
            ->where('route_id', $route->id)
            ->get()
            ->map(fn (Trip $trip) => $this->formatTripListItem($trip));

        return response()->json($trips);
    }

    public function show(Trip $trip)
    {
        if ($trip->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('Trips/Show', [
            'trip' => [
                'id'               => $trip->id,
                'distance_km'      => $trip->distance_km,
                'duration_seconds' => $trip->duration_seconds,
                'started_at'       => $trip->started_at,
                'waypoints'        => $trip->waypoints,
                'starting_lat'     => $trip->starting_lat,
                'starting_lng'     => $trip->starting_lng,
                'manual_entry'     => (bool) ($trip->manual_entry ?? false),
                'notes'            => $trip->notes,
                'motorcycle'       => $trip->motorcycle ? [
                    'id'    => $trip->motorcycle->id,
                    'brand' => $trip->motorcycle->brand,
                    'model' => $trip->motorcycle->model,
                ] : null,
                'route' => $trip->route ? [
                    'id'       => $trip->route->id,
                    'title'    => $trip->route->title,
                    'geo_json' => $trip->route->geo_json,
                ] : null,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'distance_km'      => 'nullable|numeric|min:0',
            'duration_seconds' => 'nullable|integer|min:0',
            'waypoints'        => 'required|array|min:2',
            'started_at'       => 'required|date',
            'motorcycle_id'    => ['nullable', Rule::exists('motorcycles', 'id')->where('user_id', Auth::id())],
            'route_id'         => 'nullable|exists:routes,id',
        ]);

        $firstPoint = $validated['waypoints'][0];

        $trip = Trip::create([
            'user_id'          => Auth::id(),
            'motorcycle_id'    => $validated['motorcycle_id'] ?? null,
            'route_id'         => $validated['route_id'] ?? null,
            'distance_km'      => $validated['distance_km'] ?? null,
            'duration_seconds' => $validated['duration_seconds'] ?? null,
            'starting_lat'     => $firstPoint['lat'] ?? null,
            'starting_lng'     => $firstPoint['lng'] ?? null,
            'waypoints'        => $validated['waypoints'],
            'started_at'       => $validated['started_at'],
            'manual_entry'     => false,
        ]);

        if (! empty($validated['motorcycle_id']) && ! empty($validated['distance_km'])) {
            $moto = Motorcycle::find($validated['motorcycle_id']);
            if ($moto && $moto->user_id === Auth::id()) {
                $this->addKmToMotorcycle($moto, (float) $validated['distance_km']);
            }
        }

        return response()->json(['success' => true, 'trip_id' => $trip->id]);
    }

    public function storeManual(Request $request)
    {
        $validated = $request->validate([
            'motorcycle_id' => 'required|exists:motorcycles,id',
            'distance_km'   => 'required|numeric|min:0.1',
            'started_at'    => 'required|date',
            'notes'         => 'nullable|string|max:500',
        ]);

        $trip = $this->createManualTrip([
            'motorcycle_id' => $validated['motorcycle_id'],
            'distance_km'   => $validated['distance_km'],
            'started_at'    => $validated['started_at'],
            'notes'         => $validated['notes'] ?? null,
            'waypoints'     => [],
        ]);

        return redirect()->route('trips.show', $trip);
    }

    public function applyRouteToMotorcycle(Request $request, Route $route)
    {
        $user = Auth::user();
        $isOwner = $user && (int) $route->user_id === (int) $user->id;
        if (! $route->is_public && ! $isOwner) {
            abort(403);
        }

        $validated = $request->validate([
            'motorcycle_id' => 'required|exists:motorcycles,id',
            'started_at'    => 'nullable|date',
            'round_trip'    => 'sometimes|boolean',
            'notes'         => 'nullable|string|max:500',
        ]);

        $roundTrip = (bool) ($validated['round_trip'] ?? false);
        $distance = $this->routeDistanceKm($route, $roundTrip);
        if ($distance === null) {
            return redirect()->route('routes.habitual')->withErrors([
                'habitual' => 'La ruta no té quilòmetres definits.',
            ]);
        }
        $waypoints = $this->waypointsFromRoute($route);
        $firstWp = $waypoints[0] ?? null;

        $trip = $this->createManualTrip([
            'motorcycle_id' => $validated['motorcycle_id'],
            'route_id'      => $route->id,
            'distance_km'   => $distance,
            'started_at'    => $validated['started_at'] ?? now(),
            'notes'         => $validated['notes'] ?? null,
            'waypoints'     => $waypoints,
            'starting_lat'  => $route->starting_lat ?? (is_array($firstWp) ? ($firstWp['lat'] ?? null) : null),
            'starting_lng'  => $route->starting_lng ?? (is_array($firstWp) ? ($firstWp['lng'] ?? null) : null),
        ]);

        return redirect()->route('trips.show', $trip);
    }

    public function destroy(Trip $trip)
    {
        if ($trip->user_id !== Auth::id()) {
            abort(403);
        }

        // store() suma km tant per als trajectes manuals com per als de GPS,
        // per tant l'esborrat els ha de restar en tots dos casos. Abans només
        // es restaven els manuals i el comptaquilòmetres quedava inflat.
        if ($trip->motorcycle_id && $trip->distance_km) {
            $moto = Motorcycle::find($trip->motorcycle_id);
            if ($moto && $moto->user_id === Auth::id()) {
                $this->subtractKmFromMotorcycle($moto, (float) $trip->distance_km);
            }
        }

        $trip->delete();

        return redirect()->route('routes.history');
    }
}
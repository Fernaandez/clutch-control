<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Motorcycle;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TripController extends Controller
{
    // Llista els meus recorreguts (per MyRoutes.vue)
    public function myTrips()
    {
        $trips = Trip::where('user_id', Auth::id())
            ->with(['motorcycle', 'route'])
            ->orderBy('started_at', 'desc')
            ->get()
            ->map(function ($trip) {
                return [
                    'id'               => $trip->id,
                    'distance_km'      => $trip->distance_km,
                    'duration_seconds' => $trip->duration_seconds,
                    'started_at'       => $trip->started_at,
                    'motorcycle'       => $trip->motorcycle ? [
                        'id'    => $trip->motorcycle->id,
                        'brand' => $trip->motorcycle->brand,
                        'model' => $trip->motorcycle->model,
                    ] : null,
                    'route' => $trip->route ? [
                        'id'    => $trip->route->id,
                        'title' => $trip->route->title,
                    ] : null,
                    // Primer i últim punt per mostrar minimap
                    'starting_lat' => $trip->starting_lat,
                    'starting_lng' => $trip->starting_lng,
                ];
            });

        return response()->json($trips);
    }

    // Detall d'un recorregut (Trips/Show.vue)
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
                'motorcycle'       => $trip->motorcycle ? [
                    'id'    => $trip->motorcycle->id,
                    'brand' => $trip->motorcycle->brand,
                    'model' => $trip->motorcycle->model,
                ] : null,
                'route' => $trip->route ? [
                    'id'      => $trip->route->id,
                    'title'   => $trip->route->title,
                    'geo_json' => $trip->route->geo_json,
                ] : null,
            ],
        ]);
    }

    // Crear recorregut (des del frontend, via sync)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'distance_km'      => 'nullable|numeric|min:0',
            'duration_seconds' => 'nullable|integer|min:0',
            'waypoints'        => 'required|array|min:2',
            'started_at'       => 'required|date',
            'motorcycle_id'    => 'nullable|exists:motorcycles,id',
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
        ]);

        // Sumar KM a la moto automàticament
        if (!empty($validated['motorcycle_id']) && !empty($validated['distance_km'])) {
            $moto = Motorcycle::find($validated['motorcycle_id']);
            if ($moto && $moto->user_id === Auth::id()) {
                $moto->current_km = ($moto->current_km ?? 0) + $validated['distance_km'];
                $moto->save();
            }
        }

        return response()->json(['success' => true, 'trip_id' => $trip->id]);
    }

    // Eliminar recorregut
    public function destroy(Trip $trip)
    {
        if ($trip->user_id !== Auth::id()) {
            abort(403);
        }
        $trip->delete();
        return response()->json(['success' => true]);
    }

    // Recorreguts vinculats a una ruta (per Routes/Show.vue)
    public function forRoute(Route $route)
    {
        $trips = Trip::where('user_id', Auth::id())
            ->where('route_id', $route->id)
            ->orderBy('started_at', 'desc')
            ->get()
            ->map(function ($trip) {
                return [
                    'id'               => $trip->id,
                    'distance_km'      => $trip->distance_km,
                    'duration_seconds' => $trip->duration_seconds,
                    'started_at'       => $trip->started_at,
                    'waypoints'        => $trip->waypoints,
                ];
            });

        return response()->json($trips);
    }
}

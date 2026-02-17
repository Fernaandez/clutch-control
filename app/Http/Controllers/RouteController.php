<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Motorcycle;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class RouteController extends Controller
{
    // LLISTAT
    public function index()
    {
        $routes = Route::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Routes/Index', ['routes' => $routes]);
    }

    // FORMULARI CREAR
    public function create()
    {
        return Inertia::render('Routes/Create', [
            'motorcycles' => Auth::user()->motorcycles 
        ]);
    }

    // GUARDAR (La màgia del mapa està aquí)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'planned_distance_km' => 'nullable|numeric',
            'difficulty' => 'required|in:easy,medium,hard',
            'motorcycle_id' => 'nullable|exists:motorcycles,id',
            'waypoints' => 'required|array|min:2', 
            'geo_json' => 'required', // Necessari per guardar la corba
        ]);

        $route = $request->user()->routes()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'difficulty' => $validated['difficulty'],
            'motorcycle_id' => $validated['motorcycle_id'],
            'planned_distance_km' => $validated['planned_distance_km'],
            
            // AQUESTA LÍNIA ÉS CLAU: Convertim el text JSON en Array PHP
            'geo_json' => json_decode($request->geo_json), 

            'starting_lat' => $request->waypoints[0]['lat'],
            'starting_lng' => $request->waypoints[0]['lng'],
        ]);

        // Guardar els Waypoints (només punts de pas)
        foreach ($request->waypoints as $index => $point) {
            $route->waypoints()->create([
                'latitude' => $point['lat'],
                'longitude' => $point['lng'],
                'order' => $index,
                'name' => $index === 0 ? 'Sortida' : 'Punt ' . $index,
            ]);
        }

        return redirect()->route('routes.index');
    }

    // VEURE
    public function show(Route $route)
    {
        return Inertia::render('Routes/Show', [
            'mapRoute' => $route->load('waypoints'),
            'motorcycle' => $route->motorcycle
        ]);
    }

    // EDITAR
    public function edit(Route $route)
    {
        if ($route->user_id !== Auth::id()) { abort(403); }
        return Inertia::render('Routes/Edit', [
            'mapRoute' => $route->load('waypoints'),
            'motorcycles' => Auth::user()->motorcycles
        ]);
    }

    // UPDATE
    public function update(Request $request, Route $route)
    {
        if ($route->user_id !== Auth::id()) { abort(403); }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'motorcycle_id' => 'nullable|exists:motorcycles,id',
        ]);
        $route->update($validated);
        return redirect()->route('routes.index');
    }

    // ESBORRAR
    public function destroy(Route $route)
    {
        if ($route->user_id !== Auth::id()) { abort(403); }
        $route->delete();
        return redirect()->route('routes.index');
    }
}
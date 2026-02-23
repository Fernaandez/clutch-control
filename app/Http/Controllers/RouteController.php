<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Motorcycle;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class RouteController extends Controller
{
    // 1. LLISTAT COMUNITAT (Només rutes públiques)
    public function index()
    {
        // Carreguem l'usuari per poder mostrar el nom del creador
        $routes = Route::with('user')
            ->where('is_public', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Routes/Index', ['routes' => $routes]);
    }

    // 2. NOVA FUNCIÓ: LES MEVES RUTES (Privades i públiques meves)
    public function MyRoutes()
    {
        $routes = Route::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Routes/MyRoutes', ['routes' => $routes]);
    }

    // FORMULARI CREAR
    public function create()
    {
        return Inertia::render('Routes/Create', [
            'motorcycles' => Auth::user()->motorcycles 
        ]);
    }

    // GUARDAR
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'planned_distance_km' => 'nullable|numeric',
            'difficulty' => 'required|in:easy,medium,hard',
            'motorcycle_id' => 'nullable|exists:motorcycles,id',
            'waypoints' => 'required|array|min:2', 
            'geo_json' => 'required', 
            'is_public' => 'boolean',
        ]);

        $route = $request->user()->routes()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'difficulty' => $validated['difficulty'],
            'motorcycle_id' => $validated['motorcycle_id'],
            'planned_distance_km' => $validated['planned_distance_km'],
            'geo_json' => json_decode($request->geo_json), 
            'starting_lat' => $request->waypoints[0]['lat'],
            'starting_lng' => $request->waypoints[0]['lng'],
            'is_public' => $request->is_public ?? false,
            // Per defecte, si no hi ha checkbox al formulari, es guarda com a privada (is_public = false)
        ]);

        foreach ($request->waypoints as $index => $point) {
            $route->waypoints()->create([
                'latitude' => $point['lat'],
                'longitude' => $point['lng'],
                'order' => $index,
                'name' => $index === 0 ? 'Sortida' : 'Punt ' . $index,
            ]);
        }

        // REDIRECT CANVIAT: Et porta a la teva llista privada
        return redirect()->route('routes.MyRoutes');
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
    // UPDATE
    public function update(Request $request, Route $route)
    {
        if ($route->user_id !== Auth::id()) { abort(403); }
        
        // 1. Afegim TOTS els camps del mapa a la validació
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'motorcycle_id' => 'nullable|exists:motorcycles,id',
            'is_public' => 'boolean',
            'planned_distance_km' => 'nullable|numeric',
            'waypoints' => 'required|array|min:2', 
            'geo_json' => 'required', 
        ]);
        
        // 2. Actualitzem les dades generals de la ruta
        $route->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'difficulty' => $validated['difficulty'],
            'motorcycle_id' => $validated['motorcycle_id'],
            'is_public' => $request->is_public ?? false,
            'planned_distance_km' => $validated['planned_distance_km'],
            'geo_json' => is_string($request->geo_json) ? json_decode($request->geo_json) : $request->geo_json, 
            'starting_lat' => $request->waypoints[0]['lat'] ?? $request->waypoints[0]['latitude'],
            'starting_lng' => $request->waypoints[0]['lng'] ?? $request->waypoints[0]['longitude'],
        ]);

        // 3. ESBORREM els punts (waypoints) antics de la base de dades
        $route->waypoints()->delete();

        // 4. GUARDEM els nous punts que has modificat al mapa
        foreach ($request->waypoints as $index => $point) {
            $route->waypoints()->create([
                'latitude' => $point['lat'] ?? $point['latitude'],
                'longitude' => $point['lng'] ?? $point['longitude'],
                'order' => $index,
                'name' => $index === 0 ? 'Sortida' : 'Punt ' . $index,
            ]);
        }
        
        return redirect()->route('routes.MyRoutes');
    }

    // ESBORRAR
    public function destroy(Route $route)
    {
        if ($route->user_id !== Auth::id()) { abort(403); }
        $route->delete();
        
        // REDIRECT CANVIAT
        return redirect()->route('routes.MyRoutes');
    }

    // CLONAR RUTA
    public function clone(Route $route)
    {
        // 1. Copiem la ruta principal
        $newRoute = $route->replicate();
        $newRoute->user_id = Auth::id();
        $newRoute->title = $route->title . ' (Còpia)';
        $newRoute->is_public = false; 
        $newRoute->save();

        // 2. LA CLAU: Copiem també tots els punts (waypoints) de la ruta original!
        foreach ($route->waypoints as $waypoint) {
            $newWaypoint = $waypoint->replicate();
            $newWaypoint->route_id = $newRoute->id; // Assignem el punt a la NOVA ruta
            $newWaypoint->save();
        }

        // 3. Redirigim a l'editor
        return redirect()->route('routes.edit', $newRoute->id);
    }
}   
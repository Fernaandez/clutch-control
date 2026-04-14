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
        $routes = Route::with(['user', 'reviews'])
            ->where('is_public', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $motorcycles = Auth::user()
            ? Auth::user()->motorcycles()->select('id', 'brand', 'model')->get()
            : collect();

        return Inertia::render('Routes/Index', [
            'routes' => $routes,
            'defaultMotorcycleId' => optional($motorcycles->first())->id,
        ]);
    }

    // 2. NOVA FUNCIÓ: LES MEVES RUTES (Privades i públiques meves)
    public function MyRoutes()
    {
        $routes = Route::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Routes/MyRoutes', ['routes' => $routes]);
    }

    // NOVA FUNCIÓ: PREVISUALITZAR RUTA VIA ENLLAÇ (Guest/Public)
    public function preview($token)
    {
        $route = Route::where('share_token', $token)->firstOrFail();
        
        return Inertia::render('Routes/Show', [
            'mapRoute' => $route->load(['waypoints', 'reviews.user']),
            'motorcycle' => $route->motorcycle
        ]);
    }

    // NOVA FUNCIÓ: RUTES PENDENTS (OFFLINE)
    public function pending()
    {
        return Inertia::render('Routes/Pending');
    }

    // NOVA FUNCIÓ: SINCRONITZAR RUTA OFFLINE
    public function syncOffline(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'is_public' => 'boolean',
            'distance_km' => 'nullable|numeric',
            'duration_seconds' => 'nullable|integer',
            'waypoints' => 'required|array',
            'created_at' => 'required|date',
            'original_route_id' => 'nullable|exists:routes,id',
            'motorcycle_id' => 'nullable|exists:motorcycles,id',
        ]);

        // Crear la ruta a base de dades
        $route = $request->user()->routes()->create([
            'title' => $validated['title'],
            'description' => 'Ruta gravada sense connexió.',
            'difficulty' => 'medium', // Per defecte
            'is_public' => $validated['is_public'] ?? false,
            'is_recorded' => true,
            'planned_distance_km' => $validated['distance_km'],
            'distance_km' => $validated['distance_km'],
            'duration_seconds' => $validated['duration_seconds'],
            'created_at' => $validated['created_at'],
            'starting_lat' => $validated['waypoints'][0]['lat'] ?? null,
            'starting_lng' => $validated['waypoints'][0]['lng'] ?? null,
            'geo_json' => json_encode(['type' => 'FeatureCollection', 'features' => []]), // Placeholder
            'motorcycle_id' => $validated['motorcycle_id'] ?? null,
        ]);

        // Guardar tots els waypoints del GPS
        foreach ($validated['waypoints'] as $index => $point) {
            $route->waypoints()->create([
                'latitude' => $point['lat'],
                'longitude' => $point['lng'],
                'order' => $index,
                'name' => 'Punt ' . $index,
            ]);
        }

        // Sumar KM a la moto
        if (!empty($validated['motorcycle_id']) && !empty($validated['distance_km'])) {
            $moto = \App\Models\Motorcycle::find($validated['motorcycle_id']);
            if ($moto) {
                // S'assegura que siguin números, parseFloat si cal (però el validat és numeric)
                $moto->current_km += $validated['distance_km'];
                $moto->save();
            }
        }

        return response()->json(['success' => true, 'route_id' => $route->id]);
    }

    // FORMULARI CREAR
    public function create()
    {
        return Inertia::render('Routes/Create', [
            'motorcycles' => Auth::user()->motorcycles,
            'categories' => \App\Models\RouteCategory::all()
        ]);
    }

    // NOVA FUNCIÓ: RUTA LLIURE
    public function freeRide(Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }
        
        return Inertia::render('Routes/FreeRide', [
            'motorcycle' => $motorcycle
        ]);
    }

    // GUARDAR
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'planned_distance_km' => 'nullable|numeric',
            'distance_km' => 'nullable|numeric|min:0',
            'duration_seconds' => 'nullable|integer|min:0',
            'difficulty' => 'required|in:easy,medium,hard',
            'motorcycle_id' => 'nullable|exists:motorcycles,id',
            'category_id' => 'nullable|exists:route_categories,id',
            'waypoints' => 'nullable|array', 
            'geo_json' => 'required', 
            'is_public' => 'boolean',
            'is_recorded' => 'boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $route = $request->user()->routes()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'difficulty' => $validated['difficulty'],
            'motorcycle_id' => $validated['motorcycle_id'],
            'category_id' => $validated['category_id'] ?? null,
            'planned_distance_km' => $validated['planned_distance_km'],
            'distance_km' => $validated['distance_km'] ?? null,
            'duration_seconds' => $validated['duration_seconds'] ?? null,
            'geo_json' => is_string($request->geo_json) ? json_decode($request->geo_json) : $request->geo_json, 
            'starting_lat' => isset($request->waypoints[0]) ? $request->waypoints[0]['lat'] : null,
            'starting_lng' => isset($request->waypoints[0]) ? $request->waypoints[0]['lng'] : null,
            'is_public' => $request->is_public ?? false,
            'is_recorded' => $request->is_recorded ?? false,
        ]);

        if ($request->hasFile('photo')) {
            $ext = $request->file('photo')->getClientOriginalExtension();
            $route->photo = $request->file('photo')->storeAs('routes', \Illuminate\Support\Str::random(40) . '.' . $ext, 'public');
            $route->save();
        }

        if (!empty($request->waypoints) && !($request->is_recorded ?? false)) {
            foreach ($request->waypoints as $index => $point) {
                $route->waypoints()->create([
                    'latitude' => $point['lat'],
                    'longitude' => $point['lng'],
                    'order' => $index,
                    'name' => $index === 0 ? 'Sortida' : 'Punt ' . $index,
                ]);
            }
        }

        // AUTO-SUMAR KM A LA MOTO SI ÉS GRAVADA I TÉ MOTO
        if (($request->is_recorded ?? false) && $route->motorcycle_id && isset($validated['distance_km'])) {
            $moto = \App\Models\Motorcycle::find($route->motorcycle_id);
            if ($moto) {
                $moto->current_km += $validated['distance_km'];
                $moto->save();
            }
        }

        // REDIRECT CANVIAT: Et porta a la teva llista privada
        return redirect()->route('routes.MyRoutes');
    }

    // VEURE
    public function show(Route $route)
    {
        return Inertia::render('Routes/Show', [
            'mapRoute' => $route->load(['waypoints', 'reviews.user']),
            'motorcycle' => $route->motorcycle
        ]);
    }

    // EDITAR
    public function edit(Route $route)
    {
        if ($route->user_id !== Auth::id()) { abort(403); }
        return Inertia::render('Routes/Edit', [
            'mapRoute' => $route->load('waypoints'),
            'motorcycles' => Auth::user()->motorcycles,
            'categories' => \App\Models\RouteCategory::all()
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
            'category_id' => 'nullable|exists:route_categories,id',
            'is_public' => 'boolean',
            'is_recorded' => 'boolean',
            'planned_distance_km' => 'nullable|numeric',
            'distance_km' => 'nullable|numeric|min:0',
            'duration_seconds' => 'nullable|integer|min:0',
            'waypoints' => 'nullable|array', 
            'geo_json' => 'required', 
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
        
        // 2. Actualitzem les dades generals de la ruta
        $route->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'difficulty' => $validated['difficulty'],
            'motorcycle_id' => $validated['motorcycle_id'],
            'category_id' => $validated['category_id'] ?? null,
            'is_public' => $request->is_public ?? false,
            'is_recorded' => $request->is_recorded ?? $route->is_recorded,
            'planned_distance_km' => $validated['planned_distance_km'],
            'distance_km' => $validated['distance_km'] ?? $route->distance_km,
            'duration_seconds' => $validated['duration_seconds'] ?? $route->duration_seconds,
            'geo_json' => is_string($request->geo_json) ? json_decode($request->geo_json) : $request->geo_json, 
            'starting_lat' => isset($request->waypoints[0]) ? ($request->waypoints[0]['lat'] ?? $request->waypoints[0]['latitude']) : $route->starting_lat,
            'starting_lng' => isset($request->waypoints[0]) ? ($request->waypoints[0]['lng'] ?? $request->waypoints[0]['longitude']) : $route->starting_lng,
        ]);

        if ($request->hasFile('photo')) {
            if ($route->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($route->photo);
            }
            $ext = $request->file('photo')->getClientOriginalExtension();
            $route->photo = $request->file('photo')->storeAs('routes', \Illuminate\Support\Str::random(40) . '.' . $ext, 'public');
            $route->save();
        }

        // 3. ESBORREM els punts (waypoints) antics de la base de dades
        $route->waypoints()->delete();

        // 4. GUARDEM els nous punts que has modificat al mapa (només si no és enregistrada)
        if (!empty($request->waypoints) && !($route->is_recorded)) {
            foreach ($request->waypoints as $index => $point) {
                $route->waypoints()->create([
                    'latitude' => $point['lat'] ?? $point['latitude'],
                    'longitude' => $point['lng'] ?? $point['longitude'],
                    'order' => $index,
                    'name' => $index === 0 ? 'Sortida' : 'Punt ' . $index,
                ]);
            }
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
        $newRoute->share_token = \Illuminate\Support\Str::random(10);
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
<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Motorcycle;
use App\Models\HabitualRoute;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class RouteController extends Controller
{
    /**
     * Regla de validació per a motorcycle_id: ha d'existir I ser de l'usuari.
     * Amb només `exists:motorcycles,id` qualsevol usuari podia assignar-se
     * (i sumar km a) la moto d'un altre.
     */
    private function ownedMotorcycleRule(): array
    {
        return ['nullable', Rule::exists('motorcycles', 'id')->where('user_id', Auth::id())];
    }

    /** La moto de l'usuari, o null si no n'és el propietari. */
    private function ownedMotorcycle(?int $motorcycleId): ?Motorcycle
    {
        if (empty($motorcycleId)) {
            return null;
        }

        return Motorcycle::where('id', $motorcycleId)
            ->where('user_id', Auth::id())
            ->first();
    }

    private function addKmToMotorcycle(?Motorcycle $moto, ?float $km): void
    {
        if (! $moto || ! $km || $km <= 0) {
            return;
        }

        $moto->current_km = ($moto->current_km ?? 0) + $km;
        $moto->save();
    }

    private function subtractKmFromMotorcycle(?Motorcycle $moto, ?float $km): void
    {
        if (! $moto || ! $km || $km <= 0) {
            return;
        }

        $moto->current_km = max(0, ($moto->current_km ?? 0) - $km);
        $moto->save();
    }

    /**
     * Qui pot veure una ruta: el propietari, un admin, o qualsevol si és
     * pública. Les rutes privades només s'obren amb share_token.
     */
    private function canView(Route $route): bool
    {
        $user = Auth::user();

        return (bool) $route->is_public
            || ($user && (int) $route->user_id === (int) $user->id)
            || ($user && ($user->role ?? null) === 'admin');
    }
    /**
     * Pantalla única de Rutes: les meves, les de la comunitat i les habituals.
     * Substitueix el hub, l'explorador i "les meves rutes", que eren la mateixa
     * llista amb filtres diferents.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $myRoutes = Route::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        $communityRoutes = Route::with(['user:id,name', 'reviews'])
            ->where('is_public', true)
            ->where('user_id', '!=', $userId)
            ->orderByDesc('created_at')
            ->get();

        $motorcycles = Auth::user()
            ? Auth::user()->motorcycles()->select('id', 'brand', 'model')->get()
            : collect();

        return Inertia::render('Routes/Index', [
            'myRoutes'            => $myRoutes,
            'communityRoutes'     => $communityRoutes,
            'habitualRoutes'      => $this->habitualRoutesFor($userId),
            'defaultMotorcycleId' => optional($motorcycles->first())->id,
            'initialTab'          => $request->query('tab', 'mine'),
            'ridingStats'         => $this->ridingStats($userId),
        ]);
    }

    /**
     * El que ha rodat l'usuari aquest any. La pantalla de rutes parla de rodar,
     * no de gestionar fitxers: la xifra ha de ser el primer que es veu.
     */
    private function ridingStats(int $userId): array
    {
        $trips = \App\Models\Trip::where('user_id', $userId)
            ->whereYear('started_at', now()->year);

        $last = \App\Models\Trip::with('route:id,title')
            ->where('user_id', $userId)
            ->orderByDesc('started_at')
            ->first();

        return [
            'year'       => (int) now()->year,
            'year_km'    => (float) (clone $trips)->sum('distance_km'),
            'year_trips' => (int) (clone $trips)->count(),
            'longest_km' => (float) (clone $trips)->max('distance_km'),
            'last_trip'  => $last ? [
                'id'          => $last->id,
                'distance_km' => round((float) $last->distance_km),
                'started_at'  => $last->started_at,
                'title'       => $last->route?->title,
            ] : null,
        ];
    }

    /** Rutes habituals de l'usuari, en el format que espera el frontend. */
    private function habitualRoutesFor(int $userId)
    {
        return HabitualRoute::where('user_id', $userId)
            ->with([
                'route:id,title,planned_distance_km,distance_km',
                'motorcycle:id,brand,model',
            ])
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->get()
            ->map(fn (HabitualRoute $item) => [
                'id'          => $item->id,
                'label'       => $item->label,
                'title'       => $item->displayTitle(),
                'round_trip'  => $item->round_trip,
                'distance_km' => $item->distanceKm(),
                'route_id'    => $item->route_id,
                'route_title' => $item->route?->title,
                'motorcycle'  => $item->motorcycle ? [
                    'id'    => $item->motorcycle->id,
                    'brand' => $item->motorcycle->brand,
                    'model' => $item->motorcycle->model,
                ] : null,
            ]);
    }

    public function habitual(Request $request)
    {
        $motorcycles = Auth::user()->motorcycles()
            ->select('id', 'brand', 'model', 'current_km')
            ->get();

        $routes = Route::where('user_id', Auth::id())
            ->orderBy('title')
            ->get(['id', 'title', 'planned_distance_km', 'location_city']);

        $habitualRoutes = $this->habitualRoutesFor(Auth::id());

        return Inertia::render('Routes/Habitual', [
            'motorcycles'      => $motorcycles,
            'routes'           => $routes,
            'habitualRoutes'   => $habitualRoutes,
            'preselectedRouteId' => $request->integer('route') ?: null,
        ]);
    }

    // NOVA FUNCIÓ: PREVISUALITZAR RUTA VIA ENLLAÇ (Guest/Public)
    public function preview(Request $request, $token)
    {
        $route = Route::where('share_token', $token)->firstOrFail();

        if ($request->boolean('web') || Auth::check()) {
            return Inertia::render('Routes/Show', [
                'mapRoute' => $route->load(['user', 'waypoints', 'reviews.user']),
                'motorcycle' => $route->motorcycle
            ]);
        }

        $webUrl = route('routes.preview', ['token' => $token, 'web' => 1]);

        return Inertia::render('Shared/OpenInApp', [
            'title' => 'Obre la ruta amb Clutch Control',
            'subtitle' => 'Aquesta ruta s\'ha compartit per enllac. Instal·la l\'app o obre-la al navegador.',
            'webUrl' => $webUrl,
            'deepLinkUrl' => config('services.app_links.deep_link_base') . '/r/' . $token,
            'androidStoreUrl' => config('services.app_links.android_store_url'),
            'iosStoreUrl' => config('services.app_links.ios_store_url'),
            'openAppLabel' => 'Obrir app',
            'openWebLabel' => 'Continuar en web',
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
            'motorcycle_id' => $this->ownedMotorcycleRule(),
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

        // Sumar KM a la moto (només si és de l'usuari autenticat)
        $this->addKmToMotorcycle(
            $this->ownedMotorcycle($validated['motorcycle_id'] ?? null),
            isset($validated['distance_km']) ? (float) $validated['distance_km'] : null
        );

        return response()->json(['success' => true, 'route_id' => $route->id]);
    }

    public function plan()
    {
        return Inertia::render('Routes/Plan');
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
            'motorcycle_id' => $this->ownedMotorcycleRule(),
            'category_id' => 'nullable|exists:route_categories,id',
            'waypoints' => 'nullable|array', 
            'geo_json' => 'required', 
            'is_public' => 'boolean',
            'is_recorded' => 'boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $route = $request->user()->routes()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'difficulty' => $validated['difficulty'],
            'motorcycle_id' => $validated['motorcycle_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'planned_distance_km' => $validated['planned_distance_km'] ?? null,
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
        if ($route->is_recorded && $route->motorcycle_id && isset($validated['distance_km'])) {
            $this->addKmToMotorcycle(
                $this->ownedMotorcycle($route->motorcycle_id),
                (float) $validated['distance_km']
            );
        }

        // REDIRECT CANVIAT: Et porta a la teva llista privada
        return redirect()->route('routes.MyRoutes');
    }

    // VEURE
    public function show(Route $route)
    {
        // SEGURETAT: Aquesta ruta es accessible per ID. Comprovem que
        // l'usuari hi te dret a accedir. Les rutes privades nomes les pot
        // veure el propietari o un admin; la resta de gent ha d'accedir
        // amb el share_token (via /r/{token}).
        if (! $this->canView($route)) {
            abort(403, 'Aquesta ruta es privada.');
        }

        // Si la ruta encara no te share_token (creada abans de la migracio),
        // li n'assignem un ara perque el boto de compartir funcioni.
        if (empty($route->share_token) && Schema::hasColumn('routes', 'share_token')) {
            $route->share_token = \Illuminate\Support\Str::random(12);
            $route->save();
        }

        return Inertia::render('Routes/Show', [
            'mapRoute' => $route->load(['user', 'waypoints', 'reviews.user']),
            'motorcycle' => $route->motorcycle,
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
            'motorcycle_id' => $this->ownedMotorcycleRule(),
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
            'description' => $validated['description'] ?? null,
            'difficulty' => $validated['difficulty'],
            'motorcycle_id' => $validated['motorcycle_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'is_public' => $request->is_public ?? false,
            'is_recorded' => $request->is_recorded ?? $route->is_recorded,
            'planned_distance_km' => $validated['planned_distance_km'] ?? null,
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

        // Si en crear-la vam sumar km a la moto (ruta gravada), els restem
        // en esborrar-la; si no, el comptaquilòmetres queda inflat per sempre.
        if ($route->is_recorded && $route->motorcycle_id) {
            $this->subtractKmFromMotorcycle(
                $this->ownedMotorcycle($route->motorcycle_id),
                (float) ($route->distance_km ?? 0)
            );
        }

        $route->delete();
        
        // REDIRECT CANVIAT
        return redirect()->route('routes.MyRoutes');
    }

    // CLONAR RUTA
    public function clone(Route $route)
    {
        // Sense aquesta comprovació qualsevol usuari podia clonar-se una ruta
        // privada d'un altre només sabent-ne l'ID.
        if (! $this->canView($route)) {
            abort(403, 'Aquesta ruta es privada.');
        }

        // 1. Copiem la ruta principal. Una còpia és un traçat per rodar, no el
        // registre d'algú altre: netegem la moto (que és d'un altre usuari) i
        // les dades de gravació, que no s'han fet servir mai.
        $newRoute = $route->replicate();
        $newRoute->user_id = Auth::id();
        $newRoute->title = $route->title . ' (Còpia)';
        $newRoute->is_public = false;
        $newRoute->motorcycle_id = null;
        $newRoute->is_recorded = false;
        $newRoute->distance_km = null;
        $newRoute->duration_seconds = null;
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
<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
// 1. INDEX: NOMÉS PÚBLIQUES (El taulell d'anuncis)
    public function index()
    {
        $events = Event::with(['organizer', 'participants'])
            ->where('is_public', true) // Només públiques
            ->where('start_time', '>=', now()->subDay()) // Futures
            ->orderBy('start_time', 'asc')
            ->get()
            ->map(function ($event) {
                $event->is_attending = $event->participants->contains(Auth::id());
                $event->participants_count = $event->participants->count();
                return $event;
            });

        return Inertia::render('Events/Index', [
            'events' => $events
        ]);
    }

    // 2. NOVA FUNCIÓ: LES MEVES QUEDADES (Gestió)
    public function myEvents()
    {
        $userId = Auth::id();

        // Busquem events on: (Sóc l'organitzador) O (M'he apuntat)
        $events = Event::with(['organizer', 'participants'])
            ->where(function($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->orWhereHas('participants', function($q) use ($userId) {
                          $q->where('user_id', $userId);
                      });
            })
            ->orderBy('start_time', 'asc')
            ->get()
            ->map(function ($event) {
                $event->is_attending = $event->participants->contains(Auth::id());
                $event->participants_count = $event->participants->count();
                return $event;
            });

        return Inertia::render('Events/MyEvents', [ // Crearem aquest fitxer ara
            'events' => $events
        ]);
    }

    // 2. FORMULARI PER CREAR (CREATE)
    public function create()
        {
            // Passem les meves rutes per poder triar-ne una
            $myRoutes = Route::where('user_id', Auth::id())->get();

            return Inertia::render('Events/Create', [
                'myRoutes' => $myRoutes
            ]);
        }

    // 3. GUARDAR A LA BASE DE DADES (STORE)
    public function store(Request $request)
        {
        // 1. AFEGIM 'is_public' A LA VALIDACIÓ
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'is_public' => 'boolean', // <--- IMPORTANT: Validem que sigui true/false
            
            // Validació d'etapes (la deixem igual)
            'stages' => 'array',
            'stages.*.type' => 'required|in:route,location',
            'stages.*.route_id' => 'nullable|exists:routes,id',
            'stages.*.location_name' => 'nullable|string',
            'stages.*.latitude' => 'nullable|numeric',
            'stages.*.longitude' => 'nullable|numeric',
        ]);

        // 2. CREEM L'EVENT FENT SERVIR LES DADES VALIDADES
        // Com que 'is_public' està al $fillable del teu model Event.php, 
        // això ja agafarà el valor (true o false) que enviïs des del formulari.
        $event = new Event($validated);
        
        $event->user_id = Auth::id();
        
        // Si no s'ha enviat res (per seguretat), posem true per defecte, 
        // però normalment el formulari ja envia true o false.
        if (!isset($validated['is_public'])) {
            $event->is_public = true;
        }

        // ... (Codi per calcular la ubicació inicial igual que abans) ...
        if (!empty($request->stages)) {
            $first = $request->stages[0];
            if ($first['type'] === 'route' && $first['route_id']) {
                $r = Route::find($first['route_id']);
                $event->location = $r->title; 
            } else {
                $event->location = $first['location_name'];
                $event->latitude = $first['latitude'];
                $event->longitude = $first['longitude'];
            }
        }
        
        $event->save();

        // 3. ESTAT: CONFIRMED (Arreglem també el 'going' aquí)
        $event->participants()->attach(Auth::id(), ['status' => 'confirmed']);

        // 4. GUARDEM RUTES
        if ($request->stages) {
            foreach ($request->stages as $index => $stage) {
                if ($stage['type'] === 'route' && $stage['route_id']) {
                    $event->routes()->attach($stage['route_id'], ['day_order' => $index + 1]);
                }
            }
        }

        return redirect()->route('events.index');
        }

    // 4. VEURE DETALL (SHOW)
    public function show(Event $event)
        {
            // Carreguem tota la info necessària
            $event->load(['organizer', 'routes', 'participants']);
            
            return Inertia::render('Events/Show', [
                'event' => $event,
                'isAttending' => $event->participants->contains(Auth::id())
            ]);
        }

    // 5. ACCIÓ: APUNTAR-SE (JOIN)
    public function join(Event $event)
        {
            if (!$event->participants->contains(Auth::id())) {
                $event->participants()->attach(Auth::id(), ['status' => 'confirmed']);
            }
            return back();
        }

    // 6. ACCIÓ: DESAPUNTAR-SE (LEAVE)
    public function leave(Event $event)
        {
            $event->participants()->detach(Auth::id());
            return back();
        }

    // ELIMINAR QUEDADA
    public function destroy(Event $event)
        {
            // Seguretat: Només l'organitzador pot borrar
            if ($event->user_id !== Auth::id()) {
                abort(403, 'No pots eliminar una quedada que no és teva.');
            }

            $event->delete();

            return redirect()->route('events.index');
        }
    
    // EDITAR (Només la funció buida per ara, per a que no doni error el botó)
    public function edit(Event $event)
    {
        if ($event->user_id !== Auth::id()) abort(403);
        
        // Carreguem les rutes vinculades per omplir l'itinerari
        $event->load('routes');
        
        // Les meves rutes disponibles per si en vol afegir de noves
        $myRoutes = Route::where('user_id', Auth::id())->get();
        
        return Inertia::render('Events/Edit', [
            'event' => $event,
            'myRoutes' => $myRoutes,
            // Passem les rutes actuals de l'event ja formatades
            'currentStages' => $event->routes->map(function($route) {
                return [
                    'type' => 'route',
                    'route_id' => $route->id,
                    // Si tinguessis dades de 'location' per etapa a la DB, les posaries aquí.
                    // Com que només guardem rutes, la resta ho deixem buit o per defecte.
                ];
            })
        ]);
    }

    // 2. ACTUALITZAR (Guarda els canvis)
    public function update(Request $request, Event $event)
    {
        if ($event->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'location' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_public' => 'boolean',
            'max_participants' => 'nullable|integer|min:1', // <--- NOU CAMP
            
            // Validem l'array d'etapes igual que al Create
            'stages' => 'array',
            'stages.*.type' => 'required|in:route,location',
            'stages.*.route_id' => 'nullable|exists:routes,id',
            'stages.*.location_name' => 'nullable|string',
            'stages.*.latitude' => 'nullable|numeric',
            'stages.*.longitude' => 'nullable|numeric',
        ]);

        // A. Actualitzem dades bàsiques
        $event->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'start_time' => $validated['start_time'],
            'location' => $validated['location'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'is_public' => $validated['is_public'],
            'max_participants' => $validated['max_participants'], // <--- Guardem
        ]);

        // B. Actualitzem les Rutes / Etapes
        // Estratègia: Esborrem les relacions antigues i creem les noves (és el més net per reordenar)
        $event->routes()->detach();

        if ($request->stages) {
            foreach ($request->stages as $index => $stage) {
                if ($stage['type'] === 'route' && $stage['route_id']) {
                    $event->routes()->attach($stage['route_id'], ['day_order' => $index + 1]);
                }
            }
        }

        return redirect()->route('events.mine'); // Tornem a la gestió
    }
}
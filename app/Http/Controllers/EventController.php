<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Route;
use App\Models\Conversation;
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

    // NOVA FUNCIÓ: PREVISUALITZAR EVENT VIA ENLLAÇ (Guest/Public)
    public function preview($token)
    {
        $event = Event::where('share_token', $token)->firstOrFail();
        $event->load(['organizer', 'routes.waypoints', 'participants']);
        
        if (Auth::check()) {
            $event->is_attending = $event->participants->contains(Auth::id());
        } else {
            $event->is_attending = false;
        }
        $event->participants_count = $event->participants->count();

        return Inertia::render('Events/Show', [
            'event' => $event
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
            'max_participants' => 'nullable|integer|min:1',
            
            // Validació d'etapes (la deixem igual)
            'stages' => 'array',
            'stages.*.type' => 'required|in:route,location',
            'stages.*.route_id' => 'nullable|exists:routes,id',
            'stages.*.location_name' => 'nullable|string',
            'stages.*.latitude' => 'nullable|numeric',
            'stages.*.longitude' => 'nullable|numeric',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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

        if ($request->hasFile('photo')) {
            $ext = $request->file('photo')->getClientOriginalExtension();
            $event->photo = $request->file('photo')->storeAs('events', \Illuminate\Support\Str::random(40) . '.' . $ext, 'public');
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

        // 3b. CREAR GRUP DE XAT PER A LA QUEDADA
        $groupChat = Conversation::create([
            'type' => 'group',
            'name' => '📅 ' . $event->title,
            'event_id' => $event->id,
        ]);
        $groupChat->participants()->attach(Auth::id());

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
            
            if (Auth::check()) {
                $event->is_attending = $event->participants->contains(Auth::id());
                // Find group chat id
                $groupChat = \App\Models\Conversation::where('type', 'group')->where('event_id', $event->id)->first();
                $event->group_chat_id = $groupChat ? $groupChat->id : null;
            } else {
                $event->is_attending = false;
                $event->group_chat_id = null;
            }
            $event->participants_count = $event->participants->count();
            
            return Inertia::render('Events/Show', [
                'event' => $event
            ]);
        }

    // 5. ACCIÓ: APUNTAR-SE (JOIN)
    public function join(Event $event)
        {
            if (!$event->participants->contains(Auth::id())) {
                $event->participants()->attach(Auth::id(), ['status' => 'confirmed']);

                // Afegir al grup de xat de la quedada
                $groupChat = Conversation::where('type', 'group')->where('event_id', $event->id)->first();
                if ($groupChat && !$groupChat->participants()->where('user_id', Auth::id())->exists()) {
                    $groupChat->participants()->attach(Auth::id());
                }
            }
            return back();
        }

    // 6. ACCIÓ: DESAPUNTAR-SE (LEAVE)
    public function leave(Event $event)
        {
            $event->participants()->detach(Auth::id());

            // Treure del grup de xat de la quedada
            $groupChat = Conversation::where('type', 'group')->where('event_id', $event->id)->first();
            if ($groupChat) {
                $groupChat->participants()->detach(Auth::id());
            }
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
        
        $event->load('routes');
        $myRoutes = Route::where('user_id', Auth::id())->get();

        // Construïm les etapes des del pivot: inclou tant rutes com punts de trobada
        // L'event pot tenir stages guardats com a JSON a 'location' o com a relació de rutes
        $currentStages = [];

        // 1. Si l'event té un punt de trobada (location), sempre el posem primer
        if ($event->location) {
            $currentStages[] = [
                'type'          => 'location',
                'route_id'      => null,
                'location_name' => $event->location,
                'latitude'      => $event->latitude,
                'longitude'     => $event->longitude,
            ];
        }

        // 2. Afegim les rutes GPS (en ordre)
        foreach ($event->routes->sortBy('pivot.day_order') as $route) {
            $currentStages[] = [
                'type'          => 'route',
                'route_id'      => $route->id,
                'location_name' => null,
                'latitude'      => null,
                'longitude'     => null,
            ];
        }

        // 3. Si segueix completament buit, etapa per defecte
        if (empty($currentStages)) {
            $currentStages[] = [
                'type'          => 'location',
                'route_id'      => null,
                'location_name' => '',
                'latitude'      => null,
                'longitude'     => null,
            ];
        }
        
        return Inertia::render('Events/Edit', [
            'event'         => $event,
            'myRoutes'      => $myRoutes,
            'currentStages' => array_values($currentStages),
        ]);
    }

    // 2. ACTUALITZAR (Guarda els canvis)
    public function update(Request $request, Event $event)
    {
        if ($event->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'start_time'     => 'required|date',
            'location'       => 'nullable|string',
            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
            'is_public'      => 'boolean',
            'max_participants' => 'nullable|integer|min:1',
            'stages_json'    => 'nullable|string', // <-- Rep les etapes com a JSON string
            'photo'          => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Deserialitzem les etapes
        $stages = [];
        if (!empty($validated['stages_json'])) {
            $stages = json_decode($validated['stages_json'], true) ?? [];
        }

        // A. Actualitzem dades bàsiques
        $updateData = [
            'title'           => $validated['title'],
            'description'     => $validated['description'],
            'start_time'      => $validated['start_time'],
            'is_public'       => $validated['is_public'] ?? true,
            'max_participants' => $validated['max_participants'] ?? null,
        ];

        // Localització principal: primer stage de tipus location
        $locationStage = collect($stages)->firstWhere('type', 'location');
        if ($locationStage) {
            $updateData['location']  = $locationStage['location_name'] ?? null;
            $updateData['latitude']  = $locationStage['latitude']  ?? null;
            $updateData['longitude'] = $locationStage['longitude'] ?? null;
        }

        if ($request->hasFile('photo')) {
            if ($event->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($event->photo);
            }
            $ext = $request->file('photo')->getClientOriginalExtension();
            $updateData['photo'] = $request->file('photo')->storeAs('events', \Illuminate\Support\Str::random(40) . '.' . $ext, 'public');
        }

        $event->update($updateData);

        // B. Actualitzem les Rutes / Etapes
        $event->routes()->detach();
        foreach ($stages as $index => $stage) {
            if (($stage['type'] ?? '') === 'route' && !empty($stage['route_id'])) {
                $event->routes()->attach($stage['route_id'], ['day_order' => $index + 1]);
            }
        }

        return redirect()->route('events.mine');
    }
}
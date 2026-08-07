<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Route;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class EventController extends Controller
{
// INDEX: una sola pantalla amb segments (Meves | Descobrir), com Rutes
    public function index(Request $request)
    {
        $userId = Auth::id();

        $decorate = function ($event) use ($userId) {
            $event->is_attending = $event->participants->contains($userId);
            $event->is_organizer = (int) $event->user_id === (int) $userId;
            $event->participants_count = $event->participants->count();
            $event->routes_count = $event->routes->count();
            $event->total_km = round((float) $event->routes->sum('planned_distance_km'), 1);

            // Llista densa: no cal enviar participants/routes sencers
            $event->unsetRelation('participants');
            $event->unsetRelation('routes');

            return $event;
        };

        $myEvents = Event::with(['organizer:id,name', 'participants:id', 'routes:id,planned_distance_km'])
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhereHas('participants', fn ($q) => $q->where('user_id', $userId));
            })
            ->where('start_time', '>=', now()->subDay())
            ->orderBy('start_time', 'asc')
            ->get()
            ->map($decorate)
            ->values();

        $discoverEvents = Event::with(['organizer:id,name', 'participants:id', 'routes:id,planned_distance_km'])
            ->where('is_public', true)
            ->where('start_time', '>=', now()->subDay())
            ->where('user_id', '!=', $userId)
            ->whereDoesntHave('participants', fn ($q) => $q->where('user_id', $userId))
            ->orderBy('start_time', 'asc')
            ->get()
            ->map($decorate)
            ->values();

        $next = $myEvents->first();

        return Inertia::render('Events/Index', [
            'myEvents'       => $myEvents,
            'discoverEvents' => $discoverEvents,
            'initialTab'     => $request->query('tab', 'mine'),
            'nextEvent'      => $next ? [
                'id'         => $next->id,
                'title'      => $next->title,
                'start_time' => $next->start_time,
                'location'   => $next->location,
            ] : null,
        ]);
    }

    // Redirect: bookmarks antics de /my-events
    public function myEvents()
    {
        return redirect()->route('events.index', ['tab' => 'mine']);
    }

    // NOVA FUNCIÓ: PREVISUALITZAR EVENT VIA ENLLAÇ (Guest/Public)
    public function preview(Request $request, $token)
    {
        $event = Event::where('share_token', $token)->firstOrFail();
        $event->load(['organizer', 'routes.waypoints', 'participants']);

        if (Auth::check()) {
            $event->is_attending = $event->participants->contains(Auth::id());
        } else {
            $event->is_attending = false;
        }
        $event->participants_count = $event->participants->count();

        if (!$request->boolean('web') && !Auth::check()) {
            $webUrl = route('events.preview', ['token' => $token, 'web' => 1]);

            return Inertia::render('Shared/OpenInApp', [
                'title' => 'Obre la quedada amb Clutch Control',
                'subtitle' => 'Aquesta quedada s\'ha compartit per enllac. Instal·la l\'app o obre-la al navegador.',
                'webUrl' => $webUrl,
                'deepLinkUrl' => config('services.app_links.deep_link_base') . '/e/' . $token,
                'androidStoreUrl' => config('services.app_links.android_store_url'),
                'iosStoreUrl' => config('services.app_links.ios_store_url'),
                'openAppLabel' => 'Obrir app',
                'openWebLabel' => 'Continuar en web',
            ]);
        }

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
        $request->merge([
            'description' => (($v = $request->input('description')) === '' || $v === null) ? null : $v,
            'max_participants' => (($v = $request->input('max_participants')) === '' || $v === null) ? null : $v,
        ]);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'start_time' => 'required|date|after_or_equal:today',
            'is_public' => 'boolean',
            'max_participants' => 'nullable|integer|min:2|max:999',

            'stages' => 'required|array|min:1',
            'stages.*.type' => 'required|in:route,location',
            'stages.*.route_id' => 'nullable|integer|exists:routes,id',
            'stages.*.location_name' => 'nullable|string|max:255',
            'stages.*.latitude' => 'nullable|numeric|between:-90,90',
            'stages.*.longitude' => 'nullable|numeric|between:-180,180',

            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'chat_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'title.required' => 'El títol de la quedada és obligatori.',
            'start_time.required' => 'Cal indicar la data i hora de la quedada.',
            'start_time.after_or_equal' => 'La data de la quedada no pot ser anterior a avui.',
            'max_participants.min' => 'El límit de motoristes ha de ser com a mínim 2.',
            'stages.required' => 'Has d\'afegir com a mínim una etapa (lloc de trobada o ruta).',
            'stages.min' => 'Has d\'afegir com a mínim una etapa (lloc de trobada o ruta).',
            'photo.image' => 'La foto de la quedada no és una imatge vàlida.',
            'chat_photo.image' => 'La foto del xat no és una imatge vàlida.',
        ]);

        $validator->after(function ($v) use ($request) {
            foreach ((array) $request->input('stages', []) as $i => $stage) {
                $type = $stage['type'] ?? null;
                $human = $i + 1;

                if ($type === 'route' && empty($stage['route_id'])) {
                    $v->errors()->add(
                        "stages.$i.route_id",
                        "Etapa $human: cal seleccionar una ruta del teu garatge."
                    );
                }

                if ($type === 'location' && trim((string) ($stage['location_name'] ?? '')) === '') {
                    $v->errors()->add(
                        "stages.$i.location_name",
                        "Etapa $human: cal indicar el nom del lloc de trobada."
                    );
                }
            }
        });

        $validated = $validator->validate();

        $eventColumns = array_flip(Schema::getColumnListing('events'));
        $eventData = collect([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_time' => $validated['start_time'],
            'is_public' => $validated['is_public'] ?? true,
            'max_participants' => $validated['max_participants'] ?? null,
        ])->filter(fn ($value, $key) => isset($eventColumns[$key]))->all();

        if ($request->hasFile('photo')) {
            $ext = $request->file('photo')->getClientOriginalExtension();
            if (isset($eventColumns['photo'])) {
                $eventData['photo'] = $request->file('photo')->storeAs('events', \Illuminate\Support\Str::random(40) . '.' . $ext, 'public');
            }
        }

        // ... (Codi per calcular la ubicació inicial igual que abans) ...
        $stages = $validated['stages'] ?? [];
        if (!empty($stages)) {
            $first = $stages[0];
            if ($first['type'] === 'route' && $first['route_id']) {
                $r = Route::find($first['route_id']);
                if ($r && isset($eventColumns['location'])) {
                    $eventData['location'] = $r->title;
                }
            } else {
                if (isset($eventColumns['location'])) {
                    $eventData['location'] = $first['location_name'] ?? null;
                }
                if (isset($eventColumns['latitude'])) {
                    $eventData['latitude'] = $first['latitude'] ?? null;
                }
                if (isset($eventColumns['longitude'])) {
                    $eventData['longitude'] = $first['longitude'] ?? null;
                }
            }
        }

        $event = Event::create($eventData);

        // 3. ESTAT: CONFIRMED (Arreglem també el 'going' aquí)
        if (Schema::hasTable('event_participants')) {
            $event->participants()->attach(Auth::id(), ['status' => 'confirmed']);
        }

        // 3b. CREAR GRUP DE XAT PER A LA QUEDADA
        if (Schema::hasTable('conversations') && Schema::hasTable('conversation_user')) {
            $conversationColumns = array_flip(Schema::getColumnListing('conversations'));

            if (isset($conversationColumns['type'], $conversationColumns['name'])) {
                $chatData = [
                    'type' => 'group',
                    'name' => $event->title,
                ];

                if (isset($conversationColumns['event_id'])) {
                    $chatData['event_id'] = $event->id;
                }

                if ($request->hasFile('chat_photo') && isset($conversationColumns['photo'])) {
                    $ext = $request->file('chat_photo')->getClientOriginalExtension();
                    $chatData['photo'] = $request->file('chat_photo')->storeAs('chats', \Illuminate\Support\Str::random(40) . '.' . $ext, 'public');
                }

                $groupChat = Conversation::create($chatData);
                $groupChat->participants()->attach(Auth::id());
            }
        }

        // 4. GUARDEM RUTES
        if (Schema::hasTable('event_routes') && !empty($stages)) {
            foreach ($stages as $index => $stage) {
                if ($stage['type'] === 'route' && $stage['route_id']) {
                    $event->routes()->attach($stage['route_id'], ['day_order' => $index + 1]);
                }
            }
        }

        return redirect()->route('events.index', ['tab' => 'mine']);
        }

    // 4. VEURE DETALL (SHOW)
    public function show(Event $event)
        {
            // SEGURETAT: aquest endpoint accepta l'ID numeric. Si la quedada
            // no es publica nomes hi pot accedir el creador, un participant
            // o un admin. La resta han d'usar el share_token (/e/{token}).
            $user = Auth::user();
            $isOwner = $user && (int) $event->user_id === (int) $user->id;
            $isAdmin = $user && ($user->role ?? null) === 'admin';
            $isParticipant = $user
                ? $event->participants()->where('users.id', $user->id)->exists()
                : false;

            if (!$event->is_public && !$isOwner && !$isAdmin && !$isParticipant) {
                abort(403, 'Aquesta quedada es privada.');
            }

            // Backfill del token compartible si no existia (events antics).
            if (Schema::hasColumn('events', 'share_token') && empty($event->share_token)) {
                $event->share_token = \Illuminate\Support\Str::random(12);
                $event->save();
            }

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

            return redirect()->route('events.index', ['tab' => 'mine']);
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
        
        $groupChat = Conversation::where('type', 'group')->where('event_id', $event->id)->first();
        $event->chat_photo = $groupChat?->photo;

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

        $currentParticipants = $event->participants()->count();
        $minParticipants = max(1, $currentParticipants);

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'start_time'     => 'required|date',
            'location'       => 'nullable|string',
            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
            'is_public'      => 'boolean',
            'max_participants' => 'nullable|integer|min:' . $minParticipants,
            'stages_json'    => 'nullable|string', 
            'photo'          => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'chat_photo'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_chat_photo' => 'nullable|boolean',
        ], [
            'max_participants.min' => 'El límit d\'assistents no pot ser inferior als ja apuntats (' . $currentParticipants . ').'
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

        // A.bis. Actualitzem el grup de xat (nom i foto)
        $groupChat = Conversation::where('type', 'group')->where('event_id', $event->id)->first();
        if ($groupChat) {
            $chatUpdate = ['name' => $event->title];

            if ($request->hasFile('chat_photo')) {
                if ($groupChat->photo) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($groupChat->photo);
                }
                $ext = $request->file('chat_photo')->getClientOriginalExtension();
                $chatUpdate['photo'] = $request->file('chat_photo')->storeAs('chats', \Illuminate\Support\Str::random(40) . '.' . $ext, 'public');
            } elseif ($request->boolean('remove_chat_photo') && $groupChat->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($groupChat->photo);
                $chatUpdate['photo'] = null;
            }

            $groupChat->update($chatUpdate);
        }

        // B. Actualitzem les Rutes / Etapes
        $event->routes()->detach();
        foreach ($stages as $index => $stage) {
            if (($stage['type'] ?? '') === 'route' && !empty($stage['route_id'])) {
                $event->routes()->attach($stage['route_id'], ['day_order' => $index + 1]);
            }
        }

        return redirect()->route('events.show', $event);
    }
}
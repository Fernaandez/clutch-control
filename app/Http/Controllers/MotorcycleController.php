<?php

namespace App\Http\Controllers;

use App\Models\Motorcycle;
use App\Models\SaleListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class MotorcycleController extends Controller
{
    public function index()
    {
        $motos = Motorcycle::where('user_id', Auth::id())->get();
        return Inertia::render('Motorcycles/Index', ['motos' => $motos]);
    }

    
    public function create()
    {
        return Inertia::render('Motorcycles/Create');
    }

    // 3. STORE: Guarda la moto
    public function store(Request $request)
    {
        // Multipart: "" -> null; conservar 0 vàlid per cc / power_cv
        $request->merge([
            'cc' => (($v = $request->input('cc')) === '' || $v === null) ? null : $v,
            'power_cv' => (($v = $request->input('power_cv')) === '' || $v === null) ? null : $v,
            'license_type' => (($v = $request->input('license_type')) === '' || $v === null) ? null : $v,
            'type' => (($v = $request->input('type')) === '' || $v === null) ? null : $v,
            'extras' => (($v = $request->input('extras')) === '' || $v === null) ? null : $v,
            'insurance_company' => (($v = $request->input('insurance_company')) === '' || $v === null) ? null : $v,
            'insurance_policy_number' => (($v = $request->input('insurance_policy_number')) === '' || $v === null) ? null : $v,
            'insurance_expires_at' => (($v = $request->input('insurance_expires_at')) === '' || $v === null) ? null : $v,
            'itv_expires_at' => (($v = $request->input('itv_expires_at')) === '' || $v === null) ? null : $v,
            'itv_last_passed_at' => (($v = $request->input('itv_last_passed_at')) === '' || $v === null) ? null : $v,
        ]);

        // Validem exactament els mateixos camps que envia el Vue
        $validated = $request->validate([
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year'  => 'required|integer|min:1900|max:2100',
            'current_km' => 'required|numeric|min:0',
            
            // Camps opcionals de la moto (TOTS nullable)
            'cc' => 'nullable|integer|min:0',
            'power_cv' => 'nullable|integer|min:0',
            'license_type' => 'nullable|string|in:AM,A1,A2,A',
            'type' => 'nullable|string|in:Naked,Sport,Trail,Custom,Scooter,Touring,Off-Road,Classic',
            'extras' => 'nullable|string|max:1000',
            'insurance_company' => 'nullable|string|max:100',
            'insurance_policy_number' => 'nullable|string|max:100',
            'insurance_expires_at' => 'nullable|date',
            'itv_expires_at' => 'nullable|date|after:today',
            'itv_last_passed_at' => 'nullable|date|before_or_equal:today',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Guardem només columnes que existeixen realment a la BD local.
        // Això evita 500 si alguna migració antiga encara no s'ha executat.
        $motorcycleColumns = array_flip(Schema::getColumnListing('motorcycles'));
        $data = collect($validated)
            ->only(['brand', 'model', 'year', 'current_km', 'cc', 'power_cv', 'license_type', 'type', 'extras', 'insurance_company', 'insurance_policy_number', 'insurance_expires_at', 'itv_expires_at', 'itv_last_passed_at'])
            ->filter(fn ($value, $key) => isset($motorcycleColumns[$key]))
            ->all();
        $data['user_id'] = Auth::id();

        if ($request->hasFile('photo')) {
            $ext = $request->file('photo')->getClientOriginalExtension() ?: $request->file('photo')->guessExtension() ?: 'jpg';
            if (isset($motorcycleColumns['photo'])) {
                $data['photo'] = $request->file('photo')->storeAs('motorcycles', \Illuminate\Support\Str::random(40) . '.' . $ext, 'public');
            }
        }

        Motorcycle::create($data);

        return redirect()->route('motorcycles.index');
    }

    public function edit(Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403, 'Aquesta moto no és teva!'); }
        return Inertia::render('Motorcycles/Edit', ['moto' => $motorcycle]);
    }

    public function update(Request $request, Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }

        $validated = $request->validate([
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year'  => 'required|integer',
            'current_km' => 'required|numeric|min:0',
            'cc' => 'nullable|integer|min:0',
            'power_cv' => 'nullable|integer|min:0',
            'license_type' => 'nullable|string|in:AM,A1,A2,A',
            'type' => 'nullable|string|in:Naked,Sport,Trail,Custom,Scooter,Touring,Off-Road,Classic',
            'extras' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $validated;
        
        if ($request->hasFile('photo')) {
            if ($motorcycle->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($motorcycle->photo);
            }
            $ext = $request->file('photo')->getClientOriginalExtension();
            $data['photo'] = $request->file('photo')->storeAs('motorcycles', \Illuminate\Support\Str::random(40) . '.' . $ext, 'public');
        }

        $motorcycle->update($data);

        return redirect()->route('motorcycles.index');
    }

    public function destroy(Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }
        
        // Esborrem foto principal de la moto
        if ($motorcycle->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($motorcycle->photo);
        }

        // Esborrem fotos secundàries de l'anunci de venda (si en té) per evitar brossa al servidor
        if ($motorcycle->saleListing) {
            foreach ($motorcycle->saleListing->images as $image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
            }
        }

        $motorcycle->delete();
        return redirect()->route('motorcycles.index');
    }

    public function dashboard(Motorcycle $motorcycle = null)
    {
        /** @var \App\Models\User $user */ 
        $user = Auth::user();

        if (!$motorcycle) {
            if ($user->last_motorcycle_id) {
                $motorcycle = Motorcycle::find($user->last_motorcycle_id);
            }
            if (!$motorcycle) {
                $motorcycle = $user->motorcycles()->first();
            }
        }

        if (!$motorcycle) {
            return redirect()->route('motorcycles.index');
        }

        if ($motorcycle->user_id !== Auth::id()) { abort(403); }

        if ($user->last_motorcycle_id !== $motorcycle->id) {
            $user->last_motorcycle_id = $motorcycle->id;
            $user->save();
        }

        return Inertia::render('Dashboard', [
            'moto'  => $motorcycle,
            'pulse' => $this->motorcyclePulse($motorcycle, $user),
        ]);
    }

    /**
     * Estat viu de la moto: què reclama atenció, què has fet i què ve.
     * La pantalla principal mostra contingut, no un menú, i necessita aquestes dades.
     */
    private function motorcyclePulse(Motorcycle $motorcycle, $user): array
    {
        $km = (float) ($motorcycle->current_km ?? 0);

        // Tasca de manteniment més urgent (la que ha passat més de la seva freqüència)
        $nextTask = $motorcycle->maintenanceTasks()
            ->whereNotNull('frequency_km')
            ->where('frequency_km', '>', 0)
            ->get()
            ->map(function ($task) use ($km) {
                $due = (float) ($task->last_km_done ?? 0) + (float) $task->frequency_km;

                return [
                    'title'   => $task->title,
                    'km_left' => (int) round($due - $km),
                ];
            })
            ->sortBy('km_left')
            ->first();

        $lastTrip = \App\Models\Trip::where('user_id', $user->id)
            ->where('motorcycle_id', $motorcycle->id)
            ->orderByDesc('started_at')
            ->first();

        $nextEvent = \App\Models\Event::whereHas('participants', fn ($q) => $q->where('user_id', $user->id))
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->first();

        return [
            'next_task'  => $nextTask,
            'last_trip'  => $lastTrip ? [
                'id'          => $lastTrip->id,
                'distance_km' => round((float) $lastTrip->distance_km),
                'started_at'  => $lastTrip->started_at,
                'route_title' => $lastTrip->route?->title,
            ] : null,
            'next_event' => $nextEvent ? [
                'id'         => $nextEvent->id,
                'title'      => $nextEvent->title,
                'start_time' => $nextEvent->start_time,
            ] : null,
            'total_spent' => (float) $motorcycle->maintenanceLogs()->sum('cost'),
            'logs_count'  => $motorcycle->maintenanceLogs()->count(),
            'counts'      => [
                'maintenance' => $motorcycle->maintenanceTasks()->where('type', 'maintenance')->count(),
                'repair'      => $motorcycle->maintenanceTasks()->where('type', 'repair')->count(),
                'upgrade'     => $motorcycle->maintenanceTasks()->where('type', 'upgrade')->count(),
            ],
        ];
    }

    public function documentation(Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }

        if ($motorcycle->itv_expires_at) {
            $motorcycle->update(['doc_alert_acknowledged_for' => $motorcycle->itv_expires_at]);
            $motorcycle->refresh();
        }

        return Inertia::render('Motorcycles/Documentation', [
            'moto' => $motorcycle,
            'otherExpirations' => $this->otherExpirations(Auth::user(), $motorcycle->id),
        ]);
    }

    public function documentationEdit(Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }

        return Inertia::render('Motorcycles/DocumentationEdit', ['moto' => $motorcycle]);
    }

    public function documentationUpdate(Request $request, Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }

        $request->merge([
            'insurance_company' => (($v = $request->input('insurance_company')) === '' || $v === null) ? null : $v,
            'insurance_policy_number' => (($v = $request->input('insurance_policy_number')) === '' || $v === null) ? null : $v,
            'insurance_expires_at' => (($v = $request->input('insurance_expires_at')) === '' || $v === null) ? null : $v,
            'itv_expires_at' => (($v = $request->input('itv_expires_at')) === '' || $v === null) ? null : $v,
            'itv_last_passed_at' => (($v = $request->input('itv_last_passed_at')) === '' || $v === null) ? null : $v,
        ]);

        $validated = $request->validate($this->documentationRules());
        $motorcycle->update($validated);

        return redirect()->route('motorcycles.documentation.show', $motorcycle);
    }

    private function documentationRules(): array
    {
        return [
            'insurance_company' => 'nullable|string|max:100',
            'insurance_policy_number' => 'nullable|string|max:100',
            'insurance_expires_at' => 'nullable|date',
            'itv_expires_at' => 'nullable|date|after:today',
            'itv_last_passed_at' => 'nullable|date|before_or_equal:today',
        ];
    }

    public function itvRenewedToday(Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }

        $today = now()->startOfDay();
        $motorcycle->update([
            'itv_last_passed_at' => $today,
            'itv_expires_at' => $today->copy()->addYear(),
            'doc_alert_acknowledged_for' => $today->copy()->addYear(),
        ]);

        return redirect()->route('motorcycles.documentation.show', $motorcycle);
    }

    private function otherExpirations($user, int $excludeMotoId): array
    {
        return Motorcycle::where('user_id', $user->id)
            ->where('id', '!=', $excludeMotoId)
            ->get()
            ->flatMap(function (Motorcycle $moto) {
                if (!$moto->itv_expires_at || !in_array($moto->itv_status, ['expiring_soon', 'expired'], true)) {
                    return [];
                }

                return [[
                    'motorcycle_id' => $moto->id,
                    'brand' => $moto->brand,
                    'model' => $moto->model,
                    'type' => 'itv',
                    'expires_at' => $moto->itv_expires_at->format('Y-m-d'),
                    'status' => $moto->itv_status,
                ]];
            })
            ->sortBy('expires_at')
            ->values()
            ->all();
    }

    public function addKm(Request $request, Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }
        $request->validate(['km_to_add' => 'required|numeric|min:0.1']);
        $motorcycle->current_km += $request->km_to_add;
        $motorcycle->save();
        return back();
    }
}
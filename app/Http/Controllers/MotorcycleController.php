<?php

namespace App\Http\Controllers;

use App\Models\Motorcycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MotorcycleController extends Controller
{
    // 1. INDEX: Mostra la llista
    public function index()
    {
        $motos = Motorcycle::where('user_id', Auth::id())->get();

        return Inertia::render('Motorcycles/Index', [
            'motos' => $motos
        ]);
    }

    // 2. CREATE: Mostra el formulari buit
    public function create()
    {
        return Inertia::render('Motorcycles/Create');
    }

    // 3. STORE: Guarda la moto
    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'plate' => 'required|string|max:15',
            'year'  => 'required|integer',
            'current_km' => 'required|numeric|min:0',
        ]);

        $moto = new Motorcycle();
        $moto->user_id = Auth::id();
        $moto->brand = $validated['brand'];
        $moto->model = $validated['model'];
        $moto->plate = $validated['plate'];
        $moto->year = $validated['year'];
        $moto->current_km = $validated['current_km'];
        $moto->save();

        // REDIRECCIÓ: Un cop guardat, tornem a la llista
        return redirect()->route('motorcycles.index');
    }

    // 4. EDIT: Mostra el formulari per editar
    public function edit(Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) {
            abort(403, 'Aquesta moto no és teva!');
        }

        return Inertia::render('Motorcycles/Edit', [
            'moto' => $motorcycle
        ]);
    }

    // 5. UPDATE: Guarda els canvis
    public function update(Request $request, Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }

        $validated = $request->validate([
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'plate' => 'required|string|max:15', 
            'year'  => 'required|integer',
            'current_km' => 'required|numeric|min:0',
        ]);

        $motorcycle->update($validated);

        return redirect()->route('motorcycles.index');
    }

    // 6. DESTROY: Eliminar la moto
    public function destroy(Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }

        $motorcycle->delete();

        return redirect()->route('motorcycles.index');
    }

    // --- DASHBOARD (AMB LÒGICA DE PERSISTÈNCIA) ---
// --- DASHBOARD (AMB LÒGICA DE PERSISTÈNCIA) ---
    public function dashboard(Motorcycle $motorcycle = null)
    {
        /** @var \App\Models\User $user */ // <--- AQUESTA LÍNIA ARREGLA ELS ERRORS VISUALS
        $user = Auth::user();

        // 1. Si no ens passem moto, intentem recuperar l'última visitada o la primera
        if (!$motorcycle) {
            if ($user->last_motorcycle_id) {
                // Intentem buscar la de la memòria
                $motorcycle = Motorcycle::find($user->last_motorcycle_id);
            }
            
            // Si no tenim memòria (o s'ha esborrat), agafem la primera de la llista
            if (!$motorcycle) {
                $motorcycle = $user->motorcycles()->first();
            }
        }

        // 2. Si l'usuari NO té cap moto, l'enviem al Garatge
        if (!$motorcycle) {
            return redirect()->route('motorcycles.index');
        }

        // 3. Seguretat de propietat
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }

        // 4. GUARDEM QUE AQUESTA ÉS L'ÚLTIMA MOTO VISITADA (Persistència)
        if ($user->last_motorcycle_id !== $motorcycle->id) {
            $user->last_motorcycle_id = $motorcycle->id;
            $user->save();
        }

        return Inertia::render('Dashboard', [
            'moto' => $motorcycle
        ]);
    }

    // Funció per SUMAR km
    public function addKm(Request $request, Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }

        $request->validate([
            'km_to_add' => 'required|numeric|min:0.1',
        ]);

        $motorcycle->current_km += $request->km_to_add;
        $motorcycle->save();

        return back();
    }
}
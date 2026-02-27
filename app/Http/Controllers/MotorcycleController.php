<?php

namespace App\Http\Controllers;

use App\Models\Motorcycle;
use App\Models\SaleListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // Validem exactament els mateixos camps que envia el Vue
        $validated = $request->validate([
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            // Matícula ara és opcional (ciclomotors poden tenir formats locals o no tenir-ne)
            'plate' => 'nullable|string|max:15|unique:motorcycles,plate',
            'year'  => 'required|integer',
            'current_km' => 'required|numeric|min:0',
            
            // Camps opcionals de la moto (TOTS nullable)
            'cc' => 'nullable|integer|min:0',
            'power_cv' => 'nullable|integer|min:0',
            'license_type' => 'nullable|string',
            'type' => 'nullable|string',
            'extras' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'plate.unique' => 'Aquesta matrícula ja està registrada al sistema.'
        ]);

        // Guardem la moto a la base de dades (Format Anti-Errors VS Code)
        $data = $validated;
        $data['user_id'] = Auth::id(); // Assignem el propietari

        if ($request->hasFile('photo')) {
            $ext = $request->file('photo')->getClientOriginalExtension();
            $data['photo'] = $request->file('photo')->storeAs('motorcycles', \Illuminate\Support\Str::random(40) . '.' . $ext, 'public');
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
            // nullable + ignore propi id per no fallar en update
            'plate' => ['nullable', 'string', 'max:15', \Illuminate\Validation\Rule::unique('motorcycles', 'plate')->ignore($motorcycle->id)],
            'year'  => 'required|integer',
            'current_km' => 'required|numeric|min:0',
            'cc' => 'nullable|integer|min:0',
            'power_cv' => 'nullable|integer|min:0',
            'license_type' => 'nullable|string|in:AM,A1,A2,A',
            'type' => 'nullable|string|in:Naked,Sport,Trail,Custom,Scooter,Touring,Off-Road,Classic',
            'extras' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'plate.unique' => 'Aquesta matrícula ja està registrada per una altra moto.'
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

        return Inertia::render('Dashboard', ['moto' => $motorcycle]);
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
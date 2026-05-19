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
            'insurance_expires_at' => 'nullable|date|after:today',
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

        if (isset($motorcycleColumns['plate'])) {
            $data['plate'] = 'SENSE-' . Auth::id() . '-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8));
        }

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

        $request->merge([
            'insurance_company' => (($v = $request->input('insurance_company')) === '' || $v === null) ? null : $v,
            'insurance_policy_number' => (($v = $request->input('insurance_policy_number')) === '' || $v === null) ? null : $v,
            'insurance_expires_at' => (($v = $request->input('insurance_expires_at')) === '' || $v === null) ? null : $v,
            'itv_expires_at' => (($v = $request->input('itv_expires_at')) === '' || $v === null) ? null : $v,
            'itv_last_passed_at' => (($v = $request->input('itv_last_passed_at')) === '' || $v === null) ? null : $v,
        ]);

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
            'insurance_company' => 'nullable|string|max:100',
            'insurance_policy_number' => 'nullable|string|max:100',
            'insurance_expires_at' => 'nullable|date|after:today',
            'itv_expires_at' => 'nullable|date|after:today',
            'itv_last_passed_at' => 'nullable|date|before_or_equal:today',
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

        return Inertia::render('Dashboard', ['moto' => $motorcycle]);
    }

    public function documentation(Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }

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
            'insurance_expires_at' => 'nullable|date|after:today',
            'itv_expires_at' => 'nullable|date|after:today',
            'itv_last_passed_at' => 'nullable|date|before_or_equal:today',
        ];
    }

    private function otherExpirations($user, int $excludeMotoId): array
    {
        return Motorcycle::where('user_id', $user->id)
            ->where('id', '!=', $excludeMotoId)
            ->get()
            ->flatMap(function (Motorcycle $moto) {
                $items = [];

                foreach (['insurance' => 'insurance_expires_at', 'itv' => 'itv_expires_at'] as $type => $field) {
                    $status = $type === 'insurance' ? $moto->insurance_status : $moto->itv_status;
                    if ($moto->{$field} && in_array($status, ['expiring_soon', 'expired'], true)) {
                        $items[] = [
                            'motorcycle_id' => $moto->id,
                            'brand' => $moto->brand,
                            'model' => $moto->model,
                            'type' => $type,
                            'expires_at' => $moto->{$field}->format('Y-m-d'),
                            'status' => $status,
                        ];
                    }
                }

                return $items;
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
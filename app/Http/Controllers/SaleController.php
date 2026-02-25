<?php

namespace App\Http\Controllers;

use App\Models\SaleListing;
use App\Models\Motorcycle;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    // 1. EL MUR (Totes les motos a la venda de la comunitat)
    // 1. APARADOR (Llistat d'anuncis amb FILTRES)
    public function index(Request $request)
    {
        // Comencem la consulta: només anuncis actius i no venuts, amb la info de la moto i l'usuari
        $query = SaleListing::with(['motorcycle.user'])
            ->where('is_active', true)
            ->where('is_sold', false);

        // --- FILTRES DE L'ANUNCI ---
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // --- FILTRES DE LA MOTO ---
        // Utilitzem 'whereHas' per filtrar per columnes que estan a la taula 'motorcycles'
        $query->whereHas('motorcycle', function ($q) use ($request) {
            
            if ($request->filled('brand')) {
                $q->where('brand', 'like', '%' . $request->brand . '%');
            }
            if ($request->filled('type')) {
                $q->where('type', $request->type);
            }
            if ($request->filled('license_type')) {
                $q->where('license_type', $request->license_type);
            }
            if ($request->filled('min_cc')) {
                $q->where('cc', '>=', $request->min_cc);
            }
            if ($request->filled('max_cc')) {
                $q->where('cc', '<=', $request->max_cc);
            }
        });

        // Ordenem pels més recents i fem un paginat (o get si les vols totes de cop)
        $sales = $query->orderBy('created_at', 'desc')->get();

        return Inertia::render('Sales/Index', [
            'sales' => $sales,
            'filters' => $request->all(), // Enviem els filtres actuals de tornada a Vue perquè no s'esborrin del formulari
        ]);
    }

    // 2. ELS MEUS ANUNCIS
    // 2. ELS MEUS ANUNCIS (Consulta a prova de bales)
    public function mine()
    {
        // 1. Agafem les IDs exactes de totes les teves motos
        $misMotosIds = Motorcycle::where('user_id', Auth::id())->pluck('id');

        // 2. Busquem els anuncis que pertanyin a aquestes motos
        $sales = SaleListing::with('motorcycle')
            ->whereIn('motorcycle_id', $misMotosIds)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Sales/MySales', ['sales' => $sales]);
    }

    // 3. FORMULARI CREAR
    public function create()
    {
        // En lloc de preguntar-li a l'usuari quines motos té, 
        // li preguntem directament al model de Motos (Així VS Code no es queixa)
        $availableMotorcycles = Motorcycle::where('user_id', Auth::id())
            ->whereDoesntHave('saleListing')
            ->get();

        return Inertia::render('Sales/Create', [
            'motorcycles' => $availableMotorcycles
        ]);
    }

    // 4. GUARDAR ANUNCI
    public function store(Request $request)
    {
        $validated = $request->validate([
            'motorcycle_id' => 'required|exists:motorcycles,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
        ]);

        // Igual aquí, ho busquem directament al model Motorcycle
        $moto = Motorcycle::where('user_id', Auth::id())
            ->findOrFail($validated['motorcycle_id']);

        SaleListing::create([
            'motorcycle_id' => $moto->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'location' => $validated['location'],
            'is_active' => true,
            'is_sold' => false,
        ]);

        return redirect()->route('sales.mine');
    }

    // 5. VEURE DETALL DE L'ANUNCI
    public function show(SaleListing $sale)
    {
        $sale->load(['motorcycle.user']); // Carreguem info de la moto i el venedor
        return Inertia::render('Sales/Show', ['sale' => $sale]);
    }

    // 6. EDITAR
    public function edit(SaleListing $sale)
    {
        // Comprovem que sigui el propietari de la moto
        if ($sale->motorcycle->user_id !== Auth::id()) { abort(403); }

        return Inertia::render('Sales/Edit', ['sale' => $sale->load('motorcycle')]);
    }

    // 7. ACTUALITZAR
    // 7. ACTUALITZAR ANUNCI I MOTO ALHORA
    public function update(Request $request, SaleListing $sale)
    {
        if ($sale->motorcycle->user_id !== Auth::id()) { abort(403); }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'is_active' => 'boolean',
            'is_sold' => 'boolean',
            
            // Validem també els camps tècnics de la moto (Sense l'ABS)
            'cc' => 'nullable|integer|min:0',
            'power_cv' => 'nullable|integer|min:0',
            'license_type' => 'nullable|string|in:AM,A1,A2,A',
            'type' => 'nullable|string|in:Naked,Sport,Trail,Custom,Scooter,Touring,Off-Road,Classic',
            'extras' => 'nullable|string|max:1000',
        ]);

        // 1. Actualitzem l'anunci
        $sale->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'location' => $validated['location'],
            'is_active' => $validated['is_active'],
            'is_sold' => $validated['is_sold'],
        ]);

        // 2. Actualitzem les dades tècniques de la moto
        $sale->motorcycle->update([
            'cc' => $validated['cc'],
            'power_cv' => $validated['power_cv'],
            'license_type' => $validated['license_type'],
            'type' => $validated['type'],
            'extras' => $validated['extras'],
        ]);

        return redirect()->route('sales.mine');
    }

    // 8. ESBORRAR ANUNCI
    public function destroy(SaleListing $sale)
    {
        if ($sale->motorcycle->user_id !== Auth::id()) { abort(403); }
        $sale->delete();
        return redirect()->route('sales.mine');
    }
}
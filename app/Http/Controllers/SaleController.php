<?php

namespace App\Http\Controllers;

use App\Models\SaleListing;
use App\Models\SaleImage;
use App\Models\Motorcycle;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    // 1. APARADOR (Llistat d'anuncis amb filtres frontend)
    public function index(Request $request)
    {
        $sales = SaleListing::with(['motorcycle', 'images'])
            ->withCount('favoritedBy')
            ->where('is_active', true)
            ->where('is_sold', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($sale) {
                $sale->is_favorited = $sale->isFavoritedBy(Auth::user());
                return $sale;
            });

        return Inertia::render('Sales/Index', ['sales' => $sales]);
    }

    // 2. ELS MEUS ANUNCIS
    public function mine()
    {
        $myMotoIds = Motorcycle::where('user_id', Auth::id())->pluck('id');

        $sales = SaleListing::with(['motorcycle', 'images'])
            ->withCount('favoritedBy')
            ->whereIn('motorcycle_id', $myMotoIds)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Sales/MySales', ['sales' => $sales]);
    }

    // 3. FORMULARI CREAR
    public function create()
    {
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
            'motorcycle_id'  => 'required|exists:motorcycles,id',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'location'       => 'required|string|max:255',
            // Dades tècniques de la moto
            'cc'             => 'nullable|integer|min:0',
            'power_cv'       => 'nullable|integer|min:0',
            'license_type'   => 'nullable|string|in:AM,A1,A2,A',
            'type'           => 'nullable|string|in:Naked,Sport,Trail,Custom,Scooter,Touring,Off-Road,Classic',
            'has_abs'        => 'boolean',
            'extras'         => 'nullable|string|max:1000',
            // Fotos
            'images'         => 'nullable|array|max:8',
            'images.*'       => 'image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        // Seguretat: la moto ha de ser de l'usuari
        $moto = Motorcycle::where('user_id', Auth::id())->findOrFail($validated['motorcycle_id']);

        // Validació de contacte: ha de tenir telèfon o email al perfil
        $user = Auth::user();
        if (!$user->phone_number && !$user->email) {
            return back()->withErrors(['contact' => 'Necessites un telèfon o email al teu perfil per publicar un anunci.']);
        }

        // Crear l'anunci
        $sale = SaleListing::create([
            'motorcycle_id' => $moto->id,
            'title'         => $validated['title'],
            'description'   => $validated['description'],
            'price'         => $validated['price'],
            'location'      => $validated['location'],
            'is_active'     => true,
            'is_sold'       => false,
        ]);

        // Actualitzar dades tècniques de la moto
        $moto->update([
            'cc'           => $validated['cc'] ?? $moto->cc,
            'power_cv'     => $validated['power_cv'] ?? $moto->power_cv,
            'license_type' => $validated['license_type'] ?? $moto->license_type,
            'type'         => $validated['type'] ?? $moto->type,
            'has_abs'      => $request->boolean('has_abs'),
            'extras'       => $validated['extras'] ?? $moto->extras,
        ]);

        // Guardar fotos
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $ext  = $image->getClientOriginalExtension();
                $path = $image->storeAs('sales', Str::random(40) . '.' . $ext, 'public');
                SaleImage::create(['sale_listing_id' => $sale->id, 'image_path' => $path]);
            }
        }

        return redirect()->route('sales.mine');
    }

    // 5. VEURE DETALL
    public function show(SaleListing $sale)
    {
        // Augmentem vistes si no és l'amo i només un cop per compte/IP
        if (!Auth::check() || $sale->motorcycle->user_id !== Auth::id()) {
            $viewerKey = Auth::check() ? 'user_' . Auth::id() : 'ip_' . request()->ip();
            $cacheKey = 'viewed_sale_' . $sale->id . '_' . $viewerKey;
            
            if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                $sale->increment('views_count');
                \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addDays(30));
            }
        }

        $sale->load(['motorcycle.user', 'images']);
        $sale->loadCount('favoritedBy');
        $sale->is_favorited = $sale->isFavoritedBy(Auth::user());

        return Inertia::render('Sales/Show', ['sale' => $sale]);
    }

    // 6. EDITAR
    public function edit(SaleListing $sale)
    {
        if ($sale->motorcycle->user_id !== Auth::id()) abort(403);
        return Inertia::render('Sales/Edit', ['sale' => $sale->load(['motorcycle', 'images'])]);
    }

    // 7. ACTUALITZAR
    public function update(Request $request, SaleListing $sale)
    {
        if ($sale->motorcycle->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'location'       => 'required|string|max:255',
            'is_active'      => 'boolean',
            'is_sold'        => 'boolean',
            // Dades tècniques de la moto
            'cc'             => 'nullable|integer|min:0',
            'power_cv'       => 'nullable|integer|min:0',
            'license_type'   => 'nullable|string|in:AM,A1,A2,A',
            'type'           => 'nullable|string|in:Naked,Sport,Trail,Custom,Scooter,Touring,Off-Road,Classic',
            'extras'         => 'nullable|string|max:1000',
            // Fotos noves
            'images'         => 'nullable|array|max:8',
            'images.*'       => 'image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        // Actualitzar l'anunci
        $sale->update([
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'price'       => $validated['price'],
            'location'    => $validated['location'],
            'is_active'   => $validated['is_active'] ?? $sale->is_active,
            'is_sold'     => $validated['is_sold'] ?? $sale->is_sold,
        ]);

        // Actualitzar dades tècniques de la moto
        $sale->motorcycle->update([
            'cc'           => $validated['cc'],
            'power_cv'     => $validated['power_cv'],
            'license_type' => $validated['license_type'],
            'type'         => $validated['type'],
            'extras'       => $validated['extras'],
        ]);

        // Afegir fotos noves (sense esborrar les existents)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $ext  = $image->getClientOriginalExtension();
                $path = $image->storeAs('sales', Str::random(40) . '.' . $ext, 'public');
                SaleImage::create(['sale_listing_id' => $sale->id, 'image_path' => $path]);
            }
        }

        return redirect()->route('sales.mine');
    }

    // 8. MARCAR COM A VENUT (ràpid, sense entrar a l'edit)
    public function markSold(SaleListing $sale)
    {
        if ($sale->motorcycle->user_id !== Auth::id()) abort(403);
        $sale->update(['is_sold' => true, 'is_active' => false]);
        return back();
    }

    // 9. ESBORRAR FOTO INDIVIDUAL
    public function destroyImage(SaleListing $sale, SaleImage $image)
    {
        if ($sale->motorcycle->user_id !== Auth::id()) abort(403);
        if ($image->sale_listing_id !== $sale->id) abort(403);

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back();
    }

    // 10. ESBORRAR ANUNCI
    public function destroy(SaleListing $sale)
    {
        if ($sale->motorcycle->user_id !== Auth::id()) abort(403);

        // Esborrem les fotos de storage
        foreach ($sale->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $sale->delete();
        return redirect()->route('sales.mine');
    }

    // 11. AFEGIR / TREURE FAVORITS
    public function toggleFavorite(SaleListing $sale)
    {
        $user = Auth::user();
        $user->favoriteSales()->toggle($sale->id);
        
        return back();
    }

    // 12. LLISTA DE FAVORITS
    public function favorites()
    {
        $user = Auth::user();
        
        $sales = $user->favoriteSales()
            ->with(['motorcycle', 'images'])
            ->withCount('favoritedBy')
            ->where('is_active', true)
            ->orderBy('sale_favorites.created_at', 'desc')
            ->get()
            ->map(function ($sale) {
                $sale->is_favorited = true; // Tots aquí són favorits
                return $sale;
            });

        return Inertia::render('Sales/Favorites', ['sales' => $sales]);
    }
}
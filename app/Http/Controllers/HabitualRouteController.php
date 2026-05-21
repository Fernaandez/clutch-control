<?php

namespace App\Http\Controllers;

use App\Models\HabitualRoute;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HabitualRouteController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_id'      => 'required|exists:routes,id',
            'motorcycle_id' => 'required|exists:motorcycles,id',
            'round_trip'    => 'sometimes|boolean',
            'label'         => 'nullable|string|max:120',
        ]);

        $route = Route::where('id', $validated['route_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $this->assertOwnedMotorcycle((int) $validated['motorcycle_id']);

        if (! $route->planned_distance_km && ! $route->distance_km) {
            return back()->withErrors([
                'route_id' => 'Aquesta ruta no té quilòmetres definits.',
            ]);
        }

        HabitualRoute::create([
            'user_id'       => Auth::id(),
            'route_id'      => $route->id,
            'motorcycle_id' => $validated['motorcycle_id'],
            'round_trip'    => (bool) ($validated['round_trip'] ?? false),
            'label'         => $validated['label'] ?? null,
        ]);

        return redirect()->route('routes.habitual');
    }

    public function complete(HabitualRoute $habitualRoute)
    {
        if ($habitualRoute->user_id !== Auth::id()) {
            abort(403);
        }

        return app(TripController::class)->completeFromHabitual($habitualRoute);
    }

    public function destroy(HabitualRoute $habitualRoute)
    {
        if ($habitualRoute->user_id !== Auth::id()) {
            abort(403);
        }

        $habitualRoute->delete();

        return redirect()->route('routes.habitual');
    }

    private function assertOwnedMotorcycle(int $motorcycleId): void
    {
        $owned = Auth::user()->motorcycles()->where('id', $motorcycleId)->exists();
        if (! $owned) {
            abort(403);
        }
    }
}

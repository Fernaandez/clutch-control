<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route as MotoRoute;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RouteController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'search_title', 'search_user', 'visibility', 'difficulty', 
            'distance_min', 'distance_max', 'duration_min', 'duration_max',
            'sort_field', 'sort_dir'
        ]);

        $query = MotoRoute::with('user')
            ->when($filters['search_title'] ?? null, function ($query, $search) {
                $query->where('title', 'like', '%' . $search . '%');
            })
            ->when($filters['search_user'] ?? null, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->when(isset($filters['visibility']) && $filters['visibility'] !== 'all', function ($query) use ($filters) {
                $query->where('is_public', $filters['visibility'] === 'public' ? 1 : 0);
            })
            ->when(isset($filters['difficulty']) && $filters['difficulty'] !== 'all', function ($query) use ($filters) {
                $query->where('difficulty', $filters['difficulty']);
            })
            ->when(isset($filters['distance_min']) && $filters['distance_min'] !== '', function ($query) use ($filters) {
                $query->where('planned_distance_km', '>=', $filters['distance_min']);
            })
            ->when(isset($filters['distance_max']) && $filters['distance_max'] !== '', function ($query) use ($filters) {
                $query->where('planned_distance_km', '<=', $filters['distance_max']);
            })
            ->when(isset($filters['duration_min']) && $filters['duration_min'] !== '', function ($query) use ($filters) {
                // Assuming min input is in minutes, convert to seconds
                $query->where('duration_seconds', '>=', $filters['duration_min'] * 60);
            })
            ->when(isset($filters['duration_max']) && $filters['duration_max'] !== '', function ($query) use ($filters) {
                // Assuming max input is in minutes, convert to seconds
                $query->where('duration_seconds', '<=', $filters['duration_max'] * 60);
            });

        $sortField = $filters['sort_field'] ?? 'id';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        
        $allowedSorts = ['id', 'title', 'planned_distance_km', 'duration_seconds', 'is_public', 'difficulty'];
        
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir === 'asc' ? 'asc' : 'desc');
        } elseif ($sortField === 'user') {
            $query->orderBy(User::select('name')->whereColumn('users.id', 'routes.user_id'), $sortDir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $routes = $query->paginate(15)->withQueryString();
        
        return Inertia::render('Admin/Routes/Index', [
            'routes' => $routes,
            'filters' => $filters
        ]);
    }

    public function edit(MotoRoute $route)
    {
        $route->load(['user', 'motorcycle']);
        
        $motorcycles = \App\Models\Motorcycle::where('user_id', $route->user_id)->get();

        return Inertia::render('Admin/Routes/Edit', [
            'routeRecord' => $route,
            'userMotorcycles' => $motorcycles
        ]);
    }

    public function update(Request $request, MotoRoute $route)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'nullable|string|max:50',
            'is_public' => 'boolean',
            'distance_km' => 'nullable|numeric',
            'planned_distance_km' => 'nullable|numeric',
            'duration_seconds' => 'nullable|integer',
            'share_token' => 'nullable|string',
            'motorcycle_id' => 'nullable|exists:motorcycles,id',
            'photo' => 'nullable|url',
        ]);

        $route->update($validated);

        return redirect()->route('admin.routes.index')->with('success', 'Route updated successfully.');
    }

    public function destroy(MotoRoute $route)
    {
        $route->delete();

        return redirect()->route('admin.routes.index')->with('success', 'Route deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Motorcycle;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MotorcycleController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'year_min', 'km_max']);

        $motorcycles = Motorcycle::with('user')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('brand', 'like', '%' . $search . '%')
                      ->orWhere('model', 'like', '%' . $search . '%')
                      ->orWhere('plate', 'like', '%' . $search . '%')
                      ->orWhereHas('user', function ($uq) use ($search) {
                          $uq->where('name', 'like', '%' . $search . '%');
                      });
                });
            })
            ->when($filters['year_min'] ?? null, function ($query, $yearMin) {
                $query->where('year', '>=', $yearMin);
            })
            ->when($filters['km_max'] ?? null, function ($query, $kmMax) {
                $query->where('current_km', '<=', $kmMax);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();
        
        return Inertia::render('Admin/Motorcycles/Index', [
            'motorcycles' => $motorcycles,
            'filters' => $filters
        ]);
    }

    public function edit(Motorcycle $motorcycle)
    {
        $motorcycle->load('user');
        return Inertia::render('Admin/Motorcycles/Edit', [
            'motorcycleRecord' => $motorcycle
        ]);
    }

    public function update(Request $request, Motorcycle $motorcycle)
    {
        $validated = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'plate' => 'nullable|string|max:30',
            'current_km' => 'required|integer|min:0',
            'cc' => 'nullable|integer|min:0',
            'power_cv' => 'nullable|integer|min:0',
            'license_type' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:100',
            'extras' => 'nullable|string',
        ]);

        $motorcycle->update($validated);

        return redirect()->route('admin.motorcycles.index')->with('success', 'Motorcycle updated successfully.');
    }

    public function destroy(Motorcycle $motorcycle)
    {
        $motorcycle->delete();

        return redirect()->route('admin.motorcycles.index')->with('success', 'Motorcycle deleted successfully.');
    }
}

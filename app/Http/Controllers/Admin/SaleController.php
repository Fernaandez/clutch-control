<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaleListing;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'state']);

        $sales = SaleListing::with('motorcycle.user')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhereHas('motorcycle.user', function ($uq) use ($search) {
                          $uq->where('name', 'like', '%' . $search . '%');
                      });
                });
            })
            ->when($filters['state'] ?? null, function ($query, $state) {
                if ($state !== 'all') {
                    $query->where('state', $state);
                }
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();
        
        return Inertia::render('Admin/Sales/Index', [
            'sales' => $sales,
            'filters' => $filters
        ]);
    }

    public function edit(SaleListing $sale)
    {
        $sale->load('motorcycle.user');
        return Inertia::render('Admin/Sales/Edit', [
            'saleRecord' => $sale
        ]);
    }

    public function update(Request $request, SaleListing $sale)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'state' => 'required|string|in:actiu,actiu (reservat) (nou),venuda',
        ]);

        $sale->update($validated);

        return redirect()->route('admin.sales.index')->with('success', 'Sale listing updated successfully.');
    }

    public function destroy(SaleListing $sale)
    {
        $sale->delete();

        return redirect()->route('admin.sales.index')->with('success', 'Sale listing deleted successfully.');
    }
}

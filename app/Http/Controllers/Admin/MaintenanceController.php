<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceTask;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MaintenanceController extends Controller
{
    public function index()
    {
        $tasks = MaintenanceTask::with('motorcycle.user')->latest()->paginate(15);
        
        return Inertia::render('Admin/Maintenance/Index', [
            'tasks' => $tasks
        ]);
    }

    public function edit(MaintenanceTask $maintenance)
    {
        $maintenance->load('motorcycle.user');
        return Inertia::render('Admin/Maintenance/Edit', [
            'maintenanceRecord' => $maintenance
        ]);
    }

    public function update(Request $request, MaintenanceTask $maintenance)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'frequency_km' => 'nullable|integer|min:0',
            'last_km_done' => 'nullable|integer|min:0',
            'last_date_done' => 'nullable|date',
            'is_recurring' => 'boolean',
            'affiliate_link' => 'nullable|url|max:500',
        ]);

        $maintenance->update($validated);

        return redirect()->route('admin.maintenance.index')->with('success', 'Maintenance task updated successfully.');
    }

    public function destroy(MaintenanceTask $maintenance)
    {
        $maintenance->delete();

        return redirect()->route('admin.maintenance.index')->with('success', 'Maintenance task deleted successfully.');
    }
}

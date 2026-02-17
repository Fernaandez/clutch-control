<?php

namespace App\Http\Controllers;

use App\Models\Motorcycle;
use App\Models\MaintenanceTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MaintenanceController extends Controller
{
    // --- 1. MANTENIMENT (Recurrent - Oli, Frens...) ---
    public function index(Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }

        // Filtrem només les que són de manteniment
        $tasks = $motorcycle->maintenanceTasks()
            ->where('type', 'maintenance') 
            ->get()->map(function ($task) use ($motorcycle) {
                
                // Càlculs de semàfors i barres de progrés
                $nextDueKm = $task->last_km_done + $task->frequency_km;
                $kmRemaining = $nextDueKm - $motorcycle->current_km;
                $kmSinceLast = $motorcycle->current_km - $task->last_km_done;
                $percentage = $task->frequency_km > 0 ? ($kmSinceLast / $task->frequency_km) * 100 : 0;

                $task->next_due_km = $nextDueKm;
                $task->km_remaining = $kmRemaining;
                $task->percentage = min($percentage, 100); 
                
                if ($kmRemaining < 0) $task->status = 'red';
                elseif ($percentage >= 90) $task->status = 'yellow';
                else $task->status = 'green';

                return $task;
            });

        return Inertia::render('Maintenance/Index', [
            'motorcycle' => $motorcycle,
            'tasks' => $tasks,
        ]);
    }

    public function store(Request $request, Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }

        $validated = $request->validate([
            'title' => 'required|string|max:50',
            'frequency_km' => 'required|numeric|min:100',
            'last_km_done' => 'required|numeric|min:0',
        ]);

        $motorcycle->maintenanceTasks()->create([
            'type' => 'maintenance',
            'title' => $validated['title'],
            'is_recurring' => true,
            'frequency_km' => $validated['frequency_km'],
            'last_km_done' => $validated['last_km_done'],
            'last_date_done' => now(), 
        ]);

        return redirect()->back();
    }

    public function history(Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }
        $history = $motorcycle->maintenanceLogs()->where('type', 'maintenance')->latest('date')->get();
        return Inertia::render('Maintenance/History', ['motorcycle' => $motorcycle, 'history' => $history]);
    }

    // --- 2. REPARACIONS (Avaries puntuals) ---
    public function indexRepairs(Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }
        $tasks = $motorcycle->maintenanceTasks()->where('type', 'repair')->get();
        return Inertia::render('Repairs/Index', ['motorcycle' => $motorcycle, 'tasks' => $tasks]);
    }

    public function storeRepair(Request $request, Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }
        $validated = $request->validate(['title' => 'required|string|max:50', 'description' => 'nullable|string']);
        
        $motorcycle->maintenanceTasks()->create([
            'type' => 'repair',
            'title' => $validated['title'],
            'description' => $validated['description'],
            'is_recurring' => false,
            'frequency_km' => null,
            'last_km_done' => 0
        ]);
        return redirect()->back();
    }

    public function historyRepairs(Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }
        $history = $motorcycle->maintenanceLogs()->where('type', 'repair')->latest('date')->get();
        return Inertia::render('Repairs/History', ['motorcycle' => $motorcycle, 'history' => $history]);
    }

    // --- 3. MILLORES (Upgrades) ---
    public function indexUpgrades(Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }
        $tasks = $motorcycle->maintenanceTasks()->where('type', 'upgrade')->get();
        return Inertia::render('Upgrades/Index', ['motorcycle' => $motorcycle, 'tasks' => $tasks]);
    }

    public function storeUpgrade(Request $request, Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }
        $validated = $request->validate(['title' => 'required|string|max:50', 'description' => 'nullable|string']);
        
        $motorcycle->maintenanceTasks()->create([
            'type' => 'upgrade',
            'title' => $validated['title'],
            'description' => $validated['description'],
            'is_recurring' => false,
            'frequency_km' => null,
            'last_km_done' => 0
        ]);
        return redirect()->back();
    }

    public function historyUpgrades(Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }
        $history = $motorcycle->maintenanceLogs()->where('type', 'upgrade')->latest('date')->get();
        return Inertia::render('Upgrades/History', ['motorcycle' => $motorcycle, 'history' => $history]);
    }

    // --- COMUNS (Marcar com fet i Esborrar) ---
    public function markDone(Request $request, MaintenanceTask $task)
    {
        if ($task->motorcycle->user_id !== Auth::id()) { abort(403); }

        $validated = $request->validate([
            'date' => 'required|date',
            'km_at_moment' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',       
            'description' => 'required|string|max:255',
        ]);

        // 1. Guardar a l'historial
        $task->motorcycle->maintenanceLogs()->create([
            'maintenance_task_id' => $task->id,
            'type' => $task->type, 
            'task_title' => $task->title,
            'date' => $validated['date'],
            'km_at_moment' => $validated['km_at_moment'],
            'cost' => $validated['cost'],
            'description' => $validated['description'],
        ]);

        // 2. Gestionar la tasca
        if ($task->type === 'maintenance') {
            // Si és manteniment, reiniciem el comptador
            $task->update([
                'last_km_done' => $validated['km_at_moment'],
                'last_date_done' => $validated['date'],
            ]);
        } else {
            // Si és reparació o millora, l'esborrem de "pendents"
            $task->delete();
        }

        return redirect()->back();
    }

    public function destroy(MaintenanceTask $task)
    {
        if ($task->motorcycle->user_id !== Auth::id()) { abort(403); }
        $task->delete();
        return redirect()->back();
    }

    public function globalHistory(Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }
        $history = $motorcycle->maintenanceLogs()->latest('date')->get();
        $totalCost = $history->sum('cost');
        return Inertia::render('Maintenance/GlobalHistory', ['motorcycle' => $motorcycle, 'history' => $history, 'totalCost' => $totalCost]);
    }
}
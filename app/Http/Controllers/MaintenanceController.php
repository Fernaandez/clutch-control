<?php

namespace App\Http\Controllers;

use App\Models\Motorcycle;
use App\Models\MaintenanceTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MaintenanceController extends Controller
{
    // --- 1. MANTENIMENT (Recurrent) ---
    public function index(Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }

        // FILTRE CLAU: Només mostrem tasques de tipus 'maintenance'
        $tasks = $motorcycle->maintenanceTasks()
            ->where('type', 'maintenance') 
            ->get()->map(function ($task) use ($motorcycle) {
                
                // Lògica de semàfors (només per manteniment)
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
            'type' => 'maintenance', // FORCEM EL TIPUS
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

    // --- 2. REPARACIONS (Avaries) ---
    public function indexRepairs(Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }
        
        // FILTRE CLAU: Només 'repair'
        $tasks = $motorcycle->maintenanceTasks()->where('type', 'repair')->get();
        
        return Inertia::render('Repairs/Index', [ // Carreguem la vista de Reparacions
            'motorcycle' => $motorcycle,
            'tasks' => $tasks
        ]);
    }

    public function storeRepair(Request $request, Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }
        $validated = $request->validate(['title' => 'required|string|max:50', 'description' => 'nullable|string']);
        
        $motorcycle->maintenanceTasks()->create([
            'type' => 'repair', // FORCEM EL TIPUS REPARACIÓ
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
        
        // FILTRE CLAU: Només 'upgrade'
        $tasks = $motorcycle->maintenanceTasks()->where('type', 'upgrade')->get();

        return Inertia::render('Upgrades/Index', [ // Carreguem la vista de Millores
            'motorcycle' => $motorcycle,
            'tasks' => $tasks
        ]);
    }

    public function storeUpgrade(Request $request, Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }
        $validated = $request->validate(['title' => 'required|string|max:50', 'description' => 'nullable|string']);
        
        $motorcycle->maintenanceTasks()->create([
            'type' => 'upgrade', // FORCEM EL TIPUS UPGRADE
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

    // --- COMUNS ---
    public function markDone(Request $request, MaintenanceTask $task)
    {
        if ($task->motorcycle->user_id !== Auth::id()) { abort(403); }

        $validated = $request->validate([
            'date' => 'required|date',
            'km_at_moment' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',       
            'description' => 'required|string|max:255',
        ]);

        // 1. GUARDEM A L'HISTORIAL (Sempre, sigui el que sigui)
        // Gràcies a "ON DELETE SET NULL" a la base de dades, si esborrem la tasca,
        // l'historial NO s'esborra, simplement es queda allà com a registre permanent.
        $task->motorcycle->maintenanceLogs()->create([
            'maintenance_task_id' => $task->id,
            'type' => $task->type, // Important: Guardem si era repair, upgrade o maintenance
            'task_title' => $task->title, // Guardem el títol per si s'esborra la tasca pare
            'date' => $validated['date'],
            'km_at_moment' => $validated['km_at_moment'],
            'cost' => $validated['cost'],
            'description' => $validated['description'],
        ]);

        // 2. DECIDIM EL FUTUR DE LA TASCA
        if ($task->type === 'maintenance') {
            // CAS A: Manteniment Recurrent (Oli, Frens...)
            // NO l'esborrem, només actualitzem el comptador per a la pròxima vegada.
            $task->update([
                'last_km_done' => $validated['km_at_moment'],
                'last_date_done' => $validated['date'],
            ]);
        } else {
            // CAS B: Reparació o Millora (Puntual)
            // Ja està feta i registrada a l'historial. No cal que ocupi espai a la llista.
            // L'ESBORREM de les tasques pendents.
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
}
<?php

namespace App\Http\Controllers;

use App\Models\Motorcycle;
use App\Models\MaintenanceTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MaintenanceController extends Controller
{
    // 1. LLISTA (Només recurrents)
    // 1. LLISTA TASQUES PENDENTS (Modificada: Ja no envia history)
    public function index(Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }

        $tasks = $motorcycle->maintenanceTasks()->get()->map(function ($task) use ($motorcycle) {
            // ... (TOT EL CODI DEL MAP IGUAL QUE ABANS) ...
            // ... No toquis res del càlcul del semàfor ...
            
            // Càlculs matemàtics resumits...
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
            // JA NO ENVIEM HISTORY AQUÍ
        ]);
    }

    // 1.2 NOVA FUNCIÓ PER A LA PÀGINA D'HISTORIAL
    public function history(Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }

        // Carreguem només els logs
        $history = $motorcycle->maintenanceLogs()
            ->with('task') // Per saber el títol de la tasca
            ->latest('date')
            ->get();

        return Inertia::render('Maintenance/History', [
            'motorcycle' => $motorcycle,
            'history' => $history
        ]);
    }

    // 2. CREAR (Sempre repetitiva)
    public function store(Request $request, Motorcycle $motorcycle)
    {
        if ($motorcycle->user_id !== Auth::id()) { abort(403); }

        $validated = $request->validate([
            'title' => 'required|string|max:50',
            'frequency_km' => 'required|numeric|min:100', // Obligatori perquè és manteniment
            'last_km_done' => 'required|numeric|min:0',
        ]);

        $task = new MaintenanceTask();
        $task->motorcycle_id = $motorcycle->id;
        $task->title = $validated['title'];
        $task->is_recurring = true; // SEMPRE TRUE en aquest mòdul
        $task->frequency_km = $validated['frequency_km'];
        $task->last_km_done = $validated['last_km_done'];
        $task->last_date_done = now(); 
        $task->save();

        return redirect()->back();
    }

    // 3. REGISTRAR COM A FET (Aquí arreglem el CRASH)
    public function markDone(Request $request, MaintenanceTask $task)
    {
        if ($task->motorcycle->user_id !== Auth::id()) { abort(403); }

        // VALIDACIÓ ESTRICTA: Això evita l'error SQL "cannot be null"
        $validated = $request->validate([
            'date' => 'required|date',
            'km_at_moment' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',       
            'description' => 'required|string|max:255',
        ]);

        // 1. Guardem Historial
        $task->motorcycle->maintenanceLogs()->create([
            'maintenance_task_id' => $task->id,
            'task_title' => $task->title,
            'date' => $validated['date'],
            'km_at_moment' => $validated['km_at_moment'],
            'cost' => $validated['cost'],
            'description' => $validated['description'],
        ]);

        // 2. Actualitzem la tasca (Resetejgem comptador)
        $task->update([
            'last_km_done' => $validated['km_at_moment'],
            'last_date_done' => $validated['date'],
        ]);

        return redirect()->back();
    }

    public function destroy(MaintenanceTask $task)
    {
        if ($task->motorcycle->user_id !== Auth::id()) { abort(403); }
        $task->delete();
        return redirect()->back();
    }
}
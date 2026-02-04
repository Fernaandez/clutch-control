<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MotorcycleController;
use App\Http\Controllers\MaintenanceController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// --- RUTA DASHBOARD INTEL·LIGENT ---
// El paràmetre {motorcycle?} permet carregar una moto específica o la per defecte.
Route::get('/dashboard/{motorcycle?}', [MotorcycleController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Rutes de Perfil d'Usuari (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

//ZONA DEL GARATGE CRUD COMPLET
Route::middleware(['auth', 'verified'])->group(function () {

    // Gestió bàsica (Llista, Crear, Editar, Esborrar)
    Route::resource('motorcycles', MotorcycleController::class);

    // NOVA RUTA: Sumar KM al dashboard 
    Route::post('/motorcycles/{motorcycle}/add-km', [MotorcycleController::class, 'addKm'])
        ->name('motorcycles.add-km');


    // --- MANTENIMENT ---
    // Veure la llista
    Route::get('/motorcycles/{motorcycle}/maintenance', [MaintenanceController::class, 'index'])
        ->name('motorcycles.maintenance.index');
    
    // Crear nova tasca
    Route::post('/motorcycles/{motorcycle}/maintenance', [MaintenanceController::class, 'store'])
        ->name('motorcycles.maintenance.store');

    // Esborrar tasca (aquí no cal passar la moto, només l'ID de la tasca)
    Route::delete('/maintenance-tasks/{task}', [MaintenanceController::class, 'destroy'])
        ->name('maintenance.destroy');

    // Marcar tasca com a feta (Patch perquè actualitzem una dada parcial)
    Route::patch('/maintenance-tasks/{task}/done', [MaintenanceController::class, 'markDone'])
        ->name('maintenance.mark-done');


    // Historial de manteniment
    Route::get('/motorcycles/{motorcycle}/maintenance/history', [MaintenanceController::class, 'history'])
        ->name('motorcycles.maintenance.history');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

});
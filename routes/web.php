<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MotorcycleController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\RouteController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::get('/dashboard/{motorcycle?}', [MotorcycleController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {

    Route::resource('motorcycles', MotorcycleController::class);
    Route::post('/motorcycles/{motorcycle}/add-km', [MotorcycleController::class, 'addKm'])
        ->name('motorcycles.add-km');

    // --- MANTENIMENT ---
    Route::get('/motorcycles/{motorcycle}/maintenance', [MaintenanceController::class, 'index'])->name('motorcycles.maintenance.index');
    Route::post('/motorcycles/{motorcycle}/maintenance', [MaintenanceController::class, 'store'])->name('motorcycles.maintenance.store');
    Route::get('/motorcycles/{motorcycle}/maintenance/history', [MaintenanceController::class, 'history'])->name('motorcycles.maintenance.history');

    // --- REPARACIONS (Rutes Noves) ---
    Route::get('/motorcycles/{motorcycle}/repairs', [MaintenanceController::class, 'indexRepairs'])->name('motorcycles.repairs.index');
    Route::post('/motorcycles/{motorcycle}/repairs', [MaintenanceController::class, 'storeRepair'])->name('motorcycles.repairs.store');
    Route::get('/motorcycles/{motorcycle}/repairs/history', [MaintenanceController::class, 'historyRepairs'])->name('motorcycles.repairs.history');

    // --- MILLORES (Rutes Noves) ---
    Route::get('/motorcycles/{motorcycle}/upgrades', [MaintenanceController::class, 'indexUpgrades'])->name('motorcycles.upgrades.index');
    Route::post('/motorcycles/{motorcycle}/upgrades', [MaintenanceController::class, 'storeUpgrade'])->name('motorcycles.upgrades.store');
    Route::get('/motorcycles/{motorcycle}/upgrades/history', [MaintenanceController::class, 'historyUpgrades'])->name('motorcycles.upgrades.history');

    // --- COMUNS ---
    Route::delete('/maintenance-tasks/{task}', [MaintenanceController::class, 'destroy'])->name('maintenance.destroy');
    Route::patch('/maintenance-tasks/{task}/done', [MaintenanceController::class, 'markDone'])->name('maintenance.mark-done');

    // --- HISTORIAL GLOBAL ---
    Route::get('/motorcycles/{motorcycle}/global-history', [MaintenanceController::class, 'globalHistory'])
        ->name('motorcycles.global-history');

    // --- RUTES (GPS) ---
    Route::get('/routes', [RouteController::class, 'index'])->name('routes.index');
    Route::get('/routes/create', [RouteController::class, 'create'])->name('routes.create');
    Route::post('/routes', [RouteController::class, 'store'])->name('routes.store');

    Route::get('/routes/{route}', [RouteController::class, 'show'])->name('routes.show');
    Route::get('/routes/{route}/edit', [RouteController::class, 'edit'])->name('routes.edit');
    Route::put('/routes/{route}', [RouteController::class, 'update'])->name('routes.update');
    Route::delete('/routes/{route}', [RouteController::class, 'destroy'])->name('routes.destroy');
    Route::get('/routes/{route}/edit', [RouteController::class, 'edit'])->name('routes.edit');
    Route::put('/routes/{route}', [RouteController::class, 'update'])->name('routes.update');

});
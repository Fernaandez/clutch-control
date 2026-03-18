<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MotorcycleController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SearchController;
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

// --- ENLLAÇOS PÚBLICS COMPARTITS ---
Route::get('/r/{token}', [RouteController::class, 'preview'])->name('routes.preview');
Route::get('/e/{token}', [EventController::class, 'preview'])->name('events.preview');
Route::post('/search-token', [SearchController::class, 'searchToken'])->name('search.token');

// El perfil NO demana 'verified' perquè l'usuari pugui entrar a canviar el seu correu si s'ha equivocat
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// TOT AIXÒ QUEDA BLOQUEJAT FINS QUE VERIFIQUIN EL CORREU
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard/{motorcycle?}', [MotorcycleController::class, 'dashboard'])->name('dashboard');

    Route::resource('motorcycles', MotorcycleController::class);
    Route::post('/motorcycles/{motorcycle}/add-km', [MotorcycleController::class, 'addKm'])
        ->name('motorcycles.add-km');

    // --- MANTENIMENT ---
    Route::get('/motorcycles/{motorcycle}/maintenance', [MaintenanceController::class, 'index'])->name('motorcycles.maintenance.index');
    Route::post('/motorcycles/{motorcycle}/maintenance', [MaintenanceController::class, 'store'])->name('motorcycles.maintenance.store');
    Route::get('/motorcycles/{motorcycle}/maintenance/history', [MaintenanceController::class, 'history'])->name('motorcycles.maintenance.history');

    // --- REPARACIONS ---
    Route::get('/motorcycles/{motorcycle}/repairs', [MaintenanceController::class, 'indexRepairs'])->name('motorcycles.repairs.index');
    Route::post('/motorcycles/{motorcycle}/repairs', [MaintenanceController::class, 'storeRepair'])->name('motorcycles.repairs.store');
    Route::get('/motorcycles/{motorcycle}/repairs/history', [MaintenanceController::class, 'historyRepairs'])->name('motorcycles.repairs.history');

    // --- MILLORES ---
    Route::get('/motorcycles/{motorcycle}/upgrades', [MaintenanceController::class, 'indexUpgrades'])->name('motorcycles.upgrades.index');
    Route::post('/motorcycles/{motorcycle}/upgrades', [MaintenanceController::class, 'storeUpgrade'])->name('motorcycles.upgrades.store');
    Route::get('/motorcycles/{motorcycle}/upgrades/history', [MaintenanceController::class, 'historyUpgrades'])->name('motorcycles.upgrades.history');

    // --- COMUNS ---
    Route::delete('/maintenance-tasks/{task}', [MaintenanceController::class, 'destroy'])->name('maintenance.destroy');
    Route::patch('/maintenance-tasks/{task}/done', [MaintenanceController::class, 'markDone'])->name('maintenance.mark-done');
    Route::get('/motorcycles/{motorcycle}/global-history', [MaintenanceController::class, 'globalHistory'])->name('motorcycles.global-history');

    // --- RUTES (GPS) ---
    Route::get('/routes', [RouteController::class, 'index'])->name('routes.index');
    Route::get('/my-routes', [RouteController::class, 'MyRoutes'])->name('routes.MyRoutes'); 
    Route::get('/routes/create', [RouteController::class, 'create'])->name('routes.create');
    Route::post('/routes', [RouteController::class, 'store'])->name('routes.store');
    Route::get('/routes/{route}', [RouteController::class, 'show'])->name('routes.show');
    Route::get('/routes/{route}/edit', [RouteController::class, 'edit'])->name('routes.edit');
    Route::put('/routes/{route}', [RouteController::class, 'update'])->name('routes.update');
    Route::delete('/routes/{route}', [RouteController::class, 'destroy'])->name('routes.destroy');
    Route::post('/routes/{route}/clone', [RouteController::class, 'clone'])->name('routes.clone');

    // --- QUEDADES (EVENTS) ---
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/my-events', [EventController::class, 'myEvents'])->name('events.mine'); 
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::post('/events/{event}/join', [EventController::class, 'join'])->name('events.join');
    Route::post('/events/{event}/leave', [EventController::class, 'leave'])->name('events.leave');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');

    // --- COMPRA / VENDA (Sales) ---
    Route::get('/sales', [\App\Http\Controllers\SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/favorites', [\App\Http\Controllers\SaleController::class, 'favorites'])->name('sales.favorites');
    Route::post('/sales/{sale}/favorite', [\App\Http\Controllers\SaleController::class, 'toggleFavorite'])->name('sales.toggle-favorite');
    Route::get('/my-sales', [\App\Http\Controllers\SaleController::class, 'mine'])->name('sales.mine');
    Route::get('/sales/create', [\App\Http\Controllers\SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [\App\Http\Controllers\SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/{sale}', [\App\Http\Controllers\SaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/edit', [\App\Http\Controllers\SaleController::class, 'edit'])->name('sales.edit');
    Route::put('/sales/{sale}', [\App\Http\Controllers\SaleController::class, 'update'])->name('sales.update');
    Route::delete('/sales/{sale}', [\App\Http\Controllers\SaleController::class, 'destroy'])->name('sales.destroy');
    Route::patch('/sales/{sale}/mark-sold', [\App\Http\Controllers\SaleController::class, 'markSold'])->name('sales.mark-sold');
    Route::delete('/sales/{sale}/images/{image}', [\App\Http\Controllers\SaleController::class, 'destroyImage'])->name('sales.images.destroy');

});

// --- ADMIN PANEL ---
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::resource('routes', \App\Http\Controllers\Admin\RouteController::class);
    Route::resource('events', \App\Http\Controllers\Admin\EventController::class);
    Route::resource('sales', \App\Http\Controllers\Admin\SaleController::class);
    Route::resource('motorcycles', \App\Http\Controllers\Admin\MotorcycleController::class);
    Route::resource('maintenance', \App\Http\Controllers\Admin\MaintenanceController::class);
});
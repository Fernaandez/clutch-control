<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MotorcycleBrandController;

// Default user route
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ─── Motorcycle Brands & Models ──────────────────────────────────────────────
// Només lectura. L'escriptura (save-custom) viu a routes/web.php dins del grup
// autenticat: aquí les rutes són stateless i qualsevol podia inserir marques.
Route::middleware('throttle:120,1')->group(function () {
    Route::get('/motorcycle-brands', [MotorcycleBrandController::class, 'brands']);
    Route::get('/motorcycle-models', [MotorcycleBrandController::class, 'models']);
});